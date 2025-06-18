<?php

namespace Concrete\Package\Zoomer\Block\Zoomer;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\File;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Concrete\Core\Utility\Service\Identifier;
use Punic\Exception\ValueNotInList;
use Punic\Unit;

class Controller extends BlockController implements FileTrackableInterface
{
    /**
     * @var string
     */
    const ZOOMTYPE_ZOOM = 'zoom';

    /**
     * @var string
     */
    const ZOOMTYPE_INNERZOOM = 'innerzoom';

    /**
     * @var string
     */
    const ZOOMTYPE_LENSZOOM = 'lenszoom';

    /**
     * @var string
     */
    const ZOOMTYPE_LIGHTBOX = 'lightbox';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btTable
     */
    protected $btTable = 'btZoomer';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btWrapperClass
     */
    protected $btWrapperClass = 'ccm-ui';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btInterfaceWidth
     */
    protected $btInterfaceWidth = 600;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btInterfaceHeight
     */
    protected $btInterfaceHeight = 480;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$supportSavingNullValues
     */
    protected $supportSavingNullValues = true;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btExportFileColumns
     */
    protected $btExportFileColumns = ['fID'];

    /**
     * @var \Concrete\Core\Statistics\UsageTracker\AggregateTracker|null
     */
    protected $tracker;

    /**
     * @var int|string|null
     */
    protected $fID;

    /**
     * @var string|null
     */
    protected $zoomType;

    /**
     * @var int|string|null
     */
    protected $maxThumbWidth;

    /**
     * @var int|string|null
     */
    protected $maxThumbHeight;

    /**
     * @var int|string|null
     */
    protected $maxImageWidth;

    /**
     * @var int|string|null
     */
    protected $maxImageHeight;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getBlockTypeName()
     */
    public function getBlockTypeName()
    {
        return t('Zoomer');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getBlockTypeDescription()
     */
    public function getBlockTypeDescription()
    {
        return t('Add Zoomable Images');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::registerViewAssets()
     */
    public function registerViewAssets($outputContent = '')
    {
        if ($this->zoomType === 'lightbox') {
            $assetGroupHandle = 'zoomer_lightbox';
        } else {
            $assetGroupHandle = 'zoomer';
        }
        $assetList = AssetList::getInstance();
        if (!$assetList->getAssetGroup($assetGroupHandle)) {
            $assetList->register(
                'css',
                'featherlight',
                'blocks/zoomer/assets/featherlight.css',
                ['version' => '1.7.14', 'minify' => false],
                'zoomer'
            );
            $assetList->register(
                'javascript',
                'featherlight',
                'blocks/zoomer/assets/featherlight.js',
                ['version' => '1.7.14', 'minify' => false],
                'zoomer'
            );
            $assetList->register(
                'javascript',
                'zoomer_elevatezoom',
                'blocks/zoomer/assets/elevateZoom.js',
                ['version' => '3.0.8', 'minify' => false],
                'zoomer'
            );
            $assetList->registerGroup('zoomer', [
                ['javascript', 'zoomer_elevatezoom'],
                ['javascript', 'jquery'],
            ]);
            $assetList->registerGroup('zoomer_lightbox', [
                ['css', 'featherlight'],
                ['javascript', 'jquery'],
                ['javascript', 'featherlight'],
            ]);
        }
        $this->requireAsset($assetGroupHandle);
    }

    public function add()
    {
        $this->set('img', null);
        $this->set('zoomType', 'zoom');
        $this->set('maxThumbWidth', 120);
        $this->set('maxThumbHeight', 80);
        $this->set('maxImageWidth', 1000);
        $this->set('maxImageHeight', 800);

        $this->prepareEdit();
    }

    public function edit()
    {
        $this->set('img', File::getByID($this->fID));
        $this->prepareEdit();
    }

    public function view()
    {
        $c = Page::getCurrentPage();
        $this->set('editMode', $c && !$c->isError() && $c->isEditMode());
        $img = File::getByID($this->fID);
        $this->set('img', $img);
        if (!$img) {
            return;
        }
        $ih = $this->app->make('helper/image');
        $this->set('thumb', $ih->getThumbnail($img, $this->maxThumbWidth, $this->maxThumbHeight, true));
        $this->set('large', $ih->getThumbnail($img, $this->maxImageWidth, $this->maxImageHeight, false));
        $this->set('identifierService', $this->app->make(Identifier::class));
    }

    protected function prepareEdit()
    {
        $this->set('al', $this->app->make('helper/concrete/asset_library'));
        $this->set('zoomTypes', $this->getZoomerTypes());
        try {
            $pixelsLabel = Unit::getName('graphics/pixel', 'narrow');
        } catch (ValueNotInList $_) {
            $pixelsLabel = 'px';
        }
        $this->set('pixelsLabel', $pixelsLabel);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::validate()
     */
    public function validate($args)
    {
        $check = $this->normalizeArgs($args);

        return is_array($check) ? true : $check;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::save()
     */
    public function save($args)
    {
        $normalized = $this->normalizeArgs($args);
        if (!is_array($normalized)) {
            throw new UserMessageException(implode("\n", $normalized->getList()));
        }
        parent::save($normalized);
        $this->fID = $normalized['fID'];
        if (version_compare(APP_VERSION, '9.0.2') < 0) {
            $this->getTracker()->track($this);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::delete()
     */
    public function delete()
    {
        if (version_compare(APP_VERSION, '9.0.2') < 0) {
            $this->getTracker()->forget($this);
        }
        parent::delete();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Tracker\FileTrackableInterface::getUsedCollection()
     */
    public function getUsedCollection()
    {
        return $this->getCollectionObject();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Tracker\FileTrackableInterface::getUsedFiles()
     */
    public function getUsedFiles()
    {
        $result = [];
        if (($id = (int) $this->fID) > 0) {
            $result[] = $id;
        }

        return $result;
    }

    /**
     * @return \Concrete\Core\Error\Error|\Concrete\Core\Error\ErrorList\ErrorList|array
     */
    protected function normalizeArgs($args)
    {
        if (!is_array($args)) {
            $args = [];
        }
        $normalized = [];
        $e = $this->app->make('helper/validation/error');
        $img = null;
        if (!empty($args['fID']) && is_numeric($args['fID'])) {
            $normalized['fID'] = (int) $args['fID'];
            if ($normalized['fID'] > 0) {
                $img = File::getByID($normalized['fID']);
            }
        }
        if (!$img) {
            $e->add(t('You need to select an image'));
        }
        if (!isset($args['zoomType']) || !is_string($args['zoomType']) || !array_key_exists($args['zoomType'], $this->getZoomerTypes())) {
            $e->add(t('Please select the zoom type'));
        } else {
            $normalized['zoomType'] = $args['zoomType'];
        }
        foreach (['maxThumbWidth', 'maxThumbHeight', 'maxImageWidth', 'maxImageHeight'] as $field) {
            $normalized[$field] = empty($args[$field]) || !is_numeric($args[$field]) ? 0 : (int) $args[$field];
        }
        if ($normalized['maxThumbWidth'] <= 0) {
            $e->add(t('Max Thumbnail Width must be set'));
        }
        if ($normalized['maxThumbHeight'] <= 0) {
            $e->add(t('Max Thumbnail Height must be set'));
        }
        if ($normalized['maxImageWidth'] <= 0) {
            $e->add(t('Max Image Width must be set'));
        }
        if ($normalized['maxImageHeight'] <= 0) {
            $e->add(t('Max Image Height must be set'));
        }

        return $e->has() ? $e : $normalized;
    }

    protected function getZoomerTypes()
    {
        return [
            static::ZOOMTYPE_ZOOM => t('Zoom'),
            static::ZOOMTYPE_INNERZOOM => t('Inner Zoom'),
            static::ZOOMTYPE_LENSZOOM => t('Lens Zoom'),
            static::ZOOMTYPE_LIGHTBOX => t('Lightbox'),
        ];
    }

    /**
     * @return \Concrete\Core\Statistics\UsageTracker\AggregateTracker
     */
    protected function getTracker()
    {
        if ($this->tracker === null) {
            $this->tracker = $this->app->make(AggregateTracker::class);
        }

        return $this->tracker;
    }
}

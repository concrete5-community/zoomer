<?php

use Concrete\Core\Localization\Localization;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Package\Zoomer\Block\Zoomer\Controller $controller
 * @var int $bID
 * @var string $zoomType
 * @var bool $editMode
 * @var Concrete\Core\Entity\File\File|null $img
 *
 * If $img is not null:
 * @var Concrete\Core\Utility\Service\Identifier $identifierService
 * @var stdClass $thumb
 * @var stdClass $large
 */

if (!$img) {
    if ($editMode) {
        Localization::getInstance()->withContext(Localization::CONTEXT_UI, static function() {
            ?>
            <div class="ccm-ui">
                <div class="alert alert-danger">
                    <?= t('Unable to find the configured image for the %s block', t('Zoomer')) ?>
                </div>
            </div>
            <?php
        });
    }
    return;
}

$uniqueBlockID = $bID . '_' . $identifierService->getString(12);

switch ($zoomType) {
    case $controller::ZOOMTYPE_ZOOM:
        if (!$editMode) {
            ?>
            <script>
            $(function() {
                $('#zoomer-<?= $uniqueBlockID ?>').elevateZoom();
            });
            </script>
            <?php
        }
        ?>
        <img src="<?= h($thumb->src) ?>" id="zoomer-<?= $uniqueBlockID ?>" data-zoom-image="<?= h($large->src) ?>" />
        <?php
        break;
    case $controller::ZOOMTYPE_INNERZOOM:
        if (!$editMode) {
            ?>
            <script>
            $(function() {
                $('#zoomer-<?= $uniqueBlockID ?>').elevateZoom({
                    zoomType: 'inner',
                    cursor: 'crosshair',
                });
            });
            </script>
            <?php
        }
        ?>
        <img src="<?= h($thumb->src) ?>" id="zoomer-<?= $uniqueBlockID ?>" data-zoom-image="<?= h($large->src) ?>" />
        <?php
        break;
    case $controller::ZOOMTYPE_LENSZOOM:
        if (!$editMode) {
            ?>
            <script>
            $(function(){
                $('#zoomer-<?=$uniqueBlockID?>').elevateZoom({
                    zoomType: 'lens',
                    lensShape: 'round',
                    lensSize : 100,
                });
            });
            </script>
            <?php
        }
        ?>
        <img src="<?= h($thumb->src) ?>" id="zoomer-<?= $uniqueBlockID ?>" data-zoom-image="<?= h($large->src) ?>" />
        <?php
        break;
    case $controller::ZOOMTYPE_LIGHTBOX:
        ?>
        <a href="<?= h($large->src) ?>" data-featherlight="image">
            <img src="<?= h($thumb->src) ?>" id="zoomer-<?= $uniqueBlockID ?>" />
        </a>
        <?php
        break;
}

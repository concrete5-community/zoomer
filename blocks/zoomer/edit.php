<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 *
 * @var Concrete\Core\Application\Service\FileManager $al
 * @var array $zoomTypes
 * @var string $pixelsLabel
 * @var Concrete\Core\Entity\File\File|null $img
 * @var string $zoomType
 * @var int $maxThumbWidth
 * @var int $maxThumbHeight
 * @var int $maxImageWidth
 * @var int $maxImageHeight
 */

?>
<div class="form-group">
    <?= $form->label('ccm-b-image', t('Select Image')) ?>
    <?= $al->image('ccm-b-image', 'fID', t('Choose Image'), $img) ?>
</div>

<div class="row">
    <div class="col-6 col-xs-6">
        <div class="form-group">
            <?= $form->label('zoomType', t('Zoom Type')) ?>
            <?= $form->select('zoomType', $zoomTypes, $zoomType, ['required' => 'required']) ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-6 col-xs-6">
        <div class="form-group">
            <?= $form->label('maxThumbWidth', t('Max Thumbnail Width')) ?>
            <div class="input-group">
                <?= $form->number('maxThumbWidth', $maxThumbWidth, ['min' => 1, 'required' => 'required']) ?>
                <div class="input-group-text input-group-addon"><?= $pixelsLabel ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xs-6">
        <div class="form-group">
            <?= $form->label('maxImageWidth', t('Max Image Width')) ?>
            <div class="input-group">
                <?= $form->number('maxImageWidth', $maxImageWidth, ['min' => 1, 'required' => 'required']) ?>
                <div class="input-group-text input-group-addon"><?= $pixelsLabel ?></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-6 col-xs-6">
        <div class="form-group">
            <?= $form->label('maxThumbHeight', t('Max Thumbnail Height')) ?>
            <div class="input-group">
                <?= $form->number('maxThumbHeight',$maxThumbHeight, ['min' => 1, 'required' => 'required']) ?>
                <div class="input-group-text input-group-addon"><?= $pixelsLabel ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xs-6">
        <div class="form-group">
            <?= $form->label('maxImageHeight', t('Max Image Height')) ?>
            <div class="input-group">
                <?= $form->number('maxImageHeight', $maxImageHeight, ['min' => 1, 'required' => 'required']) ?>
                <div class="input-group-text input-group-addon"><?= $pixelsLabel ?></div>
            </div>
        </div>
    </div>
</div>

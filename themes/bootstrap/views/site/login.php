<?php

use app\helpers\Translate;
use app\themes\idb\assets\IdbAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$assetBundle = IdbAsset::register($this);

$this->title = Translate::_('login', 'Login Page');

?>

<div class="container">
    <p align="center"><img src="<?= $assetBundle->getAssetUrl() ?>images/logo.png" alt="Identity Bank"
                           style="height:75px;"></p>
    <h1><?= Html::encode($this->title) ?></h1>
    <hr class="style" width="70%">
    <p><?= Translate::_('login', 'Please fill out the following fields to login to the Identity Bank') ?></p>

    <?php $form = ActiveForm::begin(
        [
            'id' => 'login-form',
            'options' => ['class' => 'form-signin'],
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'labelOptions' => ['class' => 'sr-only'],
            ],
        ]
    ); ?>

    <?php if ($model->getErrors()) { ?>
        <?= Html::tag('div', $form->errorSummary($model, ['header' => '']), ['class' => 'alert alert-danger']) ?>
    <?php } ?>

    <?= $form->field($model, 'userId')->textInput(['placeholder' => $model->getAttributeLabel('userId')]) ?>
    <?= $form->field($model, 'accountNumber')->textInput(
        ['placeholder' => $model->getAttributeLabel('accountNumber')]
    ) ?>
    <?= $form->field($model, 'accountPassword')->passwordInput(
        ['placeholder' => $model->getAttributeLabel('accountPassword')]
    ) ?>
    <?= Html::submitButton(
        Translate::_('login', 'Login'),
        ['class' => 'btn btn-lg btn-warning btn-block', 'name' => 'login-button']
    ) ?>

    <?php ActiveForm::end(); ?>
</div>

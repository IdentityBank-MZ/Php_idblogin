<?php

use app\helpers\Translate;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Translate::_('login', 'Login Page');

?>

<div class="container">
    <div class="container-inner">
        <div class="row">
            <div class="col-lg-6" style="float: none;margin: 0 auto;">
                <div class="sp-column">
                    <div class="sp-module">
                        <div class="sp-module-content">
                            <div class="alert alert-info" role="alert">
                                <?= Translate::_(
                                    'login',
                                    'Please fill out the following fields to login to the Identity Bank'
                                ) ?>
                            </div>

                            <?php $form = ActiveForm::begin(
                                [
                                    'id' => 'login-form',
                                    'options' => ['class' => 'form-signin'],
                                    'errorCssClass' => 'has-danger',
                                    'errorSummaryCssClass' => 'alert alert-danger',
                                    'fieldConfig' => [
                                        'template' => "{label}\n{input}\n{error}",
                                        'labelOptions' => ['class' => 'sr-only'],
                                        'errorOptions' => ['class' => 'text-danger'],
                                    ],
                                ]
                            ); ?>

                            <?php if ($model->getErrors()) { ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php foreach ($model->getErrors() as $error) { ?>
                                        <?= ((!empty($error[0])) ? $error[0] . '<BR>' . PHP_EOL : '') ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <?= $form->field($model, 'userId', ['enableClientValidation' => false])->textInput(
                                ['placeholder' => $model->getAttributeLabel('userId')]
                            ) ?>
                            <?= $form->field($model, 'accountNumber', ['enableClientValidation' => false])->textInput(
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
                            <?php if (!empty($businessSignUp)): ?>
                                <br>
                                <?= Html::a(
                                    Translate::_(
                                        'login',
                                        'Click here if you\'re Interested in using Identity Bank for your business'
                                    ),
                                    $businessSignUp,
                                    [
                                        'target' => '_blank',
                                        'class' => 'btn btn-lg btn-dark btn-block',
                                        'name' => 'sign-up-link',
                                        'style' => [
                                            'word-break' => 'break-word',
                                            'white-space' => 'normal',
                                            'color' => 'white !important'
                                        ]
                                    ]
                                ) ?>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

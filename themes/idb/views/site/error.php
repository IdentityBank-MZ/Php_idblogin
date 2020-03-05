<?php

use yii\helpers\Html;

$this->title = $name;

?>

<div class="container">
    <div class="container-inner">
        <div class="row">
            <div class="col-lg-10" style="float: none;margin: 0 auto;">
                <div class="sp-column">
                    <div class="sp-module">
                        <div class="sp-module-content">
                            <div class="alert alert-danger">
                                <?= nl2br(Html::encode($message)) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

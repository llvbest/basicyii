<?php

/** @var yii\web\View $this */

/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\ContactForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\captcha\Captcha;

use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use app\models\Message;
use app\models\Users;

$this->title = 'Contact';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .invalid-feedback {
        display: block;
    }
</style>
<div class="site-contact">
    <?php
    if (Yii::$app->session->hasFlash('messageFormSubmitted')): ?>

        <div class="alert alert-success">
            <?php echo \Yii::t('app', 'Thank you for send message'); ?>
        </div>

    <?php endif;?>

        <div class="row">
            <div class="col">
                <?php
                $dataProvider = new ActiveDataProvider([
                    'query'      => Message::find()->with('user')->where('status='.Message::STATUS_ACTIVE),
                    'pagination' => [
                        'pageSize' => 3,
                    ],
                    'sort'       => [
                        'defaultOrder' => [
                            'id' => SORT_DESC,
                        ],
                    ],
                ]);
                echo ListView::widget([
                    'dataProvider' => $dataProvider,
                    'options'      => [
                        'tag'   => 'div',
                        'class' => 'list-wrapper',
                        'id'    => 'list-wrapper',
                    ],
                    'layout'       => "{summary}\n{items}\n{pager}\n",
                    'itemView'     => function ($model, $key, $index, $widget) {
                        return $this->render('_list_item', ['model' => $model]);
                    },
                    'pager'        => [
                        'maxButtonCount' => 3,
                    ],
                ]);
                ?>
            </div>

            <div class="col col-lg-5">
                <?php
                $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                <?= $form->field($model, 'name')->textInput(
                    ['autofocus' => true, 'placeholder' => \Yii::t('app', 'Andrey')],
                ) ?>

                <?= $form->field($model, 'email')->textInput(
                    ['placeholder' => 'your@mail.com'],
                ) ?>

                <?= $form->field($model, 'body')->textarea(
                    [
                        'rows'        => 2,
                        'placeholder' => \Yii::t('app', 'Leave your textual mark in history'),
                    ],
                ) ?>

                <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                    'template' => '{image}<div class="row"><div class="col-lg-6">{input}</div></div>',
                ]) ?>

                <div class="form-group">
                    <?= Html::submitButton(
                        'Отправить',
                        [
                            'class' => 'btn btn-primary',
                            'name'  => 'contact-button',
                        ],
                    ) ?>
                    <div class="invalid-feedback" style="display: block">
                        <b>
                            <?php
                            if(!empty($model->errors['accessError'][0]))
                                echo $model->errors['accessError'][0];
                            ?>
                        </b>
                    </div>
                </div>

                <?php
                ActiveForm::end(); ?>
            </div>
        </div>
</div>

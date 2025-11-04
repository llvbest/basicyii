<div class="card card-default">
    <div class="card-body">
        <h5 class="card-title">
            <?php echo \yii\helpers\Html::encode($model->name) ?>
        </h5>
        <p>
            <?php echo $model->getText(); ?>
        </p>
        <p>
            <small class="text-muted">
                <?php echo Yii::$app->formatter->asRelativeTime($model->creation_time, 'now');?> |
                <?php echo $model->getIp(); ?> |
                <?php
                    echo \Yii::t(
                        'app',
                        '{postsCount, plural, =0{нет постов} =1{# пост} few{# поста} many{# постов} other{# постов}}',
                        ['postsCount' => $model->user->postsCount]
                    );
                ?><!--
                <a href="<?php echo yii\helpers\Url::toRoute(['message/view', 'id' => $model->id]);?>" >view</a>
                <a href="<?php echo yii\helpers\Url::toRoute(['message/update', 'id' => $model->id]);?>" >update</a>
                <a href="<?php echo yii\helpers\Url::toRoute(['message/delete', 'id' => $model->id]);?>" >delete</a>-->
            </small>
        </p>
    </div>
</div>
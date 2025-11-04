<?php

namespace app\controllers;

use app\models\Message;
use app\models\MessageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Displays a single Message model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Проверка собстенности поста
        if (!empty($model->user) && $model->user->ip != \Yii::$app->getRequest()->getUserIP()) {
            throw new ForbiddenHttpException(\Yii::t('app', 'Updating someone else\'s post is unavailable.'));
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Проверка собстенности поста
        if (!empty($model->user) && $model->user->ip != \Yii::$app->getRequest()->getUserIP()) {
            throw new ForbiddenHttpException(\Yii::t('app', 'Updating someone else\'s post is unavailable.'));
        }

        // Проверка 12 часов
        if (!$model->isEditable()) {
            // Можно дать более дружелюбное сообщение с оставшимся временем:
            throw new ForbiddenHttpException(\Yii::t('app', 'Editing is unavailable - more than 12 hours have passed since submission.'));
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Check 14 days
        if (!$model->canBeDeleted()) {
            throw new ForbiddenHttpException(\Yii::t('app', 'Removal is unavailable - more than 14 days have passed since publication.'));
        }
        // (soft-deleted)
        $model->setStatusInActive();
        $model->update();

        return $this->redirect(\Yii::$app->homeUrl);
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('app', 'The requested page does not exist.'));
    }
}

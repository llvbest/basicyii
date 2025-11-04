<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\MessageForm;
use app\models\Message;
use app\models\Users;

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionIndex()
    {
        $model = new MessageForm();
        $session = Yii::$app->session;

        if ($this->request->isPost &&
            $model->accessSend() &&
            $model->load(Yii::$app->request->post())
        ) {
            $session->set('timestamp', time());

            $user = Users::findOneByIp();
            $user->createUsers($model->email);

            //вынести в метод
            $message = new Message();
            $status = $message->createMessage($model->name,$user->id,$model->body);

            if($status) {
                $model->contact(Yii::$app->params['adminEmail'],$message);
            }

            Yii::$app->session->setFlash('messageFormSubmitted');

            return $this->refresh();
        }
        return $this->render('index', [
            'model' => $model,
        ]);
    }
}

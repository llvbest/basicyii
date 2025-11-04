<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Html;

/**
 * MessageForm is the model behind the contact form.
 */
class MessageForm extends Model
{
    // 3 минуты = 3 * 60 = 180 секунд
    public const ACCESS_SEND_SECONDS = 180;

    public $name;
    public $email;
    public $subject = 'protected links';
    public $body;
    public $accessError = '';
    public $verifyCode;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'body'], 'trim'],
            // name, email, subject and body are required
            [['name', 'email', 'body'], 'required'],
            //min max length rules
            ['name', 'string', 'length' => [2, 15]],
            [['body'], 'string', 'min' => 5, 'max' => 1000],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'name' => \Yii::t('app', 'name'),
            'body' => \Yii::t('app', 'body'),
            'verifyCode' => \Yii::t('app', 'verifyCode'),
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param string $email the target email address
     * @param Message $model
     *
     * @return bool whether the model passes validation
     */
    public function contact($email, Message $model)
    {
        if ($this->validate()) {
            //set new time
            $session = Yii::$app->session;
            $session->set('timestamp', time());

            Yii::$app->mailer
                ->compose()
                ->setTo($email)
                ->setFrom(
                    [Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']],
                )
                ->setReplyTo([$this->email => $this->name])
                ->setSubject($this->subject)
                ->setTextBody(
                    'Message update :' .
                    Html::a('link', ['message/update', 'id' => $model->id]) .
                    ' & delete: ' .
                    Html::a('link', ['message/view', 'id' => $model->id]),
                )
                ->send();

            return true;
        }
        return false;
    }

    /**
     * Access send form for 3 minutes | protect to spam
     *
     * @return bool
     */
    public function accessSend()
    {
        $session = Yii::$app->session;
        $timestamp = $session->get('timestamp');
        if ($timestamp + self::ACCESS_SEND_SECONDS > time()) {
            //раньше чем через 3 минуты
            $this->addError(
                'accessError',
                \Yii::t('app', 'The next message may be published in').' '.($timestamp + self::ACCESS_SEND_SECONDS - time()).' seconds',
            );
            return false;
        }
        return true;
    }
}

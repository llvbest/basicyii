<?php

namespace app\models;

use Yii;
use app\models\Message;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $ip
 * @property int $postsCount
 * @property string|null $session_id
 * @property int $creation_time
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
    * relationship with messages
     */
    public function getMessages()
    {
        return $this->hasMany(Message::class, ['user_id' => 'id'])->inverseOf('users');;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'default', 'value' => null],
            [['ip'], 'default', 'value' => (Yii::$app->getRequest()->getUserIP() ?? null)],
            [['session_id'], 'default', 'value' => (Yii::$app->session->getId() ?? null)],
            [['postsCount'], 'default', 'value' => 0],
            [['postsCount', 'creation_time'], 'integer'],
            [['session_id'], 'string'],
            [['email', 'ip'], 'string', 'max' => 255],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => \yii\behaviors\TimeStampBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => 'creation_time',
                ],
                'value'      => function () {
                    return time();
                },
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'ip' => Yii::t('app', 'Ip'),
            'postsCount' => Yii::t('app', 'Posts Count'),
            'session_id' => Yii::t('app', 'Session ID'),
            'creation_time' => Yii::t('app', 'Creation Time'),
        ];
    }

    /**
     * return user by ip address
     * @return Users
     */
    public static function findOneByIp() : Users
    {
        if (($model = Users::findOne(['ip' => Yii::$app->getRequest()->getUserIP()])) !== null) {
            return $model;
        }
        return new Users();
    }

    /**
     * create new Users or update counters messages to user
     * @param string $email
     *
     * @return void
     * @throws \yii\db\Exception
     */
    public function createUsers(string $email)
    {
        if ($this->getIsNewRecord()) {
            $this->email = $email;
            $this->save();
        } else {
            //update counters count posts
            $this->updateCounters(['postsCount' => 1]);
        }
    }

}

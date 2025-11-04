<?php

namespace app\models;

use Yii;
use app\models\Users;

/**
 * This is the model class for table "message".
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $text
 * @property string      $creation_time
 * @property string      $update_time
 * @property int         $status
 */
class Message extends \yii\db\ActiveRecord
{

    // 12 часов = 12 * 3600 = 43200 секунд
    public const EDIT_WINDOW_SECONDS = 43200;

    // 14 дней = 14 * 24 * 3600 = 1209600 секунд
    public const DELETE_WINDOW_SECONDS = 1209600;
    public const STATUS_ACTIVE = 1;
    public const STATUS_IN_ACTIVE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * relationship with users
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => \yii\behaviors\TimeStampBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => 'creation_time',
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'update_time',
                ],
                'value'      => function () {
                    return date('U');
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // name trim
            [['name'], 'trim'],
            // name are required
            [['name'], 'required'],
            //min max length rules
            ['name', 'string', 'length' => [2, 15]],

            [['text'], 'filter', 'filter' => 'trim'],
            [['text'], 'string', 'min' => 5, 'max' => 1000],
            // normalize value 'text'
            [
                ['text'],
                'filter',
                'filter' => function ($value) {
                    //normalize field text and name
                    $value = preg_replace('/[^\w\s]/uims', '', $value);
                    return htmlspecialchars($value, ENT_QUOTES);
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
            'id'            => Yii::t('app', 'ID'),
            'name'          => Yii::t('app', 'Name'),
            'text'          => Yii::t('app', 'Text'),
            'creation_time' => Yii::t('app', 'Creation time'),
            'update_time'   => Yii::t('app', 'Update time'),
        ];
    }

    /**
     * HtmlPurifier::process to attribute text
     *
     * @return string
     */
    public function getText()
    {
        return \yii\helpers\HtmlPurifier::process(
            strip_tags($this->text, '<b><i><s>'),
        );
    }

    /**
     * return ip user address
     *
     * @return string
     */
    public function getIp()
    {
        if (preg_match('/((^|:)([0-9a-fA-F]{0,4})){1,8}$/', $this->user->ip)) {
            return preg_replace(
                '/(\:[0-9a-fA-F]{1,4}){4}$/',
                '.***.***.***.***',
                $this->user->ip,
            );
        } else {
            return preg_replace(
                '/(\.\d{1,3}){2}$/',
                '.***.***',
                $this->user->ip,
            );
        }
    }

    /**
     * Возвращает true, если редактирование этой записи ещё разрешено (в пределах 12 часов после created_at)
     *
     * @return bool
     */
    public function isEditable(): bool
    {
        if ($this->creation_time === null) {
            return false;
        }
        return (time() - (int)$this->creation_time)
            <= self::EDIT_WINDOW_SECONDS;
    }

    /**
     * Возвращает оставшееся количество секунд до окончания периода редактирования.
     * Может вернуть 0 если окно истекло.
     *
     * @return int
     */
    public function editSecondsLeft(): int
    {
        if ($this->creation_time === null) {
            return 0;
        }
        $left = self::EDIT_WINDOW_SECONDS - (time()
                - (int)$this->creation_time);
        return $left > 0 ? $left : 0;
    }

    /**
     * Set status message STATUS_IN_ACTIVE
     * @return void
     */
    public function setStatusInActive(): void
    {
        $this->status = self::STATUS_IN_ACTIVE;
    }

    /**
     * Проверяет, можно ли удалить пост (в пределах 14 дней после публикации)
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        if (!$this->creation_time) {
            return false;
        }
        return (time() - $this->creation_time) <= self::DELETE_WINDOW_SECONDS;
    }

    /**
     * Возвращает оставшееся время (в секундах) до конца окна удаления
     * @return int
     */
    public function deleteSecondsLeft(): int
    {
        if (!$this->creation_time) {
            return 0;
        }
        $left = self::DELETE_WINDOW_SECONDS - (time() - $this->creation_time);
        return max($left, 0);
    }

    /**
     * Create new Message models data load and save in database
     *
     * @param string $name
     * @param int    $user_id
     * @param string $body
     *
     * @return bool
     * @throws \yii\db\Exception
     */
    public function createMessage(
        string $name,
        int $user_id,
        string $body
    ): bool {
        $this->name = $name;
        $this->user_id = $user_id;
        $this->text = $body;
        return $this->save();
    }
}

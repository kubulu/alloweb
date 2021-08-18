<?php

namespace app\notifications;

use app\jobs\SendBulkNotification;
use app\jobs\SendNotification;
use app\models\Notification;
use app\models\User;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\notifications
 */
abstract class BaseNotification extends BaseObject
{
    /**
     * @var User
     */
    public $sender;
    /**
     * @var \yii\db\ActiveRecord
     */
    public $source;
    /**
     * @var \yii\db\ActiveRecord
     */
    public $record;
    /**
     * @inheritdoc
     */
    public $recordClass = Notification::class;
    /**
     * @var string view name used for rendering the activity
     */
    public $viewName = 'default.php';
    /**
     * @var BaseNotificationCategory
     */
    protected $_category = null;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->recordClass) {
            $this->record = Yii::createObject($this->recordClass);
            $this->record->class = get_class($this);
        }
    }

    /**
     * @param array $options
     * @return BaseNotification|object
     * @throws InvalidConfigException
     */
    public static function instance($options = [])
    {
        return Yii::createObject(static::class, $options);
    }

    /**
     * @param $sender
     * @return static
     */
    public function from($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @param $source
     * @return $this
     */
    public function source($source)
    {
        $this->source = $source;
        $this->record->setPolymorphicRelation($source);

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $url = $this->source->getUrl();
        if (substr($url, 0, 4) !== 'http') {
            $url = Url::to($url, true);
        }

        return $url;
    }

    /**
     * @inheritdoc
     */
    public function text()
    {
        $html = $this->html();

        return !empty($html) ? strip_tags($html) : null;
    }

    /**
     * @inheritdoc
     */
    public function html()
    {
        return null;
    }

    /**
     * @return BaseNotificationCategory
     */
    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = $this->category();
        }

        return $this->_category;
    }

    /**
     * @return BaseNotificationCategory|null
     */
    protected function category()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getViewParams($params = [])
    {
        $viewParams = [
            'sender' => $this->sender,
            'source' => $this->source,
            'record' => $this->record,
            'viewable' => $this,
            'html' => $this->html(),
            'text' => $this->text(),
            'url' => Url::to(['/notifications/view', 'id' => $this->record->id], true),
            'isNew' => !$this->record->is_viewed,
        ];

        return ArrayHelper::merge($viewParams, $params);
    }

    /**
     * @param $query
     */
    public function sendBulk($query)
    {
        Yii::$app->queue->push(new SendBulkNotification(['notification' => $this, 'query' => $query]));
    }

    /**
     * @param User $user
     */
    public function send(User $user)
    {
        if ($this->isSender($user)) {
            return;
        }

        Yii::$app->queue->push(new SendNotification(['notification' => $this, 'receiverId' => $user->id]));
    }

    /**
     * @return string
     */
    public function getMailSubject()
    {
        return Yii::t('app', 'New notification');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isSender($user)
    {
        return $this->sender && $this->sender->id === $user->id;
    }

    /**
     * @param User $user
     * @throws Exception
     */
    public function saveRecord(User $user)
    {
        $notification = new Notification([
            'user_id' => $user->id,
            'class' => static::class,
        ]);

        if ($this->source) {
            $notification->setPolymorphicRelation($this->source);
        }

        if ($this->sender) {
            $notification->sender_user_id = $this->sender->id;
        }

        if (!$notification->save()) {
            file_put_contents(Yii::getAlias('@app/runtime/text.txt'),
                 json_encode($notification->attributes)
            );
            throw new Exception('Could not create notification entry');

        }

        $this->record = $notification;
    }

    /**
     * @param null $user
     */
    public function delete($user = null)
    {
        $condition = [];

        $condition['class'] = static::class;

        if ($user !== null) {
            $condition['user_id'] = $user->id;
        }

        if ($this->sender !== null) {
            $condition['sender_user_id'] = $this->sender->id;
        }

        if ($this->source !== null) {
            $condition['source_pk'] = $this->source->getPrimaryKey();
            $condition['source_class'] = get_class($this->source);
        }

        Notification::deleteAll($condition);
    }

    public function markAsViewed()
    {
        if (!empty($this->record->group_key)) {
            Notification::updateAll(['is_viewed' => true], [
                'class' => $this->record->class,
                'user_id' => $this->record->user_id,
            ]);
        } else {
            $this->record->is_viewed = true;
            $this->record->save();
        }

        $similarNotifications = Notification::find()
            ->where([
                'source_class' => $this->record->source_class,
                'source_pk' => $this->record->source_pk,
                'user_id' => $this->record->user_id
            ])
            ->andWhere(['!=', 'seen', '1']);
        foreach ($similarNotifications->all() as $notification) {
            $notification->getClass()->markAsViewed();
        }
    }

    /**
     * @return null
     */
    public function getGroupKey()
    {
        return null;
    }

    /**
     * @return null|string
     */
    public function render()
    {
        return null;
    }
}

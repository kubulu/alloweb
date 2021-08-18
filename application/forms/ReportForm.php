<?php

namespace app\forms;

use app\models\Report;
use app\models\User;
use Yii;

class ReportForm extends \yii\base\Model
{
    /**
     * @var int
     */
    public $reportedUserId;
    /**
     * @var string
     */
    public $reason;
    /**
     * @var string
     */
    public $description;

    public function rules()
    {
        return [
            [['reportedUserId', 'reason'], 'required'],
            [['reportedUserId'], 'integer'],
            [['reason'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 200],
            [['description'], 'required', 'when' => function(ReportForm $model) {
                return $model->reason == Report::REASON_OTHER;
            }, 'whenClient' => "function (attribute, value) {
                return $(\"input:radio[name='reason']:checked\").val() == 'other';
            }"],
            [['reportedUserId'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['reportedUserId' => 'id']
            ],
            ['reason', 'in', 'range' => [
                Report::REASON_SPAM, Report::REASON_BAD_PROFILE,
                Report::REASON_RUDE, Report::REASON_FAKE,
                Report::REASON_SCAM, Report::REASON_OTHER,
            ]],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reportedUserId' => Yii::t('app', 'Reported User'),
            'reason' => Yii::t('app', 'Reason'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return array
     */
    public function getReportReasons()
    {
        return (new Report())->getReportReasons();
    }

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }
}

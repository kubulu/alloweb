<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\Translations;
use app\models\Language;
use yii\data\ArrayDataProvider;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\controllers
 */
class LanguageController extends \app\modules\admin\components\Controller
{
    /**
     * @var string
     */
    public $model = Language::class;
    /**
     * @var Translations
     */
    public $component;
    /**
     * @inheritdoc
     */
    public $defaultAction = 'list';
    /**
     * @var string
     */
    public $layout = '@app/modules/admin/views/language/_layout.php';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'list' => [
                'class' => \app\modules\admin\actions\translations\ListAction::class,
            ],
            'change-status' => [
                'class' => \app\modules\admin\actions\translations\ChangeStatusAction::class,
            ],
            'optimizer' => [
                'class' => \app\modules\admin\actions\translations\OptimizerAction::class,
            ],
            'scan' => [
                'class' => \app\modules\admin\actions\translations\ScanAction::class,
            ],
            'translate' => [
                'class' => \app\modules\admin\actions\translations\TranslateAction::class,
            ],
            'save' => [
                'class' => \app\modules\admin\actions\translations\SaveAction::class,
            ],
            'message' => [
                'class' => \app\modules\admin\actions\translations\MessageAction::class,
            ],
            'create' => [
                'class' => \app\modules\admin\actions\translations\CreateAction::class,
            ],
            'update' => [
                'class' => \app\modules\admin\actions\translations\UpdateAction::class,
            ],
            'delete' => [
                'class' => \app\modules\admin\actions\translations\DeleteAction::class,
            ],
            'delete-source' => [
                'class' => \app\modules\admin\actions\translations\DeleteSourceAction::class,
            ],
            'import' => [
                'class' => \app\modules\admin\actions\translations\ImportAction::class,
            ],
            'export' => [
                'class' => \app\modules\admin\actions\translations\ExportAction::class,
            ],
        ];
    }

    /**
     * @param $languageSources
     * @return ArrayDataProvider
     */
    public function createLanguageSourceDataProvider($languageSources)
    {
        $data = [];
        foreach ($languageSources as $category => $messages) {
            foreach ($messages as $message => $boolean) {
                $data[] = [
                    'category' => $category,
                    'message' => $message,
                ];
            }
        }

        return new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false,
        ]);
    }
}

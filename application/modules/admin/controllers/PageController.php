<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\FileHelper;
use yii\filters\VerbFilter;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\controllers
 */
class PageController extends \app\modules\admin\components\Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'save' => ['post'],
                    'reset' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param null $currentPage
     * @return string
     */
    public function actionIndex($currentPage = null)
    {
        $pages = $this->getPages();
        if (count($pages) == 0) {
            $pages = $this->restore();
        }

        $currentFile = null;
        if ($currentPage !== null) {
            foreach ($pages as $page) {
                if (basename($page) == $currentPage) {
                    $currentFile = $page;
                }
            }
        }

        return $this->render('index', [
            'pages' => $pages,
            'currentPage' => $currentPage,
            'content' => $currentFile !== null ? file_get_contents($currentFile) : null,
            'pagesEditable' => env('ADMIN_PAGES_EDITABLE'),
        ]);
    }

    /**
     * @param $currentPage
     * @return \yii\web\Response
     */
    public function actionSave($currentPage)
    {
        if (!preg_match('~^\w(?:(?!\/\.{0,2}\/)[\w\/\-\.])*$~', $currentPage)) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Invalid page name'));
            return $this->redirect(['index', 'currentPage' => $currentPage]);
        }
        $file = Yii::getAlias('@content/pages' . '/' . $currentPage);

        if (!$this->isEditable()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Page editing is turned off'));
            return $this->redirect(['index', 'currentPage' => $currentPage]);
        }
        if (!file_exists($file)) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Page not found'));
            return $this->redirect(['index', 'currentPage' => $currentPage]);
        }
        file_put_contents($file, Yii::$app->request->post('content'));
        Yii::$app->session->setFlash('success', Yii::t('app', 'Pages has been saved'));
        return $this->redirect(['index', 'currentPage' => $currentPage]);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionReset()
    {
        if (!$this->isEditable()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Page editing is turned off'));
            return $this->redirect(['index']);
        }
        foreach ($this->getPages() as $page) {
            @unlink($page);
        }
        $this->restore();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Pages have been restored from theme files'));
        return $this->redirect('index');
    }

    /**
     * @return array
     */
    protected function getPages()
    {
        return FileHelper::findFiles(Yii::getAlias('@content/pages'), ['only' => ['*.php']]);
    }

    /**
     * @return array
     */
    protected function restore()
    {
        $sourcePages = FileHelper::findFiles(Yii::getAlias('@theme/views/site/pages'), ['only' => ['*.php']]);
        foreach ($sourcePages as $page) {
            copy($page, Yii::getAlias('@content/pages') . '/' . basename($page));
        }
        return $this->getPages();
    }

    /**
     * @return mixed
     */
    protected function isEditable()
    {
        return env('ADMIN_PAGES_EDITABLE');
    }
}

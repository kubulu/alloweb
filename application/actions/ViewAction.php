<?php

namespace app\actions;

use Yii;
use yii\web\NotFoundHttpException;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\actions
 */
class ViewAction extends \yii\web\ViewAction
{
    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function resolveViewName()
    {
        $viewName = Yii::$app->request->get($this->viewParam, $this->defaultView);

        if (!is_string($viewName) || !preg_match('~^\w(?:(?!\/\.{0,2}\/)[\w\/\-\.])*$~', $viewName)) {
            if (YII_DEBUG) {
                throw new NotFoundHttpException("The requested view \"$viewName\" must start with a word character, must not contain /../ or /./, can contain only word characters, forward slashes, dots and dashes.");
            }

            throw new NotFoundHttpException(Yii::t('app', 'The requested view "{name}" was not found.', ['name' => $viewName]));
        }

        $themeViewFile = Yii::getAlias("@content/pages/$viewName.php");
        if (file_exists($themeViewFile)) {
            return "@content/pages/$viewName";
        }

        return empty($this->viewPrefix) ? $viewName : $this->viewPrefix . '/' . $viewName;
    }
}

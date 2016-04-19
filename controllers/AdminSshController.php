<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.04.2016
 */
namespace skeeks\cms\sshConsole\controllers;

use skeeks\cms\modules\admin\controllers\AdminController;
use Yii;
use yii\web\Response;

/**
 * Class AdminSshController
 * @package skeeks\cms\sshConsole\controllers
 */
class AdminSshController extends AdminController
{
    public function init()
    {
        $this->name = \Yii::t('skeeks/sshConsole',"Ssh console");
        parent::init();
    }


    public function actionConsole()
    {
        $this->layout = '@skeeks/cms/sshConsole/views/layouts/clean';

        return $this->render($this->action->id);
    }

    public function actionIndex()
    {
        return $this->render($this->action->id);
    }

}
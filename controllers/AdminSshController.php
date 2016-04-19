<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.04.2016
 */
namespace skeeks\cms\sshConsole\controllers;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\models\Search;
use skeeks\cms\modules\admin\actions\AdminAction;
use skeeks\cms\modules\admin\controllers\helpers\rules\NoModel;
use skeeks\cms\modules\admin\models\forms\SshConsoleForm;
use skeeks\cms\modules\admin\widgets\ActiveForm;
use skeeks\sx\Dir;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

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
        return $this->render($this->action->id, [
            'action' => $this->action
        ]);
    }

    public function actionIndex()
    {
        return $this->render($this->action->id);
    }

}
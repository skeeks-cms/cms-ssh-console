<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.04.2016
 */
namespace skeeks\cms\sshConsole;

use yii\base\Widget;
use yii\helpers\Json;

/**
 * Class SshConsoleWidget
 * @package skeeks\cms\sshConsole
 */
class SshConsoleWidget extends Widget
{
    public $consoleHeight       = "600px";
    public $consoleWidth        = "100%";

    public $iframeId            = "";

    public function init()
    {
        parent::init();

        if (!$this->iframeId)
        {
            $this->iframeId = 'sx-iframe-' . $this->id;
        }
    }

    public function run()
    {
        if (!function_exists('system'))
        {
            return $this->render('ssh-no-console', [
                'widget' => $this
            ]);
        } else
        {
            return $this->render('ssh-console', [
                'widget' => $this
            ]);
        }

    }

    /**
     * @return string
     */
    public function getClientOptionsJson()
    {
        return Json::encode([
            'id'                    => $this->id,
            'iframeId'              => $this->iframeId,
        ]);
    }
}
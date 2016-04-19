<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.04.2016
 */
namespace skeeks\cms\sshConsole\assets;
use yii\web\AssetBundle;

/**
 * Class AdminSshConsoleAsset
 * @package skeeks\cms\sshConsole\assets
 */
class AdminSshConsoleAsset extends SshConsoleAsset
{
    public $css = [
        'ssh-console/ssh-console.css',
        'ssh-console/themes/ubuntu.css',
    ];
    public $js =
    [
        'ssh-console/ssh-console.js',
    ];
    public $depends = [
        '\skeeks\cms\modules\admin\assets\AdminAsset',
        '\skeeks\widget\simpleajaxuploader\Asset',
    ];
}

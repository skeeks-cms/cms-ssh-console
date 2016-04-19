<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.04.2016
 */
namespace skeeks\cms\sshConsole;

use Yii;
use yii\base\Model;

/**
 * Class SshConsoleForm
 * @package skeeks\cms\sshConsole
 */
class SshConsoleForm extends Model
{
    public $command;

    public function attributeLabels()
    {
        return [
            'command' => \Yii::t('skeeks/sshConsole','Command')
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['command'], 'required'],
            [['command'], 'string'],
        ];
    }



    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function execute()
    {
        if ($this->validate())
        {
            return true;
        } else
        {
            return false;
        }
    }


}

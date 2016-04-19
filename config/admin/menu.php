<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 15.04.2016
 */
return
[
    'other' =>
    [
        'items' =>
        [
            [
                "label"     => \Yii::t('skeeks/sshConsole', "Ssh console"),
                "img"       => ['skeeks\cms\sshConsole\assets\SshConsoleAsset', 'icons/ssh.png'],

                'items' =>
                [
                    [
                        "label"     => \Yii::t('skeeks/sshConsole', "Ssh console"),
                        "url"       => ["sshConsole/admin-ssh"],
                        "img"       => ['skeeks\cms\sshConsole\assets\SshConsoleAsset', 'icons/ssh.png'],
                    ],
                ],
            ],
        ]
    ]
];
<?php
return [

    'components' =>
    [
        'i18n' => [
            'translations' =>
            [
                'skeeks/sshConsole' => [
                    'class'             => 'yii\i18n\PhpMessageSource',
                    'basePath'          => '@skeeks/cms/sshConsole/messages',
                    'fileMap' => [
                        'skeeks/sshConsole' => 'main.php',
                    ],
                ]
            ]
        ],
    ],

    'modules' =>
    [
        'sshConsole' => [
            'class'         => 'skeeks\cms\sshConsole\SshConsoleModule',
        ]
    ]

];
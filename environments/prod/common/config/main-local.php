<?php
return [
    'bootstrap' => [
        'log',
        'queue',
    ],
    'components' => [
        'redis' =>[
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ],
        'queue' => [
            'class' => \yii\queue\redis\Queue::class,
            'redis' => [
                'class' => 'yii\redis\Connection',
                'hostname' => '127.0.0.1',
                'port' => 6379,
                'database' => 14, //redis的14数据库为queue专用，15为数据分析中心专用，
                //redis不要用flushall命令，会清空所有redis数据库的数据，可先select X 选择数据库，flushdb清空当前数据库
            ], // Redis connection component or its config
            'channel' => 'queue', // Queue channel key
            'as log' => \yii\queue\LogBehavior::class,
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=xiaomei',
            'username' => 'root',
            'password' => 'O&RGK@D@qeJF49Zu',
            'charset' => 'utf8',
        ],
        'dboa' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=xiaomei_oa',
            'username' => 'root',
            'password' => 'O&RGK@D@qeJF49Zu',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => '@common/mail',
            'useFileTransport' => false,//这句一定有，false发送邮件，true只是生成邮件在runtime文件夹下，不发邮件
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.ym.163.com',
                'username' => 'jstz@xiaomei360.com',
                'password' => 'qwerASDF',
                'port' => 25,
                'encryption' => 'tls',
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => [
                    'jstz@xiaomei360.com' => '技术通知',
                ],
            ],
        ],
        //  没有用到这里的缓存，缓存文件在www目录下
        /*'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@common/file_cache',
        ],*/
    ],
];

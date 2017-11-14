<?php
/**
 * The manifest of files that are local to specific environment.
 * This file returns a list of environments that the application
 * may be installed under. The returned data must be in the following
 * format:
 *
 * ```php
 * return [
 *     'environment name' => [
 *         'path' => 'directory storing the local files',
 *         'skipFiles'  => [
 *             // list of files that should only copied once and skipped if they already exist
 *         ],
 *         'setWritable' => [
 *             // list of directories that should be set writable
 *         ],
 *         'setExecutable' => [
 *             // list of files that should be set executable
 *         ],
 *         'setCookieValidationKey' => [
 *             // list of config files that need to be inserted with automatically generated cookie validation keys
 *         ],
 *         'createSymlink' => [
 *             // list of symlinks to be created. Keys are symlinks, and values are the targets.
 *         ],
 *     ],
 * ];
 * ```
 */
return [
    'Development' => [
        'path' => 'dev',
        'setWritable' => [
            'backend/runtime',
            'backend/web/assets',
            'backend/web/uploads',
            'frontend/runtime',
            'frontend/web/assets',
			'brand/runtime',
            'brand/web/assets',
            'service/runtime',
            'service/web/assets',
            'order/runtime',
            'order/web/assets',
            'order/web/uploads',
            'home/runtime',
            'home/web/assets',
            'market/runtime',
            'market/web/assets',
            'api/runtime',
            'api/web/assets',
            'data/runtime',
            'data/web/assets',
            'pc/runtime',
            'pc/web/assets',
            'erp/runtime',
            'erp/web/assets',
        ],
        'setExecutable' => [
            'yii',
            'tests/codeception/bin/yii',
        ],
        'setCookieValidationKey' => [
            'backend/config/main-local.php',
            'frontend/config/main-local.php',
			'brand/config/main-local.php',
            'service/config/main-local.php',
            'order/config/main-local.php',
            'home/config/main-local.php',
            'market/config/main-local.php',
            'api/config/main-local.php',
            'data/config/main-local.php',
            'pc/config/main-local.php',
            'erp/config/main-local.php',
        ],
    ],
    'Production' => [
        'path' => 'prod',
        'setWritable' => [
            'backend/runtime',
            'backend/web/assets',
            'backend/web/uploads',
            'frontend/runtime',
            'frontend/web/assets',
			'brand/runtime',
            'brand/web/assets',
            'service/runtime',
            'service/web/assets',
            'order/runtime',
            'order/web/assets',
            'order/web/uploads',
            'home/runtime',
            'home/web/assets',
            'market/runtime',
            'market/web/assets',
            'api/runtime',
            'api/web/assets',
            'data/runtime',
            'data/web/assets',
            'pc/runtime',
            'pc/web/assets',
            'erp/runtime',
            'erp/web/assets',
        ],
        'setExecutable' => [
            'yii',
        ],
        'setCookieValidationKey' => [
            'backend/config/main-local.php',
            'frontend/config/main-local.php',
			'brand/config/main-local.php',
            'service/config/main-local.php',
            'order/config/main-local.php',
            'home/config/main-local.php',
            'market/config/main-local.php',
            'api/config/main-local.php',
            'data/config/main-local.php',
            'pc/config/main-local.php',
            'erp/config/main-local.php',
        ],
    ],
];

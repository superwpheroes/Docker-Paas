<?php return array(
    'root' => array(
        'pretty_version' => 'dev-default',
        'version' => 'dev-default',
        'type' => 'project',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => NULL,
        'name' => 'facebook/pixel-for-wordpress',
        'dev' => false,
    ),
    'versions' => array(
        'facebook/php-business-sdk' => array(
            'pretty_version' => '11.0.0',
            'version' => '11.0.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../facebook/php-business-sdk',
            'aliases' => array(),
            'reference' => 'bd11628ea43903a95a656b8a5ea2789e4613b859',
            'dev_requirement' => false,
        ),
        'facebook/pixel-for-wordpress' => array(
            'pretty_version' => 'dev-default',
            'version' => 'dev-default',
            'type' => 'project',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => NULL,
            'dev_requirement' => false,
        ),
        'guzzlehttp/guzzle' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'techcrunch/wp-async-task' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../techcrunch/wp-async-task',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'reference' => '9bdbbf9df4ff5179711bb58b9a2451296f6753dc',
            'dev_requirement' => false,
        ),
    ),
);

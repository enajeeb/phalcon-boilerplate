<?php

    // Set environment based on hostname
    $hostname = php_uname("n");
    switch ($hostname) {

        // PROD
        case "www.mywebsite.com":
            $env = 'PROD';
        break;

        // LOCAL
        default:
            $env = 'DEV';
    }

    // application
    $application = array(
        'controllersDir'           => ROOT_PATH . '/app/controllers/',
        'modelsDir'                => ROOT_PATH . '/app/models/',
        'viewsDir'                 => ROOT_PATH . '/app/views/',
        'pluginsDir'               => ROOT_PATH . '/app/plugins/',
        'libraryDir'               => ROOT_PATH . '/app/library/',
        'voltDir'                  => ROOT_PATH . '/app/cache/volt/',
        'logDir'                   => ROOT_PATH . '/app/logs/',
        'utilsDir'                 => ROOT_PATH . '/app/utils/',
        'securityDir'              => ROOT_PATH . '/app/cache/security/',
        'thirdPartyDir'            => ROOT_PATH . '/app/thirdparty/',
        'baseUri'                  => '',
        'appTitle'                 => 'Phalcon Boilerplate',
        'appName'                  => 'phalcon-boilerplate',
        'baseUrl'                  => 'https://www.mywebsite.com',
        'env'                      => $env,
        'debug'                    => '0',
        'securitySalt'             => 'b5hdr6f9t5a6tjhpei9m',
        'pagination'               => array(
            'itemsPerPage'  => 25
        ),
        'hashTokenExpiryHours'     => 4,
        'dateTimeFormat'           => 'M d, Y H:i:s T'
    );

    // cache
     $cache = array(
        'lifetime' => 86400,
        'cacheDir' => ROOT_PATH . '/app/cache/',
    );

    // routes
    $routes = array(

        // IndexController
        '/' => array(
            'params' => array(
                'controller' => 'index',
                'action'     => 'index',
            ),
            'name'   => 'index-index',
        ),
        '/index' => array(
            'params' => array(
                'controller' => 'index',
                'action'     => 'index',
            ),
            'name'   => 'index-index',
        ),
        '/403' => array(
            'params' => array(
                'controller' => 'index',
                'action'     => 'forbidden',
            ),
            'name'   => 'index-forbidden',
        ),
        '/500' => array(
            'params' => array(
                'controller' => 'index',
                'action'     => 'internalServerError',
            ),
            'name'   => 'index-internalServerError',
        ),

        // AccessController
        '/signin' => array(
            'params' => array(
                'controller' => 'access',
                'action'     => 'signin',
            ),
            'name'   => 'access-signin',
        ),
        '/signout' => array(
            'params' => array(
                'controller' => 'access',
                'action'     => 'signout',
            ),
            'name'   => 'access-signout',
        ),
        '/forgot-password' => array(
            'params' => array(
                'controller' => 'access',
                'action'     => 'forgotPassword',
            ),
            'name'   => 'access-forgot-password',
        ),
        '/reset-password/(.*)' => array(
            'params' => array(
                'controller' => 'access',
                'action'     => 'resetPassword',
                'token'     => 1
            ),
            'name'   => 'access-reset-password',
        ),

        // UserController
        '/user/change-password' => array(
            'params' => array(
                'controller' => 'user',
                'action'     => 'changePassword',
            ),
            'name'   => 'user-change-password',
        )
    );

    // Environment based settings
    switch ( $env ) {
        case 'PROD':
            $database = array(
                'host'        => '127.0.0.1',
                'username'    => 'username',
                'password'    => 'password',
                'dbname'      => 'phalcon_boilerplate'
            );
        break;
        case 'DEV':
            $database = array(
                'host'        => '127.0.0.1',
                'username'    => 'phalcon',
                'password'    => 'phalcon',
                'dbname'      => 'phalcon_boilerplate'
            );

            // Application overrides
            $application['debug'] = '1';
            $application['baseUrl'] = 'http://localhost:9003';

        break;
        
    }

    return array(
        'application'      => $application,
        'database'         => $database,
        'routes'           => $routes
    );

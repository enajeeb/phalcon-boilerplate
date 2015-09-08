<?php
/**
 * Bootstraps the application
 */
use Phalcon\DI\FactoryDefault as PhDi,
    Phalcon\Config as PhConfig,
    Phalcon\Session\Adapter\Files as PhSession,
    Phalcon\Loader as PhLoader,
    Phalcon\Mvc\Url as PhUrl,
    Phalcon\Mvc\Router as PhRouter,
    Phalcon\Mvc\Application as PhApplication,
    Phalcon\Mvc\View as PhView,
    Phalcon\Mvc\View\Engine\Volt as PhVolt,
    Phalcon\Mvc\Model\Metadata\Memory as PhMetadataMemory,
    Phalcon\Cache\Frontend\Output as PhCacheFront,
    Phalcon\Cache\Backend\File as PhCacheBackFile,
    Phalcon\Cache\Backend\Apc as PhCacheBackApc,
    Phalcon\Db\Adapter\Pdo\Mysql as PhMysql,
    Phalcon\Exception as PhException,
    Phalcon\Logger as PhLogger,
    Phalcon\Logger\Adapter\File as PhLogFileAdapter,
    Phalcon\Debug as PhDebug,
    Phalcon\Dispatcher,
    Phalcon\Mvc\Dispatcher as MvcDispatcher,
    Phalcon\Events\Manager as EventsManager,
    Phalcon\Mvc\Dispatcher\Exception as DispatchException;

class Bootstrap
{

    private $di;

    /**
    * Constructor
    *
    * @param $di
    */
    public function __construct( $di )
    {
        $this->di = $di;
    }

    /**
    * Runs the application performing all initializations
    *
    * @param $options
    *
    * @return mixed
    */
    public function run( $options )
    {
        $loaders = array(
            'config',
            'session',
            'loader',
            'url',
            'router',
            'database',
            'view',
            'cache',
            'log',
            'utils',
            'debug'
        );

        try {

            // Handing missing controller errors
            $this->di->set('dispatcher', function() {

                //Create an EventsManager
                $eventsManager = new EventsManager();

                // Attach a listener
                $eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception) {

                    // Handle 404 exceptions
                    if ($exception instanceof DispatchException) {
                        $dispatcher->forward(array(
                            'controller' => 'index',
                            'action' => 'internalServerError'
                        ));
                        return false;
                    }

                    // Alternative way, controller or action doesn't exist
                    if ($event->getType() == 'beforeException') {
                        switch ($exception->getCode()) {
                            case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                            case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                                $dispatcher->forward(array(
                                    'controller' => 'index',
                                    'action' => 'internalServerError'
                                ));
                                return false;
                        }
                    }
                });

                // Instantiate the Security plugin
                $security = new Security($di);
                
                // Listen for events produced in the dispatcher using the Security plugin
                $eventsManager->attach('dispatch', $security);

                $dispatcher = new \Phalcon\Mvc\Dispatcher();

                // Bind the EventsManager to the dispatcher
                $dispatcher->setEventsManager($eventsManager);

                return $dispatcher;

            }, true);

            foreach ( $loaders as $service ) {
                $function = 'init' . ucfirst($service);
                $this->$function();
            }

            $application = new PhApplication();
            $application->setDI($this->di);

            return $application->handle()->getContent();

        } catch (PhException $e) {
            echo $e->getMessage();
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
    * Initializes the config. Reads it from its location and
    * stores it in the Di container for easier access
    *
    * @param array $options
    */
    protected function initConfig( $options = array() )
    {
        
        $configFile = require(ROOT_PATH . '/app/config/config.php');

        // Create the new object
        $config = new PhConfig($configFile);

        // Store it in the Di container
        // Settings cones from the include
        $this->di['config'] = $config;
    }

    // Protected functions
    /**
    * Initializes the session
    *
    * @param array $options
    */
    protected function initSession( $options = array() )
    {
        
        $config = $this->di['config'];

        $this->di['session'] = function () use ($config) {

            $session = new PhSession(array(
                'uniqueId' => $config->application->appName
            ));

            $session->start();

            return $session;

        };
    }

    /**
    * Initializes the loader
    *
    * @param array $options
    */
    protected function initLoader( $options = array() )
    {
        
        $config = $this->di['config'];

        // Creates the autoloader
        $loader = new PhLoader();

        $loader->registerDirs(
            array(
                $config->application->controllersDir,
                $config->application->modelsDir,
                $config->application->pluginsDir
            )
        );

        $loader->register();

        // Composer Autoloading
        require_once $config->application->vendorDir . '/autoload.php';

        // Dump it in the DI to reuse it
        $this->di['loader'] = $loader;
    }

    /**
    * Initializes the baseUrl
    *
    * @param array $options
    */
    protected function initUrl( $options = array() )
    {
        
        $config = $this->di['config'];

        /**
        * The URL component is used to generate all kind of urls in the
        * application
        */
        $this->di['url'] = function () use ($config) {
            $url = new PhUrl();
            $url->setBaseUri($config->application->baseUri);
            return $url;
        };
    }

    /**
    * Initializes the router
    *
    * @param array $options
    */
    protected function initRouter( $options = array() )
    {

        $config = $this->di['config'];

        $this->di['router'] = function () use ($config)
        {

            // Create the router without default routes (false)
            $router = new PhRouter(true);
            
            // 404
            $router->notFound(
                array(
                    "controller" => "index",
                    "action"     => "notFound",
                )
            );
            $router->removeExtraSlashes(true);

            foreach ($config['routes'] as $route => $items) {
                $router->add($route, $items->params->toArray())->setName($items->name);
            }

            return $router;
        };
    }

    /**
    * Initializes the database
    *
    * @param array $options
    */
    protected function initDatabase( $options = array() )
    {
        
        $config = $this->di['config'];

        // setup database service
        $this->di['db'] = function () use ($config)
        {

            $connection = new PhMysql(
                array(
                    'host'     => $config->database->host,
                    'username' => $config->database->username,
                    'password' => $config->database->password,
                    'dbname'   => $config->database->dbname,
                )
            );

            // log sql statements
            if ('1' == $config->application->debug) {
                $eventsManager = new EventsManager();

                $logger = new PhLogFileAdapter($config->application->logDir . "/db.log");

                //Listen all the database events
                $eventsManager->attach('db', function ($event, $connection) use ($logger)
                {
                    if ($event->getType() == 'beforeQuery') {
                        $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
                    }
                });

                // Assign the eventsManager to the db adapter instance
                $connection->setEventsManager($eventsManager);
            }

            return $connection;
        };

    }

    /**
    * Initializes the models metadata
    *
    * @param array $options
    */
    protected function initModelsMetadata( $options = array() )
    {
        $this->di['modelsMetadata'] = function()
        {
            return new PhMetadataMemory();
        };
    }

    /**
    * Initializes the view and Volt
    *
    * @param array $options
    */
    protected function initView( $options = array() )
    {
        
        $config = $this->di['config'];
        $di     = $this->di;

        /**
        * Setup the view service
        */
        $this->di['view'] = function () use ($config, $di)
        {

            $view = new PhView();
            $view->setViewsDir($config->application->viewsDir);
            $view->registerEngines(
                array(
                    '.volt' => function ($view , $di) use ($config) {
                        $volt        = new PhVolt($view , $di);
                        $voltOptions = array(
                            'compiledPath'      => $config->application->voltDir,
                            'compiledSeparator' => '_',
                        );
                        
                        if ('1' == $config->application->debug) {
                            $voltOptions['compileAlways'] = true;
                        }

                        $volt->setOptions($voltOptions);
                        $volt->getCompiler()->addFunction(
                            'tr',
                            function ($key) {
                                return "Bootstrap::translate({$key})";
                            }
                        );

                        return $volt;
                    },
                    '.phtml' => 'Phalcon\Mvc\View\Engine\Php', // Generate Template files uses PHP itself as the template engine
                )
            );

            return $view;
        };
    }

    /**
     * Initializes the cache
     *
     * @param array $options
     */
    protected function initCache($options = array())
    {
        $config = $this->di['config'];

        $this->di['viewCache'] = function () use ($config)
        {

            // Get the parameters
            $frontCache      = new PhCacheFront(array('lifetime' => $config->cache->lifetime));

            if (function_exists('apc_store')) {
                $cache = new PhCacheBackApc($frontCache);
            } else {
                $backEndOptions = array('cacheDir' => $config->cache->cacheDir);
                $cache          = new PhCacheBackFile($frontCache, $backEndOptions);
            }

            return $cache;
        };
    }

    // Protected functions
    /**
    * Initializes the file logger
    *
    * @param array $options
    */
    protected function initLog( $options = array() )
    {

        $config = $this->di['config'];

        $this->di['logger'] = function () use ($config)
        {

            $logger = new PhLogFileAdapter($config->application->logDir . "/app.log");

            return $logger;

        };
    }

    // Protected functions
    /**
    * Initializes the utilities
    *
    * @param array $options
    */
    protected function initUtils( $options = array() )
    {

        $config = $this->di['config'];

        // get all the files in app/utils directory
        if ( $handle = opendir($config->application->utilsDir) ) {
            
            while ( false !== ($entry = readdir($handle)) ) {
                if ( $entry != "." && $entry != ".." ) {
                    include_once $config->application->utilsDir . "/{$entry}";
                }
            }

            closedir($handle);
        }

    }

    /**
    * Initializes debug
    *
    * @param array $options
    */
    protected function initDebug( $options = array() )
    {
        
        $config = $this->di['config'];

        // Create the new object
        $debug = new PhDebug();

        // Store it in the Di container
        // Settings cones from the include
        if ('1' == $config->application->debug) {
            $debug->listen();
        }
        $this->di['debug'] = $debug;
    }

    /**
     * Translates a string
     *
     * @return string
     */
    public static function translate()
    {
        $return     = '';
        $messages   = array();
        $argCount   = func_num_args();
        $di         = PhDi::getDefault();
        $session    = $di['session'];
        $config     = $di['config'];
        $dispatcher = $di['dispatcher'];
        $lang       = $dispatcher->getParam('language');

        if (function_exists('apc_store')) {
            $phrases    = apc_fetch($lang . '-phrases');
            $language   = apc_fetch($lang . '-language');
        } else {
            $phrases    = $session->get('phrases');
            $language   = $session->get('language');
        }

        $changed = false;
        if (!$phrases || $language != $lang || ('1' == $config->application->debug)) {

            require ROOT_PATH . '/app/var/languages/en.php';

            /**
             * Messages comes from the above require statement. Not the best
             * way of doing it but we need this for Transilex
             */
            $english = $messages;
            $phrases = $english;
            if ('en' !== $lang) {
                if (file_exists(ROOT_PATH . '/app/var/languages/' . $lang . '.php')) {

                    /**
                     * Cleanup
                     */
                    $messages = array();
                    require ROOT_PATH . '/app/var/languages/' . $lang . '.php';

                    /**
                     * Messages comes from the above require statement. Not
                     * the best way of doing it but we need this for Transilex
                     */
                    $custom  = $messages;

                    foreach ($english as $key => $value) {
                        $phrases[$key] = (!empty($custom[$key])) ? $custom[$key] : $value;
                    }
                }

                $changed = true;
            }

            if ($changed) {
                if (function_exists('apc_store')) {
                    apc_store($lang . '-phrases', $phrases);
                    apc_store($lang . '-language', $lang);
                } else {
                    $session->set('phrases', $phrases);
                    $session->set('language', $lang);
                }
            }

        }

        // If parameters were passed process them, otherwise return an
        // empty string
        if ($argCount > 0) {
            $arguments = func_get_args();

            // The first argument is the key
            $key = $arguments[0];

            if (isset($phrases[$key])) {
                $return = $phrases[$key];

                // Any subsequent arguments need to replace placeholders
                // in the target string. Unset the key and process the
                // rest of the arguments one by one.
                unset($arguments[0]);

                foreach ($arguments as $key => $argument) {
                    $return = str_replace(":{$key}:", $argument, $return);
                }
            }
        }

        return $return;
    }

}

<?php

namespace app;

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Yaml\Yaml;
use Sorien\Provider\PimpleDumpProvider;
use Silex\Provider\DoctrineServiceProvider;
use Aea\ServiceProvider\BladeServiceProvider;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use app\Helpers\Helper;
use app\Helpers\MenuHelper;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use app\Services\RequestUriServiceProvider;
use app\Services\RedirectServiceProvider;
use Studio\Models\Visitor;

class Bootstrap
{
    /**
     * Application
     *
     * @var MyApplication
     */
    protected $my_app;

    /**
     * Constructor
     *
     */
    public function __construct(MyApplication $my_app)
    {
        $this->my_app = $my_app;

        if (!defined('PATH_ROOT'))
        define( 'PATH_ROOT', realpath(__DIR__ . "/..") );

        // init config
        $this->_iniConfig($my_app);

        // initialize providers
        $this->_iniSessionProvider($my_app);

        $this->_iniProviders($my_app);

        $helper = $this->my_app['helper'];
        $menuHelper = $this->my_app['menuhelper'];
        $helper->setContext();
        $menuHelper->setMenuContext();
        $url_generator = $this->my_app['url_generator'];
        $db = $this->my_app['db'];
        $blade = $this->my_app['blade'];
        $mailer = $this->my_app['mailer'];
        $session = $this->my_app['session'];
        $validator = $this->my_app['validator'];
        $visitor = $this->my_app['visitor'];
        $config_parameters = $this->my_app['config']['parameters'];
        $redirect = $this->my_app['redirect'];

        // load routes
        require PATH_ROOT . '/app/routes.php';

        // set session variable 'previous_route'
        // it is used by the FlagController.Redirect method
        // previous_route: do NOT match "/favicon.ico" or "/nl/flag" or "en/flag"
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
            $pattern_favicon = "/^\/favicon.ico/";
            $pattern_flag = "/^\/nl|en\/flag/";
            if (preg_match($pattern_favicon, $uri) === 0 &&
                preg_match($pattern_flag, $uri) === 0) {
                $my_app['session']->set('previous_route', $uri);
            }
        }
    }

    /**
     *  Init config
     *
     * @param MyApplication $my_app
     */
    private function _iniConfig(MyApplication $my_app){
        // a) load configuration
        // b) set time zone
        // c) error and exception handling

        // a) configuration is set by /app/Resources/Config/parameters.yml
        // maybe overridden by /env.yml, if present
        $my_app['config'] = function () use ($my_app) {
            $params = array();
            //---------------------
            $params['path_root'] = PATH_ROOT;
            $fileConfig = PATH_ROOT.'/app/config/environment.yml';
            $contents = file_get_contents($fileConfig);
            $data = Yaml::parse($contents);
            foreach ($data['parameters'] as $key => $value) {
                $params['parameters'][$key] = $this->_formatIniValue($value);
            }
            if (!defined('APP_ENV'))
            define( 'APP_ENV', $params['parameters']['environment']);
            $fileConfig =
                APP_ENV === 'prod'?
                    PATH_ROOT.'/app/config/parameters_prod.yml' :
                    PATH_ROOT.'/app/config/parameters_dev.yml';

            $contents = file_get_contents($fileConfig);
            $data = Yaml::parse($contents);
            foreach ($data['parameters'] as $key => $value) {
                $params['parameters'][$key] = $this->_formatIniValue($value);
            }
            return $params;
        };

        $config_prms = $my_app['config']['parameters'];
        $my_app['debug'] = $config_prms['debug'];
        $my_app['path_root'] = $this->my_app['config']['path_root'];

        // Doctrine (db)
        $my_app['db.options'] = [
            'driver'	=> $config_prms['db.driver'],
            'host'		=> $config_prms['db.host'],
            'dbname'	=> $config_prms['db.dbname'],
            'user'		=> $config_prms['db.user'],
            'password'	=> $config_prms['db.password'],
            'port'      => $config_prms['db.port']
        ];

        // b) set time zone
        $timezone = $config_prms['timezone'];
        if (isset($timezone)) {date_default_timezone_set($timezone);}
        else date_default_timezone_set("UTC");

        // c) error and exception handling
        // ErrorHandler converts all errors to exceptions, and exceptions are then caught by Silex
        ErrorHandler::register();

        // ExceptionHandler converts an exception to a Response object
        ExceptionHandler::register($my_app['debug']);
    }

    /**
     *  Format value function for ini config file
     *
     * @param string $value
     * @return string|int|float|bool
     */
    private function _formatIniValue($value) {
        // caller function: _iniConfig
        // format a value that comes from a yml file
        // formatted value is put into the $my_app['config']['parameters'] array in _iniConfig function
        if ($value === 'true' || $value === 'yes') {
            $value = TRUE;
        }
        if ($value === 'false' || $value === 'no') {
            $value = FALSE;
        }
        if ($value === '') {
            $value = NULL;
        }
        if (is_numeric($value)) {
            if (strpos($value, '.') !== false) {
                $value = floatval($value);
            } else {
                $value = intval($value);
            }
        }
        return $value;
    }

    private function _iniSessionProvider(MyApplication $my_app) {
        // initialize Session provider

        $bTest = APP_ENV === 'test'? true : false;

        if (!$bTest) {
            // set $this->my_app['session']
            $my_app->register(
                new SessionServiceProvider(),
                array(
                    'session.storage.save_path' => PATH_ROOT.'/cache/sessions'
                )
            );
            $my_app->before(
                function (Request $request) {
                    $request->getSession()->start();
                    $my_app['request'] = $request;
                }
            );
        } else {
            $this->my_app['session'] = new Session(new MockArraySessionStorage());
        }
    }

    private function _iniProviders(MyApplication $my_app) {
        // initialize services

        $my_app->register(new RequestUriServiceProvider());
        $my_app->register(new RedirectServiceProvider($my_app));

        // locale
        $my_app->register(new LocaleServiceProvider());

        // translation
        $my_app->register(new TranslationServiceProvider());
        $trans = $my_app['translator'];
        $trans->setFallbackLocales(array('nl'));
        $trans->addLoader('yaml', new YamlFileLoader());

        // $my_app['db'] service
        $config_prms = $my_app['config']['parameters'];
        $my_app->register(new DoctrineServiceProvider(),
            array('dbs.options'=>[
                'default'=>[
                    'driver'   => $config_prms['db.driver'],
                    'host'     => $config_prms['db.host'],
                    'dbname'   => $config_prms['db.dbname'],
                    'user'     => $config_prms['db.user'],
                    'password' => $config_prms['db.password'],
                    'charset'  => $config_prms['db.charset']
                ]
            ]
            )
        );

        $my_app['helper'] = function ($my_app) {
            return new Helper($my_app['translator'], $my_app['requesturi'], $my_app['session'], $my_app['db']);
        };

        $my_app['menuhelper'] = function ($my_app) {
            return new MenuHelper($my_app['helper'], $my_app['requesturi'], $my_app['url_generator']);
        };

        $my_app['visitor'] = function ($my_app) {
            return new Visitor($my_app['helper'], $my_app['validator'], $my_app['blade']);
        };

        // PimpleDump
        if (APP_ENV !== 'prod')
        $my_app->register(new PimpleDumpProvider());

        // validator
        $my_app->register(new ValidatorServiceProvider());

//        $my_app->register(new HttpCacheServiceProvider(), array(
//            'http_cache.cache_dir' => PATH_ROOT .'/cache/',
//        ));

        // blade templates
        $path = PATH_ROOT . '/web/resources/Views';
        $my_app->register(new BladeServiceProvider(), array(
            'blade.view_path' => $path,
            'blade.cache_path' => PATH_ROOT . '/cache'
        ));

        $my_app->register(new SwiftmailerServiceProvider());
        $my_app['swiftmailer.options'] = array(
            'host' =>       $config_prms['mail.host'], //'smtp.vevida.com',
            'port' =>       $config_prms['mail.port'], // '25'
            'username' =>   $config_prms['mail.username'],
            'password' =>   $config_prms['mail.password'],
            'encryption' => $config_prms['mail.encryption'], // 'tls'
            'auth_mode' =>  $config_prms['mail.auth_mode'] // 'login'
        );

        // asset service
        $my_app->register(new AssetServiceProvider(), array(
            'assets.version' => 'v1',
            'assets.version_format' => '%s?version=%s',
            'assets.named_packages' => array(
                'css' => array('version' => 'css2', 'path_CSS' => $my_app['path_root'].'/web/resources/CSS'),
                'icons' => array('path_icons' => $my_app['path_root'].'/web/resources/CSS')),
            )
        );

        // @@@ weg $my_app->register(new Visitor($my_app['helper'], $my_app['validator'], $my_app['blade']));

        $my_app->register(new ServiceControllerServiceProvider());
    }
}
?>
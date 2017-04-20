<?php

namespace tests\app;

use app\Services\EmailHelper;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Yaml\Yaml;
use Silex\Provider\DoctrineServiceProvider;
use Aea\ServiceProvider\BladeServiceProvider;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use app\Helpers\Helper;
use app\Helpers\MenuHelper;
use app\Services\EmailServiceDummy;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use app\Services\GlobalVarsServiceProvider;
use app\Services\RedirectServiceProvider;
use Studio\Models\Visitor;
use Studio\Controllers\WelcomeController;
use Studio\Controllers\GalleryController;
use Studio\Controllers\ExhibitionsController;
use Studio\Controllers\AboutTheWorkController;
use Studio\Controllers\CvController;
use Studio\Controllers\ContactController;
use Studio\Controllers\FlagController;
use Studio\Controllers\WorkController;
use App\MyApplication;
use StudioTest\Controllers\WebTestCaseBase;

class BootstrapTest extends WebTestCaseBase
{
    /**
     * Application
     *
     * @var MyApplication
     */
    protected $app;

    /**
     * Constructor
     *
     */
    public function __construct(MyApplication $app)
    {
        $this->app = $app;
    }

    function bootstrap(){
        if (!defined('PATH_ROOT'))
        define( 'PATH_ROOT', realpath(__DIR__ . "/../..") );
        $app = $this->app;

        // init config
        $this->_iniConfig($app);

        // initialize providers
        $this->_iniProviders($app);

        $helper = $app['helper'];
        $menuHelper = $app['menuhelper'];

        $url_generator = $this->app['url_generator'];
        $db = $this->app['db'];
        $blade = $this->app['blade'];
        $email_helper = $this->app['email_helper'];
        $session = $this->app['session'];
        $visitor = $this->app['visitor'];
        $config_parameters = $this->app['config']['parameters'];
        $redirect = $this->app['redirect'];

        // load routes
        try {
            // mount application controllers
            $this->app->mount('', new WelcomeController($helper, $menuHelper, $url_generator, $blade));
            $this->app->mount('', new GalleryController($helper, $menuHelper, $url_generator, $blade, $db));
            $this->app->mount('', new WorkController($helper, $menuHelper, $blade, $db));
            $this->app->mount('', new ExhibitionsController($helper, $menuHelper, $blade));
            $this->app->mount('', new AboutTheWorkController($helper, $menuHelper, $blade));
            $this->app->mount('', new CvController($helper, $menuHelper, $url_generator, $blade));
            $this->app->mount('', new ContactController($helper, $menuHelper, $url_generator, $visitor, $email_helper, $blade, $config_parameters));
            $this->app->mount('', new FlagController($helper, $menuHelper, $session, $url_generator, $redirect));

        } catch (\Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     *  Init config
     *
     * @param MyApplication $app
     */
    private function _iniConfig(MyApplication $app){
        // a) load configuration
        // b) set time zone
        // c) error and exception handling

        // a) configuration is set by /app/Resources/Config/parameters.yml
        // maybe overridden by /env.yml, if present
        $app['config'] = function () use ($app) {
            $params = array();
            //---------------------
            $params['path_root'] = PATH_ROOT;
            $fileConfig = PATH_ROOT.'/app/config/environment.yml';
            $contents = file_get_contents($fileConfig);
            $data = Yaml::parse($contents);
            foreach ($data['parameters'] as $key => $value) {
                $params['parameters'][$key] = $this->_formatIniValue($value);
            }
            $fileConfig = PATH_ROOT.'/tests/app/config/parameters_test.yml';

            $contents = file_get_contents($fileConfig);
            $data = Yaml::parse($contents);
            foreach ($data['parameters'] as $key => $value) {
                $params['parameters'][$key] = $this->_formatIniValue($value);
            }
            return $params;
        };

        $config_prms = $app['config']['parameters'];
        $app['debug'] = $config_prms['debug'];
        $app['path_root'] = $this->app['config']['path_root'];

        // Doctrine (db)
        $app['db.options'] = [
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
        ExceptionHandler::register($app['debug']);
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
        // formatted value is put into the $app['config']['parameters'] array in _iniConfig function
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

    private function _iniProviders(MyApplication $app) {
        // initialize services
        $app['session'] = new Session(new MockArraySessionStorage());

        $app['global_vars'] = function() {
            return new GlobalVarsServiceProvider();
        };

        $app->register(new RedirectServiceProvider($app));

        // locale
        $app->register(new LocaleServiceProvider());

        // translation
        $app->register(new TranslationServiceProvider());
        $trans = $app['translator'];
        $trans->setFallbackLocales(array('nl'));
        $trans->addLoader('yaml', new YamlFileLoader());

        // $app['db'] service
        $config_prms = $app['config']['parameters'];
        $app->register(new DoctrineServiceProvider(),
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

        $app['helper'] = function ($app) {
            return new Helper($app['translator'], $app['global_vars'], $app['session'], $app['db']);
        };

        $app['menuhelper'] = function ($app) {
            return new MenuHelper($app['helper'], $app['global_vars'], $app['url_generator']);
        };

        $app['visitor'] = function ($app) {
            return new Visitor($app['helper'], $app['validator'], $app['blade']);
        };

        // validator
        $app->register(new ValidatorServiceProvider());

        // blade templates
        $path = PATH_ROOT . '/web/resources/Views';
        $app->register(new BladeServiceProvider(), array(
            'blade.view_path' => $path,
            'blade.cache_path' => PATH_ROOT . '/cache'
        ));

        // Swift message object from factory: every call returns a new object instance
        $app['new_swift_message'] = $app->factory(function () use ($app) {
            return new \Swift_Message();
        });

        $app->register(new EmailHelper($app['new_swift_message']));
        $app['email_helper'] = function($app){
            return new EmailServiceDummy($app['email_validator']); // with stub send method
        };

        // asset service
        $app->register(new AssetServiceProvider(), array(
            'assets.version' => 'v1',
            'assets.version_format' => '%s?version=%s',
            'assets.named_packages' => array(
                'css' => array('version' => 'css2', 'path_CSS' => $app['path_root'].'/web/resources/CSS'),
                'icons' => array('path_icons' => $app['path_root'].'/web/resources/CSS')),
            )
        );

        $app->register(new ServiceControllerServiceProvider());
    }
}
?>
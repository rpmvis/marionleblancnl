<?php

namespace app\Helpers;
use app\MyApplication;

class Helper
{
    protected $my_app;
    protected $context;
    protected $locale;
    protected $session;
    protected $baseUrl;
    protected $menuHelper;

    public function __construct(MyApplication $my_app, $session)
    {
        $this->my_app = $my_app;
        $this->session = $session;
        $this->menuHelper = new MenuHelper($this);

        // set $baseUrl
        $bTest = APP_ENV === 'test' ? true : false;

        if (!$bTest) {
            // set $baseUrl = site's base url
            $url = sprintf(
                "%s://%s",
                isset($_SERVER['HTTPS']) &&
                $_SERVER['HTTPS'] != 'off' ?
                    'https' :
                    'http',
                $_SERVER['SERVER_NAME']
            );

            $url = rtrim($url, '/\\');
            $this->baseUrl = $url;
        } else {
            $this->baseUrl = 'http://s.com';
        }
    }

    function trans($trans_id): string
    {
        return $this->my_app['translator']->trans($trans_id);
    }

    function transValue($key1, $key2): string
    {
        // get translated value from a row/column database table ttranslate2
        // value field is 'trans_nl' or 'trans_en'
        // $key1 and $key2 filter the row with translated value
        // $key2 is pivoted and delivers the key for $row[$key2]

        $db = $this->my_app['db'];

        $trans_field = ($this->locale === 'nl') ? 'trans_nl' : 'trans_en';

        $sql = "SELECT $trans_field AS `$key2`
                FROM ttranslate2
                WHERE key1 =  '$key1' and key2='$key2'";
        $row = $db->fetchAssoc($sql);

        return $row[$key2];
    }

    function transRows(string $key1, $arrayColumn_aliases, string $row_order = null): array
    {
        // get array or translated rows from database table ttranslate2
        // example:
        // return array of rows with translated values in columns 'period' and 'descr'
        //   $key1: "exhibitions.group"
        //   $key2: 'row'
        //   $arrayColumn_aliases: ['period', 'descr']
        //   field column_nr: only holding values 1 and 2, to be pivoted
        //   columns 'period' and 'descr' are pivoted to column names from values 1 and 2 on field column_nr
        if ($row_order === null) {
            $row_order = 'ASC';
        }
        $fields = '';
        $i = 0;

        $trans_field = ($this->locale === 'nl') ? 'trans_nl' : 'trans_en';

        // $fields: build expression to create pivot columns 'period' and 'descr'. For example:
        // MAX( IF( column_nr =1, trans_nl, NULL ) ) AS  `period` ,
        // MAX( IF( column_nr =2, trans_nl, NULL ) ) AS  `descr`
        foreach ($arrayColumn_aliases as $alias) {
            $i++;
            $fields .= "MAX( IF( column_nr=".(string)$i.", $trans_field, NULL ) ) AS  `$alias`,";
        }
        $fields = substr($fields, 0, strlen($fields) - 1);

        $sql =
            "SELECT $fields
        FROM ttranslate2
        WHERE key1 =  '$key1'
        AND key2 =  'row'
        GROUP BY row_nr
        ORDER BY row_nr $row_order";

        $db = $this->my_app['db'];
        $rows = $db->fetchAll($sql);

        // remove new lines "\n"
        for ($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];
            foreach ($arrayColumn_aliases as $name) {
                $row[$name] = str_replace("\n", '', $row[$name]);
            }
            $rows[$i] = $row;
        }

        return $rows;
    }

    function getBaseUrl(): string
    {
        // return base URL like 'http://s.com'
        return $this->baseUrl;
    }

    function getFileUrl(string $file): string
    {
        // return file URL = base URL plus file name appended

        $file = '/'.ltrim($file, '/\\'); // remove trailing slashes from file

        return $this->baseUrl.$file;
    }

    function getLanguage(): string
    {
        // get current language ('nl' or 'en') from session
        return $this->session->get('current_language');
    }

    function setLanguage()
    {
        // a) set locale ('nl' or 'en')
        // b) store locale as 'current_language' in session
        // c) store locale in translator service
        // d) load locale resource for locale

        // a) check if locale goes as route parameter in REQUEST_URI
        $this->locale = null;
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
            if ($uri !== null) {
                $uri = ltrim($uri, '/');
                $array = explode("/", $uri);
                switch ($array[0]) {
                    case 'nl':
                        $this->locale = $array[0];
                        break;
                    case 'en':
                        $this->locale = $array[0];
                        break;
                }
            }
        }

        // resort to locale in session
        if ($this->locale === null) {
            if ($this->session->get('current_language')) {
                $this->locale = $this->session->get('current_language');
            }

            // resort to first fall back locale
            if ($this->locale === null) {
                $this->locale = $this->my_app['translator']->getFallbackLocales()[0]; // "nl"
            }
        }

        if ($this->locale === null) {
            throw new \Exception("setLanguage: unable to set locale!");
        }

        // b) store $this->locale in session
        $this->session->set('current_language', $this->locale);

        // c) store $this->locale in translator service
        $this->my_app['translator']->setLocale($this->locale);

        // d) load locale resource only for actual locale
        $resources = glob(PATH_ROOT.'/app/Translator/Locales/'.$this->locale.'*.yml');
        if ($resources) {
            foreach ($resources as $resource) {
                $this->my_app['translator']->addResource('yaml', $resource, $this->locale);
            }
        } else {
            throw new \Exception("setLanguage: no translation file found!");
        }
    }

    function setContext()
    {
        // set array of context items
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
            $arr = explode("/", substr($uri, 1));
            $active_menu = $arr[0];
        } else {
            $active_menu = '';
        }
        if (empty($active_menu)) {
            $active_menu = 'welcome';
        } elseif ($active_menu === 'gall') {
            $active_menu .= "/$arr[1]"; // gall/galleries1 or gall/galleries2
        }
        $this->context['active_menu'] = $active_menu;

        // set language + locale
        $this->setLanguage();
        $this->context['locale'] = $this->locale;

        $url = $this->getBaseUrl();
        $this->context['img_src_NL_flag'] = $url.'/web/resources/appImages/netherlands_flag.bmp';
        $this->context['img_src_UK_flag'] = $url.'/web/resources/appImages/united_kingdom_flag.bmp';

        // set top menu items
        $menuHelper = new MenuHelper($this);
        $menu_items = $menuHelper->getTopMenuItems($this->context);
        $this->context['menu_items'] = $menu_items;

        // site header: "Marion Le Blanc beeldend kunstenaar"
        $site_header = $this->transValue('header', 'header');
        $site_header_responsive = $this->transValue('header', 'header_responsive');
        $this->context['site_header'] = $site_header;
        $this->context['site_header_responsive'] = $site_header_responsive;

        // put some url's in context
        $this->context['url_CSS'] = $url.'/web/resources/CSS/styles.css';
        $this->context['img_blueDot'] = $url.'/web/resources/appImages/BlueDot.gif';
        $this->context['img_logo'] = $url.'/web/resources/appImages/LogoLichtBlauw_transp.gif';
    }

    function getContext(): array
    {
        // get array of context items
        // to be used in controllers and views
        return $this->context;
    }

    function my_app(){
        return $this->my_app;
    }

    function menuHelper() : MenuHelper{
        return $this->menuHelper;
    }
}


<?php

namespace app\Helpers;

use Pimple\ServiceProviderInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\HttpFoundation\Session\Session;
use app\Services\GlobalVarsServiceProvider;
// useable? use Symfony\Component\HttpFoundation\Tests\Session\SessionTest;
use Doctrine\DBAL\Connection;

class Helper implements ServiceProviderInterface
{
    protected $translator;
    protected $uri;
    protected $session;
    protected $db;
    protected $context;
    protected $locale;
    protected $baseUrl;

    public function register(\Pimple\Container $app)
    {
        $app['helper'] = function () {
            return $this;
        };
    }

    public function __construct(Translator $translator, GlobalVarsServiceProvider $varsProv, Session $session, Connection $db)
    {
        $this->translator = $translator;
        $this->uri = $varsProv;
        $this->db = $db;
        $this->session = $session;

        $this->baseUrl = $varsProv->getBaseUrl();
    }

    function trans($trans_id): string
    {
        return $this->translator->trans($trans_id);
    }

    function transValue($key1, $key2): string
    {
        // get translated value from a row/column database table ttranslate2
        // value field is 'trans_nl' or 'trans_en'
        // $key1 and $key2 filter the row with translated value
        // $key2 is pivoted and delivers the key for $row[$key2]

        $trans_field = ($this->locale === 'nl') ? 'trans_nl' : 'trans_en';

        $sql = "SELECT $trans_field AS `$key2`
                FROM ttranslate2
                WHERE key1 =  '$key1' and key2='$key2'";
        $row = $this->db->fetchAssoc($sql);

        return $row[$key2];
    }

    function transRows(string $key1, $arrayColumn_aliases, string $row_order = 'ASC'): array
    {
        // get array or translated rows from database table ttranslate2
        // example:
        // return array of rows with translated values in columns 'period' and 'descr'
        //   $key1: "exhibitions.group"
        //   $key2: 'row'
        //   $arrayColumn_aliases: ['period', 'descr']
        //   field column_nr: only holding values 1 and 2, to be pivoted
        //   columns 'period' and 'descr' are pivoted to column names from values 1 and 2 on field column_nr
        $fields = '';
        $i = 0;

        $trans_field = ($this->locale === 'nl') ? 'trans_nl' : 'trans_en';

        // $fields: build expression to create pivot columns 'period' and 'descr'. For example:
        // MAX( IF( column_nr =1, trans_nl, NULL ) ) AS  `period` ,
        // MAX( IF( column_nr =2, trans_nl, NULL ) ) AS  `descr`
        foreach ($arrayColumn_aliases as $alias) {
            $i++;
            $column_nr = (string)$i;
            $fields .= "MAX( IF( column_nr=$column_nr, $trans_field, NULL ) ) AS  '$alias',";
        }
        $fields = substr($fields, 0, strlen($fields) - 1);

        $sql =
        "SELECT $fields
        FROM ttranslate2
        WHERE key1 =  '$key1'
        AND key2 =  'row'
        GROUP BY row_nr
        ORDER BY row_nr $row_order";

        $rows = $this->db->fetchAll($sql);

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

    function getLanguage(): string{
        // get current language ('nl' or 'en') from session
        return $this->session->get('current_language');
    }

    function setLanguage()
    {
        $this->locale = null;

        // a) set locale ('nl' or 'en')
        // b) store locale as 'current_language' in session
        // c) store locale in translator service
        // d) load locale resource for locale

        // a) check if locale goes as route parameter in REQUEST_URI
        $uri = $this->uri->getRequestUri();
        if ($uri !== null) {
            $uri= ltrim($uri, '/');
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

        // resort to locale in session
        if ($this->locale === null) {
            if ($this->session->get('current_language')) {
                $this->locale = $this->session->get('current_language');
            }

            // resort to first fall back locale
            if ($this->locale === null) {
                $this->locale = $this->translator->getFallbackLocales()[0]; // "nl"
            }
        }

        if ($this->locale === null) {
            throw new \Exception("setLanguage: unable to set locale!");
        }

        // b) store $this->locale in session
        $this->session->set('current_language', $this->locale);

        // c) store $this->locale in translator service
        $this->translator->setLocale($this->locale);

        // d) load locale resource only for actual locale
        $resources = glob(PATH_ROOT.'/app/Translator/Locales/'.$this->locale.'*.yml');
        if ($resources) {
            foreach ($resources as $resource) {
                $this->translator->addResource('yaml', $resource, $this->locale);
            }
        } else {
            throw new \Exception("setLanguage: no translation file found!");
        }
    }

    function setContext()
    {
        // set context items array
        //   language + locale
        $this->setLanguage();
        $this->context['locale'] = $this->locale;

        $url = $this->getBaseUrl();
        $this->context['img_src_NL_flag'] = $url.'/web/resources/appImages/netherlands_flag.bmp';
        $this->context['img_src_UK_flag'] = $url.'/web/resources/appImages/united_kingdom_flag.bmp';

        //   site header: "Marion Le Blanc beeldend kunstenaar"
        $site_header = $this->transValue('header', 'header');
        $site_header_responsive = $this->transValue('header', 'header_responsive');
        $this->context['site_header'] = $site_header;
        $this->context['site_header_responsive'] = $site_header_responsive;

        //   some url's
        $this->context['url_CSS'] = $url.'/web/resources/CSS/styles.css';
        $this->context['img_blueDot'] = $url.'/web/resources/appImages/BlueDot.gif';
        $this->context['img_logo'] = $url.'/web/resources/appImages/LogoLichtBlauw_transp.gif';
    }

    function getContext(): array
    {
        // get context items array
        // to be used in controllers and views
        return $this->context;
    }
}


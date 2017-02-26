<?php
// src/Studio/Controller/WorkController.php
namespace Studio\Controllers;

use app\MyApplication;
use app\Helpers\Helper;
use app\Helpers\MenuHelper;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Silex\Api\ControllerProviderInterface;

class WorkController extends BaseController implements ControllerProviderInterface{
    protected $db;
    private $img_max_width = 400;
    private $img_max_height = 400;

    public function __construct(Helper $helper)  {
        parent::__construct($helper);
        $this->db = $this->app['db'];
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/{main_menu}/{tab_menu}/{gallery_type}/{slice_nr}/{code}/{bgcolor}',
            function (Request $request) {
                return $this->getResponse($request);})
        ->assert('main_menu', '^[a-z\d\/]*') // only a-z, 1-9 or /
        ->assert('tab_menu', '^[a-z\d\/_]*') // only a-z, 1-9 or _ or /
        ->assert('gallery_type', '^[a-z]$')   // only 1 char
        ->assert('slice_nr', '^[\d]{1}')      // only 1 digit
        ->assert('work', 'work')
        ->assert('code', "^\d{4}-[c|k|o|s|v]-\d+") // code: 1997-s-12
        ->assert('bgcolor', '[\#A-F\d]{7}')     // only 6 hexadecimals
        ->bind('work');

        return $controllers;
    }

    public function getResponse(Request $request):string{
        // menu helper for tabmenu and color menu

        // get req parameters
        $main_menu_item = $request->get('main_menu');
        $galleries = str_replace('gall/', '', $main_menu_item);
        $tab_menu = $request->get('tab_menu');
        $gallery_type= $request->get('gallery_type');
        $slice_nr = (int)$request->get('slice_nr');
        $code = $request->get('code');
        $bgcolor = $request->get('bgcolor');

        $tabmenu_items = $this->menuHelper->getTabMenuItems_work($main_menu_item, $galleries, $tab_menu, $gallery_type, $slice_nr);
        $colormenu_items = $this->menuHelper->getColorMenuItems_work($main_menu_item, $tab_menu, $gallery_type, $slice_nr, $code);

        // set context
        $this->context['bgcolor'] = $bgcolor;
        $this->context['tabmenu_items'] = $tabmenu_items;
        $this->context['back_url'] = $tabmenu_items[0]->href;
        $this->context['colormenu_items'] = $colormenu_items;
        $this->context['select_background_color'] = $this->helper->trans('page.work.select_background_color');
        $this->context['img_src_background_transp'] = $this->helper->getBaseUrl() . '/web/resources/appImages/Achtergr_transp_110x110px.png';
        $this->context['back_button_caption'] = $this->helper->trans('page.work.back_button_caption');

        // get row holding work
        $qb = $this->db->createQueryBuilder()
            ->select('*')
            ->from('twerk')
            ->where('Code = :code')
            ->setParameters(array('code' => $code)
            )
        ;
        $res = $qb->execute();
        $row = $res->fetch();

        $oWork = $this->get_oWork($row);

        // view
        $data = array('context'=>$this->context, 'work' => $oWork);
        $view = $this->app['blade']->view('pages.work', $data);
        return $view;
    }

    protected function get_oWork(array $row):work{
        $oWork = new work();
        $oWork->jaar = $row['Jaar'];
        $oWork->hoogte = $row['Hoogte'];
        $oWork->breedte = $row['Breedte'];

        // set image src, height, width, alt text
        $img_path = $this->helper->getBaseUrl() . '/web/resources/images';
        $img_year = substr($row['Code'], 0, 4);
        $img_file = $row['Code'] . ".jpg";
        $oWork->img_src = "$img_path/$img_year/$img_file";

        // img_height + img_width: calculation with max_height and max_width
        if ($row['ImageWidth'] > $row['ImageHeight']) {
            $temp = $row['ImageWidth'];
            $width = $this->img_max_width;
            $height = $row['ImageHeight'] / $temp * $this->img_max_height;
        } else {
            $temp = $row['ImageHeight'];
            $height = $this->img_max_height;
            $width = $row['ImageWidth'] / $temp * $this->img_max_width;
        }
        $oWork->img_height = $height;
        $oWork->img_width = $width;
        $oWork->img_alt = $img_file;

        if ($this->locale === 'nl'){
            $oWork->titel = $row['Titel'];
            $oWork->materiaal = $row['Materiaal'];
            $oWork->ondergrond = $row['Ondergrond'];
            $oWork->techniek = $row['Techniek'];
            $oWork->beschikbaarheid = $row['Beschikbaarheid'];
        } else {
            // translate some values if locale is 'en'
            $oWork->titel = $row['en_Titel'];
            $oWork->materiaal = $this->helper->transValue('page.work', $row['Materiaal']);
            $oWork->ondergrond = $this->helper->transValue('page.work', $row['Ondergrond']);
            $oWork->techniek = $this->helper->transValue('page.work', $row['Techniek']);
            $oWork->beschikbaarheid = $this->helper->transValue('page.work', $row['Beschikbaarheid']);
        }

        return $oWork;
    }

}

class work{
    public $img_src;
    public $img_height;
    public $img_width;
    public $img_alt;
    public $titel;
    public $jaar;
    public $hoogte;
    public $breedte;
    public $materiaal;
    public $ondergrond;
    public $techniek;
    public $beschikbaarheid;

}

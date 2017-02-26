<?php
// src/Studio/Controller/WorkController.php
namespace Studio\Controllers;

use app\MyApplication;
use app\Helpers\Helper;
use app\Helpers\MenuHelper;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Silex\Api\ControllerProviderInterface;

class GalleryController extends BaseController implements ControllerProviderInterface{

    protected $db;

    public function __construct(Helper $helper)  {
        parent::__construct($helper);
        $this->db = $this->app['db'];
        define('ICON_HEIGHT', 120);
        define('ICON_WIDTH', 120);
        define('SLICE_SIZE', 16);
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        // route for call back to calling gallery
        $controllers->get('{galleries}/{tab_menu}/{gallery_type}/{slice_nr}',
            function (Request $request) {
                return $this->getResponse($request);})
        ->assert('galleries', 'galleries1|galleries2')
        ->assert('gallery_type', '[cegkv]')
        ->assert('slice_nr', '[\d]+')
        ->bind('galleries');

        // route for main menu, gallery 1
        $controllers->get('{galleries}/{tab_menu}/{gallery_type}/{slice_nr}',
            function (Request $request) {
                return $this->getResponse($request);})
            ->assert('galleries', 'galleries1')
            ->value('tab_menu', 'gallery1')
            ->value('gallery_type', 'g')
            ->value('slice_nr', 1)
            ->bind('galleries1');

        // route for main menu, gallery 2
        $controllers->get('{galleries}/{tab_menu}/{gallery_type}/{slice_nr}',
            function (Request $request) {
                return $this->getResponse($request);})
            ->assert('galleries', 'galleries2')
            ->value('tab_menu', 'collages')
            ->value('gallery_type', 'c')
            ->value('slice_nr', 1)
            ->bind('galleries2');

        return $controllers;
    }

    public function getResponse(Request $request):string{
        // get req parameters
        $galleries = $request->get('galleries');
        $tab_menu = $request->get('tab_menu');
        $gallery_type= $request->get('gallery_type');
        $slice_nr= (int)$request->get('slice_nr');

        $main_menu_item = $this->context['active_menu'];
        $active_tabmenu = "$tab_menu/$gallery_type/".(string)$slice_nr; // e.g.: "gallery2/g/2"

        // calc $galerie_volgnr
        $galerie_volgnr_from = (int)($slice_nr - 1) * SLICE_SIZE + 1;

        // get rows holding icon info
        $qb = $this->db->createQueryBuilder()
            ->select('*')
            ->from('twerk')
            ->where('GalerieType = :galerie_type')
            ->andWhere('GalerieVolgNr >= :galerie_volgnr')
            ->orderBy('GalerieVolgNr')
            ->setParameters(
                array(
                    'galerie_type' => $gallery_type,
                    'galerie_volgnr' => $galerie_volgnr_from
                )
            )
            ->setMaxResults(SLICE_SIZE)
        ;
        $res = $qb->execute();
        $rows = $res->fetchAll();

        // fill $aIcons array
        $path = $this->helper->getBaseUrl();
        $path .= '/web/resources/images';
        $aIcons = [];

        foreach ($rows as $id => $row) {
            if ($row['ImageHeight'] === 0) {
                $msg = "Work '" . $row['Code'] . " - " . $row['Titel'] .
                   "' has an image height of 0!<br>Correct this in the database!";
                throw new \Exception($msg);
            }
            if ($row['ImageWidth'] === 0) {
                $msg = "Work '" . $row['Code'] . " - " . $row['Titel'] .
                    "' has an image width of 0!<br>Correct this in the database!";
                throw new \Exception($msg);
            }

            $year = substr($row['Code'], 0, 4);
            $path2 = "$path/$year/";

            $icon = $this->get_oIcon($main_menu_item, $tab_menu, $gallery_type, $slice_nr
                                     ,$row, $path2);

            $aIcons[] = $icon;
        }

        // get tabmenu items
        $tabmenu_items = $this->menuHelper->getTabMenuItems($main_menu_item, $active_tabmenu, $this->locale);
        $this->context['tabmenu_items'] = $tabmenu_items;

        // view
        $data = array('context'=>$this->context, 'icons' => $aIcons);
        $view = $this->app['blade']->view('pages.gallery', $data);
        return $view;
    }

    function get_oIcon(
        string $main_menu_item, string $tab_menu,
        string $gallery_type, int $slice_nr,
        array $row, string $path_in):icon {
        // create, fill and return icon object
        $oIcon = new icon();

        // title
        switch ($this->locale) {
            case 'nl':
                $title = $row['Titel'];
                break;
            default:
                $title = $row['en_Titel'];
                break;
        }
        $oIcon->title = $title;

        // src
        $file = $row['Code'] . ".jpg";
        $oIcon->src = $path_in . $file;

        // height and width
        if ($row['ImageWidth'] > $row['ImageHeight']) {
            $temp = $row['ImageWidth'];
            $width = ICON_WIDTH;
            $height = (int) ($row['ImageHeight'] / $temp * ICON_HEIGHT + 0.5);
        } else {
            $temp = $row['ImageHeight'];
            $height = ICON_HEIGHT;
            $width = (int) ($row['ImageWidth'] / $temp * ICON_WIDTH + 0.5);
        }

        $oIcon->height = $height;
        $oIcon->width = $width;

        // url to specific icon
        $url = $this->app['url_generator']
            ->generate('work', array(
                    'main_menu' =>$main_menu_item, // gall/galeries1 or gall/galeries2
                    'tab_menu' => $tab_menu,
                    'gallery_type' => $gallery_type,
                    'slice_nr' => $slice_nr,
                    'work' => 'work',
                    'code'=>$row['Code'],
                    'bgcolor'=>'#FFFFFF'
                )
            );
        $oIcon->url = $url;

        return $oIcon;
    }
}

class icon{
    public $src = '';
    public $height=120;
    public $width=120;
    public $title='';
    public $url='';
}
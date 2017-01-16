<?php

namespace app\Helpers;

use app\MyApplication;
use Silex\Exception;

class MenuHelper{
    // get menu for TopMenu or TabMenu or ColorMenu
    // TopMenu and TabMenu are 1st level and 2nd level menu's
    // ColorMenu is only used in Work page "work.blade"
    protected $app;
    protected $helper;
    protected $context;
    protected $active_menu = '';

    public function __construct(MyApplication $app, array $context){
        $this->app = $app;
        $this->helper = new Helper($app);
        $this->context = $context;
        $this->active_menu = $context['active_menu'];
    }

    public function getTopMenuItems():array{
        // get array of menu items for Top Menu
        $locale = $this->context['locale'];

        $keys = array(
            'welcome','gall/galleries1','gall/galleries2',
            'exhibitions','about_the_work','cv',
            'contact', 'flag');

        $items = [];
        foreach($keys as $key){
            $item = new MenuItem();

            // set caption property of item, after translation
            $trans_id = 'menu.'.$key;
            $caption = $this->helper->trans($trans_id);
            $item->caption = $caption;

            // set other item properties
            if ($key !== 'flag'){
                $item->href = "/$key";
            } else {
                if ($locale === 'nl'){
                    $locale2 = 'en';
                    $item->img_src  = $this->context['img_src_UK_flag'];
                } else {
                    $locale2 = 'nl';
                    $item->img_src  = $this->context['img_src_NL_flag'];
                }
                $item->href = "/$locale2/$key";
            }
            if ($key === $this->active_menu){
                $item->active = true;
            }
            // add item to collection
            $items[$key] = $item;
        }
        return $items;
    }

    public function getTabMenuItems(string $main_menu_item, string $active_tabmenu):array{
        // get array of tab menu items
        // tab menu is 2nd level menu and depends on $main_menu_item
        switch($main_menu_item){
            case 'gall/galleries1':
                $keys = array(
                    'gallery1/g/1','gallery2/g/2','gallery3/g/3',
                    'gallery4/g/4','gallery5/g/5','gallery6/g/6',
                    'gallery7/g/7','gallery8/g/8'
                );
                    break;
            case 'gall/galleries2':
                $keys = array(
                    'collages/c/1','chamber_screens/k/1','floor_designs/v/1',
                    'exhibition1/e/1','exhibition2/e/2',);
                break;
            case 'exhibitions':
                $keys = array(
                    'group','solo','duo',
                    'stock','purchases','assignment',
                    'charity');
                break;
            case 'about_the_work':
                $keys = array(
                    'about_the_work','geometry','publications',
                    'literature');
                break;
            case 'contact':
                $locale = $this->context['locale'];
                $keys = array('contact', 'studio_visit', 'links','about_this_site');
                if ($locale !== 'nl'){
                    $key = array_search('studio_visit', $keys);
                    unset($keys[$key]);}
                break;
            case 'cv':
                $keys = array('cv');
                break;
            default:
                $msg = "unknown tabmenu_type '$main_menu_item'!";
                throw new \Exception($msg);
                break;
        }
        $items = [];
        $prefix = 'tabmenu.' . $main_menu_item . '.';
        foreach($keys as $key){
            $trans_id = $prefix.$key;
            $caption = $this->helper->trans($trans_id);
            $item = new MenuItem();
            $item->caption = $caption;
            $item->href = "/$main_menu_item/$key";
            if ($key === $active_tabmenu){
                $item->active = true;
            }
            $items[$key] = $item;
        }

        return $items;
    }

    public function getTabMenuItems_work(
        string $main_menu_item,
        string $galleries, string $tab_menu,
        string $gallery_type, int $slice_nr):array{
        // get array of tab menu items for page 'work.blade'
        // this menu is a 3rd level menu, holding only 1 item,
        // that points back to the 2nd level gallery tabmenu

        // set route_name

        // translate "tabmenu.galleries/1.gallery3/g/3"
        $items = [];
        $item = new MenuItem();

        // set caption property of item, after translation
        $trans_id = "tabmenu.$main_menu_item.$tab_menu/$gallery_type/".(string)$slice_nr;
        $caption = $this->helper->trans($trans_id);
        $item->caption = $caption;

        // set href property, holding url that points back to the 2nd level gallery tabmenu
        $url = $this->app['url_generator']
            ->generate('galleries', array(
                    'galleries' => $galleries,
                    'tab_menu' => $tab_menu,
                    'gallery_type' => $gallery_type,
                    'slice_nr' => (string)$slice_nr
                )
            );
        $item->href = $url;

        // set other item property
        $item->active = true;

        // add item to collection
        $items[0] = $item;

        return $items;
    }

    public function getColorMenuItems_work(
        string $main_menu_item, string $tab_menu,
        string $gallery_type, int $slice_nr, string $code):array{
        // get array of ColorMenu items for page 'work.blade'
        // color codes are used in page 'work.blade' fir a series of "background color" icons,
        // icons that can be clicked to change background color for an actual work

        $items = [];

        $keys = array(
            'white'=>'#FFFFFF',
            'soft_yellow'=>'#FFFFD7',
            'yellow'=>'#FFFFBE',
            'soft_peach'=>'#FFE4CC',
            'peach'=>'#FFDCB4',
            'soft_green'=>'#F4FFF5',
            'green'=>'#EAFFEA',
            'soft_blue'=>'#F6FFFF',
            'blue'=>'#E8FFFF');
        foreach($keys as $key=>$value){
            $item = new ColorMenuItem();

            // url to specific icon
            $url = $this->app['url_generator']
                ->generate('work',array(
                        'main_menu' =>$main_menu_item,
                        'tab_menu' => $tab_menu,
                        'gallery_type' => $gallery_type,
                        'slice_nr' => (string)$slice_nr,
                        'code'=>$code,
                        'bgcolor'=>$value
                    )
                );
            $item->href = $url;

            // alt text
            $trans_id = "page.work.background_color.$key";
            $alt = $this->helper->trans($trans_id);
            $item->alt = $alt;

            // add item to collection
            $items[$key] = $item;
        }
        return $items;
    }
}


class MenuItem{
    // class for MenuItem object
    public $key;
    public $href;
    public $img_src = null;
    public $active = false;
}

class ColorMenuItem{
    // class for ColorMenuItem object
    public $href;
    public $alt = '';
}
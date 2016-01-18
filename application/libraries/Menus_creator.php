<?php
/**
 * Created with: PhpStorm.
 * User: adrian.voicu
 * Date: 12/12/2014
 * Time: 4:54 PM
 */

class Menus_creator {

    public $language_slug;

    public function __construct()
    {
        $this->load->model('menu_model');
        $this->load->model('menu_item_model');
        $this->language_slug = $this->session->set_language;
    }

    public function get_menu($menu_name, $language_slug = NULL, $return = 'array')
    {
        $language_slug = (is_null($language_slug)) ? $this->language_slug : $language_slug;

        $menus = $this->menu_model->set_cache('get_menus')->get_all();

        if(!empty($menus))
        {
            foreach($menus as $menu)
            {
                if($menu->title == $menu_name)
                {
                    $menu_id = $menu->id;
                    break;
                }
            }

            if(!isset($menu_id))
            {
                return FALSE;
            }

            //$this->menu_item_model->db->order_by('order','ASC');

            $menu_items = $this->menu_item_model
                ->where('menu_id',$menu_id)
                ->order_by('order')
                ->with_translations('fields:language_slug,title,url,item_id,absolute_path')
                ->set_cache('get_'.$menu_name,3600)
                ->get_all();

            $the_menu = array();

            if(!empty($menu_items))
            {
                foreach($menu_items as $item)
                {
                    if(!empty($item->translations))
                    {
                        foreach ($item->translations as $translation)
                        {
                            if($translation->language_slug==$language_slug) {
                                $url = ($translation->absolute_path == '1') ? $translation->url :  site_url($_SESSION['lang_slug'] . $translation->url);
                                $the_menu[] = array('id'=>$translation->item_id, 'parent_id'=>$item->parent_id, 'title'=>$translation->title, 'url'=>$url);
                            }
                        }
                    }
                }
            }

            switch ($return) {
                case 'array':
                    $the_menu = $this->ordered_list($the_menu);
                    break;
                case 'html_menu': {
                    $the_menu = $this->html_menu($the_menu);
                }
                case 'bootstrap_menu': {
                    $the_menu = $this->bootstrap_menu($the_menu);
                }
            }
            return $the_menu;
        }
    }

    public function ordered_list($array,$parent_id = 0)
    {
        $temp_array = array();
        foreach($array as $element)
        {
            if ($element['parent_id'] == $parent_id)
            {
                $element['subs'] = $this->ordered_menu($array, $element['id']);
                $temp_array[] = $element;
            }
        }
        return $temp_array;
    }

    function html_menu($array,$parent_id = 0,$parents = array())
    {
        if($parent_id==0)
        {
            foreach ($array as $element)
            {
                if (($element['parent_id'] != 0) && !in_array($element['parent_id'],$parents))
                {
                    $parents[] = $element['parent_id'];
                }
            }
        }
        $menu_html = '';
        foreach($array as $element)
        {
            if($element['parent_id']==$parent_id)
            {
                $menu_html .= '<li><a href="'.$element['url'].'">'.$element['title'].'</a>';
                if(in_array($element['id'],$parents))
                {
                    $menu_html .= '<ul>';
                    $menu_html .= $this->html_menu($array, $element['id'], $parents);
                    $menu_html .= '</ul>';
                }
                $menu_html .= '</li>';
            }
        }
        $menu_html .= '';
        return $menu_html;
    }

    function bootstrap_menu($array,$parent_id = 0,$parents = array())
    {
        if($parent_id==0)
        {
            foreach ($array as $element) {
                if (($element['parent_id'] != 0) && !in_array($element['parent_id'],$parents)) {
                    $parents[] = $element['parent_id'];
                }
            }
        }
        $menu_html = '';
        foreach($array as $element)
        {
            if($element['parent_id']==$parent_id)
            {
                if(in_array($element['id'],$parents))
                {
                    $menu_html .= '<li class="dropdown">';
                    $menu_html .= '<a href="'.$element['url'].'" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">'.$element['title'].' <span class="caret"></span></a>';
                }
                else {
                    $menu_html .= '<li>';
                    $menu_html .= '<a href="' . $element['url'] . '">' . $element['title'] . '</a>';
                }
                if(in_array($element['id'],$parents))
                {
                    $menu_html .= '<ul class="dropdown-menu" role="menu">';
                    $menu_html .= $this->bootstrap_menu($array, $element['id'], $parents);
                    $menu_html .= '</ul>';
                }
                $menu_html .= '</li>';
            }
        }
        return $menu_html;
    }

    public function __get($var)
    {
        return $CI =& get_instance()->$var;
    }
}
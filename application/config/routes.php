<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';
$route['404_override'] = 'findcontent';
$route['translate_uri_dashes'] = TRUE;

$route['admin'] = 'admin/dashboard';



$controllers_methods = array(
    'en' => array(
        'willkommen/list' => 'welcome/list',
        'willkommen' => 'welcome'
    ),
    'fr' => array(
        'bienvenu/list' => 'welcome/list',
        'bienvenu' => 'welcome'
    )
);


//$route['^(\w{2})/(.*)$'] = '$2';
//$route['^(\w{2})$'] = $route['default_controller'];

$route['^(\w{2})/(.*)'] = function($language, $link) use ($controllers_methods)
{
    if(array_key_exists($language,$controllers_methods))
    {
        foreach ($controllers_methods[$language] as $key => $sym_link) {
            if (strrpos($link, $key, 0) !== FALSE) {
                $new_link = ltrim($link, $key);
                $new_link = $sym_link . $new_link;
                return $new_link;
                break;
            }
            else
            {
                return 'findcontent/index';
            }
        }
    }
};
$route['^(\w{2})$'] = $route['default_controller'];

/* End of file routes.php */
/* Location: ./application/config/routes.php */
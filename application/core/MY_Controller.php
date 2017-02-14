<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public $website;
	protected $data = array();
	protected $langs = array();
    protected $default_lang;
    protected $current_lang;
	function __construct()
	{
		parent::__construct();

        // First of all let's see what languages we have and also get the default language

        $this->load->model('language_model');
        $available_languages = $this->language_model->get_all();
        if(isset($available_languages))
        {
            foreach($available_languages as $language)
            {
                $this->langs[$language->slug] = array(
                    'id'=>$language->id,
                    'slug'=>$language->slug,
                    'name'=>$language->language_name,
                    'language_directory'=>$language->language_directory,
                    'language_code'=>$language->language_code,
                    'alternate_link'=>'/'.$language->slug,
                    'default'=>$language->default);

                if($language->default == '1')
                {
                    $_SESSION['default_lang'] = $language->slug;
                    $this->default_lang = $language->slug;
                    $this->langs[$language->slug]['alternate_link'] = '';
                }
            }
        }

        // Verify if we have a language set in the URL;
        $lang_slug = $this->uri->segment(1);


        // If we do, and we have that languages in our set of languages we store the language slug in the session
        if(isset($lang_slug) && array_key_exists($lang_slug, $this->langs))
        {
            $this->current_lang = $lang_slug;
            $_SESSION['set_language'] = $lang_slug;

            // Let's make sure that if the default language is in url, we remove it from there and redirect
            if($lang_slug===$this->default_lang)
            {
                $segs = $this->uri->segment_array();
                unset($segs[1]);
                $new_url = implode('/',$segs);
                redirect($new_url, 'location', 301);
            }
        }
        //else if a session variable set_language is not set but there exists a cookie named set_language, we will use those
        // If not, we set the language session to the default language
        else
        {
            if(!isset($_SESSION['set_language']))
            {
                $set_language = get_cookie('set_language',TRUE);
                if(isset($set_language)  && array_key_exists($set_language, $this->langs))
                {
                    $this->current_lang = $set_language;
                    $_SESSION['set_language'] = $this->current_lang;
                    //$language  = ($this->current_lang==$this->default_lang) ? '' : $this->current_lang;
                    //redirect($language);

                } else {
                    # set the default lang when visiting the site for the first time
                    $this->current_lang = $this->default_lang;
                    $_SESSION['set_language'] = $this->default_lang;	
                }
            }
            else
            {
                $this->current_lang = $this->default_lang;
                $_SESSION['set_language'] = $this->default_lang;
            }
        }
        // We set a cookie so that if the visitor come again, he will be redirected to its chosen language
        set_cookie('set_language',$_SESSION['set_language'],2600000);

        // Now we store the languages as a $data key, just in case we need them in our views
        $this->data['langs'] = $this->langs;

        // Also let's have our current language in a $data key
        $this->data['current_lang'] = $this->langs[$this->current_lang];

        // For links inside our views we only need the lang slug. If the current language is the default language we don't need to append the language slug to our links
        if($this->current_lang != $this->default_lang)
        {
            $this->data['lang_slug'] = $this->current_lang.'/';
        }
        else
        {
            $this->data['lang_slug'] = '';
        }

        $_SESSION['lang_slug'] = $this->data['lang_slug'];


        $this->load->model('website_model');
        $this->website = $this->website_model->get();
        $this->data['website'] = $this->website;

        // Get the default page description and title from the database
        $this->data['page_title'] = $this->website->page_title;
        $this->data['page_description'] = $this->website->page_title;
		$this->data['before_head'] = '';
		$this->data['before_body'] = '';
	}

	protected function render($the_view = NULL, $template = 'master')
	{
        if($template == 'json' || $this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			echo json_encode($this->data);
		}
		elseif(is_null($template))
		{
			$this->load->view($the_view,$this->data);
		}
		else
		{
			$this->data['the_view_content'] = (is_null($the_view)) ? '' : $this->load->view($the_view, $this->data, TRUE);
			$this->load->view('templates/' . $template . '_view', $this->data);
		}
	}
}

class Admin_Controller extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
        $this->load->library('postal');
		$this->load->helper('url');
		if (!$this->ion_auth->logged_in())
		{
            $_SESSION['redirect_to'] = current_url();
			//redirect them to the login page
			redirect('admin/user/login', 'refresh');
		}
        $current_user = $this->ion_auth->user()->row();
        $this->user_id = $current_user->id;
		$this->data['current_user'] = $current_user;
		$this->data['current_user_menu'] = '';
		if($this->ion_auth->in_group('admin'))
		{
			$this->data['current_user_menu'] = $this->load->view('templates/_parts/user_menu_admin_view.php', NULL, TRUE);
		}

		$this->data['page_title'] = $this->website->page_title;
        	$this->data['page_description'] = $this->website->page_title;
	}
	protected function render($the_view = NULL, $template = 'admin_master')
	{
		parent::render($the_view, $template);
	}
}

class Public_Controller extends MY_Controller
{
    function __construct()
	{
        parent::__construct();
        $this->load->model('banned_model');
        $ips = $this->banned_model->fields('ip')->set_cache('banned_ips',3600)->get_all();
        $banned_ips = array();
        if(!empty($ips))
        {
            foreach($ips as $ip)
            {
                $banned_ips[] = $ip->ip;
            }
        }
        if(in_array($_SERVER['REMOTE_ADDR'],$banned_ips))
        {
            echo 'You are banned from this site.';
            exit;
        }
        if($this->website->status == '0') {
            $this->load->library('ion_auth');
            if (!$this->ion_auth->logged_in()) {
                redirect('offline', 'refresh', 503);
            }
        }
	}

    protected function render($the_view = NULL, $template = 'public_master')
    {
        /* load a generic language file (this language file will be used across many pages - like in the footer of pages) */
        $this->load->language('app_lang',$this->langs[$this->current_lang]['language_directory']);

        /* you can load a specific language file inside the controller constructor with $this->language_file = ''.
        The file will be loaded from the app_files directory inside specific language directory */
        if(!isset($this->language_file))
        {
            $uri = explode('/', uri_string());
            $calling_class = get_class($this);
            $url = array();
            foreach ($uri as $key => $value) {
                if(trim(strlen($value)>0))
                {
                    if (is_numeric($value) || ($value==$this->current_lang)) unset($uri[$key]);
                    else $url[$key] = str_replace('-', '_', $value);
                }
            }

            $methods = debug_backtrace();

            foreach($methods as $method)
            {
                if($method['function']!=='render' && method_exists($calling_class,$method['function']))
                {
                    $current_method = $method['function'];
                }
            }

            $method_key = array_search($current_method, $url);
            $language_file_array = array_slice($url, 0, ($method_key + 1));

            $calling_class = strtolower($calling_class);
            if (!in_array($calling_class, $language_file_array)) $language_file_array[] = $calling_class;
            if (!in_array($current_method, $language_file_array)) $language_file_array[] = $current_method;
            $this->language_file = implode('_', $language_file_array);
        }

        /* verify if a language file specific to the method exists. If it does, load it. If it doesn't, simply do not load anything */
        if(file_exists(APPPATH.'language/'.$this->langs[$this->current_lang]['language_directory'].'/app_files/'.strtolower($this->language_file).'_lang.php')) {
            $this->lang->load('app_files/'.strtolower($this->language_file).'_lang', $this->langs[$this->current_lang]['language_directory']);
        }

        $this->load->library('menus_creator');
        $this->data['top_menu'] = $this->menus_creator->get_menu('top-menu',$this->current_lang,'bootstrap_menu');
        parent::render($the_view, $template);
    }
}

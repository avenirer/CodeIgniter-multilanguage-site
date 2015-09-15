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
        }
        // If not, we set the language session to the default language
        else
        {
            $this->current_lang = $this->default_lang;
            $_SESSION['set_language'] = $this->default_lang;
        }
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
        $this->data['page_title'] = 'CI App';
        $this->data['page_description'] = 'CI_App';
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

		$this->data['page_title'] = 'CI App - Dashboard';
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

        $language = $this->data['current_lang'];
        $idiom = $language['language_directory'];
        $this->load->language('interface_lang',$idiom);
	}

    protected function render($the_view = NULL, $template = 'public_master')
    {
        $this->load->library('menus');
        $this->data['top_menu'] = $this->menus->get_menu('top-menu',$this->current_lang,'bootstrap_menu');
        parent::render($the_view, $template);
    }


}
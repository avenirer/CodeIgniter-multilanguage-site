<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Findcontent extends Public_Controller
{
    public $translation;

    function __construct()
    {
        parent::__construct();
        $this->load->model('slug_model');
        $this->load->model('content_model');
        $this->load->model('content_translation_model');
        $this->load->library('ion_auth');
    }

    public function index()
    {
        $total_segments = $this->uri->total_segments();
        for($i=1;$i<=$total_segments;$i++)
        {
            $url = $this->uri->segment($i);
            if(!array_key_exists($url,$this->langs))
            {
                break;
            }
        }

        if($slug = $this->slug_model->where(array('url'=>$url, 'language_slug'=>$_SESSION['set_language']))->get())
        {
            if($slug->redirect != '0' || $slug->language_slug!=$_SESSION['set_language'])
            {
                if($slug->redirect!='0')
                {
                    $slug = $this->slug_model->get($slug->redirect);
                    if($slug->language_slug == $this->default_lang)
                    {
                        redirect($slug->url, 'location', 301);
                    }
                    else
                    {
                        redirect($slug->language_slug.'/'.$slug->url,'refresh');
                    }
                }
            }
            $content_id = $slug->content_id;
            $language_slug = $slug->language_slug;

            $where = array(
                'content_id'=>$content_id,
                'language_slug'=>$language_slug);

            $where_content=array();

            if(!$this->ion_auth->logged_in())
            {
                $where_content['published'] = '1';
            }

            $this->translation = $this->content_translation_model->where($where)->with_content_type(array('where'=>$where_content))->get();

            //$content = $this->content_model->where('published','1')->with_translations(array('where'=>'`language_slug` = \'' . $language_slug . '\''))->get($content_id);

            if(is_object($this->translation) && !empty($this->translation))
            {
                $this->{'_'.$this->translation->content_type->content_type.'_show'}($content_id,$language_slug);

            }

            else {
				// the content translation is inactive show 404 page.
				show_404();
				exit;
			}
        }
        elseif($slug = $this->slug_model->where(array('url'=>$url))->get())
        {
            if($slug->language_slug == $this->default_lang)
            {
                redirect($slug->url, 'location', 301);
            }
            else
            {
                redirect($slug->language_slug.'/'.$slug->url,'refresh');
            }
        }
        else
        {
            #echo 'oups...'; // fix error header already sent when show 404 page.
            show_404();
            exit;
        }
    }

    // controller that deals with the category
    private function _category_show($content_id,$language_slug)
    {

        $total_segments = $this->uri->total_segments();

        $url = $this->uri->segment_array();

        $page_number = is_numeric($url[sizeof($url)]) ? $url[sizeof($url)] : '1';

        $posts = $this->content_translation_model->get_category_posts($content_id,$language_slug,$page_number);

        $this->data['posts'] = $posts;

        $alternate_content = $this->slug_model->where(array('content_id' => $this->translation->content_id, 'redirect' => '0', 'content_type'=>'category', 'translation_id !='=>$this->translation->id))->get_all();
        if (!empty($alternate_content))
        {
            foreach($alternate_content as $link)
            {
                $this->langs[$link->language_slug]['alternate_link'] = (($link->language_slug!==$this->default_lang) ? '/'.$link->language_slug : '').'/'.$link->url;
            }
        }
        $this->data['langs'] = $this->langs;
        $this->data['page_title'] = $this->translation->page_title;
        $this->data['page_description'] = $this->translation->page_description;
        $this->data['page_keywords'] = $this->translation->page_keywords;
        $this->data['title'] = $this->translation->title;
        $this->data['content'] = $this->translation->content;

        /*
        echo '<pre>';
        print_r($this->data);
        echo '</pre>';
        */

        $this->render('public/category_view');

    }

    // controller that deals with the page
    private function _page_show($content_id,$language_slug)
    {
        $alternate_content = $this->slug_model->where(array('content_id' => $this->translation->content_id, 'redirect' => '0'))->get_all();
        if (!empty($alternate_content))
        {
            foreach($alternate_content as $link)
            {
                $this->langs[$link->language_slug]['alternate_link'] = (($link->language_slug!==$this->default_lang) ? '/'.$link->language_slug : '').'/'.$link->url;
            }
        }
        $this->data['langs'] = $this->langs;
        $this->data['page_title'] = $this->translation->page_title;
        $this->data['page_description'] = $this->translation->page_description;
        $this->data['page_keywords'] = $this->translation->page_keywords;
        $this->data['title'] = $this->translation->title;
        $this->data['content'] = $this->translation->content;



        $this->render('public/page_view');

    }

    // controller that deals with the post
    private function _post_show($content_id,$language_slug)
    {

        $alternate_content = $this->slug_model->where(array('content_id' => $this->translation->id, 'redirect' => '0'))->get_all();
        if (!empty($alternate_content))
        {
            foreach($alternate_content as $link)
            {
                $this->langs[$link->language_slug]['alternate_link'] = (($link->language_slug!==$this->default_lang) ? '/'.$link->language_slug : '').'/'.$link->url;
            }
        }
        $this->data['langs'] = $this->langs;
        $this->data['page_title'] = $this->translation->page_title;
        $this->data['page_description'] = $this->translation->page_description;
        $this->data['page_keywords'] = $this->translation->page_keywords;
        $this->data['title'] = $this->translation->title;
        $this->data['content'] = $this->translation->content;



        $this->render('public/post_view');

    }
}

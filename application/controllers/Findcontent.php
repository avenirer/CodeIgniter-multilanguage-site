<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Findcontent extends Public_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('slug_model');
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
        //$url = $this->uri->segment(1);
        $this->load->model('slug_model');
        if($slug = $this->slug_model->where(array('url'=>$url))->get())
        {
            if($slug->redirect != '0' || $slug->language_slug!=$this->current_lang)
            {
                if($slug->redirect!='0')
                {
                    $slug = $this->slug_model->get($slug->redirect);
                }
                if($slug->language_slug == $this->default_lang)
                {
                    redirect($slug->url, 'auto', 301);
                }
                else
                {
                    redirect($slug->language_slug.'/'.$slug->url,'refresh');
                }
            }
            $content_type = $slug->content_type;
            $content_id = $slug->content_id;
            $language_slug = $slug->language_slug;

            if(!$this->load->model($content_type.'_model','content_model'))
            {
                echo 'bau';
                //show_404();
                exit;
            }
            if($content = $this->content_model->where('published','1')->with_translations('where:`language_slug` = \'' . $language_slug . '\'')->get($content_id)->translations[0]) {
                $alternate_content = $this->slug_model->where(array('content_type' => $content_type, 'content_id' => $content_id, 'redirect' => '0'))->get_all();
                if (!empty($alternate_content))
                {
                    foreach($alternate_content as $link)
                    {
                        $this->langs[$link->language_slug]['alternate_link'] = (($link->language_slug!==$this->default_lang) ? '/'.$link->language_slug : '').'/'.$link->url;
                    }
                }
                $this->data['langs'] = $this->langs;
                $this->data['page_title'] = $content->page_title;
                $this->data['page_description'] = $content->page_description;
                $this->data['page_keywords'] = $content->page_keywords;
                $this->data['title'] = $content->title;
                $this->data['content'] = $content->content;

                $this->render('public/'.$content_type . '_view');
            }
        }
        else
        {
            echo 'oups...';
            show_404();
            exit;
        }
    }
}
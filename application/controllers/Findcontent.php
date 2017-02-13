<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Findcontent extends Public_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('slug_model');
        $this->load->model('content_model');
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

            $content = $this->content_model->where('published','1')->with_translations('where:`language_slug` = \'' . $language_slug . '\'')->get($content_id);

            if(is_object($content) && $translation = $content->translations[0])
            {
                $this->{'_'.$content->content_type.'_show'}($content);

            } else {
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
    private function _category_show($content)
    {
        $translation = $content->translations[0];
        $alternate_content = $this->slug_model->where(array('content_id' => $content->id, 'redirect' => '0'))->get_all();
        if (!empty($alternate_content))
        {
            foreach($alternate_content as $link)
            {
                $this->langs[$link->language_slug]['alternate_link'] = (($link->language_slug!==$this->default_lang) ? '/'.$link->language_slug : '').'/'.$link->url;
            }
        }
        $this->data['langs'] = $this->langs;
        $this->data['page_title'] = $translation->page_title;
        $this->data['page_description'] = $translation->page_description;
        $this->data['page_keywords'] = $translation->page_keywords;
        $this->data['title'] = $translation->title;
        $this->data['content'] = $translation->content;

        $this->render('public/'.$content->content_type . '_view');

    }
}

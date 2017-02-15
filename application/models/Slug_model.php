<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Slug_model extends MY_Model
{
    public $table = 'slugs';
    public $primary_key = 'id';
    public $before_create = array('created_by');
    public $before_update = array('updated_by');

    public function created_by($data)
    {
        $data['created_by'] = $this->user_id;
        return $data;
    }

    public function updated_by($data)
    {
        $data['updated_by'] = $this->user_id;
        return $data;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function verify_insert($insert_data)
    {
        $the_new_slug = $this->_verify_slug($insert_data['url'],$insert_data['language_slug']);
        $insert_data['url'] = $the_new_slug;
        if($slug_id = $this->insert($insert_data))
        {
            $this->where(array('content_type'=>$insert_data['content_type'], 'translation_id'=>$insert_data['translation_id'], 'id !='=>$slug_id))->update(array('redirect'=>$slug_id));
            return TRUE;
        }
        return FALSE;
    }

    private function _verify_slug($str,$language)
    {
        if($this->where(array('url'=>$str,'language_slug'=>$language,'redirect'=>'0'))->get() !== FALSE)
        {
            $parts = explode('-',$str);
            if(is_numeric($parts[sizeof($parts)-1]))
            {
                $parts[sizeof($parts)-1] = $parts[sizeof($parts)-1]++;
            }
            else
            {
                $parts[] = '1';
            }
            $str = implode('-',$parts);
            $this->_verify_slug($str,$language);
        }
        elseif($this->where(array('url'=>$str,'language_slug'=>$language,'redirect != '=>'0'))->get() !== FALSE)
        {
            $this->where(array('url'=>$str,'language_slug'=>$language,'redirect != '=>'0'))->delete();
        }
        return $str;
    }

    /*
     public $rules = array(
        'insert' => array(
            'parent_id' => array('field'=>'parent_id','label'=>'Parent ID','rules'=>'trim|is_natural|required'),
            'title' => array('field'=>'title','label'=>'Title','rules'=>'trim|required'),
            'menu_title' => array('field'=>'menu_title','label'=>'Menu title','rules'=>''),
            'slug' => array('field'=>'slug', 'label'=>'Slug', 'rules'=>'trim'),
            'order' => array('field'=>'order','label'=>'Order','rules'=>'trim|is_natural|required'),
            'teaser' => array('field'=>'teaser','label'=>'Teaser','rules'=>''),
            'content' => array('field'=>'content','label'=>'Content','rules'=>'trim'),
            'page_title' => array('field'=>'page_title','label'=>'Page title','rules'=>''),
            'page_description' => array('field'=>'page_description','label'=>'Page description','rules'=>''),
            'page_keywords' => array('field'=>'page_keywords','label'=>'Page keywords','rules'=>''),
            'page_id' => array('field'=>'page_id', 'label'=>'Page ID', 'rules'=>'trim|is_natural|required'),
            'language_id' => array('field'=>'language_id','label'=>'language_id','rules'=>'trim|is_natural_no_zero|required')
        ),
        'update' => array()
    );
    */
}
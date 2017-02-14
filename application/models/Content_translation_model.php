<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Content_translation_model extends MY_Model
{

    public $table = 'content_translations';
    public $primary_key = 'id';
    public $before_create = array('created_by');
    public $before_update = array('updated_by');

    public function __construct()
    {
        parent::__construct();
        $this->has_one['content_type'] = array('foreign_model'=>'Content_model','foreign_table'=>'contents','foreign_key'=>'id','local_key'=>'content_id');
        $this->has_one['slug'] = array('foreign_model'=>'Slug_model','foreign_table'=>'slugs','foreign_key'=>'translation_id','local_key'=>'id');
    }

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

    public function get_category_posts($content_id,$language_slug,$page_number)
    {
        $this->_database->select('content_translations.title,content_translations.teaser,content_translations.created_at,content_translations.updated_at,slugs.url');
        $this->_database->where('content_translations.language_slug',$language_slug);
        $this->_database->where('contents.content_type','post');
        $this->_database->where('contents.parent_id',$content_id);
        $this->_database->where('slugs.content_type','post');
        $this->_database->order_by('content_translations.updated_at, content_translations.created_at','DESC');
        $this->_database->limit(10,($page_number-1)*10);
        $this->_database->join('contents','content_translations.content_id = contents.id');
        $this->_database->join('slugs','content_translations.id = slugs.translation_id AND slugs.content_type = \'post\'');
        $query = $this->_database->get('content_translations');
        return $query->result();
    }
}
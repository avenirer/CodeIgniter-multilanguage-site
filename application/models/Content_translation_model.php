<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Content_translation_model extends MY_Model
{

    public $table = 'content_translations';
    public $primary_key = 'id';

    public function __construct()
    {
        $this->has_one['page'] = array('Content_model','id','content_id');
        parent::__construct();
    }
}
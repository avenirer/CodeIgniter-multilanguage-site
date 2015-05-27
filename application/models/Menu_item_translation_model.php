<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_item_translation_model extends MY_Model
{
    public $table = 'menu_item_translations';
    public $primary_key = 'id';

    public function __construct()
    {
        $this->has_one['menu_item'] = array('Menu_item_model','id','item_id');
        parent::__construct();
    }
}
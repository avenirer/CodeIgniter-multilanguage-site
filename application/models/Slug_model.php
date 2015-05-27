<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Slug_model extends MY_Model
{
    public $table = 'slugs';
    public $primary_key = 'id';

    public function __construct()
    {
        parent::__construct();
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
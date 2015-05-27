<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Keyword_model extends MY_Model
{
    public $table = 'keywords';
    public $primary_key = 'id';
    public $timestamps = FALSE;

    public function __construct()
    {
        $this->pagination_delimiters = array('<li>','</li>');
        $this->pagination_arrows = array('<span aria-hidden="true">&laquo;</span>','<span aria-hidden="true">&raquo;</span>');
        parent::__construct();
    }
/*
    public $rules = array(
        'insert' => array(
            'parent_id' => array('field'=>'parent_id','label'=>'Parent ID','rules'=>'trim|is_natural|required'),
            'title' => array('field'=>'title','label'=>'Title','rules'=>'trim|required'),
            'menu_title' => array('field'=>'menu_title','label'=>'Menu title','rules'=>'trim'),
            'slug' => array('field'=>'slug', 'label'=>'Slug', 'rules'=>'trim'),
            'order' => array('field'=>'order','label'=>'Order','rules'=>'trim|is_natural|required'),
            'page_title' => array('field'=>'page_title','label'=>'Page title','rules'=>'trim'),
            'page_description' => array('field'=>'page_description','label'=>'Page description','rules'=>'trim'),
            'page_keywords' => array('field'=>'page_keywords','label'=>'Page keywords','rules'=>'trim'),
            'category_id' => array('field'=>'category_id', 'label'=>'Category ID', 'rules'=>'trim|is_natural|required'),
            'language_slug' => array('field'=>'language_slug','label'=>'language_slug','rules'=>'trim|required')
        ),
        'update' => array(
            'word' => array('field'=>'word','label'=>'Parent ID','rules'=>'trim|required'),
            'root_word' => array('field'=>'root_word','label'=>'Title','rules'=>'trim'),
            'noise_word' => array('field'=>'noise_word','label'=>'Noise word','rules'=>'trim'),
            'word_id' => array('field'=>'word_id', 'label'=>'Word ID', 'rules'=>'trim|is_natural_no_zero|required'),
            'language_slug' => array('field'=>'language_slug','label'=>'language_slug','rules'=>'trim|required')
        )
    );
*/
}
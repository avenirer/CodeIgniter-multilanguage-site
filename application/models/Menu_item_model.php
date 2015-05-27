<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_item_model extends MY_Model
{
    public $table = 'menu_items';
    public $primary_key = 'id';

    public function __construct()
    {
        $this->has_many['translations'] = array('Menu_item_translation_model','item_id','id');
        $this->pagination_delimiters = array('<li>','</li>');
        $this->pagination_arrows = array('<span aria-hidden="true">&laquo;</span>','<span aria-hidden="true">&raquo;</span>');
        parent::__construct();
    }

    public $rules = array(
        'insert' => array(
            'parent_id' => array('field'=>'parent_id','label'=>'Parent ID','rules'=>'trim|is_natural|required'),
            'title' => array('field'=>'title','label'=>'Title','rules'=>'trim|required'),
            'url' => array('field'=>'url', 'label'=>'URL', 'rules'=>'trim|required'),
            'absolute_path' => array('field'=>'absolute_path','label'=>'Absolute path','rules'=>'trim|is_natural'),
            'order' => array('field'=>'order','label'=>'Order','rules'=>'trim|is_natural|required'),
            'styling' => array('field'=>'styling','label'=>'Additional styling','rules'=>'trim'),
            'item_id' => array('field'=>'item_id', 'label'=>'item ID', 'rules'=>'trim|is_natural|required'),
            'menu_id' => array('field'=>'menu_id', 'label'=>'Menu ID', 'rules'=>'trim|is_natural_no_zero|required'),
            'language_slug' => array('field'=>'language_slug','label'=>'language_slug','rules'=>'trim|required')
        ),
        'update' => array(
            'parent_id' => array('field'=>'parent_id','label'=>'Parent ID','rules'=>'trim|is_natural|required'),
            'title' => array('field'=>'title','label'=>'Title','rules'=>'trim|required'),
            'url' => array('field'=>'url', 'label'=>'URL', 'rules'=>'trim|required'),
            'absolute_path' => array('field'=>'absolute_path','label'=>'Absolute path','rules'=>'trim|is_natural'),
            'order' => array('field'=>'order','label'=>'Order','rules'=>'trim|is_natural|required'),
            'styling' => array('field'=>'styling','label'=>'Additional styling','rules'=>'trim'),
            'item_id' => array('field'=>'item_id', 'label'=>'item ID', 'rules'=>'trim|is_natural|required'),
            'menu_id' => array('field'=>'menu_id', 'label'=>'Menu ID', 'rules'=>'trim|is_natural_no_zero|required'),
            'translation_id' => array('field'=>'translation_id', 'label'=>'Translation ID', 'rules'=>'trim|is_natural_no_zero|required'),
            'language_slug' => array('field'=>'language_slug','label'=>'language_slug','rules'=>'trim|required')
        )
    );
}
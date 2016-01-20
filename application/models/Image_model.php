<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Image_model extends MY_Model
{

    public function __construct()
    {
        $this->has_many['translations'] = array('Post_translation_model','post_id','id');
        $this->has_one['category'] = array('Category_model','id','category_id');
        $this->pagination_delimiters = array('<li>','</li>');
        $this->pagination_arrows = array('<span aria-hidden="true">&laquo;</span>','<span aria-hidden="true">&raquo;</span>');
        parent::__construct();
    }

    public $rules = array(
        'insert' => array(
            //'content_type' => array('field'=>'content_type','label'=>'Content type', 'rules'=>'trim|required'),
            'content_id' => array('field'=>'content_id','label'=>'Content ID','rules'=>'trim|is_natural_no_zero|required'),
            'titles' => array('field'=>'titles','label'=>'Title(s)','rules'=>'trim'),
            'file_names' => array('field'=>'file_names','label'=>'File name(s)','rules'=>'trim')
        ),
        'update_title' => array(
            'title' => array('field'=>'title','label'=>'Title','rules'=>'trim|required'),
            'image_id' => array('field'=>'image_id','label'=>'Image ID','rules'=>'trim|is_natural_no_zero|required')
        )
        /*
        'update' => array(
            'category_id' => array('field'=>'category_id','label'=>'Parent category ID','rules'=>'trim|is_natural|required'),
            'title' => array('field'=>'title','label'=>'Title','rules'=>'trim|required'),
            'short_title' => array('field'=>'short_title','label'=>'Short title','rules'=>'trim'),
            'slug' => array('field'=>'slug', 'label'=>'Slug', 'rules'=>'trim'),
            'teaser' => array('field'=>'teaser','label'=>'Teaser','rules'=>'trim'),
            'content' => array('field'=>'content','label'=>'Content','rules'=>'trim|required'),
            'page_title' => array('field'=>'page_title','label'=>'Page title','rules'=>'trim|required'),
            'page_description' => array('field'=>'page_description','label'=>'Page description','rules'=>'trim'),
            'page_keywords' => array('field'=>'page_keywords','label'=>'Page keywords','rules'=>'trim'),
            'translation_id' => array('field'=>'translation_id', 'label'=>'Translation ID', 'rules'=>'trim|is_natural_no_zero|required'),
            'post_id' => array('field'=>'post_id', 'label'=>'Post ID', 'rules'=>'trim|is_natural_no_zero|required'),
            'language_slug' => array('field'=>'language_slug','label'=>'language_slug','rules'=>'trim|required')
        )*/
    );
}
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Language_model extends MY_Model
{
    public $table = 'languages';
    public $primary_key = 'id';
    public function __construct()
    {
        $this->timestamps = FALSE;
        $this->before_create[] = 'remove_defaults';
        $this->before_update[] = 'remove_defaults';
        parent::__construct();
    }

    public $rules = array(
        'insert' => array(
            'language_name' => array('field'=>'language_name', 'label'=>'Language name', 'rules'=>'trim|required|is_unique[languages.language_name]'),
            'slug' => array('field'=>'slug', 'label'=>'Slug', 'rules'=>'trim|alpha_dash|required|is_unique[languages.slug]'),
            'language_directory' => array('field'=>'language_directory','label'=>'Language directory','rules'=>'trim|required'),
            'language_code' => array('field'=>'language_code','label'=>'Language code','rules'=>'trim|alpha_dash|required|is_unique[languages.language_code]'),
            'default' => array('field'=>'default','label'=>'Default','rules'=>'trim|in_list[0,1]')
        ),
        'update' => array(
            'language_name' => array('field'=>'language_name', 'label'=>'Language name','rules'=>'trim|required'),
            'slug' => array('field'=>'slug','label'=>'Slug','rules'=>'trim|alpha_dash|required'),
            'language_directory' => array('field'=>'language_directory','label'=>'Language directory','rules'=>'trim|required'),
            'language_code' => array('field'=>'language_code','label'=>'Language code','rules'=>'trim|alpha_dash|required'),
            'default' => array('field'=>'default','label'=>'Default','rules'=>'trim|in_list[0,1]'),
            'language_id' => array('field'=>'language_id','label'=>'Language ID','rules'=>'trim|integer')
        )
    );

    public function remove_defaults($data)
    {
        if($data['default']=='1')
        {
            $this->db->where('default', '1');
            $this->db->update('languages', array('default'=>'0'));
        }
        return $data;
    }
}
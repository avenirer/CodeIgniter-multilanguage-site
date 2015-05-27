<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Website_model extends MY_Model
{
    public $table = 'website';
    public $timestamps = FALSE;
    public function __construct()
    {

        parent::__construct();
    }

    public $rules = array(
        'update' => array(
            'title' => array('field'=>'title','label'=>'Title','rules'=>'trim|required'),
            'page_title' => array('field'=>'page_title','label'=>'Page title','rules'=>'trim'),
            'admin_email' => array('field'=>'admin_email','label'=>'Admin email','rules'=>'trim|valid_email|required'),
            'contact_email' => array('field'=>'contact_email', 'label'=>'Contact email', 'rules'=>'trim|valid_email')
        )
    );
}
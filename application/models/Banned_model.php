<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Banned_model extends MY_Model
{
    public $table = 'banned';
    public $primary_key = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public $rules = array(
        'insert' => array(
            'ip' => array('field'=>'ip','label'=>'IP','rules'=>'trim|required|is_unique[banned.ip]')
        )
    );
}
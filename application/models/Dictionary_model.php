<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dictionary_model extends MY_Model
{
    public $table = 'dictionary';
    public $primary_key = 'id';
    public $timestamps = FALSE;

    public function __construct()
    {
        $this->pagination_delimiters = array('<li>','</li>');
        $this->pagination_arrows = array('<span aria-hidden="true">&laquo;</span>','<span aria-hidden="true">&raquo;</span>');
        parent::__construct();
    }

    public $rules = array(
        'insert' => array(
            'word' => array('field'=>'word','label'=>'Word','rules'=>'trim|required'),
            'root_word' => array('field'=>'root_word','label'=>'Root of word','rules'=>'trim'),
            'noise_word' => array('field'=>'noise_word','label'=>'Noise word','rules'=>'trim'),
            'language_slug' => array('field'=>'language_slug','label'=>'language_slug','rules'=>'trim|required')
        ),
        'update' => array(
            'word' => array('field'=>'word','label'=>'Word','rules'=>'trim|required'),
            'root_word' => array('field'=>'root_word','label'=>'Root of word','rules'=>'trim'),
            'noise_word' => array('field'=>'noise_word','label'=>'Noise word','rules'=>'trim'),
            'word_id' => array('field'=>'word_id', 'label'=>'Word ID', 'rules'=>'trim|is_natural_no_zero|required'),
            'language_slug' => array('field'=>'language_slug','label'=>'language_slug','rules'=>'trim|required')
        )
    );
}
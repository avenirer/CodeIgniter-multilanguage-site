<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Offline extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo 'The site is offline';
    }
}
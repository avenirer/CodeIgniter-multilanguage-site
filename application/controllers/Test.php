<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends Public_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo 'testpage';
    }
}
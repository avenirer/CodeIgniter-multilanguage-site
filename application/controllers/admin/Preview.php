<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Preview extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
        parent::__construct();
        if(!$this->ion_auth->in_group('admin'))
        {
            $this->session->set_flashdata('message','You are not allowed to visit the RAKE page');
            redirect('admin','refresh');
        }
        $this->load->model('content_model');
        $this->load->model('content_translation_model');
        $this->load->model('keyword_model');
        $this->load->model('keyphrase_model');
        $this->load->library('form_validation');
        $this->load->helper('text');
		
	}
}
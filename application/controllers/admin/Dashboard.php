<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->render('admin/dashboard_view');

    }

    public function clear_cache()
    {
        $leave_files = array('.htaccess', 'index.html');
        $i = 0;
        foreach( glob(APPPATH.'cache/*') as $file ) {
            if(!in_array(basename($file), $leave_files))
            {
                unlink($file);
                $i++;
            }
        }
        $this->session->set_flashdata('message', $i.' files were deleted from the cache directory.');
        redirect('admin','refresh');
    }
}
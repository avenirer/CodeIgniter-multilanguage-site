<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends Admin_Controller
{

    function __construct()
    {
        parent::__construct();
        if(!$this->ion_auth->in_group('admin'))
        {
            $this->session->set_flashdata('message','You are not allowed to visit the MASTER page');
            redirect('admin','refresh');
        }
        $this->load->model('website_model');
        $this->load->library('form_validation');
        $this->load->helper('form');
    }
    public function index()
    {
        $writable_directories = array();
        $check_writable = array(
            'application'=> array('cache','logs'),
            'public'=> array('uploads','media'));
        foreach($check_writable as $area => $directories)
        {
            if($area == 'application')
            {
                $writable_directories['application'] = array();
                foreach($directories as $directory)
                {
                    $writable_directories['application'][$directory] = is_really_writable(APPPATH.$directory) ? '1' : '0';
                }
            }
            if($area == 'public')
            {
                $writable_directories['public'] = array();
                foreach($directories as $directory)
                {
                    $writable_directories['public'][$directory] = is_really_writable(FCPATH.$directory) ? '1' : '0';
                }

            }
        }
        $this->load->model('banned_model');
        $this->data['banned_ips'] = $this->banned_model->get_all();
        $rules = $this->website_model->rules;
        $this->form_validation->set_rules($rules['update']);
        if($this->form_validation->run()===FALSE)
        {
            $this->data['website'] = $this->website;
            $this->data['writable_directories'] = $writable_directories;
            $this->render('admin/master/index_view');
        }
        else
        {
            $update_data = array();
            $update_data['title'] = $this->input->post('title');
            $update_data['page_title'] = (strlen($this->input->post('page_title')) > 0) ? $this->input->post('page_title') : $update_data['title'];
            $update_data['admin_email'] = $this->input->post('admin_email');
            $update_data['contact_email'] = (strlen($this->input->post('contact_email')) > 0) ? $this->input->post('contact_email') : $update_data['admin_email'];

            if($this->website_model->update($update_data))
            {
                $message = 'The website\'s data has been saved. Good luck!';
            }
            else $message = 'There was a problem... Are you sure you\'ve changed anything?';
            $this->session->set_flashdata('message',$message);
            redirect('admin/master','refresh');
        }
    }

    public function change_website_status()
    {
        $this->load->model('website_model');
        $new_status = ($this->website->status == '1') ? '0' : '1';
        if($this->website_model->update(array('status'=>$new_status,'modified_by'=>$this->user_id)))
        {
            $message = 'The website is ' . (($new_status == '1') ? 'ONLINE' : 'OFFLINE');
        }
        else
        {
            $message = 'Couldn\'t change the status of the site';
        }
        $this->session->set_flashdata('message',$message);
        redirect('admin/master','refresh');
    }

    public function add_ip()
    {
        $this->load->model('banned_model');
        $rules = $this->banned_model->rules;
        $this->form_validation->set_rules($rules['insert']);
        if($this->form_validation->run()===FALSE)
        {
            $this->session->set_flashdata('message','Couldn\' insert banned IP');
            redirect('admin/master','refresh');
        }
        else
        {
            $ip = $this->input->post('ip');
            if($this->banned_model->insert(array('ip'=>$ip,'created_by'=>$this->user_id)))
            {
                $this->session->set_flashdata('message','IP inserted successfully');
                redirect('admin/master','refresh');
            }
        }

    }

    public function remove_ip($ip)
    {
        $this->session->set_flashdata('message','Couldn\' remove banned IP');
        $this->load->model('banned_model');
        if($this->banned_model->delete($ip))
        {
            $this->session->set_flashdata('message','Banned IP was deleted');
        }
        redirect('admin/master','refresh');
    }
}
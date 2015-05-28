<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->library('postal');
    }

    public function index()
    {
    }

    public function login()
    {
        if($this->ion_auth->logged_in())
        {
            redirect('admin');
        }
        $redirect_to = $this->session->flashdata('redirect_to');
        if(!isset($redirect_to) && isset($_SERVER['HTTP_REFERER']))
        {
            $redirect_to = $_SERVER['HTTP_REFERER'];
            if(strpos($redirect_to, site_url(), 0)=== FALSE) $redirect_to = site_url();
        }
        elseif(!isset($redirect_to))
        {
            $redirect_to = site_url('admin');
        }
        $this->data['redirect_to'] = $redirect_to;
        $this->data['page_title'] = 'Login';
        $this->load->library('form_validation');
        $this->form_validation->set_rules('identity', 'Identity', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('remember','Remember me','integer');
        $this->form_validation->set_rules('redirect_to','Redirect to','valid_url');
        if($this->form_validation->run()===TRUE)
        {
            $remember = (bool) $this->input->post('remember');
            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
            {
                //echo 'bau';
                //echo '<br />'.$redirect_to;
                //exit;
                //$redirect_to = $this->input->post('redirect_to');
                redirect('admin');
            }
            else
            {
                $this->session->set_flashdata('redirect_to',$this->input->post('redirect_to'));
                $this->postal->add($this->ion_auth->errors(),'error');
                redirect('admin/user/login');
            }
        }
        $this->load->helper('form');
        $this->render('admin/login_view','admin_master');
    }

    public function profile()
    {
        $this->data['page_title'] = 'User Profile';
        $user = $this->ion_auth->user()->row();
        $this->data['user'] = $user;
        $this->data['current_user_menu'] = '';
        if($this->ion_auth->in_group('admin'))
        {
            $this->data['current_user_menu'] = $this->load->view('templates/_parts/user_menu_admin_view.php', NULL, TRUE);
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('first_name','First name','trim');
        $this->form_validation->set_rules('last_name','Last name','trim');
        $this->form_validation->set_rules('company','Company','trim');
        $this->form_validation->set_rules('phone','Phone','trim');

        if($this->form_validation->run()===FALSE)
        {
            $this->render('admin/user/profile_view','admin_master');
        }
        else
        {
            $new_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name'  => $this->input->post('last_name'),
                'company'    => $this->input->post('company'),
                'phone'      => $this->input->post('phone')
            );
            if(strlen($this->input->post('password'))>=6) $new_data['password'] = $this->input->post('password');
            $this->ion_auth->update($user->id, $new_data);
            $this->postal->add($this->ion_auth->messages(),'error');
            redirect('admin/user/profile');

        }
    }

    public function logout()
    {
        $this->ion_auth->logout();
        $this->postal->add($this->ion_auth->messages(),'error');
        redirect('admin/user/login');
    }
}
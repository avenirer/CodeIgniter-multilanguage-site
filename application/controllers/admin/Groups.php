<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groups extends Admin_Controller
{

    function __construct()
    {
        parent::__construct();
        if(!$this->ion_auth->in_group('admin'))
        {
            $this->session->set_flashdata('message','You are not allowed to visit the Groups page');
            redirect('admin','refresh');
        }
    }

    public function index()
    {
        $this->data['page_title'] = 'Groups';
        $this->data['groups'] = $this->ion_auth->groups()->result();
        $this->render('admin/groups/index_view');
	}

    public function create()
    {
        $this->data['page_title'] = 'Create group';
        $this->load->library('form_validation');
        $this->form_validation->set_rules('group_name','Group name','trim|required|is_unique[groups.name]');
        $this->form_validation->set_rules('group_description','Group description','trim|required');
        if($this->form_validation->run()===FALSE)
        {
            $this->load->helper('form');
            $this->render('admin/groups/create_view');
        }
        else
        {
            $group_name = $this->input->post('group_name');
            $group_description = $this->input->post('group_description');
            $this->ion_auth->create_group($group_name, $group_description);
            $this->session->set_flashdata('message',$this->ion_auth->messages());
            redirect('admin/groups','refresh');
        }
    }

    public function edit($group_id = NULL)
    {
        $group_id = $this->input->post('group_id') ? $this->input->post('group_id') : $group_id;
        $this->data['page_title'] = 'Edit group';
        $this->load->library('form_validation');

        $this->form_validation->set_rules('group_name','Group name','trim|required');
        $this->form_validation->set_rules('group_description','Group description','trim|required');
        $this->form_validation->set_rules('group_id','Group id','trim|integer|required');

        if($this->form_validation->run() === FALSE)
        {
            if($group = $this->ion_auth->group((int) $group_id)->row())
            {
                $this->data['group'] = $group;
            }
            else
            {
                $this->session->set_flashdata('message', 'The group doesn\'t exist.');
                redirect('admin/groups', 'refresh');
            }
            $this->load->helper('form');
            $this->render('admin/groups/edit_view');
        }
        else
        {
            $group_name = $this->input->post('group_name');
            $group_description = $this->input->post('group_description');
            $group_id = $this->input->post('group_id');
            $this->ion_auth->update_group($group_id, $group_name, $group_description);
            $this->session->set_flashdata('message',$this->ion_auth->messages());
            redirect('admin/groups','refresh');
        }
    }

    public function delete($group_id = NULL)
    {
        if(is_null($group_id))
        {
            $this->session->set_flashdata('message','There\'s no group to delete');
        }
        else
        {
            $this->ion_auth->delete_group($group_id);
            $this->session->set_flashdata('message',$this->ion_auth->messages());
        }
        redirect('admin/groups','refresh');
    }
}
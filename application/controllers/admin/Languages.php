<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Languages extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('language_model');
	}

	public function index()
	{
		$this->data['languages'] = $this->language_model->order_by('language_name')->get_all();
		$this->render('admin/languages/index_view');
	}

	public function create()
	{
        $rules = $this->language_model->rules;
        $this->form_validation->set_rules($rules['insert']);
		if($this->form_validation->run()===FALSE)
		{
			$this->render('admin/languages/create_view');
		}
		else
		{
			$new_language = array(
				'language_name' => $this->input->post('language_name'),
				'slug' => $this->input->post('slug'),
				'language_directory' => $this->input->post('language_directory'),
				'language_code' => $this->input->post('language_code'),
				'default' => $this->input->post('default')
			);
			$this->session->set_flashdata('message', 'Language added successfully');
			if (!$this->language_model->insert($new_language))
			{
				$this->session->set_flashdata('message', 'There was an error inserting the new language');
			}
			redirect('admin/languages', 'refresh');
		}
	}

	public function update($language_id = NULL)
	{
		$rules = $this->language_model->rules;
        $this->form_validation->set_rules($rules['update']);

		$language_id = isset($language_id) ? (int) $language_id : (int) $this->input->post('language_id');

		if($this->form_validation->run()===FALSE)
		{
			if($this->data['language'] = $this->language_model->get($language_id))
			{
				$this->render('admin/languages/edit_view');
			}
			else
			{
				$this->session->set_flashdata('message', 'The ID for the language doesn\'t exist');
				redirect('admin/languages', 'refresh');
			}
		}
		else
		{
			$new_data = array(
				'language_name' => $this->input->post('language_name'),
				'slug' => $this->input->post('slug'),
				'language_directory' => $this->input->post('language_directory'),
				'language_code' => $this->input->post('language_code'),
				'default' => $this->input->post('default')
			);
			$this->session->set_flashdata('message', 'Language updated successfully');
			if (!$this->language_model->update($new_data, $language_id))
			{
				$this->session->set_flashdata('message', 'There was an error in updating the language');
			}
			redirect('admin/languages', 'refresh');
		}
	}

	public function delete($language_id)
	{
		if(($language = $this->language_model->get($language_id)) && $language->default == '1')
		{
			$this->session->set_flashdata('message','I can\'t delete a default language. First set another default language.');
		}
		elseif($this->language_model->delete($language_id) === FALSE)
		{
			$this->session->set_flashdata('message', 'There was an error in deleting the language');
		}
		else
		{
			$this->session->set_flashdata('message', 'Language deleted successfully');
		}
		redirect('admin/languages','refresh');
	}
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Languages extends Admin_Controller
{

	function __construct()
	{
        parent::__construct();
        if(!$this->ion_auth->in_group('admin'))
        {
            $this->postal->add('You are not allowed to visit the Languages page','error');
            redirect('admin');
        }
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

			if (!$this->language_model->insert($new_language))
			{
                $this->postal->add('There was an error inserting the new language','error');
			}
            else
            {
                $this->postal->add('Language added successfully','success');
            }
			redirect('admin/languages');
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
                $this->postal->add('The ID for the language doesn\'t exist','error');
				redirect('admin/languages');
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

			if (!$this->language_model->update($new_data, $language_id))
			{
                $this->postal->add('There was an error in updating the language','error');
			}
            else
            {
                $this->postal->add('Language updated successfully','success');
            }
			redirect('admin/languages');
		}
	}

	public function delete($language_id)
	{
		if(($language = $this->language_model->get($language_id)) && $language->default == '1')
		{
            $this->postal->add('I can\'t delete a default language. First set another default language.','error');
		}
		elseif($this->language_model->delete($language_id) === FALSE)
		{
            $this->postal->add('There was an error in deleting the language','error');
		}
		else
		{
            $this->postal->add('Language deleted successfully','success');
		}
		redirect('admin/languages');
	}
}
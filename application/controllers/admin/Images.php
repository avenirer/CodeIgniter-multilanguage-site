<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Images extends Admin_Controller
{
    private $featured_image;

	function __construct()
	{
		parent::__construct();
        $this->load->model('image_model');
        $this->load->model('content_model');
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->helper('inflector');
        $this->featured_image = $this->config->item('cms_featured_image');
	}

	public function index($content_id = NULL)
	{
        if(!isset($content_id))
        {
            $this->session->set_flashdata('message', 'You are not allowed to access <strong>images/index</strong> page unless you come from a specific content type and ID.');
            redirect('admin','refresh');
        }
        $this->data['show_images'] = $this->image_model->where(array('content_id'=>$content_id))->get_all();
        $this->data['content_id'] = $content_id;
        $this->data['upload_errors'] = '';
        $rules = $this->image_model->rules;
        $this->form_validation->set_rules($rules['insert']);
        if($this->form_validation->run()===FALSE)
        {
            $this->render('admin/images/index_view');
        }
        else
        {
            $content_id = $this->input->post('content_id');
            $title = $this->input->post('titles');
            $file_name = url_title($this->input->post('file_names'), '-', TRUE);
            $config = array(
                'upload_path' => './media/',
                'file_name' => $file_name,
                'allowed_types' => 'jpg|gif|png',
                'max_size' => '2048',
                'multi' => 'all'
            );
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('images'))
            {
                $this->data['upload_errors'] = $this->upload->display_errors();
            }
            else
            {
                $images_data = $this->upload->data();
                $insert_images = array();
                foreach($images_data as $image)
                {
                    if(strlen($title)==0) $title = $image['file_name'];
                    $insert_images[] = array(
                        'file'=>$image['file_name'],
                        'width'=>$image['image_width'],
                        'height'=>$image['image_height'],
                        'title'=>$title,
                        'content_id'=>$content_id,
                        'created_by'=>$this->user_id);
                }
                if($inserted_images = $this->image_model->insert($insert_images))
                {
                    $this->session->set_flashdata('message', sizeof($inserted_images).' image(s) were uploaded.');
                }
                else
                {
                    $this->session->set_flashdata('message', 'Oops... there was a problem... Don\'t ask me what...');
                }
                redirect('admin/images/index/'.$content_id,'refresh');
            }
            $this->render('admin/images/index_view');
        }
	}

    public function edit_title($image_id)
    {
        $image = $this->image_model->get($image_id);
        $this->data['image'] = $image;
        $rules = $this->image_model->rules;
        $this->form_validation->set_rules($rules['update_title']);
        if($this->form_validation->run()===FALSE)
        {
            $this->render('admin/images/edit_title_view');
        }
        else
        {
            $title = $this->input->post('title');
            $image_id = $this->input->post('image_id');
            if($image = $this->image_model->get($image_id))
            {
                $this->session->set_flashdata('message', 'Couldn\'t modify the image title.');
                if($this->image_model->update(array('title'=>$title),$image->id))
                {
                    $this->session->set_flashdata('message', 'The image title was modified.');
                }
            }
            else
            {
                $this->session->set_flashdata('message', 'There\'s no image with that ID.');
            }
            redirect('admin/images/index/'.$image->content_type.'/'.$image->content_id,'refresh');
        }
    }

    public function featured($content_id)
    {
        $this->data['content_id'] = (int) $content_id;
        $this->data['upload_errors'] = '';
        $rules = $this->content_model->rules;
        $this->form_validation->set_rules($rules['insert_featured']);
        if($this->form_validation->run()===FALSE)
        {
            $this->render('admin/images/upload_featured_view');
        }
        else
        {
            $config = array(
                'upload_path' => './uploads/',
                'allowed_types' => 'jpg|gif|png',
                'max_size' => '2048',
                'multi' => 'all'
            );
            $this->load->library('upload',$config);
            if(!$this->upload->do_upload('featured_image'))
            {
                $this->data['upload_errors'] = $this->upload->display_errors();
                $this->render('admin/images/upload_featured_view');
            }
            else
            {
                $content_id = $this->input->post('content_id');
                $content = $this->content_model->get($content_id);
                $image_data = $this->upload->data();
                $this->load->library('image_nation');
                $this->image_nation->source($image_data['file_name']);
                $this->image_nation->clear_sizes();
                $dimensions = array(
                    $this->featured_image => array(
                        'master_dim'    =>  'width',
                        'keep_aspect_ratio' => FALSE,
                        'style'         =>  array('vertical'=>'center','horizontal'=>'center'),
                        'overwrite'     =>  FALSE,
                        'quality'       =>  '70%'
                    )
                );
                $file_name = url_title($this->input->post('file_name'),'-',TRUE);
                if(strlen($file_name)>0) $dimensions['400x350']['file_name'] = $file_name;
                $this->image_nation->add_size($dimensions);
                $this->image_nation->process();
                if(!$this->image_nation->get_errors())
                {
                    $processed_image = $this->image_nation->get_processed();
                    //print_r($processed_image);
                    if($this->content_model->update(array('featured_image'=>$processed_image[0][$this->featured_image]['file_name']),$content_id))
                    {
                        $this->session->set_flashdata('message', 'The featured image was successfully uploaded.');
                    }
                    redirect('admin/contents/index/'.$content->content_type,'refresh');
                }
                else
                {
                    print_r($this->image_nation->get_errors());
                }
            }
        }

    }

    public function delete_featured($content_id)
    {
        $content = $this->content_model->get($content_id);
        if($content===FALSE)
        {
            $this->session->set_flashdata('message', 'There is no content there.');
            redirect('admin','refresh');
        }
        else
        {
            $id = $content->id;
            $file_name = $content->featured_image;
            @unlink(FCPATH.'media/'.$this->featured_image.'/'.$file_name);
            if($this->content_model->update(array('featured_image'=>''),$id))
            {
                $this->session->set_flashdata('message', 'The featured image was removed.');
                redirect('admin/contents/index/'.$content->content_type,'refresh');
            }

        }
    }

    public function delete($image_id)
    {
        $image = $this->image_model->get($image_id);
        $file = $image->file;
        $content_id = $image->content_id;
        if($this->image_model->delete($image_id))
        {
            $this->session->set_flashdata('message', 'The image was removed from database but not as file.');
        }
        if(unlink(FCPATH.'media/'.$file))
        {
            $this->session->set_flashdata('message', 'The image was removed.');
        }
        redirect('admin/images/index/'.$content_id,'refresh');
    }
}
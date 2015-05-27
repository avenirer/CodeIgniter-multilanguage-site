<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Menus extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
        if(!$this->ion_auth->in_group('admin'))
        {
            $this->session->set_flashdata('message','You are not allowed to visit the Categories page');
            redirect('admin','refresh');
        }
        $this->load->model('menu_model');
        $this->load->model('menu_item_model');
        $this->load->model('menu_item_translation_model');
        $this->load->model('slug_model');
        $this->load->model('language_model');
        $this->load->library('form_validation');
        $this->load->helper('text');
	}

	public function index()
	{
        $this->data['menus'] = $this->menu_model->order_by('created_at, updated_at','desc')->get_all();

        $without_menu = array();
        if($items = $this->menu_item_model->where('menu_id','0')->with_translations()->get_all())
        {
            foreach ($items as $item)
            {
                $without_menu[$item->id] = array('created_at' => $item->created_at, 'last_update' => $item->updated_at, 'deleted' => $item->deleted_at, 'title'=>'');
                if(isset($item->translations))
                {
                    foreach ($item->translations as $translation)
                    {
                        if(!isset($without_menu[$item->id]['title']))
                        {
                            $without_menu[$item->id]['title'] = $translation->title;
                        }
                        if (($translation->language_slug == $this->default_lang) && (strlen($translation->title)>0))
                        {
                            $without_menu[$item->id]['title'] = $translation->title;
                        }
                    }
                }
            }
        }
        $this->data['without_menu'] = $without_menu;
		$this->render('admin/menus/index_view');
	}

    public function create()
    {
        $rules = $this->menu_model->rules;
        $this->form_validation->set_rules($rules['insert']);
        if($this->form_validation->run()===FALSE)
        {
            $this->render('admin/menus/create_menu_view');
        }
        else
        {
            $title = url_title(convert_accented_characters($this->input->post('title')),'-',TRUE);
            if ($this->menu_model->insert(array('title'=>$title, 'created_by'=>$this->user_id)))
            {
                $this->session->set_flashdata('message', 'The new menu was created.');
            }
            redirect('admin/menus','refresh');
        }
    }

    public function edit($menu_id)
    {
        $menu = $this->menu_model->get($menu_id);
        if($menu == FALSE)
        {
            $this->session->set_flashdata('message', 'There is no menu to edit.');
            redirect('admin/menus', 'refresh');
        }
        $this->data['menu'] = $menu;

        $rules = $this->menu_model->rules;
        $this->form_validation->set_rules($rules['update']);
        if($this->form_validation->run()===FALSE)
        {
            $this->render('admin/menus/edit_menu_view');
        }
        else
        {
            $title = $this->input->post('title');
            $menu_id = $this->input->post('menu_id');
            $update_data = array('title' => $title,'updated_by' => $this->user_id);
            $this->session->set_flashdata('message', 'Couldn\'t edit menu.');
            if ($this->menu_model->update($update_data, $menu_id))
            {
                $this->session->set_flashdata('message', 'The menu was updated successfully.');
            }
            redirect('admin/menus','refresh');
        }
    }

    public function delete($menu_id)
    {
        if(!$this->menu_model->delete($menu_id))
        {
            $this->session->set_flashdata('message', 'The menu doesn\'t exist.');
            redirect('admin/menus','refresh');
        }
        if($menu_items = $this->menu_item_model->update(array('menu_id'=>'0','updated_by'=>$this->user_id),array('menu_id'=>$menu_id)))
        {
            $this->session->set_flashdata('message','The menu was deleted. Now you have '.$menu_items.' menu item without a menu location.');
        }
        redirect('admin/menus','refresh');
    }

    public function items($menu_id = NULL)
    {
        if(!isset($menu_id) || $menu_id == 0)
        {
            redirect('admin/menus','refresh');
        }
        $this->data['menu'] = $this->menu_model->get($menu_id);
        $list_items = array();

        if($items = $this->menu_item_model->order_by('order','asc')->with('translations')->get_all())
        {
            foreach ($items as $item)
            {
                $list_items[$item->id] = array('menu_id'=>$item->menu_id, 'parent_id'=>$item->parent_id, 'created_at' => $item->created_at, 'last_update' => $item->updated_at, 'deleted' => $item->deleted_at, 'translations' => array(), 'title'=>'');
                if(isset($item->translations))
                {
                    foreach ($item->translations as $translation)
                    {
                        $list_items[$item->id]['translations'][$translation->language_slug] = array('translation_id' => $translation->id, 'title' => $translation->title, 'created_at' => $translation->created_at, 'last_update' => $translation->updated_at, 'deleted' => $translation->deleted_at, 'url'=>$translation->url);
                        if(!isset($list_items[$item->id]['title']))
                        {
                            $list_items[$item->id]['title'] = $translation->title;
                        }
                        if (($translation->language_slug == $this->default_lang) && (strlen($translation->title)>0))
                        {
                            $list_items[$item->id]['title'] = $translation->title;
                        }
                    }
                }
            }
        }
        $this->data['items'] = $list_items;
        $this->data['next_previous_pages'] = $this->menu_item_model->all_pages;
        $this->render('admin/menus/index_items_view');
    }

    public function create_item($menu_id, $language_slug = NULL, $item_id = 0)
    {
        $this->data['menu_id'] = $menu_id;
        $language_slug = (isset($language_slug) && array_key_exists($language_slug, $this->langs)) ? $language_slug : $this->current_lang;

        $this->data['content_language'] = $this->langs[$language_slug]['name'];
        $this->data['language_slug'] = $language_slug;
        $item = $this->menu_item_model->get($item_id);
        if($item_id != 0 && $item==FALSE)
        {
            $item_id = 0;
        }
        if($this->menu_item_translation_model->where(array('item_id'=>$item_id,'language_slug'=>$language_slug))->get())
        {
            $this->session->set_flashdata('message', 'A translation for that menu item already exists.');
            redirect('admin/menus/items/'.$menu_id, 'refresh');
        }
        $this->data['item'] = $item;
        $this->data['item_id'] = $item_id;
        $items = $this->menu_item_translation_model->where('language_slug',$language_slug)->order_by('title')->fields('item_id,id,title')->get_all();
        $this->data['parent_items'] = array('0'=>'Top level');
        if(!empty($items))
        {
            foreach($items as $item)
            {
                $this->data['parent_items'][$item->item_id] = $item->title;
            }
        }

        $rules = $this->menu_item_model->rules;
        $this->form_validation->set_rules($rules['insert']);
        if($this->form_validation->run()===FALSE)
        {
            $this->render('admin/menus/create_item_view');
        }
        else
        {
            $parent_id = $this->input->post('parent_id');
            $title = $this->input->post('title');
            $url = $this->input->post('url');
            $absolute_path = $this->input->post('absolute_path');
            $order = $this->input->post('order');
            $styling = $this->input->post('styling');
            $item_id = $this->input->post('item_id');
            $menu_id = $this->input->post('menu_id');
            $language_slug = $this->input->post('language_slug');
            $this->session->set_flashdata('message', 'Couldn\'t add item.');
            if ($item_id == 0)
            {
                $item_id = $this->menu_item_model->insert(array('menu_id' => $menu_id, 'parent_id' => $parent_id, 'order' => $order, 'styling'=>$styling, 'created_by'=>$this->user_id));
                $this->session->set_flashdata('message', 'Item successfuly added, but didn\'t add translation...');
            }

            $insert_data = array('item_id' => $item_id, 'title' => $title, 'url' => $url, 'absolute_path' => $absolute_path, 'language_slug' => $language_slug, 'created_by'=>$this->user_id);

            if($translation_id = $this->menu_item_translation_model->insert($insert_data))
            {
                $this->session->set_flashdata('message', 'Item successfuly added.');
                $this->menu_item_model->update(array('parent_id'=>$parent_id, 'order'=>$order, 'styling'=>$styling, 'updated_by'=>$this->user_id),$item_id);
            }

            redirect('admin/menus/items/'.$menu_id,'refresh');

        }
    }

    public function edit_item($menu_id, $language_slug = NULL, $item_id = 0)
    {
        if($item_id==0)
        {
            redirect('admin/menus/items/'.$menu_id,'refresh');
        }

        $this->data['menu_id'] = $menu_id;
        $language_slug = (isset($language_slug) && array_key_exists($language_slug, $this->langs)) ? $language_slug : $this->current_lang;

        $this->data['content_language'] = $this->langs[$language_slug]['name'];
        $this->data['language_slug'] = $language_slug;
        $item = $this->menu_item_model->get($item_id);
        $translation = $this->menu_item_translation_model->where(array('item_id'=>$item_id,'language_slug'=>$language_slug))->get();
        if($translation===FALSE)
        {
            $this->session->set_flashdata('message', 'There is no translation for that menu item.');
            redirect('admin/menus/items/'.$menu_id, 'refresh');
        }
        $this->data['item'] = $item;
        $this->data['translation'] = $translation;
        $this->data['item_id'] = $item_id;
        $items = $this->menu_item_translation_model->where('language_slug',$language_slug)->where('item_id','!=',$item_id)->order_by('title')->fields('item_id,id,title')->get_all();
        $this->data['parent_items'] = array('0'=>'Top level');
        if(!empty($items))
        {
            foreach($items as $item)
            {
                $this->data['parent_items'][$item->item_id] = $item->title;
            }
        }
        $rules = $this->menu_item_model->rules;
        $this->form_validation->set_rules($rules['update']);
        if($this->form_validation->run()===FALSE)
        {
            $this->render('admin/menus/edit_item_view');
        }
        else
        {
            $parent_id = $this->input->post('parent_id');
            $title = $this->input->post('title');
            $url = $this->input->post('url');
            $absolute_path = $this->input->post('absolute_path');
            $order = $this->input->post('order');
            $styling = $this->input->post('styling');
            $translation_id = $this->input->post('translation_id');
            $item_id = $this->input->post('item_id');
            $menu_id = $this->input->post('menu_id');
            $this->session->set_flashdata('message', 'Couldn\'t edit item.');

            $update_data = array('title' => $title, 'url' => $url, 'absolute_path' => $absolute_path, 'updated_by'=>$this->user_id);

            if($this->menu_item_translation_model->update($update_data,$translation_id))
            {
                $this->session->set_flashdata('message', 'Item successfuly edited.');
                $this->menu_item_model->update(array('parent_id'=>$parent_id, 'order'=>$order,'styling'=>$styling,'updated_by'=>$this->user_id),$item_id);
            }
            redirect('admin/menus/items/'.$menu_id,'refresh');

        }
    }

    public function delete_item($menu_id, $language_slug, $item_id)
    {
        if($item = $this->menu_item_model->get($item_id))
        {
            if($language_slug=='all')
            {
                if($deleted_translations = $this->menu_item_translation_model->where('item_id',$item_id)->delete())
                {
                    $deleted_item = $this->menu_item_model->delete($item_id);
                    $this->session->set_flashdata('message', 'The item was deleted. There were also '.$deleted_translations.' translations deleted.');
                }
                else
                {
                    $deleted_item = $this->menu_item_model->delete($item_id);
                    $this->session->set_flashdata('message', 'The item was deleted');
                }
            }
            else
            {
                if($this->menu_item_translation_model->where(array('item_id'=>$item_id,'language_slug'=>$language_slug))->delete())
                {
                    $this->session->set_flashdata('message', 'The translation was deleted.');
                }
            }
        }
        else
        {
            $this->session->set_flashdata('message', 'There is no translation to delete.');
        }
        redirect('admin/menus/items/'.$menu_id,'refresh');
    }
}
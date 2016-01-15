<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Menus extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
        if(!$this->ion_auth->in_group('admin'))
        {
            $this->postal->add('You are not allowed to visit the Categories page','error');
            redirect('admin');
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
                $this->postal->add('The new menu was created.','success');
            }
            redirect('admin/menus');
        }
    }

    public function edit($menu_id)
    {
        $menu = $this->menu_model->get($menu_id);
        if($menu == FALSE)
        {
            $this->postal->add('There is no menu to edit.','error');
            redirect('admin/menus');
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
            $this->postal->add('Couldn\'t edit menu.','error');
            if ($this->menu_model->update($update_data, $menu_id))
            {
                $this->postal->add('The menu was updated successfully.','success');
            }
            redirect('admin/menus');
        }
    }

    public function delete($menu_id)
    {
        if(!$this->menu_model->delete($menu_id))
        {
            $this->postal->add('The menu doesn\'t exist.','error');
            redirect('admin/menus');
        }
        if($menu_items = $this->menu_item_model->update(array('menu_id'=>'0','updated_by'=>$this->user_id),array('menu_id'=>$menu_id)))
        {
            $this->postal->add('The menu was deleted. Now you have '.$menu_items.' menu item without a menu location.','success');
        }
        redirect('admin/menus');
    }

    public function items($menu_id = NULL)
    {
        if(!isset($menu_id) || $menu_id == 0)
        {
            redirect('admin/menus');
        }
        $this->data['menu'] = $this->menu_model->get($menu_id);
        $list_items = array();

        if($items = $this->menu_item_model->order_by('order','asc')->where('menu_id',$menu_id)->with('translations')->get_all())
        {
            foreach ($items as $item)
            {
                $list_items[$item->id] = array('menu_id'=>$item->menu_id, 'parent_id'=>$item->parent_id, 'created_at' => $item->created_at, 'last_update' => $item->updated_at, 'deleted' => $item->deleted_at, 'translations' => array(), 'title'=>'');
                if(isset($item->translations))
                {
                    foreach ($item->translations as $translation)
                    {
                        $list_items[$item->id]['translations'][$translation->language_slug] = array('translation_id' => $translation->id, 'title' => $translation->title, 'created_at' => $translation->created_at, 'last_update' => $translation->updated_at, 'deleted' => $translation->deleted_at, 'url'=>$translation->url);
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
            $this->postal->add('A translation for that menu item already exists.','error');
            redirect('admin/menus/items/'.$menu_id);
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
            if ($item_id == 0)
            {
                $item_id = $this->menu_item_model->insert(array('menu_id' => $menu_id, 'parent_id' => $parent_id, 'order' => $order, 'styling'=>$styling, 'created_by'=>$this->user_id));
                if($item_id!==FALSE)
                {
                    $this->postal->add('Item successfuly added', 'success');
                }
                else
                {
                    $this->postal->add('Couldn\'t add item.','error');
                }
            }

            $insert_data = array('item_id' => $item_id, 'title' => $title, 'url' => $url, 'absolute_path' => $absolute_path, 'language_slug' => $language_slug, 'created_by'=>$this->user_id);

            if($translation_id = $this->menu_item_translation_model->insert($insert_data))
            {
                $this->postal->add('Translation successfully added.','success');
                $this->menu_item_model->update(array('parent_id'=>$parent_id, 'order'=>$order, 'styling'=>$styling, 'updated_by'=>$this->user_id),$item_id);
            }
            else
            {
                $this->postal->add('Couldn\'t add translation.','error');
            }

            redirect('admin/menus/items/'.$menu_id);

        }
    }

    public function edit_item($menu_id, $language_slug = NULL, $item_id = 0)
    {
        if($item_id==0)
        {
            redirect('admin/menus/items/'.$menu_id);
        }

        $this->data['menu_id'] = $menu_id;
        $language_slug = (isset($language_slug) && array_key_exists($language_slug, $this->langs)) ? $language_slug : $this->current_lang;

        $this->data['content_language'] = $this->langs[$language_slug]['name'];
        $this->data['language_slug'] = $language_slug;
        $item = $this->menu_item_model->get($item_id);
        $translation = $this->menu_item_translation_model->where(array('item_id'=>$item_id,'language_slug'=>$language_slug))->get();
        if($translation===FALSE)
        {
            $this->postal->add('There is no translation for that menu item.','error');
            redirect('admin/menus/items/'.$menu_id);
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

            $update_data = array('title' => $title, 'url' => $url, 'absolute_path' => $absolute_path, 'updated_by'=>$this->user_id);

            if($this->menu_item_translation_model->update($update_data,$translation_id))
            {
                $this->postal->add('Item successfuly edited.','success');
                $this->menu_item_model->update(array('parent_id'=>$parent_id, 'order'=>$order,'styling'=>$styling,'updated_by'=>$this->user_id),$item_id);
            }
            else
            {
                $this->postal->add('Couldn\'t edit item.','error');
            }
            redirect('admin/menus/items/'.$menu_id);

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
                    $this->postal->add('The item was deleted. There were also '.$deleted_translations.' translations deleted.','success');
                }
                else
                {
                    $deleted_item = $this->menu_item_model->delete($item_id);
                    $this->postal->add('The item was deleted','success');
                }
            }
            else
            {
                if($this->menu_item_translation_model->where(array('item_id'=>$item_id,'language_slug'=>$language_slug))->delete())
                {
                    $this->postal->add('The translation was deleted.','success');
                }
            }
        }
        else
        {
            $this->postal->add('There is no translation to delete.','error');
        }
        redirect('admin/menus/items/'.$menu_id);
    }
}
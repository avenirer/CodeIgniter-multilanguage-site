<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Content_model extends MY_Model
{
    private $featured_image;
    public $before_create = array('created_by');
    public $before_update = array('updated_by');
    public $table = 'contents';
    public function __construct()
    {
        $this->featured_image = $this->config->item('cms_featured_image');
        $this->has_many['translations'] = array('Content_translation_model','content_id','id');
        parent::__construct();
    }

    public function created_by($data)
    {
        $data['created_by'] = $this->user_id;
        return $data;
    }

    public function updated_by($data)
    {
        $data['updated_by'] = $this->user_id;
        return $data;
    }

    public function get_content_list($content_type = 'post', $language_slug = NULL)
    {
        $this->db->select('contents.id as content_id, contents.content_type, contents.parent_id, contents.featured_image, contents.order, contents.published, contents.published_at, content_translations.id as translation_id, content_translations.language_slug, content_translations.short_title as translation_title, content_translations.rake as translation_rake');
        $this->db->where('contents.content_type',$content_type);
        if(isset($language_slug))
        {
            $this->db->where('content_translations.language_slug',$language_slug);
        }
        $this->db->join('content_translations','contents.id = content_translations.content_id');
        $query = $this->db->get($this->table);
        if($query->num_rows()>0)
        {
            $list_content = array();
            foreach ($query->result() as $row)
            {
                if(!array_key_exists($row->content_id,$list_content))
                {
                    $featured_image = '';
                    if (strlen($row->featured_image) > 0) $featured_image = site_url('media/' . $this->featured_image . '/' . $row->featured_image);
                    $list_content[$row->content_id] = array(
                        'content_type' => $row->content_type,
                        'published' => $row->published,
                        'published_at' => $row->published_at,
                        //'created_at' => $row->created_at,
                        'featured_image' => $featured_image,
                        //'last_update' => $page->updated_at,
                        //'deleted' => $page->deleted_at,
                        'translations' => array(),
                        'title' => '');
                }
                $list_content[$row->content_id]['translations'][$row->language_slug] = array(
                            'translation_id' => $row->translation_id,
                            'title' => $row->translation_title,
                            'rake' => $row->translation_rake);
                            //'created_at' => $translation->created_at,
                            //'last_update' => $translation->updated_at,
                            //'deleted' => $translation->deleted_at);
                if ($row->language_slug == $_SESSION['default_lang'])
                {
                    $list_content[$row->content_id]['title'] = $row->translation_title;
                }
                elseif (strlen($list_content[$row->content_id]['title']) == 0)
                {
                    $list_content[$row->content_id]['title'] = $row->translation_title;
                }
            }
            return $list_content;
        }
        else
        {
            return FALSE;
        }
    }

    public function get_parents_list($content_type,$content_id,$language_slug)
    {
        $this->db->select('contents.id, content_translations.short_title');
        $this->db->order_by('short_title','asc');
        $this->db->join('content_translations','contents.id = content_translations.content_id','right');
        $this->db->where('contents.id != ',$content_id);
        if($content_type == 'post')
        {
            $this->db->where('contents.content_type','category');
        }
        else
        {
            $this->db->where('contents.content_type',$content_type);
        }
        $this->db->where('contents.id !=',$content_id);
        $this->db->where('content_translations.language_slug',$language_slug);
        $query = $this->db->get('contents');
        $parents = array('0'=>'No parent');
        if($query->num_rows()>0)
        {
            foreach($query->result() as $row)
            {
                $parents[$row->id] = $row->short_title;
            }
        }
        return $parents;
    }

    public $rules = array(
        'insert' => array(
            'parent_id' => array('field'=>'parent_id','label'=>'Parent ID','rules'=>'trim|is_natural|required'),
            'title' => array('field'=>'title','label'=>'Title','rules'=>'trim|required'),
            'short_title' => array('field'=>'short_title','label'=>'Short title','rules'=>'trim'),
            'slug' => array('field'=>'slug', 'label'=>'Slug', 'rules'=>'trim'),
            'order' => array('field'=>'order','label'=>'Order','rules'=>'trim|is_natural'),
            'teaser' => array('field'=>'teaser','label'=>'Teaser','rules'=>'trim'),
            'content' => array('field'=>'content','label'=>'Content','rules'=>'trim'),
            'page_title' => array('field'=>'page_title','label'=>'Page title','rules'=>'trim'),
            'page_description' => array('field'=>'page_description','label'=>'Page description','rules'=>'trim'),
            'page_keywords' => array('field'=>'page_keywords','label'=>'Page keywords','rules'=>'trim'),
            'content_id' => array('field'=>'content_id', 'label'=>'Content ID', 'rules'=>'trim|is_natural|required'),
            'content_type' => array('field'=>'content_type','label'=>'Content type','rules'=>'trim|required'),
            'published_at' => array('field'=>'published_at','label'=>'Published at','rules'=>'trim|datetime'),
            'language_slug' => array('field'=>'language_slug','label'=>'Language slug','rules'=>'trim|required')
        ),
        'update' => array(
            'parent_id' => array('field'=>'parent_id','label'=>'Parent ID','rules'=>'trim|is_natural|required'),
            'title' => array('field'=>'title','label'=>'Title','rules'=>'trim|required'),
            'short_title' => array('field'=>'short_title','label'=>'Short title','rules'=>'trim'),
            'slug' => array('field'=>'slug', 'label'=>'Slug', 'rules'=>'trim'),
            'order' => array('field'=>'order','label'=>'Order','rules'=>'trim|is_natural'),
            'teaser' => array('field'=>'teaser','label'=>'Teaser','rules'=>'trim'),
            'content' => array('field'=>'content','label'=>'Content','rules'=>'trim'),
            'page_title' => array('field'=>'page_title','label'=>'Page title','rules'=>'trim|required'),
            'page_description' => array('field'=>'page_description','label'=>'Page description','rules'=>'trim'),
            'page_keywords' => array('field'=>'page_keywords','label'=>'Page keywords','rules'=>'trim'),
            'translation_id' => array('field'=>'translation_id', 'label'=>'Translation ID', 'rules'=>'trim|is_natural_no_zero|required'),
            'content_id' => array('field'=>'content_id', 'label'=>'Content ID', 'rules'=>'trim|is_natural_no_zero|required'),
            'content_type' => array('field'=>'content_type','label'=>'Content type','rules'=>'trim|required'),
            'published_at' => array('field'=>'published_at','label'=>'Published at','rules'=>'trim|datetime'),
            'language_slug' => array('field'=>'language_slug','label'=>'language_slug','rules'=>'trim|required')
        ),
        'insert_featured' => array(
            'file_name' => array('field'=>'file_name','label'=>'File name','rules'=>'trim'),
            // there where two typos in here 'Contend ID' and 'tirm' 
            'content_id' => array('field'=>'content_id','label'=>'Content ID','rules'=>'trim|is_natural_no_zero|required')
        )
    );
}

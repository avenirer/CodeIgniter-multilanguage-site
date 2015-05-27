<?php
/**
 * Created with: PhpStorm.
 * User: adrian.voicu
 * Date: 12/12/2014
 * Time: 4:54 PM
 */

class Image_nation {

    private $_image_library;
    private $_source_directory;
    private $_parent_directory;
    private $_size_folders;
    private $_default_sizes;
    private $_keep_aspect_ratio;
    private $_default_master_dim;
    private $_default_style;
    private $_overwrite_images;
    private $_default_quality;
    private $_max_filename_increment = 100;
    private $_sizes = array();

    private $_errors = array();
    private $_images = array();
    private $_processed_images = array();

    public function __construct()
    {
        $this->load->config('image_nation', TRUE);
        $this->_image_library = $this->config->item('image_library', 'image_nation');
        $this->_source_directory = $this->config->item('source_directory', 'image_nation');
        $this->_parent_directory = $this->config->item('parent_directory', 'image_nation');
        $this->_size_folders = $this->config->item('size_folders', 'image_nation');
        $this->_default_sizes = $this->config->item('default_sizes', 'image_nation');
        $this->_keep_aspect_ratio = $this->config->item('keep_aspect_ratio', 'image_nation');
        $this->_default_master_dim = $this->config->item('default_master_dim', 'image_nation');
        $this->_default_style = $this->config->item('default_style', 'image_nation');
        $this->_overwrite_images = $this->config->item('overwrite_images', 'image_nation');
        $this->_default_quality = $this->config->item('default_quality', 'image_nation');
        if(strlen($this->_parent_directory)==0) $this->_parent_directory = $this->_source_directory;
        $this->_sizes = $this->_set_defaults();

    }

    /**
     * @return mixed sets the default sizes depending on th configuration; function required in the constructor
     */
    private function _set_defaults()
    {
        if(strlen($this->_default_sizes)>0)
        {
            $sizes = explode('|', $this->_default_sizes);
            foreach($sizes as $size)
            {
                $image_path = ($this->_size_folders) ? str_replace('\\','/',FCPATH.$this->_parent_directory).'/'.$size.'/' : str_replace('\\','/',FCPATH.$this->_parent_directory).'/';
                $width_height = explode('x',$size);
                $sizes_arr[$size] = array(
                    'width' => $width_height[0],
                    'height' => $width_height[1],
                    'master_dim' => $this->_default_master_dim,
                    'keep_aspect_ratio' => $this->_keep_aspect_ratio,
                    'style' => $this->_default_style,
                    'quality' => $this->_default_quality,
                    'directory' => $image_path,
                    'file_name' => FALSE,
                    'overwrite' => $this->_overwrite_images
                );
            }
            return $sizes_arr;
        }
    }

    /**
     * source($str, $full_path = FALSE)
     * functions allows the insert of images inside the $this->_images, by receiving the source paths ($str). If the source paths are not relative to the $this->_source_directory, the second parameter should be set to TRUE
     * @param array|str $str
     * @param bool $full_path
     * @return array
     */
    public function source($str, $full_path = FALSE)
    {
        if(is_array($str))
        {
            foreach($str as $key => $value)
            {
                $str[$key] = $this->source($str[$key], $full_path);
            }
            return $str;
        }

        if($full_path)
        {
            $source_image = str_replace('\\','/',$str);
            $path = explode('/',$source_image);
            $image_name = $path[sizeof($path)-1];
        }
        else
        {
            $source_image = str_replace('\\','/',FCPATH.$this->_source_directory).'/'.$str;
            $image_name = $str;
        }


        if(file_exists($source_image))
        {
            $source_size = getimagesize($source_image);
            $this->_images[] = array('source_image' => $source_image, 'image_name' => $image_name, 'source_width'=>$source_size[0],'source_height'=>$source_size[1]);
        }
        else
        {
            $this->_errors[] = 'Image_nation: Couldn\'t find the image '.$source_image;
        }
    }

    /**
     * _set_sizes($parameters = NULL)
     * private function allowing the $this->_images to receive the dimensions
     * @param null $parameters
     */
    private function _set_sizes($parameters = NULL)
    {
        foreach($this->_images as $key => $value)
        {
            if(is_null($parameters))
            {
                $this->_images[$key]['sizes'] = $this->_sizes;
            }
            else
            {
                $dimensions = explode('|',$parameters);
                foreach($dimensions as $dimension)
                {
                    $this->_images[$key]['sizes'][$dimension] = $this->_sizes[$dimension];
                }
            }
        }
    }

    /**
     * clear_sizes()
     * public function that will clear all sizes
     * @return bool
     */
    public function clear_sizes()
    {
        $this->_sizes = array();
        return TRUE;
    }

    /**
     * add_size($parameters)
     * function allowing the adding of sizes and/or modifying the already added sizes
     * @param array $parameters
     * @return bool
     */
    public function add_size($parameters)
    {
        if(is_array($parameters))
        {
            $this->_sizes = array_merge($this->_sizes, $parameters);
        }
        foreach($parameters as $key => $value)
        {
            $width_height = explode('x',$key);
            $this->_sizes[$key]['width'] = $width_height[0];
            $this->_sizes[$key]['height'] = $width_height[1];
        }
        return TRUE;
    }

    /**
     * process($parameters = NULL)
     * function will allow the processing of the images. If $parameters are given, only the images mentioned in the parameters will be created
     * @param null $parameters
     */
    public function process($parameters = NULL)
    {
        if(!is_null($parameters) && is_array($parameters))
        {
            $this->clear_sizes();
            $this->add_size($parameters);
        }
        elseif(!is_null($parameters) && !is_array($parameters))
        {
            $this->_set_sizes($parameters);
        }
        else
        {
            $this->_set_sizes();
        }

        return $this->_create_images();
    }

    /**
     * _iterate_file_exists($path, $overwrite = FALSE)
     * private function that will look if a file exists and, if $overwrite is set to FALSE, will look for another possible name by iterating from 1 to $this->_max_filename_increment
     * @param $path
     * @param bool $overwrite
     * @return string
     */
    private function _iterate_file_exists($path, $overwrite = FALSE)
    {
        if(file_exists($path) && ($overwrite===FALSE))
        {
            for($i=1;$i<=$this->_max_filename_increment;$i++)
            {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $new_file = rtrim($path,'.'.$ext);
                $new_file .= '-'.$i.'.'.$ext;
                if(!file_exists($new_file))
                {
                    break;
                }
            }
            return $new_file;
        }
        else
        {
            return $path;
        }
    }

    /**
     * get_errors()
     * public function that will return the errors in working with images if there are any, or FALSE if no error was encountered
     * @return array|bool
     */
    public function get_errors()
    {
        if(!empty($this->_errors))
        {
            return $this->_errors;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * get_processed()
     * public function that will return an array with the processed files ($this->_processed_images)
     * @return array
     */
    public function get_processed()
    {
        return $this->_processed_images;
    }

    /**
     * _create_images()
     * private function that will iterate through the $this->_images and process the images according to the parameters given. returns $this->_processed_images
     * @return array
     */
    private function _create_images()
    {
        //print_r($this->_images);
        //exit;
        foreach($this->_images as $key => $image)
        {
            $master_config['image_library'] = $this->_image_library;
            $master_config['create_thumb'] = FALSE;
            foreach($image['sizes'] as $image_size => $params)
            {
                $size_config = array();
                $size_config['source_image'] = $image['source_image'];
                $size_config['quality'] = '100%';
                if(!isset($params['directory'])) $params['directory'] = $this->_parent_directory.'/';
                if(!file_exists($params['directory']))
                {
                    mkdir($params['directory']);
                }
                if($this->_size_folders===TRUE)
                {
                    if(!file_exists(FCPATH.$params['directory'].$image_size))
                    {
                        if(!mkdir(FCPATH.$params['directory'].$image_size))
                        {
                            show_error('Couldn\'t create directory '.$image_size);
                        }
                    }
                    else
                    {
                        $params['directory'] .= $image_size . '/';
                    }
                }
                $ext = pathinfo($image['source_image'], PATHINFO_EXTENSION);
                if(($this->_size_folders===FALSE) && ($params['file_name']===FALSE))
                {
                    $file_name = rtrim($image['image_name'],'.'.$ext);
                    $file_name .= '-'.$image_size.'.'.$ext;
                }
                elseif(isset($params['file_name']))
                {
                    $file_name = $params['file_name'].'.'.$ext;
                }
                else
                {
                    $file_name = $image['image_name'];
                }
                $size_config['new_image'] = $params['directory'].$file_name;
                $size_config['new_image'] = $this->_iterate_file_exists($size_config['new_image'],$params['overwrite']);
                $source_ratio = $image['source_width'] / $image['source_height'];
                $new_ratio = $params['width'] / $params['height'];
                if($params['keep_aspect_ratio']===FALSE && ($source_ratio!=$new_ratio))
                {
                    if($new_ratio > $source_ratio || (($new_ratio == 1) && ($source_ratio < 1)))
                    {
                        $size_config['width'] = $image['source_width'];
                        $size_config['height'] = round($image['source_width']/$new_ratio);
                        switch($params['style']['vertical'])
                        {
                            case 'center' :
                                $size_config['y_axis'] = round(($image['source_height'] - $size_config['height'])/2);
                                break;
                            case 'top' :
                                $size_config['y_axis'] = 0;
                                break;
                            case 'bottom' :
                                $size_config['y_axis'] = round(($image['source_height'] - $size_config['height']));
                                break;
                        }
                        $size_config['x_axis'] = 0;
                    }
                    else
                    {
                        $size_config['width'] = round($image['source_height'] * $new_ratio);
                        $size_config['height'] = $image['source_height'];
                        switch($params['style']['horizontal'])
                        {
                            case 'center' :
                            case 'middle' :
                                $size_config['x_axis'] = round(($image['source_width'] - $size_config['width'])/2);
                                break;
                            case 'left' :
                                $size_config['x_axis'] = 0;
                                break;
                            case 'right' :
                                $size_config['x_axis'] = round(($image['source_width'] - $size_config['width']));
                                break;
                        }
                        $size_config['y_axis'] = 0;
                    }
                }

                $config = array_merge($master_config, $size_config);
                $this->load->library('image_lib');
                if($params['keep_aspect_ratio']===FALSE && ($source_ratio!=$new_ratio))
                {
                    $config['maintain_ratio'] = FALSE;
                    $this->image_lib->initialize($config);
                    if(!$this->image_lib->crop())
                    {
                        $this->_errors[] = 'One or more errors was encountered while cropping image '.$config['source_image'];
                        $errors[] = $this->image_lib->display_errors();
                    }
                    $this->image_lib->clear();
                }

                $config['maintain_ratio'] = TRUE;
                $config['master_dim'] = isset($params['master_dim']) ? $params['master_dim'] : $this->_default_master_dim;
                $config['source_image'] = $config['new_image'];
                $config['width'] = $params['width'];
                $config['height'] = $params['height'];
                $config['quality'] = isset($params['quality']) ? $params['quality'] : $this->_default_quality;
                $this->image_lib->initialize($config);
                if(!$this->image_lib->resize())
                {
                    $this->_errors[] = 'One or more errors was encountered while resizing image '.$config['source_image'];
                    $errors[] = $this->image_lib->display_errors();
                }
                $this->image_lib->clear();

                if(!isset($errors)) $errors = array();

                $this->_processed_images[$key][$image_size] = array('file_name'=>$file_name,'path'=>$config['new_image'],'errors'=>$errors);
            }
        }
        return $this->_processed_images;
    }

    public function __get($var)
    {
        return $CI =& get_instance()->$var;
    }
}
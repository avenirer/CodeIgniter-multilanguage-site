<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Upload extends CI_Upload
{
    public $multi = 'all';

    /**
     * Hold multiple errors
     * @var array
     */
    public $multi_errors = array();
    /**
     * keep track if the upload was finished or not
     * @var bool
     */
    public $finished = FALSE;
    /**
     * a temporary string that will be appended to the errors when one or more files is/are not uploaded
     * @var string
     */
    private $tempString;
    /**
     * an array that will contain all the data regarding the successfully uploaded files
     * @var array
     */
    private $uploadedFiles = array();

    function __construct($config = array())
    {
        parent::__construct($config);
        $this->set_multi($config['multi']);
    }

    public function do_upload($field = 'userfile') {

        if (!isset($_FILES[$field])) {
            return false;
        }
        // check if it's a multiple upload. if not then fall back to CI do_upload()
        if (!is_array($_FILES[$field]['name'])) {
            return parent::do_upload($field);
        }
        // also if it is a multiple upload input type, verify if only one file was uploaded, and if yes give it to the CI do_upload()
        elseif(sizeof($_FILES[$field]['name'])==1)
        {
            $files = $_FILES[$field];
            $_FILES[$field]['name'] = $files['name'][0];
            $_FILES[$field]['type'] = $files['type'][0];
            $_FILES[$field]['tmp_name'] = $files['tmp_name'][0];
            $_FILES[$field]['error'] = $files['error'][0];
            $_FILES[$field]['size'] = $files['size'][0];
            return $this->do_upload($field);
        }
        // else do the magic
        else
        {
            $files = $_FILES[$field];
            foreach ($files['name'] as $key => $value)
            {
                $_FILES[$field]['name'] = $files['name'][$key];
                $_FILES[$field]['type'] = $files['type'][$key];
                $_FILES[$field]['tmp_name'] = $files['tmp_name'][$key];
                $_FILES[$field]['error'] = $files['error'][$key];
                $_FILES[$field]['size'] = $files['size'][$key];
                if ($this->do_upload($field))
                {
                    // if the upload was successfull add an element to the uploadedFiles array that contains the data regarding the uploaded file
                    $this->uploadedFiles[] = $this->data();
                }
                else
                {
                    // if the upload was unsuccessfull, set a temporary string that will contain the name of the file in question. The string will later be used by the modified display_errors() method
                    $this->tempString = 'File: ' . $_FILES[$field]['name'].' - Error: ';
                    // keep the errors in the multi_errors array
                    $this->multi_errors[] = $this->display_errors('', '');

                }
                // now we decide if we continue uploading depending on the "multi" key inside the configuration
                switch($this->multi)
                {
                    case 'all':
                        if(sizeof($this->multi_errors)>0 && sizeof($this->uploadedFiles>0))
                        {
                            foreach($this->uploadedFiles as $dataFile)
                            {
                                if(file_exists($dataFile['full_path'])) unlink($dataFile['full_path']);
                            }
                            break 2;
                        }
                        break;
                    case 'halt':
                        if(sizeof($this->multi_errors)>0) break 2;
                        break;
                    //case 'ignore':
                    default :
                        break;
                }
            }
            if(sizeof($this->multi_errors)>0 && $this->multi == 'all' )
            {
                return FALSE;
            }
            // at the end of the uploads, change the finished variable to true so that the class will know it finished it's main job
            $this->finished = TRUE;
            return TRUE;
        }
    }

    public function data($index = NULL)
    {
        //first we loook if the files were uploaded. if they were we just return the array with the data regarding the uploaded files
        if($this->finished === TRUE)
        {
            return $this->uploadedFiles;
        }
        // if the files were not uploaded, then we update the data
        $data = array(
            'file_name'		=> $this->file_name,
            'file_type'		=> $this->file_type,
            'file_path'		=> $this->upload_path,
            'full_path'		=> $this->upload_path.$this->file_name,
            'raw_name'		=> str_replace($this->file_ext, '', $this->file_name),
            'orig_name'		=> $this->orig_name,
            'client_name'		=> $this->client_name,
            'file_ext'		=> $this->file_ext,
            'file_size'		=> $this->file_size,
            'is_image'		=> $this->is_image(),
            'image_width'		=> $this->image_width,
            'image_height'		=> $this->image_height,
            'image_type'		=> $this->image_type,
            'image_size_str'	=> $this->image_size_str,
        );

        if ( ! empty($index))
        {
            return isset($data[$index]) ? $data[$index] : NULL;
        }

        return $data;
    }

    public function display_errors($open = '<p>', $close = '</p>')
    {
        if($this->finished === TRUE)
        {
            return $this->multi_errors;
        }
        $append = $this->tempString;
        $this->tempString = '';

        return (count($this->error_msg) > 0) ? $open . $append . implode($close . $open, $this->error_msg) . $close : '';

    }

    public function set_multi($course)
    {
        $options = array('all', 'halt','ignore');
        if(in_array($course,$options))
        {
            $this->multi = $course;
        }
        return $this;
    }
}
/* End of file MY_Upload.php */
/* Location: ./application/libraries/MY_Upload.php */
<?php
/* first we make sure this isn't called from a web browser */
if ( PHP_SAPI !== 'cli' ) exit('No web access allowed');
/* raise or eliminate limits we would otherwise put on http requests */
set_time_limit(0);
ini_set('memory_limit', '256M');

unset($argv[0]);

$command = (!empty($argv)) ? array_shift($argv) : 'help';
$arguments = $argv;

new Matches ($command, $arguments);

class Matches
{
    private $ret = "\n";
    private $ret2 = "\n\n";
    private $tab = "\t";

    function __construct($command = 'help', $arguments)
    {
        $this->{$command}($arguments);
    }

    public function help()
    {
        //TODO de facut manualul
        echo 'lista cu comenzi';
    }

    public function app($arguments = array())
    {
        $must_have = array(
            'default_key' => 'name',
            'keys' => array(
                'name' => array(
                    'question' => 'What\'s the name of the app stack (controller, model, biew) you want created',
                    'default' => '',
                    'required' => TRUE
                ),
                'extends'=> array(
                    'question'=>'What should the controller/model extend (default is CI)?',
                    'default'=>'CI'
                ),
                'methods' => array(
                    'question' => 'What methods should the controller have?',
                    'default' => 'index'
                )
            )
        );

        $args = $this->get_values($arguments,$must_have);
        print_r($args);

        $app_name = $args['name'];
        $app_extends = $args['extends'];
        $app_methods = $args['methods'];

        $this->controller(array('name:'.$app_name,'extends:'.$app_extends.'_Controller','methods:'.$app_methods));
        $this->model(array('name:'.$app_name,'extends:'.$app_extends.'_Model','methods:'.$app_methods));
        //$this->view(array('name:'.$app_name));

    }

    public function controller($arguments = array())
    {
        $must_have = array(
            'default_key' => 'name',
            'keys' => array(
                'name' => array(
                    'question' => 'What\'s the name of the controller you want created',
                    'default' => '',
                    'required' => TRUE
                ),
                'extends'=> array(
                    'question'=>'What should the controller extend?',
                    'default'=>'CI_Controller'
                ),
                'methods' => array(
                    'question' => 'What methods should the controller have?',
                    'default' => 'index'
                )
            )
        );
        $args = $this->get_values($arguments,$must_have);

        $file_data = $this->file_data($args['name'], 'controller');

        $requested_methods = explode(',',$args['methods']);
        $methods = '';
        foreach($requested_methods as $method)
        {
            $method_def = (substr($method,0,1) == '_') ? 'private function '.$method.'()' : 'public function '.$method.'()';
            $methods .= $this->tab.$method_def.$this->ret;
            $methods .= $this->tab.'{'.$this->ret;
            $methods .= $this->tab.'}'.$this->ret2;
        }
        $values['{{CONTROLLER}}'] = $file_data['class_name'];
        $values['{{CONTROLLER_FILE}}'] = $file_data['file'];
        $values['{{FILE_PATH}}'] = $file_data['file_path'];
        $values['{{EXTENDS}}'] = $args['extends'];
        $values['{{METHODS}}'] = $methods;

        if($this->create_file($file_data['file_path'],'controller',$values))
        {
            echo $this->ret.'Controller '.$file_data['class_name'].' has been created inside '.$file_data['file_path'].'.'.$this->ret;
            return TRUE;
        }
        else
        {
            echo $this->ret.'Couldn\'t create controller...';
            return FALSE;
        }


        //$args = $this->ask_away($questions,$args);
        //print_r($args);
        echo $this->ret2;

        //$extends = (isset($arguments))
        //echo 'create controller';
        //echo ;
    }

    public function model($arguments = array())
    {
        $must_have = array(
            'default_key' => 'name',
            'keys' => array(
                'name' => array(
                    'question' => 'What\'s the name of the model you want created',
                    'default' => '',
                    'required' => TRUE
                ),
                'extends'=> array(
                    'question'=>'What should the model extend?',
                    'default'=>'CI_Model'
                ),
                'methods' => array(
                    'question' => 'What methods should the model have?',
                    'default' => 'get'
                )
            )
        );
        $args = $this->get_values($arguments,$must_have);

        $file_data = $this->file_data($args['name'], 'model');

        $requested_methods = explode(',',$args['methods']);
        $methods = '';
        foreach($requested_methods as $method)
        {
            $method_def = (substr($method,0,1) == '_') ? 'private function '.$method.'()' : 'public function '.$method.'()';
            $methods .= $this->tab.$method_def.$this->ret;
            $methods .= $this->tab.'{'.$this->ret;
            $methods .= $this->tab.'}'.$this->ret2;
        }
        $values['{{MODEL}}'] = $file_data['class_name'];
        $values['{{MODEL_FILE}}'] = $file_data['file'];
        $values['{{FILE_PATH}}'] = $file_data['file_path'];
        $values['{{EXTENDS}}'] = $args['extends'];
        $values['{{METHODS}}'] = $methods;

        if($this->create_file($file_data['file_path'],'model',$values))
        {
            echo $this->ret.'Model '.$file_data['class_name'].' has been created inside '.$file_data['file_path'].'.'.$this->ret;
            return TRUE;
        }
        else
        {
            echo $this->ret.'Couldn\'t create model...';
            return FALSE;
        }


        //$args = $this->ask_away($questions,$args);
        //print_r($args);
        echo $this->ret2;

        //$extends = (isset($arguments))
        //echo 'create controller';
        //echo ;
    }


    private function file_data($path,$type)
    {
        $path = explode('/',$path);
        $file_name = explode('.',array_pop($path));
        $class_name = ucfirst($file_name[0]);
        $file = $class_name.'.php';
        $file_path = getcwd().'/'.$type.'s/'.implode('//',$path);
        $file_path = rtrim($file_path,'/');
        $file_path = $file_path.'/'.$file;
        $file_data = array('file'=>$file,'file_path'=>$file_path,'class_name'=>$class_name);
        return $file_data;
    }

    private function create_file($file_path,$template,$values)
    {
        if(!file_exists('matches/templates/'.$template.'.txt'))
        {
            echo $this->ret.'The template file doesn\'t exist.'.$this->ret;
            return FALSE;
        }
        if(file_exists($file_path))
        {
            echo $this->ret.'The file '.$this->ret.$file_path.' already exists.'.$this->ret;
            return FALSE;
        }
        else
        {
            $content = file_get_contents('matches/templates/'.$template.'.txt');
            $content = strtr($content,$values);
            $path = explode('/',$file_path);
            array_pop($path);
            $path = implode('/',$path);
            if(!file_exists($path))
            {
                mkdir($path, 0777, true);
            }
            if(file_put_contents($file_path,$content))
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }

        }
    }

    private function get_values($arguments,$must_have)
    {
        $default_key = $must_have['default_key'];
        $returned_array = array();
        foreach ($arguments as $value)
        {
            $new_value = explode(':', $value);
            if (sizeof($new_value) == 2) {
                $returned_array[$new_value[0]] = $new_value[1];
            }
            else
            {
                $returned_array[$default_key] = $new_value[0];
            }
        }

        foreach ($must_have['keys'] as $key => $must) {
            if (!array_key_exists($key, $returned_array)) {
                $returned_array[$key] = $this->ask_away($must);
            }
        }
        return $returned_array;
    }


    private function ask_away($must)
    {
        if(!isset($must['default']) || (sizeof($must['default']) == 0) || (isset($must['required']) && $must['required']===TRUE))
        {
            echo $must['question'];
            echo $this->ret;
            $input = trim(fgets(STDIN, 1024));
        }
        else
        {
            $input = $must['default'];
        }
        return $input;
    }
}


//$input = trim(fgets(STDIN,1024));

//echo "\n".$input."\n\n";

//echo $argc; // count of arguments

//echo '\n';



//print_r($argv);
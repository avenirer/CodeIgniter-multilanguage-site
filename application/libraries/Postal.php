<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Postal {

    private $ci;
    public $message_types = array();

    function __construct() {
        $this->ci = & get_instance();
        $this->ci->load->library('session');
        $this->ci->load->config('postal', TRUE, TRUE);
        if(empty($this->message_types))
        {
            $this->message_types = ($this->ci->config->item('message_types','postal') !== NULL) ? $this->ci->config->item('message_types','postal') : array(
                    'success' => array('<div class="success">', '</div>'),
                    'error' => array('<div class="error">', '</div>'),
                    'message' => array('<div class="message">', '</div>')
                );
        }
    }

    function add($message, $message_type = 'message')
    {
        $messages = array();
        if(isset($_SESSION['postal']) && is_array($_SESSION['postal']))
        {
            $messages = $_SESSION['postal'];
        }

        if (is_a($message, 'Exception')) {
            $message = $message->getMessage();
            $message_type = 'error';
        }

        if ((!isset($messages[$message_type]) || !in_array($message, $messages[$message_type])) && is_string($message) && $message) {
            $messages[$message_type][] = $message;
        }
        $_SESSION['postal'] = $messages;
    }

    function get($message_type = null, $as_array = FALSE) {

        if(is_array($message_type))
        {
            foreach($message_type as $type)
            {
                $this->get($type, $as_array);
            }
        }

        $messages = array();
        if(isset($_SESSION['postal']) && is_array($_SESSION['postal']))
        {
            $messages = $_SESSION['postal'];
        }

        if(empty($messages))
        {
            return FALSE;
        }

        $output = ($as_array === TRUE) ? array() : '';

        if (is_null($message_type))
        {
            foreach ($this->message_types as $type => $delimiters)
            {
                if (array_key_exists($type, $messages))
                {
                    foreach ($messages[$type] as $message)
                    {
                        if ($as_array === TRUE)
                        {
                            $output[$type][] = $message;
                        }
                        else
                        {
                            $output .= $this->message_types[$type][0];
                            $output .= $message;
                            $output .= $this->message_types[$type][1];
                        }
                    }
                    $this->clear($type);
                }
            }
        }
        else
        {
            if (array_key_exists($message_type, $messages))
            {
                foreach ($messages[$message_type] as $message)
                {
                    if ($as_array)
                    {
                        $output[] = $message;
                    }
                    else
                    {
                        $output .= $this->message_types[$message_type][0] . $message . $this->message_types[$message_type][1];
                    }
                }
                $this->clear($message_type);
            }
        }
        return $output;
    }

    function clear($message_type = null)
    {
        if (!empty($message_type))
        {
            $messages = array();
            if(isset($_SESSION['postal']) && is_array($_SESSION['postal']))
            {
                $messages = $_SESSION['postal'];
            }

            if (array_key_exists($message_type, $messages)) {
                unset($messages[$message_type]);
                $_SESSION['postal'] = $messages;
            }
        }
        else
        {
            $_SESSION['postal'] = array();
        }
    }



    function count($message_type = null)
    {
        $messages = array();
        if(isset($_SESSION['postal']) && is_array($_SESSION['postal']))
        {
            $messages = $_SESSION['postal'];
        }

        if (isset($message_type))
        {
            if (array_key_exists($message_type, $messages)) {
                return sizeof($messages[$message_type]);
            }
            return 0;
        }
        else
        {
            $i = 0;
            foreach ($messages as $message_type => $m)
            {
                $i += sizeof($messages[$message_type]);
            }
            return $i;
        }
    }


}
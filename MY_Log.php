<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Vpx Kog
 *
 * Extends core Log Class
 *
 * @author      Liaan vd Merwe <info@vpx.co.za>
 
 * @version     1.0 
 *
 */
class MY_log extends CI_Log {

    function __construct()
    {

        parent::__construct();
    }

    /**
     * Write Log File
     *
     * Generally this function will be called using the global log_message() function
     *
     * @param	string	$level 	The error level: 'error', 'debug' or 'info'
     * @param	string	$msg 	The error message
     * @return	bool
     */
    public function write_log($level, $msg)
    {

        if ($this->_enabled === FALSE)
        {
            return FALSE;
        }

        $level = strtoupper($level);

        if ((!isset($this->_levels[$level]) OR ( $this->_levels[$level] > $this->_threshold)) && !isset($this->_threshold_array[$this->_levels[$level]]))
        {
            return FALSE;
        }

        $filepath = $this->_log_path . 'log-' . date('Y-m-d') . '.' . $this->_file_ext;
        $message = '';

        if (!file_exists($filepath))
        {
            $newfile = TRUE;
            // Only add protection to php files
            if ($this->_file_ext === 'php')
            {
                $message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
            }
        }

        if (!$fp = @fopen($filepath, 'ab'))
        {
            return FALSE;
        }

        flock($fp, LOCK_EX);

        // Instantiating DateTime with microseconds appended to initial date is needed for proper support of this format
        if (strpos($this->_date_fmt, 'u') !== FALSE)
        {
            $microtime_full = microtime(TRUE);
            $microtime_short = sprintf("%06d", ($microtime_full - floor($microtime_full)) * 1000000);
            $date = new DateTime(date('Y-m-d H:i:s.' . $microtime_short, $microtime_full));
            $date = $date->format($this->_date_fmt);
        } else
        {
            $date = date($this->_date_fmt);
        }

        $message .= $this->_format_line($level, $date, $msg);

        for ($written = 0, $length = self::strlen($message); $written < $length; $written += $result)
        {
            if (($result = fwrite($fp, self::substr($message, $written))) === FALSE)
            {
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        if (isset($newfile) && $newfile === TRUE)
        {
            chmod($filepath, $this->_file_permissions);
        }

        if (function_exists('mail'))
        {
            ##Email mesasge as well, will slow down server if lots of debugging is happening so make sure to enable emails only on production
            $config = & get_config();

            if (isset($config['log_email']) and $config['log_email'])
            {
                if (!isset($config['log_email_to_address']))
                {
                    show_error('To email not set for email logs');
                }
                if (!isset($config['log_email_from_address']))
                {
                    show_error('From email not set for email logs');
                }
                if (!isset($config['log_email_subject']))
                {
                    show_error('Email subject not set');
                }
                $headers = 'From: ' . $config['log_email_from_name'] . ' <' . $config['log_email_from_address'] . ">\r\n";

                //$mail = new PHPMailer(true);
                mail($config['log_email_to_address'], "Codeigniter Error", $msg, $headers);
            }
        }


        return is_int($result);
    }

}

/* End of file Vpx_Output.php */
/* Location: ./application/models/Vpx_Output.php */

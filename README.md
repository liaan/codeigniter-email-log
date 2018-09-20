# codeigniter-email-log

Extend the Log class so that all errrors/warning etc is also emailed after written to the log file

Copy MY_Log.php to application/core and rename if needed to your subclass_prefix

### WARNING .. Makes the site slow if debug loggine enabled with email as each message is sent individually

In config.php file of your CI_ENV add:
```
$config['log_email'] = true;  
$config['log_email_to_address'] = 'info@vpx.co.za';  
$config['log_email_from_address'] = 'info@vpx.co.za';  
$config['log_email_from_name'] = 'CodeIgniter Log';  
$config['log_email_subject'] = 'My Site Error';  
```

<?php
// call your class (require_once)
require_once 'reCaptcha.php';
$reCaptcha = new reCaptcha('register', '(site key)', '(secret key)'); // load recaptcha class for spam protection

if ($_SERVER['REQUEST_METHOD'] === 'POST') // Check form submitted via POST
{
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
	
    if (!$reCaptcha->success()) {
        echo 'recaptcha failed!';
    } else {
    	echo 'recaptcha success';
    }
} else {
		// other stuff
}

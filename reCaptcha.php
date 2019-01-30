<?php

class reCaptcha
{
    protected $form_label;
    protected $site_key;
    private $secret_key;

    public function __construct($form_label, $site_key, $secret_key)
    {
        $this->form_label = $form_label;
        $this->site_key = $site_key;
        $this->secret_key = $secret_key;
    }

    public function success()
    {
        if (!empty($_POST['g-recaptcha-response'])) {
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = [
                'secret' => $this->secret_key,
                'response' => $_POST['g-recaptcha-response'],
                'remoteip' => !empty($this->getIP()) ? $this->getIP() : ''
            ];

            $options = [
                'http' => [
                    'header' => 'Content-type: application/x-www-form-urlencoded\r\n',
                    'method' => 'POST',
                    'content' => http_build_query($data)
                ]
            ];

            $context = stream_context_create($options);
            $result = json_decode(file_get_contents($url, false, $context));

            #print_r($result);
            return isset($result->score) >= 0.5;
        }
        return false;
    }

    public function render()
    {
        return '
            <script src="https://www.google.com/recaptcha/api.js?render=' . $this->site_key . '"></script>
            <script>
                grecaptcha.ready(function () {
                    grecaptcha.execute(\'' . $this->site_key . '\', { action: \'' . $this->form_label . '\' }).then(function (token) {
                        var recaptchaResponse = document.getElementById(\'recaptchaResponse\');
                        recaptchaResponse.value = token;
                    });
                });
            </script>
		';
    }

    public function getIP()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
        {
            if (array_key_exists($key, $_SERVER) === true)
            {
                foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip)
                {
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
                    {
                        return $ip;
                    }
                }
            }
        }
        return false;
    }

}

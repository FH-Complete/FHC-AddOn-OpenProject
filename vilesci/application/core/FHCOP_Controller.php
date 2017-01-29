<?php

defined('BASEPATH') or exit('No direct script access allowed');

class FHCOP_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

		$this->load->library('rest');

		$config = $this->config->item('openproject');
		$url = rtrim($config['server'], '/') . $config['api_path'];

		$rest_config = [
			'server' => $url,
			'http_user' => 'apikey',
			'http_pass' => $config['api_key'],
			'http_auth' => 'basic',
			//'ssl_verify_peer' => TRUE,
			//'ssl_cainfo' => '/certs/cert.pem'
		];

		$this->rest->initialize($rest_config);
    }

    protected function responseJSON($content)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($content));
    }

    protected function responseError($errorCode, $message, $description, $statusCode = 400)
    {
        $this->output->set_status_header($statusCode);
        $this->responseJSON(["error" => ["code" => $errorCode, "value" => $message, "description" => $description]]);
    }
}

<?php

defined('BASEPATH') || exit('No direct script access allowed');

class FHCOP_Controller extends CI_Controller
{
    /**
     * FHCOP_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

		$this->load->library('rest');

		$config = $this->config->item('openproject');
		$url = rtrim($config['server'], '/').$config['api_path'];

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

    /**
     * Sets the content type to JSON and outsputs the content
     *
     * @param string $content Content to be displayed as JSON.
     */
    protected function _responseJSON($content)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($content));
    }

    /**
     * Sets the content type to JSON and outputs an error
     *
     * @param int $errorCode Error code.
     * @param string $message Error message (what is wrong).
     * @param string $description Error description (how to fix it).
     * @param int $statusCode HTTP status code.
     */
    protected function _responseError($errorCode, $message, $description, $statusCode = 400)
    {
        $this->output->set_status_header($statusCode);
        $this->responseJSON(["error" => ["code" => $errorCode, "value" => $message, "description" => $description]]);
    }
}

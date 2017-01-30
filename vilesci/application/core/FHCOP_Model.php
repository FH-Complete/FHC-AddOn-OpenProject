<?php

defined('BASEPATH') or exit('No direct script access allowed');

class FHCOP_Model extends CI_Model {

	public $result;

	function __construct() {
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

        $json_file = file_get_contents(getcwd() . "/application/config/openproject.json");
        $this->config->load('openproject');
        $this->config->set_item('openproject', json_decode($json_file, true));
	}
}

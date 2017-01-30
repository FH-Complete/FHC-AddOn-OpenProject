<?php

defined('BASEPATH') || exit('No direct script access allowed');

class FHCOP_Model extends CI_Model
{

    public $result;

    /**
     * FHCOP_Model constructor.
     *
     * Loads the rest library, reads and sets the openproject config if not done yet.
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->config->item('openproject') == null)
        {
            $prod_path = APPPATH.'config/openproject.json';
            $dev_path = APPPATH.'config/development/openproject.json';

            if (file_exists($dev_path))
            {
                $config_path = $dev_path;
            }
            elseif (file_exists($prod_path))
            {
                $config_path = $prod_path;
            }
            else
            {
                echo 'No config file found found!';
                return;
            }

            $config = json_decode(file_get_contents($config_path), true);
            $this->config->set_item('openproject', $config);
        }

        if(!$this->load->is_loaded('rest'))
        {
            $this->load->library('rest');

            $config = $this->config->item('openproject');
            $url = rtrim($config['server'], '/').$config['api_path'];

            $rest_config = [
                'server' => $url,
                'http_user' => 'apikey',
                'http_pass' => $config['api_key'],
                'http_auth' => 'basic',
            ];

            $this->rest->initialize($rest_config);
        }
    }
}

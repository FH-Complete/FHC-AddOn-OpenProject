<?php

defined('BASEPATH') || exit('No direct script access allowed');

class FHCOP_Controller extends CI_Controller
{
    /**
     * FHCOP_Controller constructor.
     *
     * Loads the rest library, reads and sets the openproject config if not done yet.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('language');
        $this->lang->load('fhcop');

        $fhc = $this->load->database('fhcomplete', true);
        $query = $fhc->get_where("system.tbl_appdaten", array('uid' => 'admin', 'app' => 'FHCOP-AddOn'));
        $result = $query->result();
        if ($query->num_rows() === 0)
        {
            $this->_responseError(FHCOP_NO_CONFIG, '/Install');
            $this->output->_display();
            die();
        }
        $config = json_decode($result[0]->daten, true);
        $this->config->set_item('openproject', $config);
        $fhc->close();


        $this->load->library('rest');

        $config = $this->config->item('openproject');
        $url = rtrim($config['server'], '/').$this->config->item('api_path');

        $rest_config = [
            'server' => $url,
            'http_user' => 'apikey',
            'http_pass' => $config['api_key'],
            'http_auth' => 'basic',
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
     * @param int $statusCode HTTP status code.
     */
    protected function _responseError($errorCode, $message, $statusCode = 400)
    {
        $this->output->set_status_header($statusCode);
        $this->_responseJSON([
            'error' => [
                'value' => $message,
                'description' => $this->lang->line('fhcop_'.$errorCode)
            ],
            'success' => false
        ]);
    }
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');

class FHCOP_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function responseJSON($content)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($content));
    }
}

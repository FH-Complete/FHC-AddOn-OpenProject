<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class FHC_Projekt extends FHCOP_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database('fhcomplete');
        $this->load->model('planner/FHC_Projekt_Model', 'ProjektModel');
    }

    public function projekt($projekt_kurzbz)
    {
        $this->responseJSON($this->ProjektModel->load($projekt_kurzbz));
    }
}
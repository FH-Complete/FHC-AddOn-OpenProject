<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class FHC_Projekt extends FHCOP_Controller
{
    /**
     * FHC_Projekt constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('planner/projekt_model', 'ProjektModel');
    }

    /**
     * Zeigt das FHComplete Planner projekt an.
     *
     * @param string $projekt_kurzbz Die 'projekt_kurzbz' des gewünschten Projekts.
     */
    public function projekt($projekt_kurzbz)
    {
        $this->_responseJSON($this->ProjektModel->load($projekt_kurzbz));
    }
}

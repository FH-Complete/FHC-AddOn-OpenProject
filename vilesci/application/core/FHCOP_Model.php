<?php

defined('BASEPATH') || exit('No direct script access allowed');

class FHCOP_Model extends CI_Model
{

    /**
     * FHCOP_Model constructor.
     *
     * Loads the rest library, reads and sets the openproject config if not done yet.
     */
    public function __construct()
    {
        parent::__construct();
    }
}

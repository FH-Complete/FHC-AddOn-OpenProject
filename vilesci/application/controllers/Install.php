<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once('../../../include/authentication.class.php');
require_once('../../../include/benutzerberechtigung.class.php');


class Install extends CI_Controller
{
    /**
     * Install constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->database('fhcomplete');
    }

    /**
     * Adds two new permission for the plugin and assignes them to admin.
     *
     * @return void
     */
    public function index()
    {
        $uid = (new authentication())->getUser();
        $berechtigungen = new benutzerberechtigung();
        $berechtigungen->getBerechtigungen($uid);

        if (!$berechtigungen->isBerechtigt('basis/addon'))
        {
            echo 'No permissions to run the install script!';
            return;
        }

        $permissions = [
            [
                'berechtigung_kurzbz' => 'openproject/sync',
                'beschreibung' => 'OpenProject Projekt Synchronisation'
            ],
            [
                'berechtigung_kurzbz' => 'openproject/config',
                'beschreibung' => 'OpenProject Plugin Konfiguration'
            ]
        ];

        foreach ($permissions as $permission)
        {
            $query = $this->db->get_where('system.tbl_berechtigung', $permission);

            if ($query->num_rows() < 1)
            {
                $this->db->insert('system.tbl_berechtigung', $permission);
                echo 'Inserted new permission '.$permission['berechtigung_kurzbz'];
            }
            else
            {
                echo 'Permission '.$permission['berechtigung_kurzbz'].' does already exist';
            }
            echo '<br>';
        }
        echo '<br>';

        $permission_assignments = [
            [
                'berechtigung_kurzbz' => 'openproject/sync',
                'rolle_kurzbz' => 'admin',
                'art' => 'suid'
            ],
            [
                'berechtigung_kurzbz' => 'openproject/config',
                'rolle_kurzbz' => 'admin',
                'art' => 'suid'
            ]
        ];

        foreach ($permission_assignments as $permission_assignment)
        {
            $query = $this->db->get_where('system.tbl_rolleberechtigung', $permission_assignment);

            if ($query->num_rows() < 1)
            {
                $this->db->insert('system.tbl_rolleberechtigung', $permission_assignment);
                echo 'Assigned '.$permission_assignment['berechtigung_kurzbz'].
                    ' to '.$permission_assignment['rolle_kurzbz'];
            }
            else
            {
                echo $permission_assignment['berechtigung_kurzbz'].
                    ' already assigned to '.$permission_assignment['rolle_kurzbz'];
            }
            echo '<br>';
        }
        echo '<br>';

        $fhc = $this->load->database('fhcomplete', true);
        $query = $fhc->get_where('system.tbl_appdaten', ['uid' => 'admin', 'app' => 'FHCOP-AddOn']);
        if ($query->num_rows() === 0)
        {
            $config = [
              'server' => '',
              'api_key' => '',

              'type_mapping' => [
                  'Arbeitspaket' => ['href' => '', 'title' => ''],
                  'Milestone' => ['href' => '', 'title' => ''],
                  'Task' => ['href' => '', 'title' => ''],
                  'Projektphase' => ['href' => '', 'title' => ''],
                ],

                'status_mapping' => [
                  'new' => ['href' => '', 'title' => ''],
                  'closed' => ['href' => '', 'title' => '']
                ],

                'role_mapping' => [
                    'member' => ['id' => 0, 'name' => ''],
                    'admin' => ['id' => 0, 'name' => ''],
                ]
            ];

            $data = [
                'uid' => 'admin',
                'app' => 'FHCOP-AddOn',
                'daten' => json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'freigabe' => 'false'
            ];

            $fhc->insert("system.tbl_appdaten", $data);
            echo 'Initialized empty config in database';
        }
        else
        {
            echo "Config entry 'FHCOP-AddOn' in database does already exist";
        }
        $fhc->close();
    }
}

<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('../../../include/authentication.class.php');
require_once('../../../include/benutzerberechtigung.class.php');

/**
 * Syncs FHC Projekt to OP Projekt.
 */
class Sync extends FHCOP_Controller
{
    /**
     * Sync constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('planner/projekt_model', 'DataModel');
        $this->load->model('openproject/project_model', 'ProjectModel');
        $this->load->model('openproject/user_model', 'UserModel');
        $this->load->model('openproject/workpackage_model', 'WorkPackageModel');
    }

    /**
     * Default functions.
     */
    public function index()
    {
        if (!isset($_GET['projekt_kurzbz']))
        {
            $this->_responseError('MISSING_PARAM', 'projekt_kurzbz', 'GET Parameter is missing.');
            return;
        }
        // http://fhcop-inf.technikum-wien.at/fhcomplete/addons/openproject/vilesci/index.dist.php/planner/FHC_Projekt/projekt/TestProjekt
        // /\-- JSON of TestProjekt
        // Load FHComplete Projekt
        $projekt = $this->DataModel->load($_GET['projekt_kurzbz']);

        // Check if FHComplete Projekt exists
        if (is_array($projekt))
        {
            $this->_responseError('NOT_FOUND', 'projekt_kurzbz', $projekt['error']);
            return;
        }

        if (!$this->__has_permission())
        {
            $this->_responseError('ACCESS_DENIED', '', 'You must be an admin or a project member of '.$_GET['projekt_kurzbz']);
            return;
        }

        // Check for missing users
        $missing_users = [];
        foreach ($projekt->projektressourcen as $res)
        {
            if (!$this->UserModel->exists($res->ressource_id))
            {
                $missing_users[] = $res->ressource_id;
            }
        }

        // Exit if users are missings
        if (count($missing_users) !== 0 && !(isset($_GET['user_check']) && $_GET['user_check'] === 'false'))
        {
            $this->_responseError(
                'MISSING_USERS',
                $missing_users,
                'Not all Users have been created in OpenProject. Use GET Parameter \'user_check=false\' to ignore this error.',
                409
            );
            return;
        }

        // Create OP Project
        if (($project_id = $this->ProjectModel->insert($projekt->projekt_kurzbz, $projekt->titel, $projekt->beschreibung)) === -1)
        {
            $this->_responseError('CANNOT_CREATE', 'Project', 'Error when inserting Project, it might exist already.');
            return;
        }

        // MAPPING funktion_kurzbz -> roles OP
        // Add all existing users to OP Project
        foreach ($projekt->projektressourcen as $res)
        {
            if (!in_array($res->ressource_id, $missing_users))
            {
                $admin = $res->funktion_kurzbz == 'Projektleiter' ? true : false;
                $this->ProjectModel->add_user($res->ressource_id, $admin);
            }
        }

        // Creating all Workpackages OP from Projektphasen/Tasks FHC
        $this->load->library('workpackage');

        foreach ($projekt->projektphasen as $phase)
        {
            $wp_phase = new Workpackage();
            $wp_phase->SetSubject($phase->bezeichnung)
                ->setDescription($phase->beschreibung)
                ->setStatus('new')
                ->setPersonDays($phase->personentage)
                ->setType($phase->typ)
                ->setProjectID($project_id)
                ->setStartDate($phase->start)
                ->setDueDate($phase->ende);
            if (!in_array($phase->ressource_id, $missing_users))
            {
                $wp_phase->setResponsible($phase->ressource_id);
            }

            // Maps OP workpackage id to FHC projektphase_id
            $phasen_map[$phase->projektphase_id] = $this->WorkPackageModel->create($wp_phase);

            foreach ($phase->projekttasks as $task)
            {
                $wp_task = new Workpackage();
                $wp_task->SetSubject($task->bezeichnung)
                    ->setDescription($task->beschreibung)
                    ->setStatus($task->erledigt === 't' ? 'closed' : 'new')
                    ->setParent($phasen_map[$phase->projektphase_id])
                    // Aufwand ???
                    ->setType('Task')
                    ->setProjectID($project_id)
                    ->setDueDate($task->ende);
                if (!in_array($task->ressource_id, $missing_users))
                {
                    $wp_task->setResponsible($task->ressource_id);
                }
                $this->WorkPackageModel->create($wp_task);
            }
        }

        // Patch OP Workpackages to include parent phasen
        foreach ($projekt->projektphasen as $phase)
        {
            if (!is_null($phase->projektphase_fk))
            {
                $this->WorkPackageModel->set_parent($phasen_map[$phase->projektphase_id], $phasen_map[$phase->projektphase_fk]);
            }
        }

        $this->_responseJSON(['success' => true]);
    }


    private function __has_permission()
    {
        $uid = (new authentication())->getUser();

        return $this->__is_admin($uid) || $this->__is_project_member($uid);
    }

    private function __is_project_member($uid)
    {
        $fhc_db = $this->load->database('fhcomplete', true);

        $fhc_db->select('mitarbeiter_uid', 'student_uid');
        $fhc_db->from('fue.tbl_ressource r');
        $fhc_db->join('fue.tbl_projekt_ressource pr', 'pr.ressource_id = r.ressource_id');
        $fhc_db->where('pr.projekt_kurzbz', $_GET['projekt_kurzbz']);
        $fhc_db->where('r.student_uid', $uid);
        $fhc_db->or_where('r.mitarbeiter_uid', $uid);
        $query = $fhc_db->get();

        return $query->num_rows() > 0;
    }

    private function __is_admin($uid)
    {
        $berechtigungen = new benutzerberechtigung();
        $berechtigungen->getBerechtigungen($uid);

        return $berechtigungen->isBerechtigt('admin');
    }
}

<?php
defined('BASEPATH') || exit('No direct script access allowed');
require_once('../../../include/authentication.class.php');
require_once('../../../include/benutzerberechtigung.class.php');

class Configure extends FHCOP_Controller
{

    private $permitted_post_parameter = ['url', 'api_key', 'Arbeitspaket', 'Milestone', 'Task', 'Projektphase', 'new', 'closed', 'member', 'admin'];

    /**
     * Configure constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->database('openproject');
    }

    /**
     *
     */
    public function index()
    {
        $uid = (new authentication())->getUser();
        $berechtigungen = new benutzerberechtigung();
        $berechtigungen->getBerechtigungen($uid);
        if(!$berechtigungen->isBerechtigt('admin'))
        {
            $this->_responseError('ACCESS_DENIED', $uid, 'User is not an admin.');
            return;
        }

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

        if (json_last_error() !== JSON_ERROR_NONE)
        {
            echo 'Config file contains invalid JSON.';
            return;
        }


        $types = $this->__getTypes();
        $roles = $this->__getRoles();
        $statuses = $this->__getStatuses();

        $alert = "";

        if ($this->input->server('REQUEST_METHOD') == 'POST')
        {
            foreach ($_POST as $key => $value)
            {
                if (!in_array($key, $this->permitted_post_parameter))
                {
                    break;
                }

                if (isset($config[$key]))
                {
                    $config[$key] = $value;
                }

                if (isset($config['type_mapping'][$key]))
                {
                    $config['type_mapping'][$key]['title'] = $value;
                    $config['type_mapping'][$key]['href'] = $types[$value];
                }

                if (isset($config['status_mapping'][$key]))
                {
                    $config['status_mapping'][$key]['title'] = $value;
                    $config['status_mapping'][$key]['href'] = $statuses[$value];
                }

                if (isset($config['role_mapping'][$key]))
                {
                    $config['role_mapping'][$key]['name'] = $value;
                    $config['role_mapping'][$key]['id'] = $roles[$value];
                }
            }

            file_put_contents($config_path, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
            $alert = '<div class="alert alert-success"> <strong>Success!</strong> Your settings have been saved!</div>';
        }

        $data = [
            'alert' => $alert,
            'config' => $config,
            'types' => $types,
            'statuses' => $statuses,
            'roles' => $roles,
        ];
        $this->load->view('configure', $data);
    }

    /**
     * Returns the open project work package type.
     *
     * Fetches the statuss from the OpenProject API.
     *
     * @return array with type => resource
     */
    private function __getTypes()
    {
        $types = [];

        $result = $this->rest->get('types');
        $types_objects = $result->_embedded->elements;

        foreach ($types_objects as $type_object)
        {
            $types[$type_object->_links->self->title] = $type_object->_links->self->href;
        }

        return $types;
    }

    /**
     * Returns the open project statuses.
     *
     * Fetches the statuses from the OpenProject API.
     *
     * @return array with status => resource
     */
    private function __getStatuses()
    {
        $result = $this->rest->get('statuses');
        $status_objects = $result->_embedded->elements;

        $statuses = [];

        foreach ($status_objects as $status_object)
        {
            $statuses[$status_object->_links->self->title] = $status_object->_links->self->href;
        }

        return $statuses;
    }

    /**
     * Returns the open project roles.
     *
     * Fetches the roles from the OpenProject database.
     *
     * @return array with role => id
     */
    private function __getRoles()
    {
        $query = $this->db->get_where('roles', ['assignable' => true]);

        $roles = [];

        foreach ($query->result() as $role)
        {
            $roles[$role->name] = $role->id;
        }

        return $roles;
    }
}

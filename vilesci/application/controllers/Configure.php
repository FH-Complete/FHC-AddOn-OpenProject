<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Configure extends FHCOP_Controller
{

    private $permitted_post_parameter = ['url', 'api_key', 'Arbeitspaket', 'Milestone', 'Task', 'Projektphase', 'new', 'closed'];

    /**
     * Configure constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function index()
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

        //TODO json_last_error() ?
        $config = json_decode(file_get_contents($config_path), true);


        // Fetch OpenProject work package types
        $result = $this->rest->get('types');
        $types_objects = $result->_embedded->elements;

        $types = [];

        foreach ($types_objects as $type_object)
        {
            $types[$type_object->_links->self->title] = $type_object->_links->self->href;
        }

        // Fetch OpenProject statuses
        $result = $this->rest->get('statuses');
        $status_objects = $result->_embedded->elements;

        $statuses = [];

        foreach ($status_objects as $status_object)
        {
            $statuses[$status_object->_links->self->title] = $status_object->_links->self->href;
        }


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
            }

            file_put_contents($config_path, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
            $alert = '<div class="alert alert-success"> <strong>Success!</strong> Your settings have been saved!</div>';
        }

        $data = [
            'alert' => $alert,
            'config' => $config,
            'types' => $types,
            'statuses' => $statuses,
        ];
        $this->load->view('configure', $data);
    }
}

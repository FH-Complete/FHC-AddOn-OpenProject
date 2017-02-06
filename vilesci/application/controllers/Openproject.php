<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Openproject extends CI_Controller
{

    /**
     * Openproject constructor.
     */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('openproject/user_model');
		$this->load->model('openproject/project_model');
		$this->load->model('openproject/workpackage_model');
	}

    /**
     *
     */
	public function index()
	{
	}

    /**
     * @param string $id UID of the user
     */
	public function user_exists($id)
	{
		$exists = $this->user_model->exists($id);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'exists' => $exists
			]));
	}

    /**
     * inserts project
     *
     * @param string $name Name of the project
     */
	public function insert_project($name)
	{
		$successful = $this->project_model->insert('test-2', 'Test 2', 'this is a second test project');
		$this->output->set_output("Hello World!");


		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'successful' => $successful
			]));
	}

    /**
     * returns the specified project
     *
     * @param string $id Name of the project
     */
	public function get_project($id)
	{
		$project = $this->rest->get('projects/'.$id);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($project));
	}

    /**
     * returns all work package types
     */
	public function get_types()
	{
		$project = $this->rest->get('types');

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($project));
	}

    /**
     * [testing] creates a work package
     */
	public function create_work_package()
	{
		$this->load->library('workpackage');

		$wp = new Workpackage();
		$wp->setSubject("test package from ci")
			->setDescription("this package is a test package")
			->setParent(72)
			->setStatus("new")
			->setResponsible("if16b074")
			->setPersonDays(5)
			->setType("Task")
			->setProjectID("34")
			->setStartDate("2017-01-01")
			->setDueDate("2017-01-20");


		/* $this->output->set_output($this->workpackage->create($wp)); */
		print_r($this->workpackage_model->create($wp));
	}

    /**
     * [testing] adds if16b043 as user to project 59
     */
	public function add_user()
	{
		$this->project_model->project_id = 59;
		$this->project_model->add_user('if16b043');
	}

    /**
     * [testing] sets parent of a task
     */
	public function set_parent()
	{
		$this->workpackage_model->set_parent(81, 84);
	}
}

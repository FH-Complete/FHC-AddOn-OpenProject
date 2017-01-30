<?php
defined('BASEPATH') || exit('No direct script access allowed');

/*
 * OpenProject Project
 */
class Project_model extends CI_Model
{

	public $project_id;

    /**
     * Project_model constructor.
     */
	public function __construct()
	{
		parent::__construct();
		$this->load->database('openproject');
		$this->load->model('openproject/user_model');
	}

    /**
     * Creates an openproject project in the database
     *
     * @param string $identifier identifier for the openproject project
     * @param string $name name of the openproject project
     * @param string $description description of the openproject project
     * @return int the id of the created openproject project
     */
	public function insert($identifier, $name, $description)
	{
		$this->db->select_max('rgt');
		$max_rgt = $this->db->get('projects')->row()->rgt ?: 0;

		$query = $this->db->get_where('projects', ['identifier' => $identifier]);

		if ($query->num_rows() > 0)
		{
			//Project already exists;
			return -1;
		}

		$this->db->set([
			'name' => $name,
			'description' => $description,
			'identifier' => $identifier,
			'status' => 1,
			'is_public' => false,
			'lft' => $max_rgt + 1,
			'rgt' => $max_rgt + 2,
		]);
		$this->db->set('created_on', 'now()');
		$this->db->set('updated_on', 'now()');
		$success = $this->db->insert('projects');

		if ($success)
		{
			$this->project_id = $this->db->insert_id();

			// all modules and types have to be enabled manually
			$this->__enable_modules($this->project_id);
			$this->__enable_all_types($this->project_id);
		}

		return $success ? $this->project_id : -1;
 	}

	/**
	 * Adds a project to a user.
	 *
	 * Will add a user to a project or do nothing if he is already added.
	 * Expects the attribute $this->project_id to be set.
	 */
	public function add_user($login, $admin = false)
	{
		//TODO add FHC <-> OP role mapping?
		//     update roles if user is already added?

		$user = $this->user_model->get($login);

		$role = $admin ? 'admin' : 'member';
		$role_id = $this->config->item('openproject')['role_mapping'][$role]['id'];

		$query = $this->db->get_where('members', [
			'user_id' => $user['id'],
			'project_id' => $this->project_id
		]);

		if ($query->num_rows() > 0)
		{
			// user is already added to the project
			return;
		}

		$this->db->set('user_id', $user['id']);
		$this->db->set('project_id', $this->project_id);
		$this->db->set('created_on', 'now()');

		$this->db->insert('members');

		$member_id = $this->db->insert_id();

		$this->db->insert('member_roles', [
			'member_id' => $member_id,
			'role_id' => $role_id
		]);
	}


	/**
	 * Enables the modules for a project
	 * The config is found in openproject.php
	 */
	private function __enable_modules($project_id)
	{
		$modules = $this->config->item('openproject')['default_modules'];

		// TODO insert_batch() ?
		foreach ($modules as $module)
		{
			$this->db->insert('enabled_modules', [
				'name' => $module,
				'project_id' => $project_id,
			]);
		}
	}

	/**
	 * Enables all available types of work packages for a project
	 */
	private function __enable_all_types($project_id)
	{
		$query = $this->db->get('types');

		foreach ($query->result() as $row)
		{
			$this->db->insert('projects_types', [
				'project_id' => $project_id,
				'type_id' => $row->id
			]);
		}
	}
}

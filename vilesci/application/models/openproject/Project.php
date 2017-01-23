<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * OpenProject Project
 */
class Project extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('openproject');
	}

	public function insert($name)
	{
		$this->db->select_max('rgt');
		$max_rgt = $this->db->get('projects')->row()->rgt ?: 0;

		$query = $this->db->get_where('projects', ['identifier' => $name]);

		if ($query->num_rows() > 0)
		{
			return false;
			//return "Project $name exists already";
		}

		$this->db->set([
			'name' => $name,
			'description' => '',
			'identifier' => $name,
			'status' => 1,
			'is_public' => false,
			'lft' => $max_rgt+1,
			'rgt' => $max_rgt+2,
		]);
		$this->db->set('created_on', 'now()');
		$this->db->set('updated_on', 'now()');
		$success = $this->db->insert('projects');

		if ($success)
		{
			$project_id = $this->db->insert_id();

			// all modules and types have to be enabled manually
			$this->enable_modules($project_id);
			$this->enable_all_types($project_id);
		}

		return $success;
	}


	/**
	 * Enables the modules for a project
	 * The config is found in openproject.php
	 */
	private function enable_modules($project_id)
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
	private function enable_all_types($project_id)
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

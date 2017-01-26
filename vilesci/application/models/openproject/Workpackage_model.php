<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * OpenProject User
 */
class Workpackage_model extends FHCOP_Model {

	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Create a work package.
	 *
	 * Creates a work package via the Open Project API.
	 *
	 * @param Workpackage $wp
	 * @return integer|null The id of the created work package or null if it fails.
	 */
	public function create($wp) {

		$data = [
			'subject' => $wp->subject,
			'description' => [
				'raw' => $wp->description,
			],
			'_links' => [
				'type' => [
					'href' => $wp->typeResource
				],
				'responsible' => [
					'href' => $wp->responsibleResource
				],
				'status' => [
					'href' => $wp->statusResource
				],
				'parent' => [
					'href' => $wp->parentResource
				],
			],
			'startDate' => $wp->startDate,
			'dueDate' => $wp->dueDate,
			'estimatedTime' => $wp->estimatedTime,
		];

		$result = $this->rest->postJSON("projects/$wp->projectID/work_packages", $data);

		return property_exists($result, 'id') ? $result->id : null;
	}

}

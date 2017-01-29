<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Workpackage {

	private $subject;		//*
	private $description;	//*
	private $type;			//*
	private $typeResource;	//*
	private $projectID;		//*
	private $status;		//*
	private $statusResource;//*
	private $startDate;		//*
	private $dueDate;		//*
	private $costObject;
	private $responsible;	//*
	private $responsibleResource; //*
	private $estimatedTime;	//*
	private $parent;
	private $parentResource;

	private $CI;
	const PERSON_DAYS_IN_HOURS = 8;


	public function __construct()
	{
		$this->CI = &get_instance();

		$this->CI->load->model('openproject/user_model');
	}

	public function __get($property)
	{
		if (property_exists($this, $property))
		{
			return $this->$property;
		}
	}

	public function setSubject($subject)
	{
		$this->subject = $subject;
		return $this;
	}

	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	public function setType($type)
	{
		$this->type = $type;
		//TODO check existence
		$this->typeResource = $this->CI->config->item('openproject')['type_mapping'][$type]['href'];
		return $this;
	}

	public function setProjectID($projectID)
	{
		$this->projectID = $projectID;
		return $this;
	}

	/*
	 * Sets a status and statusResource.
	 * Valid statuses: ["new", "closed"]
	 */
	public function setStatus($status)
	{
		$this->status = $status;
		$this->statusResource = $this->CI->config->item('openproject')['status_mapping'][$status]['href'];

		return $this;
	}

	/**
	 * Expects an ISO 8601 date, e.g. “2014-05-21”
	 */
	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
		return $this;
	}

	/**
	 * Expects an ISO 8601 date, e.g. “2014-05-21”
	 */
	public function setDueDate($dueDate)
	{
		$this->dueDate = $dueDate;
		return $this;
	}

	public function setCostObject($costObject)
	{
		$this->costObject = $costObject;
		return $this;
	}

	public function setResponsible($id)
	{
		if (is_null($id))
		{
			return $this;
		}
		$this->responsible = $id;

		$user = $this->CI->user_model->get($id);

		if (!is_null($user))
		{
			$this->responsibleResource = $user['resource'];
		}

		return $this;
	}

	/**
	 * Expects an ISO 8601 duration, e.g. “P1DT18H”
	 */
	public function setEstimatedTime($duration)
	{
		$this->estimatedTime = $duration;
		return $this;
	}

	/**
	 * Sets the estimatedTime.
	 * Uses the constant PERSON_DAYS_IN_HOURS
	 */
	public function setPersonDays($personDays)
	{
		$hours = $personDays * $this->PERSON_DAYS_IN_HOURS;
		$this->setEstimatedTime("PT{$hours}H");
		return $this;
	}

	public function setParent($id)
	{
		$api_path = $this->CI->config->item('openproject')['api_path'];

		$this->parent = $id;
		$this->parentResource = "{$api_path}work_packages/{$id}";

		return $this;
	}
}

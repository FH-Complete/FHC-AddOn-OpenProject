<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * OpenProject User
 */
class User_model extends FHCOP_Model {

	private $cache = [];

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Returns a user.
	 *
	 * Fetches a user from the OpenProject API via a login.
	 *
	 * @param string $id eg if16b000
	 * @return array|null Returns 'id', 'name' and 'resource' in an array.
	 */
	public function get($id)
	{
		if (!array_key_exists($id, $this->cache))
		{
			$filter = [
				'login' => [
					'operator' => '=',
					'values' => [$id]
				]
			];

			$result = $this->rest->get('users?filters=[' . json_encode($filter) . ']');

			if ($result->count < 1)
			{
				return null;
			}

			$this->cache[$id] = [
				'id' => $result->_embedded->elements[0]->login,
				'name' => $result->_embedded->elements[0]->name,
				'resource' => $result->_embedded->elements[0]->_links->self->href,
			];
		}

		return $this->cache[$id];
	}

	/**
	 * Checks whether a user exists.
	 *
	 * Checks via the OpenProject API if a user with a given login exists.
	 *
	 * @param string $id eg if16b000
	 * @return boolean
	 */
	public function exists($id)
	{
		$user = $this->get($id);
		return !!$user;
	}

}

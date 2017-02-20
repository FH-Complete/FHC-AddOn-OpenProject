<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * OpenProject User
 */
class User_model extends FHCOP_Model
{

    private $cache = [];

    /**
     * User_model constructor.
     */
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

            $result = $this->rest->get('users?filters=['.json_encode($filter).']');

            if ($result->count < 1)
            {
                return null;
            }

            $user = $result->_embedded->elements[0];

            $this->cache[$id] = [
            'id' => $user->id,
            'login' => $user->login,
            'name' => $user->name,
            'resource' => $user->_links->self->href,
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
    * @return bool
    */
    public function exists($id)
    {
        $user = $this->get($id);
        return (bool)$user;
    }

    /**
     * Checks whether a user doesn't exists.
     *
     * Checks via the OpenProject API if a user with a given login exists.
     *
     * @param string $id eg if16b000
     * @return bool
     */
    public function not_exists($id)
    {
        return !$this->exists($id);
    }
}

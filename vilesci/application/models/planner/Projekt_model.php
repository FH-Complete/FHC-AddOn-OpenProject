<?php
class Projekt_model extends FHCOP_Model
{
    const CLEANUP = true;
    private $fhc; // Database Object

    /**
     * Projekt_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->fhc = $this->load->database('fhcomplete', true);
    }

    /**
     * @param string $id 'projekt_kurzbz' of the planner project
     * @return array returns the complete project
     */
    public function load($id)
    {
        $projekt = $this->__fetch("projekt", "projekt_kurzbz", $id);
        if (count($projekt) == 0)
        {
            return $this->__error("No project found with this projekt_kurzbz!");
        }
        $projekt = $projekt[0];
        $this->__fetchRes($projekt);

        $projekt->projektphasen = $this->__fetch("projektphase", "projekt_kurzbz", $id);
        foreach ($projekt->projektphasen as $phase)
        {
            $this->__fetchRes($phase);
            $phase->projekttasks = $this->__fetch("projekttask", "projektphase_id", $phase->projektphase_id);
            foreach ($phase->projekttasks as $task)
                $this->__fetchRes($task);
        }

        $projekt->projektressourcen = $this->__fetch("projekt_ressource", "projekt_kurzbz", $id);
        foreach ($projekt->projektressourcen as $res)
        {
            $this->__fetchRes($res);
        }

        return $projekt;
    }

    /**
     * @param string $table planner database table without the 'fue.tbl_' prefix
     * @param string $attr attribute for the where clause
     * @param string $val value for the where clause
     * @return object returns an object from the database
     */
    private function __fetch($table, $attr, $val)
    {
        return $this->__cleanup($this->fhc->get_where("fue.tbl_".$table, array($attr => $val))->result());
    }

    /**
     * Fetches the UID of mitarbeiter or student from ressource and replaces the ressource_id field with it
     *
     * @param object $obj object to fetch the ressource_id from
     * @return array returns an error message on failure
     */
    private function __fetchRes(&$obj)
    {
        if (is_object($obj) && $obj->ressource_id != null)
        {
            $res = $this->__fetch("ressource", "ressource_id", $obj->ressource_id);
            if ($res != null && is_array($res))
            {
                $res = $res[0];
                $obj->ressource_id = $res->mitarbeiter_uid == null ? $res->student_uid == null ? null : $res->student_uid : $res->mitarbeiter_uid;
            }
            else
            {
                return $this->__error("Error fetching Ressource with ressource_id ".$obj->ressource_id."! Result was expected.");
            }
        }
    }

    /**
     * Removes unnecessary fields from the results
     *
     * @param object $objs an object or an array of objects to be cleaned
     * @return mixed returns the cleaned object
     */
    private function __cleanup($objs)
    {
        if ($this::CLEANUP === true)
        {
            foreach ($objs as $obj)
            {
                unset($obj->insertamum);
                unset($obj->insertvon);
                unset($obj->updateamum);
                unset($obj->updatevon);
                unset($obj->scrumsprint_id);
                unset($obj->mantis_id);
            }
        }
        return $objs;
    }

    /**
     * Creates an error message to return
     *
     * @param string $desc Error description
     * @return array Returns the error as associative array
     */
    private function __error($desc = "Error fetching project (no message given)!")
    {
        return ["error" => $desc];
    }
}

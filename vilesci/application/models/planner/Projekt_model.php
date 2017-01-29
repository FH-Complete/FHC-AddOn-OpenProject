<?php
class Projekt_model extends FHCOP_Model
{
    const CLEANUP = true;
    private $fhc; // Database Object

    public function __construct()
    {
        parent::__construct();
        $this->fhc = $this->load->database('fhcomplete', true);
    }
    public function load($id)
    {
        $projekt = $this->fetch("projekt", "projekt_kurzbz", $id);
        if(count($projekt) == 0)
            return $this->error("No project found with this projekt_kurzbz!");
        $projekt = $projekt[0];
        $this->fetchRes($projekt);

        $projekt->projektphasen = $this->fetch("projektphase", "projekt_kurzbz", $id);
        foreach($projekt->projektphasen as $phase)
        {
            $this->fetchRes($phase);
            $phase->projekttasks = $this->fetch("projekttask", "projektphase_id", $phase->projektphase_id);
            foreach($phase->projekttasks as $task)
                $this->fetchRes($task);
        }

        $projekt->projektressourcen = $this->fetch("projekt_ressource", "projekt_kurzbz", $id);
        foreach($projekt->projektressourcen as $res)
            $this->fetchRes($res);

       return $projekt;
    }

    private function fetch($table, $attr, $val)
    {
        return $this->cleanup($this->fhc->get_where("fue.tbl_".$table, array($attr => $val))->result());
    }

    private function fetchRes(&$obj)
    {
        if(is_object($obj) && $obj->ressource_id != null)
        {
            $res = $this->fetch("ressource", "ressource_id", $obj->ressource_id);
            if($res != null && is_array($res))
            {
                $res = $res[0];
                $obj->ressource_id = $res->mitarbeiter_uid == null ? $res->student_uid == null ? null : $res->student_uid : $res->mitarbeiter_uid;
            }
            else
                return $this->errror("Error fetching Ressource with ressource_id ".$obj->ressource_id."! Result was expected.");
        }
    }

    private function cleanup($objs)
    {
        if($this::CLEANUP === true)
        {
            foreach($objs as $obj)
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

    private function error($desc = "Error fetching project (no message given)!")
    {
        return ["error" => $desc];
    }
}
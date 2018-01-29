<?php
require_once('config.php');

class Statuses {
	private $id;
	private $name;


	//------------------------ GETTERS ------------------------
	public function getID(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}


	public function toArray()
    {
        //return an array version of the Report item
        return array(
            'id' => $this->id,
            'name' => $this->name,
        
        );
    }

	//------------------------ SETTERS ------------------------

	public function setID($id){
		$this->id = $id;
	}

	public function setName($name){
		$this->name = $name;
	}

	

	
	/*
	*	given a status's id, looks up and sets the status's local vars
	* 	@param $id | int report ID in DB
	*	@return bool | only if lookup fails (not in DB)
	*/
	public function fetch($id){
		$db = new Database();
		$sql = "SELECT * FROM statuses WHERE id=? ORDER BY id";
		$sql = $db->prepareQuery($sql, $id);

		$results = $db->select($sql);

		if(count($results) != 0){
			$this->setID($results[0]['id']);
			$this->setName($results[0]['name']);
		
		} else return false;
	}

	/*
	* 	deletes the current Status from the DB using this object's id
	*/
	public function delete(){
		$db = new Database();
		$sql = "DELETE FROM statuses WHERE id=?";
		$sql = $db->prepareQuery($sql, $this->id);
		$db->query($sql);
	}
}

?>
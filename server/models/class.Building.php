<?php
require_once('config.php');

class Building {
	private $id;
	private $name;


	//------------------------ GETTERS ------------------------
	public function getID(){
		return $this->id;
	}

	public function getName(){
		return $this->buildingName;
	}

	public function toArray()
    {
        //return an array version of the building item
        return array(
            'id' => $this->id,
            'name' => $this->buildingName,
        );
    }

	//------------------------ SETTERS ------------------------

	public function setID($id){
		$this->id = $id;
	}

	public function setName($name){
		$this->buildingName = $name;
	}


	//------------------------ DB METHODS ------------------------

	/*
	*	takes the building object and saves to DB.

	*	@case1 new building | add new record to DB
	*	
	*	@return boolean | false on fail to get id from DB in 
	*/
	public function save(){
		$db = new Database();

		$reportInDB = isset($this->id);

		if($reportInDB === false){ //new comment
			$sql = "INSERT INTO `buildings`(`buildingName`) VALUES(?)";
			$sql = $db->prepareQuery($sql, $this->name);
			$db->query($sql);
		}
	}

	/*
	*	given a buildings id, looks up and sets the building local vars
	* 	@param $id | int building ID in DB
	*	@return bool | only if lookup fails (not in DB)
	*/
	public function fetch($id){
		$db = new Database();
		$sql = "SELECT * FROM buildings WHERE id=? ORDER BY id";
		$sql = $db->prepareQuery($sql, $id);

		$results = $db->select($sql);

		if(count($results) != 0){
			$this->setID($results[0]['id']);
			$this->setName($results[0]['buildingName']);	
		} else return false;
	}

public function getAtID($id){
$db = new Database();
		$sql = "SELECT * FROM buildings WHERE id=? ORDER BY id";
		$sql = $db->prepareQuery($sql, $id);

		$results = $db->select($sql);
		return $results;
}

	public function retrieve(){
	$db = new Database();
		$sql = "SELECT * FROM buildings ORDER BY buildingName";
		$sql = $db->prepareQuery($sql);

		$results = $db->select($sql);
	return $results;
	}
	/*
	* 	deletes the current building from the DB using this object's id
	*/
	public function delete(){
		$db = new Database();
		$sql = "DELETE FROM buildings WHERE id=?";
		$sql = $db->prepareQuery($sql, $this->id);
		$db->query($sql);
	}
	
}

?>
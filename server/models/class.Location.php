<?php
require_once('config.php');

class Location {
	private $id;
	private $buildingID;
	private $room = null;

	//------------------------ GETTERS ------------------------
	public function getID(){
		return $this->id;
	}

	public function getBuildingID(){
		return $this->buildingID;
	}

	public function getBuildingName(){
		$db = new Database();
		$sql = 'SELECT buildingName FROM buildings WHERE id=?';
		$sql = $db->prepareQuery($sql, $this->buildingID);
		$results = $db->select($sql);
		return $results[0]['buildingName']; 
	}

	public function getRoom(){
		return $this->room;
	}

	//------------------------ SETTERS ------------------------
	public function setID($id){
		$this->id = $id;
	}

	public function setBuildingID($buildingID){
		$this->buildingID = $buildingID;
	}

	public function setRoom($room){
		$this->room = $room;
	}

	//------------------------ DB METHODS ------------------------

	/*
	*	saves a new location in the DB if needed
	*
	*	@case1: location not in DB | add new record
	*	@case2: location in db | set local object to correct ID
	*/
	public function save(){
		$db = new Database();

		//check to see if this location already exists
		$sql = "SELECT * FROM locations WHERE buildingID=? AND room=?";
		$sql = $db->prepareQuery($sql, $this->buildingID, $this->room);
		$results = $db->select($sql);

		//location doesnt exist
		if(count($results) == 0){
			//insert new location
			$sql = "INSERT INTO locations(buildingID, room) VALUES(?,?)";
			$sql = $db->prepareQuery($sql, $this->buildingID, $this->room);
			$results = $db->query($sql);

			//get id of location
			if(is_null($this->room) || empty($this->room)){
				$sql = "SELECT * FROM locations WHERE buildingID=? AND room IS NULL";	
				$sql = $db->prepareQuery($sql, $this->buildingID);
			} else {
				$sql = "SELECT * FROM locations WHERE buildingID=? AND room=?";	
				$sql = $db->prepareQuery($sql, $this->buildingID, $this->room);
			}
			
			$results = $db->select($sql);
			if(isset($results[0]['id'])){
				$this->setID($results[0]['id']);	
			}
		} else { //location exists
			if(is_null($this->id)){ //get id of existing location
				//get id from DB
				if(isset($results[0]['id'])){
					$this->setID($results[0]['id']);	
				} else return false;
			}
		}
	}

	public function fetch($id){
		$db = new Database();
		$sql = "SELECT * FROM locations WHERE id=?";
		$sql = $db->prepareQuery($sql, $id);

		$results = $db->select($sql);

		if(count($results) != 0){
			$this->setID($id);
			$this->setBuildingID($results[0]['buildingID']);
			$this->setRoom($results[0]['room']);
		} else return false;
	}
	public function getAtID($id){
		$db = new Database();
		$sql = "SELECT * FROM locations WHERE id=?";
		$sql = $db->prepareQuery($sql, $id);

		$results = $db->select($sql);
	return $results;
	}
	/*
	*	finds a building's id for usage in the person object
	*	doesn't create building if doesn't exist. 
	*	Building list Supposed to be static?
	*
	*	@param $building | string building to lookup id for
	*
	*	@return int | id if found, bool (false) otherwise
	*/
	public static function lookupBuildingID($building){
		$db = new Database();
		$sql = "SELECT id FROM buildings WHERE buildingName=?";
		$sql = $db->prepareQuery($sql, $building);

		$results = $db->select($sql);
		if(isset($results[0]['id'])){
			return $results[0]['id'];
		} else return false;
	}

	/*
	*	check to see if a location exists on the DB (prevents accidental duplicates)
	*	
	*	@param $buildingID | int id of building
	*		$room | int room number in building (opt null)
	*
	*	@return bool | false if location doesn't exist,
	*		int | location id if does exist
	*/
	public static function locationExists($buildingID, $room){
		$db = new Database();
		$sql = "SELECT * FROM locations WHERE buildingID=? AND room=?";
		$sql = $db->prepareQuery($sql, $buildingID, $room);
		$results = $db->select($sql);
		if(isset($results[0]['id'])){
			return $results[0]['id'];	
		} else return false;
	}

	/*
	* 	deletes the current Location from the DB using this object's id
	*/
	public function delete(){
		$db = new Database();
		$sql = "DELETE FROM locations WHERE id=?";
		$sql = $db->prepareQuery($sql, $this->id);
		$db->query($sql);
	}
}

?>
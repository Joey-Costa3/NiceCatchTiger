<?php
//require_once('Path.php');
//require_once(Path::models() . 'config.php');
require_once('config.php');

class ReportKind {
	private $id;
	private $reportKind;
	private $default;


	//------------------------ GETTERS ------------------------
	public function getID(){
		return $this->id;
	}

	public function getReportKind(){
		return $this->reportKind;
	}

	public function getDefault()
	{
	return $this->default;
	}

	//------------------------ SETTERS ------------------------

	public function setID($id){
		$this->id = $id;
	}

	public function setReportKind($reportKind){
		$this->reportKind = $reportKind;
	}

public function setDefault($default){
		$this->default = $default;
	}
	//------------------------ DB METHODS ------------------------



	/*
	*	given a person's id, looks up and sets the person's local vars
	* 	@param $id | int person ID in DB
	*	@return bool | only if lookup fails (not in DB)
	*/
	public function fetch($id){
		$db = new Database();
		$sql = "SELECT * FROM reportKinds WHERE id=?";
		$sql = $db->prepareQuery($sql, $id);

		$results = $db->select($sql);

		if(count($results) != 0){
			$this->setID($id);
			$this->setReportKind($results[0]['reportKind']);
			$this->setDefault($results[0]['default']);	
		} else return false;
	}
	/*
	*	given a person's id
	* 	@param $id | int person ID in DB
	*	@return ReportKind
	*/
	public function getAtID($id)
	{
		$db = new Database();
		$sql = "SELECT * FROM reportKinds WHERE id=?";
		$sql = $db->prepareQuery($sql, $id);

		$results = $db->select($sql);
	return $results;
	}
public function retrieve(){
	$db = new Database();
		$sql = "SELECT * FROM reportKinds WHERE `default` = 1 ORDER BY reportKind";
		$sql = $db->prepareQuery($sql);

		$results = $db->select($sql);
	return $results;
	}
	
	/*
	* 	deletes the current Person from the DB using this object's id
	*/
	public function delete(){
		$db = new Database();
		$sql = "DELETE FROM reportKinds WHERE id=?";
		$sql = $db->prepareQuery($sql, $this->id);
		$db->query($sql);
	}
}


?>
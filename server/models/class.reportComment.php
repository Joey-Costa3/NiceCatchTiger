<?php
require_once('config.php');

class ReportComment {
	private $id;
	private $reportID;
	private $commentID;


	//------------------------ GETTERS ------------------------
	public function getID(){
		return $this->id;
	}

	public function getReportID(){
		return $this->reportID;
	}

	public function getCommentID(){
		return $this->commentID;
	}



	public function toArray()
    {
        //return an array version of the Report item
        return array(
            'id' => $this->id,
            'reportID' => $this->reportID,
            'commentID' => $this->commentID
         
        );
    }

	//------------------------ SETTERS ------------------------

	public function setID($id){
		$this->id = $id;
	}

	public function setReportID($reportID){
		$this->reportID = $reportID;
	}

	public function setCommentID($commentID){
		$this->commentID = $commentID;
	}




	//------------------------ DB METHODS ------------------------

	/*
	*	takes the reportComment object and saves to DB.

	*	@case1 new reportComment | add new record to DB
	*	
	*/
	public function save(){
		$db = new Database();

		$reportInDB = isset($this->id);

		if($reportInDB === false){ //new comment
			$sql = "INSERT INTO `reportComment`(`reportID`, `commentID`) VALUES(?,?)";
			$sql = $db->prepareQuery($sql, $this->reportID, $this->commentID);
			$db->query($sql);
		}
	}

	/*
	*	given a reportComments id, looks up and sets the reportComment local vars
	* 	@param $id | int reportComment ID in DB
	*	@return bool | only if lookup fails (not in DB)
	*/
	public function fetch($id){
		$db = new Database();
		$sql = "SELECT * FROM reportComment WHERE id=? ORDER BY id";
		$sql = $db->prepareQuery($sql, $id);

		$results = $db->select($sql);

		if(count($results) != 0){
			$this->setID($results[0]['id']);
			$this->setReportID($results[0]['reportID']);
			$this->setCommentID($results[0]['comment']);
		} else return false;
	}

	
	/*
	* 	deletes the current comment from the DB using this object's id
	*/
	public function delete(){
		$db = new Database();
		$sql = "DELETE FROM reportComment WHERE id=?";
		$sql = $db->prepareQuery($sql, $this->id);
		$db->query($sql);
	}
}

?>
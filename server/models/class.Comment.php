<?php
require_once('config.php');

class Comment {
	private $id;
	private $name;
	private $comment;
	private $dateSent;


	//------------------------ GETTERS ------------------------
	public function getID(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}

	public function getComment(){
		return $this->comment;
	}

	public function getDateSent(){
		return $this->dateSend;
	}


	public function toArray()
    {
        //return an array version of the Report item
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'comment' => $this->comment,
            'dateSent' => $this->dateSent,
         
        );
    }

	//------------------------ SETTERS ------------------------

	public function setID($id){
		$this->id = $id;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function setComment($comment){
		$this->comment = $comment;
	}

	public function setDateSend($dateSent){
		$this->dateSent = $dateSent;
	}


	//------------------------ DB METHODS ------------------------

	/*
	*	takes the comment object and saves to DB.

	*	@case1 new comment | add new record to DB
	*	@case2 old comment with ID | update record in DB
	*	
	*	@return boolean | false on fail to get id from DB in case 3
	*/
	public function save(){
		$db = new Database();

		$reportInDB = isset($this->id);

		if($reportInDB === false){ //new comment
			$sql = "INSERT INTO `comments`(`name`, `comment`, `dateSent`) VALUES(?,?,?)";
			$sql = $db->prepareQuery($sql, $this->name, $this->comment, $this->dateSent);
			$db->query($sql);
		}
	}

	/*
	*	given a comments id, looks up and sets the comments local vars
	* 	@param $id | int comment ID in DB
	*	@return bool | only if lookup fails (not in DB)
	*/
	public function fetch($id){
		$db = new Database();
		$sql = "SELECT * FROM comments WHERE id=? ORDER BY id";
		$sql = $db->prepareQuery($sql, $id);

		$results = $db->select($sql);

		if(count($results) != 0){
			$this->setID($results[0]['id']);
			$this->setName($results[0]['name']);
			$this->setComment($results[0]['comment']);
			$this->setdateTime($results[0]['dateTime']);	
		} else return false;
	}

	
	/*
	* 	deletes the current comment from the DB using this object's id
	*/
	public function delete(){
		$db = new Database();
		$sql = "DELETE FROM comments WHERE id=?";
		$sql = $db->prepareQuery($sql, $this->id);
		$db->query($sql);
	}
}

?>
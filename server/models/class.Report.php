<?php
require_once('config.php');

class Report {
	private $id;
	private $description;
	private $involvementKindID;
	private $reportKindID;
	private $locationID;
	private $personID;
	private $departmentID;
	private $reportTime;
	private $statusID;
	private $actionTaken;
	private $photoPath;
	private $incidentTime;
	private $ipAddress;
	private $isIOS;

	//------------------------ GETTERS ------------------------
	public function getID(){
		return $this->id;
	}

	public function getDescription(){
		return $this->description;
	}

	public function getInvolvementKindID(){
		return $this->involvementKindID;
	}

	public function getReportKindID(){
		return $this->reportKindID;
	}

	public function getLocationID(){
		return $this->locationID;
	}

	public function getPersonID(){
		return $this->personID;
	}

	public function getDepartmentID(){
		return $this->departmentID;
	}

	public function getReportTime(){
		return $this->reportTime;
	}

	public function getStatusID(){
		return $this->statusID;
	}

	public function getActionTaken(){
		return $this->actionTaken;
	}

	public function getPhotoPath(){
		return $this->photoPath;
	}
	
	public function getIncidentTime()
	{
	
	return $this->incidentTime;
	}
	public function getipAddress()
	{
		return $this->ipAddress;
	}
	public function getIsIOS()
	{
		return $this->isIOS;
	}

	public function toArray()
    {
        //return an array version of the Report item
        return array(
            'id' => $this->id,
            'description' => $this->description,
            'involvementKindID' => $this->involvementKindID,
            'reportKindID' => $this->reportKindID,
            'locationID' => $this->locationID,
            'personID' => $this->personID,
            'departmentID' => $this->departmentID,
            'reportTime' => $this->reportTime,
            'statusID' => $this->statusID,
            'actionTaken' => $this->actionTaken,
            'photoPath' => $this->photoPath,
            'incidentTime' => $this->incidentTime,
            'ipAddress' => $this->ipAddress,
            'isIOS' => $this->isIOS
        );
    }

	//------------------------ SETTERS ------------------------

// Function to get the client IP address
public function get_ip() {
    $ip_keys = array( 'REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED');
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                // trim for safety measures
                $ip = trim($ip);
                // attempt to validate IP
                if ($this->validate_ip($ip)) {
                    return $ip;
                }
            }
        }
    }
    $toReturn = '';
    $toReturn = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
    return $toReturn;
}
/**
 * Ensures an ip address is both a valid IP and does not fall within
 * a private network range.
 */
public function validate_ip($ip)
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return false;
    }
    return true;
}
	public function setID($id){
		$this->id = $id;
	}

	public function setDescription($description){
		$this->description = $description;
	}

	public function setInvolvementKindID($involvementKindID){
		$this->involvementKindID = $involvementKindID;
	}

	public function setReportKindID($reportKindID){
		$this->reportKindID = $reportKindID;
	}

	public function setLocationID($locationID){
		$this->locationID = $locationID;
	}

	public function setPersonID($personID){
		$this->personID = $personID;
	}

	public function setDepartmentID($departmentID){
		$this->departmentID = $departmentID;
	}

	public function setReportTime($reportTime){
		$this->reportTime = $reportTime;
	}

	public function setStatusID($statusID){
		$this->statusID = $statusID;
	}

	public function setActionTaken($actionTaken){
		$this->actionTaken = $actionTaken;
	}

	public function setPhotoPath($photoPath){
		$this->photoPath = $photoPath;
	}
	public function setIncidentTime($incidentTime){
		$this->incidentTime = $incidentTime;
	}
	public function setipAddress($ipAddress)
	{
		$this->ipAddress = $ipAddress;
	}
	public function setIsIOS($isIOS)
	{
		$this->isIOS = $isIOS;
	}
	

	//------------------------ DB METHODS ------------------------

	/*
	*	takes the report object and saves to DB.

	*	@case1 new report | add new record to DB
	*	@case2 old report with ID | update record in DB
	*	@case3 old report with no ID | look up id from person/time, set local, update record in DB
	*	
	*	@return boolean | false on fail to get id from DB in case 3
	*/
	public function save(){
		$db = new Database();

		$reportInDB = isset($this->id);

		if($reportInDB === false){ //new report
			$sql = "INSERT INTO `reports`(`description`, `involvementKindID`, `reportKindID`, `locationID`, `personID`, `departmentID`, `reportTime`,`statusID`,`actionTaken`, `photoPath`, `incidentTime`, `ipAddress`, `isIOS`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$sql = $db->prepareQuery($sql, $this->description, $this->involvementKindID, $this->reportKindID, $this->locationID, $this->personID, $this->departmentID, $this->reportTime, $this->statusID, $this->actionTaken, $this->photoPath, $this->incidentTime, $this->get_ip(), $this->isIOS);
 			$db->query($sql);
			$emailParameters = 
			[$this->description, //0 
			$this->involvementKindID, // 1
			$this->reportKindID, //2
			$this->locationID, //3
			$this->personID,  //4
			$this->departmentID, //5
			$this->reportTime, //6
			$this->statusID, //7
			$this->actionTaken, //8
			$this->photoPath, //9
			$this->incidentTime, //10
			$this->get_ip(), //11
			$this->isIOS]; //12
			
			$this->sendEmail($emailParameters);
			//get id of new Report
			$reportInDB = Report::reportExists($this->personID, $this->reportTime);
			//echo "The reportID = " . $reportInDB . "\n";
			if($reportInDB != false){
				$this->id = $reportInDB;
			} else return false;
		} else { //old report
			if(is_null($this->id)){ //old report, new object. no local id yet
				$this->id = $reportInDB;
			}

			$sql = "UPDATE reports SET `description`=?, `involvementKindID`=?, `reportKindID`=?, `locationID`=?, `personID`=?, `departmentID`=?, `reportTime`=?, `statusID`=?, `actionTaken`=?, `photoPath`=?, `incidentTime`=?, `ipAddress`=?, `isIOS`=? WHERE id=?";
			$sql = $db->prepareQuery($sql, $this->description, $this->involvementKindID, $this->reportKindID, $this->locationID, $this->personID, $this->departmentID, $this->reportTime, $this->statusID, $this->actionTaken, $this->photoPath, $this->incidentTime,$this->get_ip(),$this->isIOS, $this->id);
			$db->query($sql);
		}
	}
	/* 
	* send the report to the designated email
	* @return bool | only if the email was delivered (may not be received just was delivered) 
	*/
	public function sendEmail($reportData){
		$toWhom = 'jacosta@g.clemson.edu';
		$toResearchSafety ='researchsafety@g.clemson.edu';
		$subject = 'Nice Catch Tiger Report';
		
		$device = 'iOS';
		if ($reportData[12])
		{
		$device = 'iOS';
		}else{
		$device = 'Android';
		}
		// Data types
		$involvementKind = new InvolvementKind();
		$reportKind = new ReportKind();
		$building = new Building();
		$department = new Department();
		$person = new Person();
		$status = new Statuses();
		$location = new Location();
		$personKind = new PersonKind();
		
		// ID's from the new report
		$involvementKindID = $reportData[1];
		$reportKindID = $reportData[2];
		$locationID = $reportData[3];
		$departmentID = $reportData[5];
		$personID = $reportData[4];
		$statusID = $reportData[7];
		
		// value's from ID's
		$reportType = $reportKind->getAtID($reportKindID);
		$reportKind = $reportType[0]['reportKind'];
		
		$person = $person->getAtID($personID);
		$personName = $person[0]['name'];
		$personUsername = $person[0]['username'];
		$personPhone = $person[0]['phone'];
		$personKindID = $person[0]['personKindID'];
		
		$personKind = $personKind->getAtID($personKindID);
		$personKindString = $personKind[0]['personKind'];
		
		$location = $location->getAtID($locationID);
		$locationRoom = $location[0]['room'];
		$buildingID = $location[0]['buildingID'];
		
		$building = $building->getAtID($buildingID);
		$buildingName = $building[0]['buildingName'];
		
		$department = $department->getAtID($departmentID);
		$departmentName = $department[0]['departmentName'];
		
		$involvementKind = $involvementKind->getAtID($involvementKindID);
		$involvementKindString = $involvementKind[0]['involvementKind'];
		
		$message = '
		<html>
			<head>
				<title>A New report was submitted</title>
			</head>
			<body>
				<table>
				<!--
					<tr>
						<th>A New Report Was Submitted!</th>
					</tr>
				-->
					<tr>
						<td>Report: '.$reportKind.' </td>
					</tr>
					<tr>
						<td>Involvement: '.$involvementKindString.'</td>
					</tr>
					
					<tr>
						<td><br></td> <!-- ADD AN EMPTY SPACE BETWEEN CATEGRORIES-->
					</tr>
					
					<tr>
						<td>Building: '.$buildingName.'</td>
					</tr>
					<tr>
						<td>Room: '.$locationRoom.'</td>
					</tr>
					<tr>
						<td>Department: '.$departmentName.'</td>
					</tr>
					
					<tr>
						<td><br></td> <!-- ADD AN EMPTY SPACE BETWEEN CATEGRORIES-->
					</tr>
					
					<tr>
						<td>Person: '.$personName.'</td>
					</tr>
					<tr>
						<td>Username: '.$personUsername .' ('
						.$personKindString.')</td>
					</tr>
					
					<tr>
						<td><br></td> <!-- ADD AN EMPTY SPACE BETWEEN CATEGRORIES-->
					</tr>
					
					<tr>
						<td>Report Time: '.date_format(date_create($reportData[6]), 'm-d-Y g:i A').'</td>
					</tr>
					<tr>
						<td>Incident Time: '.date_format(date_create($reportData[10]), 'm-d-Y g:i A').'</td>
					</tr>
					
					<tr>
						<td><br></td> <!-- ADD AN EMPTY SPACE BETWEEN CATEGRORIES-->
					</tr>
					
					<tr>
						<td>Description: '.$reportData[0].'</td>
					</tr>
					<tr>
						<td>'.$device.'</td>
					</tr>
					<!-- DO NOT DISPLAY THIS INFORMATION 
					<tr>
						<td>Status: '.$statusID.'</td>
					</tr>
					<tr>
						<td>Device Type: '.$device.'</td>
					</tr>
					-->
		
				</table>
			</body>
		</html>
		';
		
		$headers ='MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: researchsafety@clemson.edu' . "\r\n";
		// if(!mail($toWhom, $subject, $message, $headers))
// 			echo'Failed sending email';
		mail($toResearchSafety, $subject, $message, $headers);
		//mail($toWhom, $subject, $message, $headers);

	}
	/*
	*	given a report's id, looks up and sets the report's local vars
	* 	@param $id | int report ID in DB
	*	@return bool | only if lookup fails (not in DB)
	*/
	public function fetch($id){
		$db = new Database();
		$sql = "SELECT * FROM reports WHERE id=? ORDER BY id";
		$sql = $db->prepareQuery($sql, $id);

		$results = $db->select($sql);

		if(count($results) != 0){
			$this->setID($results[0]['id']);
			$this->setDescription($results[0]['description']);
			$this->setInvolvementKindID($results[0]['involvementKindID']);
			$this->setReportKindID($results[0]['reportKindID']);
			$this->setLocationID($results[0]['locationID']);
			$this->setPersonID($results[0]['personID']);
			$this->setDepartmentID($results[0]['departmentID']);
			$this->setReportTime($results[0]['reportTime']);
			$this->setStatusID($results[0]['statusID']);
			$this->setActionTaken($results[0]['actionTaken']);
			$this->setPhotoPath($results[0]['photoPath']);
			$this->setIncidentTime($results[0]['incidentTime']);
			$this->setipAddress($results[0]['ipAddress']);
			$this->setIsIOS($results[0]['isIOS']);
		} else return false;
	}

	public static function getAllReports($status){
		$db = new Database();
		$sql = '';
		if($status == 1 || $status == 2 || $status == 3){
			$sql = "SELECT * FROM reports WHERE statusID=? ORDER BY id DESC";
			$sql = $db->prepareQuery($sql, $status);
		}
		else{		
			$sql = "SELECT * FROM reports ORDER BY id DESC";
			$sql = $db->prepareQuery($sql);
		}
		
		$results = $db->select($sql);

		return $results;
	
	}


	/*
	*	TODO -- remove. id set should be enough
	*	check to see if a report exists on the DB (prevents accidental duplicates)
	*	
	*	@param $personID | int id of person submitting 
	*		$reportTime | datetime of report submission
	*
	*	@return bool | false if report doesn't exist,
	*		int | report id if does exist
	*/
	public static function reportExists($personID, $reportTime){
		$db = new Database();
		$sql = "SELECT * FROM `reports` WHERE `personID`=? AND `reportTime`=?";
		$sql = $db->prepareQuery($sql, $personID, $reportTime);
		$results = $db->select($sql);
		if(isset($results[0]['id'])){
			return $results[0]['id'];	
		} else return false;
	}
	/*
	* 	changes the status of the current Report from the DB using this object's id
	*/
		public function updateStatus($type){
		$db = new Database();
		$sql = "UPDATE reports SET `statusID`=? WHERE id=?";
		$status = 1;
		if($type == "viewed"){
		$status = 3;
		}
		else if ($type == "closed"){
		$status = 2;
		}
		$sql = $db->prepareQuery($sql, $status, $this->id);
		$db->query($sql);
	}
	/*
		changes the photo path of the report at a given ID
		returns true IFF the report exists and the photoPath was changed
	*/
	public function updatePhotoPath($id, $path){
		$db = new Database();
		$sql = "UPDATE reports SET `photoPath`=? WHERE id=?";
		
		$sql = $db->prepareQuery($sql, $path, $id);
		$result = $db->query($sql);
		return $result;
	}
	/*
	* 	deletes the current Report from the DB using this object's id
	*/
	public function delete(){
		$db = new Database();
		$sql = "DELETE FROM reports WHERE id=?";
		$sql = $db->prepareQuery($sql, $this->id);
		$db->query($sql);
	}
}

?>
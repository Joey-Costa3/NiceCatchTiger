<?php
require_once('config.php');

//for cleaning and validating form inputs.
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

// Method: POST, PUT, GET etc
// Data: array("param" => "value") ==> index.php?param=value
function CallAPI($method, $url, $data = false){
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}



//------------------------ LOOKUP METHODS (FOR Loader Controller) ------------------------

//lookup reports by filter ID
function getReports($statusID){
	$reports = array();
	// $statusID is statusID filter
	// $reportKind is reportKind filter
	// $building is building filter
	// $departments is department filter
	// $involvementKind is involvementKind filter
	// $personName is person name filter
	// customDate is custom date range filter
	
	if(!is_null($statusID)){
		$db = new Database();
		$sql = "SELECT `id` FROM reports WHERE statusID=? ORDER BY incidentTime DESC";
		$sql = $db->prepareQuery($sql, $statusID);
	} else { //not filtered. all reports
		$db = new Database();
		$sql = "SELECT `id` FROM reports ORDER BY incidentTime DESC";
	}

	$results = $db->select($sql);
	if(is_array($results)){
		foreach($results as $result){
			$newReport = new Report();
			$newReport->fetch($result['id']); 
			$reports[] = $newReport->toArray();
		}
	}
	return $reports;
}

function getDefaultPersonKinds(){
	$db = new Database();
	$sql = "SELECT * FROM personKinds WHERE `default`=1";
	$results = $db->select($sql);
	$personKinds = array();

	if(is_array($results)){
		foreach($results as $result){
			$personKinds[] = array('id' => $result['id'], 'personKind' => $result['personKind'], 'default' => $result['default']);
		}
	}
	return $personKinds;	
}


function getDefaultInvolvements(){
	$db = new Database();
	$sql = "SELECT * FROM involvementKinds WHERE `default`=1";
	$results = $db->select($sql);
	$involvements = array();

	if(is_array($results)){
		foreach($results as $result){
			$involvements[] = array('id' => $result['id'], 'involvementKind' => $result['involvementKind'], 'default' => $result['default']);
		}
	}
	return $involvements;
}

function getDefaultReportKinds(){
	$db = new Database();
	$sql = "SELECT * FROM reportKinds WHERE `default`=1";
	$results = $db->select($sql);
	$reportKinds = array();

	if(is_array($results)){
		foreach($results as $result){
			$reportKinds[] = array('id' => $result['id'], 'reportKind' => $result['reportKind'], 'default' => $result['default']);
		}
	}
	return $reportKinds;
}

function getBuildings(){
	$db = new Database();
	$sql = "SELECT * FROM buildings ORDER BY buildingName";
	$results = $db->select($sql);
	$buildings = array();

	if(is_array($results)){
		foreach($results as $result){
			$buildings[] = array('id' => $result['id'], 'buildingName' => $result['buildingName']);
		}
	}
	return $buildings;
}

function getDepartments(){
	$db = new Database();
	$sql = "SELECT * FROM departments ORDER BY departmentName";
	$results = $db->select($sql);
	$departments = array();

	if(is_array($results)){
		foreach($results as $result){
			$departments[] = array('id' => $result['id'], 'departmentName' => $result['departmentName']);
		}
	}
	return $departments;
}

//------------------------ add new involvements/report kinds ------------------------

/*
*	adds a new involvement if necessary. returns the id
*/
function getInvolvementKindID($involvement){
	$db = new Database();
	$sql = "SELECT * FROM involvementKinds WHERE involvementKind=?";
	$sql = $db->prepareQuery($sql, $involvement);

	$results = $db->select($sql);
	if(isset($results[0]['id'])){
		return $results[0]['id'];	
	} else { //add new involvement
		$sql = "INSERT INTO involvementKinds(`involvementKind`,`default`) VALUES(?,0)";
		$sql = $db->prepareQuery($sql, $involvement);
		$db->query($sql);
	}

	$sql = "SELECT * FROM involvementKinds WHERE involvementKind=?";
	$sql = $db->prepareQuery($sql, $involvement);

	$results = $db->select($sql);
	if(isset($results[0]['id'])){
		return $results[0]['id'];	
	} else return false;
}

/*
*	given an existing involvement kind id, update the kind to the given name
*/
function updateInvolvementKind($id, $involvementKind){
	$db = new Database();
	$sql = "UPDATE involvementKinds SET involvementKind=? WHERE id=?";
	$sql = $db->prepareQuery($sql, $involvementKind, $id);

	$db->query($sql);
	return array(
		'id' => $id,
		'involvementKind' => $involvementKind
		);
}

/*
*	adds a new report kind if necessary. returns the id
*/
function getReportKindID($reportKind){
	$db = new Database();
	$sql = "SELECT * FROM reportKinds WHERE reportKind=?";
	$sql = $db->prepareQuery($sql, $reportKind);

	$results = $db->select($sql);
	if(isset($results[0]['id'])){
		return $results[0]['id'];	
	} else { //add new involvement
		$sql = "INSERT INTO reportKinds(`reportKind`,`default`) VALUES(?,0)";
		$sql = $db->prepareQuery($sql, $reportKind);
		$db->query($sql);
	}

	$sql = "SELECT * FROM reportKinds WHERE reportKind=?";
	$sql = $db->prepareQuery($sql, $reportKind);

	$results = $db->select($sql);
	if(isset($results[0]['id'])){
		return $results[0]['id'];	
	} else return false;
}

function updateReportKind($id,$reportKind){
	$db = new Database();
	$sql = "UPDATE reportKinds SET reportKind=? WHERE id=?";
	$sql = $db->prepareQuery($sql, $reportKind, $id);

	$db->query($sql);
	return array(
		'id' => $id,
		'reportKind' => $reportKind
		);
}

function getDepartmentID($departmentName){
	$db = new Database();
	$sql = "SELECT id FROM departments WHERE departmentName=?";
	$sql = $db->prepareQuery($sql, $departmentName);

	$results = $db->select($sql);
	if(isset($results[0]['id'])){
		return $results[0]['id'];
	} else return -1;
}

/*
*	adds a new person kind if necessary. returns the id
*/
function getPersonKindID($personKind){
	$db = new Database();
	$sql = "SELECT * FROM personKinds WHERE personKind=?";
	$sql = $db->prepareQuery($sql, $personKind);

	$results = $db->select($sql);
	if(isset($results[0]['id'])){
		return $results[0]['id'];	
	} else { //add new involvement
		$sql = "INSERT INTO personKinds(`personKind`,`default`) VALUES(?,0)";
		$sql = $db->prepareQuery($sql, $personKind);
		$db->query($sql);
	}

	$sql = "SELECT * FROM personKinds WHERE personKind=?";
	$sql = $db->prepareQuery($sql, $personKind);

	$results = $db->select($sql);
	if(isset($results[0]['id'])){
		return $results[0]['id'];	
	} else return false;
}

function updatePersonKind($id,$personKind){
	$db = new Database();
	$sql = "UPDATE personKinds SET personKind=? WHERE id=?";
	$sql = $db->prepareQuery($sql, $personKind, $id);

	$db->query($sql);
	return array(
		'id' => $id,
		'personKind' => $personKind
		);
}

?>
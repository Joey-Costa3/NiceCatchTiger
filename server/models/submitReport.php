<?php
	require_once(dirname(dirname(__FILE__)).'/models/config.php');

	$id = $_POST["id"];
	$description = $_POST["description"];
	$involvementKind = $_POST["involvementKind"];
	
	$reportKind = $_POST["reportKind"];
	$buildingName = $_POST["buildingName"];
	$room = $_POST["room"];
	$personKind = $_POST["personKind"];
	$name = $_POST["name"];
	$username = $_SERVER["REMOTE_USER"];
	$phone = $_POST["phone"];
	$department = $_POST["department"];
	$reportTime = $_POST["reportTime"];
	
	$statusID = $_POST["statusID"];
	$actionTaken = $_POST["actionTaken"];
	$incidentTime = $_POST["incidentTime"];
	$isIOS = $_POST["isIOS"];
	// long and lat would have been used as a poor form of authentication
	$longi = $_POST["longi"]; // not used 
	$lat = $_POST["lat"];		// not used

		
	
	$report = new Report();

        //update if an id exists
        if(isset($id)){
            $report->setID($id);
        }

        $report->setDescription(htmlspecialchars($description));

        //given name. get id
        $report->setInvolvementKindID(getInvolvementKindID(htmlspecialchars($involvementKind)));

        //given name. get id
        $report->setReportKindID(getReportKindID(htmlspecialchars($reportKind)));

       // ----------------------------------------- set up location
       $location = new Location();

        $buildingID = Location::lookupBuildingID($buildingName);
        if($buildingID == false){ 
            //building not found
            return -1;
        }

        $location->setBuildingID($buildingID);

        //handle reports with no room or blank room set
        if(isset($room) && strtolower($room) != 'null' && $room != ''){
            $location->setRoom(strip_tags($room));
        }
        
        $location->save(); //creates new location if necessary. sets id

        $locID = $location->getID();
        // ----------------------------------------- end set location
		//echo 'Loc ID = '. $location->getID() . '<br>';
        $report->setLocationID($locID);
        

        // --------------------------- set up person
         $person = new Person();
        
        $person->setPersonKindID(getPersonKindID(htmlspecialchars($personKind)));
        $person->setUsername(htmlspecialchars($username));
        $person->setName(htmlspecialchars($name));
        $person->setPhone(htmlspecialchars($phone));
        $person->save();

        $personID = $person->getID();
        // --------------------------- end set up person
        $report->setPersonID($personID);
        

        //given dept name. get id
        $report->setDepartmentID(getDepartmentID($department));
        
        $report->setReportTime($reportTime);
        $report->setStatusID($statusID);
        $report->setActionTaken($actionTaken);
        $report->setIncidentTime($incidentTime);
        $report->setipAddress($report->get_ip());
        $report->setIsIOS($isIOS);
        $report->save();
		if(!isset($id)){
            $report->setID($report->getID());
        }

        //return the report item in array format
        
        $jsonReturn = Array();
        $jsonReturn['message'] = '';
        $jsonReturn['data'] = Array();
        $jsonReturn['data'] = $report->toArray();
        echo json_encode($jsonReturn);

?>
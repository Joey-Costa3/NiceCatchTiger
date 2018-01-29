<?php
require_once 'class.API.php';

/*
*   CONSTRUCTOR
*   ENDPOINTS
*       1.0 INVOLVEMENTS
*       1.1 REPORT KINDS
*       1.2 PERSON KINDS
*       1.3 BUILDINGS
*       1.4 DEPARTMENTS
*       1.5 REPORTS
*       1.6 REPORT (invisible)
*   HELPERS
*       2.0 SET UP PERSON
*       2.1 SET UP LOCATION
*       2.2 REQUEST FIELDS SUBMITTED
*       2.3 VALIDATE PHOTO
*
*   Known Issues: 
*   - for photo get, api tries to set header after echo image
*/ 

class NiceCatchAPI extends API {
    //what to return from endpoint for processing
    //code is http status code.
    private $response = array('code' => 200, 'data' => null, 'message' => '');

    public function __construct($request, $origin) {
        parent::__construct($request);
        //$this->response = array('code' => 200, 'data' => null);        
    }

    //------------------------ INVOLVEMENTS ENDPOINT <1.0> ------------------------
    public function involvements(){
        if($this->method == 'GET'){
            //get a list of default involvement kinds
            $this->response['data'] = getDefaultInvolvements();
        } else if($this->method == 'POST'){
            if(isset($this->request['id']) && isset($this->request['involvementKind'])){
                //if the ID is set, editing an existing involvement
                $this->response['data'] = updateInvolvementKind($this->request['id'],$this->request['involvementKind']);
            } elseif(!$this->requestFieldsSubmitted(array('involvementKind'))){
                //not all necessary values submitted
                $this->response['message'] = "endpoint requires an involvementKind";
                $this->response['code'] = 400;
            } else {
                //new involvement (if not in DB)
                $this->response['data'] = array(
                    'id' => getInvolvementKindID($this->request['involvementKind']),
                    'involvementKind' => $this->request['involvementKind']
                );    
            }
        } else { 
            $this->response['message'] = "endpoint does not recognize " . $this->method . " requests";   
            $this->response['code'] = 405;
        }

        return $this->response;
    }

    //------------------------ REPORT KINDS ENDPOINT <1.1> ------------------------
    public function reportKinds(){
        if($this->method == 'GET'){
            $this->response['data'] = getDefaultReportKinds();
        } else if($this->method == 'POST'){
            if($this->requestFieldsSubmitted(array('id','reportKind'))){
                //if the ID is set, editing an existing reportKind
                $this->response['data'] = updateReportKind($this->request['id'],$this->request['reportKind']);
            } elseif(!$this->requestFieldsSubmitted(array('reportKind'))){
                //if no reportKind is set, invalid
                $this->response['message'] = "endpoint requires a reportKind";
                $this->response['code'] = 400;
            } else {
                //new report kind (if not in DB)
                $this->response['data'] = array(
                    'id' => getReportKindID($this->request['reportKind']),
                    'reportKind' => $this->request['reportKind']
                );                
            }
        } else {
            $this->response['message'] = "endpoint does not recognize " . $this->method . " requests";   
            $this->response['code'] = 405;
        }
        return $this->response;
    }

    //------------------------ PERSON KINDS ENDPOINT <1.2> ------------------------
    public function personKinds(){
        if($this->method == 'GET'){
            $this->response['data'] = getDefaultPersonKinds();
        } else if($this->method == 'POST'){
            //if the ID is set, editing an existing personKind
            if($this->requestFieldsSubmitted(array('id','personKind'))){
                $this->response['data'] = updatePersonKind($this->request['id'],$this->request['personKind']);
            } elseif(!$this->requestFieldsSubmitted(array('personKind'))){
                $this->response['message'] = "endpoint requires a personKind";
                $this->response['code'] = 400;
            } else {
                //new report kind (if not in DB)
                $this->response['data'] = array(
                    'id' => getPersonKindID($this->request['personKind']),
                    'personKind' => $this->request['personKind']
                );                
            }
        } else { 
            $this->response['message'] = "endpoint does not recognize " . $this->method . " requests";   
            $this->response['code'] = 405;
        }

        return $this->response;
    }

    //------------------------ BUILDINGS ENDPOINT <1.3> ------------------------
    public function buildings(){
        if($this->method == 'GET'){
            $this->response['data'] = getBuildings();
        } else {
            $this->response['message'] = "endpoint does not recognize " . $this->method . " requests";   
            $this->response['code'] = 405;
        }

        return $this->response;
    }

    //------------------------ DEPARTMENTS ENDPOINT <1.4> ------------------------
    public function departments(){   
        if($this->method == 'GET'){
            $this->response['data'] = getDepartments();
        } else { 
            $this->response['message'] = "endpoint does not recognize " . $this->method . " requests";   
            $this->response['code'] = 405;
        }

        return $this->response;
    }

    //------------------------ REPORTS ENDPOINT <1.5> ------------------------
    protected function reports(){
        //URI: /api/v1/reports
        //$this->response['message']   = $this->method. " reports(). count: " . count($_POST) . " value = " . $this->request['id'];
        if(!is_array($this->args) || count($this->args) == 0){
            $this->response['data'] = $this->reportsCollection();
           // $this->response['data'] = $this->method. " reports(). count: " . count($_POST) . " value = " . $this->request['description'];
        } else if(count($this->args) == 1){ //URI: /api/v1/reports/<ID>
            $this->response['data'] = $this->report();
        } else if(count($this->args) == 2 && $this->args[1] == 'photo') {
            //an individual photo's report
            $this->response['data'] = $this->reportPhoto();
        } else {
            $this->response['message'] = "improper API call to reports endpoint";
            $this->response['code'] = 400;
        }

        return $this->response;
    }

    //handler for API call to the reports collection
    private function reportsCollection(){
        if($this->method == 'GET'){
            //return list of reports
            return $this->reportsGet();

        } else if($this->method == 'POST'){
            if(!$this->requestFieldsSubmitted(["description","involvementKind","reportKind","buildingName","personKind","username","name","phone","department","reportTime","statusID","actionTaken","incidentTime", "isIOS"])){
                $this->response['message'] = "error: missing report information";
                $this->response['code'] = 400;
            }
            return $this->reportPost();
        } else {
            $this->response['message'] = "endpoint does not recognize " . $this->method . " requests";
            $this->response['code'] = 405;
        }
    }

    //return a list of reports, filtered by filter arg
    private function reportsGet(){
        if(!isset($this->request['filter'])){
            return getReports(null);
        } else { 
            switch($this->request['filter']){
                case 'new':
                    return getReports(1);
                case 'open':
                    return getReports(2);
                case 'closed':
                	return getReports(3);
                case 'all':
                    return getReports(null);
                default:
                    $this->response['message'] = 'Incorrect filter';
                    $this->response['code'] = 400;
            }
        }//else
    }//reportsGet

    //------------------------ REPORT ENDPOINT (invisible) <1.6> ------------------------

    //handler for API call to a single note
    private function report(){
        if($this->method == 'GET'){
            return $this->reportGet();
        } else if($this->method == 'POST'){
            $this->response['message'] = "Update Report (POST) not implemented";
            $this->response['code'] = 400;
        } else if ($this->method == 'DELETE'){
            $this->response['message'] = "Single Report (DELETE) not implemented";
            $this->response['code'] = 400;
        }
    }

    //parse out args and make a new report
    private function reportPost(){
        $report = new Report();

        //update if an id exists
        if(isset($this->request['id'])){
            $report->setID($this->request['id']);
        }

        $report->setDescription($this->request['description']);

        //given name. get id
        $report->setInvolvementKindID(getInvolvementKindID($this->request['involvementKind']));

        //given name. get id
        $report->setReportKindID(getReportKindID($this->request['reportKind']));
        
        //set up location
        if(!$this->requestFieldsSubmitted(["buildingName"])){
            $this->response['message'] = "error: missing location information";
            $this->response['code'] = 400;
        }
        $locID = $this->setUpLocation();
        if($locID != -1) $report->setLocationID($locID);
        else {
            $this->response['message'] = "error: invalid location information";
            $this->response['code'] = 400;
        }

        //set up person
        if(!$this->requestFieldsSubmitted(["personKind","username","name","phone"])){
            $this->response['message'] = "error: missing person information";
            $this->response['code'] = 400;
        }
        $personID = $this->setUpPerson();
        if($personID != -1) $report->setPersonID($personID);
        else { 
            $this->response['message'] = "error: invalid person data";
            $this->response['code'] = 400;
        }

        //given dept name. get id
        $report->setDepartmentID(getDepartmentID($this->request['department']));
        
        $report->setReportTime($this->request['reportTime']);
        $report->setStatusID($this->request['statusID']);
        $report->setActionTaken($this->request['actionTaken']);
        $report->setIncidentTime($this->request['incidentTime']);
        $report->setipAddress($report->get_ip());
        $report->setIsIOS($this->request['isIOS']);
        $report->save();

        //return the report item in array format
        return $report->toArray();
    }

    //return data for a single report
    private function reportGet(){
        $reportID = $this->args[0];
        $report = new Report();
        $report->fetch($reportID);
        return $report->toArray();
    }

    //handles uploading or getting a report's photo
    private function reportPhoto(){
        switch($this->method){
            case 'GET':
                return $this->reportPhotoGet();
            case 'POST':  
                return $this->reportPhotoPost();
            default:
                $this->response['message'] = 'error: endpoint does not recognize ' . $this->method . ' requests';
                $this->response['code'] = 405;
        }

    }

    private function reportPhotoGet(){
        $reportID = $this->args[0];
        $report = new Report();
        $report->fetch($reportID);
        if($report->getPhotoPath() == null){
            $this->response['message'] = 'error: no photo for this report';
            $this->response['code'] = 400;
        }

        //echo image to browser/client
        $img = file_get_contents($report->getPhotoPath());
        if($img == false){ //make sure photo loaded
            $this->response['message'] = 'error: broken photo path';
            $this->response['code'] = 500;
        }

        //find photo type
        //$filetype = exif_imagetype($report->getPhotoPath());

        /*
        return 'filetype: ' . $filetype;

        switch($filetype){

            case 3:
                header('content-type: image/png');
            case IMAGETYPE_JPEG:
                header('content-type: image/jpeg');
            case IMAGETYPE_GIF:
                header('content-type: image/gif');
            default:
                return 'error: stored image type not recognized';
        }*/
        
        header('content-type: image/png');
        header("HTTP/1.1 200 OK");
        echo $img;
    }

    private function reportPhotoPost(){
        if(!$this->validatePhoto()) return 'error: photo upload failed'; 
        $report = new Report();

        //fetch and check for valid report
        $report->fetch($this->args[0]);
        if($report->getID() == null){
            $this->response['message'] = 'error: failed to load report with id ' . $this->args[0];
            $this->response['code'] = 405;
        }

        //report id
        $reportID = $this->args[0];


        $photo = $this->files['photo'];
        $upload_dir = Path::uploads() . $reportID . '/';

        //make the directory if it doesn't already exist
        if(!file_exists($upload_dir)){
            mkdir($upload_dir, 0755, true);
            chmod($upload_dir, 0775);
        }
		
        //make sure there wasnt an error with the upload
        if($photo['error'] !== UPLOAD_ERR_OK){
            $this->response['message'] = 'error: photo upload error';
            $this->response['code'] = 400;
        }

        //make sure filename is safe
        $name = preg_replace("/[^A-Z0-9._-]/i", "_", $photo['name']);

        //different dir for each report
        $i = 0;
        $parts = pathinfo($name);
     
        while(file_exists($upload_dir . $name)){
            //myfile-1.png
            $name = $parts['filename'] . '-' . $i . '.' . $parts['extension'];
        }

        //move file from temp directory
        $success = move_uploaded_file($photo['tmp_name'], $upload_dir . $name);
        if(!$success){
            $this->response['message'] = 'error: unable to save file';
            $this->response['code'] = 500;
        }

        //set proper file permissions on new file
        chmod($upload_dir . $name, 0644);

        //update the report in DB with file location
        $report->setPhotoPath($upload_dir . $name);
        $report->save();

        return $report->toArray();
    }

    //------------------------ HELPERS ------------------------

    /*  <2.0>
    *   creates a person using request data
    *   @preq personKind, username, name, and phone are set
    *   @ret int | person id if valid request data, -1 otherwise
    */
    private function setUpPerson(){
        $person = new Person();
        
        $person->setPersonKindID(getPersonKindID($this->request['personKind']));
        $person->setUsername($this->request['username']);
        $person->setName($this->request['name']);
        $person->setPhone($this->request['phone']);
        $person->save();

        return $person->getID();
    }

    /*  <2.1>
    *   creates a location using request data
    *   @ret int | location id if valid request data, -1 otherwise
    */
    private function setUpLocation(){
        $location = new Location();

        $buildingID = Location::lookupBuildingID($this->request['buildingName']);
        if($buildingID == false){ 
            //building not found
            return -1;
        }

        $location->setBuildingID($buildingID);

        //handle reports with no room or blank room set
        if(isset($this->request['room']) && strtolower($this->request['room']) != 'null' && $this->request['room'] != ''){
            $location->setRoom($this->request['room']);
        }
        
        $location->save(); //creates new location if necessary. sets id

        return $location->getID();
    }

    /*  <2.2>
    *   checks if all necessary variables are set
    *   $vars is an array
    */
    private function requestFieldsSubmitted($vars){
        if(is_array($vars)){
            foreach($vars as $var){
                if(!isset($this->request[$var])) return false;
            }
            return true;
        }
        return false;
    }

    /*  <2.3>
    *   validates an image to make sure it is valid
    *   helps prevent incorrect uploads/malicious files
    */
    private function validatePhoto(){
        if(!empty($this->files['photo'])){
            $photo = $this->files['photo'];
            
            //verify file is correct type (gif, jpeg, png)
            $filetype = exif_imagetype($photo['tmp_name']);
            $allowed = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);
            if(in_array($filetype, $allowed)){
                return true;
            }
        }
        return false;
    }
 }
 ?>
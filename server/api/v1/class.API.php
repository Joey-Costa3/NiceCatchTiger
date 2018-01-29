<?php

//SOURCE: http://coreymaynard.com/blog/creating-a-restful-api-with-php/

abstract class API
{
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';
    
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     */
    protected $endpoint = '';
    
    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     */
    //protected $verb = '';
    
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();
    
    /**
     * Property: file
     * Stores the input of the PUT request
     */
    //protected $file = Null;

    /**
    *   Property: files
    *   from the $_FILES variable (for uploading photos)
    */
    protected $files = null;

    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($request) {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

		// AWS
		//header("Policy: POLICY");
		//header("Signature: SIGNATURE");
	// 	header("acl: public-read");
// 		//header("x-amz-meta-uuid: 00");
// 		header("AWSAccessKeyID: AKIAI7MQHC5G7ULRWT5Q");
// 		header("x-amz-algorithm: AWS4-HMAC-SHA256");
        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = array_shift($this->args);
        /*if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }*/
        

        $this->method = $_SERVER['REQUEST_METHOD'];
        header("method: ".$this->method);
        header("servermethod: ". $_SERVER['REQUEST_METHOD']);
        header("http_x_method: ".$_SERVER['HTTP_X_HTTP_METHOD']);
        
        $method = '';
        if($this->method == 'POST2'){
        $this->method = 'POST';
        }else{
        $method = $this->method;
        }
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }

        switch($this->method) {
        case 'DELETE':
        case 'POST':
            $this->request = $this->_cleanInputs($_POST);
            if(isset($_FILES)){
                $this->files = $_FILES;
            }
            break;
        case 'GET':
            $this->request = $this->_cleanInputs($_GET);
            break;
        case 'PUT':
            $this->request = $this->_cleanInputs($_GET);
            //$this->file = file_get_contents("php://input");
            break;
        default:
            $this->_response('Invalid Method', 405);
            break;
        }
    }//end constructor 

    //run the request through the endpoint
    public function processAPI() {
        if (method_exists($this, $this->endpoint)) {
            $responseArr = $this->{$this->endpoint}($this->args);

            $finalArr = array();
            $finalArr['data'] = $responseArr['data'];
            $finalArr['message'] = $responseArr['message'];

            //return $this->_response($this->{$this->endpoint}($this->args));
            return $this->_response($finalArr, $responseArr['code']);
        }
        return $this->_response("No Endpoint: $this->endpoint", 404);
    }

    //add http info to the response
    private function _response($data, $status = 200) {
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        return json_encode($data);
    }

    //to prevent injection
    private function _cleanInputs($data) {
        // $clean_input = Array();
//         if (is_array($data)) {
//             foreach ($data as $k => $v) {
//                 $clean_input[$k] = $this->_cleanInputs($v);
//             }
//         } else {
//             $clean_input = trim(strip_tags($data));
//         }
//         return $clean_input;
return $data;
    }

    private function _requestStatus($code) {
        $status = array(  
            200 => 'OK',
            304 => 'Not Modified',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',   
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ); 
        return ($status[$code])?$status[$code]:$status[500]; 
    }


}

?>
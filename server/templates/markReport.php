<?php
	require_once(dirname(dirname(__FILE__)) . '/models/config.php');

$path = Path::externalRoot().Path::subDirectory();

 		$id = $_POST["id"];		
		$action = $_POST["action"];
		$viewing = $_POST["viewing"];
		
		echo 'The id of the report to mark as '.$action.' is '.$id.'. We will then forward the user back to viewing '.$viewing.' reports';

//$reports = json_decode(CallAPI("GET","".$path."/api/v1/reports/".$id.""), true)['data'];
 $json_url = $path."/api/v1/reports/".$id."";
$json = file_get_contents($urlJSON);
$data = json_decode($json, TRUE)['data'];
 ///print_r($data);
// print(count($data));
$reports = $data;

//echo '<br> https://people.cs.clemson.edu/~jacosta/api/v1/reports/'.$id.'<br>';
//expand report location and person to show details, not just ID
	if(is_array($reports)){

		for($i = 0; $i < 1; $i++){
	
			$db = new Database();
			$sql = "UPDATE reports SET `statusID`=? WHERE id=?";
			$status = 1;
			if($action == "Reviewed"){
			$status = 3;
			}
			else if ($action == "Resolved"){
			$status = 2;
			}
			$sql = $db->prepareQuery($sql, $status, $id);
			$db->query($sql);
			echo 'Successfully marked the message  as '.$viewing.'- transferring back to page.php<br>';
			$location = $path."/templates/page.php?".ucfirst($viewing);
			echo $location;
			//header("Location: ".$location);
			exit;
			
		}
	}

echo 'Error in setting the report status. Please go back in your browser and try again. <br>
If this error continues to show up please contact support.';
		
?>
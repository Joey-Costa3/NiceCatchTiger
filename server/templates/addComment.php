<?php
	require_once(dirname(dirname(__FILE__)) . '/models/config.php');

$path = Path::externalRoot().Path::subDirectory();
 		$id = $_POST["id"];		
		$action = $_POST["action"];
		$viewing = $_POST["viewing"];
		$name = $_POST["name"];
		$comment = $_POST["comment"];
		$button = $_POST["button"];
		echo 'The id of the report to add '.$button.' is '.$id.'. We will then forward the user back to viewing '.$viewing.' reports<br>';
		echo '<br>Data id: ' .$id.', action: ' .$button.', viewing: '.$viewing.', name: '.$name.', comment: '.$comment.' <br>' ;
		echo 'Value of the button ' . $button. '<br>';
		//$path = "http://".$path;
		echo $path;

if( $button == 'Open' || $button == 'Closed' || $button == 'Received')
{
echo '<br> Changing the statusID of the report<br>';
echo 'The id of the report to mark as '.$button.' is '.$id.'. We will then forward the user back to viewing '.$viewing.' reports';

	//$reports = json_decode(CallAPI("GET","https://people.cs.clemson.edu/~jacosta/api/v1/reports/".$id.""), true)['data'];
	//$reports = json_decode(CallAPI("GET","".$path."/api/v1/reports/".$id.""), true)['data'];
	
	$database = new Database();
$sqlReport = "Select * from reports WHERE id=?";
$sqlReport = $database->prepareQuery($sqlReport, $id);
$reports = $database->select($sqlReport);
	//echo '<br> https://people.cs.clemson.edu/~jacosta/api/v1/reports/'.$id.'<br>';
	//expand report location and person to show details, not just ID
	if(is_array($reports)){

		for($i = 0; $i < 1; $i++){ // should only return 1 with the same ID ignore rest
	
			$db = new Database();
			$sql = "UPDATE reports SET `statusID`=? WHERE id=?";
			$status = 1;
			if($button == "Open"){
			$status = 2;
			}
			else if ($button == "Closed"){
			$status = 3;
			}
			$sql = $db->prepareQuery($sql, $status, $id);
			$db->query($sql);
			echo 'Successfully marked the message  as '.$button.'('.$status.')- transferring back to page.php';
			} // end for loop 
	} // end if $reports array exists
	$button = 'Comment';
}
if($button == 'Comment')
{
	echo '<br> Adding a comment to the report';
			$db = new Database();
			
			// MySQL now() yyyy-MM-dd hh:mm:ss.uuuu
			
			$nowDate = date("Y-m-d H:i:s");
			$name = htmlspecialchars($name);
			$comment = htmlspecialchars($comment);
			$sql = "INSERT INTO `comments`(`name`, `comment`, `commentDate`) VALUES(?,?,?)";
			$sql = $db->prepareQuery($sql, $name, $comment, $nowDate);
			$db->query($sql);
			$sql2 = "SELECT id from comments ORDER BY id DESC LIMIT 1";
			$results = $db->select($sql2);
			echo $sql; 
			echo '<br>';
			//echo $results;
			if(is_array($results)){
					foreach($results as $result){
					//echo $result['id'];
					$commentID = $result['id'];
					$sqlRC = "INSERT INTO `reportComment` (`reportID`, `commentID`) VALUES(?, ?)";
					$sqlRC = $db->prepareQuery($sqlRC, $id, $commentID);
					$db->query($sqlRC);
					// echo $sqlRC; 
// 					echo '<br>';
					echo 'Successfully added comment - transferring back to page.php';
					echo $path."/templates/page.php?".ucfirst($viewing);
					echo '<br>';
					//header("Location: https://people.cs.clemson.edu/~jacosta/templates/page.php?".ucfirst($viewing)."");
					$location = $path."/templates/page.php?".ucfirst($viewing);
					echo $location;
					header("Location:".$location);
					exit();
					} 
				}
			echo '<br>';
}
else
{ 
			echo '<br>Error in adding a comment. Please go back in your browser and try again. <br>
 			If this error continues to show up please contact support.';
}	 
		
?>
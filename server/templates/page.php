<html lang="en">
	 <?php include('header.php'); ?>

	<body>
	
    <!-- Begin page content -->
    <div class="containerC">

<?php

$showIP = false;
$showOS = false;
	require_once(dirname(dirname(__FILE__)) . '/models/config.php');
	$path = Path::externalRoot().Path::subDirectory();




$query = $_SERVER['QUERY_STRING'];
// $urlJSON = $path."/api/v1/reports";

 if ($query == 'Received')
 {
 	
	//$reports = json_decode(CallAPI("GET","".$path."/api/v1/reports?filter=new"), true)['data'];
		$data2 = new Report();
 	$reportData = $data2->getAllReports(1); // 1 is received reports
// 	$urlJSON = $urlJSON."?filter=new";
 }		
else  if ($query == 'Open')
 {
 	//$reports = json_decode(CallAPI("GET","".$path."/api/v1/reports?filter=open"), true)['data'];
 		$data2 = new Report();
 	$reportData = $data2->getAllReports(2); // 2 is open reports
//  		$urlJSON = $urlJSON."?filter=open";

 }
 else if ($query == 'Closed')
 {
 	//$reports = json_decode(CallAPI("GET","".$path."/api/v1/reports?filter=closed"), true)['data'];
 		$data2 = new Report();
 	$reportData = $data2->getAllReports(3); // 3 is closed reports
//  		$urlJSON = $urlJSON."?filter=closed";

 }
 else if ($query == 'All')
 {

 	$data2 = new Report();
 	$reportData = $data2->getAllReports(0); // 0 is all reports
//  		$urlJSON = $urlJSON."?filter=all";

 }
 else
 {
 	header("Location: ".$path."/templates/page.php?All", true);
 	
 }

 
//  $json_url = $path."/api/v1/api.php?request=reports&filter=all";
 
 
 //echo $urlJSON;
// $json = file_get_contents($urlJSON);
// 
// $data = json_decode($json, TRUE)['data'];


 ///print_r($data);
// print(count($data));
$reports = $reportData;
  
	//expand report location and person to show details, not just ID
	if(is_array($reports)){

		for($i = 0; $i < count($reports); $i++){
			$person = new Person();
			$person->fetch($reports[$i]['personID']);

			$reports[$i]['personKind'] = $person->getPersonKindName();
			$reports[$i]['personName'] = $person->getName();
			$reports[$i]['personUsername'] = $person->getUsername(); 
			$reports[$i]['personPhone'] = $person->getPhone();

			$location = new Location();
			$location->fetch($reports[$i]['locationID']);

			$reports[$i]['locationBuilding'] = $location->getBuildingName();
			$reports[$i]['locationRoom'] = $location->getRoom(); 
			
			$involvementKind = new InvolvementKind();
			$involvementKind->fetch($reports[$i]['involvementKindID']);
						
			$reports[$i]['involvementKind'] = $involvementKind->getInvolvementKind();
			$reports[$i]['involvementDefault'] = $involvementKind->getDefault();
			
			$reportKind = new ReportKind();
			$reportKind->fetch($reports[$i]['reportKindID']);
			
			$reports[$i]['reportKind'] = $reportKind->getReportKind();
			$reports[$i]['reportDefault'] = $reportKind->getDefault();
			
			$statuses = new Statuses();
			$statuses->fetch($reports[$i]['statusID']);
	
			$reports[$i]['status'] = $statuses->getName();
			
			$department = new Department();
			$department->fetch($reports[$i]['departmentID']);
			
			$reports[$i]['department'] = $department->getName();
			//echo '<br>Report ' .$i;
		}
	}
	else{
	//echo 'No Reports';
	}

	//var_dump($reports);
?>


 <!~~ in .container div ~~>
 
 

<div class='row'>
	<div>
		<div>
			<h1> <?php 	echo $query; ?> Nice Catch Tiger! Reports  </h1>
			<!-- show button to download CSV file -->
			
    <textarea id="txt" class='txtarea hidden' aria-label="CSV File Text">
    
    <?php  
    $x = 0;
	$len = count($reports);
    echo '['; foreach($reports as $report){
	echo '{';
    echo '"Report Time":'; echo '"'.$report['reportTime'].'"'; echo ',';    
    echo '"Incident Time":'; echo '"'.$report['incidentTime'].'"'; echo ',';
    echo '"Description":'; echo '"'.$report['description'].'"'; echo ',';
    echo '"Involvement Kind":'; echo '"'.$report['involvementKind'].'"'; echo ',';
    echo '"Report Kind":'; echo '"'.$report['reportKind'].'"'; echo ',';
    echo '"Location":'; echo '"'.$report['locationBuilding'].' '.$report['locationRoom'].'"'; echo ',';
    echo '"Department":'; echo '"'.$report['department'].'"'; echo ',';
    echo '"Name":'; echo '"'.$report['personName'].'"'; echo ',';
    echo '"Username":'; echo '"'.$report['personUsername'].' ('.$report['personKind'].')'.'"'; echo ',';
    echo '"Status":'; echo '"'.$report['status'].'"';

	$db = new Database();

   	$sqlComment = "SELECT * from reportComment WHERE reportID = ".$report['id']." ORDER BY id DESC";
   	//echo $sqlComment;
	$reportCommentResults = $db->select($sqlComment);
	$y = 0;
	$commentCount = count($reportCommentResults);
	if($commentCount > 0){
   								 
   		foreach($reportCommentResults as $reportComment){
		$commentsql = "SELECT * FROM comments WHERE id =? ORDER BY id";
		$commentsql = $db->prepareQuery($commentsql, $reportComment['commentID']);
		$commentResults = $db->select($commentsql);
		
				if(count($commentResults) > 0){										
						foreach($commentResults as $comment){
							if($y == 0)
							{
							echo ',';
							}
							$y = $y + 1;

							 echo '"Comment'.$y.'":"'.$comment['name'].'\n'.$comment['comment'].' \n('.$comment['commentDate'].')"';
							//  echo '<tr>'.
//  							'<td>'. $comment['commentDate'].'</td>'.
//  							'<td>'. $comment['name'].'</td> '.
//  							'<td>'. $comment['comment'].'</td>'.
//  							'</tr>';
							if ($y != $commentCount)
    							{
								echo',';
								}
   							}
   						}		 			
   				}
   			}
    if ($x == $len - 1)
    	{
		echo'}';
		}
		else
		{
		echo'},';
		}
	$x++;
    }  echo ']';?>
    
    </textarea>
    <button id='download'>Download Excel</button>   

		</div>

		<!~~ reports table ~~>
		
		
		
		<div style="overflow-x:auto;">
		<table id="tableMain" name="tableMain" class="table table-striped filterable table-responsive">
			<thead>
				<tr>
				<?php 
				if($showIP)
				{
				echo '<th>ipAddress</th>';
				} 
				if ($showOS)
				{
				echo '<th>ID</th>';
				echo '<th>OS</th>';
				}
				?>
				<th>Time of Incident</th>
		  		<th>Reported Time</th>
		  		<th>Description</th>
		  		<th>Involvement</th>
		  		<th>Report Type</th>
		  		<th>Location</th>
		  		<th>Department</th>
		  		<th>Person</th>
		  		<th>Image</th>
		  		<?php  // set up page specific coluns =)
		  		if ($query == 'Received') { echo '<th>Mark as Open</th>';}
		  		 if ($query == 'Open') { echo 
		  		 '<th>View Comments</th>
		  		 <th>Add Comment & Close</th>';}
		  		 if ($query == 'All'){ echo '<th>' . 'Status'.'</th>';}
		  		 if ($query == 'Closed') { echo '<th>' . 'Comments'.'</th>
		  		 <th>Add Comment & Reopen</th>';}
		  		?>
		  	
		  	</tr>
			</thead>
			<tbody>
				<?php
				if(is_array($reports)){
					foreach($reports as $report){
					$imageURL = $report['photoPath'];
					$base = Path::base();

					$search = $base;
					$subDir = Path::subDirectory()."/";
					$replace = $subDir;
					
					$finalImageURL = str_replace($search, $replace, $imageURL);
					
					$date = substr($report['reportTime'], 0, -8);
					$time = substr($report['reportTime'], strlen($date));
					
					$dateI = substr($report['incidentTime'], 0, -8);
					$timeI = substr($report['incidentTime'], strlen($dateI));
					
						echo '<tr>'; // individual cells
						if ($showIP)
						{
						echo '<td>' . $report['ipAddress'] . '</td>'; /// SHOW OR HIDE THE IPADDRESS
						}
							
							// SHOW OR HIDE THE ios or andriod field
							if ($showOS)
							{
							echo '<td>' .$report['id'].'</td>';
								if ($report['isIOS']){
								echo '<td>' . 'iOS' . '</td>'; 
								}else {
								echo '<td>' . 'Android' . '</td>';
								}
								
							}
							echo '<td>' . date_format(date_create($dateI), 'm-d-Y') . '<br/> ' . date_format(date_create($timeI), 'g:i A') . '</td>' .
							'<td>' . date_format(date_create($date), 'm-d-Y'). '<br/> '  .date_format(date_create($time), 'g:i A')  . '</td>' .
							'<td>' . $report['description'] . '</td>';
							if($report['involvementDefault'] == 0)
							{
							echo '<td><u>Other</u> - ' . $report['involvementKind'] . '</td>';

							}
							else
							{
							echo '<td>' . $report['involvementKind'] . '</td>';
							}
							if($report['reportDefault'] == 0)
							{
								echo '<td><u>Other</u> - ' . $report['reportKind'] . '</td>';

							}
							else
							{
							echo '<td>' . $report['reportKind'] . '</td>';
							}
							echo '<td>' . // location cell
								$report['locationBuilding'] . '<br />' . 
								$report['locationRoom'] . 
							'</td>' .
							'<td>'. // department cell
								$report['department'] .
								'</td>' . 
							'<td>' .  // person cell
								$report['personName'] . '<br />' . 
								$report['personUsername'] . ' (' . $report['personKind'] . ')<br />' . 
								$report['personPhone'] . '</td>' .
								'<td >'; // image cell
								if ($imageURL != "")
								{
								 echo '<img class="lazy" id=reportImage alt="User submitted report photo '.$report['id'].'" src=' .$finalImageURL. ' height=200px />';
							// 	echo $base.$finalImageURL;
// 								 list($width, $height) = getimagesize($base.$finalImageURL);
// 								echo '(W, H) = ' . $width. ' ' . $height;
								}
								else { echo 'No Image'; } 
								 echo '</td>'; // end image cell
							
								if ($query == 'Received') {
								// mark as Received cell - review and add comment
									echo 
							'<td>'. 
							'<form onsubmit="return show_confirm_Open('.$report['id'].', \''.$report['description'].'\', \''.$query.'\');" action="addComment.php" method="POST">
								<input type="hidden"  name="id" value="'.$report['id'].'">
								<input type="hidden" name="action" value="Open">
								<input type="hidden" name="viewing" value="'.$query.'">
 								 <label for="'.$report['id'].'name3" id="'.$report['id'].'name5">Name:<br></label>
								<input type="text" name="name" id="'.$report['id'].'name3" size="40"><br>
								 <label for="'.$report['id'].'comment3" id="'.$report['id'].'comment5">Comment:<br></label>
								<textarea name="comment" id="'.$report['id'].'comment3" rows="3" cols="40"></textarea><br>
																<div id="'.$report['id'].'feedback3"></div><br>';

								
								echo '

								 
								<button type="submit" name="button" value="Open">Add Comment and Leave Open</button>
								<button type="submit" name="button" value="Closed"> Add Comment and Close</button>';
								echo '
								<script>
								function show_confirm_Open(ID, description, action) {
								    var name = document.getElementById(""+ID+"name3").value;
    								 var comment = document.getElementById(""+ID+"comment3").value;

								 	if ((name != "" && name.replace(/\s/g,"") != "") && (comment != "" && comment.replace(/\s/g,"") != "")) 
 									{
									 	if(window.confirm(""+name+" are you sure you want to add the comment, "+comment+", to the report with description: " + description + "?"))
									 	{
									 	}
									 	else { return false; }
 									}
 									else
 									{ 
 									if(name.trim() == ""){
 									document.getElementById(""+ID+"name5").innerHTML = "Name: <span style=\'color: red;\'> **Field cannot be empty</span>";
 									}else{
 									 document.getElementById(""+ID+"name5").innerHTML = "Name:";

 									}
 									if(comment.trim() == ""){
 									 document.getElementById(""+ID+"comment5").innerHTML = "Comment: <span style=\'color: red;\'> **Field cannot be empty</span>";

 									}else{
 									 	document.getElementById(""+ID+"comment5").innerHTML = "Comment:";

 									}
 									return false; } 
								}
								</script>
								
								<script>
								$(document).ready(function() {
    							var text_max = 250;
   								 $("#'.$report['id'].'feedback3").html(text_max + " characters remaining");

    								$("#'.$report['id'].'comment3").keyup(function() {
        						var text_length = $("#'.$report['id'].'comment3").val().length;
      							  var text_remaining = text_max - text_length;

      							  $("#'.$report['id'].'feedback3").html(text_remaining + " characters remaining");
  									  });
									});
								
								</script>
								
								
								</form></td>';
								
								
								}
								if($query == 'Closed' || $query == 'Open')
								{
								// show a button to view the comments
						
								echo '<td>';
									echo '<!-- Trigger/Open The Modal -->
									<button data-toggle="modal" data-id="comment'.$report['id'].'" data-target="commentModal" id="comment'.$report['id'].'">Comments</button>

									<!-- The Modal -->
							<div id="commentModal'.$report['id'].'" class="modal">

  						<!-- Modal content -->
 						 <div class="modal-content">
   						 <div class="modal-header">
     					 <button class="close" id="close'.$report['id'].'">X</button>
     						 <h2>  '; 
     						   if ($imageURL != "")
								{
								 echo '<img class="lazy" id=reportImage alt="User submitted report photo '.$report['id'].'" src=' .$finalImageURL. ' height=200px />';
								}
								else { //do nothing
								 } 
								echo '
        					'.$report['description'].'
     						</h2>
   							 </div>
   							 <div class="modal-body" id="modal'.$report['id'].'"> ';
      							
   							 		$db = new Database();

   							 	$sqlComment = "SELECT * from reportComment WHERE reportID = ".$report['id']." ORDER BY id DESC";
   							 	//echo $sqlComment;
								$reportCommentResults = $db->select($sqlComment);
								 echo '<div class="Table">
  									  <div class="Heading">
											<div class ="Cell">
  							  						<p>Date Time</p>
		  									</div> <!-- end cell -->
		  									<div class ="Cell">
		  											<p>Name</p>
		  									</div> <!-- end cell -->
		  									<div class ="Cell">
		  											<p>Comment</p>
		  									</div> <!-- end cell -->
		  							</div> <!-- end heading --> ';
   								 if(count($reportCommentResults) > 0){
   								 
   										foreach($reportCommentResults as $reportComment){
										$commentsql = "SELECT * FROM comments WHERE id =".$reportComment['commentID']." ORDER BY id";
										$commentResults = $db->select($commentsql);
										if(count($commentResults) > 0)
										{
										
											foreach($commentResults as $comment)
											{
												$date = substr($comment['commentDate'], 0, -8);
												$time = substr($comment['commentDate'], strlen($date));
					

												 echo '<div class="RowInfo">'.
 												'<div class="Cell"><p>' . date_format(date_create($date), 'm-d-Y'). '<br/> '  .date_format(date_create($time), 'g:i A')  . '</p></div>' .
 												'<div class="Cell"><p>'. $comment['name'].'</p></div> '.
 												'<div class="Cell"><p>'. $comment['comment'].'</p></div>'.
 												'</div> <!-- end row class --> ';
   							 				}
   							 			}
   							 			
   							 		}
   							 		
   							 		}
   							 		else
   							 		{
   							 		echo '<p>No comments ' .''/* $sqlComment */. '</p>';
   							 		}
   							 		echo '</div> <!-- end table --> </div>';
echo '
    
    <div class="modal-footer">
	<p>Comment View</p>
    </div></div></div>
    	<script>
		setModalFunctions("commentModal'.$report['id'].'", "comment'.$report['id'].'", "close'.$report['id'].'");
		</script>

 	';	
								echo'</td>'; // end the comment modal button cell
								
								
							
								// add comment but not resolve
								echo 
							'<td>'. 
							'<form onsubmit="return show_confirm_add_comment('.$report['id'].', \''.$report['description'].'\');" action="addComment.php" method="POST">
								<input type="hidden"  name="id" value="'.$report['id'].'">
								<input type="hidden" name="action" value="comment">
								<input type="hidden" name="viewing" value="'.$query.'">
 								 <label for="'.$report['id'].'name" id="'.$report['id'].'name6">Name:<br></label>
								<input type="text" name="name" id="'.$report['id'].'name" size="40"><br>
 								 <label for="'.$report['id'].'comment" id="'.$report['id'].'comment6">Comment:<br></label>
								<textarea name="comment" id="'.$report['id'].'comment" rows="3" cols="40" maxlength="250"></textarea>
								<div id="'.$report['id'].'feedback"></div><br>';
								if ($query == 'Open') 
								{
								echo '

								
								<button type="submit" name="button" value="Comment">Add Comment</button>
								<button type="submit" name="button" value="Closed"> Add Comment and Close</button>'
								;
								}
								if ($query == 'Closed')
								{
								echo '
								 
								<button type="submit" name="button" value="Comment">Add Comment</button>
								<button type="submit" name="button" value="Open"> Add Comment and Reopen</button>'
								;
								}
								echo '
								<script>
								function show_confirm_add_comment(ID, description) {
								    var name = document.getElementById(""+ID+"name").value;
    								 var comment = document.getElementById(""+ID+"comment").value;

								 	if ((name != "" && name.replace(/\s/g,"") != "") && (comment != "" && comment.replace(/\s/g,"") != ""))
 									{
									 	if(window.confirm(""+name+" are you sure you want to add the comment, "+comment+", to report with description: " + description + " ?"))
									 	{
									 	
									 	}
									 	else { 
									 	return false; }
 									}
 									else
 									{ 
 									if(name == "" || name.replace(/\s/g,"") == ""){
 									document.getElementById(""+ID+"name6").innerHTML = "Name: <span style=\'color: red;\'> **Field cannot be empty</span>";
 									}else{
 									 document.getElementById(""+ID+"name6").innerHTML = "Name:";

 									}
 									if(comment == "" || comment.replace(/\s/g,"") == ""){
 									 document.getElementById(""+ID+"comment6").innerHTML = "Comment: <span style=\'color: red;\'> **Field cannot be empty</span>";

 									}else{
 									 	document.getElementById(""+ID+"comment6").innerHTML = "Comment:";

 									}
 									return false; } 
								}
								</script>
								<script>
								$(document).ready(function() {
    							var text_max = 250;
   								 $("#'.$report['id'].'feedback").html(text_max + " characters remaining");

    								$("#'.$report['id'].'comment").keyup(function() {
        						var text_length = $("#'.$report['id'].'comment").val().length;
      							  var text_remaining = text_max - text_length;

      							  $("#'.$report['id'].'feedback").html(text_remaining + " characters remaining");
  									  });
									});
								
								</script>
								
								</form></td>';
							
								}
								if ($query == 'All')
								{
								echo '<td>' . $report['status'].'</td>';
								}
								
							echo '</tr>';// end table row
					}// for each
				} //if 
				?>
			</tbody>  	
		</table>
	</div>
<script language="javascript" type="text/javascript">
var table1_Props = {
	col_3: "select",
    col_4: "select",
    col_8: "none",
    col_9: "none",
    col_10: "none",
    
    display_all_text: " [ Show all ] ",
    sort_select: true,
    on_change: true,
	btn_reset: true

};
jQuery(document).ready(function () {
  // your code here
  var tf = setFilterGrid("tableMain", table1_Props);

});
</script>
	</div> <!~~ end col ~~>
</div> <!~~ end row ~~>

 <?php include('footer.php'); ?> 
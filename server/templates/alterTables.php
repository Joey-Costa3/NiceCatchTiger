<?php
	require_once(dirname(dirname(__FILE__)) . '/models/config.php');

		$path = Path::externalRoot().Path::subDirectory();
		//$path = "http://".$path;
 		$id = $_POST["id"];	 // the id of the item to be modified	
		$action = $_POST["action"]; // what are we doing to this (Add, Edit or Delete)
		$table = $_POST["table"]; // to which table is this being done
		echo $table .'- name of table <br>';
		$name = $_POST['name'];  // a name - used for editing or adding departments, involvements, buildings
		$db = new Database();

		date_default_timezone_set('GMT');
		if($table == "Departments") //departments
		{
		echo 'You were at Departments<br>'; 
				if($action == "Add")
				{
				echo 'You were trying to add a department<br>';
					$sql = "INSERT INTO departments (departmentName, username, date_created, date_updated) VALUES (?,?,now(),now())";
					$sql = $db->prepareQuery($sql, $name, $_SERVER['PHP_AUTH_USER']);
					$db->query($sql);
					echo 'Succesfully Added a department - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);				
				}
				if($action == "Edit")
				{
					echo 'You were trying to edit a department<br>';
					$sql = "UPDATE departments SET departmentName=?, username=?, date_updated=now() WHERE id=?";
					$sql = $db->prepareQuery($sql, $name, $_SERVER['PHP_AUTH_USER'], $id);
					$db->query($sql);
					echo 'Succesfully edited department - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);

				}
				else if ($action == "Delete")
				{
					echo 'You were trying to delete a department<br>';
					$sql = "DELETE FROM departments WHERE id=?";
					$sql = $db->prepareQuery($sql, $id);
					$db->query($sql);
					echo 'Succesfully deleted department - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);
					
				}
				else 
				{
				echo '<br>Error in modifying Departments. Please go back in your browser and try again. <br>
 					If this error continues to show up please contact support.';
				}
		}
		else if($table == "Involvements") // involvements
		{
		echo 'You were at Involvement<br>'; 
				if($action == "Add")
				{
				echo 'You were trying to add a involvement<br>';
					$sql = "INSERT INTO involvementKinds (involvementKind, `default`, username, date_created, date_updated) VALUES (?,?,?,now(), now())";
					$default = 1;
					$sql = $db->prepareQuery($sql, $name, $default, $_SERVER['PHP_AUTH_USER']);
					$db->query($sql);
					echo 'Succesfully Added a involvement kind - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);				
				}
				if($action == "Edit")
				{
					echo 'You were trying to edit a involvement<br>';
					$sql = "UPDATE involvementKinds SET involvementKind=?, username=?, date_updated=now() WHERE id=?";
					$sql = $db->prepareQuery($sql, $name, $_SERVER['PHP_AUTH_USER'], $id);
					$db->query($sql);
					echo 'Succesfully edited involvement - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);

				}
				else if ($action == "Delete")
				{
					echo 'You were trying to delete a involvement<br>';
					$sql = "DELETE FROM involvementKinds WHERE id=?";
					$sql = $db->prepareQuery($sql, $id);
					$db->query($sql);
					echo 'Succesfully deleted involvement - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);
					
				}
				else 
				{
				echo '<br>Error in modifying Involvement. Please go back in your browser and try again. <br>
 					If this error continues to show up please contact support.';
				}
			}
			else if($table == "Buildings")  // buildings
			{
			echo 'You were at Buildings<br>'; 
				if($action == "Add")
				{
				echo 'You were trying to add a building<br>';
					$sql = "INSERT INTO buildings (buildingName, username, date_created, date_updated) VALUES (?,?,now(),now())";
					$sql = $db->prepareQuery($sql, $name, $_SERVER['PHP_AUTH_USER']);
					echo 'here';
					$db->query($sql);
					
					echo 'Succesfully Added a building - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);				
				}
				if($action == "Edit")
				{
					echo 'You were trying to edit a building<br>';
					$sql = "UPDATE buildings SET buildingName=?, username=?, date_updated=now() WHERE id=?";
					$sql = $db->prepareQuery($sql, $name, $_SERVER['PHP_AUTH_USER'], $id);
					$db->query($sql);
					echo 'Succesfully edited building - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);

				}
				else if ($action == "Delete")
				{
					echo 'You were trying to delete a building<br>';
					$sql = "DELETE FROM buildings WHERE id=?";
					$sql = $db->prepareQuery($sql, $id);
					$db->query($sql);
					echo 'Succesfully deleted building - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);
					
				}
				else 
				{
				echo '<br>Error in modifying building. Please go back in your browser and try again. <br>
 					If this error continues to show up please contact support.';
				}
			}
			else if($table == "ReportTypes")
			{
			echo 'You were at ReportTypes<br>'; 
				if($action == "Add")
				{
				echo 'You were trying to add a report type<br>';
					$sql = "INSERT INTO reportKinds (reportKind, `default`, username, date_created, date_updated) VALUES (?,?,?,now(), now())";
					$default = 1;
					$sql = $db->prepareQuery($sql, $name, $default, $_SERVER['PHP_AUTH_USER']);
					$db->query($sql);
					echo 'Succesfully Added a report type - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);				
				}
				if($action == "Edit")
				{
					echo 'You were trying to edit a report type<br>';
					$sql = "UPDATE reportKinds SET reportKind=?, username=?, date_updated=now() WHERE id=?";
					$sql = $db->prepareQuery($sql, $name,$_SERVER['PHP_AUTH_USER'], $id);
					$db->query($sql);
					echo 'Succesfully edited report type - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);

				}
				else if ($action == "Delete")
				{
					echo 'You were trying to delete a report type<br>';
					$sql = "DELETE FROM reportKinds WHERE id=?";
					$sql = $db->prepareQuery($sql, $id);
					$db->query($sql);
					echo 'Succesfully deleted report type - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);
					
				}
				else 
				{
				echo '<br>Error in modifying report type. Please go back in your browser and try again. <br>
 					If this error continues to show up please contact support.';
				}
			}
			else if($table == "PersonKinds")
			{
			echo 'You were at PersonKinds<br>'; 
				if($action == "Add")
				{
				echo 'You were trying to add a person kind<br>';
					$sql = "INSERT INTO personKinds (personKind, `default`, username, date_created, date_updated) VALUES (?,?,?,now(), now())";
					$default = 1;
					$sql = $db->prepareQuery($sql, $name, $default, $_SERVER['PHP_AUTH_USER']);
					$db->query($sql);
					echo 'Succesfully Added a person kind - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);				
				}
				if($action == "Edit")
				{
					echo 'You were trying to edit a person kind<br>';
					$sql = "UPDATE personKinds SET personKind=?, username=?, date_updated=now() WHERE id=?";
					$sql = $db->prepareQuery($sql, $name, $_SERVER['PHP_AUTH_USER'], $id);
					$db->query($sql);
					echo 'Succesfully edited person kind - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);

				}
				else if ($action == "Delete")
				{
					echo 'You were trying to delete a person kind<br>';
					$sql = "DELETE FROM personKinds WHERE id=?";
					$sql = $db->prepareQuery($sql, $id);
					$db->query($sql);
					echo 'Succesfully deleted personKind - returning to page';
					header("Location: ".$path."/templates/authorTool.php?".ucfirst($table)."", true);
					
				}
				else 
				{
				echo '<br>Error in modifying personKind. Please go back in your browser and try again. <br>
 					If this error continues to show up please contact support.';
				}
			}
		
		
		
		
		?>
<html lang="en">

 <?php include('authorHeader.php'); ?>

<body>

    <!-- Begin page content -->
    <div class="containerC">


<?php
	require_once(dirname(dirname(__FILE__)) . '/models/config.php');
	

?>
<div>
			<h1> Nice Catch Tiger! Authoring Tool  </h1><br>




<?php 

			    	$query = $_SERVER['QUERY_STRING'];
			    	if ($query == "Buildings") // *****************************************
			    	{
			    			// display table name and button to add items
 					echo '<h2>'.$query.' </h2> 
					<!-- Trigger/Open The Modal -->
									<button data-toggle="modalAdd" data-id="addBuild" data-target="addBuild" id="addBuild">Add</button>

									<!-- The Modal -->
							<div id="addBuildModal" class="modal">

  						<!-- Modal content -->
 						 <div class="modal-content">
   						 <div class="modal-header">
     					 <button class="close" id="closeAddBuilding">X</button>
     						 <h2>Adding New Building </h2>
   							 </div>
   							 <div class="modal-body"> 
   							 <h3>Enter a name for the new building</h3>
   							 <form action="alterTables.php" method="POST">
   							 <input type="hidden" name="id" value="0">
   							 <label for="newBuilding">Add new building</label>
   							 <input type="text" name="name" size="100" maxlength="250" id="newBuilding">
   							 <input type="hidden" name="table" value="'.$query.'">
   							 <button type="submit" name="action" value="Add" id="submitNewB">Confirm
   							 </form>
   							 </div>';
      							
   							 	
echo '
    
    <div class="modal-footer">
	<p>Adding Building</p>
    </div>
     	<script type="text/javascript" language="javascript">
			setModalFunctions("addBuildModal", "addBuild", "closeAddBuilding");
			setAccessibilityControls("closeAddBuilding", "newBuilding", "submitNewB");
		</script>
	</div>
 </div>			
 					<br>';
					 $buildings = new Building();
					 $reports = $buildings->retrieve();
					// echo '<br>Count of buildings '.count($reports).'<br>';
					
					 echo'<div style="overflow-x:auto;"><table id="buildings" class="table table-striped padding">';
					 echo '	<thead>
				<tr>
				<th>Name</th>
				<th width="50px">Update</th>
				<th width="20px">Delete</th>
		  	</tr>
			</thead>';
			echo '<tbody>';
			if(is_array($reports)){
					foreach($reports as $report){
					echo '<tr>';
					echo '<td>'.$report['buildingName'].'</td>';
					
					
				/////// ---------------------------------------------- edit modal

					echo '<td>
					
					<!-- Trigger/Open The Modal -->
									<button data-toggle="modal" data-id="editBuild'.$report['id'].'" data-target="editBuild" id="editBuild'.$report['id'].'">Edit</button>

									<!-- The Modal -->
							<div id="editBuildModal'.$report['id'].'" class="modal">

  						<!-- Modal content -->
 						 <div class="modal-content">
   						 <div class="modal-header">
     					 <button class="close" id="close'.$report['id'].'">X</button>
     						 <h2>'.$report['buildingName'].'</h2>
   							 </div>
   							 <div class="modal-body"> 
   							 <h3>What would you like to rename this building to?</h3>
   							 <form action="alterTables.php" method="POST">
   							 <input type="hidden" name="id" value="'.$report['id'].'">
   							 <label for="renameBuilding'.$report['id'].'">Rename building</label>
   							 <input type="text" name="name" size="100" maxlength="250" id="renameBuilding'.$report['id'].'">
   							 <input type="hidden" name="table" value="'.$query.'">
   							 <button type="submit" name="action" value="Edit" id="submitrenameBuilding'.$report['id'].'">Confirm
   							 </form>
   							 </div>';
      							
   							 	
echo '
    
    <div class="modal-footer">
	<p>Editing Building</p>
    </div>
    	 <script>
			setModalFunctions("editBuildModal'.$report['id'].'", "editBuild'.$report['id'].'", "close'.$report['id'].'");
			setAccessibilityControls("close'.$report['id'].'", "renameBuilding'.$report['id'].'", "submitrenameBuilding'.$report['id'].'");
		</script>
</td>
 	';	
					
					/////////-------------------------------------------- end edit modal
					echo '<th class="center">
					<form onsubmit="return show_confirm('.$report['id'].', \''.$report['buildingName'].'\');" action="alterTables.php" method="POST">
					<input type="hidden"  name="id" value="'.$report['id'].'">
					<input type="hidden" name="table" value="'.$query.'">
					<button type="submit" class="empty" name="action" value="Delete">x
					<script>
								function show_confirm(ID, buildingName) {
					
									 	if(window.confirm("Are you sure you want to delete \"" + buildingName + "\"?"))
									 	{
									 	
									 	}
									 	else { return false; }
 						
								}
								</script>
					</form>
					</th>';
					echo '</tr>';
					}
				}
			echo'</tbody></table></div>';
			    	}
			    	
 					else if ($query == 'Departments') // *****************************************
 					{
 					// display table name and button to add items
 					echo '<h2>Departments </h2> 
					<!-- Trigger/Open The Modal -->
									<button data-toggle="modalAdd" data-id="addDepart" data-target="addDepart" id="addDepart">Add</button>

									<!-- The Modal -->
							<div id="addDepartModal" class="modal">

  						<!-- Modal content -->
 						 <div class="modal-content">
   						 <div class="modal-header">
     					 <button class="close" id="closeAddDepartment">X</button>
     						 <h2>Adding New Department</h2>
   							 </div>
   							 <div class="modal-body"> 
   							 <h3>Enter a name for the new department</h3>
   							 <form action="alterTables.php" method="POST">
   							 <input type="hidden" name="id" value="0">
   							 <label for="addDepartment">Add new department</label>
   							 <input type="text" name="name" size="100" maxlength="250" id="addDepartment">
   							 <input type="hidden" name="table" value="'.$query.'">
   							 <button type="submit" name="action" value="Add" id="submitNewD">Confirm
   							 </form>
   							 </div>';
      							
   							 	
echo '
    
    <div class="modal-footer">
	<p>Adding Department</p>
    </div>
   		 <script>
			setModalFunctions("addDepartModal", "addDepart", "closeAddDepartment");
			setAccessibilityControls("closeAddDepartment", "addDepartment", "submitNewD");
		</script>
	</div>
 </div>			
 					<br>';
					 $departments = new Department();
					 $reports = $departments->retrieve();
					// echo '<br>Count of departments '.count($reports).'<br>';
					 echo'<div style="overflow-x:auto;"><table id="departments" class="table table-striped padding">';
					 echo '	<thead>
				<tr>
				<th>Name</th>
				<th width="50px">Update</th>
				<th width="20px">Delete</th>
		  	</tr>
			</thead>';
			echo '<tbody>';
			if(is_array($reports)){
					foreach($reports as $report){
					echo '<tr>';
					echo '<td>'.$report['departmentName'].'</td>';
				/////// ---------------------------------------------- edit modal

					echo '<td>
					
					<!-- Trigger/Open The Modal -->
									<button data-toggle="modal" data-id="editDepart'.$report['id'].'" data-target="editDepart" id="editDepart'.$report['id'].'">Edit</button>

									<!-- The Modal -->
							<div id="editDepartModal'.$report['id'].'" class="modal">

  						<!-- Modal content -->
 						 <div class="modal-content">
   						 <div class="modal-header">
     					 <button class="close" id="close'.$report['id'].'">X</button>
     						 <h2>'.$report['departmentName'].'</h2>
   							 </div>
   							 <div class="modal-body"> 
   							 <h3>What would you like to rename this department to?</h3>
   							 <form action="alterTables.php" method="POST">
   							 <input type="hidden" name="id" value="'.$report['id'].'">
   							 <label for="renameDepartment'.$report['id'].'">Rename department</label>
   							 <input type="text" name="name" size="100" maxlength="250" id="renameDepartment'.$report['id'].'">
   							 <input type="hidden" name="table" value="'.$query.'">
   							 <button type="submit" name="action" value="Edit" id="submitrenameDepartment'.$report['id'].'">Confirm
   							 </form>
   							 </div>';
      							
   							 	
echo '
    
    <div class="modal-footer">
	<p>Editing Department</p>
    </div>
     	<script>
			setModalFunctions("editDepartModal'.$report['id'].'", "editDepart'.$report['id'].'", "close'.$report['id'].'");
			setAccessibilityControls("close'.$report['id'].'", "renameDepartment'.$report['id'].'", "submitrenameDepartment'.$report['id'].'");
		</script>
</td>
 	';	
					
					/////////-------------------------------------------- end edit modal
					echo '<th class="center">
					<form onsubmit="return show_confirm('.$report['id'].', \''.$report['departmentName'].'\');" action="alterTables.php" method="POST">
					<input type="hidden"  name="id" value="'.$report['id'].'">
					<input type="hidden" name="table" value="'.$query.'">
					<button type="submit" class="empty" name="action" value="Delete">x
					<script>
								function show_confirm(ID, departmentName) {
					
									 	if(window.confirm("Are you sure you want to delete \"" + departmentName + "\" ?"))
									 	{
									 	
									 	}
									 	else { return false; }
 						
								}
								</script>
					</form>
					</th>';
					echo '</tr>';
					}
				}
			echo'</tbody></table></div>';
					}		
					else  if ($query == 'Involvements')  // *****************************************
					 {
					// display table name and button to add items
 					echo '<h2>'.$query.' </h2> 
					<!-- Trigger/Open The Modal -->
									<button data-toggle="modalAdd" data-id="addInvolve" data-target="addInvolve" id="addInvolve">Add</button>

									<!-- The Modal -->
							<div id="addInvolveModal" class="modal">

  						<!-- Modal content -->
 						 <div class="modal-content">
   						 <div class="modal-header">
     					 <button class="close" id="closeAddInvolvement">X</button>
     						 <h2>Adding New Involvement Kind</h2>
   							 </div>
   							 <div class="modal-body"> 
   							 <h3>Enter a name for the new involvement</h3>
   							 <form action="alterTables.php" method="POST">
   							 <input type="hidden" name="id" value="0">
   							 <label for="addInvolvement">Add new involvement kind</label>
   							 <input type="text" name="name" size="100" maxlength="250" id="addInvolvement">
   							 <input type="hidden" name="table" value="'.$query.'">
   							 <button type="submit" name="action" value="Add" id="submitNewI">Confirm
   							 </form>
   							 </div>';
      							
   							 	
echo '
    
    <div class="modal-footer">
	<p>Adding Involvment Kind</p>
    </div>
    	 <script>
			setModalFunctions("addInvolveModal", "addInvolve", "closeAddInvolvement");
			setAccessibilityControls("closeAddInvolvement", "addInvolvement", "submitNewI");
		</script>
	</div>
 </div>			
 					<br>';
					 $involvements = new InvolvementKind();
					 $reports = $involvements->retrieve();
					// echo '<br>Count of involvements '.count($reports).'<br>';
					 echo'<div style="overflow-x:auto;"><table id="involvements" class="table table-striped padding">';
					 echo '	<thead>
				<tr>
				<th>Name</th>
				<th width="50px">Update</th>
				<th width="20px">Delete</th>
		  	</tr>
			</thead>';
			echo '<tbody>';
			if(is_array($reports)){
					foreach($reports as $report){
					echo '<tr>';
					if ($report['default'] == 1)
					{
					echo '<td>'.$report['involvementKind'].'</td>';
					}
					else
					{
					echo '<td class="userSubmitted">'.$report['involvementKind'].'</td>';

					}
				/////// ---------------------------------------------- edit modal

					echo '<td>
					
					<!-- Trigger/Open The Modal -->
									<button data-toggle="modal" data-id="editInvolve'.$report['id'].'" data-target="editInvolve" id="editInvolve'.$report['id'].'">Edit</button>

									<!-- The Modal -->
							<div id="editInvolveModal'.$report['id'].'" class="modal">

  						<!-- Modal content -->
 						 <div class="modal-content">
   						 <div class="modal-header">
     					 <button class="close" id="close'.$report['id'].'">X</button>
     						 <h2>'.$report['involvementKind'].'</h2>
   							 </div>
   							 <div class="modal-body"> 
   							 <h3>What would you like to rename this involvement kind to?</h3>
   							 <form action="alterTables.php" method="POST">
   							 <input type="hidden" name="id" value="'.$report['id'].'">
   							 <label for="renameInvolvement'.$report['id'].'">Rename involvement kind</label>
   							 <input type="text" name="name" size="100" maxlength="250" id="renameInvolvement'.$report['id'].'">
   							 <input type="hidden" name="table" value="'.$query.'">
   							 <button type="submit" name="action" value="Edit" id="submitrenameInvolvement'.$report['id'].'">Confirm
   							 </form>
   							 </div>';
      							
   							 	
echo '
    
    <div class="modal-footer">
	<p>Editing Involvement</p>
    </div>
     	<script>
			setModalFunctions("editInvolveModal'.$report['id'].'", "editInvolve'.$report['id'].'",  "close'.$report['id'].'");
			setAccessibilityControls("close'.$report['id'].'", "renameInvolvement'.$report['id'].'", "submitrenameInvolvement'.$report['id'].'");
		</script>
</td>
 	';	
					
					/////////-------------------------------------------- end edit modal
					echo '<th class="center">
					<form onsubmit="return show_confirm('.$report['id'].', \''.$report['involvementKind'].'\');" action="alterTables.php" method="POST">
					<input type="hidden"  name="id" value="'.$report['id'].'">
					<input type="hidden" name="table" value="'.$query.'">
					<button type="submit" class="empty" name="action" value="Delete">x
					<script>
								function show_confirm(ID, involvementKind) {
					
									 	if(window.confirm("Are you sure you want to delete \"" + involvementKind + "\" ?"))
									 	{
									 	
									 	}
									 	else { return false; }
 						
								}
								</script>
					</form>
					</th>';
					echo '</tr></div>';
					}
				}
			echo'</tbody></table></div>';
			    	}
					 else if ($query == 'PersonKinds')// *****************************************
 					{
					// display table name and button to add items
 					echo '<h2>Person Kinds </h2> 
					<!-- Trigger/Open The Modal -->
									<button data-toggle="modalAdd" data-id="addPersonKind" data-target="addPersonKind" id="addPersonKind">Add</button>

									<!-- The Modal -->
							<div id="addPersonKindModal" class="modal">

  						<!-- Modal content -->
 						 <div class="modal-content">
   						 <div class="modal-header">
     					 <button class="close" id="closeAddPersonKind">X</button>
     						 <h2>Adding New Person Kind </h2>
   							 </div>
   							 <div class="modal-body"> 
   							 <h3>Enter a name for the new person kind</h3>
   							 <form action="alterTables.php" method="POST">
   							 <input type="hidden" name="id" value="0">
   							 <label for="addPersonKind">Add person kind</label>
   							 <input type="text" name="name" size="100" maxlength="250" id="addPersonKind">
   							 <input type="hidden" name="table" value="'.$query.'">
   							 <button type="submit" name="action" value="Add" id="submitNewP">Confirm
   							 </form>
   							 </div>';
      							
   							 	
echo '
    
    <div class="modal-footer">
	<p>Adding Person Kind</p>
    </div>
     	<script>
			setModalFunctions("addPersonKindModal", "addPersonKind", "closeAddPersonKind");
			setAccessibilityControls("closeAddPersonKind", "addPersonKind", "submitNewP");
		</script>
	</div>
 </div>			
 					<br>';
					 $personKinds = new PersonKind();
					 $reports = $personKinds->retrieve();
					// echo '<br>Count of reportTypes '.count($reports).'<br>';
					 echo'<div style="overflow-x:auto;"><table id="personKind" class="table table-striped padding">';
					 echo '	<thead>
				<tr>
				<th>Name</th>
				<th width="50px">Update</th>
				<th width="20px">Delete</th>
		  	</tr>
			</thead>';
			echo '<tbody>';
			if(is_array($reports)){
					foreach($reports as $report){
					echo '<tr>';
					if ($report['default'] == 1)
					{
					echo '<td>'.$report['personKind'].'</td>';
					}
					else
					{
					echo '<td class="userSubmitted">'.$report['personKind'].'</td>';

					}
					
				/////// ---------------------------------------------- edit modal

					echo '<td>
					
					<!-- Trigger/Open The Modal -->
									<button data-toggle="modal" data-id="editPersonKind'.$report['id'].'" data-target="editPersonKind" id="editPersonKind'.$report['id'].'">Edit</button>

									<!-- The Modal -->
							<div id="editPersonKindModal'.$report['id'].'" class="modal">

  						<!-- Modal content -->
 						 <div class="modal-content">
   						 <div class="modal-header">
     					 <button class="close" id="close'.$report['id'].'">X</button>
     						 <h2>'.$report['personKind'].'</h2>
   							 </div>
   							 <div class="modal-body"> 
   							 <h3>What would you like to rename this person kind to?</h3>
   							 <form action="alterTables.php" method="POST">
   							 <input type="hidden" name="id" value="'.$report['id'].'">
   							 <label for="renamePersonKind'.$report['id'].'">Rename person kind</label>
   							 <input type="text" name="name" size="100" maxlength="250" id="renamePersonKind'.$report['id'].'">
   							 <input type="hidden" name="table" value="'.$query.'">
   							 <button type="submit" name="action" value="Edit" id="submitrenamePersonKind'.$report['id'].'">Confirm
   							 </form>
   							 </div>';
      							
   							 	
echo '
    
    <div class="modal-footer">
	<p>Editing Person Kind</p>
    </div>
     	<script>
			setModalFunctions("editPersonKindModal'.$report['id'].'", "editPersonKind'.$report['id'].'", "close'.$report['id'].'");
			setAccessibilityControls("close'.$report['id'].'", "renamePersonKind'.$report['id'].'", "submitrenamePersonKind'.$report['id'].'");
		</script>
</td>
 	';	
					
					/////////-------------------------------------------- end edit modal
					echo '<th class="center">
					<form onsubmit="return show_confirm('.$report['id'].', \''.$report['personKind'].'\');" action="alterTables.php" method="POST">
					<input type="hidden"  name="id" value="'.$report['id'].'">
					<input type="hidden" name="table" value="'.$query.'">
					<button type="submit" class="empty" name="action" value="Delete">x
					<script>
								function show_confirm(ID, personKind) {
					
									 	if(window.confirm("Are you sure you want to delete \"" + personKind + "\"?"))
									 	{
									 	
									 	}
									 	else { return false; }
 						
								}
								</script>
					</form>
					</th>';
					echo '</tr>';
					}
				}
			echo'</tbody></table></div>';
			    	}
 					else if($query == 'ReportTypes')// *****************************************
					 {
						// display table name and button to add items
						
 					echo '<h2>Report Types </h2> 
 					
					<!-- Trigger/Open The Modal -->
									<button data-toggle="modalAdd" data-id="addReportType" data-target="addReportType" id="addReportType">Add</button>

									<!-- The Modal -->
							<div id="addReportTypeModal" class="modal">

  						<!-- Modal content -->
 						 <div class="modal-content">
   						 <div class="modal-header">
     					 <button class="close" id="closeAddRT">X</button>
     						 <h2>Adding New Report Type </h2>
   							 </div>
   							 <div class="modal-body"> 
   							 <h3>Enter a name for the new report type</h3>
   							 <form action="alterTables.php" method="POST">
   							 <input type="hidden" name="id" value="0">
   							 <label for="addNewReportType">Add new report type</label>
   							 <input type="text" name="name" size="100" maxlength="250" id="addNewReportType">
   							 <input type="hidden" name="table" value="'.$query.'">
   							 <button type="submit" name="action" value="Add" id="submitNewR">Confirm
   							 </form>
   							 </div>
   							 	
   							 ';
      							
   							 	
echo '
    
    <div class="modal-footer">
	<p>Adding Report Type</p>
    </div>
     <script>
			setModalFunctions("addReportTypeModal", "addReportType", "closeAddRT");
			setAccessibilityControls("closeAddRT", "addReportType", "submitNewR");
	 </script>
	</div>
 </div>			
 					<br>';
					 $reportTypes = new ReportKind();
					 $reports = $reportTypes->retrieve();
					// echo '<br>Count of reportTypes '.count($reports).'<br>';
					 echo'<div style="overflow-x:auto;"><table id="reportTypes" class="table table-striped padding">';
					 echo '	<thead>
				<tr>
				<th>Name</th>
				<th width="50px">Update</th>
				<th width="20px">Delete</th>
		  	</tr>
			</thead>';
			echo '<tbody>';
			if(is_array($reports)){
					foreach($reports as $report){
					echo '<tr>';
					if ($report['default'] == 1)
					{
					echo '<td>'.$report['reportKind'].'</td>';
					}
					else
					{
					echo '<td class="userSubmitted">'.$report['reportKind'].'</td>';

					}
					
				/////// ---------------------------------------------- edit modal

					echo '<td>
					
					<!-- Trigger/Open The Modal -->
									<button data-toggle="modal" data-id="editReportType'.$report['id'].'" data-target="editReportType" id="editReportType'.$report['id'].'">Edit</button>

									<!-- The Modal -->
							<div id="editReportTypeModal'.$report['id'].'" class="modal">

  						<!-- Modal content -->
 						 <div class="modal-content">
   						 <div class="modal-header">
     					 <button class="close" id="close'.$report['id'].'">X</button>
     						 <h2>'.$report['reportKind'].'</h2>
   							 </div>
   							 <div class="modal-body"> 
   							 <h3>What would you like to rename this report type to?</h3>
   							 <form action="alterTables.php" method="POST">
   							 <input type="hidden" name="id" value="'.$report['id'].'">
   							 <label for="renameReportType'.$report['id'].'">Rename report type</label>
   							 <input type="text" name="name" size="100" maxlength="250" id="renameReportType'.$report['id'].'">
   							 <input type="hidden" name="table" value="'.$query.'">
   							 <button type="submit" name="action" value="Edit" id="submitrenameReportType'.$report['id'].'">Confirm
   							 </form>
   							 </div>';
      							
   							 	
echo '
    
    <div class="modal-footer">
	<p>Editing Report Type</p>
    </div>
     	<script>
			setModalFunctions("editReportTypeModal'.$report['id'].'", "editReportType'.$report['id'].'", "close'.$report['id'].'");
			setAccessibilityControls("close'.$report['id'].'","renameReportType'.$report['id'].'", "submitrenameReportType'.$report['id'].'");
		</script>
</td>
 	';	
					
					/////////-------------------------------------------- end edit modal
					echo '<th class="center">
					<form onsubmit="return show_confirm('.$report['id'].', \''.$report['reportKind'].'\');" action="alterTables.php" method="POST">
					<input type="hidden"  name="id" value="'.$report['id'].'">
					<input type="hidden" name="table" value="'.$query.'">
					<button type="submit" class="empty" name="action" value="Delete">x
					<script>
								function show_confirm(ID, reportKind) {
					
									 	if(window.confirm("Are you sure you want to delete \"" + reportKind + "\"?"))
									 	{
									 	
									 	}
									 	else { return false; }
 						
								}
								</script>
					</form>
					</th>';
					echo '</tr>';
					}
				}
			echo'</tbody></table></div>';
			    	}
			    	else // *****************************************
			    	{
			    		echo '<p> This page has nothing on it. Please click a valid page above</p>';
				//header("Location: https://people.cs.clemson.edu/~jacosta/templates/authorTool.php?Buildings", true);

			    	}
			    	?>
 <!~~ in .container div ~~>

			</div>
<br><br>
</div>
 <?php include('footer.php'); ?> 
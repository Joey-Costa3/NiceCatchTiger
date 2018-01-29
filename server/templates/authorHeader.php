<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>NiceCatch Admin</title>
	<!-- Bootstrap -->
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

	<!-- my stylesheet -->
	<link rel='stylesheet' href='mainstyle.css' />
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?"/>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	
		<script type="text/javascript" language="javascript" src="tablefilter.js"></script> 	
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script> 
	
	
	<script type="text/javascript" language="javascript">

function setAccessibilityControls(firstInputID, secondInputID, thirdInputID){
	var firstInput = document.getElementById(firstInputID); // will be the close button
	var secondInput = document.getElementById(secondInputID); // will be the text input
	var thirdInput = document.getElementById(thirdInputID); // will be the submit button
	
	/*redirect last tab to first input*/
	thirdInput.onkeydown = function (e) {
 	  if ((e.which === 9 && !e.shiftKey)) {
  	     firstInput.focus();
  	     return false; // prevent the keypress (we already moved the focus)
  	 }
	};

	/*redirect first shift+tab to last input*/
	firstInput.onkeydown = function (e) {
	    if ((e.which === 9 && e.shiftKey)) {
	    	 thirdInput.focus();
	    	 return false; // prevent the keypress (we already moved the focus)
	    }
	};

	

}

</script>
<script type="text/javascript" language="javascript">
function setModalFunctions(modalID, showBtnID, spanElementID){
// get the modal
var modal = document.getElementById(modalID);

// Get the button that opens the modal
var btn = document.getElementById(showBtnID);

// Get the <span> element that closes the modal
var span = document.getElementById(spanElementID);

// When the user clicks on the button, open the modal 
btn.onclick = function() {
    modal.style.display = "block";
    span.focus();
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
// $(window).onclick = function(event) {
//     if (event.target == modal) {
//         modal.style.display = "none";
//     }
// }

// esc key close modal
$(document).keyup(function(e) {
     if (e.keyCode == 27) { // escape key maps to keycode `27`
        modal.style.display = "none";
    }
});




}

</script>



    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				    <span class="sr-only">Toggle navigation</span>
				    <span class="icon-bar"></span>
				    <span class="icon-bar"></span>
				    <span class="icon-bar"></span>
				</button>
			  	<a class="navbar-brand" href="">Nice Catch Tiger! <img id='paw' src='tigerpaw-64.png' width='24px' alt='Clemson Tiger Paw'/> </a>
			</div>
			<div id="navbar" class="collapse navbar-collapse">
			  	<ul class="nav navbar-nav navbar-right">
			    	<li><a href="page.php?All">Web Tool</a></li>
			    	<?php 
			    	$query = $_SERVER['QUERY_STRING'];
 					if ($query == 'Departments')
 					{
					echo '<li><a href="?Buildings">Buildings</a></li>
			    	<li class="Active"><a href="?Departments">Departments</a></li>
			    	<li><a href="?Involvements">Involvements</a></li>
			    	<li><a href="?PersonKinds">PersonKinds</a></li>
			    	<li><a href="?ReportTypes">ReportTypes</a></li>			    	' ;
					}		
					else  if ($query == 'Involvements')
					 {
					echo '<li><a href="?Buildings">Buildings</a></li>
			    	<li><a href="?Departments">Departments</a></li>
			    	<li class="Active"><a href="?Involvements">Involvements</a></li>
			    	<li><a href="?PersonKinds">PersonKinds</a></li>
			    	<li><a href="?ReportTypes">ReportTypes</a></li>			    	' ;
			    	}
					 else if ($query == 'PersonKinds')
 					{
					echo '<li><a href="?Buildings">Buildings</a></li>
			    	<li><a href="?Departments">Departments</a></li>
			    	<li><a href="?Involvements">Involvements</a></li>
			    	<li class="Active"><a href="?PersonKinds">PersonKinds</a></li>
			    	<li><a href="?ReportTypes">ReportTypes</a></li>			    	' ;
			    	}
 					else if($query == 'ReportTypes')
					 {
					echo '<li><a href="?Buildings">Buildings</a></li>
			    	<li><a href="?Departments">Departments</a></li>
			    	<li><a href="?Involvements">Involvements</a></li>
			    	<li><a href="?PersonKinds">PersonKinds</a></li>
			    	<li class="Active"><a href="?ReportTypes">ReportTypes</a></li>			    	' ;
			    	}
			    	else 
			    	{
			    	echo '<li class="Active"><a href="?Buildings">Buildings</a></li>
			    	<li><a href="?Departments">Departments</a></li>
			    	<li><a href="?Involvements">Involvements</a></li>
			    	<li><a href="?PersonKinds">PersonKinds</a></li>
			    	<li><a href="?ReportTypes">ReportTypes</a></li>			    	' ;
			    	}
			    	?>
			    	
			    			    	
			  	</ul>
			 
			</div><!--/.nav-collapse -->
		</div>
    </nav>

	</head>

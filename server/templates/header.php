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
	<!-- my favicon -->

	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?"/>


	<!-- Help with table width responsiveness -- >


	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
			<script type="text/javascript" language="javascript" src="tablefilter.js"></script> 	
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script> 


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
$(window).onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// esc key close modal
$(document).keyup(function(e) {
     if (e.keyCode == 27) { // escape key maps to keycode `27`
        modal.style.display = "none";
    }
});

span.onkeydown = function (e) {
	    if ((e.which === 9)) {
	    	 return false; // prevent the keypress
	    }
	};

}

</script>

	<script>
	$(document).ready(function(){
    $('#download').click(function(){
        var data = $('#txt').val();
        if(data == '')
            return;
        var title = "Reports_";
        var date = new Date().toLocaleString();
        var endTitle = date.concat("");
        JSONToCSVConvertor(data, title.concat(endTitle), true);
    });
});

function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
    //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
    var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
    
    var CSV = '';    
    //Set Report title in first row or line
    
    CSV += ReportTitle + '\r\n\n';

    //This condition will generate the Label/Header
    if (ShowLabel) {
        var row = "";
        
        //This loop will extract the label from 1st index of on array
        for (var index in arrData[0]) {
            
            //Now convert each value to string and comma-seprated
            row += index + ',';
        }

        row = row.slice(0, -1);
        
        //append Label row with line break
        CSV += row + '\r\n';
    }
    
    //1st loop is to extract each row
    for (var i = 0; i < arrData.length; i++) {
        var row = "";
        
        //2nd loop will extract each column and convert it in string comma-seprated
        for (var index in arrData[i]) {
            row += '"' + arrData[i][index] + '",';
        }

        row.slice(0, row.length - 1);
        
        //add a line break after each row
        CSV += row + '\r\n';
    }

    if (CSV == '') {        
        alert("Invalid data");
        return;
    }   
    
    //Generate a file name
    var fileName = "NiceCatchTiger_";
    //this will remove the blank-spaces from the title and replace it with an underscore
    fileName += ReportTitle.replace(/ /g,"_");   
    
    //Initialize file format you want csv or xls
    var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
    
    // Now the little tricky part.
    // you can use either>> window.open(uri);
    // but this will not work in some browsers
    // or you will not get the correct file extension    
    
    //this trick will generate a temp <a /> tag
    var link = document.createElement("a");    
    link.href = uri;
    
    //set the visibility hidden so it will not effect on your web-layout
    link.style = "visibility:hidden";
    link.download = fileName + ".csv";
    
    //this part will append the anchor tag and remove it after automatic click
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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
			    	
			    	<?php 
			    	//echo '<li><a href="authoringTool.html">Authoring Tool</a></li>';
			    	echo '<li><a href="authorTool.php?Buildings">Authoring Tool</a></li>';
 					if ($_SERVER['QUERY_STRING'] == 'Received')
 					{
					echo '<li><a href="?All">All</a></li>
			    	<li class="Active"><a href="?Received">Received</a></li>
			    	<li><a href="?Open">Open</a></li>
			    	<li><a href="?Closed">Closed</a></li>' ;
					}		
					else  if ($_SERVER['QUERY_STRING'] == 'Open')
					 {
					echo '<li><a href="?All">All</a></li>
			    	<li><a href="?Received">Received</a></li>
			    	<li class="Active"><a href="?Open">Open</a></li>
			    	<li><a href="?Closed">Closed</a></li>' ;					 }
					 else if ($_SERVER['QUERY_STRING'] == 'Closed')
 					{
					echo '<li><a href="?All">All</a></li>
			    	<li><a href="?Received">Received</a></li>
			    	<li ><a href="?Open">Open</a></li>
			    	<li class="Active"><a href="?Closed">Closed</a></li>' ;	 					}
 					else 
					 {
					echo '<li class="Active"><a href="?All">All</a></li>
			    	<li><a href="?Received">Received</a></li>
			    	<li><a href="?Open">Open</a></li>
			    	<li><a href="?Closed">Closed</a></li>' ;	 					}
			    	?>
			    	
			    			    	
			  	</ul>
			 
			</div><!--/.nav-collapse -->
		</div>
    </nav>



</head>

<?php
	require_once(dirname(dirname(__FILE__)).'/models/config.php');
	$data = $_POST["imageData"];
	//verify file is correct type (png)
    
    
  //   if(!check_base64_image($data)){
//     	echo "File Type not recognized" ;
//         exit();
//     }
    
    
	$reportId = $_POST["imageName"];
	$image =  base64_decode("$data");
	
	$f = finfo_open();
	$mime_type = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
	// image type from app will always be image/jpeg otherwise exit
	if($mime_type != 'image/jpeg'){
		echo 'File Type not recognized: ' . $mime_type;
        exit();
	}
	
		
		
	$path = Path::uploads().$reportId.'/';	
	if(!file_exists($path)){
		mkdir($path, 0755, true);
		chmod($path, 0775);
		
	}	
	$check = file_put_contents($path.'report-image.png', $image);
	$report = new Report();
	if($check){
		//dont save
		chmod($path.'report-image.png',0644);	
		//update db
		$report->updatePhotoPath($reportId,$path.'report-image.png');
			echo "Saved";
	}
	else{
		
			//echo $reportId. " " . $data. "<br>";
			echo "Did not save correctly";


	}
	

?>
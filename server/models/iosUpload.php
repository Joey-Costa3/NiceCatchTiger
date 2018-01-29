<?php



	 require_once(dirname(dirname(__FILE__)).'/models/config.php');
// 	//$data = $_POST["photo"];
 	$reportId = $_POST["imageName"];
// 	//$image =  base64_decode("$data");	
 	$path = Path::uploads().$reportId.'/';	
	if(!file_exists($path)){
		mkdir($path, 0775, true);
		chmod($path, 0775);
		
	}	
	$photo = $_FILES['photo'];
	//verify file is correct type (gif, jpeg, png)
    $filetype = exif_imagetype($photo['tmp_name']);
    $allowed = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);
    if(!in_array($filetype, $allowed)){
        echo "File Type not recognized: " . $filetype;
        exit();
    }
	$name = preg_replace("/[^A-Z0-9._-]/i", "_", $photo['name']);
	//echo isset($photo);
	//$check = file_put_contents($path.'report-image.png', $name);
	$check = move_uploaded_file($photo['tmp_name'], $path.'report-image.png');
	$report = new Report();
	if($check){
		//dont save
		chmod($path.'report-image.png',0644);	
		//update db
		$report->updatePhotoPath($reportId,$path.'report-image.png');
			echo "Saved";
	}
	else{
		
			echo "Did not save correctly";
			
	}
	

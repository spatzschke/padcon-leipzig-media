<?php

include('SimpleImage.php');
$image = new SimpleImage();

// If you want to ignore the uploaded files, 
// set $demo_mode to true;

$demo_mode = false;
$upload_dir = 'uploads/';
$allowed_ext = array('jpg','jpeg','png','gif','pdf');
$image_ext = array('jpg','jpeg','png','gif');
$pdf_ext = array('pdf');

//$host = 'http://localhost:81/padcon-leipzig-media/';
$host = 'http://media.padcon-leipzig.de/';

if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
	exit_status('Error! Wrong HTTP method!');
}


if(array_key_exists('pic',$_FILES) && $_FILES['pic']['error'] == 0 ){
	
	$pic = $_FILES['pic'];

	if(!in_array(get_extension($pic['name']),$allowed_ext)){
		exit_status('Only '.implode(',',$allowed_ext).' files are allowed!');
	}
	
	if(in_array(get_extension($pic['name']),$image_ext)){
		if($_GET['number'] != '') {
			$upload_dir = 'media/image/product/';
		} else {
			$upload_dir = 'media/image/';
		}	
	}
	
	if(in_array(get_extension($pic['name']),$pdf_ext)){
		$upload_dir = 'media/pdf/';	}	

	if($demo_mode){
		
		// File uploads are ignored. We only log them.
		
		$line = implode('		', array( date('r'), $_SERVER['REMOTE_ADDR'], $pic['size'], $pic['name']));
		file_put_contents('log.txt', $line.PHP_EOL, FILE_APPEND);
		
		exit_status('Uploads are ignored in demo mode.');
	}
	
	$image->load($pic['tmp_name']);
	$image->resizeToWidth(1024);
	$image->save($upload_dir.$_GET['number'].'_'.$_GET['color'].'.'.get_extension($pic['name']));
	$image->resizeToHeight(120);
	$image->save($upload_dir.$_GET['number'].'_'.$_GET['color'].'t.'.get_extension($pic['name']));
	
	//exit_status('File was uploaded successfuly and resizing!');
	
	// Move the uploaded file from the temporary 
	// directory to the uploads folder:
	
	if(move_uploaded_file($pic['tmp_name'], $upload_dir.$_GET['number'].'_'.$_GET['color'].'.'.get_extension($pic['name']))){
		success_status('File was uploaded successfuly!', $host.$upload_dir.$_GET['number'].'_'.$_GET['color'], get_extension($pic['name']), $_GET['color']);
	}
	
}

exit_status('Something went wrong with your upload!');


// Helper functions

function exit_status($str){
	echo json_encode(array('status'=>$str));
	exit;
}

function success_status($str, $path, $ext, $color){
	echo '{ "status" : "'.$str.'", "path" : "'.$path.'", "ext" : "'.$ext.'", "color" : "'.$color.'" }';
	exit;
}

function get_extension($file_name){
	$ext = explode('.', $file_name);
	$ext = array_pop($ext);
	return strtolower($ext);
}
?>
<?php
	if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
		exit_status('Error! Wrong HTTP method!');
	}
	if(array_key_exists('file',$_FILES)){
		$upload_dir = isset($_REQUEST['upload_dir']) ? $_REQUEST['upload_dir'] : $upload_dir ;
		$file_name =basename($_FILES['file']['name']);
		$upload_file = $upload_dir.$file_name;
		$result = move_uploaded_file($_FILES['file']['tmp_name'], $upload_file);
	}
	exit;
?>
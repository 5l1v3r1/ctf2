<?php
  include("inc/common.php");
  
  // Response should be JSON, provide correct header
  header('Content-Type: application/json');
  
  // Check a file has been submitted
  if(isset($_FILES['files'])) {
  
    // Make the file array more standard
    $upload = diverse_array($_FILES["files"]);
    
    // Load data for uploaded file	  
    $file_name    = $key.$_FILES['files']['name'][$key];
    $file_size    = $_FILES['files']['size'][$key];
    $file_tmp     = $_FILES['files']['tmp_name'][$key];
    $file_type    = $_FILES['files']['type'][$key];

    // Confirm file is allowed and confirm MIME type.
    $file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));
    $extensions = array("jpeg","jpg","png","gif","doc","pdf","docx"); 
    if(in_array($file_ext,$extensions )=== false){
        $errors[]='For security you can only upload files in the following formats .doc, .pdf, .jpg, .png, .gif.';
    }	

    // Check file size
    if($file_size > (2*1024*1024)){
        $errors[]='File size must be less than 2 MB';
    }	
    	
    $desired_dir="user_data";
    if(empty($errors)==true){
        
        // Copy file to uploads directory and rename to a random hash.
        if(is_dir("$desired_dir/".$file_name)==false) {
            move_uploaded_file($file_tmp,"user_data/".$file_name);
        } else {
            $new_dir="user_data/".$file_name.time();
            rename($file_tmp,$new_dir);				
        }
        
        // Store original filename, doctype, comment and new location in DB
			
    } else {
        //Output error messages as JSON response to display to user.
        print_r($errors);
    }
    
    // Return JSON with file information
    if(empty($error)){
      echo "Success";
    }
    
  }

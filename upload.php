<?php
  include("inc/common.php");
  
  // Response should be JSON, provide correct header
  header('Content-Type: application/json');
  
  // Check a file has been submitted
  if(isset($_FILES['file'])) {
  
    // Confirm the image upload was successful
    if($_FILES['file']['error'] != UPLOAD_ERR_OK) {
        uploadError(array('msg' => 'An unknown error occurred during upload. Please don\'t try any funny business...'));
    }
   
    // Make the file array more standard
    //$upload = diverse_array($_FILES["files"]);
    
    // Load data for uploaded file	  
    $file_name  = filter_var(trim($_FILES['file']['name']), FILTER_SANITIZE_STRING,  FILTER_FLAG_STRIP_LOW);  // Sanitize the file name
    $file_size  = $_FILES['file']['size'];
    $file_tmp   = $_FILES['file']['tmp_name'];
    //$file_type  = $_FILES['file']['type']; // Don't trust this.
    
    // Validate SHA256 Hash
    $sha256 = filter_input(INPUT_POST, 'sha256', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[A-Fa-f0-9]{64}$/")));
    if($sha256===FALSE) { //Check a valid hash was provided -- !!! Confirm this is correct check
        uploadError(array('msg' => 'An incorrect file hash was provided. Please try uploading again.'));
    }
    
    // Validate comment
    $comment = filter_input(INPUT_GET, 'comment', FILTER_SANITIZE_STRING,  FILTER_FLAG_STRIP_LOW); // Remove any tags and low characters
    if(!empty($comment)) { //Check a comment was uploaded
        // Cut long comments nicely
        $shortcomment = wordwrap($str, 100);
        $comment = $shortcomment[0] . '...';
    }
    
    // Check file doesn't aleady exist
    if (checkIfExists($sha256)) { // File already exists, user should be redirected to download page.
        uploadError(array('msg' => 'Sorry this file already exists.'));          
    }

    // Confirm file is allowed and confirm MIME type.
    $file_ext=strtolower(end(explode('.', $file_name)));
    $file_mime = checkMime($file_tmp);
    $extensions = array("jpeg","jpg","png","gif","doc","pdf","docx"); 
    if(in_array($file_ext,$extensions )=== false){
        uploadError(array('msg' => 'For security reasons you can only upload files in the following formats .doc, .docx, .pdf, .jpg, .png, .gif.'));
    }
    if(!$file_mime) {
         uploadError(array('msg' => 'The uploaded files MIME was not in the allowed list, no funny business....'));
    }

    // Check file size
    if($file_size > (2*1024*1024)){
        uploadError(array('msg' => 'File size must be less than 2 MB'));
    }
    	
    // Begin uploading file and copy to uploads directory
    // Copy file to uploads directory and rename to a random hash.
    $fileLocation = $config['upload_dir'].'/'.$sha256;
    if (!file_exists($fileLocation)) {
        move_uploaded_file($file_tmp, $fileLocation);
    } else {
        uploadError(array($msg => 'Sorry this file has already been uploaded.'));			
    }
        
    // Store original filename, doctype, comment and new location in DB
    
    //Yeah should use Prepared Statments, but I'm being lazy. Don't concate commands like I'm doing. Bad stuff!
    $sql_filename   = $mysqli->real_escape_string($file_name);
    $sql_sha256     = $mysqli->real_escape_string($sha256);
    $sql_mime       = $mysqli->real_escape_string($file_mime);
    $comment        = $mysqli->real_escape_string($comment);
    
    $query  = "INSERT INTO uploads (originalFilename, filehash, newFilename, mimeType, comment) ";
    $query .= "VALUES ('$sql_filename', '$sql_sha256', '$sql_sha256', '$sql_mime', '$comment')";
    
    if (! $mysqli->query($query) ) {
        printf("MySQL Error: %s\n", mysqli_sqlstate($query));
    }
    
    // We have got to the end and file has been uploaded successfully. Return data about image:
    
    
    // Return successful JSON with file information
    $response['status'] = 'success';
    $response['info']   = '<p>Success! Your file has now been uploaded and shared with the world! You will now be redirected to the list of recent files</p>';
    $response['sha256'] = $sha256;
    echo json_encode($response);
    
  } else {
    // No file data was provided in the POST request
    uploadError(array($msg => 'No file was provided in the upload request. Please try again'));
  }

<?php
  include("inc/config.php");
  
  if(isset($_FILES['files'])){
    $errors= array();
    
	  foreach($_FILES['files']['tmp_name'] as $key => $tmp_name ){  // Loop through each uploaded file
	  
		  $file_name = $key.$_FILES['files']['name'][$key];
		  $file_size =$_FILES['files']['size'][$key];
		  $file_tmp =$_FILES['files']['tmp_name'][$key];
		  $file_type=$_FILES['files']['type'][$key];
		
		  $file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));
      $extensions = array("jpeg","jpg","png"); 		
      if(in_array($file_ext,$extensions )=== false){
        $errors[]='For security you can only upload files in the following formats .doc, .pdf, .jpg, .png, .gif.';
  	  }	
  	  
      if($file_size > 2097152){
        $errors[]='File size must be less than 2 MB';
      }		
      //$query="INSERT into upload_data (`USER_ID`,`FILE_NAME`,`FILE_SIZE`,`FILE_TYPE`) VALUES('$user_id','$file_name','$file_size','$file_type'); ";
      $desired_dir="user_data";
      if(empty($errors)==true){
        if(is_dir($desired_dir)==false){
          mkdir("$desired_dir", 0700);		// Create directory if it does not exist
        }
        if(is_dir("$desired_dir/".$file_name)==false){
          move_uploaded_file($file_tmp,"user_data/".$file_name);
        }else{	// rename the file if another one exist
          $new_dir="user_data/".$file_name.time();
          rename($file_tmp,$new_dir);				
        }
        ///mysql_query($query);			
      }else{
        print_r($errors);
      }
    }
    if(empty($error)){
      echo "Success";
    }
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <!-- First commit 4e0ae09 - I <3 cRYP70ANaRCHy - Julian -->
        
        <title>HoneyLeaks - The most secure leak platform ever conceived</title>
        <link rel="stylesheet" type="text/css" href="static/css/bootstrap.css">
        
        <!-- Favicon definition -->
        <link rel="shortcut icon" href="static/img/favicon.ico">
        <link rel="icon" href="static/img/favicon.ico" type="image/x-icon">

        <!-- Javascript inclusion -->
        <script type='text/javascript' src='https://www.google.com/jsapi'></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script type="text/javascript" src="static/js/bootstrap.js"></script>
        <script type="text/javascript" src="static/js/base.js"></script>
        <script type="text/javascript" src="static/js/index.js"></script>
    </head>

    <body>
        <div class="wrapper">
            <div class="navbar navbar-fixed-top">
                <div class="navbar-inner">
                    <div class="container">
                        
                        <ul class="nav">
                            <li id="mnu-home">
                                <a href="/"><i class="icon-home icon-white"></i></a>
                            </li>
                            <li id="mnu-community"><a href="about.php">About</a></li>
                            <li id="mnu-community"><a href="recent-leaks.php">Recent Leaks</a></li>
                        </ul>
                    </div>
                </div>
            </div>
     
            <!-- Upload progress dialog -->
            <!-- Added AJAX upload tracking in XXXXXXX - Julian -->
            <div id="dlg-upload-progress" class="modal hide">
                <div class="modal">
                      <div class="modal-header">
                        <a class="close" href="#" onclick="javascript:cancelUpload()">x</a>
                        <h3>Uploading file...</h3>
                      </div>
                      <div class="modal-body">
                          <p>Please wait, do not close the window until the upload ends.</p>
                          <p>The time required for this operation depends on the file size, the net load and your connection speed.</p>

		                  <div id="gif-upload-progress-bar" class="hide gif-progress-bar">
			                <p>Please wait...</p>
			                <span class="bar"></span>
		                  </div>

		                  <div id="upload-progress-bar" class="hide" >
			                <p>Uploading file...</p>
	                      	<div class="progress progress-danger progress-striped active">
				                <div id="upload-progress" class="bar" style="width: 0%;"></div>
	                      	</div>
		                  </div>
                      </div>
                </div>
            </div>
            <!-- End of upload progress dialog -->

            <!-- File too large dialog -->
            <div id="dlg-file-too-large" class="modal hide">
                <div class="modal-header">
                    <a class="close" href="#">×</a>
                    <h3>File too large</h3>
                </div>
                <div class="modal-body">
                    <p>The submitted file execeeds the 2MB size limit.</p>
                </div>
                <div class="modal-footer">
                    <a class="btn cancel" href="#">Cancel</a>
                </div>
            </div>
            <!-- Enf of file too large dialog -->

            <div class="container">

                <div id="logo" class="center margin-top-8">
                    <img src="static/img/logo.png" alt="HoneyLeaks">
                </div>

                <div id="description" class="center margin-top-2">
                    HoneyLeaks is a free service that allows <em>the leaking of sensitive files</em> and facilitates their anonymous storage and distribution.
                </div>

                <div id="box" class="center margin-top-4">

                    <div id="file" class="center box">
                        <form id="frm-file" action="" method="post" enctype="multipart/form-data" style="margin:0">
                            <div class="file-chooser center">
                                <input id="file-choosen" type="file" name="files[]" size="45">
                                <span id="file-name" class="file-name" style="-moz-user-select: none;text-align:left;">No file selected</span>
                                <span class="action" style="-moz-user-select:none;">Choose File</span>
                            </div>
                        </form>

                        <!-- File limit change to 2MB in commit XXXXXXX - DC -->
                        <div class="center">Maximum file size: 2MB</div>

                        <div class="center margin-top-2" style="width:600px;color:gray;">
                            For safety please only upload files of type: .doc, .pdf, .jpg, .png, .gif
                        </div>

                        <div class="center margin-top-3">
                            <button id="btn-scan-file" class="btn btn-primary start xlarge" type="submit">Leak it!</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="push"></div>
        </div>

        <div class="footer center">
            Yeah, this is a CTF challenge. Don't actually leak anything!
        </div>
    </body>
</html>

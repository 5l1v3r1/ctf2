<?php
  include("inc/common.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <!-- First commit 4e0ae09 - I <3 cRYP70ANaRCHy - Julian -->
       
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Pragma" content="no-store">
        <meta http-equiv="Expires" content="-1">
    
        <title>HoneyLeaks - The most secure leak platform ever conceived</title>
        <link rel="stylesheet" type="text/css" href="/static/css/bootstrap.css">
        
        <!-- Favicon definition -->
        <link rel="shortcut icon" href="/static/img/favicon.ico">
        <link rel="icon" href="/static/img/favicon.ico" type="image/x-icon">

        <!-- Javascript inclusion -->        
        <script type='text/javascript' src='https://www.google.com/jsapi'></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script type="text/javascript" src="/static/js/bootstrap.js"></script>
        <script type="text/javascript" src="/static/js/base.js"></script>

        
        <script type="text/javascript" src="/static/js/sha256.js"></script>
        <script type="text/javascript" src="/static/js/index.js"></script>
        
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
                            <li id="mnu-community"><a href="/recent-leaks/">Recent Leaks</a></li>
                        </ul>
                        <?php if($_SESSION['admin']==1) { ?>
                        <ul class="nav pull-right">
                            <li><a href="/admin-area/">Admin Area</a></li>
                            <li><a href="/admin-area/?action=logout">Logout</a></li>
                        </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>
     
            <!-- Upload progress dialog -->
            <!-- Added AJAX upload tracking in 1772c330fb - Julian -->
            <div id="dlg-upload-progress" class="modal hide">
                <div class="modal">
                      <div class="modal-header">
                        <a class="close" href="#" onclick="javascript:cancelUpload()">×</a>
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
            
            <!-- Upload error dialog -->
            <div id="dlg-upload-error" class="modal hide">
                <div class="modal-header">
                    <a class="close" href="#">×</a>
                    <h3>Upload Error</h3>
                </div>
                <div class="modal-body">
                    <p class="error-message"></p>
                </div>
                <div class="modal-footer">
                    <a class="btn cancel" href="#">Cancel</a>
                </div>
            </div>
            <!-- Enf of upload error dialog -->


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
            <!-- End of file too large dialog -->

            <div class="container">

                <div id="logo" class="center margin-top-8">
                    <img src="/static/img/logo.png" alt="HoneyLeaks">
                </div>

                <div id="description" class="center margin-top-2">
                    HoneyLeaks is a free service that facilitates <em>the anonymous leaking of information</em> and facilitates its secure storage and distribution.
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

                        <!-- File limit changed to 2MB in commit e02a6c3. It works now :) - DC -->
                        <div class="center">Maximum file size: 2MB</div>

                        <div class="center margin-top-2" style="width:600px;color:gray;">
                            For safety please only upload files of type: .doc, .pdf, .jpg, .png, .gif, .zip
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
            Yeah, this is a CTF challenge. Don't actually leak anything!  - Email donnchacarroll@gmail.com if you think its broken.
        </div>
    </body>
</html>

<?php
    include("../inc/common.php");

    if($_REQUEST['action'] == 'login') { // User is trying to login check their credentials
        
        $username = $mysqli->real_escape_string(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS)); // Should already have been sanitized
        $password = md5($config['salt'] . $_POST['password']);
        
        // Should really concate queries like this!
        $result = $mysqli->query("SELECT id FROM users WHERE username = '$username' AND password = '$password'");
        
        if(($result->num_rows) > 0){  // Check if a file with this hash was already uploaded
            $_SESSION['admin'] = 1; // Set admin session 
        } else {
            $error = 1;
        }
        
    } else if($_REQUEST['action'] == 'logout') {
        // Logout the user / Destroy session
        session_unset(); 
        session_destroy(); 
        
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <!-- First commit 4e0ae09 - I <3 cRYP70ANaRCHy - Julian -->
       
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Pragma" content="no-store">
        <meta http-equiv="Expires" content="-1">
    
        <title>HoneyLeaks - Admin Area</title>
        <link rel="stylesheet" type="text/css" href="/static/css/bootstrap.css">
        
        <!-- Favicon definition -->
        <link rel="shortcut icon" href="/static/img/favicon.ico">
        <link rel="icon" href="/static/img/favicon.ico" type="image/x-icon">

        <!-- Javascript inclusion -->        
        <script type='text/javascript' src='https://www.google.com/jsapi'></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script type="text/javascript" src="/static/js/bootstrap.js"></script>
        <script type="text/javascript" src="/static/js/base.js"></script>
        
        <script src="/static/js/jquery.tablesorter.min.js"></script>
        
        <script type="text/javascript" src="/static/js/sha256.js"></script>
        <script type="text/javascript" src="/static/js/index.js"></script>
        
        <script type="text/javascript">
            $(document).ready(function(){
                $('table#recent-leaks').tablesorter({sortList: [[2,1]]});
            });
        </script>
        
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
                            <li><a href="/recent-leaks/">Recent Leaks</a></li>
                        </ul>
                        
                        <?php if($_SESSION['admin']==1) { ?>
                        <ul class="nav pull-right">
                            <li><a href="/admin-area/?action=logout">Logout</a></li>
                        </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="container">

                <div id="logo" class="center margin-top-6">
                    <img src="/static/img/logo-text.png" alt="HoneyLeaks">
                </div>

                <div id="box" class="center margin-top-4">
                    <h2 class="margin-bottom-2">Admin Area</h2>
                    <?php 
                    
                if($_SESSION['admin']==1) {
                //=============================================================================================
               
                    // Run actions requested by the admin 
                    if($_REQUEST['action'] == 'delete'){
                        $sha256 = filter_input(INPUT_GET, 'sha256', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[A-Fa-f0-9]{64}$/")));
                        if(checkIfExists($sha256)) { //Check a valid hash was provided and confirm this is correct check
                        
                            if(deleteFile($sha256)){ ?>
                            <div class="alert alert-success">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                That file has now been removed.
                            </div>
                            <?php }
                            
                        } else {
                        // Return error message if the file requested doesn't exist
                        ?>
                            <div class="alert alert-warn">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                The file you have requested does not exist.
                            </div>
                        <?php }
                    }
                     
                    if($_REQUEST['action'] == 'secret'){
                        $sha256 = filter_input(INPUT_GET, 'sha256', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[A-Fa-f0-9]{64}$/")));
                        if(checkIfExists($sha256)) { //Check a valid hash was provided and confirm this is correct check
                        
                            if(secretFile($sha256)){ ?>
                            <div class="alert alert-success">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                That file has now been made secret. It will be moved to a secure area and deleted from the public listing.
                            </div>
                            <?php }
                            
                        } else {
                        // Return error message if the file requested doesn't exist
                        ?>
                            <div class="alert alert-warn">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                The file you have requested does not exist.
                            </div>
                        <?php }
                    }
    
                    if(isset($_GET['sha256'])) {
                        $curSha256 = $_GET['sha256']; // Store hash passed to page
                    }
                    ?>
                    
                    
                    <table class="table table-bordered table-striped table-hover" id="recent-leaks">
                        <thead>
                            <tr>
                                <th style="cursor:pointer;">Filename</th>
                                <th style="cursor:pointer;">File Type</th>
                                <th style="cursor:pointer;">Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        //If no results output that messages,
                        $result = $mysqli->query("SELECT id, originalFilename, newFilename, mimeType, time FROM uploads ORDER BY id DESC;");
    
                        if($result->num_rows == 0){  //Check if a file with this hash was already uploaded
                        ?><tr><td colspan="4"><p class="center">There are no leaks yet. Upload one!</p></td></tr>
                        
                        <?php } else { // otherwise display listing of items
                        
                            while ($row = $result->fetch_object()) { ?>
                            <tr<?php if(($row->newFilename)==$curSha256) { echo ' class="info"'; } ?>>
                                <td><?=filter_var($row->originalFilename, FILTER_SANITIZE_SPECIAL_CHARS); ?></td>
                                <td><?=filter_var($row->mimeType, FILTER_SANITIZE_SPECIAL_CHARS); ?></td>
                                <td><?=$row->time; ?></td>
                                <td>
                                    <a title="Download" href="/download/?sha256=<?=filter_var($row->newFilename, FILTER_SANITIZE_SPECIAL_CHARS); ?>" class="btn btn-mini"><i class="icon-download "></i></a>
                                    <a title="Delete" class="btn btn-mini btn-danger" href="?action=delete&sha256=<?=filter_var($row->newFilename, FILTER_SANITIZE_SPECIAL_CHARS); ?>"><i class="icon-trash icon-white"></i></a>
                                    <div class="pull-right">
                                        <a title="Make File Secret" class="btn btn-mini btn-warning" href="?action=secret&sha256=<?=filter_var($row->newFilename, FILTER_SANITIZE_SPECIAL_CHARS); ?>">Secret <i class="icon-fire icon-white"></i></a>
                                    </div>
                                </td>
                            </tr><?php }     
                        }
                        $result->close();
                        ?>
                        </tbody>
                    </table>
                    
                <?php
                //=============================================================================================
                } else {  // User is not logged in ?>
                    
                    <?php if($error==1) { ?>
                        <div class="alert alert-error">
                            <button data-dismiss="alert" class="close" type="button">×</button>
                            Your username or password was entered incorrectly. This request has been logged. (<?=$_SERVER['REMOTE_ADDR'];?>)
                        </div>
                    <?php } ?>
                    <?php if($_REQUEST['action']=='logout') { ?>
                        <div class="alert alert-warn">
                            <button data-dismiss="alert" class="close" type="button">×</button>
                            You have been logged out
                        </div>
                    <?php } ?>
                
                    <form id="frm-login" action="" method="post" style="margin:0">
                        
                        <!-- I'm being lazy with the style for the login form -->
                        <div class="comment-chooser center margin-bottom-1">
                            <input id="comment" type="text" name="username" />
                            <span class="comment" style="-moz-user-select: none;"></span>
                            <span id="comment" class="action" style="-moz-user-select: none;">Username</span>
                        </div>
                            
                        <div class="comment-chooser center margin-bottom-1">
                            <input id="comment" type="password" name="password" />
                            <span class="comment" style="-moz-user-select: none;"></span>
                            <span id="comment" class="action" style="-moz-user-select: none;">Password</span>
                        </div>
                        
                        <input id="login" type="hidden" name="action" value="login" />
                        
                        <div class="center margin-top-2">
                            <button id="btn-scan-file" class="btn btn-primary start xlarge" type="submit">Login</button>
                        </div>
                        
                     </form>
                       
                <?php } ?>
                </div>
            </div>

            <div class="push"></div>
        </div>
    </body>
</html>

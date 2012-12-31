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

            <div class="container">

                <div id="logo" class="center margin-top-6">
                    <img src="/static/img/logo-text.png" alt="HoneyLeaks">
                </div>

                <div id="description" class="center margin-top-2">
                    HoneyLeaks is a free service that facilitates <em>the anonymous leaking of information</em> and facilitates its secure storage and distribution.
                </div>

                <div id="box" class="center margin-top-4">
                    
                    <?php 
                    // Display information boxes depending on the context
                    if($_GET['exists']==1) { ?>
                    <div class="alert">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        That file is already uploaded! It's highlighted below for you.
                    </div>
                    <?php } else if($_GET['success']==1) { ?>
                    <div class="alert alert-success">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        Your file was uploaded successfully. Thanks for sharing it with the world.
                    </div>
                    <?php }
                    $curSha256 = $_GET['sha256']; // Store hash passed to page
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
                        ?><tr><td colspan="4"><p class="center">There are no leaks yet. Upload one!</p></td></tr><?php
                            
                        } else { // otherwise display listing of items
                        
                            while ($row = $result->fetch_object()) { ?>
                            <tr<?php if(($row->newFilename)==$curSha256) { echo ' class="info"'; } ?>>
                                <td><?=filter_var($row->originalFilename, FILTER_SANITIZE_SPECIAL_CHARS); ?></td>
                                <td><?=filter_var($row->mimeType, FILTER_SANITIZE_SPECIAL_CHARS); ?></td>
                                <td><?=$row->time; ?></td>
                                <td><a href="/download/?sha256=<?=filter_var($row->newFilename, FILTER_SANITIZE_SPECIAL_CHARS); ?>"><button class="btn btn-mini">Download</button></a></td>
                            </tr><?php }     
                        }
                        $result->close();
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="push"></div>
        </div>

        <div class="footer center">
            Yeah, this is a CTF challenge. Don't actually leak anything!  - Email donnchacarroll@gmail.com if you think its broken.
        </div>
    </body>
</html>

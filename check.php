<?php
    include("inc/common.php");
    
    // Response should be JSON, provide correct header
    header('Content-Type: application/json');

    function checkIfExists($sha256) {
        global $mysqli;
        $result = $mysqli->query("SELECT id FROM uploads WHERE filehash = '". $sha256 ."'");
        
        if($result->num_rows > 0){  //Check if a file with this hash was already uploaded
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getUploadURL($sha256) {
        global $config;
        return $config['base_url'] . $config['upload_url'] . $sha256;
    }



    //Validate SHA256 Hash
    $sha256 = filter_input(INPUT_GET, 'sha256', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[A-Fa-f0-9]{64}$/")));
    $response = array();

    if($sha256) { //Check a valid hash was provided
    
        if (checkIfExists($sha256)===FALSE) { //This is a new download, continue with upload
            $response["file_exists"]    = FALSE;        
        } else { // File already exists, user should be redirected to download page.
            $response['file_exists']    = TRUE;          
        }
        $response['upload_url'] = getUploadUrl($sha256); 

    } else { //Invalid Hash provided. Return JSON with error message
            $response['file_exists']    = FALSE;
            $response['error']          = "The file hash was incorrect. Please try again. Don't try anything sneaky...";
    }
    
    echo json_encode($response);
    die();


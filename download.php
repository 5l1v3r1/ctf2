<?php
    include("inc/common.php");
 
    //- turn off compression on the server
    @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 'Off');
     
    if(!isset($_REQUEST['sha256']) || empty($_REQUEST['sha256'])) 
    {
	    header("HTTP/1.0 400 Bad Request");
	    exit;
    }
     
    // allow a file to be streamed instead of sent as an attachment
    $is_attachment = isset($_REQUEST['stream']) ? false : true;
    
    $sha256 = filter_input(INPUT_GET, 'sha256', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[A-Fa-f0-9]{64}$/")));
    $file_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $config['upload_dir'] . $sha256;
    
    // make sure the file exists
    if ( ($sha256!==FALSE) && (checkIfExists($sha256)) && (is_file($file_path)) ) {
    
        $sql_sha256  = $mysqli->real_escape_string($sha256);
        $result = $mysqli->query("SELECT id, originalFilename, newFilename, mimeType FROM uploads WHERE newFilename = '".$sql_sha256."' LIMIT 1;");
        $file_info = $result->fetch_object();
        $file_name = $file_info->originalFilename;
        
	    $file_size  = filesize($file_path);
	    $file = @fopen($file_path,"rb");
	    if ($file)
	    {
		    // set the headers, prevent caching
		    header("Pragma: public");
		    header("Expires: -1");
		    header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
		    header("Content-Disposition: attachment; filename=\"$file_name\"");
     
            // set appropriate headers for attachment or streamed file
            if ($is_attachment)
                    header("Content-Disposition: attachment; filename=\"$file_name\"");
            else
                    header('Content-Disposition: inline;');
     
            // set the mime type based on extension, add yours if needed.
            header("Content-Type: " . $file_info->mimeType);
     
		    //check if http_range is sent by browser (or download manager)
		    if(isset($_SERVER['HTTP_RANGE']))
		    {
			    list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);
			    if ($size_unit == 'bytes')
			    {
				    //multiple ranges could be specified at the same time, but for simplicity only serve the first range
				    //http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
				    list($range, $extra_ranges) = explode(',', $range_orig, 2);
			    }
			    else
			    {
				    $range = '';
				    header('HTTP/1.1 416 Requested Range Not Satisfiable');
				    exit;
			    }
		    }
		    else
		    {
			    $range = '';
		    }
     
		    //figure out download piece from range (if set)
		    list($seek_start, $seek_end) = explode('-', $range, 2);
     
		    //set start and end based on range (if set), else set defaults
		    //also check for invalid ranges.
		    $seek_end   = (empty($seek_end)) ? ($file_size - 1) : min(abs(intval($seek_end)),($file_size - 1));
		    $seek_start = (empty($seek_start) || $seek_end < abs(intval($seek_start))) ? 0 : max(abs(intval($seek_start)),0);
     
		    //Only send partial content header if downloading a piece of the file (IE workaround)
		    if ($seek_start > 0 || $seek_end < ($file_size - 1))
		    {
			    header('HTTP/1.1 206 Partial Content');
			    header('Content-Range: bytes '.$seek_start.'-'.$seek_end.'/'.$file_size);
			    header('Content-Length: '.($seek_end - $seek_start + 1));
		    }
		    else
		      header("Content-Length: $file_size");
     
		    header('Accept-Ranges: bytes');
     
		    set_time_limit(0);
		    fseek($file, $seek_start);
     
		    while(!feof($file)) 
		    {
			    print(@fread($file, 1024*8));
			    ob_flush();
			    flush();
			    if (connection_status()!=0) 
			    {
				    @fclose($file);
				    exit;
			    }			
		    }
     
		    // file save was a success
		    @fclose($file);
		    exit;
	    }
	    else 
	    {
		    // file couldn't be opened
		    header("HTTP/1.0 500 Internal Server Error");
		    exit;
	    }
    } else {
	    // file does not exist
	    header("HTTP/1.0 404 Not Found");
	    exit;
    }

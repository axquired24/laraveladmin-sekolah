<?php

namespace App\Http;

function hl_ifIsset($param, $type='get') {
    switch ($type) {
	    case "post":
	    	$ret = isset($_POST[$param]) ? $_POST[$param] : null;
	    	return $ret;
	    	break;
	    default:
		    $ret = isset($_GET[$param]) ? $_GET[$param] : null;
		    return $ret;
		    break;
    }
}
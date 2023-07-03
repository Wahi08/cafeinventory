<?php
ob_start();
ini_set('date.timezone','Asia/Kuala_Lumpur');
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

require_once('initialize.php');
require_once('classes/DBConnection.php');
require_once('classes/SystemSettings.php');
$db = new DBConnection;
$conn = $db->conn;
function redirect($url=''){
	if(!empty($url))
	echo '<script>location.href="'.base_url .$url.'"</script>';
}
function validate_image($imagePath) {
    // Check if the image path is valid
    if (!empty($imagePath) && file_exists($imagePath)) {
        // Read the image file as binary data
        $imageData = file_get_contents($imagePath);

        // Convert the binary data to base64 format
        $base64Image = base64_encode($imageData);

        // Generate the data URI with the appropriate image MIME type
        $mime = mime_content_type($imagePath);
        $dataURI = 'data:' . $mime . ';base64,' . $base64Image;

        return $dataURI;
    } else {
        // Return a placeholder image or default image data URI
        return 'uploads/avatars/3.png';// Replace with your placeholder image or default image data URI
    }
}

function format_num($number = '' , $decimal = ''){
    if(is_numeric($number)){
        $ex = explode(".",$number);
        $decLen = isset($ex[1]) && abs($ex[1]) != 0 ? strlen($ex[1]) : 0;
        if(is_numeric($decimal)){
            return number_format($number,$decimal);
        }else{
            return number_format($number,$decLen);
        }
    }else{
        return "Invalid Input";
    }
}
function isMobileDevice(){
    $aMobileUA = array(
        '/iphone/i' => 'iPhone', 
        '/ipod/i' => 'iPod', 
        '/ipad/i' => 'iPad', 
        '/android/i' => 'Android', 
        '/blackberry/i' => 'BlackBerry', 
        '/webos/i' => 'Mobile'
    );

    //Return true if Mobile User Agent is detected
    foreach($aMobileUA as $sMobileKey => $sMobileOS){
        if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
            return true;
        }
    }
    //Otherwise return false..  
    return false;
}
ob_end_flush();
?>
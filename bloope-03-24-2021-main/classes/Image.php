<?php
include ('DB.php');

class Image{
    
    public static function uploadImage($db_conn,$formname,$query, $param2){
         //convert the image to uploaded to base64_encode
    $image = base64_encode(file_get_contents($_FILES[$formname]['tmp_name']));
    
    //create an option as required by imgur
    $options = array('http'=>array(
            'method'=>"POST",
            'header'=>"Authorization: Bearer ded6a9d040e8c400420852d0e25ef2a23a11450e\n".
            "Content-Type: application/x-www-form-urlencoded",
            'content'=>$image
    ));

    $context = stream_context_create($options);

    $imgurURL = "https://api.imgur.com/3/image";

    //imgur does not allow images over 10 mb,
    //10240000 = 10mb

    if ($_FILES[$formname]['size'] > 10240000) {
        die('Image too big, must be 10MB or less!');
    }

    //send to imgur
    $response = file_get_contents($imgurURL, false, $context);
    $response = json_decode($response);

    $param1 = $response->data->link;

    //update user's table with new profileImg
    //'UPDATE users SET `profileImg`=? WHERE id=?'
    //$response->data->link, $userid
    $stmt = mysqli_prepare($db_conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $param1, $param2);
    mysqli_stmt_execute($stmt);
    echo 'Image uploaded';
    }
}
?>
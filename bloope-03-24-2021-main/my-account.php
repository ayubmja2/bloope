<?php
include('classes/DB.php');
include('./classes/Login.php');

if (Login::isLoggedIn($db_conn)) {
        $userid = Login::isLoggedIn($db_conn);
} else {
        die('Not logged in');
}
 if (isset($_POST['uploadprofileimg'])) {

   Image::uploadImage($db_conn,"profileimg",'UPDATE users SET `profileImg`=? WHERE id=?', $userid);
}
?>
<h1>My Account</h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
    Upload a profile image:
    <input type="file" name="profileimg" required>
    <input type="submit" name="uploadprofileimg" value="Upload Image">
</form>


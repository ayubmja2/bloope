<?php
include('./classes/DB.php');
include('./classes/Login.php');
//This is a temporary solution, for token to be sent via email the project has to be uploaded to a server
$tokenIsValid = False;
//Checks if the user is Logged In.
if (Login::isLoggedIn($db_conn)) {
    if (isset($_POST['changepassword'])) {

        //Gets old password and checks if the passwords match.
        $oldpassword = $_POST['oldpassword'];
        $newpassword = $_POST['newpassword'];
        $newpasswordrepeat = $_POST['newpasswordrepeat'];

        //Gets the user id if the user is logged in.
        $userid = Login::isLoggedIn($db_conn);

        //Gets the password for the corresponding user id.
        $sql = "SELECT * FROM `users` WHERE id = ?";
        $stmt = mysqli_prepare($db_conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $userid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $correct_password = '';
        if (mysqli_num_rows($result) == 1) {
            foreach ($result as $r)
            {
                $correct_password =  $r['password'];
            }   
        } 
        //To verify if the user enterted the correct value for the old password            
        if (password_verify($oldpassword, $correct_password)) {
            if ($newpassword == $newpasswordrepeat) {
                if(!$oldpassword == $newpassword){
                    if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {
                        //Updates the password.
                        $sql  = 'UPDATE users SET password=? WHERE id=?';
                        $stmt = mysqli_prepare($db_conn, $sql);
                        mysqli_stmt_bind_param($stmt, 'si',  password_hash($newpassword, PASSWORD_BCRYPT), $userid);
                        mysqli_stmt_execute($stmt);
                        echo 'Password changed successfully!';
                    }
                } else {
                        echo 'Passwords don\'t match!';
                }
            }else{
                echo "New password cannot be the same as previous";
            }
        } else {
            echo 'Incorrect old password!';
        }
    }
} else {
    //If the user forgets his old password a token should be sent to the users email
    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        //Gets the user id based on the email provided
        $sql = "SELECT * FROM `password_tokens` WHERE token = ?";
        $stmt = mysqli_prepare($db_conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', sha1($token));
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $userid = '';
        if (mysqli_num_rows($result) == 1) {
            foreach ($result as $r)
            {
                    $userid =  $r['user_id'];
            } 

            $tokenIsValid = True;
            if (isset($_POST['changepassword'])) {
                //update the password and delete the token.
                $newpassword = $_POST['newpassword'];
                $newpasswordrepeat = $_POST['newpasswordrepeat'];
                    if ($newpassword == $newpasswordrepeat) {
                        if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {
                            $sql  = 'UPDATE users SET password=? WHERE id=?';
                            $stmt = mysqli_prepare($db_conn, $sql);
                            mysqli_stmt_bind_param($stmt, 'si',  password_hash($newpassword, PASSWORD_BCRYPT), $userid);
                            mysqli_stmt_execute($stmt);
                            echo 'Password changed successfully!';

                            $sql = "DELETE FROM password_tokens WHERE user_id=?";
                            $stmt = mysqli_prepare($db_conn, $sql);
                            mysqli_stmt_bind_param($stmt, 'i', $userid);
                            mysqli_stmt_execute($stmt);
                            echo 'Password Token Deleted';
                        }
                    } else {
                            echo 'Passwords don\'t match!';
                    }
                }
        } else {
                die('Token invalid');
        }
    } else {
            die('Not logged in');
    }
}


?>
<h1>Change your Password</h1>

<form action="<?php if (!$tokenIsValid) { echo 'change-password.php'; } else { echo 'change-password.php?token='.$token.''; } ?>" method="post">
        <?php if (!$tokenIsValid) { echo '<input type="password" name="oldpassword" value="" placeholder="Current Password ..."><p />'; } ?>
        <input type="password" name="newpassword" value="" placeholder="New Password ..."><p />
        <input type="password" name="newpasswordrepeat" value="" placeholder="Repeat Password ..."><p />
        <input type="submit" name="changepassword" value="Change Password">
</form>
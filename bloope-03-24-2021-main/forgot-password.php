<?php
include('./classes/DB.php');
include('classes/Mail.php');

if (isset($_POST['resetpassword'])) {

        if(isset($_POST['email'])){
                $email = $_POST['email'];
                if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                        $cstrong = True;
                        $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
                        $userid = "";

                        $sql = "SELECT * FROM `users` WHERE email = ?";
                        $stmt = mysqli_prepare($db_conn, $sql);
                        mysqli_stmt_bind_param($stmt, 's', $email);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $correct_password = '';
                        if (mysqli_num_rows($result) == 1) {
                        foreach ($result as $r)
                        {
                                $userid =  $r['id'];
                        }   
                        $sql = "INSERT INTO `password_tokens`(`token`,`user_id`) VALUES (?,?)";
                        $stmt = mysqli_prepare($db_conn, $sql);
                        mysqli_stmt_bind_param($stmt, 'ss', sha1($token), $userid);
                        mysqli_stmt_execute($stmt);   
                        Mail::sendMail('Forgot Password!', "<a href='http://localhost:8888/bloope/change-password.php?token=$token'>http://localhost:8888/bloope/change-password.php?token=$token</a>", $email);
                   
                        echo 'Email sent!';
                        echo '<br />';
                        echo $token;
                        }  
                }
        }
}

?>
<h1>Forgot Password</h1>
<form action="forgot-password.php" method="post">
        <input type="text" name="email" value="" placeholder="Email ..."><p />
        <input type="submit" name="resetpassword" value="Reset Password">
</form>
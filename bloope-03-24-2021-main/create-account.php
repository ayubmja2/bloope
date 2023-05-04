<?php
include('classes/DB.php');
// include('classes/Mail.php');

//Mail::sendMail();
if (isset($_POST['createaccount'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    
    $sql = "SELECT username FROM `users` WHERE username = ?";

    $stmt = mysqli_prepare($db_conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!mysqli_num_rows($result) > 0) {
        $sql = "SELECT `email` FROM `users` WHERE email = ?";
        $stmt = mysqli_prepare($db_conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (!mysqli_num_rows($result) > 0) {
            if(strlen($username) >=3 && strlen($username) <=32){
                if(preg_match('/[a-zA-Z0-9_]+/', $username)){
                    if(strlen($password) >= 6 && strlen($password) <= 60){
                        if(filter_var($email, FILTER_VALIDATE_EMAIL)){

                            error_reporting(E_ALL); ini_set('display_errors', '1');

                            $isVerified =0;
                            $sql = "INSERT INTO `users`(`username`,`password`,`email`,`phone_number`, `isVerified`) VALUES (?,?,?,?,?)";
                            $stmt = mysqli_prepare($db_conn, $sql);
                            mysqli_stmt_bind_param($stmt, 'ssssi', $username, password_hash($password, PASSWORD_BCRYPT), $email, $email, $isVerified);
                            mysqli_stmt_execute($stmt);
                            Mail::sendMail('Welcome to our Social Network!', 'Your account has been created!', $email);

                        //echo "echo ". mysqli_stmt_execute($stmt);
                            echo "<p class='alert alert-success'>Success!</p>";
                        }else{
                            echo "<p class='alert alert-danger'>Invalid Email</p>";
                        }
                    }else{
                        echo "<p class='alert alert-danger'>Invalid Password</p>";
                    }
                }else{
                    echo "<p class='alert alert-danger'>Invalid Username. wrong format</p>";
                }
            }else{
                echo "<p class='alert alert-danger'>Invalid Username. Length Issue</p>";
            }
        } else {        
            echo "<p class='alert alert-danger'>Email Address in use!</p>";
        }    
    } else {        
        echo "<p class='alert alert-danger'>User already exists</p>";
    }  
}

?>
<html>
    <head>
            <title>Create Account| BLOOPE</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
            <link rel="stylesheet" href="./css/style.css" />
    </head>
    <body>
        <header>
            <img src="./bloope.png" alt='bloope'>
            <h1>Create an Account</h1>
        </header>
        <form action="create-account.php" method="post" class="rounded">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" value="" placeholder="Martin Luther King Jr"><p />
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
            </div>

            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email address">
                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <input type="submit" name="createaccount" class="btn btn-primary" value="Create Account">
        </form>
    </body>
</html>
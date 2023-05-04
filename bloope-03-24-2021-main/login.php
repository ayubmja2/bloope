<?php
include('classes/DB.php');
//james account password = test123
if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT username FROM `users` WHERE username = ?";
        $stmt = mysqli_prepare($db_conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 1) {
                $sql = "SELECT * FROM `users` WHERE username = ?";
                $stmt = mysqli_prepare($db_conn, $sql);
                mysqli_stmt_bind_param($stmt, 's', $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $correct_password = '';
                $user_id = '';
                foreach ($result as $r)
                    {
                        $correct_password =  $r['password'];
                        $user_id = $r['id'];
                    }                
                    if (password_verify($password, $correct_password)) {
                        echo '<p class="alert alert-success">Logged in!</p>';
                        $cstrong = True;
                        $token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
                        //get the user id from the users table and add a token into the login_token table
                        $sql = "INSERT INTO `login_tokens`(`token`,`user_id`) VALUES (?,?)";
                        $stmt = mysqli_prepare($db_conn, $sql);
                        mysqli_stmt_bind_param($stmt, 'si', sha1($token), $user_id);
                        mysqli_stmt_execute($stmt);
                       
                        setcookie("BLOOPE", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
                        setcookie("BLOOPE_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
                } else {
                        echo '<p class="alert alert-danger">Incorrect Password!</p>';
                }
        } else {
                echo '<p class="alert alert-danger">User not registered!</p>';
        }
        
}

?>
<html>
        <head>
                <title>Login In| BLOOPE</title>
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
                <link rel="stylesheet" href="./css/style.css" />
        </head>
        <body>
                <header>
                        <img src="./bloope.png" alt='bloope'>
                        <h1>Login to your account</h1>
                </header>
                <form action="login.php" method="post" class="rounded">
                        <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" name="username" id="username" value="" placeholder="Martin Luther King Jr"><p />
                        </div>
                        <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                        </div>
                        <input type="submit" name="login" class="btn btn-primary" value="Login">
                </form>
        </body>
</html>
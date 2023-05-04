<?php
include ('DB.php');
//A login class to check if a user is logged in
class Login {
    //If the cookie is select the user with the corresponding token
    public static function isLoggedIn($db_conn) {
        if (isset($_COOKIE['BLOOPE'])) {
            $sql = 'SELECT * FROM login_tokens WHERE token=?';
            $stmt = mysqli_prepare($db_conn, $sql);
            mysqli_stmt_bind_param($stmt, 's', sha1($_COOKIE['BLOOPE']));
            mysqli_stmt_execute($stmt);
            $user_id = "";
            $result = mysqli_stmt_get_result($stmt);
            //Ensuring there is just one user with corresponding details.
            if (mysqli_num_rows($result) == 1) {
                foreach ($result as $r)
                {
                    $user_id = $r['user_id'];
                }  
                if (isset($_COOKIE['BLOOPE_'])) {
                    return $user_id;
                } else {
                    $cstrong = True;
                    //insert new token
                    $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
                    $sql = "INSERT INTO `login_tokens`(`token`,`user_id`) VALUES (?,?)";
                    $stmt = mysqli_prepare($db_conn, $sql);
                    mysqli_stmt_bind_param($stmt, 'si', sha1($token), $user_id);
                    mysqli_stmt_execute($stmt);
    
                    //delete old tokens.
                    $sql = "DELETE FROM login_tokens WHERE token=?";
                    $stmt = mysqli_prepare($db_conn, $sql);
                    mysqli_stmt_bind_param($stmt, 's', sha1($_COOKIE['BLOOPE']));
                    mysqli_stmt_execute($stmt);
    
                    setcookie("BLOOPE", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
                    setcookie("BLOOPE_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
    
                    return $user_id;
                }
            }
        }
        return false;
    }
}

?>
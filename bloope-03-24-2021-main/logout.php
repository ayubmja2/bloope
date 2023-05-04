<?php
include('./classes/DB.php');
include('./classes/Login.php');

if (!Login::isLoggedIn($db_conn)) {
        die("Not logged in.");
}

if (isset($_POST['confirm'])) {
        if (isset($_POST['alldevices'])) {
                $sql = "DELETE FROM login_tokens WHERE user_id=?";
                $stmt = mysqli_prepare($db_conn, $sql);
                mysqli_stmt_bind_param($stmt, 'i', Login::isLoggedIn($db_conn));
                mysqli_stmt_execute($stmt);

        } else {
            if (isset($_COOKIE['BLOOPE'])) {

                $sql = "DELETE FROM login_tokens WHERE token=?";
                $stmt = mysqli_prepare($db_conn, $sql);
                mysqli_stmt_bind_param($stmt, 's', sha1($_COOKIE['BLOOPE']));
                mysqli_stmt_execute($stmt);
            }
            setcookie('BLOOPE', '1', time()-3600);
            setcookie('BLOOPE_', '1', time()-3600);
        }

}

?>
<h1>Logout of your Account?</h1>
<p>Are you sure you'd like to logout?</p>
<form action="logout.php" method="post">
        <input type="checkbox" name="alldevices" value="alldevices"> Logout of all devices?<br />
        <input type="submit" name="confirm" value="Confirm">
</form>
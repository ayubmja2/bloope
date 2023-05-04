<?php
session_start();
$cstrong = True;
$token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = $token;
}

include('./classes/DB.php');
include('./classes/Login.php');
if (Login::isLoggedIn($db_conn)) {
    $userid = Login::isLoggedIn($db_conn);
} else {
    die('Not logged in');
}

if (isset($_POST['send'])) {
    if (!isset($_POST['nocsrf'])) {
        die("INVALID TOKEN");
    }

    if ($_POST['nocsrf'] != $_SESSION['token']) {
        die("INVALID TOKEN");
    }
    error_reporting(E_ALL); ini_set('display_errors', '1');
    $hasBeenRead = 0;
    $sql = "SELECT id FROM `users` WHERE id=?";
    $stmt = mysqli_prepare($db_conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $_GET['receiver']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    //if yes select all the post from the topic.
    if (mysqli_num_rows($result) == 1) {
        $sql = "INSERT INTO `messages`(`body`, `sender`, `receiver`, `hasBeenRead`) VALUES (?,?,?,?)";
        $stmt = mysqli_prepare($db_conn, $sql);
        mysqli_stmt_bind_param($stmt, 'siii', $_POST['body'], $userid, $_GET['receiver'], $hasBeenRead);
        mysqli_stmt_execute($stmt);
        //DB::query("INSERT INTO messages VALUES ('', :body, :sender, :receiver, 0)", array(':body'=>$_POST['body'], ':sender'=>$userid, ':receiver'=>htmlspecialchars($_GET['receiver'])));
        echo "Message Sent!";
    } else {
        die('Invalid ID!');
    }
    session_destroy();
}
?>
<h1>Send a Message</h1>
<form action="send-message.php?receiver=<?php echo htmlspecialchars($_GET['receiver']); ?>" method="post">
        <textarea name="body" rows="8" cols="80"></textarea>
        <input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
        <input type="submit" name="send" value="Send Message">
</form>
<?php
include ('DB.php');

class Notify{
    public static function createNotify($db_conn, $text = "", $postId = 0) {
        $text = explode(" ", $text);
        $notify = array();

        foreach ($text as $word) {
            if (substr($word, 0, 1) == "@") {
                $notify[substr($word, 1)] = array("type"=>1, "extra"=>' { "postbody": "'.htmlentities(implode(" ", $text)).'" } ');
            }
        }

        error_reporting(E_ALL); ini_set('display_errors', '1');

        if (count($text) == 1 && $postId != 0) {
            $notifyType = 2;
            error_reporting(E_ALL); ini_set('display_errors', '1');

            $sql = "SELECT posts.user_id AS receiver, post_likes.user_id AS sender FROM posts, post_likes WHERE posts.id = post_likes.post_id AND posts.id=?";
            $stmt = mysqli_prepare($db_conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i',$postId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $row = $result->fetch_assoc();
                
                $receiver = $row['receiver'];
                $sender = $row['sender'];

                $sql = "INSERT INTO `notifications`(`type`,`receiver`,`sender`) VALUES (?,?,?)";
                $stmt = mysqli_prepare($db_conn, $sql);
                mysqli_stmt_bind_param($stmt, 'iii', $notifyType, $receiver, $sender);
                mysqli_stmt_execute($stmt);
            }
        }

        return $notify;
}
}
?>
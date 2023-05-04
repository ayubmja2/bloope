<?php
include ('DB.php');

class Comment{
    public static function createComment($db_conn, $commentBody, $postId, $userId) {

        if (strlen($commentBody) > 160 || strlen($commentBody) < 1) {
                die('Incorrect length!');
        }

        $sql = "SELECT `id` FROM `posts` WHERE `id` = ?";
        $stmt = mysqli_prepare($db_conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i',$postId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 0) {
            echo 'Invalid post ID';
        }
         else {
            $sql = "INSERT INTO `comments`(`comment`,`user_id`,`post_id`) VALUES (?,?,?)";
            $stmt = mysqli_prepare($db_conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sii', $commentBody, $userId, $postId);
            mysqli_stmt_execute($stmt);
        }
    }

    public static function displayComments($db_conn, $postId) {

        $sql = "SELECT comments.comment, users.username FROM comments, users WHERE post_id = ? AND comments.user_id = users.id";
        $stmt = mysqli_prepare($db_conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i',$postId);
        mysqli_stmt_execute($stmt);
        $comments = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($comments) > 0) {
            foreach($comments as $comment) {
                echo $comment['comment']." ~ ".$comment['username']."<hr />";
            }
        }
        
}
}
?>
<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Image.php');

if (isset($_GET['topic'])) {

    //Check if post of the gotten topic exists
    $sql = "SELECT topics FROM `posts` WHERE FIND_IN_SET(?, topics)";
    $stmt = mysqli_prepare($db_conn, $sql);
    mysqli_stmt_bind_param($stmt, 's',$_GET['topic']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    //if yes select all the post from the topic.
    if (mysqli_num_rows($result) > 0) {
    
        $sql = "SELECT * FROM `posts` WHERE FIND_IN_SET(?, topics)";
        $stmt = mysqli_prepare($db_conn, $sql);
        mysqli_stmt_bind_param($stmt, 's',$_GET['topic']);
        mysqli_stmt_execute($stmt);
        $posts = mysqli_stmt_get_result($stmt);

        foreach($posts as $post) {
            // echo "<pre>";
            // print_r($post);
            // echo "</pre>";
            echo $post['body']."<br />";
        }
    }
}
?>
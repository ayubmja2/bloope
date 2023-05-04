<?php
include ('DB.php');
class Post{

    public static function createPost($db_conn, $postbody, $loggedInUserId, $profileUserId){
        $defaultLikes = 0;
        if (strlen($postbody) > 160 || strlen($postbody) < 1) {
            die('Incorrect length!');
        }

        $topics = self::getTopics($postbody);

        if ($loggedInUserId == $profileUserId) {
                        //create notification
            if (count(Notify::createNotify($db_conn,$postbody)) != 0) {                
                foreach (Notify::createNotify($db_conn,$postbody) as $key => $notifyInfo) {
                    $sender = $loggedInUserId;
                    $receiver = "";
                    $sql = "SELECT * FROM `users` WHERE `username` = ?";
                    $stmt = mysqli_prepare($db_conn, $sql);
                    mysqli_stmt_bind_param($stmt, 's', $key);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    if (mysqli_num_rows($result) == 1) {
                        
                        $row = $result->fetch_assoc();
                        $receiver = $row['id'];
                        $sql = "INSERT INTO `notifications`(`type`,`receiver`,`sender`, `extra`) VALUES (?,?,?,?)";
                        $stmt = mysqli_prepare($db_conn, $sql);
                        mysqli_stmt_bind_param($stmt, 'iiis', $notifyInfo["type"], $receiver, $sender, $notifyInfo['extra']);
                        mysqli_stmt_execute($stmt);
                    }
                }
            }
            //create post
            $sql = "INSERT INTO `posts`(`body`,`user_id`,`likes`, `topics`) VALUES (?,?,?,?)";
            $stmt = mysqli_prepare($db_conn, $sql);
            mysqli_stmt_bind_param($stmt, 'siis', $postbody, $loggedInUserId, $defaultLikes, $topics);
            mysqli_stmt_execute($stmt);
        } else {
                die('Incorrect user!');
        }
    }

    public static function createImgPost($db_conn, $postbody, $loggedInUserId, $profileUserId){
        $defaultLikes = 0;
        if (strlen($postbody) > 160) {
            die('Incorrect length!');
        }

        $topics = self::getTopics($postbody);

        if ($loggedInUserId == $profileUserId) {
            //create notification

            if (count(Notify::createNotify($db_conn,$postbody)) != 0) {
                foreach (Notify::createNotify($db_conn,$postbody) as $key => $notifyInfo) {
                    $sender = $loggedInUserId;
                    $receiver = "";
                    $sql = "SELECT * FROM `users` WHERE `username` = ?";
                    $stmt = mysqli_prepare($db_conn, $sql);
                    mysqli_stmt_bind_param($stmt, 's', $key);
                    mysqli_stmt_execute($stmt);
                    
                    $result = mysqli_stmt_get_result($stmt);
                    if (mysqli_num_rows($result) == 1) {

                        $row = $result->fetch_assoc();
                        $receiver = $row['id'];

                        $sql = "INSERT INTO `notifications`(`type`,`receiver`,`sender`, `extra`) VALUES (?,?,?,?)";
                        $stmt = mysqli_prepare($db_conn, $sql);
                        mysqli_stmt_bind_param($stmt, 'iiis', $notifyInfo["type"], $receiver, $sender, $notifyInfo['extra']);
                        mysqli_stmt_execute($stmt);
                    }
                }
            }

            //create post

            $sql = "INSERT INTO `posts`(`body`,`user_id`,`likes`, `topics`) VALUES (?,?,?,?)";
            $stmt = mysqli_prepare($db_conn, $sql);
            mysqli_stmt_bind_param($stmt, 'siis', $postbody, $loggedInUserId, $defaultLikes, $topics);
            mysqli_stmt_execute($stmt);

            $postId = "";
            $sql = "SELECT * FROM `posts` WHERE `user_id` = ? ORDER BY ID DESC LIMIT 1";
            $stmt = mysqli_prepare($db_conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i',$loggedInUserId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $row = $result->fetch_assoc();
                $postid = $row['id'];
            }
            return $postid;
        } else {
                die('Incorrect user!');
        }
    }

    public static function likePost($db_conn,$postId, $likerId){
        $sql = "SELECT * FROM `post_likes` WHERE `post_id` = ? AND `user_id` =?";
        $stmt = mysqli_prepare($db_conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii',$postId, $likerId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 0) {
                $sql  = 'UPDATE posts SET likes=likes+1 WHERE id=?';
                $stmt = mysqli_prepare($db_conn, $sql);
                mysqli_stmt_bind_param($stmt, 'i',  $postId);
                mysqli_stmt_execute($stmt);
            $sql = "INSERT INTO `post_likes`(`post_id`,`user_id`) VALUES (?,?)";
            $stmt = mysqli_prepare($db_conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ii', $postId, $likerId);
            mysqli_stmt_execute($stmt);
            Notify::createNotify($db_conn,"", $postId);
        } else {
            $sql  = 'UPDATE posts SET likes=likes-1 WHERE id=?';
            $stmt = mysqli_prepare($db_conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $postId);
            mysqli_stmt_execute($stmt);
        
            $sql = "DELETE FROM `post_likes` WHERE `post_id`=? AND `user_id`=?";
            $stmt = mysqli_prepare($db_conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ii', $postId, $likerId);
            mysqli_stmt_execute($stmt);
        }
    }

    public static function getTopics($text) {
        $text = explode(" ", $text);
        $topics = "";

        foreach ($text as $word) {
            if (substr($word, 0, 1) == "#") {
                $topics .= substr($word, 1).",";
            }
        }

        return $topics;
    }
   


    public static function add_link($text) {
        $text = explode(" ", $text);
        $newstring = "";

        foreach ($text as $word) {
            if (substr($word, 0, 1) == "@") {
                $newstring .= "<a href='profile.php?username=".substr($word, 1)."'>".htmlspecialchars($word)."</a> ";
            } else if (substr($word, 0, 1) == "#") {
                $newstring .= "<a href='topics.php?topic=".substr($word, 1)."'>".htmlspecialchars($word)."</a> ";
            } else {
                $newstring .= htmlspecialchars($word)." ";
            }
        }

        return $newstring;
    }

    public static function displayPosts($db_conn,$userid, $username, $loggedInUserId){
        $posts = "";
        $sql = "SELECT * FROM `posts` WHERE `user_id` = ? ORDER BY id DESC";
        $stmt = mysqli_prepare($db_conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $userid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            foreach ($result as $r)
            {
                $sql = "SELECT * FROM `post_likes` WHERE `post_id` = ? AND `user_id` = ? ORDER BY id DESC";
                $stmt = mysqli_prepare($db_conn, $sql);
                mysqli_stmt_bind_param($stmt, 'ii', $r['id'], $loggedInUserId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) == 0) {
                    $posts .= "<img width='100' src='".$r['postImg']."'>".self::add_link($r['body'])."
                    <form action='profile.php?username=$username&postid=".$r['id']."' method='post'>
                            <input type='submit' name='like' value='Like'>
                            <span>".$r['likes']." likes</span>";

                        if ($userid == $loggedInUserId) {
                            $posts .= "<input type='submit' name='deletepost' value='Delete' />";
                        }
                        $posts .= "
                        </form><hr /></br />
                        ";
                }else{
                    $posts .= "<img width='100' src='".$r['postImg']."'>".self::add_link($r['body'])."
                    <form action='profile.php?username=$username&postid=".$r['id']."' method='post'>
                            <input type='submit' name='unlike' value='Unlike'>
                            <span>".$r['likes']." likes</span>";
                        if ($userid == $loggedInUserId) {
                            $posts .= "<input type='submit' name='deletepost' value='Delete' />";
                        }
                        $posts .= "
                        </form><hr /></br />
                        ";
                }
            }

        } 
        return $posts;
    }
}


?>
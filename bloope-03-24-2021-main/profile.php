<?php
include('classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Image.php');
include('./classes/Notify.php');

       // echo 'Logged In';
        //epassword for Verified account is Verified1234

        $username = "";
        $isFollowing = False;
        $isVerified = 0;

        //if username is set get the users details
        if (isset($_GET['username'])) {
            $userid = '';
            $followerid = Login::isLoggedIn($db_conn);
            $sql = "SELECT * FROM `users` WHERE username = ?";
            $stmt = mysqli_prepare($db_conn, $sql);
            mysqli_stmt_bind_param($stmt, 's', $_GET['username']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                foreach ($result as $r)
                {
                    $username =  $r['username'];
                    $userid = $r['id'];
                    $isVerified = $r['isVerified'];
                }  
                
                if (isset($_POST['follow'])) {
                    //followingid is the logged in user
                    //userid is the user id for the user's profile you are in
                    if ($userid != $followerid) {
    
                        $sql = "SELECT * FROM `followers` WHERE `user_id` = ? AND `follower_id` =?";
                        $stmt = mysqli_prepare($db_conn, $sql);
                        mysqli_stmt_bind_param($stmt, 'ii', $userid, $followerid);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
    
                        if (mysqli_num_rows($result) == 0) {
                            if ($followerid == 24) {
                                $verifiy = 1;
                                $sql  = 'UPDATE users SET isVerified=? WHERE id=?';
                                $stmt = mysqli_prepare($db_conn, $sql);
                                mysqli_stmt_bind_param($stmt, 'ii', $verifiy, $userid);
                                mysqli_stmt_execute($stmt);
                            }
                            $sql = "INSERT INTO `followers`(`user_id`,`follower_id`) VALUES (?,?)";
                            $stmt = mysqli_prepare($db_conn, $sql);
                            mysqli_stmt_bind_param($stmt, 'ii', $userid, $followerid);
                            mysqli_stmt_execute($stmt);
                        } else {
                            echo 'Already following!';
                        }  
                        $isFollowing = True;              
                    }
                }
    
                if (isset($_POST['unfollow'])) {
                    if ($userid != $followerid) {
                        $sql = "SELECT * FROM `followers` WHERE `user_id` = ? AND `follower_id` = ?";
                        $stmt = mysqli_prepare($db_conn, $sql);
                        mysqli_stmt_bind_param($stmt, 'ii', $userid, $followerid);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
    
                        if (mysqli_num_rows($result) == 1) {
                            if ($followerid == 24) {
                                $unVerifiy = 0;
                                $sql  = 'UPDATE users SET isVerified=? WHERE id=?';
                                $stmt = mysqli_prepare($db_conn, $sql);
                                mysqli_stmt_bind_param($stmt, 'ii', $unVerifiy, $userid);
                                mysqli_stmt_execute($stmt);
                            }
                            $sql = "DELETE FROM `followers` WHERE `user_id`=? AND follower_id=?";
                            $stmt = mysqli_prepare($db_conn, $sql);
                            mysqli_stmt_bind_param($stmt, 'ii', $userid, $followerid);
                            mysqli_stmt_execute($stmt);
                        }                            
                        $isFollowing = False;
                    }
                }
    
                //To check if you are following a user 
                $sql = "SELECT * FROM `followers` WHERE `user_id` = ? AND `follower_id`= ?";
                $stmt = mysqli_prepare($db_conn, $sql);
                mysqli_stmt_bind_param($stmt, 'ii', $userid, $followerid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
    
                if (mysqli_num_rows($result) == 1) {
                    //echo 'Already following!';
                    $isFollowing = True;
                }
                
                if (isset($_POST['deletepost'])) {
                    $sql = "SELECT id FROM `posts` WHERE id=? AND user_id=?";
                    $stmt = mysqli_prepare($db_conn, $sql);
                    mysqli_stmt_bind_param($stmt, 'ii',$_GET['postid'], $followerid);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    //if yes select all the post from the topic.
                    if (mysqli_num_rows($result) > 0) {
                    
                        //Delete all likes
                        $sql = "DELETE FROM post_likes WHERE post_id=?";
                        $stmt = mysqli_prepare($db_conn, $sql);
                        mysqli_stmt_bind_param($stmt, 'i', $_GET['postid']);
                        mysqli_stmt_execute($stmt);
                        //Delete post
                        $sql = "DELETE FROM posts WHERE id=? AND user_id=?";
                        $stmt = mysqli_prepare($db_conn, $sql);
                        mysqli_stmt_bind_param($stmt, 'ii', $_GET['postid'], $followerid);
                        mysqli_stmt_execute($stmt);

                    }           
                }
    
                if (isset($_POST['post'])) {
                    if ($_FILES['postimg']['size'] == 0) {
                        Post::createPost($db_conn,$_POST['postbody'], Login::isLoggedIn($db_conn), $userid);
                    }else{
                        $postid = Post::createImgPost($db_conn,$_POST['postbody'], Login::isLoggedIn($db_conn), $userid);
                        Image::uploadImage($db_conn,'postimg', "UPDATE posts SET postImg=? WHERE id=?", $postid);
                    }
                }
    
                if (isset($_GET['postid']) && !isset($_POST['deletepost'])) {
                    Post::likePost($db_conn,$_GET['postid'], $followerid);
                } 
                $posts = Post::displayPosts($db_conn,$userid,  $username, $followerid);
    

            } else {
                die('User not found!');
            }    
        }

?>


<!-- <form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
        <textarea name="postbody" rows="8" cols="80"></textarea>
        <br />Upload an image:
        <input type="file" name="postimg">
        <input type="submit" name="post" value="Post">
</form> -->


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloope</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/Footer-Dark.css">
    <link rel="stylesheet" href="assets/css/Highlight-Clean.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/Navigation-Clean1.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/untitled.css">
</head>

<body>
<header class="hidden-sm hidden-md hidden-lg">
        <div class="searchbox">
            <form>
                <h1 class="text-left">Bloope</h1>
                <div class="searchbox"><i class="glyphicon glyphicon-search"></i>
                    <input class="form-control" type="text">
                </div>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button">MENU <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li role="presentation"><a href="#">My Profile</a></li>
                        <li class="divider" role="presentation"></li>
                        <li role="presentation"><a href="index.html">Timeline </a></li>
                        <li role="presentation"><a href="messages.html">Messages </a></li>
                        <li role="presentation"><a href="notify.php">Notifications </a></li>
                        <li role="presentation"><a href="my-account.php">My Account</a></li>
                        <li role="presentation"><a href="logout.php">Logout </a></li>
                    </ul>
                </div>
            </form>
        </div>
        <hr>
    </header>
    <div>
        <nav class="navbar navbar-default hidden-xs navigation-clean">
            <div class="container">
                <div class="navbar-header"><a class="navbar-brand navbar-link" href="#"><i class="icon ion-ios-navigate"></i></a>
                    <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
                </div>
                <div class="collapse navbar-collapse" id="navcol-1">
                    <form class="navbar-form navbar-left">
                        <div class="searchbox"><i class="glyphicon glyphicon-search"></i>
                            <input class="form-control" type="text">
                        </div>
                    </form>
                    <ul class="nav navbar-nav hidden-md hidden-lg navbar-right">
                        <li role="presentation"><a href="index.html">My Timeline</a></li>
                        <li class="dropdown open"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" href="#">User <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li role="presentation"><a href="#">My Profile</a></li>
                                <li class="divider" role="presentation"></li>
                                <li role="presentation"><a href="index.html">Timeline </a></li>
                                <li role="presentation"><a href="messages.html">Messages </a></li>
                                <li role="presentation"><a href="notify.php">Notifications </a></li>
                                <li role="presentation"><a href="my-account.php">My Account</a></li>
                                <li role="presentation"><a href="logout.php">Logout </a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav hidden-xs hidden-sm navbar-right">
                        <li role="presentation"><a href="index.html">Timeline</a></li>
                        <li role="presentation"><a href="messages.html">Messages</a></li>
                        <li role="presentation"><a href="notify.php">Notifications</a></li>
                        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">User <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li role="presentation"><a href="#">My Profile</a></li>
                                <li class="divider" role="presentation"></li>
                                <li role="presentation"><a href="index.html">Timeline </a></li>
                                <li role="presentation"><a href="messages.html">Messages </a></li>
                                <li role="presentation"><a href="notify.php">Notifications </a></li>
                                <li role="presentation"><a href="my-account.php">My Account</a></li>
                                <li role="presentation"><a href="logout.php">Logout </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="container">
        <h1><?php echo $username; ?>'s Profile <?php if ($isVerified) { echo '<i class="glyphicon glyphicon-ok-sign verified" data-toggle="tooltip" title="Verified User" style="font-size:28px;color:#da052b;"></i>'; } ?>
        <form action="profile.php?username=<?php echo $username; ?>" method="post">
<?php
        if ($userid != $followerid) {
                if ($isFollowing) {
                        echo '<input type="submit" name="unfollow" value="Unfollow" style="background-image:url(&quot;none&quot;);background-color:#da052b;color:#fff;padding:16px 32px;margin:0px 0px 6px;border:none;box-shadow:none;text-shadow:none;opacity:0.9;text-transform:uppercase;font-weight:bold;font-size:13px;letter-spacing:0.4px;line-height:1;outline:none;">';
                } else {
                        echo '<input type="submit" name="follow" value="Follow" style="background-image:url(&quot;none&quot;);background-color:#da052b;color:#fff;padding:16px 32px;margin:0px 0px 6px;border:none;box-shadow:none;text-shadow:none;opacity:0.9;text-transform:uppercase;font-weight:bold;font-size:13px;letter-spacing:0.4px;line-height:1;outline:none;">';
                }
        }
        ?>
</form>
</h1></div>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><span><strong>About Me</strong></span>
                            <p>Welcome to my profile bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;bla bla&nbsp;</p>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group">
                            <div class="timelineposts">

                            </div>
                    </ul>
                </div>
                <div class="col-md-3">
                    <?php
                    if ($userid == $followerid) {
                        echo '<button class="btn btn-default" type="button" style="width:100%;background-image:url(&quot;none&quot;);background-color:#da052b;color:#fff;padding:16px 32px;margin:0px 0px 6px;border:none;box-shadow:none;text-shadow:none;opacity:0.9;text-transform:uppercase;font-weight:bold;font-size:13px;letter-spacing:0.4px;line-height:1;outline:none;" onclick="showNewPostModal()">NEW POST</button>';
                    }
                    ?>
                    <ul class="list-group"></ul>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id ="commentsModal"role="dialog" tabindex="-1" style="padding-top:100px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
                    <h4 class="modal-title">Comments</h4></div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto">
                    <p>The content of your modal.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="newPostModal" role="dialog" tabindex="-1" style="padding-top:100px;">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
                    <h4 class="modal-title">New Post</h4></div>
                <div style="max-height: 400px; overflow-y: auto">
                        <form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
                                <textarea name="postbody" rows="8" cols="80"></textarea>
                                <br />Upload an image:
                                <input type="file" name="postimg">

                </div>
                <div class="modal-footer">
                    <input type="submit" name="post" value="Post" class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:#da052b;color:#fff;padding:16px 32px;margin:0px 0px 6px;border:none;box-shadow:none;text-shadow:none;opacity:0.9;text-transform:uppercase;font-weight:bold;font-size:13px;letter-spacing:0.4px;line-height:1;outline:none;">
                    <button class="btn btn-default" type="button" data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-dark navbar-fixed-bottom" style="position: relative;">
        <footer>
            <div class="container">
                <p class="copyright">Bloope© 2021</p>
            </div>
        </footer>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-animation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>
    <script type="text/javascript">

        function scrollToAnchor(aid){
            try {
            var aTag = $(aid);
            $('html,body').animate({scrollTop: aTag.offset().top},'slow');
            } catch (error) {
                    console.log(error)
            }
        }

        $(document).ready(function() {
            $.ajax({

                type: "GET",
                url: "api/profileposts?username=<?php echo $username; ?>",
                processData: false,
                contentType: "application/json",
                data: '',
                success: function(r) {
                    console.log(r);
                    var posts = JSON.parse(r)
                    $.each(posts, function(index) {
                        if (posts[index].PostImage == "") {
                            $('.timelineposts').html(
                                $('.timelineposts').html() +
                                '<li class="list-group-item" id="'+posts[index].PostId+'"><blockquote><p>'+posts[index].PostBody+'</p><footer>Posted by '+posts[index].PostedBy+' on '+posts[index].PostDate+'<button class="btn btn-default" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"> <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+posts[index].Likes+' Likes</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-flash" style="color:#f9d616;"></i><span style="color:#f9d616;"> Comments</span></button></footer></blockquote></li>'
                            )
                        } else {
                            $('.timelineposts').html(
                                $('.timelineposts').html() +
                                '<li class="list-group-item" id="'+posts[index].PostId+'"><blockquote><p>'+posts[index].PostBody+'</p><img src="" data-tempsrc="'+posts[index].PostImage+'" class="postimg" id="img'+posts[index].postId+'"><footer>Posted by '+posts[index].PostedBy+' on '+posts[index].PostDate+'<button class="btn btn-default" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"> <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+posts[index].Likes+' Likes</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-flash" style="color:#f9d616;"></i><span style="color:#f9d616;"> Comments</span></button></footer></blockquote></li>'
                            )
                        }

                        $('[data-postid]').click(function() {
                                var buttonid = $(this).attr('data-postid');

                                $.ajax({

                                        type: "GET",
                                        url: "api/comments?postid=" + $(this).attr('data-postid'),
                                        processData: false,
                                        contentType: "application/json",
                                        data: '',
                                        success: function(r) {
                                                var res = JSON.parse(r)
                                                showCommentsModal(res);
                                        },
                                        error: function(r) {
                                                console.log(r)
                                        }

                                });
                        });

                        $('[data-id]').click(function() {
                                var buttonid = $(this).attr('data-id');
                                $.ajax({

                                        type: "POST",
                                        url: "api/likes?id=" + $(this).attr('data-id'),
                                        processData: false,
                                        contentType: "application/json",
                                        data: '',
                                        success: function(r) {
                                                var res = JSON.parse(r)
                                                $("[data-id='"+buttonid+"']").html(' <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+res.Likes+' Likes</span>')
                                        },
                                        error: function(r) {
                                                console.log(r)
                                        }

                                });
                        })
                    })
                    $('.postimg').each(function() {
                        this.src=$(this).attr('data-tempsrc')
                        this.onload = function() {
                            this.style.opacity = '1';
                        }
                    })

                    scrollToAnchor(location.hash)

                },
                error: function(r) {
                        console.log(r)
                }

            });

        });

        function showNewPostModal(res) {
                $('#newPostModal').modal('show')
        }
        function showCommentsModal(res) {
                $('#commentsModal').modal('show')
                var output = "";
                for (var i = 0; i < res.length; i++) {
                        output += res[i].Comment;
                        output += " ~ ";
                        output += res[i].CommentedBy;
                        output += "<hr />";
                }

                $('.modal-body').html(output)
        }

    </script>
</body>

</html>
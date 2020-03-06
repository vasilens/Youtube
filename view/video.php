<?php
$user_id = null;
if (isset($_SESSION["logged_user"])){
    $user_id = $_SESSION["logged_user"]["id"];
}
require_once "header.php";
require_once "navigation.php";
?>
<main>
    <?php
    if (isset($video)) {
        $video_id = $video['id'];
        echo "<video width='854' height='480' controls autoplay>
        <source src='" . $video["video_url"] . "' type='video/mp4'></video><br>";
        echo $video["title"] . "<br>";
        echo $video["date_uploaded"] . "<br>";
        echo "Views: " . $video['views'] . "<br>";
        echo $video["name"] . "<br>";
        echo "<a href='index.php?target=user&action=getById&id=" . $video["user_id"] . "'>" . $video["username"] . "</a><br>";
        if(isset($_SESSION['logged_user'])){
        echo "<button id='showPlaylists' onclick='showMyPlaylists(". $user_id." ,". $video_id .")'>Add to playlist</button>";
        echo "<table id='playlist-holder'></table>";
        }
        if ($video["owner_id"] == $user_id){
            echo "<a href='index.php?target=video&action=loadEdit&id=" . $video["id"] . "'><button>Edit video</button></a><br>";
            echo "<a href='index.php?target=video&action=delete&id=" . $video["id"] . "'><button>Delete video</button></a><br>";
        }
        else {
            if ($user_id) {
                if ($video["isFollowed"]) {
                    echo "<button id='follow-button' onclick='unfollowUser(" . $video["user_id"] . ")'>Unsubscribe</button><br>";
                } else {
                    echo "<button id='follow-button' onclick='followUser(" . $video["user_id"] . ")'>Subscribe</button><br>";
                }
            }
        }
        echo "<hr>";
        echo $video["description"] . "<br>";
    }
    else {
        header("Location:index.php");
    }
    ?>
    </p>
<img style="height: 30px;" src="styles/images/likeImg.png" id="like" <?php if ($video["isReacting"] == 1) { echo " style='color:blue' "; } else { echo " style='color:gray' "; }?>
        onclick="likeVideo(<?= $video["id"]; ?>)">
    (<span id="likes-count"><?= $video["likes"]; ?></span>)
<img style="height:30px;" src="styles/images/dislikeImg.png" id="dislike"  <?php if ($video["isReacting"] == 0) { echo " style='color:blue' "; } else { echo " style='color:gray' "; }?>
        onclick="dislikeVideo(<?= $video["id"]; ?>)">
    (<span id="dislikes-count"><?= $video["dislikes"]; ?></span>)
    <br>
    <?php
    if(isset($user_id) && !empty($user_id)){
        ?>
    Write comment:
    <form method="post" action="index.php?target=comment&action=add">
        <input type="hidden" id="video_id" name="video_id" value="<?= $video["id"]; ?>" required>
        <input type="hidden" id="owner_id" name="owner_id" value="<?= $user_id; ?>" required>
        <textarea rows="5" cols="50" id="content" name="content" required></textarea><br>
        <input type="button" onclick="addComment()" name="comment" value="Post comment">
    </form>
        <?php
    }
    ?>
        <table id="comments" class="table">
        <?php
        if (isset($comments) && $comments){
            foreach ($comments as $comment){
                if (!$comment["likes"]){
                    $comment["likes"] = 0;
                }
                if (!$comment["dislikes"]){
                    $comment["dislikes"] = 0;
                }
                echo "<tr id='comment" . $comment["id"] . "'>";
                echo "<td><img width='50px' src='" . $comment["avatar_url"] . "'></td>";
                echo "<td>" . $comment["name"] . "</td><td>" . $comment["date"] . "</td>";
                echo "<td>" . $comment["content"] . "</td>";
                echo "<td>
                    <img src='styles/images/likeImg.png' style='height: 30px;' id='like-comment" . $comment["id"] . "' onclick='likeComment(" . $comment["id"] .
                    ")'>(<span id='comment" . $comment["id"] . "-likes'>" . $comment["likes"] .
                    "</span>)<img src='styles/images/dislikeImg.png' style='height: 30px' id='dislike-comment" . $comment["id"] . "' onclick='dislikeComment(" . $comment["id"] .
                    ")'>(<span id='comment" . $comment["id"] . "-dislikes'>" . $comment["dislikes"] . "</span>)";
                if ($comment["owner_id"] == $user_id){
                    echo "<img style='height: 20px;margin-left: 5px;' src='styles/images/deleteIcon.png' id='delete-comment' onclick='deleteComment(" . $comment["id"] .
                        ")'></button>";
                }
                echo "</td></tr>";
            }
        }
        ?>
        </table>
</main>


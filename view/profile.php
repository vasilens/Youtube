<?php
if (isset($_SESSION["logged_user"])){
    $user_id = $_SESSION["logged_user"]["id"];
}
else {
    $user_id = null;
}
require_once "header.php";
require_once "navigation.php";

if (isset($user)) {
    echo "<img width=100px src='" . $user["avatar_url"] . "'>";
    echo $user["username"] . "<br>";
    echo "Name: " . $user["name"] . "<br>";
    echo "Registered on: " . $user["registration_date"] . "<br>";
    if ($user["id"] == $user_id){
        echo "<a href='index.php?target=view&action=viewRouter&view=editProfile'><button>Edit profile</button></a><br>";
    }
    elseif ($user_id) {
        if ($user["isFollowed"]) {
            echo "<button id='follow-button' onclick='unfollowUser(" . $user["id"] . ")'>Unsubscribe</button><br>";
        }
        else {
            echo "<button id='follow-button' onclick='followUser(" . $user["id"] . ")'>Subscribe</button><br>";
        }
    }
    echo "<h3>Videos:</h3>";
}
else {
    header("Location:index.php");
}
if (isset($videos)) {
    if ($videos) {
        echo "<table>";
        foreach ($videos as $video) {
            if (!$video["thumbnail_url"]) {
                $video["thumbnail_url"] = 'https://therisingnetwork.com/wp-content/plugins/video-thumbnails/default.jpg';
            }
            echo "<tr><td><a href='index.php?target=video&action=getById&id=" . $video["id"] . "'><img width='200px' src='";
            echo $video["thumbnail_url"];
            echo "'></a></td></tr>";
            echo "<tr><td><b>";
            echo $video["title"];
            echo "</b></td></tr>";
            echo "<tr><td>";
            echo $video["username"];
            echo "</td></tr>";
            echo "<tr><td>";
            echo $video["views"];
            echo " views</td></tr>";
            echo "<tr><td>";
            echo $video["date_uploaded"];
            echo "</td></tr>";
        }
        echo "</table>";
    }
    else {
        echo "<h3>No videos to show.</h3>";
    }
}
?>


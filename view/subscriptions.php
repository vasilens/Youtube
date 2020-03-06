<?php
require_once "header.php";
require_once "navigation.php";
?>
<table>
    <?php
    if(isset($subscriptions)){
        if ($subscriptions) {
            foreach ($subscriptions as $subscription) {
                echo "<tr><td rowspan='2'><img style='border-radius: 50%;' alt='No photo' width='100px' src='";
                echo $subscription['avatar_url'];
                echo "'></td>";
                echo "<td>";
                echo "<a href='index.php?target=user&action=getById&id=" . $subscription["followed_id"] . "'><b>" . $subscription["username"] . "</b></a>";
                echo "</td></tr>";
                echo "<tr><td>";
                echo $subscription['name'];
                echo "</td></tr>";
            }
        }
        else {
            echo "<h3>No active subsciptions.</h3>";
        }
    }
    if(isset($user)){
        if (!empty($user)) {
            foreach ($user as $value) {
                echo "<tr><td rowspan='2'><img style='border-radius: 50%;' alt='No photo' width='100px' src='";
                echo $value['avatar_url'];
                echo "'></td>";
                echo "<td>";
                echo $value["username"] . "</b>";
                echo "</td></tr>";
                echo "<tr><td>";
                echo $value['name'];
                echo "</td></tr>";
                echo "<div class='playlistContainer'><tr><td>";
                echo "<a href='index.php?target=playlist&action=clickedPlaylist&id=" . $value["id"] . "'><b>" . $value["playlist_title"] . "</b></a>";
                echo "</td><td>";
                echo $value["date_created"];
                echo "</td></tr></div>";
                echo "<tr><td colspan='2'><a href='index.php?target=video&action=getById&id=" . $value["video_id"] . "'><img width='200px' src='";
                echo $value["thumbnail_url"];
                echo "'></a></td></tr>";
                echo "<tr><td>";
                echo $value["title"];
                echo "</td></tr>";
                echo "<tr><td>";
                echo $value["date_uploaded"];
                echo "</td></tr>";
            }
        }
        else {
            echo "<h3>No active subsciptions.</h3>";
        }
    }
    ?>
</table>


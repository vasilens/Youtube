<?php
$user_id = null;
if(isset($_SESSION['logged_user'])){
    $user_id = $_SESSION['logged_user']['id'];
}
require_once "header.php";
?>
<nav>
    <ul>
    <li><a href="index.php?target=video&action=getAll"><img src="styles/images/homeLogo.png" class="navPics">Home</a></li>
    <li><a href="index.php?target=video&action=getTrending"><img src="styles/images/trendingLogo.png" class="navPics"">Trending</a></li>
    <li><a href="index.php?target=user&action=subscriptions&user_id=<?= $user_id ?>"><img src="styles/images/subscriptionsLogo.png" class="navPics">Subscriptions</a></li>
    <hr>
    <li><a href="index.php?target=video&action=getByOwnerId&owner_id=<?= $user_id; ?>"><img src="styles/images/libraryLogo.png" class="navPics">Library</a></li>
    <li><a href="index.php?target=video&action=getHistory"><img src="styles/images/historyLogo.png" class="navPics">History</a></li>
    <li><a href="index.php?target=video&action=getWatchLater"><img src="styles/images/watchlaterLogo.png" class="navPics">Watch Later</a></li>
    <li><a href="index.php?target=video&action=getLikedVideos"><img src="styles/images/likedLogo.png" class="navPics">Liked videos</a></li>
    <li><a href="index.php?target=playlist&action=getMyPlaylists"><img src="styles/images/playlistLogo.png" class="navPics">Playlists</a></li>
    </ul>
</nav>

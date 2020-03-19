<?php
$user_id = null;
if(isset($_SESSION['logged_user'])){
    $user_id = $_SESSION['logged_user']['id'];
}
require_once "header.php";
?>
<nav>
    <ul>
    <li><a href="/"><img src="../../styles/images/homeLogo.png" class="navPics">Home</a></li>
    <li><a href="/trending"><img src="../../styles/images/trendingLogo.png" class="navPics"">Trending</a></li>
    <li><a href="/subscriptions"><img src="../../styles/images/subscriptionsLogo.png" class="navPics">Subscriptions</a></li>
    <hr>
    <li><a href="/library/<?= $user_id; ?>"><img src="../../styles/images/libraryLogo.png" class="navPics">Library</a></li>
    <li><a href="/history"><img src="../../styles/images/historyLogo.png" class="navPics">History</a></li>
    <li><a href="/watchlater"><img src="../../styles/images/watchlaterLogo.png" class="navPics">Watch Later</a></li>
    <li><a href="/likedvideos"><img src="../../styles/images/likedLogo.png" class="navPics">Liked videos</a></li>
    <li><a href="/myplaylists"><img src="../../styles/images/playlistLogo.png" class="navPics">Playlists</a></li>
    </ul>
</nav>

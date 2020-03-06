<?php
if(isset($_SESSION['logged_user'])){
    $user_id = $_SESSION['logged_user']['id'];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="styles/style.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <script type="text/javascript" src="js/script.js"></script>
    <title>Document</title>
</head>
<body>
<header>
    <a href="index.php?target=video&action=getAll"><img class="youtubeLogo" src="styles/images/youtube_PNG5.png" alt="youtube logo"></a>
    <form id="searchForm" action="index.php?target=search&action=search" method="post">
        <input style="width: 50%;height: 29px;"  type="text" placeholder="Search" name="search_query">
        <input  type="submit" name="search" value="Search">
    </form>
    <?php
    if (isset($user_id) && !empty($user_id)){
        ?>
        <a href="index.php?target=view&action=viewRouter&view=upload"><button class="headerButtons">Upload video</button></a>
        <a href="index.php?target=user&action=getById&id=<?= $user_id; ?>"><button class="headerButtons">View profile</button></a>
        <a href="index.php?target=user&action=logout"><button class="headerButtons">Logout</button></a>
        <?php
    }
    else {
        ?>
        <a href="index.php?target=view&action=viewRouter&view=login"><button class="headerButtons">Login</button></a>
        <?php
    }
    ?>
</header>
</body>
</html>
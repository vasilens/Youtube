<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">
    <title>Youtube</title>
</head>
<body>
<?php
if(isset($msg)){
    ?>
    <div style="text-align: center;" class="alert alert-danger" role="alert">
        <?php echo $msg ?>
    </div>
    <?php
}
?>
<div class="container">
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <header class="card-header">
                <a href="index.php?target=view&action=viewRouter&view=login" class="float-right btn btn-outline-primary mt-1">Log in</a>
                <h4 class="card-title mt-2">Sign up</h4>
            </header>
            <article class="card-body">
                <form action="index.php?target=user&action=register" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" placeholder="" required>
                    </div>
                    <div class="form-group">
                        <label>Email address</label>
                        <input type="email" name="email" class="form-control" placeholder="" required>
                        <small class="form-text text-muted">We'll never share your email with anyone else.</small>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control" type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm password</label>
                        <input type="password" name="cpassword" class="form-control" placeholder="" required>
                    </div>
                    <div class="form-group">
                        <label>Add avatar</label>
                        <input class="form-control" type="file" name="avatar">
                    </div>
                    <div class="form-group">
                        <button type="submit" name="register" class="btn btn-primary btn-block"> Register  </button>
                    </div>
                    <small class="text-muted">By clicking the 'Sign Up' button, you confirm that you accept our <br> Terms of use and Privacy Policy.</small>
                </form>
            </article>
            <div class="border-top card-body text-center">Already have an account? <a href="index.php?target=view&action=viewRouter&view=login">Log In</a></div>
        </div>
    </div>
</div>
</div>
</body>
</html>

<?php
if (!isset($_SESSION["logged_user"])){
    header("Location:index.php");
}
$user = $_SESSION['logged_user'];
require_once "header.php";
require_once "navigation.php";
?>
<form action="index.php?target=user&action=edit" method="post" enctype="multipart/form-data">
    <table>
        <tr>
            <td><label for="username"><b>Username:</b></label></td>
            <td><input type="text" id="username" name="username" value="<?= $user['username'] ?>" required></td>
        </tr>
        <tr>
            <td><label for="email"><b>Email:</b></label></td>
            <td><input type="email" id="email" name="email" value="<?= $user['email'] ?>" required></td>
        </tr>
        <tr>
            <td><label for="full_name"><b>Name:</b></label></td>
            <td><input type="text" id="full_name" name="full_name" value="<?= $user['full_name'] ?>" required></td>
        </tr>
        <tr>
            <td><label for="password"><b>Current password:</b></label></td>
            <td><input type="password" id="password" name="password" required></td>
        </tr>
        <tr>
            <td><label for="new_password"><b>New password:</b></label></td>
            <td><input type="password" id="new_password" name="new_password"></td>
        </tr>
        <tr>
            <td><label for="cpassword"><b>Confirm password:</b></label></td>
            <td><input type="password" id="cpassword" name="cpassword"></td>
        </tr>
        <tr>
            <td><label for="avatar"><b>Change avatar</b></label></td>
            <td><input type="file" name="avatar" id="avatar"</td>
        </tr>
        <tr>
            <td><input type="submit" name="edit" value="Edit"></td>
        </tr>
    </table>
</form>
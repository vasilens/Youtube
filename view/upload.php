<?php
if (!isset($_SESSION["logged_user"])) {
    header("Location:index.php");
}
$user_id = $_SESSION["logged_user"]["id"];
if (!isset($categories)){
    header("Location:index.php");
}
require_once "header.php";
require_once "navigation.php";
?>
<!--<button><a href="index.php">Home</a></button>-->
<form action="index.php?target=video&action=upload" method="post" enctype="multipart/form-data">
    <table>
        <tr>
            <td><label for="title"><b>Title:</b></label></td>
            <td><input type="text" id="title" name="title" required></td>
        </tr>
        <tr>
            <td><label for="description"><b>Description:</b></label></td>
            <td><textarea id="description" name="description" required></textarea></td>
        </tr>
        <input type="hidden" name="owner_id" value="<?= $_SESSION["logged_user"]["id"]; ?>"
        <tr>
            <td><label for="category_id"><b>Category:</b></label></td>
            <td><select id="category_id" name="category_id" required>
                    <?php
                    foreach ($categories as $category){
                        echo "<option value='". $category["id"] . "'>" . $category["name"] . "</option>";
                    }
                    ?>
                </select></td>
        </tr>
        <tr>
            <td><label for="video"><b>Video:</b></label></td>
            <td><input type="file" id="video" name="video" required></td>
        </tr>
        <tr>
            <td><label for="thumbnail"><b>Thumbnail:</b></label></td>
            <td><input type="file" id="thumbnail" name="thumbnail"</td>
        </tr>
        <tr>
            <td><input type="submit" name="upload" value="Upload"></td>
        </tr>
    </table>
</form>
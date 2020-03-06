<?php
if (!isset($_SESSION["logged_user"]["id"])) {
    header("Location:index.php");
}
$user_id = $_SESSION["logged_user"]["id"];
if (!isset($video) || !isset($categories)){
    header("Location:index.php");
}
require_once "header.php";
require_once "navigation.php";
?>
<form action="index.php?target=video&action=edit" method="post" enctype="multipart/form-data">
    <table>
        <input type="hidden" name="id" value="<?= $video["id"]; ?>"
        <tr>
            <td><label for="title"><b>Title:</b></label></td>
            <td><input type="text" id="title" name="title" value="<?= $video["title"] ?>" required></td>
        </tr>
        <tr>
            <td><label for="description"><b>Description:</b></label></td>
            <td><textarea id="description" name="description" required><?= $video["description"]; ?></textarea></td>
        </tr>
        <input type="hidden" name="owner_id" value="<?= $_SESSION["logged_user"]["id"]; ?>"
        <tr>
            <td><label for="category_id"><b>Category:</b></label></td>
            <td><select id="category_id" name="category_id" required>
                    <?php
                    foreach ($categories as $category){
                        echo "<option value='". $category["id"] . "'";
                        if ($category["id"] == $video["category_id"]){
                            echo " selected";
                        }
                        echo ">" . $category["name"] . "</option>";
                    }
                    ?>
                </select></td>
        </tr>
        <tr>
            <td><b>Current thumbnail:</b></td>
            <td><img width="200px" src="<?= $video["thumbnail_url"] ?>" </td>
        </tr>
        <tr>
            <td><label for="thumbnail"><b>Add new:</b></label></td>
            <td><input type="file" id="thumbnail" name="thumbnail"></td>
            <input type="hidden" name="thumbnail_url" value="<?= $video["thumbnail_url"] ?>">
        </tr>
        <tr>
            <td><input type="submit" name="edit" value="Edit"></td>
        </tr>
    </table>
</form>
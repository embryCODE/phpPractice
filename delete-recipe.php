<?php
include('db.php');
$db = new db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the id of the recipe to delete
        $recipeId = $_REQUEST['recipe-id'];

        // Call a method on the db which will do the deleting
        $db->deleteRecipe($recipeId);

        // If successfull, go home
        header("Location: /");
    } catch (PDOException $e) {
        echo $e;
    }
}

include('html/topHTML.php');
?>

<h1>Delete recipe</h1>

<p>Are you sure you want to delete the recipe with the id
    of <?php echo $_GET['recipe-id'] ?>?</p>

<form method="post">
    <input type="submit" value="Yes, delete"/>
    <a href="javascript:history.back()">Cancel</a>
</form>

<?php include('html/bottomHTML.php') ?>


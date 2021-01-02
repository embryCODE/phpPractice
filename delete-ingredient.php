<?php
include('db.php');
$db = new db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the id of the ingredient to delete
        $ingredientId = $_REQUEST['ingredient-id'];

        // Call a method on the db which will do the deleting
        $db->deleteIngredient($ingredientId);

        // If successfull, go to all-ingredients page
        header("Location: /all-ingredients.php");
    } catch (PDOException $e) {
        echo $e;
    }
}

include('html/topHTML.php');
?>

<h1>Delete ingredient</h1>

<p>Are you sure you want to delete the ingredient with the id
    of <?php echo $_GET['ingredient-id'] ?>?</p>

<form method="post">
    <input type="submit" value="Yes, delete"/>
    <a href="javascript:history.back()">Cancel</a>
</form>

<?php include('html/bottomHTML.php') ?>


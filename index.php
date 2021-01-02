<?php
include('db.php');
include('html/topHTML.php');
?>

<h1>All recipes</h1>

<?php
$db = new db();
$allRecipes = $db->getAllRecipes();

foreach ($allRecipes as $recipe) {
    $ingredients = $db->getIngredientsByRecipeId($recipe['id'])

    ?>
    <div class="recipe-card">
        <h2><?php echo ucfirst($recipe['name']) ?></h2>
        <h3>Ingredients</h3>
        <ul>
            <?php foreach ($ingredients as $ingredient) { ?>
                <li><?php echo $ingredient['name'] ?></li>
            <?php } ?>
        </ul>

        <h3>Instructions</h3>
        <p><?php echo $recipe['instructions'] ?></p>

        <a href="/add-edit-recipe.php?recipe-id=<?php echo $recipe['id'] ?>">Edit</a> | <a href="/delete-recipe.php?recipe-id=<?php echo $recipe['id'] ?>">Delete</a>
    </div>
<?php } ?>


<?php include('html/bottomHTML.php') ?>


<?php
include('db.php');

$db = new db();
$ingredients = $db->getAllIngredients();
$isEditMode = (bool)$_REQUEST['recipe-id'];
$recipeToEdit = null;
$ingredientsInThisRecipe = null;

/**
 * Check to see if the ingredient is in the recipe, by ID.
 *
 * @param $ingredient
 * @param $ingredientsInThisRecipe
 * @return bool
 */
function isIngredientInThisRecipe($ingredient, $ingredientsInThisRecipe): bool
{
    if (!$ingredientsInThisRecipe) return false;

    $isInRecipe = false;

    foreach ($ingredientsInThisRecipe as $currentIngredientToCheck) {
        if ((int )$ingredient['id'] === (int)$currentIngredientToCheck['id']) {
            $isInRecipe = true;
        }
    }

    return $isInRecipe;
}

function addNewRecipe($db, $name, $instructions, $ingredients)
{
    $newRecipeId = $db->addNewRecipe(['name' => $name, 'instructions' => $instructions]);
    $db->addIngredientsToRecipe($newRecipeId, $ingredients);
}

function editRecipe($recipeId, $db, $name, $instructions, $ingredients)
{
    $db->editRecipe(['id' => $recipeId, 'name' => $name, 'instructions' => $instructions]);
    $db->editIngredientsOnRecipe($recipeId, $ingredients);
}

if ($isEditMode) {
    $recipeId = $_REQUEST['recipe-id'];
    $recipeToEdit = $db->getRecipe($recipeId);
    $ingredientsInThisRecipe = $db->getIngredientsByRecipeId($recipeId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $instructions = $_POST['instructions'];
    $ingredients = $_POST['ingredients'];

    try {
        if ($name && $instructions) {
            if ($isEditMode) {
                editRecipe($recipeToEdit['id'], $db, $name, $instructions, $ingredients);
            } else {
                addNewRecipe($db, $name, $instructions, $ingredients);
            }

            header("Location: /");
        } else {
            ?>
            <p>Invalid form</p>
            <?php
        }
    } catch (PDOException $e) {
        ?>
        <p>There was an error:</p>
        <pre><?php echo $e ?></pre>
        <?php
    }
}

include('html/topHTML.php');
?>

<h1><?php echo $isEditMode ? 'Edit' : 'Add' ?> recipe</h1>

<form method="post">
    <label for="recipe-name">Name</label>
    <br/>
    <input id="recipe-name" name="name"
           autofocus
           value="<?php echo $recipeToEdit['name'] ?>"/>
    <br/>
    <label for="recipe-instructions">Instructions</label>
    <br/>
    <textarea id="recipe-instructions"
              name="instructions"><?php echo $recipeToEdit['instructions'] ?></textarea>
    <br/>
    <label for="recipe-ingredients">Ingredients</label>
    <br/>
    <select id="recipe-ingredients" name="ingredients[]" multiple>
        <?php foreach ($ingredients as $ingredient) {
            ?>

            <option <?php echo isIngredientInThisRecipe($ingredient, $ingredientsInThisRecipe) ? 'selected' : '' ?>
                    value="<?php echo $ingredient['id'] ?>"><?php echo ucfirst($ingredient['name']) ?></option>

            <?php
        } ?>
    </select>
    <br/>
    <input type="submit"
           value="<?php echo $isEditMode ? 'Edit' : 'Add' ?> recipe"/>
</form>

<?php include('html/bottomHTML.php') ?>


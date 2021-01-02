<?php
include('db.php');

$db = new db();
$ingredients = $db->getAllIngredients();
$isEditMode = (bool)$_REQUEST['ingredient-id'];
$ingredientToEdit = null;

if ($isEditMode) {
    $ingredientId = $_REQUEST['ingredient-id'];
    $ingredientToEdit = $db->getIngredient($ingredientId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];

    try {
        if ($name && $quantity) {
            if ($isEditMode) {
                $db->editIngredient(
                    [
                        'id' => $ingredientId,
                        'name' => $name,
                        'quantity' => $quantity
                    ]);
            } else {
                $newIngredientId = $db->addNewIngredient(
                    ['name' => $name,
                        'quantity' => $quantity
                    ]);
            }

            header("Location: /all-ingredients.php");
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

<h1><?php echo $isEditMode ? 'Edit' : 'Add' ?> ingredient</h1>

<form method="post">
    <label for="ingredient-name">Name</label>
    <br/>
    <input id="ingredient-name" name="name"
           autofocus
           value="<?php echo $ingredientToEdit['name'] ?>"/>
    <br/>
    <label for="ingredient-quantity">Quantity</label>
    <br/>
    <input type="number" id="ingredient-quantity" name="quantity"
           value="<?php echo $ingredientToEdit['quantity'] ?>"/>
    <br/>
    <input type="submit"
           value="<?php echo $isEditMode ? 'Edit' : 'Add' ?> ingredient"/>
</form>

<?php include('html/bottomHTML.php') ?>


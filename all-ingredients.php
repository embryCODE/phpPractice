<?php
include('db.php');
include('html/topHTML.php');
?>

<h1>All ingredients</h1>

<?php
$db = new db();
$allIngredients = $db->getAllIngredients();
?>

<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Quantity</th>
        <th>Actions</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($allIngredients as $ingredient) {
        $ingredients = $db->getIngredientsByRecipeId($ingredient['id'])
        ?>
        <tr>
            <td><?php echo ucfirst($ingredient['name']) ?></td>
            <td><?php echo ucfirst($ingredient['quantity']) ?></td>
            <td>
                <a href="/add-edit-ingredient.php?ingredient-id=<?php echo $ingredient['id'] ?>">Edit</a>
                |
                <a href="/delete-ingredient.php?ingredient-id=<?php echo $ingredient['id'] ?>">Delete</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?php include('html/bottomHTML.php') ?>

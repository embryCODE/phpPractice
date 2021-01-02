<?php

class db
{
    protected $connectionDetails;

    public function __construct($dbhost = 'db', $dbuser = 'user', $dbpass = 'pass', $dbname = 'php-practice', $charset = 'utf8')
    {
        $this->connectionDetails = [
            'dbhost' => $dbhost,
            'dbuser' => $dbuser,
            'dbpass' => $dbpass,
            'dbname' => $dbname,
            'charset' => $charset,

        ];
    }

    public function getRecipe($recipeIdToGet)
    {
        $conn = $this->getDBConnection();
        $result = $conn->query('SELECT * FROM recipes WHERE id = ' . $recipeIdToGet);
        return $result->fetch_array();
    }

    public function getAllRecipes(): array
    {
        $conn = $this->getDBConnection();
        $result = $conn->query('SELECT * from recipes');

        return $this->createArrayFromResult($result);
    }

    public function getIngredientsByRecipeId($recipeId): array
    {
        $conn = $this->getDBConnection();
        $sql = <<<'QUERY'
            SELECT *
            from ingredients
                     LEFT OUTER JOIN recipes_and_ingredients
                                     on ingredients.id =
                                        recipes_and_ingredients.ingredient_id
            WHERE recipe_id = ?;
QUERY;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $this->createArrayFromResult($result);
    }

    public function getAllIngredients(): array
    {
        $conn = $this->getDBConnection();
        $result = $conn->query('SELECT * from ingredients');

        return $this->createArrayFromResult($result);
    }

    public function getIngredient($ingredientIdToGet)
    {
        $conn = $this->getDBConnection();
        $result = $conn->query('SELECT * FROM ingredients WHERE id = ' . $ingredientIdToGet);
        return $result->fetch_array();
    }

    /**
     * @param array $recipeToAdd
     * @return int|string
     * @throws PDOException
     */
    public function addNewRecipe(array $recipeToAdd)
    {
        $conn = $this->getDBConnection();
        $sql = <<<'QUERY'
                INSERT INTO recipes (name, instructions)
                    VALUES (?, ?)
QUERY;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $recipeToAdd['name'], $recipeToAdd['instructions']);
        $stmt->execute();

        if ($stmt->error) {
            throw new PDOException($stmt->error);
        }

        $stmt->close();

        $newRecipeId = $conn->insert_id;
        $conn->close();

        return $newRecipeId;
    }

    public function addIngredientsToRecipe($recipeId, $ingredients)
    {
        if (!$ingredients) return;

        $conn = $this->getDBConnection();

        foreach ($ingredients as $ingredientId) {
            $ingredientIdAsNumber = strval($ingredientId);
            $quantity = 1; // Laziness

            $sql = <<<'QUERY'
                INSERT INTO recipes_and_ingredients (recipe_id, ingredient_id, ingredient_quantity)
                    VALUES (?, ?, ?)
QUERY;
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $recipeId, $ingredientIdAsNumber, $quantity);
            $stmt->execute();

            if ($stmt->error) {
                throw new PDOException($stmt->error);
            }

            $stmt->close();
        }

        $conn->close();
    }

    public function editIngredientsOnRecipe($recipeId, $ingredients)
    {
        $conn = $this->getDBConnection();
        $conn->query('
            DELETE
            FROM recipes_and_ingredients
            WHERE recipe_id = 
        ' . $recipeId);

        $this->addIngredientsToRecipe($recipeId, $ingredients);
    }

    /**
     * @param array $recipeToAdd
     * @throws PDOException
     */
    public function editRecipe(array $recipeToAdd)
    {
        $conn = $this->getDBConnection();
        $sql = <<<'QUERY'
                UPDATE recipes
                    SET name=?,
                        instructions=?
                    WHERE id = ?
QUERY;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $recipeToAdd['name'], $recipeToAdd['instructions'], $recipeToAdd['id']);
        $stmt->execute();

        if ($stmt->error) {
            throw new PDOException($stmt->error);
        }

        $stmt->close();
        $conn->close();
    }

    public function deleteRecipe(string $recipeIdToDelete)
    {
        $recipeIdAsInt = (int)$recipeIdToDelete;
        $conn = $this->getDBConnection();
        $conn->query('DELETE FROM recipes WHERE id = ' . $recipeIdAsInt);
        $conn->close();
    }

    /**
     * @param array $ingredientToAdd
     * @return int|string
     * @throws PDOException
     */
    public function addNewIngredient(array $ingredientToAdd)
    {
        $conn = $this->getDBConnection();
        $sql = <<<'QUERY'
            INSERT INTO ingredients (name, quantity)
                VALUES (?, ?)
QUERY;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $ingredientToAdd['name'], $ingredientToAdd['quantity']);
        $stmt->execute();

        if ($stmt->error) {
            throw new PDOException($stmt->error);
        }

        $stmt->close();

        $newIngredientId = $conn->insert_id;
        $conn->close();

        return $newIngredientId;
    }

    /**
     * @param array $ingredientToAdd
     * @return int|string
     * @throws PDOException
     */
    public function editIngredient(array $ingredientToAdd)
    {
        $conn = $this->getDBConnection();
        $sql = <<<'QUERY'
            UPDATE ingredients
                SET name=?,
                    quantity=?
                WHERE id = ?
QUERY;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $ingredientToAdd['name'], $ingredientToAdd['quantity'], $ingredientToAdd['id']);
        $stmt->execute();

        if ($stmt->error) {
            throw new PDOException($stmt->error);
        }

        $stmt->close();
        $conn->close();
    }

    public function deleteIngredient(string $ingredientIdToDelete)
    {
        $ingredientIdAsInt = (int)$ingredientIdToDelete;
        $conn = $this->getDBConnection();
        $conn->query('DELETE FROM ingredients WHERE id = ' . $ingredientIdAsInt);
        $conn->close();
    }

    private function getDBConnection()
    {
        $conn = mysqli_connect(
            $this->connectionDetails['dbhost'],
            $this->connectionDetails['dbuser'],
            $this->connectionDetails['dbpass'],
            $this->connectionDetails['dbname']);

        if ($conn->connect_error) {
            $this->error('Failed to connect to MySQL - ' . $conn->connect_error);
        }

        $conn->set_charset($this->connectionDetails['charset']);

        return $conn;
    }

    private function createArrayFromResult($result): array
    {
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function error(string $error)
    {
        exit($error);
    }
}
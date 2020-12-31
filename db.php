<?php

class db
{
    protected $connection;

    public function __construct($dbhost = 'db', $dbuser = 'user', $dbpass = 'pass', $dbname = 'php-practice', $charset = 'utf8')
    {
        $this->connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

        if ($this->connection->connect_error) {
            $this->error('Failed to connect to MySQL - ' . $this->connection->connect_error);
        }

        $this->connection->set_charset($charset);
    }

    public function getAllRecipes(): array
    {
        $conn = $this->connection;
        $result = $conn->query('SELECT * from recipes');

        return $this->createArrayFromResult($result);
    }

    public function getIngredientsByRecipeId($recipeId): array
    {
        $conn = $this->connection;
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

    private function createArrayFromResult($result): array {
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function error(string $error)
    {
        exit($error);
    }
}
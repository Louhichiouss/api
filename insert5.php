<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
    die();
}

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Bad Request!. Only POST method is allowed',
    ]);
    exit;
}

require 'db_connect.php';
$database = new Operations();
$conn = $database->dbConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->recette) || !isset($data->depense) || !isset($data->description) || !isset($data->date)|| !isset($data->jour)) {
    echo json_encode([
        'success' => 0,
        'message' => 'Please enter compulsory fields',
    ]);
    exit;
} elseif (empty(trim($data->recette)) || empty(trim($data->depense)) || empty(trim($data->description)) || empty(trim($data->date))|| empty(trim($data->jour))) {
    echo json_encode([
        'success' => 0,
        'message' => 'Field cannot be empty. Please fill all the fields.',
    ]);
    exit;
}

try {
    $recette = htmlspecialchars(trim($data->recette));
    $depense = htmlspecialchars(trim($data->depense));
    $description = htmlspecialchars(trim($data->description));
    $date = htmlspecialchars(trim($data->date));
    $jour = htmlspecialchars(trim($data->jour));
    $beneficie = $recette - $depense;
  
    $query = "INSERT INTO `recette` (
        `recette`,
        `depense`,
        `description`,
        `beneficie`,
        `date`,
        `jour`
    ) VALUES (
        :recette,
        :depense,
        :description,
        :beneficie,
        :date,
        :jour
    )";

    $stmt = $conn->prepare($query);

    $stmt->bindValue(':recette', $recette, PDO::PARAM_STR);
    $stmt->bindValue(':depense', $depense, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':beneficie', $beneficie, PDO::PARAM_STR);
    $stmt->bindValue(':date', $date, PDO::PARAM_STR);
    $stmt->bindValue(':jour', $jour, PDO::PARAM_STR);

   
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            'success' => 1,
            'message' => 'Data Inserted Successfully.',
        ]);
        exit;
    }

    echo json_encode([
        'success' => 0,
        'message' => 'There is some problem in data inserting',
    ]);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage(),
    ]);
    exit;
}

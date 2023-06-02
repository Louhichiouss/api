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

if (!isset($data->Nom) || !isset($data->Description) || !isset($data->Quantite) || !isset($data->Prix) || !isset($data->Date)) {
    echo json_encode([
        'success' => 0,
        'message' => 'Please enter compulsory fields',
    ]);
    exit;
} elseif (empty(trim($data->Nom)) || empty(trim($data->Description)) || empty(trim($data->Quantite)) || empty(trim($data->Prix)) || empty(trim($data->Date))) {
    echo json_encode([
        'success' => 0,
        'message' => 'Field cannot be empty. Please fill all the fields.',
    ]);
    exit;
}

try {
    $Nom = htmlspecialchars(trim($data->Nom));
    $Description = htmlspecialchars(trim($data->Description));
    $Quantite = htmlspecialchars(trim($data->Quantite));
    $Prix = htmlspecialchars(trim($data->Prix));
    $Date = htmlspecialchars(trim($data->Date));
  
    $query = "INSERT INTO `matriel` (
        `Nom`,
        `Description`,
        `Quantite`,
        `Prix`,
        `Date`
    ) VALUES (
        :Nom,
        :Description,
        :Quantite,
        :Prix,
        :Date
    )";

    $stmt = $conn->prepare($query);

    $stmt->bindValue(':Nom', $Nom, PDO::PARAM_STR);
    $stmt->bindValue(':Description', $Description, PDO::PARAM_STR);
    $stmt->bindValue(':Quantite', $Quantite, PDO::PARAM_STR);
    $stmt->bindValue(':Prix', $Prix, PDO::PARAM_STR);
    $stmt->bindValue(':Date', $Date, PDO::PARAM_STR);
   
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

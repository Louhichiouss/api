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

if (  !isset($data->date)|| !isset($data->condition) || !isset($data->heure) || !isset($data->msg)) {
    echo json_encode([
        'success' => 0,
        'message' => 'Please enter compulsory fields',
    ]);
    exit;
} elseif (  empty(trim($data->date)) || empty(trim($data->condition))  || empty(trim($data->heure)) || empty(trim($data->msg))) {
    echo json_encode([
        'success' => 0,
        'message' => 'Field cannot be empty. Please fill all the fields.',
    ]);
    exit;
}

try {
    
    $date = htmlspecialchars(trim($data->date));
    $condition = htmlspecialchars(trim($data->condition));
    $heure = htmlspecialchars(trim($data->heure));
    $msg = htmlspecialchars(trim($data->msg));


    $query = "INSERT INTO `register` (
       
        `date`,
        `condition`,
        `heure`,
        'msg'
    ) VALUES (
        
        :date,
        :condition,
        :heure;
        :msg
    )";

    $stmt = $conn->prepare($query);

    $stmt->bindValue(':date', $date, PDO::PARAM_STR);
    $stmt->bindValue(':condition', $condition, PDO::PARAM_STR);
    $stmt->bindValue(':heure', $heure, PDO::PARAM_STR);
    $stmt->bindValue(':msg', $msg, PDO::PARAM_STR);


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

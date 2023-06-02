












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

if (!isset($data->text) || !isset($data->start) || !isset($data->end) ) {
    echo json_encode([
        'success' => 0,
        'message' => 'Please enter compulsory fields',
    ]);
    exit;
} elseif (empty(trim($data->text)) || empty(trim($data->start)) || empty(trim($data->end)) ) {
    echo json_encode([
        'success' => 0,
        'message' => 'Field cannot be empty. Please fill all the fields.',
    ]);
    exit;
}

try {
    $text = htmlspecialchars(trim($data->text));
    $start = htmlspecialchars(trim($data->start));
    $end= htmlspecialchars(trim($data->end));
  
    $query = "INSERT INTO `events` (
        `text`,
        `start`,
        `end`,
    ) VALUES (
        :text,
        :start,
        :end,
    )";

    $stmt = $conn->prepare($query);

    $stmt->bindValue(':text', $text, PDO::PARAM_STR);
    $stmt->bindValue(':start', $start, PDO::PARAM_STR);
    $stmt->bindValue(':end', $end, PDO::PARAM_STR);
   
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
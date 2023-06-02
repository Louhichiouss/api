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

if (!isset($data->username) || !isset($data->email) || !isset($data->tel)  || !isset($data->loc)  || !isset($data->sexe) || !isset($data->mp) || !isset($data->cmp)) {
    echo json_encode([
        'success' => 0,
        'message' => 'Please enter compulsory fields',
    ]);
    exit;
} elseif (empty(trim($data->username)) || empty(trim($data->email)) || empty(trim($data->tel)) || empty(trim($data->loc)) || empty(trim($data->sexe)) || empty(trim($data->mp)) || empty(trim($data->cmp))) {
    echo json_encode([
        'success' => 0,
        'message' => 'Field cannot be empty. Please fill all the fields.',
    ]);
    exit;
}

try {
    $username = htmlspecialchars(trim($data->username));
    $email = htmlspecialchars(trim($data->email));
    $tel = htmlspecialchars(trim($data->tel));
    
    $loc = htmlspecialchars(trim($data->loc));
    $sexe = htmlspecialchars(trim($data->sexe));

    $mp = htmlspecialchars(trim($data->mp));
    $cmp = htmlspecialchars(trim($data->cmp));

    $query = "INSERT INTO `med` (
        `username`,
        `email`,
        `tel`,
        `loc`,
        `sexe`,
        `mp`,
        `cmp`
    ) VALUES (
        :username,
        :email,
        :tel,
        :loc,
        :sexe,
        :mp,
        :cmp
    )";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':tel', $tel, PDO::PARAM_STR);
    $stmt->bindValue(':loc', $loc, PDO::PARAM_STR);
    $stmt->bindValue(':sexe', $sexe, PDO::PARAM_STR);
    $stmt->bindValue(':mp', $mp, PDO::PARAM_STR);
    $stmt->bindValue(':cmp', $cmp, PDO::PARAM_STR);

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

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

if (!isset($data->P_nom) || !isset($data->P_prenom) || !isset($data->P_tel) || !isset($data->P_email) || !isset($data->P_region) || !isset($data->P_c)|| !isset($data->P_sexe) || !isset($data->P_nbs) || !isset($data->P_pt)) {
    echo json_encode([
        'success' => 0,
        'message' => 'Please enter compulsory fields',
    ]);
    exit;
} elseif (empty(trim($data->P_nom)) || empty(trim($data->P_prenom)) || empty(trim($data->P_tel)) || empty(trim($data->P_email)) || empty(trim($data->P_region))|| empty(trim($data->P_c)) || empty(trim($data->P_sexe)) || empty(trim($data->P_nbs)) || empty(trim($data->P_pt))) {
    echo json_encode([
        'success' => 0,
        'message' => 'Field cannot be empty. Please fill all the fields.',
    ]);
    exit;
}

try {
    $P_nom = htmlspecialchars(trim($data->P_nom));
    $P_prenom = htmlspecialchars(trim($data->P_prenom));
    $P_tel = htmlspecialchars(trim($data->P_tel));
    $P_email = htmlspecialchars(trim($data->P_email));
    $P_region = htmlspecialchars(trim($data->P_region));
    $P_c = htmlspecialchars(trim($data->P_c));
    $P_sexe = htmlspecialchars(trim($data->P_sexe));
    $P_nbs = htmlspecialchars(trim($data->P_nbs));
    $P_pt = htmlspecialchars(trim($data->P_pt));



    $query = "INSERT INTO `patient` (
        `P_nom`,
        `P_prenom`,
        `P_tel`,
        `P_email`,
        `P_region`,
        `P_c`,
        `P_sexe`,
        `P_nbs`,
        `P_pt`
    ) VALUES (
        
        :P_nom,
        :P_prenom,
        :P_tel,
        :P_email,
        :P_region,
        :P_c,
        :P_sexe,
        :P_nbs,
        :P_pt
    )";

    $stmt = $conn->prepare($query);

    $stmt->bindValue(':P_nom', $P_nom, PDO::PARAM_STR);
    $stmt->bindValue(':P_prenom', $P_prenom, PDO::PARAM_STR);
    $stmt->bindValue(':P_tel', $P_tel, PDO::PARAM_STR);
    $stmt->bindValue(':P_email', $P_email, PDO::PARAM_STR);
    $stmt->bindValue(':P_region', $P_region, PDO::PARAM_STR);
    $stmt->bindValue(':P_c', $P_c, PDO::PARAM_STR);
    $stmt->bindValue(':P_sexe', $P_sexe, PDO::PARAM_STR);
    $stmt->bindValue(':P_nbs', $P_nbs, PDO::PARAM_STR);
    $stmt->bindValue(':P_pt', $P_pt, PDO::PARAM_STR);


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

<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
    die();
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Bad Request detected! Only PUT method is allowed',
    ]);
    exit;
endif;

require 'db_connect.php';
$database = new Operations();
$conn = $database->dbConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode(['success' => 0, 'message' => 'Please enter correct patient id.']);
    exit;
}

try {
    $fetch_post = "SELECT * FROM `med` WHERE id=:id";
    $fetch_stmt = $conn->prepare($fetch_post);
    $fetch_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);
    $fetch_stmt->execute();

    if ($fetch_stmt->rowCount() > 0) :
        $row = $fetch_stmt->fetch(PDO::FETCH_ASSOC);
        $date= isset($data->date) ? $data->date : $row['date'];
        $conditions = isset($data->conditions) ? $data->conditions : $row['conditions'];
        $heure = isset($data->heure) ? $data->heure : $row['heure'];
        $msg = isset($data->msg) ? $data->msg : $row['msg'];

        $update_query = "UPDATE `med` SET date = :date, conditions= :conditions, heure= :heure, msg = :msg , action = ''   WHERE id = :id";

        $update_stmt = $conn->prepare($update_query);

        $update_stmt->bindValue(':date', htmlspecialchars(strip_tags($date)), PDO::PARAM_STR);
        $update_stmt->bindValue(':conditions', htmlspecialchars(strip_tags($conditions)), PDO::PARAM_STR);
        $update_stmt->bindValue(':heure', htmlspecialchars(strip_tags($heure)), PDO::PARAM_STR);
        $update_stmt->bindValue(':msg', htmlspecialchars(strip_tags($msg)), PDO::PARAM_STR);
        $update_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            echo json_encode([
                'success' => 1,
                'message' => 'Record updated successfully'
            ]);
            exit;
        }

        echo json_encode([
            'success' => 0,
            'message' => 'Did not update. Something went wrong.'
        ]);
        exit;

    else :
        echo json_encode(['success' => 0, 'message' => 'Invalid ID. No record found by the ID.']);
        exit;
    endif;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}

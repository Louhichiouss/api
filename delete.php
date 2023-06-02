<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: DELETE");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
    die();
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Bad Request detected. HTTP method should be DELETE',
    ]);
} else {
    require 'db_connect.php';
    $database = new Operations();
    $conn = $database->dbConnection();

    $data = json_decode(file_get_contents("php://input"));
    $id =  $_GET['id'];

    if (!isset($id)) {
        echo json_encode(['success' => false, 'message' => 'Please provide the post ID.']);
    } else {
        try {
            $fetch_post = "SELECT * FROM `patient` WHERE P_id=:P_id";
            $fetch_stmt = $conn->prepare($fetch_post);
            $fetch_stmt->bindValue(':P_id', $id, PDO::PARAM_INT);
            $fetch_stmt->execute();

            if ($fetch_stmt->rowCount() > 0) {
                $delete_post = "DELETE FROM `patient` WHERE P_id=:P_id";
                $delete_post_stmt = $conn->prepare($delete_post);
                $delete_post_stmt->bindValue(':P_id', $id, PDO::PARAM_INT);

                if ($delete_post_stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Record Deleted successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Could not delete. Something went wrong.'
                    ]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid ID. No posts found by the ID.']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}

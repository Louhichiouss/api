<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: DELETE");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
    die();
}

if ($method !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Bad Request detected. HTTP method should be DELETE',
    ]);
} else {
    require 'db_connect.php';
    $database = new Operations();
    $conn = $database->dbConnection();

    $id = $_GET['id'];

    if (!isset($id) || empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please provide the post ID.']);
    } else {
        try {
            $fetch_post = "SELECT * FROM `recette` WHERE id=:id";
            $fetch_stmt = $conn->prepare($fetch_post);
            $fetch_stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $fetch_stmt->execute();

            if ($fetch_stmt->rowCount() > 0) {
                $delete_post = "DELETE FROM `recette` WHERE id=:id";
                $delete_post_stmt = $conn->prepare($delete_post);
                $delete_post_stmt->bindValue(':id', $id, PDO::PARAM_INT);

                if ($delete_post_stmt->execute()) {
                    http_response_code(200);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Record deleted successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Could not delete. Something went wrong.'
                    ]);
                }
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Invalid ID. No posts found by the ID.']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}

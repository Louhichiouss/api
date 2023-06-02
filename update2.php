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

// print_r($data);

// die();


//print_r($hobbies);


if (!isset($data->id)) {
    echo json_encode(['success' => 0, 'message' => 'Please enter correct matriel id.']);
    exit;
}

try {

    $fetch_post = "SELECT * FROM `recette` WHERE id=:id";
    $fetch_stmt = $conn->prepare($fetch_post);
    $fetch_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);
    $fetch_stmt->execute();

    if ($fetch_stmt->rowCount() > 0) :
     //echo 'AAA';
        $row = $fetch_stmt->fetch(PDO::FETCH_ASSOC);
        $recette= isset($data->recette) ? $data->recette : $row['recette'];
        $depense= isset($data->depense) ? $data->depense : $row['depense'];

        $description = isset($data->description) ? $data->description : $row['description'];
        $beneficie = isset($data->beneficie) ? $data->beneficie : $row['beneficie'];

        $date = isset($data->date) ? $data->date : $row['date'];

        $jour = isset($data->jour) ? $data->jour : $row['jour'];
       



       $update_query = "UPDATE `recette` SET recette= :recette, depense= :depense, description = :description, beneficie = :beneficie, date= :date,jour= :jour
     
        WHERE id = :id";

        $update_stmt = $conn->prepare($update_query);

        $update_stmt->bindValue(':recette', htmlspecialchars(strip_tags($recette)), PDO::PARAM_STR);
        $update_stmt->bindValue(':depense', htmlspecialchars(strip_tags($depense)), PDO::PARAM_STR);

        $update_stmt->bindValue(':description', htmlspecialchars(strip_tags($description)), PDO::PARAM_STR);
        $update_stmt->bindValue(':beneficie', htmlspecialchars(strip_tags($beneficie)), PDO::PARAM_STR);

         $update_stmt->bindValue(':date', htmlspecialchars(strip_tags($date)), PDO::PARAM_STR);

          $update_stmt->bindValue(':jour', htmlspecialchars(strip_tags($jour)), PDO::PARAM_STR);
         
        $update_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);


        if ($update_stmt->execute()) {

            echo json_encode([
                'success' => 1,
                'message' => 'Record udated successfully'
            ]);
            exit;
        }

        echo json_encode([
            'success' => 0,
            'message' => 'Did not udpate. Something went  wrong.'
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
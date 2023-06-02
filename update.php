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


if (!isset($data->P_id)) {
    echo json_encode(['success' => 0, 'message' => 'Please enter correct Patients id.']);
    exit;
}

try {

    $fetch_post = "SELECT * FROM `patient` WHERE P_id=:P_id";
    $fetch_stmt = $conn->prepare($fetch_post);
    $fetch_stmt->bindValue(':P_id', $data->P_id, PDO::PARAM_INT);
    $fetch_stmt->execute();

    if ($fetch_stmt->rowCount() > 0) :
     //echo 'AAA';
        $row = $fetch_stmt->fetch(PDO::FETCH_ASSOC);
        $P_nom= isset($data->P_nom) ? $data->P_nom : $row['P_nom'];
        $P_prenom = isset($data->P_prenom) ? $data->P_prenom : $row['P_prenom'];
        $P_tel = isset($data->P_tel) ? $data->P_tel : $row['P_tel'];

        $P_email = isset($data->P_email) ? $data->P_email : $row['P_email'];

        $P_region = isset($data->P_region) ? $data->P_region : $row['P_region'];
        $P_c = isset($data->P_c) ? $data->P_c : $row['P_c'];

     

        $P_sexe = isset($data->P_sexe) ? $data->P_sexe : $row['P_sexe'];
        $P_nbs = isset($data->P_nbs) ? $data->P_nbs : $row['P_nbs'];

        $P_pt = isset($data->P_pt) ? $data->P_pt : $row['P_pt'];



       $update_query = "UPDATE `patient` SET P_nom = :P_nom, P_prenom = :P_prenom, P_tel = :P_tel, P_email = :P_email,
       P_region = :P_region, P_c = :P_c, P_sexe= :P_sexe,
       P_nbs= :P_nbs,P_pt= :P_pt
        WHERE P_id = :P_id";

        $update_stmt = $conn->prepare($update_query);

        $update_stmt->bindValue(':P_nom', htmlspecialchars(strip_tags($P_nom)), PDO::PARAM_STR);
        $update_stmt->bindValue(':P_prenom', htmlspecialchars(strip_tags($P_prenom)), PDO::PARAM_STR);
        $update_stmt->bindValue(':P_tel', htmlspecialchars(strip_tags($P_tel)), PDO::PARAM_STR);

         $update_stmt->bindValue(':P_email', htmlspecialchars(strip_tags($P_email)), PDO::PARAM_STR);

          $update_stmt->bindValue(':P_region', htmlspecialchars(strip_tags($P_region)), PDO::PARAM_STR);
          $update_stmt->bindValue(':P_c', htmlspecialchars(strip_tags($P_c)), PDO::PARAM_STR);


        $update_stmt->bindValue(':P_sexe', htmlspecialchars(strip_tags($P_sexe)), PDO::PARAM_STR);
        $update_stmt->bindValue(':P_nbs', htmlspecialchars(strip_tags($P_nbs)), PDO::PARAM_STR);
        $update_stmt->bindValue(':P_pt', htmlspecialchars(strip_tags($P_pt)), PDO::PARAM_STR);

        $update_stmt->bindValue(':P_id', $data->P_id, PDO::PARAM_INT);


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
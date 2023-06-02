

<?php
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer
require_once 'C:\Users\Louhichii\Documents\hyperbare\PHPMailer-master\src\PHPMailer.php';
require_once 'C:\Users\Louhichii\Documents\hyperbare\PHPMailer-master\src\SMTP.php';
require_once 'C:\Users\Louhichii\Documents\hyperbare\PHPMailer-master\src\Exception.php';

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
        $action = isset($data->action) ? $data->action : $row['action'];

        $update_query = "UPDATE `med` SET action = :action WHERE id = :id";

        $update_stmt = $conn->prepare($update_query);

        $update_stmt->bindValue(':action', htmlspecialchars(strip_tags($action)), PDO::PARAM_STR);

        $update_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            // Send email notification if action is updated to "accept"
            if ($action == "Accepter" && !empty(trim($row['email']))) {
                $mail = new PHPMailer(true);

                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
                $mail->Port = 465; // Replace with your SMTP port
                $mail->SMTPAuth = true;
                $mail->Username = 'oxyboreal2@gmail.com'; // Replace with your SMTP username
                $mail->Password = 'iacumladindrlzjp'; // Replace with your SMTP password
                $mail->SMTPSecure = 'ssl';
                $mail->setFrom('oxyboreal2@gmail.com', 'BorelForest'); // Replace with your from email and name
                $mail->addAddress($row['email']); // Add recipient email address

                $mail->Subject = 'Acceptation de rendez-vous';
            
                $mail->Body = 'Cher ' . $row['username'] . ", votre rendez-vous a été confirmé dans notre centre de l'oxygénothérapie hyperbare le " . $row['date'] . ", à l'heure " . $row['heure'] .  ", Nous vous contacterons pour plus d’informations .";
        
                
                $mail->send();
            }
            if ($action == "Refuser" && !empty(trim($row['email']))) {
                $mail = new PHPMailer(true);

                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
                $mail->Port = 465; // Replace with your SMTP port
                $mail->SMTPAuth = true;
                $mail->Username = 'oxyboreal2@gmail.com'; // Replace with your SMTP username
                $mail->Password = 'iacumladindrlzjp'; // Replace with your SMTP password
                $mail->SMTPSecure = 'ssl';
                $mail->setFrom('oxyboreal2@gmail.com', 'BorelForest'); // Replace with your from email and name
                $mail->addAddress($row['email']); // Add recipient email address

                $mail->Subject = 'Refuser de rendez-vous';
            
                $mail->Body = 'Cher ' . $row['username'] . ", votre rendez-vous a été Refuser dans notre centre de l'oxygénothérapie hyperbare le " . $row['date'] . ", à l'heure " . $row['heure'] . ", Nous vous contacterons pour plus d’informations .";
        
                
                $mail->send();
            }
    
            http_response_code(200);
            echo json_encode([
                'success' => 1,
                'message' => 'Action updated successfully!',
            ]);
            exit;
        } else {
            http_response_code(503);
            echo json_encode([
                'success' => 0,
                'message' => 'Unable to update action',
            ]);
            exit;
        }
    
    else :
        http_response_code(404);
        echo json_encode([
            'success' => 0,
            'message' => 'No patient found!',
        ]);
        exit;
    endif;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
    'success' => 0,
    'message' => 'Unable to update action' . $e->getMessage(),
    ]);
    exit;
    }
    ?>
    

<?php
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
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

try {
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

    // if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //     throw new Exception('Bad Request!. Only POST method is allowed');
    // }

    require 'db_connect.php';
    $database = new Operations();
    $conn = $database->dbConnection();

    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->nom) || !isset($data->email) || !isset($data->tel) || !isset($data->msg)) {
        echo json_encode([
            'success' => 0,
            'message' => 'Please enter compulsory fields',
        ]);
        exit;
    } elseif (empty(trim($data->nom)) || empty(trim($data->email)) || empty(trim($data->tel)) || empty(trim($data->msg))) {
        echo json_encode([
            'success' => 0,
            'message' => 'Field cannot be empty. Please fill all the fields.',
        ]);
        exit;
    }

    $nom = htmlspecialchars(trim($data->nom));
    $email = htmlspecialchars(trim($data->email));
    $tel = htmlspecialchars(trim($data->tel));
    $msg = htmlspecialchars(trim($data->msg));

    $query = "INSERT INTO `users` (
        `nom`,
        `email`,
        `tel`,
        `msg`
    ) VALUES (
        :nom,
        :email,
        :tel,
        :msg
    )";

    $stmt = $conn->prepare($query);

    $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':tel', $tel, PDO::PARAM_STR);
    $stmt->bindValue(':msg', $msg, PDO::PARAM_STR);

    if ($stmt->execute()) {


        $nom = $data->nom;
        $email = $data->email;
        $tel = $data->tel;
        $msg = $data->msg;
       
    
       // Set email body
// Set email body
$mail->Body = 
    "nom: " . $nom . "<br>" .
    "email: " . $email . "<br>" .
    "tel: " . $tel . "<br>" .
    "msg: " . $msg;

try {
    // Send the email
    $mail->addAddress('oxyboreal2@gmail.com'); // Add recipient email address (adheya  bech yb3th email lel patient just nabdlo addadresse )
    $mail->isHTML(true);
    $mail->Subject = 'Contactez-nous'; // Replace with your email subject
    $mail->send();
    echo 'Email sent successfully.';
} catch (Exception $e) {
    echo 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
}


       
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
}

catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage(),
    ]);
    exit;
}

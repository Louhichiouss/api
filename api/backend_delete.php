<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization");


require 'db_connect.php';
$database = new Operations();
$conn = $database->dbConnection();

class Result {}

$json = file_get_contents('php://input');
$params = json_decode($json);

$stmt = $conn->prepare("DELETE FROM events WHERE id = :id");
$stmt->bindParam(':id', $params->id);
$stmt->execute();

$response = new Result();
$response->result = 'OK';
$response->message = 'Delete successful';

header('Content-Type: application/json');
echo json_encode($response);

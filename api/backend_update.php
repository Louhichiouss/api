<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization");

require 'db_connect.php';
$database = new Operations();
$conn = $database->dbConnection();

class Result
{
}

$json = file_get_contents('php://input');
$params = json_decode($json);

$stmt = $conn->prepare("UPDATE events SET text = :text WHERE id = :id");
$stmt->bindParam(':id', $params->id);
$stmt->bindParam(':text', $params->name);
$stmt->execute();

$response = new Result();
$response->result = 'OK';
$response->message = 'Update successful';

header('Content-Type: application/json');
echo json_encode($response);

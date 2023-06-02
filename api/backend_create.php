<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization");

date_default_timezone_set('Africa/Tunis');

require 'db_connect.php';
$database = new Operations();
$conn = $database->dbConnection();

$json = file_get_contents('php://input');
$params = json_decode($json);

if ($params != null) {
  $stmt = $conn->prepare("INSERT INTO events (text, start, end) VALUES (:text, :start, :end)");
  $stmt->bindParam(':start', $params->start);
  $stmt->bindParam(':end', $params->end);
  $stmt->bindParam(':text', $params->text);
  $stmt->execute();

  class Result {}

  $response = new Result();
  $response->result = 'OK';
  $response->message = 'Created with id: ' . $conn->lastInsertId();
  $response->id = $conn->lastInsertId();
  $start = new DateTime($params->start, new DateTimeZone('UTC'));
  $end = new DateTime($params->end, new DateTimeZone('UTC'));
  $response->start = $start->setTimezone(new DateTimeZone('Africa/Tunis'))->format('Y-m-d H:i:s');
  $response->end = $end->setTimezone(new DateTimeZone('Africa/Tunis'))->format('Y-m-d H:i:s');

  header('Content-Type: application/json');
  echo json_encode($response);
} else {
  // handle null case
  // maybe return an error response
}

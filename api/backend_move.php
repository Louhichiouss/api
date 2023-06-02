<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization");

require 'db_connect.php';
$database = new Operations();
$conn = $database->dbConnection();

// set the timezone to Africa/Tunis
date_default_timezone_set('Africa/Tunis');

class Event {}


$json = file_get_contents('php://input');
$params = json_decode($json);

$stmt = $conn->prepare("UPDATE events SET start = :start, end = :end WHERE id = :id");
$stmt->bindParam(':id', $params->id);
$start = new DateTime($params->newStart, new DateTimeZone('UTC'));
$end = new DateTime($params->newEnd, new DateTimeZone('UTC'));
$stmt->bindParam(':start', $start->format('Y-m-d H:i:s'));
$stmt->bindParam(':end', $end->format('Y-m-d H:i:s'));
$stmt->execute();

$stmt = $conn->prepare("SELECT * FROM events WHERE id = :id");
$stmt->bindParam(':id', $params->id);
$stmt->execute();
$result = $stmt->fetch();

$updatedEvent = new Event();
$updatedEvent->id = $result['id'];
$updatedEvent->text = $result['text'];

$start = new DateTime($result['start'], new DateTimeZone('UTC'));
$start->setTimezone(new DateTimeZone('Africa/Tunis'));
$updatedEvent->start = $start->format('Y-m-d H:i:s');

$end = new DateTime($result['end'], new DateTimeZone('UTC'));
$end->setTimezone(new DateTimeZone('Africa/Tunis'));
$updatedEvent->end = $end->format('Y-m-d H:i:s');

$response = new stdClass();
$response->result = 'OK';
$response->message = 'Update successful';
$response->updatedEvent = $updatedEvent;

header('Content-Type: application/json');
echo json_encode($response);
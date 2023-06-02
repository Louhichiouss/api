<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization");

require 'db_connect.php';
$database = new Operations();
$conn = $database->dbConnection();

// retrieve start and end dates from request body
$json = file_get_contents('php://input');
$params = json_decode($json);

// set the timezone to Africa/Tunis
date_default_timezone_set('Africa/Tunis');

$stmt = $conn->prepare("SELECT * FROM events WHERE NOT ((end <= :start) OR (start >= :end))");
$stmt->bindParam(':start', $params->start);
$stmt->bindParam(':end', $params->end);
$stmt->execute();
$result = $stmt->fetchAll();

class Event {}

$events = array();

foreach ($result as $row) {
  $start = new DateTime($row['start'], new DateTimeZone('Africa/Tunis'));
  $start->setTimezone(new DateTimeZone('UTC'));
  $utcStart = $start->format('Y-m-d H:i:s');

  $end = new DateTime($row['end'], new DateTimeZone('Africa/Tunis'));
  $end->setTimezone(new DateTimeZone('UTC'));
  $utcEnd = $end->format('Y-m-d H:i:s');

  $e = new Event();
  $e->id = $row['id'];
  $e->text = $row['text'];
  $e->start = $utcStart;
  $e->end = $utcEnd;
  $events[] = $e;
}

header('Content-Type: application/json');

foreach ($events as $event) {
  $start = new DateTime($event->start, new DateTimeZone('UTC'));
  $start->setTimezone(new DateTimeZone('Africa/Tunis'));
  $event->start = $start->format('Y-m-d H:i:s');

  $end = new DateTime($event->end, new DateTimeZone('UTC'));
  $end->setTimezone(new DateTimeZone('Africa/Tunis'));
  $event->end = $end->format('Y-m-d H:i:s');
}

echo json_encode($events);

<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "livestock_db";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed"]));
}

// Get tagId from URL
$tagId = $_GET['tagId'] ?? '';

if ($tagId == '') {
    echo json_encode(["error" => "No tagId provided"]);
    exit;
}

// Query animal
$sql = "SELECT * FROM animals WHERE tagId=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $tagId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $animal = $result->fetch_assoc();

    // Get latest health record
    $sqlRecord = "SELECT * FROM healthRecords WHERE tagId=? ORDER BY id DESC LIMIT 1";
    $stmtRecord = $conn->prepare($sqlRecord);
    $stmtRecord->bind_param("s", $tagId);
    $stmtRecord->execute();
    $recordResult = $stmtRecord->get_result();
    $healthRecord = $recordResult->fetch_assoc();

    $response = [
        "name" => $animal['name'],
        "age" => date_diff(date_create($animal['birthdate']), date_create('today'))->y,
        "isPregnant" => (bool)$animal['isPregnant'],
        "isSick" => (bool)$animal['isSick'],
        "ownerContact" => $animal['ownerContact'],
        "latestHealthRecord" => $healthRecord
    ];
    echo json_encode($response);
} else {
    echo json_encode(["error" => "Tag not found"]);
}

$conn->close();
?>
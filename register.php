<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "livestock_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed"]));
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

if($method == 'POST'){
    $tagId = $data['tagId'] ?? '';
    $name = $data['name'] ?? '';
    $animalType = $data['animalType'] ?? '';
    $sex = $data['sex'] ?? '';
    $breed = $data['breed'] ?? '';
    $birthdate = $data['birthdate'] ?? '';
    $ownerContact = $data['ownerContact'] ?? '';

    // Check if animal exists
    $stmtCheck = $conn->prepare("SELECT tagId FROM animals WHERE tagId=?");
    $stmtCheck->bind_param("s", $tagId);
    $stmtCheck->execute();
    $exists = $stmtCheck->get_result()->num_rows > 0;

    if($exists){
        // Update existing
        $stmt = $conn->prepare("UPDATE animals SET name=?, animalType=?, sex=?, breed=?, birthdate=?, ownerContact=? WHERE tagId=?");
        $stmt->bind_param("sssssss",$name,$animalType,$sex,$breed,$birthdate,$ownerContact,$tagId);
        $stmt->execute();
        echo json_encode(["status"=>"updated"]);
    } else {
        // Insert new
        $stmt = $conn->prepare("INSERT INTO animals (tagId,name,animalType,sex,breed,birthdate,ownerContact) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssss",$tagId,$name,$animalType,$sex,$breed,$birthdate,$ownerContact);
        $stmt->execute();
        echo json_encode(["status"=>"created","tagId"=>$tagId]);
    }
} elseif($method == 'DELETE'){
    $tagId = $data['tagId'] ?? '';
    if($tagId==''){ echo json_encode(["error"=>"No tagId"]); exit;}
    $stmt = $conn->prepare("DELETE FROM animals WHERE tagId=?");
    $stmt->bind_param("s",$tagId);
    $stmt->execute();
    echo json_encode(["status"=>"deleted"]);
}
$conn->close();
?>
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$conn = new mysqli('localhost', 'root', '', 'vehicle_management');

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT c.*, v.Make, v.Model FROM Compliance c JOIN Vehicle v ON c.VehicleID = v.VehicleID";
    $result = $conn->query($sql);
    $compliance = [];
    while ($row = $result->fetch_assoc()) {
        $compliance[] = $row;
    }
    echo json_encode($compliance);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['VehicleID'], $data['Type'], $data['DueDate'], $data['Status'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    $vehicleID = (int)$data['VehicleID'];
    $type = $conn->real_escape_string($data['Type']);
    $dueDate = $conn->real_escape_string($data['DueDate']);
    $status = $conn->real_escape_string($data['Status']);
    $sql = "INSERT INTO Compliance (VehicleID, Type, DueDate, Status) VALUES ($vehicleID, '$type', '$dueDate', '$status')";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid ID']);
        exit;
    }
    $sql = "DELETE FROM Compliance WHERE ComplianceID=$id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

$conn->close();
?>
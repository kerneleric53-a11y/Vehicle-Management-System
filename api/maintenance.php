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
    $sql = "SELECT m.*, v.Make, v.Model FROM Maintenance m JOIN Vehicle v ON m.VehicleID = v.VehicleID";
    $result = $conn->query($sql);
    $maintenance = [];
    while ($row = $result->fetch_assoc()) {
        $maintenance[] = $row;
    }
    echo json_encode($maintenance);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['VehicleID'], $data['ServiceDate'], $data['Cost'], $data['ServiceType'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    $vehicleID = (int)$data['VehicleID'];
    $serviceDate = $conn->real_escape_string($data['ServiceDate']);
    $cost = (float)$data['Cost'];
    $serviceType = $conn->real_escape_string($data['ServiceType']);
    $sql = "INSERT INTO Maintenance (VehicleID, ServiceDate, Cost, ServiceType) VALUES ($vehicleID, '$serviceDate', $cost, '$serviceType')";
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
    $sql = "DELETE FROM Maintenance WHERE MaintenanceID=$id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

$conn->close();
?>
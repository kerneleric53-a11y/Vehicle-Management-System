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
    $sql = "SELECT f.*, v.Make, v.Model FROM FuelLog f JOIN Vehicle v ON f.VehicleID = v.VehicleID";
    $result = $conn->query($sql);
    $fuelLogs = [];
    while ($row = $result->fetch_assoc()) {
        $fuelLogs[] = $row;
    }
    echo json_encode($fuelLogs);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['VehicleID'], $data['Date'], $data['Liters'], $data['CostPerLiter'], $data['Odometer'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    $vehicleID = (int)$data['VehicleID'];
    $date = $conn->real_escape_string($data['Date']);
    $liters = (float)$data['Liters'];
    $costPerLiter = (float)$data['CostPerLiter'];
    $odometer = (int)$data['Odometer'];
    $sql = "INSERT INTO FuelLog (VehicleID, Date, Liters, CostPerLiter, Odometer) VALUES ($vehicleID, '$date', $liters, $costPerLiter, $odometer)";
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
    $sql = "DELETE FROM FuelLog WHERE FuelLogID=$id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

$conn->close();
?>
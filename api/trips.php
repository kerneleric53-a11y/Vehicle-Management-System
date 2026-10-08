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
    $sql = "SELECT t.*, v.Make, v.Model, d.Name AS DriverName FROM Trip t JOIN Vehicle v ON t.VehicleID = v.VehicleID JOIN Driver d ON t.DriverID = d.DriverID";
    $result = $conn->query($sql);
    $trips = [];
    while ($row = $result->fetch_assoc()) {
        $trips[] = $row;
    }
    echo json_encode($trips);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['VehicleID'], $data['DriverID'], $data['StartDate'], $data['EndDate'], $data['Distance'], $data['StartLocation'], $data['EndLocation'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    $vehicleID = (int)$data['VehicleID'];
    $driverID = (int)$data['DriverID'];
    $startDate = $conn->real_escape_string($data['StartDate']);
    $endDate = $conn->real_escape_string($data['EndDate']);
    $distance = (float)$data['Distance'];
    $startLocation = $conn->real_escape_string($data['StartLocation']);
    $endLocation = $conn->real_escape_string($data['EndLocation']);
    $sql = "INSERT INTO Trip (VehicleID, DriverID, StartDate, EndDate, Distance, StartLocation, EndLocation) 
            VALUES ($vehicleID, $driverID, '$startDate', '$endDate', $distance, '$startLocation', '$endLocation')";
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
    $sql = "DELETE FROM Trip WHERE TripID=$id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

$conn->close();
?>
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
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $sql = "SELECT * FROM Vehicle WHERE Make LIKE '%$search%' OR Model LIKE '%$search%' OR Year LIKE '%$search%'";
    $result = $conn->query($sql);
    $vehicles = [];
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
    echo json_encode($vehicles);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['make'], $data['model'], $data['year'], $data['vin'], $data['regNo'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    $make = $conn->real_escape_string($data['make']);
    $model = $conn->real_escape_string($data['model']);
    $variant = $conn->real_escape_string($data['variant'] ?? '');
    $year = (int)$data['year'];
    $vin = $conn->real_escape_string($data['vin']);
    $regNo = $conn->real_escape_string($data['regNo']);
    $sql = "INSERT INTO Vehicle (Make, Model, Variant, Year, VIN, RegistrationNo) VALUES ('$make', '$model', '$variant', $year, '$vin', '$regNo')";
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
    $sql = "DELETE FROM Vehicle WHERE VehicleID=$id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

$conn->close();
?>
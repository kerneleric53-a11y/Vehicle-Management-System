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
    $sql = "SELECT r.*, v.Make, v.Model FROM Reservation r JOIN Vehicle v ON r.VehicleID = v.VehicleID";
    $result = $conn->query($sql);
    $reservations = [];
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
    echo json_encode($reservations);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['VehicleID'], $data['StartDate'], $data['EndDate'], $data['Purpose'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    $vehicleID = (int)$data['VehicleID'];
    $startDate = $conn->real_escape_string($data['StartDate']);
    $endDate = $conn->real_escape_string($data['EndDate']);
    $purpose = $conn->real_escape_string($data['Purpose']);
    // Check for double-booking
    $sql = "SELECT COUNT(*) AS count FROM Reservation WHERE VehicleID = $vehicleID 
            AND (('$startDate' BETWEEN StartDate AND EndDate) OR ('$endDate' BETWEEN StartDate AND EndDate))";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Vehicle is already reserved for this time']);
        exit;
    }
    $sql = "INSERT INTO Reservation (VehicleID, StartDate, EndDate, Purpose) VALUES ($vehicleID, '$startDate', '$endDate', '$purpose')";
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
    $sql = "DELETE FROM Reservation WHERE ReservationID=$id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

$conn->close();
?>
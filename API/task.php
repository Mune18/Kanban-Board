<?php
// Set the default timezone
date_default_timezone_set("Asia/Manila");

// Set the maximum execution time for requests
set_time_limit(1000);

// Set the content type to JSON
header('Content-Type: application/json');

// Allow access from http://localhost:4200 for all requests
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Origin: http://localhost:50364");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle CORS preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

require_once 'database.php';

$connection = (new Connection())->connect();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($connection);
        break;
    case 'POST':
        handlePost($connection);
        break;
    case 'PUT':
        handlePut($connection);
        break;
    case 'DELETE':
        handleDelete($connection);
        break;
}

function handleGet($connection) {
    $sql = 'SELECT * FROM tasks';
    $stmt = $connection->query($sql);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the date to ISO 8601 for frontend parsing
    foreach ($tasks as &$task) {
        $task['due_date'] = date('c', strtotime($task['due_date'])); // Convert to ISO 8601
    }

    echo json_encode($tasks);
}

function handlePost($connection) {
    $data = json_decode(file_get_contents('php://input'), true);
    $dueDate = date('Y-m-d H:i:s', strtotime($data['dueDate'])); // Convert to MySQL DATETIME format

    $sql = 'INSERT INTO tasks (title, description, due_date, status, priority) VALUES (:title, :description, :due_date, :status, :priority)';
    $stmt = $connection->prepare($sql);
    $stmt->execute([
        ':title' => $data['title'],
        ':description' => $data['description'],
        ':due_date' => $dueDate,  // Use the converted date
        ':status' => $data['status'],
        ':priority' => $data['priority']
    ]);
    echo json_encode(['status' => 'Task added']);
}

function handlePut($connection) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['status']) && isset($data['id'])) {
        // Update only the status
        $sql = 'UPDATE tasks SET status = :status WHERE id = :id';
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id' => $data['id'],
            ':status' => $data['status']
        ]);
        echo json_encode(['status' => 'Task status updated']);
    } else {
        // Update all details if no status is provided
        $sql = 'UPDATE tasks SET title = :title, description = :description, due_date = :due_date, status = :status, priority = :priority WHERE id = :id';
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id' => $data['id'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':due_date' => $data['dueDate'],
            ':status' => $data['status'],
            ':priority' => $data['priority']
        ]);
        echo json_encode(['status' => 'Task updated']);
    }
}

function handleDelete($connection) {
    $id = $_GET['id'];
    $sql = 'DELETE FROM tasks WHERE id = :id';
    $stmt = $connection->prepare($sql);
    $stmt->execute([':id' => $id]);
    echo json_encode(['status' => 'Task deleted']);
}
?>

<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "intern_users";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["error" => "db connection failed"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_POST['token']) || empty(trim($_POST['token']))) {
        echo json_encode(["error" => "missing token"]);
        exit;
    }

    $token = trim($_POST['token']);

    try {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
    } catch (Exception $e) {
        echo json_encode(["error" => "redis failed"]);
        exit;
    }

    $user_id = $redis->get($token);

    if (!$user_id) {
        echo json_encode(["error" => "invalid or expired token"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        echo json_encode(["error" => "user not found"]);
    }

    $stmt->close();

} else {
    echo json_encode(["error" => "invalid request"]);
}

$conn->close();
?>
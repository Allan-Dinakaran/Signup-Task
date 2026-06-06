<?php
require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

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

    try {
        $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
        $collection  = $mongoClient->intern_users->profiles;

        $profile = $collection->findOne(['mysql_id' => (int)$user_id]);

        if ($profile) {
            echo json_encode([
                'name'       => $profile['name'],
                'email'      => $profile['email'],
                'age'        => $profile['age'] ?? '',
                'dob'        => $profile['dob'] ?? '',
                'contact'    => $profile['contact'] ?? '',
                'created_at' => (string)$profile['created_at']
            ]);
        } else {
            echo json_encode(["error" => "profile not found"]);
        }

    } catch (Exception $e) {
        echo json_encode(["error" => "mongodb failed - " . $e->getMessage()]);
    }

} else {
    echo json_encode(["error" => "invalid request"]);
}
?>

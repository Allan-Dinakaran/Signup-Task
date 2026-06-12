<?php
require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_POST['token']) || empty(trim($_POST['token']))) {
        echo json_encode(["error" => "missing token"]);
        exit;
    }

    $token = trim($_POST['token']);

    $redis_host = $_ENV['REDIS_HOST'] ?? '127.0.0.1';
    $redis_port = $_ENV['REDIS_PORT'] ?? 6379;
    $redis_pass = $_ENV['REDIS_PASS'] ?? null;

    try {
        $redis = new Redis();
        $redis->connect($redis_host, (int)$redis_port);

        if ($redis_pass) {
            $redis->auth($redis_pass);
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "redis failed"]);
        exit;
    }

    $user_id = $redis->get($token);

    if (!$user_id) {
        echo json_encode(["error" => "invalid or expired token"]);
        exit;
    }

    $age     = trim($_POST['age'] ?? '');
    $dob     = trim($_POST['dob'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    try {
        $mongo_uri = $_ENV['MONGO_URI'] ?? "mongodb://localhost:27017";

        $mongoClient = new MongoDB\Client($mongo_uri);
        $collection  = $mongoClient->intern_users->profiles;

        $collection->updateOne(
            ['mysql_id' => (int)$user_id],
            ['$set' => [
                'age'     => $age,
                'dob'     => $dob,
                'contact' => $contact
            ]]
        );

        echo json_encode(["success" => "profile updated"]);

    } catch (Exception $e) {
        echo json_encode(["error" => "mongodb failed - " . $e->getMessage()]);
    }

} else {
    echo json_encode(["error" => "invalid request"]);
}
?>

<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "intern_users";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['email'], $_POST['password'])) {

        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $token = bin2hex(random_bytes(32));

                $redis = new Redis();
                $redis->connect('127.0.0.1', 6379);

                $redis->set($token, $user['id']);
                $redis->expire($token, 3600);

                echo $token;

            } else {
                echo "error";
            }

        } else {
            echo "error";
        }

        $stmt->close();
        $conn->close();
    }
}

?>
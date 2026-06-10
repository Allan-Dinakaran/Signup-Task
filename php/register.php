<?php
require_once __DIR__ . '/../vendor/autoload.php';

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'allan1234';
$db   = getenv('DB_NAME') ?: 'intern_users';

// Your connection line looks like this on line 9:
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['name'], $_POST['email'], $_POST['password'])) {

        $name     = trim($_POST['name']);
        $email    = trim($_POST['email']);
        $password = $_POST['password'];

        if ($name === "" || $email === "" || $password === "") {
            echo "error: empty fields";
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "error: invalid email";
            exit;
        }

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            echo "error: email already exists";
            $check->close();
            $conn->close();
            exit;
        }
        $check->close();

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $mysql_id = $stmt->insert_id;

            try {
                $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
                $collection  = $mongoClient->intern_users->profiles;

                $collection->insertOne([
                    'mysql_id'   => $mysql_id,
                    'name'       => $name,
                    'email'      => $email,
                    'age'        => '',
                    'dob'        => '',
                    'contact'    => '',
                    'created_at' => new MongoDB\BSON\UTCDateTime()
]);

                echo "success";

            } catch (Exception $e) {
                echo "error: mongodb failed - " . $e->getMessage();
            }

        } else {
            echo "error: " . $stmt->error;
        }

        $stmt->close();

    } else {
        echo "error: missing fields";
    }

} else {
    echo "error: invalid request";
}

$conn->close();
?>

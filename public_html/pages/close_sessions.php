<?php
session_start();
require_once "connection.php";

$userID = $_SESSION["userID"];
$sessionID = session_id(); // Unique session identifier

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["action"]) && $data["action"] == "closeTab") {
        $sql = "DELETE FROM user_sessions WHERE userID = ? AND session_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userID, $sessionID]);

        if ($stmt->rowCount() > 0) {
            session_unset();
            session_destroy();
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete session."]);
        }
        exit;
    }
}
?>
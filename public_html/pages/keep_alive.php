<?php
    session_start();
    require_once "connection.php";
    
    $userID = $_SESSION["userID"];
    $sessionID = session_id(); // Unique session identifier
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (isset($data["action"]) && $data["action"] == "keepAlive") {
            $sql = "UPDATE user_sessions SET last_active = NOW() WHERE userID = ? AND session_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID, $sessionID]);
            exit;
        }
    }
?>
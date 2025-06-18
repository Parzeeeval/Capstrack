<?php

session_start();
require_once "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if the action is "browserClose"
    if (isset($data["action"]) && $data["action"] == "browserClose") {

        if (isset($_SESSION["userID"])) {
            $userID = $_SESSION["userID"];

            try {
                $conn->beginTransaction();

                $sql = "UPDATE users SET session = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute(["offline", $userID]);

                if ($result) {
                    $conn->commit();

        


                    // Respond with success
                    echo json_encode(["status" => "success"]);
                } else {
                    throw new Exception("Error on changing user session to offline mode");
                }
            } catch (Exception $e) {
                $conn->rollBack();

                // Log the error (server-side logging or database logging is preferred)
                error_log($e->getMessage());

                // Respond with error
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            // No userID found in the session
            echo json_encode(["status" => "error", "message" => "No active session found."]);
        }

        exit; // Stop further processing
    }
}

?>
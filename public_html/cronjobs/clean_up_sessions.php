<?php
$host = 'localhost';
$dbname = 'u354989168_capstrack_db1';
$dbusername = 'u354989168_admin01'; 
$dbpassword = '@Capstrack2024';

$conn = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Delete sessions that haven't been active for 5 minutes
$sql = "DELETE FROM user_sessions WHERE last_active < NOW() - INTERVAL 5 MINUTE";
$stmt = $conn->prepare($sql);
$stmt->execute();

// Optionally update user status based on remaining active sessions
$updateStatus = "UPDATE users u
                 SET u.session = 'offline'
                 WHERE NOT EXISTS (
                     SELECT 1 FROM user_sessions us
                     WHERE us.userID = u.id
                 )";
$updateStmt = $conn->prepare($updateStatus);
$updateStmt->execute();

session_start();

session_unset();
session_destroy();
?>
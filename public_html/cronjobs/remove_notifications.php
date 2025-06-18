<?php
    $host = 'localhost';
    $dbname = 'u354989168_capstrack_db1';
    $dbusername = 'u354989168_admin01'; 
    $dbpassword = '@Capstrack2024';

    try {
        // Connect to the database
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // SQL to delete notifications older than 7 days
        $sql = "DELETE FROM notifications WHERE `date` < NOW() - INTERVAL 7 DAY";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    
        // Output number of rows affected
        echo $stmt->rowCount() . " notifications deleted.";
    } 
    
    catch (PDOException $e) {
        // Handle errors
        echo "Error: " . $e->getMessage();
    }
?>
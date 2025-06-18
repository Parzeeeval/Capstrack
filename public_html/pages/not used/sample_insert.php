<?php

 // Connect to MySQL LOCALLY
 $host = 'localhost';
 $dbname = 'testlogin';
 $dbusername = 'root'; // Default username for XAMPP
 $dbpassword = ''; // Default password for XAMPP

 try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $curr_year = date("Y");
    $curr_month = date("m");   

    $query = "SELECT * FROM sequence_tracker";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);


    if($result){
        if($curr_year == $result["current_year"]){
            $next_sequence = $result["last_sequence"];

            $next_sequence = $next_sequence + 1;

            echo "$curr_year" . "$next_sequence";
        }

        else if($curr_year != $result["current_year"]){
            $query = "UPDATE sequence_tracker SET current_year = ?, last_sequence = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$curr_year, 1000]);

            echo "year and last sequence updated";
        }
    }

} 

catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
<?php 
    try{
        $host = 'localhost';
        $dbname = 'u354989168_capstrack_db1';
        $dbusername = 'u354989168_admin01'; 
        $dbpassword = '@Capstrack2024';
        
        /*Connect to MySQL LOCALLY
        $host = 'localhost';
        $dbname = 'testlogin';
        $dbusername = 'root'; Default username for XAMPP
        $dbpassword = '';  Default password for XAMPP*/

        $conn = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }
    
    catch(PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit;
    }

?>

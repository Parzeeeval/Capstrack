 <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Remove Panelist</title>
        <link rel="stylesheet" href="pages/group_inner_people.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="pages/session_tracker.js"></script>
    </head>
    
    <body>
         <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    </body>
    
</html>
 
 <?php 
        require "connection.php";
        session_start();
        
        $projectID = $_SESSION["projectIDValue"];
        
        function removePanelist(){
            global $conn, $projectID;
            
            $panelID = $_POST["panelistID"];
            
            try{
                $conn->beginTransaction();
                
                $sql = "DELETE FROM panelists WHERE panelistID = ? AND projectID = ?";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([$panelID, $projectID]);
                
                if($result){
                    $conn->commit();
                    
                    echo '<script>
                            Swal.fire({
                                 title: "Success",
                                text: "Capstone Panelist succesfuly removed with Faculty ID of:'.addslashes($panelID).'",
                                icon: "success",
                                confirmButtonText: "OK"
                            });
                        </script>';
                }
                
            }
            
            catch(Exception $e){
                $conn->rollBack();
                
                echo '<script>
                        Swal.fire({
                             title: "Error",
                            text: "Failed to remove panelist",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    </script>';
            }
        }
        
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(isset($_POST["remove-panelist-btn"])){
               removePanelist();
            }
        }
?>

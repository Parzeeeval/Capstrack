<?php
    session_start();
    require 'connection.php';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["cardValue"])){
            $courseID = $_POST["cardValue"];
            $_SESSION["courseID"] = $courseID;
        }
    }
    
    else{
        if(isset($_SESSION["courseID"])){
            $courseID = $_SESSION["courseID"];
        }
        
        else{
            header("Location: /courses");
            exit();
        }
    }
    
    function showSpecializations(){
        
        global $conn, $backgroundColor, $courseID;
    
        $userID = $_SESSION["userID"];
        
        $sql = "SELECT COUNT(*) as count FROM specializations WHERE courseID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$courseID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $count = $result['count']; // This will hold the number of rows
            
        if($count >= 1){    
            $sql = "SELECT accessLevel from faculty WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            $accessLevel = $result["accessLevel"];
            
            if($accessLevel >= 3){
                $sql = "SELECT * FROM specializations WHERE courseID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$courseID]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
               echo '
                    <script>
    
                        var button = document.createElement("button");
                        button.innerHTML = "+"; 
                        button.className = "create-btn-design";
                        button.onclick = function() {
                            window.location.href = "create_specializations"; 
                        };
                    
    
                        document.getElementById("createDiv").appendChild(button);
                    </script>
                ';
            }
    
    
            else if($accessLevel == 2){
                $sql = "SELECT adminID FROM courses WHERE courseID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$courseID]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($result){
                    $adminID = $result["adminID"];
                    
                    if($userID == $adminID){
                        $sql = "SELECT * FROM specializations WHERE courseID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$courseID]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        
                        //$sql = "SELECT * FROM specializations s JOIN sections sc ON sc.specialization = s.name WHERE sc.coordinatorID = ? AND sc.courseID = ?";
                
                        $sql = "SELECT sp.*
                            FROM capstone_projects cp
                            JOIN sections s ON cp.sectionID = s.sectionID
                            JOIN advisers ad ON cp.projectID = ad.projectID
                            JOIN specializations sp ON sp.name = s.specialization
                            WHERE ad.adviserID = ? AND s.courseID = ?";
                            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID]);
                        $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        
                        $result = array_merge($result, $secondResult);  // Combine both results
                        
                        
                        $sql = "SELECT sp.*
                            FROM capstone_projects cp
                            JOIN sections s ON cp.sectionID = s.sectionID
                            JOIN panelists ps ON cp.projectID = ps.projectID
                            JOIN specializations sp ON sp.name = s.specialization
                            WHERE ps.panelistID = ? AND s.courseID = ?";
                            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID]);
                        $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        
                        $result = array_merge($result, $thirdResult);  // Combine both results
                
                    
                       echo '
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    var button = document.createElement("button");
                                    button.innerHTML = "+"; 
                                    button.className = "create-btn-design";
                                    button.onclick = function() {
                                        window.location.href = "create_specializations"; 
                                    };
                        
                                    var createDiv = document.getElementById("createDiv");
                                    if (createDiv) {
                                        createDiv.appendChild(button);
                                    }
                                });
                            </script>
                        ';
                    }
                    
                    else{
                        $sql = "SELECT * FROM specializations s JOIN sections sc ON sc.specialization = s.name WHERE sc.coordinatorID = ? AND sc.courseID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        
                        //$sql = "SELECT * FROM specializations s JOIN sections sc ON sc.specialization = s.name WHERE sc.coordinatorID = ? AND sc.courseID = ?";
                
                        $sql = "SELECT sp.*
                            FROM capstone_projects cp
                            JOIN sections s ON cp.sectionID = s.sectionID
                            JOIN advisers ad ON cp.projectID = ad.projectID
                            JOIN specializations sp ON sp.name = s.specialization
                            WHERE ad.adviserID = ? AND s.courseID = ?";
                            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID]);
                        $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        
                        $result = array_merge($result, $secondResult);  // Combine both results
                        
                        
                        $sql = "SELECT sp.*
                            FROM capstone_projects cp
                            JOIN sections s ON cp.sectionID = s.sectionID
                            JOIN panelists ps ON cp.projectID = ps.projectID
                            JOIN specializations sp ON sp.name = s.specialization
                            WHERE ps.panelistID = ? AND s.courseID = ?";
                            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID]);
                        $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        
                        $result = array_merge($result, $thirdResult);  // Combine both results
                    }
                    
                }
            }
    
            else if($accessLevel <= 1){
                
                $sql = "SELECT * FROM specializations s JOIN sections sc ON sc.specialization = s.name WHERE sc.coordinatorID = ? AND sc.courseID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$userID, $courseID]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                
                
                //$sql = "SELECT * FROM specializations s JOIN sections sc ON sc.specialization = s.name WHERE sc.coordinatorID = ? AND sc.courseID = ?";
                
                $sql = "SELECT sp.*
                    FROM capstone_projects cp
                    JOIN sections s ON cp.sectionID = s.sectionID
                    JOIN advisers ad ON cp.projectID = ad.projectID
                    JOIN specializations sp ON sp.name = s.specialization
                    WHERE ad.adviserID = ? AND s.courseID = ?";
                    
                $stmt = $conn->prepare($sql);
                $stmt->execute([$userID, $courseID]);
                $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                
                $result = array_merge($result, $secondResult);  // Combine both results
                
                
                $sql = "SELECT sp.*
                    FROM capstone_projects cp
                    JOIN sections s ON cp.sectionID = s.sectionID
                    JOIN panelists ps ON cp.projectID = ps.projectID
                    JOIN specializations sp ON sp.name = s.specialization
                    WHERE ps.panelistID = ? AND s.courseID = ?";
                    
                $stmt = $conn->prepare($sql);
                $stmt->execute([$userID, $courseID]);
                $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                
                $result = array_merge($result, $thirdResult);  // Combine both results
                
    
            
                if(count($result) <= 0){
                    header("Location: /sections");
                    exit();
                }
                
            }
    
    
            if (count($result) >= 1) {
                
                //This array below is for removing duplicate entries in cards
                $specialNames = [];
                
                foreach ($result as $row) {
                    $specializationName = $row["name"];
                    
                    if(in_array($specializationName, $specialNames)){
                        continue;
                    }
                    
                    $specialNames[] = $specializationName;
                    

                    echo '
                    <form action="/sections"  method="POST">
                        <div class="card-container" onclick="this.closest(\'form\').submit();">
                            <input type="hidden" name="specializedValue" value="' . htmlspecialchars($specializationName, ENT_QUOTES) . '">
                            <div class="card" style="background-color:#A65A83;">
                                
                                <div class="folder-bottom4">
                                    <div class="circle4"></div>
                                </div>
                                <div class="course-content">
                                    <h2>' . htmlspecialchars($specializationName, ENT_QUOTES) . '</h2>
                                    <p>' . htmlspecialchars($courseID, ENT_QUOTES) . '</p>
                                </div>
                            </div>
                        </div>
                    </form>';
                }
                
                $_SESSION["NoSpecialization"] = false;
            }
            
             else{
                echo '
                    <script>
                        window.location.href = "/sections";
                    </script>';
                    
                $_SESSION["NoSpecialization"] = true;
            }
        }
        
        else if($count <= 0){
            $_SESSION["NoSpecialization"] = true;
            
            header("Location: /sections");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Specializations</title>
        <link rel="stylesheet" href="pages/card_layout.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="pages/session_tracker.js"></script>
    </head>
    
    <body>
        <?php require 'header.php'; ?> <!--This is for the topbar -->
        <?php require 'menu.php'; ?> <!--This is for the menu -->
    
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        
        <div class="create-button" id="createDiv">
            
        </div>
        
        <div class="card-container">
            <?php
                
                showSpecializations();                   
            ?>
          
        </div>
        
        <?php require 'footer.php'; ?> 
    </body>
</html>
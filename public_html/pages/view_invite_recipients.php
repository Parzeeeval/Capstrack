<?php
    require "connection.php";
    session_start();
    
  
     if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["taskID"])){
           $_SESSION["taskID"] = $_POST["taskID"];
           getTrackingNum($_POST["taskID"]);
        }
     }
   
    
    
    function getTrackingNum($taskID){
        global $conn;
        

        try{
            $sql = "SELECT * FROM tracking WHERE projectID = ? AND taskID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectID"], $taskID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                $_SESSION["trackingNum"] = $result["number"];
            }
            
            else{
                throw new Exception("Failed to retrieve tracking number");
            }
        }
        
        catch(Exception $e){
            echo '
                <script>
                    console.log("Error:'.$e->getMessage().'");
                </script>
            ';
        }
    }

    function getRecepients(){
        global $conn;
        
        try{
            // Fixed SQL: Added the missing comma between adviserID and projectID
            $sql = "SELECT u.*,
                           p.panelistID AS panelist_status, 
                           p.projectID AS panelist_projectID,
                           p.level, 
                           a.adviserID AS adviser_status, 
                           a.projectID AS adviser_projectID
                    FROM users u
                    LEFT JOIN panelists p ON p.panelistID = u.id AND p.projectID = ?
                    LEFT JOIN advisers a ON a.adviserID = u.id AND a.projectID = ?
                    WHERE p.projectID = ? OR a.projectID = ?";
            
            // Fixed number of parameters in the execute() call
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectID"], $_SESSION["projectID"], $_SESSION["projectID"], $_SESSION["projectID"]]);
    
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($results) >= 1) {
                foreach ($results as $result) {
                    $fullname = $result["surname"] . ", " . $result["firstname"] . " " . $result["middlename"];
                    

                    // Determine if they are a panelist, adviser, or both
                    $isPanelist = !is_null($result['panelist_status']);
                    $isAdviser = !is_null($result['adviser_status']);
                    
                    $facultyID = "";
                    $submit_status = "Not Yet Submitted";
                    $role = "";
                    
                    $date = "";
                   
                    if ($isPanelist) {
                        $level = $result["level"];
                        $facultyID = $result["panelist_status"];
                        
                        $sql = "SELECT submit_date FROM invitations WHERE projectID = ? AND trackingNum = ? AND facultyID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_SESSION["projectID"], $_SESSION["trackingNum"], $facultyID]);
                        $submitDate = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    
                        if($submitDate["submit_date"] != null || $submitDate["submit_date"] != ""){
                            $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $submitDate["submit_date"]);
    
                            // Format the date and time
                            $date = $dateTime->format('F j, Y h:i A');
                            
    
                            if($date != ""){
                                $submit_status = "Submitted";
                            }
                            
                            else{
                               $submit_status = "Not Yet Submitted";
                               $date = "";
                            }
                        }
                       
                        
                        if($level >= 2){
                          $role = "Chairman Panelist";
                        } 
                        
                        else {
                           $role = "Panelist";
                        }
                    }
    
                    if ($isAdviser) {
                       $role = "Adviser";
                       $facultyID = $result["adviser_status"];
                       
                       $sql = "SELECT submit_date FROM invitations WHERE projectID = ? AND trackingNum = ? AND facultyID = ?";
                       $stmt = $conn->prepare($sql);
                       $stmt->execute([$_SESSION["projectID"], $_SESSION["trackingNum"], $facultyID]);
                       $submitDate = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    
                        if($submitDate["submit_date"] != null || $submitDate["submit_date"] != ""){
                            $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $submitDate["submit_date"]);
    
                            // Format the date and time
                            $date = $dateTime->format('F j, Y h:i A');
                            
    
                            if($date != ""){
                                $submit_status = "Submitted";
                            }
                            
                            else{
                               $submit_status = "Not Yet Submitted";
                               $date = "";
                            }
                        }
                    }
                    
                
                   echo '
                        <div class="invitation-card" 
                             data-faculty-id="'.htmlspecialchars($facultyID).'"
                             data-faculty-name="'.htmlspecialchars($fullname).'"
                             data-faculty-role="'.htmlspecialchars($role).'"
                             onclick="submitInvitation(this)">
                             
                            <div class="invitation-card-header">'.$role.'</div>
                            <div class="invitation-card-body">'.htmlspecialchars($fullname).'</div>
                            <div class="invitation-attachment">
                                <i class="fas fa-paperclip"></i> Defense Invitation
                            </div>
                            <div class="invitation-footer">
                                <span>'.$submit_status.'</span>
                                <span>'.$date.'</span>
                            </div>
                        </div>
                    
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            function submitInvitation(element) {
                                const facultyID = element.getAttribute("data-faculty-id");
                                const facultyName = element.getAttribute("data-faculty-name");
                                const facultyRole = element.getAttribute("data-faculty-role");
                            
                                $.ajax({
                                    url: "/send_defense_invitation",
                                    type: "POST", // Changed to POST
                                    data: {
                                        facultyID: facultyID,
                                        facultyName: facultyName,
                                        facultyRole: facultyRole,
                                        ajax: true // Custom parameter to indicate this is an AJAX request
                                    },
                                    success: function(response) {
                                        console.log("Data sent successfully");
                                        // Redirect to the desired page after processing
                                        window.location.href = "/send_defense_invitation"; // Change to your target page
                                    },
                                    error: function(xhr, status, error) {
                                        console.error("Error:", error);
                                    }
                                });
                            }
                        </script>
                    ';
                }
            }
        } 
        
        catch(Exception $e) { // Fixed the exception class name
            echo '
                <script>
                    console.log("'.$e->getMessage().'");
                </script>   
            ';
        }
    }
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="pages/title_evaluation.css">
        <link rel="stylesheet" href="pages/view_invite_recipients.css">
        <script src="pages/session_tracker.js"></script>
        
        <title>Invitation Recipients</title>
    </head>
    
    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
       
        <h2 class="section-title">Invitations</h2>
        <p class="semester-info"></p>
        <p class="due-date-info"></p>
    
     
        <div class="invitation-container">
            <?php
                getRecepients();
            ?>
        </div>
        
      
        
    <?php require 'footer.php'; ?>
    </body>
</html>

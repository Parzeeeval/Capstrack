<?php
    require_once "connection.php";
    session_start();
    
    $fileExists = false;
    $isCoordinator = false;
    
    $_SESSION["canViewFile"] = true;
    

    function checkUser(){
        global $conn, $isCoordinator, $fileExists;
        
        try{
            $sql = "SELECT c.*, u.* FROM coordinators c JOIN users u ON c.facultyID = u.id WHERE facultyID = ? AND sectionID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"], $_SESSION["sectionID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){

                $fullname = $result["surname"] . ", " . $result["firstname"] . " " . $result["middlename"];
                        
                $sql = "SELECT submit_date FROM capstone_papers WHERE projectID = ? AND trackingNum = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["trackingNum"]]);
                $submitDate = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        
                if($submitDate["submit_date"] != null || $submitDate["submit_date"] != ""){
                    $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $submitDate["submit_date"]);

                    // Format the date and time
                    $date = $dateTime->format('F j, Y h:i A');
                    

                    if($date != ""){
                       echo '
                            <script>
                                document.getElementById("submitDate").innerText = "'.$date.'";
                            </script>
                        ';
                    }
                    
                    else{
                      echo'
                            <script>
                                document.getElementById("submitDate").innerText = "Not Yet Submitted";
                            </script>
                        ';
                    }
                }
                
                else{
                  echo '
                        <script>
                            document.getElementById("submitDate").innerText = "Not Yet Submitted";
                        </script>
                    ';
                }
                
                $isCoordinator = true;
                           
            }
        }
        
        catch(Exception $e){
             echo '<script>
                        Swal.fire({
                             title: "Error",
                            text: "Error Message:'.$e->getMessage().'",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/answer_capstone_paper";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/answer_capstone_paper";
                                    }
                                });
                  </script>';
        }
    }
    
    
    function getStatus(){
        global $conn, $isCoordinator, $fileExists;
        
        try{
            $sql = "SELECT 
                    s.*,
                    cp.*,
                    ay.*
                    FROM sections s
                    JOIN capstone_projects cp ON cp.sectionID = s.sectionID
                    JOIN academic_year ay ON ay.id = cp.academicYearID
                    WHERE cp.projectID = ? 
                    AND cp.sectionID = ? 
                    AND cp.academicYearID = ?";
                    
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["sectionID"], $_SESSION["acadYearValue"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                $groupInfo = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                $groupNum = "Group " . $result["groupNum"];
                $specialization = $result["specialization"];
                
                $display = $groupInfo . ", " . $groupNum . ", " . $specialization;
                
                $semester = "";
                
                if($result["semester"] == 2){
                    $semester = "2nd Semester";
                } else {
                    $semester = "1st Semester";
                }
                
                $acadYear = $result["start_year"] . "-" . $result["end_year"] . " (" . $semester . ")";
                
                echo '
                    <script>
                         document.getElementById("groupInfo").innerHTML = "'.$display.'";
                         document.getElementById("acadYear").innerHTML = "'.$acadYear.'";
                    </script>
                ';
                
                $sql = "SELECT * FROM capstone_papers WHERE projectID = ? AND academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["acadYearValue"]]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result && !empty($result["filepath"])) {
                    $filepath = $result["filepath"];
                    $filename = basename($filepath);
                    
                    // Check if the file exists
                    if (file_exists($filepath)) {
                        $submitDate = $result["submit_date"];
                        $date = new DateTime($submitDate);
                        $formattedDate = $date->format('F j, Y g:i A');
                        
                        $status = $result["status"];
                        
                        // Output the file path and name as JSON for JavaScript to handle
                        echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                displayExistingFile('{$filename}', '{$filepath}');
                            });
                            
                            document.getElementById('fileName').innerHTML = '{$filename}';
                            document.getElementById('submitDate').innerHTML = 'Submitted: {$formattedDate}';
                        </script>";
                        
                        if($status == "accepted"){
                            echo '
                                <script>
                                    document.getElementById("statusLabel").innerHTML = "Capstone Paper Accepted &#x2705";
                                    document.getElementById("statusLabel").hidden = false;
                                </script>
                            ';
                        } else {
                            if($isCoordinator == true){
                                echo '
                                    <div>
                                        <form action="" method="POST">
                                            <button name="acceptBtn" class="button-upload">ACCEPT</button>
                                            <span class="not-accepted-text">Not yet accepted</span>
                                        </form>
                                    </div>
                                ';
                            }
                            
                            if($status == "submitted"){
                                $conn->beginTransaction();
                                
                                $sql = "UPDATE capstone_papers SET status = ? WHERE projectID = ? AND academicYearID = ?";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute(["evaluating", $_SESSION["projectIDValue"], $_SESSION["acadYearValue"]]);
                                
                                if($result){
                                    
                                     $sql = "SELECT * FROM sections WHERE sectionID = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["sectionID"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                     $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                                     
                                     
                                     $sql = "SELECT groupNum FROM capstone_projects WHERE projectID = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["projectIDValue"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                     
                                     $groupNum = $result["groupNum"];
                                    
                                    
                                     $sql = "SELECT * FROM users WHERE id = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["userID"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                      
                                     $firstname = $result["firstname"];
                                     $surname = $result["surname"];
                                     $middlename = $result["middlename"];

                                     $action = "". $surname . ", " . $firstname . " " . $middlename . " accepted the capstone paper of Group " . $groupNum . " in " . $section;
                                     
                                     date_default_timezone_set('Asia/Manila');
                                     $date = date('Y-m-d H:i:s');
                            
                                     $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                                     $stmt = $conn->prepare($sql);
                                     $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                                     
                                     $conn->commit();
                                } 
                                
                                else {
                                    $conn->rollBack();
                                    throw new Exception("Failed to update capstone paper status");
                                }
                            }
                        }
                    } else {
                        // If the file doesn't exist, show a message
                        echo "<script>
                            document.getElementById('fileName').innerHTML = 'Capstone Paper Not Found';
                            document.getElementById('submitDate').innerHTML = 'Not Yet Submitted';
                        </script>";
                    }
                    
                    $fileExists = true;
                } else {
                    echo "<script>
                        document.getElementById('fileName').innerHTML = 'Capstone Paper Not Yet Submitted';
                        document.getElementById('submitDate').innerHTML = 'Not Yet Submitted';
                    </script>";
                }
            } else {
                throw new Exception("Error retrieving group info");
            }
        } catch(Exception $e) {
            echo '<script>
                Swal.fire({
                     title: "Error",
                    text: "Error Message:'.$e->getMessage().'",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "/answer_capstone_paper";
                            }
                            else if (result.isDismissed) {
                                 window.location.href = "/answer_capstone_paper";
                            }
                        });
              </script>';
        }
    }

    
    function getComment(){
        global $conn;
        
        try{
            $sql = "SELECT * FROM capstone_papers WHERE projectID = ? AND academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["acadYearValue"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
             
                $comment = $result["comment"];
                echo "$comment";
            }
        }
        
        catch(Exception $e){
             echo '<script>
                        Swal.fire({
                             title: "Error",
                            text: "Error Message:'.$e->getMessage().'",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/answer_capstone_paper";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/answer_capstone_paper";
                                    }
                                });
                  </script>';
        }
    }

    function getTrackingNum(){
        global $conn;
        
        try{
            $sql = "SELECT * FROM tracking WHERE projectID = ? AND taskID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["taskID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                $_SESSION["trackingNum"] = $result["number"];
                
                echo '
                    <script>
                        document.getElementById("trackingLabel").innerText = "Tracking Number: '.$_SESSION["trackingNum"].'";
                    </script>
                ';
            }
            
            else{
                 echo '
                    <script>
                        document.getElementById("trackingLabel").innerText = "Tracking Number: TBD";
                    </script>
                ';
            }
        }
        
        catch(Exception $e){
         echo '<script>
                Swal.fire({
                     title: "Error",
                    text: "Error Message:'.$e->getMessage().'",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/answer_capstone_paper";
                        }
                        
                        else if (result.isDismissed) {
                             window.location.href = "/answer_capstone_paper";
                        }
                    });
              </script>';
        }
    }
   
    function updateLogs() {
        global $conn;
    
        try {
                // Fetch user details
                $sql = "SELECT * FROM users WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["userID"]]);
                $userResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($userResult) {
                    $description = "";
                    $firstname = $userResult["firstname"];
                    $surname = $userResult["surname"];
    
                 
                    $description = "Coordinator: " . $surname . ", " . $firstname . " accepted the capstone paper";
                    
                  
                    $conn->beginTransaction();
    
                    // Insert log entry
                    date_default_timezone_set('Asia/Manila');
                    $sql = "INSERT INTO activity_logs (userID, projectID, taskID, description, date, time, trackingNum) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $result = $stmt->execute([
                        $_SESSION["userID"],
                        $_SESSION["projectIDValue"],
                        $_SESSION["taskID"],
                        $description,
                        date("Y-m-d"),
                        date("H:i:s"),
                        $_SESSION["trackingNum"]
                    ]);
                    
                    if($result){
                        
                        $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_SESSION["userID"]]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        $coord_name = $user["firstname"] . " " . $user["surname"] ;
                      
                        $desc = "Capstone Coordinator: " . $coord_name . " accepted the capstone paper";
                                
                        date_default_timezone_set('Asia/Manila');
                        $date = date('Y-m-d H:i:s');
                        
                        
                        $sql = "SELECT id FROM students WHERE projectID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_SESSION["projectIDValue"]]);
                        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if(count ($students) >= 1){
                            foreach($students as $student){
                                $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$student["id"], $desc, $date]);
                                
                                if(!$result){
                                    throw new Exception("Failed to insert notifications");  
                                }
                            }
                        }
                        
                            
                        $conn->commit();
                    }
                    
                    else{
                        throw new Exception("Failed to insert activity log as panelist");
                    }
                
                }
                
                else{
                    throw new Exception("Failed to get coordinator info for updating logs");
                }
        } 
        
        catch (Exception $e) {
            $conn->rollBack();
            
            unset($_POST["acceptBtn"]);
    
            echo '<script>
                    Swal.fire({
                        title: "Error",
                        text: "Error Message: ' . $e->getMessage() . '",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "/answer_capstone_paper";
                            }
                            
                            else if (result.isDismissed) {
                                 window.location.href = "/answer_capstone_paper";
                            }
                        });
                  </script>';
        }
    }

    
    
    function updateStatus(){
        global $conn;
        
        try{
            $conn->beginTransaction();
            
            
            $sql = "UPDATE capstone_papers SET status = ? WHERE projectID = ? AND academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute(["accepted", $_SESSION["projectIDValue"], $_SESSION["acadYearValue"]]);

            if($result){
                
                $sql = "UPDATE tracking SET status = ? WHERE number = ?";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute(["completed", $_SESSION["trackingNum"]]);
                
                if($result){

                    $conn->commit();
                    
                    updateLogs();
                
                    unset($_POST["acceptBtn"]);
                
                    echo '<script>
                            Swal.fire({
                                 title: "Success",
                                text: "Capstone Paper Accepted!",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/answer_capstone_paper";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/answer_capstone_paper";
                                    }
                                });
                    </script>';
                }
                
                else{
                    throw new Exception("Failed to update document tracking status of capstone paper to completed");
                }
                
                    
                  
            }
          
            else{
                throw new Exception("Failed to accept capstone paper");
            }
        }
        
        catch(Exception $e){
            $conn->rollBack();
            
            unset($_POST["acceptBtn"]);
            
            echo '<script>
                    Swal.fire({
                         title: "Error",
                        text: "Error Message:'.$e->getMessage().'",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/answer_capstone_paper";
                        }
                        
                        else if (result.isDismissed) {
                             window.location.href = "/answer_capstone_paper";
                        }
                    });
                  </script>';
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
        <link rel="stylesheet" href="pages/invitation2.css">
        <script src="pages/session_tracker.js"></script>
        
        <title>Capstone Paper</title>
      
    </head>
    
    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
        

        <h1 class="section-title">Capstone Paper</h1>
        <p id="groupInfo" class="group-info">BSIT 3A-G1, Group 2, Business Analytics </p>
       
        <div id="infoContainer" class="semester-container">
            <p id="acadYear" class="semester-info">2023 - 2024 (2nd Semester)</p>
        </div>
        
        <div class="horizontal-container">
            <div class="left-side">
                
                <div class="comments-section">
                    <label for="comments">Comments</label>
                    <textarea id="comments" rows="4" placeholder="Student Comments..." disabled><?php getComment();?></textarea>
                </div>
            </div>
        
            <div id="file-display" class="right-side">
                <div class="file-section" > <!--change width ng file box here pag need -->
                    <label for="file-upload">File</label>
                    <div id="file-upload" class="file-upload">
                        <i class="fas fa-file-pdf" style="color: red; font-size: 35px;"></i>
                        <span id="fileName">Open Capstone Paper</span>
                    </div>
                    
                    <p id="submitDate" class="submitted-date-info">Submitted: November 30, 2023</p>
                    
                     <span id="statusLabel" class="accepted-text" hidden></span>
                </div>
            </div>
        </div>
        
         
        
        <!-- Modal for displaying PDF -->
        <div id="pdf-modal" class="modal">
            <div class="modal-content">
                 <!--<h2 id="modal-file-name"></h2>-->
                <span class="close-button" onclick="closeModal()">&times;</span>
                <iframe id="pdf-frame" name="" src="" width="100%" height="100%" style="border:none;"></iframe>
            </div>
        </div>
    
        <script>
            const fileDisplay = document.getElementById('file-display');
            let tempPdfUrl = ''; // Temporary URL for the uploaded PDF
            

            // Click to view the selected or fetched file
            fileDisplay.addEventListener('click', function() {
                if (tempPdfUrl) {
                    openModal(tempPdfUrl); // For newly uploaded file
                }
            });
            
            // Display existing file from database
            function displayExistingFile(filename, filepath) {
                fileDisplay.style.display = 'block';
               
                // Open existing file in modal on click
                fileDisplay.addEventListener('click', function () {
                    openModal(filepath);  // Direct path from database
                });
            }
            
            // Modal functions for viewing PDFs
            function openModal(filePath) {
                const modal = document.getElementById('pdf-modal');
                const pdfFrame = document.getElementById('pdf-frame');
                
                //const modalFileName = document.getElementById('modal-file-name'); //Get the element for the filename
                //modalFileName.textContent = filename; // Display the filename in the modal
                
                
                pdfFrame.src = filePath;
                modal.style.display = 'block';
            }
            
            function closeModal() {
                const modal = document.getElementById('pdf-modal');
                modal.style.display = 'none';
                document.getElementById('pdf-frame').src = '';  // Clear iframe src on close
            }
        </script>
        
        <?php checkUser(); ?>
        <?php getStatus(); ?>
        
              
        <div style="margin-bottom: 100px;">
            <p class="document-tracking" id="trackingLabel" style="margin-bottom: 100px;"></p>
        </div>
        
        <?php getTrackingNum(); ?>
        
    
        <?php 
            // Check if the form has been submitted and a file is uploaded
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if(isset($_POST["taskID"])){
                    $_SESSION["taskID"] = $_POST["taskID"];
                    getTrackingNum();
                }
                
                else if(isset($_POST["acceptBtn"])){
                    updateStatus();
                }
            }
        ?>
        
        <?php require 'footer.php'; ?>
    </body>
</html>
<footer>
        <p>© Capstrack 2024</p>
    </footer>


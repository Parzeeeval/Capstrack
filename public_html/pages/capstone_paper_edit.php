<?php
    require_once "connection.php";
    session_start();
    
    
     $_SESSION["canViewFile"] = true;
    
    $fileExists = false;
    $fileRemoved = false;

    
    function getStatus(){
        global $conn, $fileExists;
        
       try {
            $sql = "SELECT * FROM capstone_papers WHERE projectID = ? AND academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectID"], $_SESSION["acadYearID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && !empty($result["filepath"])) {
                $filepath = $result["filepath"];
                $filename = basename($filepath);
                
                // Check if the file exists on the server
                if (file_exists($filepath)) {
                    $_SESSION["filepath"] = $filepath;
                    $status = $result["status"];
                    $submitDate = $result["submit_date"];
                    
                    if ($submitDate != null || $submitDate != "") {
                        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $submitDate);
        
                        // Format the date and time
                        $date = $dateTime->format('F j, Y h:i A');
                        
                        if ($date != "") {
                            echo '
                                <script>
                                    document.getElementById("submit_date").innerText = "Submitted: '.$date.'";
                                </script>
                            ';
                        } 
                        
                        else {
                            echo '
                                <script>
                                    document.getElementById("submit_date").innerText = "Not Yet Submitted";
                                </script>
                            ';
                        }
                    } 
                    
                    else {
                        echo '
                            <script>
                                document.getElementById("submit_date").innerText = "Not Yet Submitted";
                            </script>
                        ';
                    }
                    
                    // Output the file path and name as JSON for JavaScript to handle
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            displayExistingFile('{$filename}', '{$filepath}');
                        });
                        
                        document.getElementById('submit-btn').innerText = 'Unsubmit';
                        document.getElementById('submit-btn').style.backgroundColor = '#b22222';
                        document.getElementById('submit-btn').onclick = enableButton; // Change function
                        document.getElementById('remove-file').style.display = 'none'; // Hide the button
                        document.getElementById('remove-file').disabled = true; // Disables the button
                        document.getElementById('comments').disabled = true;
                        
                        document.getElementById('file-upload').onclick = ''; // Disables the file upload
                    </script>";
                    
                    $fileExists = true;
                    
                    if ($status == "accepted") {
                        echo ' 
                            <script>
                                document.getElementById("acceptStatus").innerHTML = "Capstone Paper Accepted &#x2705";
                                document.getElementById("submit-btn").remove();
                            </script>
                        ';
                    }
                } 
                
                else {
                    // If the file doesn't exist, show a message
                    echo '
                        <script>
                            document.getElementById("submit_date").innerText = "File Not Found";
                            document.getElementById("submit-btn").disabled = true; // Disable the submit button if file is missing
                        </script>
                    ';
                }
            } 
            
            else {
                echo ' 
                    <script>
                        document.getElementById("submit_date").innerText = "Not Yet Submitted";
                        document.getElementById("submit-btn").disabled = true; // Disable the submit button if file is missing
                    </script>
                ';
            }
        } 
        
        catch (Exception $e) {

        }
    }
    
    function getComment(){
        global $conn;
        
        try{
            $sql = "SELECT * FROM capstone_papers WHERE projectID = ? AND academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectID"], $_SESSION["acadYearID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
             
                $comment = $result["comment"];
                echo "$comment";
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

    function getTrackingNum(){
        global $conn;
        
        try{
            $sql = "SELECT * FROM tracking WHERE projectID = ? AND taskID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectID"], $_SESSION["taskID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                $_SESSION["trackingNum"] = $result["number"];
                
                echo '
                    <p class="document-tracking" id="trackingLabel" style="margin-bottom: 50px;">Tracking Number: '.$_SESSION["trackingNum"].'</p>
                ';
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
    
    
    function updateLogs($category) {
        global $conn;
    
        try {
            // Fetch user details (same as above)
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"]]);
            $userResult = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userResult) {
                $firstname = $userResult["firstname"];
                $surname = $userResult["surname"];

                // Prepare log description for adviser
                $description = "";
                
                if($category == "upload"){
                    $description = "Student: " . $surname . ", " . $firstname . " updated the capstone paper";
                }
                
                else if($category == "comment"){
                    $description = "Student: " . $surname . ", " . $firstname . " updated the capstone paper and comments";
                }
                
                else{
                    $description = "Student: " . $surname . ", " . $firstname . " updated the the capstone paper";
                }
                
                
                $conn->beginTransaction();

                // Insert log entry for adviser
                date_default_timezone_set('Asia/Manila');
                $sql = "INSERT INTO activity_logs (userID, projectID, taskID, description, date, time, trackingNum) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([
                    $_SESSION["userID"],
                    $_SESSION["projectID"],
                    $_SESSION["taskID"],
                    $description,
                    date("Y-m-d"),
                    date("H:i:s"),
                    $_SESSION["trackingNum"]
                ]);
                
                if($result){
                    
                    $sql = "SELECT * FROM tracking WHERE number = ? AND taskID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["trackingNum"], $_SESSION["taskID"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result){
                        $status = $result["status"];
                        
                        if($status == "started"){
                            
                            $sql = "UPDATE tracking SET status = ? WHERE number = ? AND status = 'started'";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute(["submitted", $_SESSION["trackingNum"]]);
                            
                            if($result){
                                $conn->commit();
                            }
                            
                            else{
                                throw new Exception("Failed to update document tracking status of capstone paper to submitted");
                            }
                        }
                        
                        else{
                            $conn->commit();
                        }
                    }
                    
                    else{
                        throw new Exception("Failed to get status of tracking");
                    }
                }
                
                else{
                    throw new Exception("Failed to insert activity log as student");
                }
            }
        
        
            else {
                throw new Exception("Failed to retrieve user informations for activity logs");
            }
            
        } 
        
        catch (Exception $e) {
            $conn->rollBack();
            
            unset($_POST["submit-btn"]);
    
            echo '<script>
                    Swal.fire({
                        title: "Error",
                        text: "Error Message: ' . $e->getMessage() . '",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "/edit_capstone_paper";
                            }
                            
                            else if (result.isDismissed) {
                                 window.location.href = "/edit_capstone_paper";
                            }
                        });
                  </script>';
        }
    }

    function uploadFile($comment) {
        global $conn;
    
        try {
            // Validate required session variables
            $requiredSessionVars = ["projectID", "acadYearID", "trackingNum"];
            foreach ($requiredSessionVars as $var) {
                if (!isset($_SESSION[$var]) || empty($_SESSION[$var])) {
                    throw new Exception("Missing or empty session variable: $var");
                }
            }
    
            // Check if a file was uploaded
            if (!isset($_FILES['uploaded_file'])) {
                throw new Exception("No file was uploaded.");
            }
            
            $conn->beginTransaction();
    
            $targetDir = "capstone_papers/";
            $fileTmpPath = $_FILES['uploaded_file']['tmp_name'];
            $fileName = basename($_FILES['uploaded_file']['name']);
            $fileType = $_FILES['uploaded_file']['type'];
            $allowedFileType = 'application/pdf';
    
            if ($fileType !== $allowedFileType) {
                throw new Exception("Only PDF files are allowed. Uploaded type: $fileType");
            }
    
            // Create a directory for the capstone paper based on projectID and academic year
            $paperDir = $targetDir . $_SESSION["projectID"] . "-" . $_SESSION["acadYearID"];
            if (!is_dir($paperDir) && !mkdir($paperDir, 0777, true)) {
                throw new Exception("Failed to create paper directory: $paperDir");
            }
    
            // Define the target path for the uploaded file
            $targetFilePath = $paperDir . '/' . $fileName;
            if (!move_uploaded_file($fileTmpPath, $targetFilePath)) {
                throw new Exception("Failed to move uploaded file to: $targetFilePath");
            }
    
            date_default_timezone_set('Asia/Manila');
            $date = date("Y-m-d H:i:s");
    
            // Database check for an existing record in capstone_papers
            $checkSql = "SELECT COUNT(*) FROM capstone_papers WHERE projectID = ? AND academicYearID = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->execute([
                $_SESSION["projectID"], 
                $_SESSION["acadYearID"]
            ]);
    
            // Set fileExists based on the database result
            $fileExists = $checkStmt->fetchColumn() > 0;
            echo '<script>console.log("fileExists based on DB check: '.json_encode($fileExists).'");</script>';
    
            if (!$fileExists) {
                // INSERT operation
                echo '<script>console.log("Attempting INSERT operation");</script>';
    
                $sql = "INSERT INTO capstone_papers (projectID, status, submit_date, academicYearID, filepath, comment, trackingNum) VALUES(?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    $_SESSION["projectID"], 
                    "submitted", 
                    $date, 
                    $_SESSION["acadYearID"], 
                    $targetFilePath, 
                    $comment, 
                    $_SESSION["trackingNum"]
                ]);
    
                $rowCount = $stmt->rowCount();
                if ($rowCount > 0) {
                    
                    $sql = "SELECT * FROM sections s
                          JOIN capstone_projects cp ON cp.sectionID = s.sectionID
                          JOIN academic_year ay ON cp.academicYearID = ay.id
                          JOIN users u ON cp.coordinatorID = u.id
                          WHERE s.sectionID = ?
                    ";
                  
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["sectionID"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result){
                        
                        $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                        
                        $sql =  "SELECT groupNum FROM capstone_projects WHERE projectID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_SESSION["projectID"]]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if($result){
                            
                            $groupNum = $result["groupNum"];
                            
                            $desc =  $section . " Group " . $groupNum . " submitted their capstone paper" ;
                            
                            date_default_timezone_set('Asia/Manila');
                            $date = date('Y-m-d H:i:s');
                        
                                        
                            $sql = "SELECT panelistID FROM panelists WHERE projectID = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["projectID"]]);
                            $panelists = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if(count ($panelists) >= 1){
                                foreach($panelists as $panelist){
                                    $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                    $stmt = $conn->prepare($sql);
                                    $result = $stmt->execute([$panelist["panelistID"], $desc, $date]);
                                    
                                    if(!$result){
                                        throw new Exception("Failed to insert notifications");  
                                    }
                                }
                            }
                            
                            
                            $sql = "SELECT adviserID FROM advisers WHERE projectID = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["projectID"]]);
                            $advisers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if(count ($advisers) >= 1){
                                foreach($advisers as $adviser){
                                    $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                    $stmt = $conn->prepare($sql);
                                    $result = $stmt->execute([$adviser["adviserID"], $desc, $date]);
                                    
                                    if(!$result){
                                        throw new Exception("Failed to insert notifications");  
                                    }
                                }
                            }
                            
                            $sql = "SELECT facultyID FROM coordinators WHERE sectionID = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["sectionID"]]);
                            $coordinator = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if($coordinator){

                                $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$coordinator["facultyID"], $desc, $date]);
                                
                                if(!$result){
                                    throw new Exception("Failed to insert notifications");  
                                }
                                
                            }
                            
                             $sql = "SELECT * FROM users WHERE id = ?";
                             $stmt = $conn->prepare($sql);
                             $stmt->execute([$_SESSION["userID"]]);
                             $result = $stmt->fetch(PDO::FETCH_ASSOC);
                              
                             $firstname = $result["firstname"];
                             $surname = $result["surname"];
                             $middlename = $result["middlename"];
                                    
                             $action = "". $surname . ", " . $firstname . " " . $middlename . " submitted their capstone paper";
            
                            $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                            
                            if(!$result){
                                throw new Exception("Failed to insert action logs");  
                            }
                    
                            $conn->commit();
                            
                            updateLogs("upload");
                            unset($_POST["submit-btn"]);
                            
                            echo '<script>
                                    Swal.fire({
                                        title: "Success",
                                        text: "Capstone Paper Uploaded!",
                                        icon: "success",
                                        confirmButtonText: "OK"
                                    }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "/edit_capstone_paper";
                                            }
                                            
                                            else if (result.isDismissed) {
                                                 window.location.href = "/edit_capstone_paper";
                                            }
                                        });
                                </script>';
                        }
                    }
                    
                } 
                
                else {
                    throw new Exception("No rows inserted. Row count: $rowCount.");
                }
    
            } 
            
            else {
                // UPDATE operation
                echo '<script>console.log("Attempting UPDATE operation");</script>';
                
                date_default_timezone_set('Asia/Manila');
                $date = date("Y-m-d H:i:s");
    
                $sql = "UPDATE capstone_papers SET filepath = ?, comment = ?, submit_date = ? WHERE projectID = ? AND academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    $targetFilePath, 
                    $comment,
                    $date,
                    $_SESSION["projectID"], 
                    $_SESSION["acadYearID"]
                ]);
    
                $rowCount = $stmt->rowCount();
                if ($rowCount > 0) {
                    
                     $sql = "SELECT * FROM sections s
                          JOIN capstone_projects cp ON cp.sectionID = s.sectionID
                          JOIN academic_year ay ON cp.academicYearID = ay.id
                          JOIN users u ON cp.coordinatorID = u.id
                          WHERE s.sectionID = ?
                    ";
                  
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["sectionID"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result){
                        
                        $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                        
                        $sql =  "SELECT groupNum FROM capstone_projects WHERE projectID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_SESSION["projectID"]]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if($result){
                            
                            $groupNum = $result["groupNum"];
                            
                            $desc =  $section . " Group " . $groupNum . " updated their capstone paper" ;
                            
                            date_default_timezone_set('Asia/Manila');
                            $date = date('Y-m-d H:i:s');
                        
                                        
                            $sql = "SELECT panelistID FROM panelists WHERE projectID = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["projectID"]]);
                            $panelists = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if(count ($panelists) >= 1){
                                foreach($panelists as $panelist){
                                    $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                    $stmt = $conn->prepare($sql);
                                    $result = $stmt->execute([$panelist["panelistID"], $desc, $date]);
                                    
                                    if(!$result){
                                        throw new Exception("Failed to insert notifications");  
                                    }
                                }
                            }
                            
                            
                            $sql = "SELECT adviserID FROM advisers WHERE projectID = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["projectID"]]);
                            $advisers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if(count ($advisers) >= 1){
                                foreach($advisers as $adviser){
                                    $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                    $stmt = $conn->prepare($sql);
                                    $result = $stmt->execute([$adviser["adviserID"], $desc, $date]);
                                    
                                    if(!$result){
                                        throw new Exception("Failed to insert notifications");  
                                    }
                                }
                            }
                            
                            $sql = "SELECT facultyID FROM coordinators WHERE sectionID = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["sectionID"]]);
                            $coordinator = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if($coordinator){

                                $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$coordinator["facultyID"], $desc, $date]);
                                
                                if(!$result){
                                    throw new Exception("Failed to insert notifications");  
                                }
                                
                            }
                    
                        
                         $sql = "SELECT * FROM users WHERE id = ?";
                         $stmt = $conn->prepare($sql);
                         $stmt->execute([$_SESSION["userID"]]);
                         $result = $stmt->fetch(PDO::FETCH_ASSOC);
                          
                         $firstname = $result["firstname"];
                         $surname = $result["surname"];
                         $middlename = $result["middlename"];
                                
                         $action = "". $surname . ", " . $firstname . " " . $middlename . " re-submitted their capstone paper";
        
                        $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                        
                        if(!$result){
                            throw new Exception("Failed to insert action logs");  
                        }
                    
                        $conn->commit();
                        updateLogs("upload");
                        unset($_POST["submit-btn"]);
                        
                        echo '<script>
                                Swal.fire({
                                    title: "Success",
                                    text: "Capstone Paper Updated!",
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "/edit_capstone_paper";
                                        }
                                        
                                        else if (result.isDismissed) {
                                             window.location.href = "/edit_capstone_paper";
                                        }
                                    });
                            </script>';
                        }
                    }
                } 
                
                else {
                    throw new Exception("No rows updated. Row count: $rowCount.");
                }
            }
    
        } 
        
        catch (Exception $e) {
            $conn->rollBack();
            unset($_POST["submit-btn"]);
            echo '<script>console.error("Error: '.$e->getMessage().'");</script>';
        }
    }

    
    
    function updateComment($comment){
        global $conn;
        
        try{

            $conn->beginTransaction();
            
            $sql = "UPDATE capstone_papers SET comment = ? WHERE projectID = ? AND academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$comment, $_SESSION["projectID"], $_SESSION["acadYearID"]]);
       
            if($result){
                
                
              $sql = "SELECT * FROM sections s
                      JOIN capstone_projects cp ON cp.sectionID = s.sectionID
                      JOIN academic_year ay ON cp.academicYearID = ay.id
                      JOIN users u ON cp.coordinatorID = u.id
                      WHERE s.sectionID = ?
                ";
              
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["sectionID"]]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($result){
                    
                    $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                    
                    $sql =  "SELECT groupNum FROM capstone_projects WHERE projectID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["projectID"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result){
                        
                        $groupNum = $result["groupNum"];
                        
                        $desc =  $section . " Group " . $groupNum . " updated the comment on their capstone paper" ;
                        
                        date_default_timezone_set('Asia/Manila');
                        $date = date('Y-m-d H:i:s');
                    
                                    
                        $sql = "SELECT panelistID FROM panelists WHERE projectID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_SESSION["projectID"]]);
                        $panelists = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if(count ($panelists) >= 1){
                            foreach($panelists as $panelist){
                                $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$panelist["panelistID"], $desc, $date]);
                                
                                if(!$result){
                                    throw new Exception("Failed to insert notifications");  
                                }
                            }
                        }
                        
                        
                        $sql = "SELECT adviserID FROM advisers WHERE projectID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_SESSION["projectID"]]);
                        $advisers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if(count ($advisers) >= 1){
                            foreach($advisers as $adviser){
                                $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$adviser["adviserID"], $desc, $date]);
                                
                                if(!$result){
                                    throw new Exception("Failed to insert notifications");  
                                }
                            }
                        }
                        
                        $sql = "SELECT facultyID FROM coordinators WHERE sectionID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_SESSION["sectionID"]]);
                        $coordinator = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if($coordinator){

                            $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$coordinator["facultyID"], $desc, $date]);
                            
                            if(!$result){
                                throw new Exception("Failed to insert notifications");  
                            }
                            
                        }
                
                
                    $conn->commit();
                    
                    updateLogs("comment");
                    
                    unset($_POST["submit-btn"]);
                    
                     echo '<script>
                                Swal.fire({
                                     title: "Success",
                                    text: "Capstone Paper Uploaded!",
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "/edit_capstone_paper";
                                        }
                                        
                                        else if (result.isDismissed) {
                                             window.location.href = "/edit_capstone_paper";
                                        }
                                    });
                        </script>';
                    
                    }
                }
            }
            
            else{
                throw new Exception("Failed creating capstone paper");
           }
        }
        
         catch(Exception $e){
            $conn->rollBack();
            
            unset($_POST["submit-btn"]);
            
            echo '<script>
                Swal.fire({
                     title: "Error",
                    text: "Error: '.addslashes($e->getMessage()).'",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "/edit_capstone_paper";
                    }
                    
                    else if (result.isDismissed) {
                         window.location.href = "/edit_capstone_paper";
                    }
                });
            </script>';;
        }
    }
    
    
    function getTemplate(){
        global $conn, $fileExists;
        
        try{
            $sql = "SELECT * FROM document_templates WHERE taskID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["taskID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && !empty($result["filepath"])) {
                $filepath = $result["filepath"];
                $filename = basename($filepath);
                
                if (file_exists($filepath)) {
                    $_SESSION["filepath"] = $filepath;
                    
    
                    // Output the file path and name as JSON for JavaScript to handle
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            displayTemplate('{$filename}', '{$filepath}');
                        });
                    </script>";
                    
                    $fileExists = true;
                }
                
                else {
                    // If the file doesn't exist, show a message
                    echo '
                        <script>
                            document.getElementById("template-text").textContent = "No Template Uploaded Yet";
                        </script>
                    ';
                }
            }
            
            else{
                 echo '
                    <script>
                        document.getElementById("template-text").textContent = "No Template Uploaded Yet";
                    </script>
                ';
            }
        }
        
        catch(Exception $e){
            echo '<script>
                    console.log("'.$e->getMessage().'");
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
        <link rel="stylesheet" href="pages/capstone_paper.css">
        <script src="pages/session_tracker.js"></script>
        
        <title>Capstone Papers</title>
      
    </head>
    
    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
        
        <h1 class="section-title">Capstone Paper</h1>
        
        <!--<p class="panel-info" id="facultyName"><?php echo htmlspecialchars($_SESSION["facultyName"]); ?></p>
        <p class="paneltitle-info" id="facultyRole"><?php echo htmlspecialchars($_SESSION["facultyRole"]); ?></p>-->
        
        <div class="semester-container">
            <p id="submit_date" class="due-date-info">Submitted: November 30, 2023</p>
        </div>
        
        <form action="" method="POST" enctype="multipart/form-data" id="fileUploadForm">
            <div class="horizontal-container">
                <div class="left-side">
                    <div class="document-section">
                        <label for="document-template">Document Template</label>
                        <div id="document-template" class="document-template">
                            <i class="fas fa-file-pdf" style="color: red; font-size: 20px;"></i>
                            <span id="template-text" style="text-align: center;">Click To View Document Template</span>
                        </div>
                    </div>
        
                    <div class="comments-section">
                        <label for="comments">Comments</label>
                        <textarea id="comments" name="paperComment" rows="4" placeholder="Optional Comments..."><?php getComment();?></textarea>
                    </div>
                </div>
        
                <div class="right-side">
                     <label for="file-upload">File</label>
                    <div class="file-section">
                        
                            <!-- Input for file upload -->
                            <input type="file" id="file-upload-input" name="uploaded_file" accept="application/pdf" style="display: none;" required>
                            
                            <!-- Custom styled file upload div -->
                            <div id="file-upload" class="file-upload" onclick="document.getElementById('file-upload-input').click();">
                                <span id="file-upload-text"><i class="fas fa-upload"></i>Click to upload a PDF file</span>
                            </div>
                            
                            <!-- Container for displaying uploaded file and remove button -->
                            <div id="file-display" class="file-display" style="display: none;">
                                <i class="fas fa-file-pdf" style="color: red; font-size: 20px;"></i>
                                <span id="file-name" class="highlight"></span>
                                
                                <button id="remove-file" class="remove-file" style="border: none; background: none;" type="button">
                                    <i id="removeIcon" class="fas fa-times-circle"></i>
                                </button>
                                
                                <img id="loading-gif" src="pages/images/loading.gif" alt="Uploading File..." style="display: none; width: 100px; height: 100px; margin-left: 350px; margin-top: 150px; position: fixed;">
                            </div>
                        
                    </div>
            
                    <div class="panelist-section">
                        <br>
 
                        <div class="panelist-info-buttons">
                            <button id="submit-btn" name="submit-btn" class="button-submit" type="button" onclick="showLoadingAndSubmit();">Submit</button>
                            
                            <span id="acceptStatus" class="accepted-text"></span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
       
        
        <!-- Modal for displaying PDF -->
        <div id="pdf-modal" class="modal">
            <div class="modal-content">
                 <!--<h2 id="modal-file-name"></h2>-->
                <span class="close-button" onclick="closeModal()">&times;</span>
                <iframe id="pdf-frame" name="" src="" width="100%" height="100%" style="border:none;"></iframe>
            </div>
        </div>
    
        <script>
            const fileInput = document.getElementById('file-upload-input');
            const fileDisplay = document.getElementById('file-display');
            const fileNameSpan = document.getElementById('file-name');
            const fileUploadText = document.getElementById('file-upload-text');
            const template = document.getElementById('document-template');
            const removeFileButton = document.getElementById('remove-file');
            let tempPdfUrl = ''; // Temporary URL for the uploaded PDF
            
            
            // Handle new file selection and display
            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const fileName = file.name;
                    fileNameSpan.textContent = file.name;
                    fileDisplay.style.display = 'flex'; // Show file display
                    fileUploadText.textContent = 'Uploaded File: ' + file.name;
                    
                    // Create a temporary URL for preview
                    tempPdfUrl = URL.createObjectURL(file);
                    
                    displayExistingFile(fileName, tempPdfUrl);
                    
                    //Re-enabled the submit button again if there is now an uploaded file
                    document.getElementById('submit-btn').disabled = false;
                    document.getElementById('submit-btn').style.backgroundColor = '#066BA3';
                }
            });
            
            // Handle file removal
            removeFileButton.addEventListener('click', function() {
                fileInput.value = ''; // Clear the file input
                fileDisplay.style.display = 'none'; // Hide file display
                fileUploadText.textContent = 'Click to upload a PDF file';
                tempPdfUrl = ''; // Clear temporary URL
                
                //Disable submit button since there is no file
                document.getElementById('submit-btn').style.backgroundColor = 'gray';
                document.getElementById('submit-btn').disabled = true;
            });
            
            // Click to view the selected or fetched file
            fileNameSpan.addEventListener('click', function() {
                if (tempPdfUrl) {
                    openModal(tempPdfUrl); // For newly uploaded file
                }
            });
            
            
            // Display existing file from database
            function displayExistingFile(filename, filepath) {
                fileNameSpan.textContent = filename;
                fileDisplay.style.display = 'flex';
                fileUploadText.textContent = 'Uploaded File: ' + filename;
                
                // Open existing file in modal on click
                fileNameSpan.addEventListener('click', function () {
                    openModal(filepath);  // Direct path from database
                });
            }
            
            function displayTemplate(filename, filepath) {
                // Open existing file in modal on click
                template.addEventListener('click', function() {
                    openModal(filepath); 
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
            
            function enableButton(){
                
                document.getElementById('submit-btn').onclick= showLoadingAndSubmit

                document.getElementById('submit-btn').innerText = 'Submit';
                document.getElementById('submit-btn').style.backgroundColor = '#066BA3';
                document.getElementById('remove-file').style.display = 'block'; 
                document.getElementById('remove-file').disabled = false;
                document.getElementById('comments').disabled = false;
                
                document.getElementById('file-upload').onclick = function() {
                    document.getElementById('file-upload-input').click();
                };
            }
            
            function showLoadingAndSubmit() {
                // Disable and hide the submit button, show the loading GIF
                document.getElementById("submit-btn").disabled = true;
                document.getElementById("submit-btn").hidden = true;
                
                document.getElementById("removeIcon").style.display = "none";
                
                document.getElementById('remove-file').disabled = true;
                document.getElementById('remove-file').hidden = true;
                
                document.getElementById("loading-gif").style.display = "block";
                
                // Optionally disable other form fields
                document.getElementById("file-upload").onclick = '';
                document.getElementById("comments").readOnly = true;
            
                // Submit the form
                document.getElementById("fileUploadForm").submit();
            }
        </script>
        
        
        <?php getStatus(); ?>
        
        
    
        <?php 
            // Check if the form has been submitted and a file is uploaded
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                global $fileExists;
                
                if (isset($_POST['taskID'])) {
                    $_SESSION["taskID"] = $_POST["taskID"];
                 
                    echo '<script>
                            console.log("'.$_SESSION["taskID"].'");
                        </script>';
                    
                }
                
                else{
            
                    if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] === UPLOAD_ERR_OK) {
                        uploadFile($_POST["paperComment"]);
                    }
                    
                    else if (!isset($_FILES['uploaded_file']) || $_FILES['uploaded_file']['error'] !== UPLOAD_ERR_OK){
                        
                        if($fileExists == false){
    
                            echo '<script>
                                Swal.fire({
                                     title: "No File Selected",
                                    text: "Please select a PDF File to upload",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/edit_capstone_paper";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/edit_capstone_paper";
                                    }
                                });
                            </script>';
                            
                            unset($_POST["submit-btn"]);
                        }
                        
                        else if($fileExists == true){
                            updateComment($_POST["paperComment"]);
                        }
                    }
                }
            }
        ?>
        
        
        
        <div>
            <?php
                //Always put this below
                
                getTrackingNum();
                getTemplate();
            ?>
        </div>
        
        <?php require 'footer.php'; ?>
    </body>
</html>
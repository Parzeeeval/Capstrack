<?php
    require_once "connection.php";
    session_start();
    
    $_SESSION["canViewFile"] = true;
    
    $fileExists = false;
    
    $selectedOption = isset($_GET['options']) ? $_GET['options'] : '';
    
    function checkUser(){
        global $conn, $selectedOption, $fileExists;
        
        try{
            $sql = "SELECT * FROM coordinators WHERE facultyID = ? AND sectionID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"], $_SESSION["sectionID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                
                $firstID = null;
                
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
                $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["projectIDValue"], $_SESSION["projectIDValue"], $_SESSION["projectIDValue"]]);
        
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
                            
                             if ($firstID === null) { // Set firstID only once if it’s still null
                                $firstID = $facultyID;
                             }
                            
                            $sql = "SELECT submit_date FROM invitations WHERE projectID = ? AND trackingNum = ? AND facultyID = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["trackingNum"], $facultyID]);
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
                           
                           if ($firstID === null) { // Set firstID only once if it’s still null
                                $firstID = $facultyID;
                           }
                           
                           $sql = "SELECT submit_date FROM invitations WHERE projectID = ? AND trackingNum = ? AND facultyID = ?";
                           $stmt = $conn->prepare($sql);
                           $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["trackingNum"], $facultyID]);
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
                            <script>
                                document.getElementById("dropdown").style.display = "block";
                                document.getElementById("options").disabled = false;
                            </script>
                        ';
                        
                        $isSelected = ($selectedOption == $facultyID) ? ' selected' : '';
                        echo "<option value=\"$facultyID\"$isSelected>$fullname</option>";
                    }
                }
                    
                 
                if ($firstID !== null) {
                    echo'
                        <script>
                            console.log("here")
                        </script>
                    ';
                    viewInvitations($firstID);
                }
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
                                        window.location.href = "/answer_defense_invitation";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/answer_defense_invitation";
                                    }
                                });
                  </script>';
        }
    }
    
    function getStatus(){
        global $conn, $fileExists;
        
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
                }
                
                else{
                    $semester = "1st Semester";
                }
                
                $acadYear = $result["start_year"] . "-" . $result["end_year"] . " (" . $semester . ")";
                
                echo '
                    <script>
                         document.getElementById("groupInfo").innerHTML = "'.$display.'";
                          document.getElementById("acadYear").innerHTML = "'.$acadYear.'";
                    </script>
                ';
                
                $sql = "SELECT * FROM invitations WHERE projectID = ? AND facultyID = ? AND academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["userID"], $_SESSION["acadYearValue"]]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
               if ($result && !empty($result["filepath"])) {
                    $filepath = $result["filepath"];
                    $filename = basename($filepath);
                    
                    $submitDate = $result["submit_date"];
                    $date = new DateTime($submitDate);
                    $formattedDate = $date->format('F j, Y g:i A');
                    
                    $status = $result["status"];
                    
                    // Check if the file exists on the server
                    if (file_exists($filepath)) {
                        // Output the file path and name as JSON for JavaScript to handle
                        echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                displayExistingFile('{$filename}', '{$filepath}');
                            });
                            
                            document.getElementById('fileName').innerHTML = '{$filename}';
                            document.getElementById('submitDate').innerHTML = 'Submitted: {$formattedDate}';
                        </script>";
                        
                        if ($status == "accepted") {
                            echo '
                                <script>
                                    document.getElementById("statusLabel").innerHTML = "Invitation Letter Accepted &#x2705";
                                    document.getElementById("statusLabel").hidden = false;
                                </script>
                            ';
                        } else {
                            echo '
                                <div>
                                    <form action="" method="POST">
                                        <button name="acceptBtn" class="button-upload">Accept</button>
                                        <span class="not-accepted-text">Not yet accepted</span>
                                    </form>
                                </div>
                            ';
                            
                            if ($status == "submitted") {
                                $conn->beginTransaction();
                                
                                $sql = "UPDATE invitations SET status = ? WHERE projectID = ? AND facultyID = ? AND academicYearID = ?";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute(["evaluating", $_SESSION["projectIDValue"], $_SESSION["userID"], $_SESSION["acadYearValue"]]);
                                
                                if ($result) {
                                    $conn->commit();
                                } else {
                                    $conn->rollBack();
                                    throw new Exception("Failed to update invitation status");
                                }
                            }
                        }
                    } else {
                        // If the file doesn't exist, show a message
                        echo "<script>
                            document.getElementById('fileName').innerHTML = 'File Not Found';
                            document.getElementById('submitDate').innerHTML = 'Not Yet Submitted';
                        </script>";
                    }
                    
                    $fileExists = true;
                } 
                
                else {
                     echo "<script>
                            document.getElementById('fileName').innerHTML = 'Invitation Letter Not Yet Submitted';
                            document.getElementById('submitDate').innerHTML = 'Not Yet Submitted';
                        </script>";
                }
            }
            
            else{
                 echo '<script>
                            console.log("failed to get invitations");
                        </script>';
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
                                        window.location.href = "/answer_defense_invitation";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/answer_defense_invitation";
                                    }
                                });
                  </script>';
        }
    }
    
    
    function viewInvitations($userID){
        global $conn, $fileExists;
    
        try {
            $sql = "SELECT * FROM invitations WHERE projectID = ? AND facultyID = ? AND academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectIDValue"], $userID, $_SESSION["acadYearValue"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($result && !empty($result["filepath"])) {
                $filepath = $result["filepath"];
                $filename = basename($filepath);
                
                // Check if the file actually exists on the server
                if (file_exists($filepath)) {
                    $submitDate = $result["submit_date"];
                    $date = new DateTime($submitDate);
                    $formattedDate = $date->format('F j, Y g:i A');
                    
                    $status = $result["status"];
                    $comment = $result["comment"];
                    
                    // Output the file path and name as JSON for JavaScript to handle
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            document.getElementById('file-display').style.pointerEvents = 'auto';
                            
                            displayExistingFile('{$filename}', '{$filepath}');
                            
                            document.getElementById('fileName').innerHTML = '{$filename}';
                            document.getElementById('submitDate').innerHTML = 'Submitted: {$formattedDate}';
                            document.getElementById('comments').value = " . json_encode($comment) . ";
                        });

                    </script>";
                    
                    if ($status == "accepted") {
                        echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                document.getElementById("statusLabel").innerHTML = "Invitation Letter Accepted &#x2705";
                                document.getElementById("statusLabel").hidden = false;
                            });
                        </script>';
                    }
                } 
                
                else {
                    // File path exists in the database but file is missing on the server
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            document.getElementById('fileName').innerHTML = 'File path exists but Invitation Letter is missing on the server';
                            document.getElementById('submitDate').innerHTML = 'Submission date not available';
                            
                            document.getElementById('statusLabel').innerHTML = '';
                            document.getElementById('statusLabel').hidden = false;
                            document.getElementById('comments').value = '';
                            
                            document.getElementById('file-display').style.pointerEvents = 'none';
                        });
                    </script>";
                }
            } 
            
            else {
                // No entry in the database or filepath is empty
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('fileName').innerHTML = 'Invitation Letter Not Yet Submitted';
                        document.getElementById('submitDate').innerHTML = 'Not Yet Submitted';
                        
                        document.getElementById('statusLabel').innerHTML = '';
                        document.getElementById('statusLabel').hidden = false;
                        document.getElementById('comments').value = '';
                        
                        document.getElementById('file-display').style.pointerEvents = 'none';
                    });
                </script>";
            }
        } 
        
        catch (Exception $e) {
            echo '<script>
                Swal.fire({
                    title: "Error",
                    text: "Error Message: ' . $e->getMessage() . '",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/answer_defense_invitation";
                        }
                        
                        else if (result.isDismissed) {
                             window.location.href = "/answer_defense_invitation";
                        }
                    });
            </script>';
        }
    }
        
    
    function getComment(){
        global $conn;
        
        try{
            $sql = "SELECT * FROM invitations WHERE projectID = ? AND facultyID = ? AND academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["userID"], $_SESSION["acadYearValue"]]);
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
                                        window.location.href = "/answer_defense_invitation";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/answer_defense_invitation";
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
                        document.getElementById("trackingLabel").innerText = "Tracking Number:  '.$_SESSION["trackingNum"].'";
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
                                window.location.href = "/answer_defense_invitation";
                            }
                            
                            else if (result.isDismissed) {
                                 window.location.href = "/answer_defense_invitation";
                            }
                        });
              </script>';
        }
    }
    
    function countRecepients() {
        global $conn;
    
        try {
            // Count panelists
            $sql = "SELECT COUNT(*) FROM panelists WHERE projectID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectIDValue"]]);
            $panelCount = $stmt->fetchColumn() ?: 0; // Use fetchColumn to get the count
    
            // Count advisers
            $sql = "SELECT COUNT(*) FROM advisers WHERE projectID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectIDValue"]]);
            $adviserCount = $stmt->fetchColumn() ?: 0; // Use fetchColumn to get the count
    
            // Total count
            $totalCount = $panelCount + $adviserCount;
    
            return $totalCount;
            
        } 
        
        catch (Exception $e) {
            // Log the error
            echo '<script>
                    console.log(' . json_encode($e->getMessage()) . ');
                  </script>';
            return 0; // Return 0 in case of an error
        }
    }
    
    function updateLogs() {
        global $conn;
    
        try {
            // Check if the user is a panelist
            $sql = "SELECT * FROM panelists WHERE panelistID = ? AND projectID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"], $_SESSION["projectIDValue"]]);
            $panelistResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($panelistResult) { // If user is a panelist
                $panelistLevel = $panelistResult["level"];
    
                // Fetch user details
                $sql = "SELECT * FROM users WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["userID"]]);
                $userResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($userResult) {
                    $description = "";
                    $firstname = $userResult["firstname"];
                    $surname = $userResult["surname"];
    
                    // Prepare log description based on level
                    if ($panelistLevel >= 2) {
                        $description = "Chairman Panelist: " . $surname . ", " . $firstname . " accepted the defense invitation";
                    } 
                    
                    else {
                        $description = "Panelist: " . $surname . ", " . $firstname . " accepted the defense invitation";
                    }
                    
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
                        $conn->commit();
                    }
                    
                    else{
                        throw new Exception("Failed to insert activity log as panelist");
                    }
                }
            } 
            
            else {
                // If user is not a panelist, check if they are an adviser
                
                $sql = "SELECT * FROM advisers WHERE adviserID = ? AND projectID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["userID"], $_SESSION["projectIDValue"]]);
                $adviserResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($adviserResult) {
                    // Fetch user details (same as above)
                    $sql = "SELECT * FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["userID"]]);
                    $userResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
                    if ($userResult) {
                        $firstname = $userResult["firstname"];
                        $surname = $userResult["surname"];
    
                        // Prepare log description for adviser
                        $description = "Adviser: " . $surname . ", " . $firstname . " accepted the defense invitation";
                        
                        
                        $conn->beginTransaction();
    
                        // Insert log entry for adviser
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
                            $conn->commit();
                        }
                        
                        else{
                            throw new Exception("Failed to insert activity log as adviser");
                        }
                    }
                } 
                
                else {
                    throw new Exception("User is neither a panelist nor an adviser.");
                }
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
                                window.location.href = "/answer_defense_invitation";
                            }
                            
                            else if (result.isDismissed) {
                                 window.location.href = "/answer_defense_invitation";
                            }
                        });
                  </script>';
        }
    }

    
    
    function updateStatus(){
        global $conn;
        
        try{
            $conn->beginTransaction();
            
            
            $sql = "UPDATE invitations SET status = ? WHERE projectID = ? AND facultyID = ? AND academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute(["accepted", $_SESSION["projectIDValue"], $_SESSION["userID"], $_SESSION["acadYearValue"]]);

            if($result){
                
                $conn->commit(); //complete the update first to get accurate count
                
                
                $sql = "SELECT * FROM invitations WHERE trackingNum = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["trackingNum"]]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $inviteCount = count($results); //Total number of sent invitations not the amount of recepients
                
                if($inviteCount >= 1){
                    
                    $completed = false;
                    $counter = 0;
                    
                    foreach($results as $result){
                        if($result["status"] == "accepted"){
                            $counter++;
                        }
                        
                        else{
                            break;
                        }
                    }
                    
                    
                    if($counter >= countRecepients()){
                         echo'
                            <script>
                                console.log("'.countRecepients().'");
                            </script>
                        ';
                        
                        $completed = true;
                        
                        $conn->beginTransaction();
                    
                        //code here to update tracking status
                        
                        $sql = "UPDATE tracking SET status = ? WHERE number = ?";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute(["completed", $_SESSION["trackingNum"]]);
                        
                        if($result){
                            
                            $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["userID"]]);
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $recepient_name = $user["firstname"] . " " . $user["surname"] ;
                          
                            $desc = "Invitation Recipient: " . $recepient_name . " accepted the capstone defense invitation";
                                    
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
                            throw new Exception("Failed to update document tracking status of invitation to completed");
                        }
                    }
                    
                    else{
                        
                        $conn->beginTransaction();
                        
                        $sql = "UPDATE tracking SET status = ? WHERE number = ?";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute(["evaluating", $_SESSION["trackingNum"]]);
                        
                        if($result){
                            
                            $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["userID"]]);
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $recepient_name = $user["firstname"] . " " . $user["surname"] ;
                          
                            $desc = "Invitation Recipient: " . $recepient_name . " accepted the capstone defense invitation";
                                    
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
                            throw new Exception("Failed to update document tracking status of invitation to evaluating");
                        }
                        
                        
                        echo'
                            <script>
                                console.log("there are still remaining recepients left");
                            </script>
                        ';
                    }
                }
                
                
                                                
                updateLogs();
                
                unset($_POST["acceptBtn"]);
                
                echo '<script>
                            Swal.fire({
                                 title: "Success",
                                text: "Invitation Accepted!",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/answer_defense_invitation";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/answer_defense_invitation";
                                    }
                                });
                    </script>';
            }
            
            else{
                throw new Exception("Failed to accept invitation");
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
                                window.location.href = "/answer_defense_invitation";
                            }
                            
                            else if (result.isDismissed) {
                                 window.location.href = "/answer_defense_invitation";
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
        
        <title>Invitation Letter</title>
      
    </head>

    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
        

        <h1 class="section-title">Invitation Letter</h1>
        <p id="groupInfo" class="group-info">BSIT 3A-G1, Group 2, Business Analytics </p>
       
        <div id="infoContainer" class="semester-container">
            <p id="acadYear" class="semester-info">2023 - 2024 (2nd Semester)</p>
        </div>
        
        <div id="dropdown" class="semester-container" style="display: none;">
            <form action="" method="GET">
                <label for="options" style="font-size: 20px;">Select Recepient</label>
                
                <select name="options" id="options" class="semester-container" style="font-size: 20px; margin-top: 12px;" onchange="this.form.submit();" disabled>
                     <?php checkUser(); ?>
                </select>

            </form>
        </div>
      
        <div class="horizontal-container">
            <div class="left-side">
                <div class="comments-section">
                    <label for="comments">Comments</label>
                    <textarea id="comments" rows="4" placeholder="Student Comments..." disabled><?php getComment();?></textarea>
                </div>
            </div>
        
            <div id="file-display" class="right-side">
                <div class="file-section"> <!--change width ng file box here pag need -->
                    <label for="file-upload">File</label>
                    <div id="file-upload" class="file-upload">
                        <i class="fas fa-file-pdf" style="color: red; font-size: 35px;"></i>
                        <span id="fileName" >Open Invitation Letter</span>
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
        
        
        <?php getStatus(); ?>
        
        <div>
            <p class="document-tracking" id="trackingLabel"></p>
        </div>
        
    
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
            
            
            else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
             
                if(isset($_GET["options"])){
                    viewInvitations($_GET["options"]);
                    getTrackingNum();
                }
            }
        ?>
        
        <?php require 'footer.php'; ?>
    </body>
</html>


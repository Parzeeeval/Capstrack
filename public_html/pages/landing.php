<?php
    
    require "connection.php";


    function updateYear(){
        global $conn;
      
        try {
        //DATABASE INFO
        $host = 'localhost';
        $dbname = 'u354989168_capstrack_db1';
        $dbusername = 'u354989168_admin01'; 
        $dbpassword = '@Capstrack2024';
    
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            $acadyearID = $result["id"];
            
            $start_year = $result["start_year"];
            $end_year = $result["end_year"];
            
            $start_month = $result["start_month"];
            $end_month = $result["end_month"];
            
            $start_day = $result["start_day"];
            $end_day = $result["end_day"];
            
            
            $nextsem_year = $result["nextsem_year"];
            $nextsem_month = $result["nextsem_month"];
            $nextsem_day = $result["nextsem_day"];
            
            $mode = $result["mode"];
            
            if($mode == 1){ //Mode 1 meaning the default, which is 3rd year 2nd sem and 4th year 1st sem
                $curr_year = date("Y");
                $curr_month = ltrim(date("m"), "0");
        
                if ($end_year == $curr_year) {
                    if ($curr_month >= $end_month) {
                        
                        $new_start_year = $start_year + 1;
                        $new_end_year = $new_start_year + 1;
                        $new_nextsem_year = $new_start_year + 1;
                        
                        $sql = "INSERT INTO academic_year (start_year, start_month, start_day, end_year, end_month, end_day, nextsem_year, nextsem_month, nextsem_day, mode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$new_start_year, $start_month, $start_day, $new_end_year, $end_month, $end_day, $new_nextsem_year, $nextsem_month, $nextsem_day, $mode]);
                        
                        if ($result) {
                            echo '<script>
                                console.log("Successfully created a new academic year with start year of: ' . json_encode($new_start_year) . ', and end year of: ' . json_encode($new_end_year) . '");
                            </script>';
        
                            $new_acadYearID = $conn->lastInsertId();
                            $prev_acadYearID = $new_acadYearID - 1;
        
                            // Start a transaction for section and student updates
                            $conn->beginTransaction();
                            
    
                            $new_year = $curr_year + 1;
                            
                            $sql = "UPDATE sequence_tracker SET current_year = ?, last_sequence = ?";
                            $stmt = $conn->prepare($sql);
                            $verifySequence = $stmt->execute([$new_year, 1000]);
                            
                            if($verifySequence){
                                $sql = "SELECT * FROM sections WHERE academicYearID = ? AND yearLevel = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$prev_acadYearID, 3]);
                                $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($sections) >= 1) {
                                    foreach ($sections as $section) {
                                        $prev_sectionID = $section["sectionID"];
                                        $coordinator = $section["coordinatorID"];
                                        $courseID = $section["courseID"];
                                        $sec_letter = $section["section_letter"];
                                        $sec_group = $section["section_group"];
                                        $specialization = $section["specialization"];
            
                                        // Insert new section
                                        $sql = "INSERT INTO sections (coordinatorID, courseID, yearLevel, section_letter, section_group, specialization, academicYearID, semester)
                                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                                        $stmt = $conn->prepare($sql);
                                        $verify1 = $stmt->execute([$coordinator, $courseID, 4, $sec_letter, $sec_group, $specialization, $new_acadYearID, 1]);
            
                                        if ($verify1) {
                                            $new_sectionID = $conn->lastInsertId();
            
                                            $sql = "INSERT INTO coordinators (facultyID, sectionID, academicYearID, semester) VALUES(?, ?, ?, ?)";
                                            $stmt = $conn->prepare($sql);
                                            $adviseVerify = $stmt->execute([$coordinator, $new_sectionID, $new_acadYearID, 1]);
                                            
                                            if($adviseVerify){
                                        
                                                // Fetch students in the previous section
                                                $sql = "SELECT * FROM students WHERE sectionID = ?";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->execute([$prev_sectionID]);
                                                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                                foreach ($students as $student) {
                                                    // Update each student with the new section ID
                                                    $sql = "UPDATE students SET new_sectionID = ? WHERE sectionID = ?";
                                                    $stmt = $conn->prepare($sql);
                                                    $verify2 = $stmt->execute([$new_sectionID, $prev_sectionID]);
                                        
                                                    if ($verify2) {
                                                        echo '
                                                            <script>
                                                                console.log("Sections and students updated");
                                                            </script>
                                                        ';
                                                    }
                                                    
                                                    else{
                                                        throw new Exception("Error updating student section ID");
                                                    }
                                                }
                                            }
                                            
                                            else{
                                                throw new Exception("Error inserting coordinator");
                                            }
                                        } 
                                        
                                        else {
                                            throw new Exception("Error inserting new section");
                                        }
                                    }
                                }
                            }
                            
                            else {
                                throw new Exception("Failed to update last_sequence table");
                            }
                            
                            // Commit transaction after all updates
                            $conn->commit();
                            
                            updateProjects();
    
        
                        } 
                        
                        else {
                            throw new Exception("Error in creating a new academic year");
                        }
                    } 
                } 
                
                else {
                    echo '<script>';
                        echo 'console.log("The CURRENT YEAR is still the same as the STARTING YEAR");';
                    echo '</script>';
                }
            }
            
            
            else if($mode == 2){ //Mode 2 meaning 3rd year 1st sem and 3rd year 2nd sem
            
                $curr_year = date("Y");
                $curr_month = ltrim(date("m"), "0");
                $curr_day = ltrim(date("d"), "0");
                
               
                if($curr_year >= $end_year){
                    if($curr_month >= $end_month && $curr_day >= $end_day){
                        
                        $new_start_year = $start_year + 1;
                        $new_end_year = $new_start_year + 1;
                        
                        $new_nextsem_year = 0;
                        
                        if($end_year == $start_year && $end_year == $nextsem_year){
                            $new_nextsem_year = $start_year + 1;  //change the logic
                        }
                        
                        else{
                            $new_nextsem_year = $new_start_year + 1;
                        }
                        
                        $conn->beginTransaction();
                        
                        
                        $sql = "INSERT INTO academic_year (start_year, start_month, start_day, end_year, end_month, end_day, nextsem_year, nextsem_month, nextsem_day, mode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$new_start_year, $start_month, $start_day, $new_end_year, $end_month, $end_day, $new_nextsem_year, $nextsem_month, $nextsem_day, $mode]);
                        
                        if ($result) {
                            
                            $new_acadYearID = $conn->lastInsertId();
                            $prev_acadYearID = $new_acadYearID - 1;

                            $new_year = $curr_year + 1;
                            
                            $sql = "UPDATE sequence_tracker SET current_year = ?, last_sequence = ?";
                            $stmt = $conn->prepare($sql);
                            $verifySequence = $stmt->execute([$new_year, 1000]);
                            
                            if($verifySequence){
                                $conn->commit();
                                
                                echo '<script>
                                    console.log("Successfully created a new academic year with start year of: ' . json_encode($new_start_year) . ', and end year of: ' . json_encode($new_end_year) . '");
                                </script>';
                            }
                        }
                    }
                    
                    else{
                         if($curr_year >= $nextsem_year){
                            if($curr_month >= $nextsem_month && $curr_day >= $nextsem_day){
                                
                                $conn->beginTransaction();
                  
                                $sql = "SELECT * FROM sections WHERE academicYearID = ? AND yearLevel = ? AND semester = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$acadyearID, 3, 1]);
                                $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($sections) >= 1) {
                                    
                                    $sql = "UPDATE sections SET semester = ? WHERE yearLevel = ? AND semester = ? AND academicYearID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $verify = $stmt->execute([2, 3, 1, $acadyearID]);
                        
                                    if ($verify) {
                                        
                                        $conn->commit();
                                        
                                        echo '
                                            <script>
                                                console.log("Mode 2 (NEXT SEMESTER) - (3rd year 1st sem and 3rd year 2nd sem) Succesful! 3rd year semesters updated to 2nd");
                                            </script>
                                        ';
                                    }
                                    
                                    else{
                                        throw new Exception("Error updating semester of 3rd years");
                                    }
                                }
                                
                                else{
                                    echo '
                                        <script>
                                            console.log("Mode 2 - (3rd year 1st sem and 3rd year 2nd sem) No 3rd year sections with 1st semester has been found");
                                            console.log("Mode 2 - (3rd year 1st sem and 3rd year 2nd sem) Meaning that all current 3rd year sections are in 2nd semester");
                                        </script>
                                    ';
                                }
                            }
                        
                            else{
                                 echo '
                                    <script>
                                        console.log("Current date is still for 1st semester");
                                    </script>
                                ';
                            }
                        }
                    }
                }
                
                else{
                    if($curr_year >= $nextsem_year){
                        if($curr_month >= $nextsem_month && $curr_day >= $nextsem_day){
                        
                            $conn->beginTransaction();
              
                            $sql = "SELECT * FROM sections WHERE academicYearID = ? AND yearLevel = ? AND semester = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$acadyearID, 3, 1]);
                            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (count($sections) >= 1) {
                                
                                $sql = "UPDATE sections SET semester = ? WHERE yearLevel = ? AND semester = ? AND academicYearID = ?";
                                $stmt = $conn->prepare($sql);
                                $verify = $stmt->execute([2, 3, 1, $acadyearID]);
                    
                                if ($verify) {
                         
                                    $conn->commit();
                                    
                                    echo '
                                        <script>
                                            console.log("Mode 2 (NEXT SEMESTER) - (3rd year 1st sem and 3rd year 2nd sem) Succesful! 3rd year semesters updated to 2nd");
                                        </script>
                                    ';
                                }
                                
                                else{
                                    throw new Exception("Error updating semester of 3rd years");
                                }
                                
                                        
                            }
                            
                            else{
                                echo '
                                    <script>
                                        console.log("Mode 2 - (3rd year 1st sem and 3rd year 2nd sem) No 3rd year sections with 1st semester has been found");
                                        console.log("Mode 2 - (3rd year 1st sem and 3rd year 2nd sem) Meaning that all current 3rd year sections are in 2nd semester");
                                    </script>
                                ';
                            }
                        }
                    
                        else{
                             echo '
                                <script>
                                    console.log("Current date is still for 1st semester");
                                </script>
                            ';
                        }
                    }
                }
            }
            
            else if($mode == 3){ //Mode 3 meaning 4th year 1st sem and 4th year 2nd sem
            
                $curr_year = date("Y");
                $curr_month = ltrim(date("m"), "0");
                $curr_day = ltrim(date("d"), "0");
                
               
                if($curr_year >= $end_year){
                    if($curr_month >= $end_month && $curr_day >= $end_day){
                        
                        $new_start_year = $start_year + 1;
                        $new_end_year = $new_start_year + 1;
                        
                        $new_nextsem_year = 0;
                        
                        if($end_year == $start_year && $end_year == $nextsem_year){
                            $new_nextsem_year = $start_year + 1;  //change the logic
                        }
                        
                        else{
                            $new_nextsem_year = $new_start_year + 1;
                        }
                        
                        $conn->beginTransaction();
                        
                        
                        $sql = "INSERT INTO academic_year (start_year, start_month, start_day, end_year, end_month, end_day, nextsem_year, nextsem_month, nextsem_day, mode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$new_start_year, $start_month, $start_day, $new_end_year, $end_month, $end_day, $new_nextsem_year, $nextsem_month, $nextsem_day, $mode]);
                        
                        if ($result) {
                            
                            $new_acadYearID = $conn->lastInsertId();
                            $prev_acadYearID = $new_acadYearID - 1;

                            $new_year = $curr_year + 1;
                            
                            $sql = "UPDATE sequence_tracker SET current_year = ?, last_sequence = ?";
                            $stmt = $conn->prepare($sql);
                            $verifySequence = $stmt->execute([$new_year, 1000]);
                            
                            if($verifySequence){
                                $conn->commit();
                                
                                echo '<script>
                                    console.log("Successfully created a new academic year with start year of: ' . json_encode($new_start_year) . ', and end year of: ' . json_encode($new_end_year) . '");
                                </script>';
                            }
                        }
                    }
                    
                    else{
                         if($curr_year >= $nextsem_year){
                            if($curr_month >= $nextsem_month && $curr_day >= $nextsem_day){
                                
                                $conn->beginTransaction();
                  
                                $sql = "SELECT * FROM sections WHERE academicYearID = ? AND yearLevel = ? AND semester = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$acadyearID, 4, 1]);
                                $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($sections) >= 1) {
                                    
                                    $sql = "UPDATE sections SET semester = ? WHERE yearLevel = ? AND semester = ? AND academicYearID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $verify = $stmt->execute([2, 4, 1, $acadyearID]);
                        
                                    if ($verify) {
                                        
                                        $conn->commit();
                                        
                                        echo '
                                            <script>
                                                console.log("Mode 3 (NEXT SEMESTER) - (4th year 1st sem and 4th year 2nd sem) Succesful! 4th year semesters updated to 2nd");
                                            </script>
                                        ';
                                    }
                                    
                                    else{
                                        throw new Exception("Error updating semester of 3rd years");
                                    }
                                }
                                
                                else{
                                    echo '
                                        <script>
                                            console.log("Mode 3 - (4th year 1st sem and 4th year 2nd sem) No 4th year sections with 1st semester has been found");
                                            console.log("Mode 3 - (4th year 1st sem and 4th year 2nd sem) Meaning that all current 4th year sections are in 2nd semester");
                                        </script>
                                    ';
                                }
                            }
                        
                            else{
                                 echo '
                                    <script>
                                        console.log("Current date is still for 1st semester");
                                    </script>
                                ';
                            }
                        }
                    }
                }
                
                else{
                    if($curr_year >= $nextsem_year){
                        if($curr_month >= $nextsem_month && $curr_day >= $nextsem_day){
                        
                            $conn->beginTransaction();
              
                            $sql = "SELECT * FROM sections WHERE academicYearID = ? AND yearLevel = ? AND semester = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$acadyearID, 4, 1]);
                            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (count($sections) >= 1) {
                                
                                $sql = "UPDATE sections SET semester = ? WHERE yearLevel = ? AND semester = ? AND academicYearID = ?";
                                $stmt = $conn->prepare($sql);
                                $verify = $stmt->execute([2, 4, 1, $acadyearID]);
                    
                                if ($verify) {
                         
                                    $conn->commit();
                                    
                                    echo '
                                        <script>
                                            console.log("Mode 3 (NEXT SEMESTER) - (4th year 1st sem and 4th year 2nd sem) Succesful! 4th year semesters updated to 2nd");
                                        </script>
                                    ';
                                }
                                
                                else{
                                    throw new Exception("Error updating semester of 3rd years");
                                }
                                
                                        
                            }
                            
                            else{
                                echo '
                                    <script>
                                        console.log("Mode 3 - (4th year 1st sem and 4th year 2nd sem) No 4th year sections with 1st semester has been found");
                                        console.log("Mode 3 - (4th year 1st sem and 4th year 2nd sem) Meaning that all current 4th year sections are in 2nd semester");
                                    </script>
                                ';
                            }
                        }
                    
                        else{
                             echo '
                                <script>
                                    console.log("Current date is still for 1st semester");
                                </script>
                            ';
                        }
                    }
                }
            }
        }
        
        else {
            throw new Exception("Error getting academic year from database");
        }
    } 
     
        
        catch (Exception $e) {
            // Rollback transaction if an error occurs
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            
            echo '<script>';
                echo 'console.log("Error: ' . addslashes($e->getMessage()) . '");';
            echo '</script>';
        }
    }
    
    
    function generateTrackingNumber(){
        // Get today's date in YYYYMMDD format
        $date = date('Ymd');
    
        // Generate a 16-character random string
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $randomString = '';
        for ($i = 0; $i < 16; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
    
        // Combine date and random string to create tracking number
        $trackingNumber = $date . $randomString;
    
        return $trackingNumber;
    }
    
    // Function to check if the tracking number already exists
    function trackingNumberExists($conn, $trackingNumber) {

        $sql = "SELECT COUNT(*) FROM tracking WHERE number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$trackingNumber]);
        return $stmt->fetchColumn() > 0; // Returns true if it exists
    }
    
    function updateProjects(){
        global $conn;
        
        try{
            $conn->beginTransaction();
        
            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($result) {
                $new_acadyear = $result["id"];
                $prev_acadyear = $new_acadyear - 1;
                
                $sql = "SELECT * FROM capstone_projects WHERE academicYearID = ?";
                $stmt = $conn->prepare($sql); 
                $stmt->execute([$prev_acadyear]);
                $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

                
                if(count ($groups) >= 1){
                    
                    foreach($groups as $group){
                        $projectID = $group["projectID"];
                        $groupNum = $group["groupNum"];
                        $coordinator = $group["coordinatorID"];
                        $title = $group["title"];
                        $status = $group["status"];
                        //$defense = $groups["defense"];
                        
                        $sql = "SELECT * FROM students WHERE projectID = ? LIMIT 1";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$projectID]);
                        $getStudent = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if($getStudent){
                            $newSectionID = $getStudent["new_sectionID"];
                            
                            $sql = "INSERT INTO capstone_projects (sectionID, groupNum, academicYearID, coordinatorID, title, status, defense) VALUES(?, ?, ?, ?, ?, ?, ?)";
                            
                            $stmt = $conn->prepare($sql);
                            $verify1 = $stmt->execute([ $newSectionID, $groupNum, $new_acadyear, $coordinator, $title, $status, "pending"]);
                            
                            if($verify1){
                                $prevprojectID = $projectID;
                                $newProjectID = $conn->lastInsertId();
                                
                                
                                $sql = "SELECT * FROM tasks";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if($result){
                                    
                                    foreach($result as $task){
                                         // Prepare your SQL statement for insertion
                                        $sql = "INSERT INTO tracking (projectID, taskID, academicYearID, status, number) VALUES (?, ?, ?, ?, ?)";
                                        $stmt = $conn->prepare($sql);
                                        
                                        // Generate a unique tracking number
                                        do {
                                            $trackingNumber = generateTrackingNumber();
                                        } while (trackingNumberExists($conn, $trackingNumber));
                                        
                                        // Execute the insert
                                        $result = $stmt->execute([$newProjectID, $task["id"], $new_acadyear, "started", $trackingNumber]);
                                         
                                         if($result){
                                             //continue as the foreach loop not finished yet
                                         }
                                         
                                         else{
                                             throw new Exception("Error inserting values in tracking number");
                                             break;
                                         }
                                    }
                                }
                                
                                $sql = "SELECT * FROM students WHERE projectID = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$prevprojectID]);
                                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if(count ($students) >= 1){
                                    foreach($students as $student){
                                        
                                        $sql = "UPDATE students SET new_projectID = ? WHERE projectID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $verify2 = $stmt->execute([$newProjectID, $prevprojectID]);
                                        
                                        if(!$verify2){
                                             throw new Exception("Error updating student new projectID");
                                        }
                                    }
                                    
                                    $sql = "SELECT * FROM panelists WHERE projectID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute([$prevprojectID]);
                                    $panelists = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if(count ($panelists) >= 1){
                                        foreach($panelists as $panelist){
                                            $panelID = $panelist["panelistID"];
                                            $level = $panelist["level"];
                                            
                                            $sql = "INSERT INTO panelists (panelistID, projectID, level, academicYearID) VALUES(?, ?, ?, ?)";
                                            $stmt = $conn->prepare($sql);
                                            $verify3 = $stmt->execute([$panelID, $newProjectID, $level, $new_acadyear]);
                                            
                                            if(!$verify3){
                                                 throw new Exception("Error inserting panelists with new projectID");
                                            }
                                            
                                        }
                                        
                                        
                                        
                                        $sql = "SELECT * FROM advisers WHERE projectID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute([$prevprojectID]);
                                        $advisers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if(count ($advisers) >= 1){
                                        
                                            foreach($advisers  as $adviser){
                                                $adviserID = $adviser["adviserID"];
        
                                                $sql = "INSERT INTO advisers (adviserID, projectID, academicYearID) VALUES(?, ?, ?)";
                                                $stmt = $conn->prepare($sql);
                                                $verify4 = $stmt->execute([$adviserID, $newProjectID, $new_acadyear]);
                                                
                                                if(!$verify4){
                                                     throw new Exception("Error inserting advisers with new projectID");
                                                }
                                                
                                            }
                                        }
                                        
                                     }
                                }
                            }
                            
                            
                        }
                        
                    }
                }
                
                
                $conn->commit();
            }
        }
        
        
        catch (Exception $e) {
            // Rollback transaction if an error occurs
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            echo '<script>';
                echo 'console.log("Error updating capstone projects: ' . addslashes($e->getMessage()) . '");';
            echo '</script>';
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="pages/landing.css">
    
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&ampdisplay=swap"
    />
    
    <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
  </head>
  
  <body>
    <header class="header">
      <nav>
        <div class="nav__bar">
          <div class="logo">
            <a href="#"><img src="pages/assets/logo.png" alt="logo" /></a>
          </div>
          <div class="p">
            <p>CAPSTRACK</p>
            
            <form action="" method="POST">
                <button type="submit" hidden disabled>Update Acad Year</button>
            </form>
            
          </div>
        </div>
        <ul class="nav__links" id="nav-links"></ul>
         <a href="login" class="btn nav__btn">Sign in</a>
      </nav>
      <div class="section__container header__container" id="home">
        <h1>Explore a world of documents at your fingertips.</h1>
        <div class="tracking-container">
          <div class="input-icon">
            <i class="ri-map-pin-line"></i>
            <form action="/tracking_number" method="POST">
                    <input name="trackingNum"
                      type="text"
                      placeholder="Enter the document tracking number"
                      class="tracking-input"/>
                  </div>
                  <button name="track-btn" class="btn tracking-btn">Track Document</button>
             </form>
        </div>
      </div>
    </header>

    <section class="section__container feature__container">
      <p class="section__subheader">capstrack</p>
      <h2 class="section__header">Main Features</h2>
      <div class="feature__grid">
        <div class="feature__card">
          <div class="feature__card__image">
            <img src="pages/assets/feature1.png" alt="feature" />
          </div>
          <div class="feature__card__details">
            <h4>Document Tracker</h4>
            <p>
              Track the progress of your capstone documents from start to finish.
              Stay updated on deadlines and milestones effortlessly.
            </p>
          </div>
        </div>

        <div class="feature__card">
          <div class="feature__card__image">
            <img src="pages/assets/feature2.png" alt="feature" />
          </div>
          <div class="feature__card__details">
            <h4>Record Organizer</h4>
            <p>
              Efficiently organize your capstone research, documents, and
              references. Access everything you need in one centralized
              location.
            </p>
          </div>
        </div>

        <div class="feature__card">
          <div class="feature__card__image">
            <img src="pages/assets/feature3.png" alt="feature" />
          </div>
          <div class="feature__card__details">
            <h4>Document Retrieval</h4>
            <p>
              Quickly retrieve any document or resource you need for your
              capstone project. Say goodbye to endless searching and wasting
              time.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="section__container about__container" id="about">
      <div class="about__image">
        <img src="pages/assets/collab.png" alt="about" />
      </div>

      <div class="about__content">
        <p class="section__subheader">ABOUT US</p>
        <h2 class="section__header">Simplify Collaboration!</h2>
        <p class="section__description">
          Collaborate seamlessly with your peers and coordinator, ensuring smooth
          progress and feedback loops.
        </p>

        <div class="about__btn">
          <button class="btn">Collaborate Now</button>
        </div>
      </div>
    </section>

    <section class="background-wrapper">
      <div class="section__container2 about__container2" id="about">
        <div class="about__image2">
          <img src="pages/assets/progress.png" alt="about2" />
        </div>
        <div class="about__content2">
          <h2 class="section__header2">Track Progress Effortlessly!</h2>
          <p class="section__description2">
            Never lose sight of your capstone's progress again. Our intuitive
            progress tracking tools empower you to monitor every milestone,
            revision, and deadline effortlessly.
          </p>
          <div class="about__btn2">
            <button class="btn2">Get Started</button>
          </div>
        </div>
      </div>
    </section>

    <section class="contact" id="contact">
      <div class="contact__bg">
        <div class="logo2">
          <a href="#"><img src="pages/assets/logo.png" alt="logo2" /></a>
          <h1>Efficiently Track, Organize, and Analyze Your Capstone Journey</h1>
          <p class="subheading">
            Whether you're a student, faculty member, or administrator, Capstrack
            streamlines the process of tracking, organizing, and analyzing
            capstone project data.
          </p>
        </div>
        <div class="Contactus">
          <h1>Contact Us</h1>
        </div>
        <div class="contact__content">
          <h4>Contact Us!</h4>
        
          <input
            type="text"
            placeholder="capstrack.bulsu.cict@capstrack.tech"
            class="tracking-input2"
          
         
       
        </div>
      </div>
    </section>

    <footer class="footer" id="contact">
      <div class="section__container footer__container">
        <div class="footer__col">
          <h4>Our Mission</h4>
          <p class="section__description">
            To provide students, faculty, and administrators with an intuitive
            platform that simplifies the tracking, organization, and analysis of
            project data.
          </p>
        </div>
        <div class="footer__col">
          <h4>Contact Us</h4>
          <ul class="footer__links">
            <li><a href="#">Phone: +63 (123) 456 7890</a></li>
            <li><a href="#">Address: MacArthur Highway, Brgy. Guinhawa</a></li>
            <li><a href="#">Email: Capstrack@gmail.com</a></li>
          </ul>
        </div>
        <div class="footer__col">
          <h4>Our Services</h4>
          <ul class="footer__links">
            <li><a href="#">Document Tracking</a></li>
            <li><a href="#">Document Management</a></li>
            <li><a href="#">Collaboration Tools</a></li>
            <li><a href="#">Reporting Tools</a></li>
          </ul>
        </div>
        </div>
      <div class="footer__bar">
        Copyright © 2024 CapsTrack. All rights reserved.
      </div>
      <img src="pages/assets/nbnb.png" class="overlay-image" alt="descriptive text" />
    </footer>
    
    <script src="pages/landing.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    
    
    <?php 
         if ($_SERVER["REQUEST_METHOD"] == "POST") {
           updateYear();
        }
    ?>
    
    
  </body>
</html>

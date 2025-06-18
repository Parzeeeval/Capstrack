<?php

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
                    if ($curr_month > $end_month || ($curr_month == $end_month && $curr_day >= $end_day)) {
                        
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
                                
                                $sql = "UPDATE capstone_projects SET status = ? WHERE academicYearID = ?";
                                $stmt = $conn->prepare($sql);
                                $verifySequence = $stmt->execute("finished", $acadyearID);
                                
                                if($verifySequence){
                                    $conn->commit();
                                    
                                    echo '<script>
                                        console.log("Successfully created a new academic year with start year of: ' . json_encode($new_start_year) . ', and end year of: ' . json_encode($new_end_year) . '");
                                    </script>';
                                }
                                
                                else{
                                    throw new Exception("Error updating status of capstone projects to finished");
                                }
                            }
                        }
                    }
                    
                    else{
                         if($curr_year >= $nextsem_year){
                            if ($curr_month > $nextsem_month || ($curr_month == $nextsem_month && $curr_day >= $nextsem_day)) {
                                
                                $conn->beginTransaction();
                  
                                $sql = "SELECT * FROM sections WHERE academicYearID = ? AND yearLevel = ? AND semester = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$acadyearID, 3, 1]);
                                $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($sections) >= 1) {
                                    
                                    $sql = "SELECT projectID FROM capstone_projects WHERE academicYearID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute([$acadYearID]);
                                    $id_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach($id_list as $id){
                                        $sql = "SELECT * FROM invitations WHERE projectID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute([$id["projectID"]]);
                                        $invitations= $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        foreach ($invitations as $invitation) {
                                        
                                            if ($invitations && isset($invitation["filepath"])) {
                                                 $inv_filepath = $invitation["filepath"];
                                            
                                                 $sql = "INSERT INTO previous_documents (projectID, filepath) VALUES(?, ?)";
                                                 $stmt = $conn->prepare($sql);
                                                 $inv_verify = $stmt->execute([$id["projectID"], $inv_filepath]);
                                                
                                                 if(!$inv_verify){
                                                    throw new Exception("Failed to archive previous invitation files");
                                                 }
                                            }
                                            
                                            else{
                                                //nothing for now
                                            }
                                            
                                        }
                                        
                                        
                                        
                                        $sql = "SELECT * FROM capstone_papers WHERE projectID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute([$id["projectID"]]);
                                        $paper = $stmt->fetch(PDO::FETCH_ASSOC);
                                        
                                        if ($paper && isset($paper["filepath"])) {
                                            
                                            $paper_filepath = $paper["filepath"];
                                            
                                            $sql = "INSERT INTO previous_documents (projectID, filepath) VALUES(?, ?)";
                                            $stmt = $conn->prepare($sql);
                                            $paper_verify = $stmt->execute([$id["projectID"], $paper_filepath]);
                                            
                                            if(!$paper_verify){
                                                throw new Exception("Failed to archive previous capstone paper file");
                                            }
                                        } 
                                        
                                        else {
                                            //nothing for now
                                        }
                                        
                                        
                                        $stmt = $conn->prepare("DELETE FROM invitations WHERE projectID = ?");
                                        $inv_del_result= $stmt->execute([$id["projectID"]]);

                                        if(!$inv_del_result){
                                            throw new Exception("Failed reset invitation records");
                                        }
                                        
                                        $stmt = $conn->prepare("DELETE FROM capstone_papers WHERE projectID = ?");
                                        $paper_del_result= $stmt->execute([$id["projectID"]]);

                                        if(!$paper_del_result){
                                            throw new Exception("Failed reset capstone paper records");
                                        }
                                        
                                        
                                        $stmt = $conn->prepare("UPDATE tracking SET status = ? WHERE projectID = ? AND taskID <> '1'");
                                        $tracking_result= $stmt->execute(["started", $id["projectID"]]);
                                        
                                        if(!$tracking_result){
                                            throw new Exception("Failed to reset tracking statuses");
                                        }
                                        
                                        $stmt = $conn->prepare("DELETE FROM activity_logs WHERE projectID = ? AND taskID <> '1'");
                                        $logs_result= $stmt->execute([$id["projectID"]]);
                                        
                                        if(!$logs_result){
                                            throw new Exception("Failed to reset activity logs");
                                        }
                                        
                                        $stmt = $conn->prepare("DELETE FROM defense_answers WHERE projectID = ?");
                                        $def_result= $stmt->execute([$id["projectID"]]);
                                        
                                        if(!$def_result){
                                            throw new Exception("Failed to reset defense answers");
                                        }
                                        
                                        $stmt = $conn->prepare("DELETE FROM defense_dates WHERE projectID = ?");
                                        $date_result= $stmt->execute([$id["projectID"]]);
                                        
                                        if(!$date_result){
                                            throw new Exception("Failed to reset defense dates");
                                        }
                                    }
                                    
                                    
                                    
                                    
                                    $sql = "UPDATE sections SET semester = ? WHERE yearLevel = ? AND semester = ? AND academicYearID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $verify = $stmt->execute([2, 3, 1, $acadyearID]);
                        
                                    if ($verify) {
                                        
                                            $sql = "UPDATE capstone_projects SET defense = ? WHERE academicYearID = ?";
                                            $stmt = $conn->prepare($sql);
                                            $verifySequence = $stmt->execute(["pending", $acadyearID]);
                                            
                                            if($verifySequence){
                                                $conn->commit();
                                                
                                                echo '
                                                    <script>
                                                        console.log("Mode 2 (NEXT SEMESTER) - (3rd year 1st sem and 3rd year 2nd sem) Succesful! 3rd year semesters updated to 2nd");
                                                    </script>
                                                ';
                                            }
                                            
                                            else{
                                                throw new Exception("Error updating defense of 3rd years back to pending");
                                            }
                                    }
                                    
                                    else{
                                        throw new Exception("Error updating semester of 3rd years");
                                    }
                                }
                                
                                else{
                                    echo '
                                        <script>
                                            console.log("Mode 2 - (3rd year 1st sem and 3rd year 2nd sem) No 3rd year sections with 1st semester has been found");
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
                        if ($curr_month > $nextsem_month || ($curr_month == $nextsem_month && $curr_day >= $nextsem_day)) {
                        
                            $conn->beginTransaction();
              
                            $sql = "SELECT * FROM sections WHERE academicYearID = ? AND yearLevel = ? AND semester = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$acadyearID, 3, 1]);
                            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                            if (count($sections) >= 1) {
                                
                                $sql = "SELECT projectID FROM capstone_projects WHERE academicYearID = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$acadYearID]);
                                $id_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach($id_list as $id){
                                    $sql = "SELECT * FROM invitations WHERE projectID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute([$id["projectID"]]);
                                    $invitations= $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($invitations as $invitation) {
                                    
                                        if ($invitations && isset($invitation["filepath"])) {
                                             $inv_filepath = $invitation["filepath"];
                                        
                                             $sql = "INSERT INTO previous_documents (projectID, filepath) VALUES(?, ?)";
                                             $stmt = $conn->prepare($sql);
                                             $inv_verify = $stmt->execute([$id["projectID"], $inv_filepath]);
                                            
                                             if(!$inv_verify){
                                                throw new Exception("Failed to archive previous invitation files");
                                             }
                                        }
                                        
                                        else{
                                            //nothing for now
                                        }
                                        
                                    }
                                    
                                    $sql = "SELECT * FROM capstone_papers WHERE projectID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute([$id["projectID"]]);
                                    $paper = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if ($paper && isset($paper["filepath"])) {
                                        
                                        $paper_filepath = $paper["filepath"];
                                        
                                        $sql = "INSERT INTO previous_documents (projectID, filepath) VALUES(?, ?)";
                                        $stmt = $conn->prepare($sql);
                                        $paper_verify = $stmt->execute([$id["projectID"], $paper_filepath]);
                                        
                                        if(!$paper_verify){
                                            throw new Exception("Failed to archive previous capstone paper file");
                                        }
                                    } 
                                    
                                    else {
                                        //nothing for now
                                    }
                                    
                                    
                                    
                                    $stmt = $conn->prepare("DELETE FROM invitations WHERE projectID = ?");
                                    $inv_del_result= $stmt->execute([$id["projectID"]]);

                                    if(!$inv_del_result){
                                        throw new Exception("Failed reset invitation records");
                                    }
                                    
                                    $stmt = $conn->prepare("DELETE FROM capstone_papers WHERE projectID = ?");
                                    $paper_del_result= $stmt->execute([$id["projectID"]]);

                                    if(!$paper_del_result){
                                        throw new Exception("Failed reset capstone paper records");
                                    }
                                    
                                    
                                    $stmt = $conn->prepare("UPDATE tracking SET status = ? WHERE projectID = ? AND taskID <> '1'");
                                    $tracking_result= $stmt->execute(["started", $id["projectID"]]);
                                    
                                    if(!$tracking_result){
                                        throw new Exception("Failed to reset tracking statuses");
                                    }
                                    
                                    $stmt = $conn->prepare("DELETE FROM activity_logs WHERE projectID = ? AND taskID <> '1'");
                                    $logs_result= $stmt->execute([$id["projectID"]]);
                                    
                                    if(!$logs_result){
                                        throw new Exception("Failed to reset activity logs");
                                    }
                                    
                                    $stmt = $conn->prepare("DELETE FROM defense_answers WHERE projectID = ?");
                                    $def_result= $stmt->execute([$id["projectID"]]);
                                    
                                    if(!$def_result){
                                        throw new Exception("Failed to reset defense answers");
                                    }
                                    
                                    $stmt = $conn->prepare("DELETE FROM defense_dates WHERE projectID = ?");
                                    $date_result= $stmt->execute([$id["projectID"]]);
                                    
                                    if(!$date_result){
                                        throw new Exception("Failed to reset defense dates");
                                    }
                                }
                                
                                $sql = "UPDATE sections SET semester = ? WHERE yearLevel = ? AND semester = ? AND academicYearID = ?";
                                $stmt = $conn->prepare($sql);
                                $verify = $stmt->execute([2, 3, 1, $acadyearID]);
                    
                                if ($verify) {
                         
                                    $sql = "UPDATE capstone_projects SET defense = ? WHERE academicYearID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $verifySequence = $stmt->execute(["pending", $acadyearID]);
                                    
                                    if($verifySequence){
                                        $conn->commit();
                                        
                                        echo '
                                            <script>
                                                console.log("Mode 2 (NEXT SEMESTER) - (3rd year 1st sem and 3rd year 2nd sem) Succesful! 3rd year semesters updated to 2nd");
                                            </script>
                                        ';
                                    }
                                    
                                    else{
                                        throw new Exception("Error updating defense of 3rd years back to pending");
                                    }
                                }
                                
                                else{
                                    throw new Exception("Error updating semester of 3rd years");
                                }
                                
                                        
                            }
                            
                            else{
                                echo '
                                    <script>
                                        console.log("Mode 2 - (3rd year 1st sem and 3rd year 2nd sem) No 3rd year sections with 1st semester has been found");
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
                    if ($curr_month > $end_month || ($curr_month == $end_month && $curr_day >= $end_day)) {
                        
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
                                
                                $sql = "UPDATE capstone_projects SET status = ? WHERE academicYearID = ?";
                                $stmt = $conn->prepare($sql);
                                $verifySequence = $stmt->execute("finished", $acadyearID);
                                
                                if($verifySequence){
                                    $conn->commit();
                                    
                                    echo '<script>
                                        console.log("Successfully created a new academic year with start year of: ' . json_encode($new_start_year) . ', and end year of: ' . json_encode($new_end_year) . '");
                                    </script>';
                                }
                                
                                else{
                                     throw new Exception("Error updating status of capstone projects to finished");
                                }
                            }
                        }
                    }
                    
                    else{
                         if($curr_year >= $nextsem_year){
                            if ($curr_month > $nextsem_month || ($curr_month == $nextsem_month && $curr_day >= $nextsem_day)) {
                                
                                $conn->beginTransaction();
                  
                                $sql = "SELECT * FROM sections WHERE academicYearID = ? AND yearLevel = ? AND semester = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$acadyearID, 4, 1]);
                                $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($sections) >= 1) {
                                    
                                    $sql = "SELECT projectID FROM capstone_projects WHERE academicYearID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute([$acadYearID]);
                                    $id_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach($id_list as $id){
                                        $sql = "SELECT * FROM invitations WHERE projectID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute([$id["projectID"]]);
                                        $invitations= $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        foreach ($invitations as $invitation) {
                                        
                                            if ($invitations && isset($invitation["filepath"])) {
                                                 $inv_filepath = $invitation["filepath"];
                                            
                                                 $sql = "INSERT INTO previous_documents (projectID, filepath) VALUES(?, ?)";
                                                 $stmt = $conn->prepare($sql);
                                                 $inv_verify = $stmt->execute([$id["projectID"], $inv_filepath]);
                                                
                                                 if(!$inv_verify){
                                                    throw new Exception("Failed to archive previous invitation files");
                                                 }
                                            }
                                            
                                            else{
                                                //nothing for now
                                            }
                                            
                                            $stmt = $conn->prepare("DELETE FROM invitations WHERE projectID = ?");
                                            $inv_del_result= $stmt->execute([$id["projectID"]]);
    
                                            if(!$inv_del_result){
                                                throw new Exception("Failed reset invitation records");
                                            }
                                            
                                            $stmt = $conn->prepare("DELETE FROM capstone_papers WHERE projectID = ?");
                                            $paper_del_result= $stmt->execute([$id["projectID"]]);
    
                                            if(!$paper_del_result){
                                                throw new Exception("Failed reset capstone paper records");
                                            }
                                            
                                            
                                            $stmt = $conn->prepare("UPDATE tracking SET status = ? WHERE projectID = ? AND taskID <> '1'");
                                            $tracking_result= $stmt->execute(["started", $id["projectID"]]);
                                            
                                            if(!$tracking_result){
                                                throw new Exception("Failed to reset tracking statuses");
                                            }
                                            
                                            $stmt = $conn->prepare("DELETE FROM activity_logs WHERE projectID = ? AND taskID <> '1'");
                                            $logs_result= $stmt->execute([$id["projectID"]]);
                                            
                                            if(!$logs_result){
                                                throw new Exception("Failed to reset activity logs");
                                            }
                                            
                                            $stmt = $conn->prepare("DELETE FROM defense_answers WHERE projectID = ?");
                                            $def_result= $stmt->execute([$id["projectID"]]);
                                            
                                            if(!$def_result){
                                                throw new Exception("Failed to reset defense answers");
                                            }
                                            
                                            $stmt = $conn->prepare("DELETE FROM defense_dates WHERE projectID = ?");
                                            $date_result= $stmt->execute([$id["projectID"]]);
                                            
                                            if(!$date_result){
                                                throw new Exception("Failed to reset defense dates");
                                            }
                                        
                                        }
                                        
                                        $sql = "SELECT * FROM capstone_papers WHERE projectID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute([$id["projectID"]]);
                                        $paper = $stmt->fetch(PDO::FETCH_ASSOC);
                                        
                                        if ($paper && isset($paper["filepath"])) {
                                            
                                            $paper_filepath = $paper["filepath"];
                                            
                                            $sql = "INSERT INTO previous_documents (projectID, filepath) VALUES(?, ?)";
                                            $stmt = $conn->prepare($sql);
                                            $paper_verify = $stmt->execute([$id["projectID"], $paper_filepath]);
                                            
                                            if(!$paper_verify){
                                                throw new Exception("Failed to archive previous capstone paper file");
                                            }
                                        } 
                                        
                                        else {
                                            //nothing for now
                                        }
                                    }
                                    
                                    $sql = "UPDATE sections SET semester = ? WHERE yearLevel = ? AND semester = ? AND academicYearID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $verify = $stmt->execute([2, 4, 1, $acadyearID]);
                        
                                    if ($verify) {
                                        
                                        $sql = "UPDATE capstone_projects SET defense = ? WHERE academicYearID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $verifySequence = $stmt->execute(["pending", $acadyearID]);
                                        
                                        if($verifySequence){
                                            
                                            $conn->commit();
                                            
                                            echo '
                                                <script>
                                                    console.log("Mode 3 (NEXT SEMESTER)  - (4th year 1st sem and 4th year 2nd sem) Succesful! 4th year semesters updated to 2nd");
                                                </script>
                                            ';
                                        }
                                        
                                        else{
                                            throw new Exception("Error updating defense of capstone projects back to pending");
                                        }
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
                        if ($curr_month > $nextsem_month || ($curr_month == $nextsem_month && $curr_day >= $nextsem_day)) {
                        
                            $conn->beginTransaction();
              
                            $sql = "SELECT * FROM sections WHERE academicYearID = ? AND yearLevel = ? AND semester = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$acadyearID, 4, 1]);
                            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($sections) >= 1) {
                                    
                                    $sql = "SELECT projectID FROM capstone_projects WHERE academicYearID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute([$acadYearID]);
                                    $id_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach($id_list as $id){
                                        $sql = "SELECT * FROM invitations WHERE projectID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute([$id["projectID"]]);
                                        $invitations= $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        foreach ($invitations as $invitation) {
                                        
                                            if ($invitations && isset($invitation["filepath"])) {
                                                 $inv_filepath = $invitation["filepath"];
                                            
                                                 $sql = "INSERT INTO previous_documents (projectID, filepath) VALUES(?, ?)";
                                                 $stmt = $conn->prepare($sql);
                                                 $inv_verify = $stmt->execute([$id["projectID"], $inv_filepath]);
                                                
                                                 if(!$inv_verify){
                                                    throw new Exception("Failed to archive previous invitation files");
                                                 }
                                            }
                                            
                                            else{
                                                //nothing for now
                                            }
                                            
                                            
                                            $stmt = $conn->prepare("DELETE FROM invitations WHERE projectID = ?");
                                            $inv_del_result= $stmt->execute([$id["projectID"]]);
    
                                            if(!$inv_del_result){
                                                throw new Exception("Failed reset invitation records");
                                            }
                                            
                                            $stmt = $conn->prepare("DELETE FROM capstone_papers WHERE projectID = ?");
                                            $paper_del_result= $stmt->execute([$id["projectID"]]);
    
                                            if(!$paper_del_result){
                                                throw new Exception("Failed reset capstone paper records");
                                            }
                                            
                                            
                                            $stmt = $conn->prepare("UPDATE tracking SET status = ? WHERE projectID = ? AND taskID <> '1'");
                                            $tracking_result= $stmt->execute(["started", $id["projectID"]]);
                                            
                                            if(!$tracking_result){
                                                throw new Exception("Failed to reset tracking statuses");
                                            }
                                            
                                            $stmt = $conn->prepare("DELETE FROM activity_logs WHERE projectID = ? AND taskID <> '1'");
                                            $logs_result= $stmt->execute([$id["projectID"]]);
                                            
                                            if(!$logs_result){
                                                throw new Exception("Failed to reset activity logs");
                                            }
                                            
                                            $stmt = $conn->prepare("DELETE FROM defense_answers WHERE projectID = ?");
                                            $def_result= $stmt->execute([$id["projectID"]]);
                                            
                                            if(!$def_result){
                                                throw new Exception("Failed to reset defense answers");
                                            }
                                            
                                            $stmt = $conn->prepare("DELETE FROM defense_dates WHERE projectID = ?");
                                            $date_result= $stmt->execute([$id["projectID"]]);
                                            
                                            if(!$date_result){
                                                throw new Exception("Failed to reset defense dates");
                                            }
                                            
                                        }
                                        
                                        $sql = "SELECT * FROM capstone_papers WHERE projectID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute([$id["projectID"]]);
                                        $paper = $stmt->fetch(PDO::FETCH_ASSOC);
                                        
                                        if ($paper && isset($paper["filepath"])) {
                                            
                                            $paper_filepath = $paper["filepath"];
                                            
                                            $sql = "INSERT INTO previous_documents (projectID, filepath) VALUES(?, ?)";
                                            $stmt = $conn->prepare($sql);
                                            $paper_verify = $stmt->execute([$id["projectID"], $paper_filepath]);
                                            
                                            if(!$paper_verify){
                                                throw new Exception("Failed to archive previous capstone paper file");
                                            }
                                        } 
                                        
                                        else {
                                            //nothing for now
                                        }
                                    }
                                
                                $sql = "UPDATE sections SET semester = ? WHERE yearLevel = ? AND semester = ? AND academicYearID = ?";
                                $stmt = $conn->prepare($sql);
                                $verify = $stmt->execute([2, 4, 1, $acadyearID]);
                    
                                if ($verify) {
                         
                                    $sql = "UPDATE capstone_projects SET defense = ? WHERE academicYearID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $verifySequence = $stmt->execute(["pending", $acadyearID]);
                                    
                                    if($verifySequence){
                                        
                                        $conn->commit();
                                        
                                        echo '
                                            <script>
                                                console.log("Mode 3 (NEXT SEMESTER)  - (4th year 1st sem and 4th year 2nd sem) Succesful! 4th year semesters updated to 2nd");
                                            </script>
                                        ';
                                    }
                                    
                                    else{
                                        throw new Exception("Error updating defense of capstone projects back to pending");
                                    }
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
        try{
            
            //DATABASE INFO
            $host = 'localhost';
            $dbname = 'u354989168_capstrack_db1';
            $dbusername = 'u354989168_admin01'; 
            $dbpassword = '@Capstrack2024';
        
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
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

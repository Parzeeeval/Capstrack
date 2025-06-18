<?php 
    session_start();
    require 'connection.php';
    
    
    function showSections() {
        global $conn;
        
        $specialization = $_SESSION["specialization"];

        $userID = $_SESSION["userID"];
        $courseID = $_SESSION["courseID"];
        
        
        
        $sql = "SELECT accessLevel from faculty WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $accessLevel = $result["accessLevel"];
        
        
        
        $sql = "SELECT adminID FROM courses WHERE courseID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$courseID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $adminID = $result["adminID"];


        if($accessLevel >= 2){
            
            $sql = "SELECT * FROM specializations WHERE courseID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$courseID]);
            
            $specializationCount = 0;
            
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $specializationCount++;
            }
            
            if($specializationCount >= 1){
                if($userID == $adminID){
                    $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            sections s
                        JOIN 
                            users u ON s.coordinatorID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                        WHERE 
                            s.specialization = ? AND s.courseID = ?";
            
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$specialization, $courseID]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                else{
                    //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                    $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            sections s
                        JOIN 
                            users u ON s.coordinatorID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                        WHERE
                            s.coordinatorID = ?
                        AND
                            s.specialization = ? AND s.courseID = ?";
            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $specialization, $courseID]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        
                    //For selecting sections in which the faculty member is an ADVISER for a capstone group        
                    $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            capstone_projects cp 
                            
                        JOIN
                            sections s ON cp.sectionID = s.sectionID
                            
                        JOIN
                            coordinators cr ON cr.facultyID = s.coordinatorID
                            
                        JOIN 
                            advisers ad ON cp.projectID = ad.projectID
                        
                        JOIN 
                            users u ON cr.facultyID = u.id
                            
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                            
                        WHERE
                            ad.adviserID = ?
                        AND
                            s.specialization = ? AND s.courseID = ?";
            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $specialization, $courseID]);
                        $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        $result = array_merge($result, $secondResult);  // Combine both results
                        
                        
                    //For selecting sections in which the faculty member is a PANELIST for a capstone group        
                    $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            capstone_projects cp 
                        JOIN
                            sections s ON cp.sectionID = s.sectionID
                            
                        JOIN
                            coordinators cr ON cr.facultyID = s.coordinatorID
                            
                        JOIN 
                            panelists ps ON cp.projectID = ps.projectID
                        
                        JOIN 
                            users u ON cr.facultyID = u.id
                            
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                            
                        WHERE
                            ps.panelistID = ?
                        AND
                            s.specialization = ? AND s.courseID = ?";
            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $specialization, $courseID]);
                        $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        $result = array_merge($result, $thirdResult);  // Combine both results
                        
                        $result = array_unique($result); //Removes any potential duplicated sections
                }
                
                
                
                if(count($result) <= 0){
                    echo '<h1> No sections found  </h1>';
                }
            }
            
             else if ($specializationCount <= 0){
                 if($userID == $adminID){
                     $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            sections s
                        JOIN 
                            users u ON s.coordinatorID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                        WHERE 
                             s.courseID = ?";
    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$courseID]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                
                else{
                    
                    //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                   $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            sections s
                        JOIN 
                            users u ON s.coordinatorID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                        WHERE
                            s.coordinatorID = ? AND s.courseID = ?";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                    
                    //For selecting sections in which the faculty member is an ADVISER for a capstone group   
                    $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            capstone_projects cp 
                            
                        JOIN
                            sections s ON cp.sectionID = s.sectionID
                            
                        JOIN
                            coordinators cr ON cr.facultyID = s.coordinatorID
                            
                        JOIN 
                            advisers ad ON cp.projectID = ad.projectID
                        
                        JOIN 
                            users u ON cr.facultyID = u.id
                            
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                            
                        WHERE
                            ad.adviserID = ? AND s.courseID = ?";
            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID]);
                        $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        $result = array_merge($result, $secondResult);  // Combine both results
                        
                    
                    //For selecting sections in which the faculty member is a PANELIST for a capstone group        
                    $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            capstone_projects cp 
                            
                        JOIN
                            sections s ON cp.sectionID = s.sectionID
                            
                        JOIN
                            coordinators cr ON cr.facultyID = s.coordinatorID
                            
                        JOIN 
                            panelists ps ON cp.projectID = ps.projectID
                        
                        JOIN 
                            users u ON cr.facultyID = u.id
                            
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                            
                        WHERE
                            ps.panelistID = ? AND s.courseID = ?";
            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID]);
                        $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        $result = array_merge($result, $thirdResult);  // Combine both results
                        
                        $result = array_unique($result); //Removes any potential duplicated sections
                    
                }
                
                if(count($result) <= 0){
                    echo '<h1> No sections found  </h1>';
                }
            }
            
        }

        else if ($accessLevel <= 1){
            $sql = "SELECT * FROM specializations WHERE courseID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$courseID]);
            
            $specializationCount = 0;
            
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $specializationCount++;
            }
            
             if($specializationCount >= 1){
                 
                 //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                 $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            sections s
                        JOIN 
                            users u ON s.coordinatorID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                        WHERE
                            s.coordinatorID = ?
                        AND
                            s.specialization = ? AND s.courseID = ?";
            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $specialization, $courseID]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                
                //For selecting sections in which the faculty member is an ADVISER for a capstone group        
                $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            capstone_projects cp 
                            
                        JOIN
                            sections s ON cp.sectionID = s.sectionID
                            
                        JOIN
                            coordinators cr ON cr.facultyID = s.coordinatorID
                            
                        JOIN 
                            advisers ad ON cp.projectID = ad.projectID
                        
                        JOIN 
                            users u ON cr.facultyID = u.id
                            
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                            
                        WHERE
                            ad.adviserID = ?
                        AND
                            s.specialization = ? AND s.courseID = ?";
            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $specialization, $courseID]);
                        $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        $result = array_merge($result, $secondResult);  // Combine both results
     
     
                //For selecting sections in which the faculty member is a PANELIST for a capstone group        
                $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            capstone_projects cp 
                            
                        JOIN
                            sections s ON cp.sectionID = s.sectionID
                            
                        JOIN
                            coordinators cr ON cr.facultyID = s.coordinatorID
                            
                        JOIN 
                            panelists ps ON cp.projectID = ps.projectID
                        
                        JOIN 
                            users u ON cr.facultyID = u.id
                            
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                            
                        WHERE
                            ps.panelistID = ?
                        AND
                            s.specialization = ? AND s.courseID = ?";
            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $specialization, $courseID]);
                        $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        $result = array_merge($result, $thirdResult);  // Combine both results
                        
                        $result = array_unique($result); //Removes any potential duplicated sections
            }
            
             else if ($specializationCount <= 0){
                 
                   //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                   $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            sections s
                        JOIN 
                            users u ON s.coordinatorID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                        WHERE
                            s.coordinatorID = ? AND s.courseID = ?";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                    
                    //For selecting sections in which the faculty member is an ADVISER for a capstone group   
                    $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            capstone_projects cp 
                            
                        JOIN
                            sections s ON cp.sectionID = s.sectionID
                            
                        JOIN
                            coordinators cr ON cr.facultyID = s.coordinatorID
                            
                        JOIN 
                            advisers ad ON cp.projectID = ad.projectID
                        
                        JOIN 
                            users u ON cr.facultyID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                            
                        WHERE
                            ad.adviserID = ? AND s.courseID = ?";
            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID]);
                        $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        $result = array_merge($result, $secondResult);  // Combine both results
                        
                    
                    //For selecting sections in which the faculty member is a PANELIST for a capstone group        
                    $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            capstone_projects cp 
                            
                        JOIN
                            sections s ON cp.sectionID = s.sectionID
                            
                        JOIN
                            coordinators cr ON cr.facultyID = s.coordinatorID
                            
                        JOIN 
                            panelists ps ON cp.projectID = ps.projectID
                        
                        JOIN 
                            users u ON cr.facultyID = u.id
                            
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                            
                        WHERE
                            ps.panelistID = ? AND s.courseID = ?";
            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID]);
                        $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        $result = array_merge($result, $thirdResult);  // Combine both results
                        
                        $result = array_unique($result); //Removes any potential duplicated sections
             }
        }
        
    
         if (count($result) >= 1) {
                foreach ($result as $row) {
                    $sectionID = $row["sectionID"];
                    $_SESSION["sectionID"] = $sectionID;
                    
                    $section = $row["sectionName"];
                    $acadYear = $row["start_year"] . "-" . $row["end_year"];
                    $acadYearValue = $row["academicYearID"];
                    $coordinator = $row["surname"] . ", " . $row["firstname"] . " " . $row["middlename"];
                    $coordinatorID = $row["coordinatorID"];
                    
                    // Escape values to prevent XSS issues
                    $escapedSectionID = addslashes($sectionID);
                    $escapedSection = addslashes($section);
                    $escapedAcadYear = addslashes($acadYear);
                    $escapedCoordinator = addslashes($coordinator);
                    $escapedCoordinatorID = addslashes($coordinatorID);
                    $escapedAcadYearValue = addslashes($acadYearValue);
            
                    echo "<script>
                        var container = document.getElementById('section-container'); 
                        container.insertAdjacentHTML('beforeend', `
                            <form action=\"/groups\" method=\"POST\">
                                <div class=\"card-container\" onclick=\"this.closest('form').submit();\">
                                    <input type=\"hidden\" name=\"sectionValue\" value=\"$escapedSectionID\">
                                    <input type=\"hidden\" name=\"coordinatorValue\" value=\"$escapedCoordinatorID\">
                                    <input type=\"hidden\" name=\"coordinatorNameValue\" value=\"$escapedCoordinator\">
                                    <input type=\"hidden\" name=\"acadYearValue\" value=\"$escapedAcadYearValue\">
                                    
                                    <div class=\"card\" style=\"background-color:#0096FF;\">
                                        <div class=\"options\">
                                            <div class=\"hamburger\"></div>
                                            <div class=\"dropdown\">
                                                <button class=\"dropdown-item\">Edit</button>
                                                <button class=\"dropdown-item\">Delete</button>
                                            </div>
                                        </div>
                                        <div class=\"folder-bottom\">
                                            <div class=\"circle\"></div>
                                        </div>
                                        <div class=\"course-content\">
                                            <h2>$escapedSection</h2>
                                            <p>$escapedAcadYear</p>
                                            <p>$escapedCoordinator</p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        `);
                    </script>";
            }
        }
        
        else {
            echo '<h1>No existing student sections yet for this specialization</h1>';
        }
    }
    
    function showThirdyears() {
        global $conn;
        
        $specialization = $_SESSION["specialization"];
        
        echo'
            <script>
                console.log("SPECIAL: '.$specialization.'");
            </script>
        ';
        
        try{
            $userID = $_SESSION["userID"];
            $courseID = $_SESSION["courseID"];
            
             echo'
                <script>
                    console.log("here 33");
                </script>
            ';
            
            
            $sql = "SELECT accessLevel from faculty WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            $accessLevel = $result["accessLevel"];
            
            
            
            $sql = "SELECT adminID FROM courses WHERE courseID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$courseID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $adminID = $result["adminID"];
    
    
            if($accessLevel >= 2){
                
                $sql = "SELECT * FROM specializations WHERE courseID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$courseID]);
                
                $specializationCount = 0;
                
                while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $specializationCount++;
                }
                
                if($specializationCount >= 1){
                    if($userID == $adminID){
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                sections s
                            JOIN 
                                users u ON s.coordinatorID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                            WHERE 
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 3";
                
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$specialization, $courseID]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    
                    else{
                        //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                sections s
                            JOIN 
                                users u ON s.coordinatorID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                            WHERE
                                s.coordinatorID = ?
                            AND
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 3";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            
                        //For selecting sections in which the faculty member is an ADVISER for a capstone group        
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                advisers ad ON cp.projectID = ad.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ad.adviserID = ?
                            AND
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 3";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
                            
                            
                        //For selecting sections in which the faculty member is a PANELIST for a capstone group        
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                panelists ps ON cp.projectID = ps.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ps.panelistID = ?
                            AND
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 3";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                            
                            $result = array_unique($result); //Removes any potential duplicated sections
                    }
                    
                    
                    
                    if(count($result) <= 0){
                        echo '<h1> No sections found  </h1>';
                    }
                }
                
                 else if ($specializationCount <= 0){
                     if($userID == $adminID){
                         $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                sections s
                            JOIN 
                                users u ON s.coordinatorID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                            WHERE 
                                 s.courseID = ? AND s.yearLevel = 3";
        
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$courseID]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    
                    
                    else{
                        
                        //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                       $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                sections s
                            JOIN 
                                users u ON s.coordinatorID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                            WHERE
                                s.coordinatorID = ? AND s.courseID = ? AND s.yearLevel = 3";
    
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                        
                        //For selecting sections in which the faculty member is an ADVISER for a capstone group   
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                advisers ad ON cp.projectID = ad.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ad.adviserID = ? AND s.courseID = ? AND s.yearLevel = 3";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
                            
                        
                        //For selecting sections in which the faculty member is a PANELIST for a capstone group        
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                panelists ps ON cp.projectID = ps.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ps.panelistID = ? AND s.courseID = ? AND s.yearLevel = 3";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                            
                            $result = array_unique($result); //Removes any potential duplicated sections
                        
                    }
                    
                    if(count($result) <= 0){
                        echo '<h1> No sections found  </h1>';
                    }
                }
                
            }
    
            else if ($accessLevel <= 1){
                $sql = "SELECT * FROM specializations WHERE courseID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$courseID]);
                
                $specializationCount = 0;
                
                while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $specializationCount++;
                }
                
                 if($specializationCount >= 1){
                     
                     //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                     $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                sections s
                            JOIN 
                                users u ON s.coordinatorID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                            WHERE
                                s.coordinatorID = ?
                            AND
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 3";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                    
                    //For selecting sections in which the faculty member is an ADVISER for a capstone group        
                    $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                advisers ad ON cp.projectID = ad.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ad.adviserID = ?
                            AND
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 3";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
         
         
                    //For selecting sections in which the faculty member is a PANELIST for a capstone group        
                    $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                panelists ps ON cp.projectID = ps.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ps.panelistID = ?
                            AND
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 3";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                            
                            $result = array_unique($result); //Removes any potential duplicated sections
                }
                
                 else if ($specializationCount <= 0){
                     
                       //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                       $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                sections s
                            JOIN 
                                users u ON s.coordinatorID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                            WHERE
                                s.coordinatorID = ? AND s.courseID = ? AND s.yearLevel = 3";
    
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                        
                        //For selecting sections in which the faculty member is an ADVISER for a capstone group   
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                advisers ad ON cp.projectID = ad.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ad.adviserID = ? AND s.courseID = ? AND s.yearLevel = 3";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
                            
                        
                        //For selecting sections in which the faculty member is a PANELIST for a capstone group        
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                panelists ps ON cp.projectID = ps.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ps.panelistID = ? AND s.courseID = ? AND s.yearLevel = 3";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                            
                            $result = array_unique($result); //Removes any potential duplicated sections
                 }
            }
            
        
           if (count($result) >= 1) {
                foreach ($result as $row) {
                    $sectionID = $row["sectionID"];
                    $_SESSION["sectionID"] = $sectionID;
                    
                    $section = $row["sectionName"];
                    $acadYear = $row["start_year"] . "-" . $row["end_year"];
                    $acadYearValue = $row["academicYearID"];
                    $coordinator = $row["surname"] . ", " . $row["firstname"] . " " . $row["middlename"];
                    $coordinatorID = $row["coordinatorID"];
                    
                    // Escape values to prevent XSS issues
                    $escapedSectionID = addslashes($sectionID);
                    $escapedSection = addslashes($section);
                    $escapedAcadYear = addslashes($acadYear);
                    $escapedCoordinator = addslashes($coordinator);
                    $escapedCoordinatorID = addslashes($coordinatorID);
                    $escapedAcadYearValue = addslashes($acadYearValue);
            
                    echo "<script>
                        var container = document.getElementById('section-container'); 
                        container.insertAdjacentHTML('beforeend', `
                            <form action=\"/groups\" method=\"POST\">
                                <div class=\"card-container\" onclick=\"this.closest('form').submit();\">
                                    <input type=\"hidden\" name=\"sectionValue\" value=\"$escapedSectionID\">
                                    <input type=\"hidden\" name=\"coordinatorValue\" value=\"$escapedCoordinatorID\">
                                    <input type=\"hidden\" name=\"coordinatorNameValue\" value=\"$escapedCoordinator\">
                                    <input type=\"hidden\" name=\"acadYearValue\" value=\"$escapedAcadYearValue\">
                                    
                                    <div class=\"card\" style=\"background-color:#0096FF;\">
                                        <div class=\"options\">
                                            <div class=\"hamburger\"></div>
                                            <div class=\"dropdown\">
                                                <button class=\"dropdown-item\">Edit</button>
                                                <button class=\"dropdown-item\">Delete</button>
                                            </div>
                                        </div>
                                        <div class=\"folder-bottom\">
                                            <div class=\"circle\"></div>
                                        </div>
                                        <div class=\"course-content\">
                                            <h2>$escapedSection</h2>
                                            <p>$escapedAcadYear</p>
                                            <p>$escapedCoordinator</p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        `);
                    </script>";
                }
            }
            
            else {
                echo '<h1>No existing student sections yet for this specialization</h1>';
            }
        }
        
        catch(Exception $e){
             echo'
                <script>
                    console.log("'.$e->getMessage().'");
                </script>
            ';
        }
    }
    
    function showFourthyears() {
        global $conn;
        
        $specialization = $_SESSION["specialization"];
        
        echo'
            <script>
                console.log("SPECIAL: '.$specialization.'");
            </script>
        ';
        
        try{
            $userID = $_SESSION["userID"];
            $courseID = $_SESSION["courseID"];
            
             echo'
                <script>
                    console.log("here 33");
                </script>
            ';
            
            
            $sql = "SELECT accessLevel from faculty WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            $accessLevel = $result["accessLevel"];
            
            
            
            $sql = "SELECT adminID FROM courses WHERE courseID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$courseID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $adminID = $result["adminID"];
    
    
            if($accessLevel >= 2){
                
                $sql = "SELECT * FROM specializations WHERE courseID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$courseID]);
                
                $specializationCount = 0;
                
                while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $specializationCount++;
                }
                
                if($specializationCount >= 1){
                    if($userID == $adminID){
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                sections s
                            JOIN 
                                users u ON s.coordinatorID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                            WHERE 
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 4";
                
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$specialization, $courseID]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    
                    else{
                        //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                sections s
                            JOIN 
                                users u ON s.coordinatorID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                            WHERE
                                s.coordinatorID = ?
                            AND
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 4";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            
                        //For selecting sections in which the faculty member is an ADVISER for a capstone group        
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                advisers ad ON cp.projectID = ad.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ad.adviserID = ?
                            AND
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 4";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
                            
                            
                        //For selecting sections in which the faculty member is a PANELIST for a capstone group        
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                panelists ps ON cp.projectID = ps.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ps.panelistID = ?
                            AND
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 4";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                            
                            $result = array_unique($result); //Removes any potential duplicated sections
                    }
                    
                    
                    
                    if(count($result) <= 0){
                        echo '<h1> No sections found  </h1>';
                    }
                }
                
                 else if ($specializationCount <= 0){
                     if($userID == $adminID){
                         $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                sections s
                            JOIN 
                                users u ON s.coordinatorID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                            WHERE 
                                 s.courseID = ? AND s.yearLevel = 4";
        
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$courseID]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    
                    
                    else{
                        
                        //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                       $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                sections s
                            JOIN 
                                users u ON s.coordinatorID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                            WHERE
                                s.coordinatorID = ? AND s.courseID = ? AND s.yearLevel = 4";
    
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                        
                        //For selecting sections in which the faculty member is an ADVISER for a capstone group   
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                advisers ad ON cp.projectID = ad.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ad.adviserID = ? AND s.courseID = ? AND s.yearLevel = 4";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
                            
                        
                        //For selecting sections in which the faculty member is a PANELIST for a capstone group        
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                panelists ps ON cp.projectID = ps.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ps.panelistID = ? AND s.courseID = ? AND s.yearLevel = 4";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                            
                            $result = array_unique($result); //Removes any potential duplicated sections
                        
                    }
                    
                    if(count($result) <= 0){
                        echo '<h1> No sections found  </h1>';
                    }
                }
                
            }
    
            else if ($accessLevel <= 1){
                $sql = "SELECT * FROM specializations WHERE courseID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$courseID]);
                
                $specializationCount = 0;
                
                while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $specializationCount++;
                }
                
                 if($specializationCount >= 1){
                     
                     //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                     $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                sections s
                            JOIN 
                                users u ON s.coordinatorID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                            WHERE
                                s.coordinatorID = ?
                            AND
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 4";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                    
                    //For selecting sections in which the faculty member is an ADVISER for a capstone group        
                    $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                advisers ad ON cp.projectID = ad.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ad.adviserID = ?
                            AND
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 4";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
         
         
                    //For selecting sections in which the faculty member is a PANELIST for a capstone group        
                    $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                panelists ps ON cp.projectID = ps.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ps.panelistID = ?
                            AND
                                s.specialization = ? AND s.courseID = ? AND s.yearLevel = 4";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                            
                            $result = array_unique($result); //Removes any potential duplicated sections
                }
                
                 else if ($specializationCount <= 0){
                     
                       //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                       $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                sections s
                            JOIN 
                                users u ON s.coordinatorID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                            WHERE
                                s.coordinatorID = ? AND s.courseID = ? AND s.yearLevel = 4";
    
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                        
                        //For selecting sections in which the faculty member is an ADVISER for a capstone group   
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                advisers ad ON cp.projectID = ad.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ad.adviserID = ? AND s.courseID = ? AND s.yearLevel = 4";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
                            
                        
                        //For selecting sections in which the faculty member is a PANELIST for a capstone group        
                        $sql = "SELECT 
                                CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                                s.sectionID,
                                s.coordinatorID,
                                s.courseID,
                                s.academicYearID,
                                s.section_letter,
                                s.section_group,
                                s.yearLevel,
                                u.surname,
                                u.firstname,
                                u.middlename,
                                ay.start_year,
                                ay.end_year
                            FROM 
                                capstone_projects cp 
                                
                            JOIN
                                sections s ON cp.sectionID = s.sectionID
                                
                            JOIN
                                coordinators cr ON cr.facultyID = s.coordinatorID
                                
                            JOIN 
                                panelists ps ON cp.projectID = ps.projectID
                            
                            JOIN 
                                users u ON cr.facultyID = u.id
                                
                            JOIN 
                                academic_year ay ON s.academicYearID = ay.id
                                
                            WHERE
                                ps.panelistID = ? AND s.courseID = ? AND s.yearLevel = 4";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                            
                            $result = array_unique($result); //Removes any potential duplicated sections
                 }
            }
            
        
           if (count($result) >= 1) {
                foreach ($result as $row) {
                    $sectionID = $row["sectionID"];
                    $_SESSION["sectionID"] = $sectionID;
                    
                    $section = $row["sectionName"];
                    $acadYear = $row["start_year"] . "-" . $row["end_year"];
                    $acadYearValue = $row["academicYearID"];
                    $coordinator = $row["surname"] . ", " . $row["firstname"] . " " . $row["middlename"];
                    $coordinatorID = $row["coordinatorID"];
                    
                    // Escape values to prevent XSS issues
                    $escapedSectionID = addslashes($sectionID);
                    $escapedSection = addslashes($section);
                    $escapedAcadYear = addslashes($acadYear);
                    $escapedCoordinator = addslashes($coordinator);
                    $escapedCoordinatorID = addslashes($coordinatorID);
                    $escapedAcadYearValue = addslashes($acadYearValue);
            
                    echo "<script>
                        var container = document.getElementById('section-container'); 
                        container.insertAdjacentHTML('beforeend', `
                            <form action=\"/groups\" method=\"POST\">
                                <div class=\"card-container\" onclick=\"this.closest('form').submit();\">
                                    <input type=\"hidden\" name=\"sectionValue\" value=\"$escapedSectionID\">
                                    <input type=\"hidden\" name=\"coordinatorValue\" value=\"$escapedCoordinatorID\">
                                    <input type=\"hidden\" name=\"coordinatorNameValue\" value=\"$escapedCoordinator\">
                                    <input type=\"hidden\" name=\"acadYearValue\" value=\"$escapedAcadYearValue\">
                                    
                                    <div class=\"card\" style=\"background-color:#0096FF;\">
                                        <div class=\"options\">
                                            <div class=\"hamburger\"></div>
                                            <div class=\"dropdown\">
                                                <button class=\"dropdown-item\">Edit</button>
                                                <button class=\"dropdown-item\">Delete</button>
                                            </div>
                                        </div>
                                        <div class=\"folder-bottom\">
                                            <div class=\"circle\"></div>
                                        </div>
                                        <div class=\"course-content\">
                                            <h2>$escapedSection</h2>
                                            <p>$escapedAcadYear</p>
                                            <p>$escapedCoordinator</p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        `);
                    </script>";
                }
            }
            
            else {
                echo '<h1>No existing student sections yet for this specialization</h1>';
            }
        }
        
        catch(Exception $e){
             echo'
                <script>
                    console.log("'.$e->getMessage().'");
                </script>
            ';
        }
    }
    
    
    
    function showFilterSection($category, $yearLevel, $acadYear){
        global $conn;
        
        echo '
            <script>
                 console.log("HERE 123");
                 console.log("'.addslashes($category).'");
                 console.log("'.addslashes($yearLevel).'");
                 console.log("'.addslashes($acadYear).'");
            </script>
        ';
        
        try{
            if($category == "all"){
                
                if($yearLevel == "all"){
                    showSections();
                }
                
                else if($yearLevel == "3"){
                    showThirdyears();
                }
                
                else if($yearLevel == "4"){
                    showFourthyears();
                }
            }
            
            else if($category == "coordinator"){
                
            }
            
            else if($category == "panelist"){
                
            }
            
            else if($category == "adviser"){
                
            }
        }
        
        catch (Exception $e){
            echo'
                <script>
                    console.log("'.$e->getMessage().'");
                </script>
            ';
        }
    }
    
    
    
    function showCoordinator($yearLevel){
        global $conn;
        
        $specialization = $_SESSION["specialization"];

        $userID = $_SESSION["userID"];
        $courseID = $_SESSION["courseID"];
        
        
        
        $sql = "SELECT accessLevel from faculty WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $accessLevel = $result["accessLevel"];
        
        
        
        $sql = "SELECT adminID FROM courses WHERE courseID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$courseID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $adminID = $result["adminID"];


        if($accessLevel >= 2){
            
            $sql = "SELECT * FROM specializations WHERE courseID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$courseID]);
            
            $specializationCount = 0;
            
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $specializationCount++;
            }
            
            if($specializationCount >= 1){
                if($userID == $adminID){
                    $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            sections s
                        JOIN 
                            users u ON s.coordinatorID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                        WHERE 
                            s.specialization = ? AND s.courseID = ? AND s.yearLevel = ?";
            
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$specialization, $courseID, $yearLevel]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                else{
                    //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                    $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            sections s
                        JOIN 
                            users u ON s.coordinatorID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                        WHERE
                            s.coordinatorID = ?
                        AND
                            s.specialization = ? AND s.courseID = AND s.yearLevel = ?";
            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $specialization, $courseID, $yearLevel]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                   
                        
                        $result = array_unique($result); //Removes any potential duplicated sections
                }
                
                
                
                if(count($result) <= 0){
                    echo '<h1> No sections found  </h1>';
                }
            }
            
             else if ($specializationCount <= 0){
                 if($userID == $adminID){
                     $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            sections s
                        JOIN 
                            users u ON s.coordinatorID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                        WHERE 
                             s.courseID = ? AND s.yearLevel = ?";
    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$courseID, $yearLevel]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                
                else{
                    
                    //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                   $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            sections s
                        JOIN 
                            users u ON s.coordinatorID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                        WHERE
                            s.coordinatorID = ? AND s.courseID = ? AND s.yearLevel = ?";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID, $yearLevel]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                   
                        $result = array_unique($result); //Removes any potential duplicated sections
                    
                }
                
                if(count($result) <= 0){
                    echo '<h1> No sections found  </h1>';
                }
            }
            
        }

        else if ($accessLevel <= 1){
            $sql = "SELECT * FROM specializations WHERE courseID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$courseID]);
            
            $specializationCount = 0;
            
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $specializationCount++;
            }
            
             if($specializationCount >= 1){
                 
                 //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                 $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            sections s
                        JOIN 
                            users u ON s.coordinatorID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                        WHERE
                            s.coordinatorID = ?
                        AND
                            s.specialization = ? AND s.courseID = ?";
            
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $specialization, $courseID]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        
                        $result = array_unique($result); //Removes any potential duplicated sections
            }
            
             else if ($specializationCount <= 0){
                 
                   //For selecting sections in which the faculty member is a CAPSTONE COORDINATOR for a section
                   $sql = "SELECT 
                            CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName,
                            s.sectionID,
                            s.coordinatorID,
                            s.courseID,
                            s.academicYearID,
                            s.section_letter,
                            s.section_group,
                            u.surname,
                            u.firstname,
                            u.middlename,
                            ay.start_year,
                            ay.end_year
                        FROM 
                            sections s
                        JOIN 
                            users u ON s.coordinatorID = u.id
                        JOIN 
                            academic_year ay ON s.academicYearID = ay.id
                        WHERE
                            s.coordinatorID = ? AND s.courseID = ?";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userID, $courseID]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                    
                        $result = array_unique($result); //Removes any potential duplicated sections
             }
        }
        
    
         if (count($result) >= 1) {
                foreach ($result as $row) {
                    $sectionID = $row["sectionID"];
                    $_SESSION["sectionID"] = $sectionID;
                    
                    $section = $row["sectionName"];
                    $acadYear = $row["start_year"] . "-" . $row["end_year"];
                    $acadYearValue = $row["academicYearID"];
                    $coordinator = $row["surname"] . ", " . $row["firstname"] . " " . $row["middlename"];
                    $coordinatorID = $row["coordinatorID"];
                    
                    // Escape values to prevent XSS issues
                    $escapedSectionID = addslashes($sectionID);
                    $escapedSection = addslashes($section);
                    $escapedAcadYear = addslashes($acadYear);
                    $escapedCoordinator = addslashes($coordinator);
                    $escapedCoordinatorID = addslashes($coordinatorID);
                    $escapedAcadYearValue = addslashes($acadYearValue);
            
                    echo "<script>
                        var container = document.getElementById('section-container'); 
                        container.insertAdjacentHTML('beforeend', `
                            <form action=\"/groups\" method=\"POST\">
                                <div class=\"card-container\" onclick=\"this.closest('form').submit();\">
                                    <input type=\"hidden\" name=\"sectionValue\" value=\"$escapedSectionID\">
                                    <input type=\"hidden\" name=\"coordinatorValue\" value=\"$escapedCoordinatorID\">
                                    <input type=\"hidden\" name=\"coordinatorNameValue\" value=\"$escapedCoordinator\">
                                    <input type=\"hidden\" name=\"acadYearValue\" value=\"$escapedAcadYearValue\">
                                    
                                    <div class=\"card\" style=\"background-color:#0096FF;\">
                                        <div class=\"options\">
                                            <div class=\"hamburger\"></div>
                                            <div class=\"dropdown\">
                                                <button class=\"dropdown-item\">Edit</button>
                                                <button class=\"dropdown-item\">Delete</button>
                                            </div>
                                        </div>
                                        <div class=\"folder-bottom\">
                                            <div class=\"circle\"></div>
                                        </div>
                                        <div class=\"course-content\">
                                            <h2>$escapedSection</h2>
                                            <p>$escapedAcadYear</p>
                                            <p>$escapedCoordinator</p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        `);
                    </script>";
            }
        }
        
        else {
            echo '<h1>No existing student sections yet for this specialization</h1>';
        }
    }
    
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sections</title>
        <link rel="stylesheet" href="pages/card_layout.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="pages/session_tracker.js"></script>
    </head>
    
    <body>
        <?php include 'header.php'; ?> <!--This is for the topbar -->
        <?php include 'menu.php'; ?> <!--This is for the menu -->

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        
        <div class="create-button" id="createDiv">
            <?php
                //LOGIC FOR IMPLEMENTING A CREATE BUTTON
                
                if($_SESSION["accountType"] == "faculty"){
                    $sql = "SELECT f.accessLevel, c.courseID 
                            FROM faculty f 
                            JOIN courses c ON c.adminID = f.id
                            WHERE f.id = ?";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["userID"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $_SESSION["accessLevel"] = $result["accessLevel"];
                    $courseID_coordinator = $result["courseID"];
                    
        
                    if($_SESSION["accessLevel"] >= 2 && $courseID_coordinator == $_SESSION["courseID"]){
                        echo '
                            <script>
            
                                var button = document.createElement("button");
                                button.innerHTML = "+"; 
                                button.className = "create-btn-design";
                                button.onclick = function() {
                                    window.location.href = "create_sections"; 
                                };
                            
            
                                document.getElementById("createDiv").appendChild(button);
                            </script>
                        ';
                     }
                }
            
            ?>
        </div>
        
        <div id="title-container" class="dropdown-container">
            <?php
                if($_SESSION["specialization"] != "N/A"){
                    echo'
                        <h2 class="custom-title" id="custom-title">'.$_SESSION["courseID"].', '.$_SESSION["specialization"].'</h2>
                    ';
                }
                
                else{
                    echo'
                        <h2 class="custom-title" id="custom-title">'.$_SESSION["courseID"].'</h2>
                    ';
                }
            ?>
        </div>
        
        <div class="dropdown-container">
            <br>
            
            <form id="filter-form" action="" method="GET">
                
                <select name="category" id="categoryID" class="dropdowns">
                    <option value="all" <?php echo (isset($_GET['category']) && $_GET['category'] == 'all') ? 'selected' : ''; ?>>All Category</option>
                    <option value="coordinator" <?php echo (isset($_GET['category']) && $_GET['category'] == 'coordinator') ? 'selected' : ''; ?>>Coordinator</option>
                    <option value="panelist" <?php echo (isset($_GET['category']) && $_GET['category'] == 'panelist') ? 'selected' : ''; ?>>Panelist</option>
                    <option value="adviser" <?php echo (isset($_GET['category']) && $_GET['category'] == 'adviser') ? 'selected' : ''; ?>>Adviser</option>
                </select>
                
            
                <select name="yearLevel" id="yearLevelID" class="dropdowns">
                    <option value="all" <?php echo (isset($_GET['yearLevel']) && $_GET['yearLevel'] == 'all') ? 'selected' : ''; ?>>All Year Level</option>
                    <option value="3" <?php echo (isset($_GET['yearLevel']) && $_GET['yearLevel'] == '3') ? 'selected' : ''; ?>>3rd Year</option>
                    <option value="4" <?php echo (isset($_GET['yearLevel']) && $_GET['yearLevel'] == '4') ? 'selected' : ''; ?>>4th Year</option>
                </select>
                
            
                <select name="acadYear" id="acadYearID" class="dropdowns">
                    <?php 
                        $sql = "SELECT * FROM academic_year";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
            
                        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                            $yearID = $result["id"];
                            $year = $result["start_year"] . "-" . $result["end_year"];
                            
                            // Set selected attribute based on submitted value
                            $selected = (isset($_GET['acadYear']) && $_GET['acadYear'] == $yearID) ? 'selected' : '';
                            echo '<option value="'.$yearID.'" '.$selected.'>'.$year.'</option>';
                        }
                    ?>
                </select>
            </form>
            
        </div>
        
        
        <div id="section-container" class="card-container">
            <!--Content is dynamically displayed through php-->
        </div>
        
        
        
        <!--Keep the POST and GET method here below and not anywhere else in the code-->
        
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["specializedValue"])) {
                    $specialization = $_POST["specializedValue"];
                    
                    $_SESSION["specialization"] = $specialization;
                    
                    showSections();
                }
            }
            
            else if ($_SERVER["REQUEST_METHOD"] == "GET"){
                if(isset($_GET["category"]) && isset($_GET["yearLevel"]) && isset($_GET["acadYear"])){
                    
                    echo '
                        <script>
                             document.getElementById("section-container").innerHTML = "";
                        </script>
                    ';
        
                    showFilterSection($_GET["category"], $_GET["yearLevel"], $_GET["acadYear"]);
                }
            }
            
            else{
                if(isset($_SESSION["courseID"])){
                    if(isset($_SESSION["specialization"])){
                        $specialization = $_SESSION["specialization"];
                    }
                    
                    else{
                         
                        $sql = "SELECT * FROM specializations WHERE courseID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$courseID]);
                        
                        $specializationCount = 0;
                        
                        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                            $specializationCount++;
                        }
                        
                        if($specializationCount >= 1){
                            header("Location: /specializations");
                            exit();
                        }
                    }
                }
        
                else{
                    header("Location: /courses");
                    exit();
                }
            }
        ?>

        <script>
            document.querySelectorAll('.dropdowns').forEach(function(dropdown) {
                dropdown.addEventListener('change', function() {
                    document.getElementById('filter-form').submit(); // Use the form's id to submit
                });
            });
                        
        </script>
        

        <?php include 'footer.php'; ?> 
    </body>
</html>
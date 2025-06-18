<?php 
    session_start();
    require 'connection.php';
    
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["specializedValue"])) {
            //$specialization = $_POST["specializedValue"];
            
            $_SESSION["specialization"] = $_POST["specializedValue"];
            
            echo '
                <script>
                     console.log("'.$_SESSION["specialization"].'");
                </script>
            ';
        }
    }
    
    
    function showSections($category, $yearLevel, $acadYear) {
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
        
        
        
        if($accessLevel >= 3){
            
            $sql = "SELECT * FROM specializations WHERE courseID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$courseID]);
            
            $specializationCount = 0;
            
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $specializationCount++;
            }
            
            if($specializationCount >= 1){
                if($userID == $adminID || $category == "all"){ //HERE IN ACCESSLEVEL 3 USE OR INSTEAD OF AND
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
                            s.specialization = ? AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
            
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(params: [$specialization, $courseID, $yearLevel, $yearLevel, $acadYear]);  //DALAWA NAKALAGAY NA $yearLevel SA PARAMETER DAHIL GUMAMIT NG OR CONDITON SA WHERE
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                else{

                    $result = [];
                    
                    if($category == "all" || $category == "coordinator"){
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
                                s.specialization = ? AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID, $yearLevel, $yearLevel, $acadYear]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                        
                    
                    if($category == "all" || $category == "adviser"){
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
                                s.specialization = ? AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID, $yearLevel, $yearLevel, $acadYear]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
                    }
                    

                    if($category == "all" || $category == "panelist"){
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
                                s.specialization = ? AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID, $yearLevel, $yearLevel, $acadYear]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                    }      
                    
                     // Remove duplicates based on `sectionID`
                    $uniqueResults = [];
                    foreach ($result as $item) {
                        $uniqueResults[$item['sectionID']] = $item; // Use `sectionID` as the key
                    }
                    $result = array_values($uniqueResults); // Re-index the array
                    
                }
                                
                
                if(count($result) <= 0){
                    echo '
                        <script>
                            var container = document.getElementById("result-container"); 
                            container.insertAdjacentHTML("beforeend", `
                                <h1 style="color: red; font-weight: bold;">Returned Result: 0</h1>
                            `);
                        </script>
                     ';
                }
            }
            
             else if ($specializationCount <= 0){
                 if($userID == $adminID || $category == "all"){
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
                             s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$courseID, $yearLevel, $yearLevel, $acadYear]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                
                else{
                    $result = [];
                    
                    if($category == "all" || $category == "coordinator"){
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
                                AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";

                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$userID, $courseID, $yearLevel, $yearLevel, $acadYear]);
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                        

                    if($category == "all" || $category == "adviser"){
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
                                
                            AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID, $yearLevel, $yearLevel, $acadYear]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
                    }
                    
                    if($category == "all" || $category == "panelist"){
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

                            AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID, $yearLevel, $yearLevel, $acadYear]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                    }
                        
                     // Remove duplicates based on `sectionID`
                    $uniqueResults = [];
                    foreach ($result as $item) {
                        $uniqueResults[$item['sectionID']] = $item; // Use `sectionID` as the key
                    }
                    $result = array_values($uniqueResults); // Re-index the array
                    
                }
                
                if(count($result) <= 0){
                    echo '
                        <script>
                            var container = document.getElementById("result-container"); 
                            container.insertAdjacentHTML("beforeend", `
                                <h1 style="color: red; font-weight: bold;">Returned Result: 0</h1>
                            `);
                        </script>
                    ';
                }
            }
            
        }
        //END OF LEVEL 3
        
        


        else if($accessLevel == 2){
            
            $sql = "SELECT * FROM specializations WHERE courseID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$courseID]);
            
            $specializationCount = 0;
            
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $specializationCount++;
            }
            
            if($specializationCount >= 1){
                if($userID == $adminID && $category == "all"){
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
                            s.specialization = ? AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
            
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(params: [$specialization, $courseID, $yearLevel, $yearLevel, $acadYear]);  //DALAWA NAKALAGAY NA $yearLevel SA PARAMETER DAHIL GUMAMIT NG OR CONDITON SA WHERE
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                else{

                    $result = [];
                    
                    if($category == "all" || $category == "coordinator"){
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
                                s.specialization = ? AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID, $yearLevel, $yearLevel, $acadYear]);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                        
                    
                    if($category == "all" || $category == "adviser"){
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
                                s.specialization = ? AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID, $yearLevel, $yearLevel, $acadYear]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
                    }
                    

                    if($category == "all" || $category == "panelist"){
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
                                s.specialization = ? AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $specialization, $courseID, $yearLevel, $yearLevel, $acadYear]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                    }      
                    
                    // Remove duplicates based on `sectionID`
                    $uniqueResults = [];
                    foreach ($result as $item) {
                        $uniqueResults[$item['sectionID']] = $item; // Use `sectionID` as the key
                    }
                    $result = array_values($uniqueResults); // Re-index the array
                    
                }
                                
                
                if(count($result) <= 0){
                    echo '
                        <script>
                            var container = document.getElementById("result-container"); 
                            container.insertAdjacentHTML("beforeend", `
                                <h1 style="color: red; font-weight: bold;">Returned Result: 0</h1>
                            `);
                        </script>
                     ';
                }
            }
            
             else if ($specializationCount <= 0){
                 if($userID == $adminID && $category == "all"){
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
                             s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$courseID, $yearLevel, $yearLevel, $acadYear]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                
                else{
                    $result = [];
                    
                    if($category == "all" || $category == "coordinator"){
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
                                AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";

                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$userID, $courseID, $yearLevel, $yearLevel, $acadYear]);
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                        

                    if($category == "all" || $category == "adviser"){
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
                                
                            AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID, $yearLevel, $yearLevel, $acadYear]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
                    }
                    
                    if($category == "all" || $category == "panelist"){
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

                            AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?";
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID, $yearLevel, $yearLevel, $acadYear]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                    }
                        
                    //$result = array_unique($result); //Removes any potential duplicated sections
                    
                     // Remove duplicates based on `sectionID`
                    $uniqueResults = [];
                    foreach ($result as $item) {
                        $uniqueResults[$item['sectionID']] = $item; // Use `sectionID` as the key
                    }
                    $result = array_values($uniqueResults); // Re-index the array
                    
                }
                
                if(count($result) <= 0){
                    echo '
                        <script>
                            var container = document.getElementById("result-container"); 
                            container.insertAdjacentHTML("beforeend", `
                                <h1 style="color: red; font-weight: bold;">Returned Result: 0</h1>
                            `);
                        </script>
                    ';
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

                    $result = [];
                        
                    if($category == "all" || $category == "coordinator"){
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
                                    s.specialization = ? AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?"; 
                    
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$userID, $specialization, $courseID, $yearLevel, $yearLevel, $acadYear]);
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                        
                    
                    if($category == "all" || $category == "adviser"){
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
                                    s.specialization = ? AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?"; 
                    
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$userID, $specialization, $courseID, $yearLevel, $yearLevel, $acadYear]);
                                $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                $result = array_merge($result, $secondResult);  // Combine both results
                    }
                    

                    if($category == "all" || $category == "panelist"){
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
                                    s.specialization = ? AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?"; 
                    
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$userID, $specialization, $courseID, $yearLevel, $yearLevel, $acadYear]);
                                $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                $result = array_merge($result, $thirdResult);  // Combine both results
                    }  

                     // Remove duplicates based on `sectionID`
                    $uniqueResults = [];
                    foreach ($result as $item) {
                        $uniqueResults[$item['sectionID']] = $item; // Use `sectionID` as the key
                    }
                    $result = array_values($uniqueResults); // Re-index the array
            }
            
             else if ($specializationCount <= 0){
                 
                    $result = [];
                        
                    if($category == "all" || $category == "coordinator"){
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
                                    
                                AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?"; 

                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$userID, $courseID, $yearLevel, $yearLevel, $acadYear]);
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    

                    if($category == "all" || $category == "adviser"){
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
                            
                            AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?"; 
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID, $yearLevel, $yearLevel, $acadYear]);
                            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $secondResult);  // Combine both results
                    }


                    if($category == "all" || $category == "panelist"){
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
                            
                            AND s.courseID = ? AND (s.yearLevel = ? OR ? = 'all')  AND s.academicYearID = ?"; 
                
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$userID, $courseID, $yearLevel, $yearLevel, $acadYear]);
                            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $result = array_merge($result, $thirdResult);  // Combine both results
                    }
                        
                     // Remove duplicates based on `sectionID`
                    $uniqueResults = [];
                    foreach ($result as $item) {
                        $uniqueResults[$item['sectionID']] = $item; // Use `sectionID` as the key
                    }
                    $result = array_values($uniqueResults); // Re-index the array
             }
        }
        
    
         if (count($result) >= 1) {
             
                echo '
                    <script>
                        var container = document.getElementById("result-container"); 
                        container.insertAdjacentHTML("beforeend", `
                            <h2 style="color: green; font-weight: bold; margin-bottom: -25px;">Returned Result: ' . addslashes(count($result)) . '</h2>
                        `);
                    </script>
                ';
                
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
                                    
                                    <div class=\"card\" style=\"background-color:#DF8F68;\">
                                        
                                        <div class=\"folder-bottom3\">
                                            <div class=\"circle3\"></div>
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
            echo '<h1 style="text-align: center; margin-top: -20px;">No sections found with the given parameters</h1>';
        }
    }
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=0.8">
        <title>Sections</title>
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
            <?php
                //LOGIC FOR IMPLEMENTING A CREATE BUTTON
                
                if($_SESSION["accountType"] == "faculty"){
                    $sql = "SELECT accessLevel FROM faculty WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["userID"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result){
                        
                        $_SESSION["accessLevel"] = $result["accessLevel"];
                        
                        $sql = "SELECT c.courseID 
                                FROM faculty f 
                                JOIN courses c ON c.adminID = f.id
                                WHERE f.id = ? AND c.courseID = ?";
                        
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_SESSION["userID"], $_SESSION["courseID"]]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        $courseID_coordinator = $result["courseID"];
                        
                        
                         if($_SESSION["accessLevel"] >= 3){
                            echo '
                                <script>
                                
                                    console.log("hey");
                
                                    var button = document.createElement("button");
                                    button.innerHTML = "+"; 
                                    button.className = "create-btn-design";
                                    button.onclick = function() {
                                        window.location.href = "create_sections"; 
                                    };
                                    
                                    var createDiv = document.getElementById("createDiv");
                                    
                                    if (createDiv) {
                                        createDiv.appendChild(button);
                                    }
                                </script>
                            ';
                            
                            if($_SESSION["NoSpecialization"] == true){
                                    echo '
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            var button = document.createElement("button");
                                            button.innerHTML = "+ Specialization"; 
                                            button.className = "create-btn-design2";
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
                         }
                        
            
                        else if($_SESSION["accessLevel"] == 2 && $courseID_coordinator == $_SESSION["courseID"]){
                            echo '
                                <script>
                                
                                    console.log("hey");
                
                                    var button = document.createElement("button");
                                    button.id = "section-btn";
                                    button.innerHTML = "+"; 
                                    button.className = "create-btn-design";
                                    button.onclick = function() {
                                        window.location.href = "create_sections"; 
                                    };
                                    
                                    var createDiv = document.getElementById("createDiv");
                                    
                                    if (createDiv) {
                                        createDiv.appendChild(button);
                                    }
                                </script>
                            ';
                            
                            if($_SESSION["NoSpecialization"] == true){
                                    echo '
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            var button = document.createElement("button");
                                            button.id = "specialization-btn";
                                            button.innerHTML = "+ Specialization"; 
                                            button.className = "create-btn-design2";
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
                         }
                    }
                }
            
            ?>
        </div>
        
        <div class="dropdown-container">
            <?php
                if($_SESSION["specialization"] != "No Specialization" && $_SESSION["specialization"] != ""){
                    echo'
                        <h2 class="custom-title" id="custom-title">'.$_SESSION["courseID"].', '.$_SESSION["specialization"].'</h2>
                    ';
                }
                
                else if($_SESSION["specialization"] == "No Specialization" && $_SESSION["specialization"] == ""){
                    echo'
                        <h2 class="custom-title" id="custom-title">'.$_SESSION["courseID"].', No Specialization</h2>
                    ';
                }
                
                else{
                    echo'
                        <h2 class="custom-title" id="custom-title">'.$_SESSION["courseID"].', No Specialization</h2>
                    ';
                }
            ?>
        </div>
        
        <div id="dropdown-div" class="dropdown-container">
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
                        $sql = "SELECT * FROM academic_year ORDER BY id DESC";
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
        
        <br>
        
        <div id="result-container" class="dropdown-container">
            <!-- Content comes from php -->
        </div>
        
        
        <div id="section-container" class="card-container">
            <?php
                $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $acadYear = $result["id"];
                $category = "all";
                $yearLevel = "all";
    
                showSections($category, $yearLevel, $acadYear);
            ?>
        </div>
        
        
        
        <!--Keep the POST and GET method here below and not anywhere else in the code-->
        
        <?php
           
            if ($_SERVER["REQUEST_METHOD"] == "GET"){
                if(isset($_GET["category"]) && isset($_GET["yearLevel"]) && isset($_GET["acadYear"])){
                    
                    echo '
                        <script>
                             document.getElementById("section-container").innerHTML = "";
                             document.getElementById("result-container").innerHTML = "";
                        </script>
                    ';
                    
                    $sql = "SELECT * FROM academic_year WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_GET["acadYear"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result){
                        $_SESSION["previousYear"] = $this_acadYearID = $result["id"];
                        
                        $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if($result){
                            
                            $curr_acadYearID = $result["id"];
                            
                            echo '
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        document.getElementsByClassName("create-btn-design")[0].style.display = ' . ($curr_acadYearID != $this_acadYearID ? '"none"' : '"block"') . ';
                                        document.getElementsByClassName("create-btn-design2")[0].style.display = ' . ($curr_acadYearID != $this_acadYearID ? '"none"' : '"block"') . ';
                                    });
                                </script>
                                ';
        
                            showSections($_GET["category"], $_GET["yearLevel"], $_GET["acadYear"]);
                        }
                    }
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
        

        <?php require 'footer.php'; ?> 
    </body>
</html>
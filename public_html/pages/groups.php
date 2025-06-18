<?php 
    session_start();
    require 'connection.php';
    
    $buttonExists = false;
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["sectionValue"]) && isset($_POST["acadYearValue"]) && isset($_POST["coordinatorValue"]) && isset($_POST["coordinatorNameValue"])) {
            $sectionID = $_POST["sectionValue"];
            $acadYearID = $_POST["acadYearValue"];
            $coordinator = $_POST["coordinatorValue"];
            $coordinatorName = $_POST["coordinatorNameValue"];
            
            $_SESSION["sectionID"] = $sectionID;
            $_SESSION["acadYearID"] = $acadYearID;
            $_SESSION["coordinator"] = $coordinator;
            $_SESSION["coordinatorName"] = $coordinatorName;
            
            //prevents the confirm form resubmission when using built in back arrow of browsers
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
    
    else{
        if(isset($_SESSION["sectionID"])){
            $sectionID = $_SESSION["sectionID"];
            $acadYearID= $_SESSION["acadYearID"];
            $coordinator = $_SESSION["coordinator"];
            $coordinatorName = $_SESSION["coordinatorName"];
        }
        
        else{
            header("Location: /courses");

            exit();
        }
    }
    
    function showInfo($info){
        echo '
            <h1 class="custom-title">' . addslashes($info) . '</h1>
        ';
    }
    
    function showGroups() {
        global $conn, $backgroundColor, $sectionID, $acadYearID, $coordinator, $coordinatorName, $buttonExists;
        
        $sectionID = $_SESSION["sectionID"];
        $acadYearID = $_SESSION["acadYearID"];
        $coordinator = $_SESSION["coordinator"];
        //$coordinatorName = $_SESSION["coordinatorName"];
        
        $userID = $_SESSION["userID"];
        
        echo "<script>console.log('Coordinator ID: " . addslashes($coordinator) . "');</script>";
        
        
        
        if($_SESSION["userID"] == $coordinator){
            
            echo "<script>console.log('HERE TRUE');</script>";
            
            //GET CAPSTONE GROUPS AS COORDINATOR
            $sql =  "SELECT * FROM capstone_projects cp 
                    JOIN academic_year ay ON cp.academicYearID = ay.id 
                    JOIN coordinators cord ON cord.sectionID = cp.sectionID
                    JOIN users u ON cord.facultyID = u.id 
                    WHERE cp.sectionID = ? 
                    AND cp.academicYearID = ?";   
        
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$sectionID, $acadYearID]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        else{
            $sql = "SELECT adminID FROM courses WHERE courseID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["courseID"]]);
            $verifier = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $course_admin = $verifier["adminID"];
            
            
            $sql = "SELECT accessLevel FROM faculty WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"]]);
            $getLevel = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $_SESSION["accessLevel"] = $getLevel["accessLevel"];
            
            //NEWEST CHANGE
            
            if($_SESSION["accessLevel"] >= 3){
                echo "<script>console.log('HERE ADMIN');</script>";
                //GET ALL CAPSTONE GROUPS AS THE SUPER ADMIN
                $sql =  "SELECT * FROM capstone_projects cp 
                        JOIN academic_year ay ON cp.academicYearID = ay.id 
                        WHERE cp.sectionID = ? 
                        AND cp.academicYearID = ?";   
            
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID, $acadYearID]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if(isset($_SESSION["previousYear"])){
                    
                    $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result2){
                        $latest_year = $result2["id"];
                        
                        if($acadYearID == $latest_year){
                            echo '
                                <div class="create-button">
                                    <button onclick="location.href=\'/create_groups\'" class="create-btn-design">+</button>
                                </div>
                            ';
                            //<button onclick="location.href=\'/create_templates\'" class="create-btn-design"><i class="fas fa-paperclip" style="font-size: 22px;"></i></button>
                            
                            $buttonExists = true;
                        }
                    }
                    
                }
                
                else{
                    echo '
                        <div class="create-button">
                            <button onclick="location.href=\'/create_groups\'" class="create-btn-design">+</button>
                        </div>
                    ';
                }
            }
            
            else if($_SESSION["accessLevel"] == 2 && $_SESSION["userID"] == $course_admin){
                //GET ALL CAPSTONE GROUPS AS THE COURSE ADMIN
                $sql =  "SELECT * FROM capstone_projects cp 
                        JOIN academic_year ay ON cp.academicYearID = ay.id 
                        WHERE cp.sectionID = ? 
                        AND cp.academicYearID = ?";   
            
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID, $acadYearID]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            else{
                //GET CAPSTONE GROUPS AS PANELIST
                $sql =  "SELECT * FROM capstone_projects cp 
                        JOIN academic_year ay ON cp.academicYearID = ay.id 
                        JOIN panelists ps ON ps.projectID = cp.projectID
                        JOIN users u ON ps.panelistID = u.id 
                        WHERE cp.sectionID = ? 
                        AND ps.panelistID = ?
                        AND cp.academicYearID = ?";   
            
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID, $_SESSION["userID"], $acadYearID]);
                $firstResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                
                //GET CAPSTONE GROUPS AS ADVISER
                $sql =  "SELECT * FROM capstone_projects cp 
                        JOIN academic_year ay ON cp.academicYearID = ay.id 
                        JOIN advisers ad ON ad.projectID = cp.projectID
                        JOIN users u ON ad.adviserID = u.id 
                        WHERE cp.sectionID = ? 
                        AND ad.adviserID = ?
                        AND cp.academicYearID = ?";   
            
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID, $_SESSION["userID"], $acadYearID]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $result = array_merge($result, $firstResult);  // Combine both results
            }
        }
        
        
        //LOGIC FOR DISPLAYING THE ABOVE TITLE FOR A SECTION
        $sql = "SELECT * FROM sections s
                JOIN academic_year ay ON ay.id= s.academicYearID
                WHERE s.sectionID = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute([$sectionID]);
        $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($result2){
            $section = htmlspecialchars($result2['courseID']) . ' ' . htmlspecialchars($result2['yearLevel']) . htmlspecialchars($result2['section_letter']) . htmlspecialchars($result2['section_group']);
                    
            $specialization = $result2["specialization"];
            
            if($specialization == "N/A"){
                $specialization = "";
            }
            
            else{
                $specialization = ", " . $result2["specialization"];
            }
            
            $acadYear = $result2["start_year"] . "-" . $result2["end_year"];
            
            $info = $section . $specialization . ", (" . $acadYear . ")";
                        
            echo '
                <script>
                    console.log("here the info");
                    // Use PHP to assign a value to the JavaScript variable
                    var info = "' . addslashes($info) . '";
            
                    // Use JavaScript to change the content of the <h1> tag
                    document.getElementById("custom-title").textContent = info;
                </script>
            ';
            
        }
        
        if (count($result) >= 1) {
            $counter = 0;
            
            foreach ($result as $row) {
                
                $acadYear = $row["start_year"] . "-" . $row["end_year"];
                $title = $row["title"];
                $coordinatorName = $row["surname"] . ", " . $row["firstname"];
                $projectID = $row["projectID"];
                $groupNum = $row["groupNum"];

                
                echo '
                    <form action="/group_inner" method="POST">
                        <div class="card-container" onclick="this.closest(\'form\').submit();">
                            <input type="hidden" name="sectionValue" value="' . htmlspecialchars($sectionID, ENT_QUOTES) . '">
                            <input type="hidden" name="acadYearValue" value="' . htmlspecialchars($acadYearID, ENT_QUOTES) . '">
                            <input type="hidden" name="coordinatorValue" value="' . htmlspecialchars($coordinator, ENT_QUOTES) . '">
                            <input type="hidden" name="titleValue" value="' . htmlspecialchars($title, ENT_QUOTES) . '">
                            <input type="hidden" name="projectIDValue" value = "'. htmlspecialchars($projectID, ENT_QUOTES).'">
                            
                            <div class="card" style="background-color:#B2A4DD;">
                               
                                <div class="folder-bottom2">
                                    <div class="circle2"></div>
                                </div>
                                <div class="course-content">
                                    <h2 class="groupNum">GROUP ' . htmlspecialchars($groupNum, ENT_QUOTES) . '</h2>
   
                                    <br>
                                    
                                    <br>
                                    
                                    <div class="divider"></div>
                                    
                                    <br>
                                    
                                    <small style="font-size: 20px;" class="title">' . htmlspecialchars($title, ENT_QUOTES) . '</small>
                                </div>
                            </div>
                        </div>
                    </form>
                ';
                
                $counter++;
            }
        } 
        
        else {
            echo '<h1>No existing capstone groups/projects yet for this section</h1>';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Capstone Groups</title>
        <link rel="stylesheet" href="pages/card_layout.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="pages/session_tracker.js"></script>
    </head>
   
    <body>
        <?php require 'header.php'; ?> <!--This is for the topbar -->
        <?php require 'menu.php'; ?> <!--This is for the menu -->
    
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        
        
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
                
                 if($_SESSION["userID"] == $_SESSION["coordinator"]){
                    
                    if($buttonExists == false){
                        
                        if(isset($_SESSION["previousYear"])){
                            
                            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if($result){
                                
                                $latest_year = $result["id"];
                                
                                if($_SESSION["acadYearID"] == $latest_year){
                                    echo '
                                        <div class="create-button">
                                            <button onclick="location.href=\'/create_groups\'" class="create-btn-design">+</button>
                                            <button onclick="location.href=\'/defense_date\'" class="create-btn-design"><i class="fas fa-calendar" style="font-size: 22px;"></i></button>
                                        </div>
                                    ';
                                }
     
                            }
                        }
                        
                        else{
                            echo '
                                <div class="create-button">
                                    <button onclick="location.href=\'/create_groups\'" class="create-btn-design">+</button>
                                    <button onclick="location.href=\'/defense_date\'" class="create-btn-design"><i class="fas fa-calendar" style="font-size: 22px;"></i></button>
                                </div>
                            ';
                        }
                    }
                 }
            
                 else if($_SESSION["userID"] == $_SESSION["coordinator"] && $_SESSION["accessLevel"] >= 2 && $courseID_coordinator == $_SESSION["courseID"]){
                    echo '
                        <div class="create-button">
                            <button onclick="location.href=\'/create_groups\'" class="create-btn-design">+</button>
                        </div>
                    ';
                 }
           }
        ?>
    
        
        <div>
            <h2 class="custom-title" id="custom-title"></h2>
        </div>
        
        <div class="card-container">
            <?php
                showGroups();
            ?>
          
        </div>
    
        <?php require 'footer.php'; ?> 
    </body>
</html>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="pages/group_inner.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <title>Capstone Group</title>
        <script src="pages/session_tracker.js"></script>
    </head>

    <body>
        <?php require 'header.php'; ?> <!-- This is for the topbar -->
        <?php require 'menu.php'; ?> <!-- This is for the menu -->
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        
        <?php
           require 'connection.php';
           session_start();
           
           $_SESSION["titleNum"] = "1"; //Related sa title evaluation answer page, para mag reset selected title everytime mag exist si user
           
           $projectID = $_SESSION["projectIDValue"];

          if($_SERVER["REQUEST_METHOD"] == "POST"){
 
                if(isset($_POST["sectionValue"]) && isset($_POST["coordinatorValue"]) && isset($_POST["acadYearValue"]) && isset($_POST["titleValue"]) && isset($_POST["projectIDValue"])){
                    $sectionID = htmlspecialchars($_POST["sectionValue"], ENT_QUOTES);
                    $coordinatorValue = htmlspecialchars($_POST["coordinatorValue"], ENT_QUOTES);
                    $acadYearValue = htmlspecialchars($_POST["acadYearValue"], ENT_QUOTES);
                    $title = htmlspecialchars($_POST["titleValue"], ENT_QUOTES);
                    $projectID = htmlspecialchars($_POST["projectIDValue"], ENT_QUOTES);
                    
                    $_SESSION["sectionID"] = $sectionID;
                    $_SESSION["coordinatorValue"]  = $coordinatorValue;
                    $_SESSION["acadYearValue"] = $acadYearValue;
                    $_SESSION["titleValue"] = $title;
                    $_SESSION["projectIDValue"] = $projectID;
                    
        
                    
                    echo "<script>
                        console.log('sectionID:', " . json_encode($_SESSION["sectionID"]) . ");
                        console.log('coordinatorValue:', " . json_encode($_SESSION["coordinatorValue"]) . ");
                        console.log('acadYearValue:', " . json_encode($_SESSION["acadYearValue"]) . ");
                        console.log('titleValue:', " . json_encode($_SESSION["titleValue"]) . ");
                        console.log('projectIDValue:', " . json_encode($_SESSION["projectIDValue"]) . ");
                    </script>";
                    
                    
                    
                    $sql = "SELECT accessLevel FROM faculty WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["userID"]]);
                    $getLevel = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $_SESSION["accessLevel"] = $getLevel["accessLevel"];
                    
                    
                    //THIS IS THE UPDATED VERSION
                    
                    //prevents confirm resubmission when using the built in back arrows of browsers
                    //header("Location: " . $_SERVER['REQUEST_URI']);
                    //exit;
                  
                }
                
                else if(isset($_POST["remove-panelist-btn"])){
                    
                    $sectionID = $_SESSION["sectionID"];
                    $coordinatorValue = $_SESSION["coordinatorValue"];
                    $acadYearValue = $_SESSION["acadYearValue"];
                    $title = $_SESSION["titleValue"];
                    $projectID = $_SESSION["projectIDValue"];
                    
                    removePanelist();
                }
                
                else if(isset($_POST["remove-chairman-btn"])){
                    
                    $sectionID = $_SESSION["sectionID"];
                    $coordinatorValue = $_SESSION["coordinatorValue"];
                    $acadYearValue = $_SESSION["acadYearValue"];
                    $title = $_SESSION["titleValue"];
                    $projectID = $_SESSION["projectIDValue"];
                    
                    removeChairman();
                }
                
                else if(isset($_POST["remove-adviser-btn"])){
                    
                    $sectionID = $_SESSION["sectionID"];
                    $coordinatorValue = $_SESSION["coordinatorValue"];
                    $acadYearValue = $_SESSION["acadYearValue"];
                    $title = $_SESSION["titleValue"];
                    $projectID = $_SESSION["projectIDValue"];
                    
                    removeAdviser();
                }
                
                else if(isset($_POST["remove-student-btn"])){
                    
                    $sectionID = $_SESSION["sectionID"];
                    $coordinatorValue = $_SESSION["coordinatorValue"];
                    $acadYearValue = $_SESSION["acadYearValue"];
                    $title = $_SESSION["titleValue"];
                    $projectID = $_SESSION["projectIDValue"];
                    
                    removeStudent();
                }
                
                else {
                    
                    die("Required values are missing.");
                }
          } 
          
          else {
              if(isset($_SESSION["sectionID"]) && isset($_SESSION["coordinatorValue"]) && isset($_SESSION["acadYearValue"]) && isset($_SESSION["titleValue"])  & isset($_SESSION["projectIDValue"])){
                  
                    $sql = "SELECT title FROM capstone_projects WHERE projectID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["projectIDValue"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $title = $result["title"];
                    
                    
                    $sectionID = $_SESSION["sectionID"];
                    $coordinatorValue = $_SESSION["coordinatorValue"];
                    $acadYearValue = $_SESSION["acadYearValue"];
                    $projectID = $_SESSION["projectIDValue"];
              }
          }
          
          
          function getSectionValue() {
            global $conn;
            
            try {
                $sectionID = $_SESSION["sectionID"];
                
                $sql = "SELECT * FROM sections WHERE sectionID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $section = htmlspecialchars($result['courseID']) . ' ' . htmlspecialchars($result['yearLevel']) . htmlspecialchars($result['section_letter']) . htmlspecialchars($result['section_group']);
                
                return $section;
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
          }
        
        
        
           // Only fetch the projectID once
           $projectID = $_SESSION["projectIDValue"];
           
           function getStudents(){
               global $conn, $projectID;
               
               $sql = "
                    SELECT sec.*, cp.* FROM capstone_projects cp
                    JOIN sections sec ON cp.sectionID = sec.sectionID
                    WHERE cp.projectID = ?
                ";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$projectID]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC); //NEWEST CHANGE
                
                $yearLevel = $result["yearLevel"];
                
                $_SESSION["yearLevel"] = $yearLevel;
               
               if($_SESSION["yearLevel"] == 3){
                   
                   // Fetch students with their 3RD YEAR  projectID
                    $sql = "SELECT u.surname, u.firstname, u.id
                    FROM users u
                    JOIN students s
                    ON u.id = s.id
                    WHERE s.projectID = ?";
               }
               
               else if($_SESSION["yearLevel"] == 4){
                  // Fetch students with their 4TH YEAR projectID
                    $sql = "SELECT u.surname, u.firstname, u.id
                    FROM users u
                    JOIN students s
                    ON u.id = s.id
                    WHERE s.new_projectID = ?"; 
               }
               
               
                $stmt = $conn->prepare($sql);
                $stmt->execute([$projectID]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Display students
                $index = 0;
                foreach ($result as $row) {
                    $studentName = $row["surname"] . ", " . $row["firstname"];
                    $studentID = $row["id"];
                    
                    $stmt = $conn->prepare("SELECT accessLevel FROM faculty WHERE id = ?");
                    $stmt->execute([$_SESSION["userID"]]);
                    $super = $stmt->fetch(PDO::FETCH_ASSOC);
                
                    $access = $super["accessLevel"];
                
                    if($_SESSION["userID"] == $_SESSION["coordinatorValue"] || $access >= 3){
                        
                        if(isset($_SESSION["previousYear"])){
                    
                            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if($result2){
                                $latest_year = $result2["id"];
                                
                                if($_SESSION["previousYear"] == $latest_year){
                                    echo "<div class='person'>
                                            <span class='name'>$studentName</span>
                                            <div class='dropdown'>
                                                <button class='hamburger' onclick='toggleDropdown(\"student-$index\")'>☰</button>
                                                <div class='dropdown-content' id='dropdown-student-$index'>
                                                     <form action='' method='POST'>
                                                           <input type='hidden' name='studentID' value='$studentID'>
                                                           <button class ='flat-button' type='submit' name='remove-student-btn'>Remove</button>
                                                   </form>
                                                </div>
                                            </div>
                                        </div>";
                                }
                                
                                else{
                                    echo "<div class='person'>
                                            <span class='name'>$studentName</span>
                                        </div>";
                                }
                            }
                        }
                        
                        else{
                           echo "<div class='person'>
                                <span class='name'>$studentName</span>
                                <div class='dropdown'>
                                    <button class='hamburger' onclick='toggleDropdown(\"student-$index\")'>☰</button>
                                    <div class='dropdown-content' id='dropdown-student-$index'>
                                         <form action='' method='POST'>
                                               <input type='hidden' name='studentID' value='$studentID'>
                                               <button class ='flat-button' type='submit' name='remove-student-btn'>Remove</button>
                                       </form>
                                    </div>
                                </div>
                            </div>"; 
                        }
                    }
                    
                    else{
                        echo "<div class='person'>
                                <span class='name'>$studentName</span>
                            </div>";
                    }

                    $index++;
                }
           }
                    
           function getAdvisers(){
               global $conn, $projectID;
               
               // Fetch advisers
                $sql = "SELECT u.surname, u.firstname, u.id
                        FROM users u
                        JOIN advisers a
                        ON u.id = a.adviserID
                        WHERE a.projectID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$projectID]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Display advisers
                $index = 0;
                foreach ($result as $row) {
                    $adviserName = $row["surname"] . ", " . $row["firstname"];
                    $adviserID = $row["id"];
                    
                    $stmt = $conn->prepare("SELECT accessLevel FROM faculty WHERE id = ?");
                    $stmt->execute([$_SESSION["userID"]]);
                    $super = $stmt->fetch(PDO::FETCH_ASSOC);
                
                    $access = $super["accessLevel"];
                
                    if($_SESSION["userID"] == $_SESSION["coordinatorValue"] || $access >= 3){
                        
                        if(isset($_SESSION["previousYear"])){
                    
                            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if($result2){
                                $latest_year = $result2["id"];
                                
                                if($_SESSION["previousYear"] == $latest_year){
                                    echo "<div class='person'>
                                            <span class='name'>$adviserName</span>
                                            <div class='dropdown'>
                                                <button class='hamburger' onclick='toggleDropdown(\"adviser-$index\")'>☰</button>
                                                <div class='dropdown-content' id='dropdown-adviser-$index'>
                                                   <form action='' method='POST'>
                                                       <input type='hidden' name='adviserID' value='$adviserID'>
                                                       <button class ='flat-button' type='submit' name='remove-adviser-btn'>Remove</button>
                                                   </form>
                                                </div>
                                            </div>
                                        </div>";
                                }
                                
                                else{
                                  echo "<div class='person'>
                                            <span class='name'>$adviserName</span>
                                        </div>";  
                                }
                            }
                        }
                        
                        else{
                          echo "<div class='person'>
                                    <span class='name'>$adviserName</span>
                                    <div class='dropdown'>
                                        <button class='hamburger' onclick='toggleDropdown(\"adviser-$index\")'>☰</button>
                                        <div class='dropdown-content' id='dropdown-adviser-$index'>
                                           <form action='' method='POST'>
                                               <input type='hidden' name='adviserID' value='$adviserID'>
                                               <button class ='flat-button' type='submit' name='remove-adviser-btn'>Remove</button>
                                           </form>
                                        </div>
                                    </div>
                                </div>";  
                        }
                    }
                    
                    else{
                      echo "<div class='person'>
                                <span class='name'>$adviserName</span>
                            </div>";  
                    }

                    $index++;
                }
           }
           
           function getChairman(){
               global $conn, $projectID;
               
               // Fetch panelist chairman (level 2)
                $sql = "SELECT u.surname, u.firstname, u.id
                        FROM users u
                        JOIN panelists p
                        ON u.id = p.panelistID
                        WHERE p.projectID = ? AND p.level = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$projectID, 2]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Display panelist chairman
                $index = 0;
                foreach ($result as $row) {
                    $chairmanName = $row["surname"] . ", " . $row["firstname"];
                    $chairmanID = $row["id"];
                    
                    
                    
                    $stmt = $conn->prepare("SELECT accessLevel FROM faculty WHERE id = ?");
                    $stmt->execute([$_SESSION["userID"]]);
                    $super = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $access = $super["accessLevel"];
                    
                    if($_SESSION["userID"] == $_SESSION["coordinatorValue"] || $access >= 3){
                
                        if(isset($_SESSION["previousYear"])){
                    
                            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if($result2){
                                $latest_year = $result2["id"];
                                
                                if($_SESSION["previousYear"] == $latest_year){
                            
                                    echo "<div class='person'>
                                            <span class='name'>$chairmanName</span>
                                            <div class='dropdown'>
                                                <button class='hamburger' onclick='toggleDropdown(\"chairman-$index\")'>☰</button>
                                                <div class='dropdown-content' id='dropdown-chairman-$index'>
                                                   <form action='' method='POST'>
                                                       <input type='hidden' name='chairmanID' value='$chairmanID'>
                                                       <button class ='flat-button' type='submit' name='remove-chairman-btn'>Remove</button>
                                                  </form>
                                                </div>
                                            </div>
                                        </div>";
                                }
                                
                                else{
                                    echo "<div class='person'>
                                        <span class='name'>$chairmanName</span>
                                    </div>";
                                }
                            }
                        }
                        
                        else{
                             echo "<div class='person'>
                                    <span class='name'>$chairmanName</span>
                                    <div class='dropdown'>
                                        <button class='hamburger' onclick='toggleDropdown(\"chairman-$index\")'>☰</button>
                                        <div class='dropdown-content' id='dropdown-chairman-$index'>
                                           <form action='' method='POST'>
                                               <input type='hidden' name='chairmanID' value='$chairmanID'>
                                               <button class ='flat-button' type='submit' name='remove-chairman-btn'>Remove</button>
                                          </form>
                                        </div>
                                    </div>
                                </div>";
                        }
                    }
                    
                    else{
                        echo "<div class='person'>
                                <span class='name'>$chairmanName</span>
                            </div>";
                    }

                    $index++;
                }
           }
           
           
           function getPanelists(){
               global $conn, $projectID;
               
            // Fetch panelists (level 1)
               $sql = "SELECT u.surname, u.firstname, u.id
                       FROM users u
                       JOIN panelists p
                       ON u.id = p.panelistID
                       WHERE p.projectID = ? AND p.level = 1";
               $stmt = $conn->prepare($sql);
               $stmt->execute([$projectID]);
               $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
               // Display panelists
               $index = 0;
               foreach ($result as $row) {
                   $panelistName = $row["surname"] . ", " . $row["firstname"];
                   $panelistID = $row["id"];
                    
                   $stmt = $conn->prepare("SELECT accessLevel FROM faculty WHERE id = ?");
                   $stmt->execute([$_SESSION["userID"]]);
                   $super = $stmt->fetch(PDO::FETCH_ASSOC);
                
                   $access = $super["accessLevel"];
                
                   if($_SESSION["userID"] == $_SESSION["coordinatorValue"] || $access >= 3){
                      
                      if(isset($_SESSION["previousYear"])){
                    
                        $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if($result2){
                            $latest_year = $result2["id"];
                            
                            if($_SESSION["previousYear"] == $latest_year){
                                   echo "<div class='person'>
                                           <span class='name'>$panelistName</span>
                                           <div class='dropdown'>
                                               <button class='hamburger' onclick='toggleDropdown(\"panelist-$index\")'>☰</button>
                                               <div class='dropdown-content' id='dropdown-panelist-$index'>
                                                  <form action='' method='POST'>
                                                       <input type='hidden' name='panelistID' value='$panelistID'>
                                                       <button class ='flat-button' type='submit' name='remove-panelist-btn'>Remove</button>
                                                  </form>
                                               </div>
                                           </div>
                                         </div>";
                    
                            }
                            
                            else{
                                echo "<div class='person'>
                                    <span class='name'>$panelistName</span>
                                </div>";
                            }
                        }
                    }
                    
                    else{
                        echo "<div class='person'>
                                   <span class='name'>$panelistName</span>
                                   <div class='dropdown'>
                                       <button class='hamburger' onclick='toggleDropdown(\"panelist-$index\")'>☰</button>
                                       <div class='dropdown-content' id='dropdown-panelist-$index'>
                                          <form action='' method='POST'>
                                               <input type='hidden' name='panelistID' value='$panelistID'>
                                               <button class ='flat-button' type='submit' name='remove-panelist-btn'>Remove</button>
                                          </form>
                                       </div>
                                   </div>
                                 </div>";
      
                    }
               }
               
               else{
                    echo "<div class='person'>
                            <span class='name'>$panelistName</span>
                        </div>";
               }
               
               $index++;
               
             }
         }
           
           
            function removePanelist(){
               global $conn, $projectID;
               
               $panelID = $_POST["panelistID"];
               
               try{
                   $conn->beginTransaction();
                   
                   $sql = "DELETE FROM panelists WHERE panelistID = ? AND projectID = ?";
                   $stmt = $conn->prepare($sql);
                   $result = $stmt->execute([$panelID, $projectID]);
                   
                   if($result){
                       $sql = "SELECT surname, firstname FROM users WHERE id = ?";
                       $stmt = $conn->prepare($sql);
                       $stmt->execute([$panelID]);
                       $result = $stmt->fetch(PDO::FETCH_ASSOC);
                       
                       $surname = $result["surname"];
                       $firstname = $result["firstname"];
                       $fullname = $surname . ", " . $firstname;
                               
                       
                       
                       $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                       $stmt = $conn->prepare($sql);
                       $stmt->execute();
                       $yearResult = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                       $acadYear = $yearResult["id"];
                       
                       
                       $sql = "SELECT * FROM faculty_count WHERE facultyID = ?";
                       $stmt = $conn->prepare($sql);
                       $stmt->execute([$panelID]);
                       $countResult = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                       if($countResult){
                            $current_count = $countResult["panelist_count"];
                            $current_limit = $countResult["panelist_limit"];
                            
                            $newCount = $current_count - 1;
                       
                            $sql = "UPDATE faculty_count SET panelist_count = ? WHERE facultyID = ?";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$newCount, $panelID]);
                       
                       
                            if($result){
                                
                                $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$_SESSION["userID"]]);
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                $coord_name = $user["firstname"] . " " . $user["surname"] ;
                                
                                $section = getSectionValue();
                                
                                $desc = $coord_name . " REMOVED you as a CAPSTONE PANELIST for a capstone group in " . $section;
                                
                                date_default_timezone_set('Asia/Manila');
                                $date = date('Y-m-d H:i:s');
                                
                                $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$panelID, $desc, $date]);
                                
                                if($result){
                                    
                                     $sql = "SELECT * FROM sections WHERE sectionID = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["sectionID"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                     $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                                     
                                     
                                     $sql = "SELECT groupNum FROM capstone_projects WHERE projectID = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$projectID]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                     
                                     $groupNum = $result["groupNum"];
                                     
                                     
                                     
                                     $sql = "SELECT * FROM users WHERE id = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$panelID]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                      
                                     $panelist_name = "". $result["surname"] . ", " . $result["firstname"]  . " " . $result["middlename"] ;
                                    
                                    
                                     $sql = "SELECT * FROM users WHERE id = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["userID"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                      
                                     $firstname = $result["firstname"];
                                     $surname = $result["surname"];
                                     $middlename = $result["middlename"];
  
  
                                     $action = "". $surname . ", " . $firstname . " " . $middlename . " removed " . $panelist_name . " as a panelist of Group " . $groupNum . " in " . $section;
                                     
                                     date_default_timezone_set('Asia/Manila');
                                     $date = date('Y-m-d H:i:s');
                            
                                     $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                                     $stmt = $conn->prepare($sql);
                                     $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
             
                                   $conn->commit();
                                   
                                   unset($_POST["remove-panelist-btn"]);
                                   
                                   echo '<script>
                                        Swal.fire({
                                             title: "Panelist Removed",
                                            text: "Capstone Panelist: '.addslashes($fullname).' removed",
                                            icon: "error",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = window.location.pathname;
                                            }
                                            
                                            else if (result.isDismissed) {
                                                 window.location.href = window.location.pathname;
                                            }
                                        });
                                    </script>';
                                }
                                
                                else{
                                    throw new Exception("Failed to insert notifications");
                                }
                            }
                           
                            else{
                                throw new Exception("Failed to update panelist count of faculty");
                            }
                       }
                   }
                   
               }
               
               catch(Exception $e){
                   $conn->rollBack();
                   
                   unset($_POST["remove-panelist-btn"]);
                   
                   echo '<script>
                           Swal.fire({
                                title: "Error",
                               text: "Failed to remove panelist",
                               icon: "error",
                               confirmButtonText: "OK"
                           }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = window.location.pathname;
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = window.location.pathname;
                                    }
                                });
                       </script>';
               }
           }
           
            function removeChairman(){
               global $conn, $projectID;
               
               $chairmanID = $_POST["chairmanID"];
               
               try{
                   $conn->beginTransaction();
                   
                   $sql = "UPDATE panelists SET projectID = ? WHERE panelistID = ? AND projectID = ?";
                   $stmt = $conn->prepare($sql);
                   $result = $stmt->execute([NULL, $chairmanID, $projectID]);
                   
                   if($result){
                       $sql = "SELECT surname, firstname FROM users WHERE id = ?";
                       $stmt = $conn->prepare($sql);
                       $stmt->execute([$chairmanID]);
                       $result = $stmt->fetch(PDO::FETCH_ASSOC);
                       
                       $surname = $result["surname"];
                       $firstname = $result["firstname"];
                       $fullname = $surname . ", " . $firstname;
                       
                       $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                       $stmt = $conn->prepare($sql);
                       $stmt->execute();
                       $yearResult = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                       $acadYear = $yearResult["id"];
                       
                       
                       $sql = "SELECT * FROM faculty_count WHERE facultyID = ?";
                       $stmt = $conn->prepare($sql);
                       $stmt->execute([$chairmanID]);
                       $countResult = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                       if($countResult){
                            $current_count = $countResult["panelist_count"];
                            $current_limit = $countResult["panelist_limit"];
                            
                            $newCount = $current_count - 1;
                       
                            $sql = "UPDATE faculty_count SET panelist_count = ? WHERE facultyID = ?";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$newCount, $chairmanID]);
                               
                       
                           if($result){
                              
                                $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$_SESSION["userID"]]);
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                $coord_name = $user["firstname"] . " " . $user["surname"] ;
                                
                                $section = getSectionValue();
                                
                                $desc = $coord_name . " REMOVED you as a CAPSTONE PANELIST CHAIRMAN for a capstone group in " . $section;
                                
                                date_default_timezone_set('Asia/Manila');
                                $date = date('Y-m-d H:i:s');
                                
                                $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$chairmanID, $desc, $date]);
                                
                                if($result){
                                    
                                   
                                     $sql = "SELECT * FROM sections WHERE sectionID = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["sectionID"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                     $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                                     
                                     
                                     $sql = "SELECT groupNum FROM capstone_projects WHERE projectID = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$projectID]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                     
                                     $groupNum = $result["groupNum"];
                                     
                                     
                                     
                                     $sql = "SELECT * FROM users WHERE id = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$chairmanID]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                      
                                     $panelist_name = "". $result["surname"] . ", " . $result["firstname"]  . " " . $result["middlename"] ;
                                    
                                    
                                     $sql = "SELECT * FROM users WHERE id = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["userID"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                      
                                     $firstname = $result["firstname"];
                                     $surname = $result["surname"];
                                     $middlename = $result["middlename"];
    
    
                                     $action = "". $surname . ", " . $firstname . " " . $middlename . " removed " . $panelist_name . " as a chairman panelist of Group " . $groupNum . " in " . $section;
                                     
                                     date_default_timezone_set('Asia/Manila');
                                     $date = date('Y-m-d H:i:s');
                            
                                     $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                                     $stmt = $conn->prepare($sql);
                                     $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
    
                                   $conn->commit();
                                   
                                   unset($_POST["remove-chairman-btn"]);
                                   
                                   echo '<script>
                                        Swal.fire({
                                             title: "Panelist Chairman Removed",
                                            text: "Capstone Panelist Chairman: '.addslashes($fullname).' removed",
                                            icon: "error",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = window.location.pathname;
                                            }
                                            
                                            else if (result.isDismissed) {
                                                 window.location.href = window.location.pathname;
                                            }
                                        });
                                    </script>';
                                }
                                
                                else{
                                    throw new Exception("Error inserting in notifications");
                                }
                            }
                            
                            else{
                                throw new Exception("Failed to update panelist count of faculty");
                            }
                       }
                   }
                   
               }
               
               catch(Exception $e){
                   $conn->rollBack();
                   
                   unset($_POST["remove-chairman-btn"]);
                   
                   echo '<script>
                           Swal.fire({
                                title: "Error",
                               text: "Failed to remove chairman",
                               icon: "error",
                               confirmButtonText: "OK"
                           }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = window.location.pathname;
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = window.location.pathname;
                                    }
                                });
                       </script>';
               }
           }
           
            function removeAdviser(){
               global $conn, $projectID;
               
               $adviserID = $_POST["adviserID"];
               
               try{
                   $conn->beginTransaction();
                   
                   $sql = "UPDATE advisers SET projectID = ? WHERE adviserID = ? AND projectID = ?";
                   $stmt = $conn->prepare($sql);
                   $result = $stmt->execute([NULL, $adviserID, $projectID]);
                   
                   if($result){
                       $sql = "SELECT surname, firstname FROM users WHERE id = ?";
                       $stmt = $conn->prepare($sql);
                       $stmt->execute([$adviserID]);
                       $result = $stmt->fetch(PDO::FETCH_ASSOC);
                       
                       if($result){
                           $surname = $result["surname"];
                           $firstname = $result["firstname"];
                           $fullname = $surname . ", " . $firstname;
                           
                           
                           
                           $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                           $stmt = $conn->prepare($sql);
                           $stmt->execute();
                           $yearResult = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                           $acadYear = $yearResult["id"];
                           
                           
                           $sql = "SELECT * FROM faculty_count WHERE facultyID = ?";
                           $stmt = $conn->prepare($sql);
                           $stmt->execute([$adviserID]);
                           $countResult = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                           if($countResult){
                                $current_count = $countResult["adviser_count"];
                                $current_limit = $countResult["adviser_limit"];
                                
                                $newCount = $current_count - 1;
                           
                                $sql = "UPDATE faculty_count SET adviser_count = ? WHERE facultyID = ?";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$newCount, $adviserID]);
                               
                                if($result){
                                    
                                    $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute([$_SESSION["userID"]]);
                                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    $coord_name = $user["firstname"] . " " . $user["surname"] ;
                                    
                                    $section = getSectionValue();
                                    
                                    $desc = $coord_name . " REMOVED you as a CAPSTONE ADVISER for a capstone group in " . $section;
                                    
                                    date_default_timezone_set('Asia/Manila');
                                    $date = date('Y-m-d H:i:s');
                                    
                                    $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                    $stmt = $conn->prepare($sql);
                                    $result = $stmt->execute([$adviserID, $desc, $date]);
                                    
                                    if($result){
                                        
                                         $sql = "SELECT * FROM sections WHERE sectionID = ?";
                                         $stmt = $conn->prepare($sql);
                                         $stmt->execute([$_SESSION["sectionID"]]);
                                         $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                        
                                         $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                                         
                                         
                                         $sql = "SELECT groupNum FROM capstone_projects WHERE projectID = ?";
                                         $stmt = $conn->prepare($sql);
                                         $stmt->execute([$projectID]);
                                         $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                         
                                         $groupNum = $result["groupNum"];
                                         
                                         
                                         
                                         $sql = "SELECT * FROM users WHERE id = ?";
                                         $stmt = $conn->prepare($sql);
                                         $stmt->execute([$adviserID]);
                                         $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                          
                                         $adviser_name = "". $result["surname"] . ", " . $result["firstname"]  . " " . $result["middlename"] ;
                                        
                                        
                                         $sql = "SELECT * FROM users WHERE id = ?";
                                         $stmt = $conn->prepare($sql);
                                         $stmt->execute([$_SESSION["userID"]]);
                                         $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                          
                                         $firstname = $result["firstname"];
                                         $surname = $result["surname"];
                                         $middlename = $result["middlename"];
      
      
                                         $action = "". $surname . ", " . $firstname . " " . $middlename . " removed " . $adviser_name . " as a capstone adviser of Group " . $groupNum . " in " . $section;
                                         
                                         date_default_timezone_set('Asia/Manila');
                                         $date = date('Y-m-d H:i:s');
                                
                                         $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                                         $stmt = $conn->prepare($sql);
                                         $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                               
                                       $conn->commit();
                                       
                                       unset($_POST["remove-adviser-btn"]);
                                       
                                       echo '<script>
                                            Swal.fire({
                                                 title: "Adviser Removed",
                                                text: "Capstone Adviser: '.addslashes($fullname).' removed",
                                                icon: "error",
                                                confirmButtonText: "OK"
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = window.location.pathname;
                                                }
                                                
                                                else if (result.isDismissed) {
                                                     window.location.href = window.location.pathname;
                                                }
                                            });
                                        </script>';
                                    }
                                    
                                    else{
                                        throw new Exception("Error inserting in notifications");
                                    }
                                }
                                
                                else{
                                    throw new Exception("Failed to update adviser count of faculty");
                                }
                           }
                       }
                   }
                   
               }
               
               catch(Exception $e){
                   $conn->rollBack();
                   
                   unset($_POST["remove-adviser-btn"]);
                   
                   echo '<script>
                           Swal.fire({
                                title: "Error",
                               text: "Failed to remove adviser",
                               icon: "error",
                               confirmButtonText: "OK"
                           }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = window.location.pathname;
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = window.location.pathname;
                                    }
                                });
                       </script>';
               }
           }
           
           
            function removeStudent(){
               global $conn, $projectID;
               
               $studentID = $_POST["studentID"];
               
               try{
                   $conn->beginTransaction();
                   
                    $sql = "SELECT mode FROM academic_year ORDER BY id DESC LIMIT 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $mode = 0;
       
                    
                    if($result){
                        $mode = $result["mode"];
                    }
                    
                    
                    $result = "";
                    
                    if($mode == 1 || $mode == 2){
                        $sql = "UPDATE students SET projectID = ? WHERE id = ? AND projectID = ?"; //use id not studentID column
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([0, $studentID, $projectID]); //0 is the dummy capstone project used to remove students
                    }
                    
                    else if($mode == 3){
                        $sql = "UPDATE students SET projectID = ?, new_projectID = ? WHERE id = ? AND projectID = ?"; //use id not studentID column
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([0, NULL, $studentID, $projectID]); //0 is the dummy capstone project used to remove students
                    }


                   if($result){
                       $sql = "SELECT surname, firstname FROM users WHERE id = ?";
                       $stmt = $conn->prepare($sql);
                       $stmt->execute([$studentID]);
                       $result = $stmt->fetch(PDO::FETCH_ASSOC);
                       
                       if($result){
                            $surname = $result["surname"];
                            $firstname = $result["firstname"];
                            $fullname = $surname . ", " . $firstname;
                            
                            
                           
                            $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["userID"]]);
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $coord_name = $user["firstname"] . " " . $user["surname"] ;
                            
                            $section = getSectionValue();
                            
                            $desc = $coord_name . " REMOVED you in your capstone group in " . $section;
                            
                            date_default_timezone_set('Asia/Manila');
                            $date = date('Y-m-d H:i:s');
                            
                            $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$studentID, $desc, $date]);
                            
                            
                            if($result){
                                
                                     $sql = "SELECT * FROM sections WHERE sectionID = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["sectionID"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                     $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                                     
                                     
                                     $sql = "SELECT groupNum FROM capstone_projects WHERE projectID = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$projectID]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                     
                                     $groupNum = $result["groupNum"];
                                     
                                     
                                     
                                     $sql = "SELECT * FROM users WHERE id = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$studentID]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                      
                                     $student_name= "". $result["surname"] . ", " . $result["firstname"]  . " " . $result["middlename"] ;
                                    
                                    
                                     $sql = "SELECT * FROM users WHERE id = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["userID"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                      
                                     $firstname = $result["firstname"];
                                     $surname = $result["surname"];
                                     $middlename = $result["middlename"];
    
    
                                     $action = "". $surname . ", " . $firstname . " " . $middlename . " removed " . $student_name . " as a member of Group " . $groupNum . " in " . $section;
                                     
                                     date_default_timezone_set('Asia/Manila');
                                     $date = date('Y-m-d H:i:s');
                            
                                     $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                                     $stmt = $conn->prepare($sql);
                                     $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                                     
                                   $conn->commit();
                                   
                                   unset($_POST["remove-student-btn"]);
                                   
                                   echo '<script>
                                        Swal.fire({
                                             title: "Student Removed",
                                            text: "Student: '.addslashes($fullname).' removed",
                                            icon: "error",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = window.location.pathname;
                                            }
                                            
                                            else if (result.isDismissed) {
                                                 window.location.href = window.location.pathname;
                                            }
                                        });
                                    </script>';
                            }
                            
                            else{
                                throw new Exception("Error inserting in notifications");
                            }
                       }
                   }
                   
               }
               
               catch(Exception $e){
                   $conn->rollBack();
                   
                   unset($_POST["remove-student-btn"]);
                   
                   echo '<script>
                           Swal.fire({
                                title: "Error",
                               text: "Failed to remove student",
                               icon: "error",
                               confirmButtonText: "OK"
                           }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = window.location.pathname;
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = window.location.pathname;
                                    }
                                });
                       </script>';
               }
           }
           
           
            function countChairman(){
                global $conn;
                
                try{
                    $sql = "SELECT * FROM panelists WHERE projectID = ? AND level = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["projectIDValue"], 2]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if(count($result) <= 0){
                        if($_SESSION["userID"] == $_SESSION["coordinatorValue"]){
                             if(isset($_SESSION["previousYear"])){
                    
                                    $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();
                                    $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if($result2){
                                        $latest_year = $result2["id"];
                                        
                                        if($_SESSION["previousYear"] == $latest_year){
                                            echo '<button class="add-btn" name="addChairman" onclick="window.location.href=\'/add_chairman\'">+</button>';
                                        }
                                    }
                             }
                             
                             else{
                                 echo '<button class="add-btn" name="addChairman" onclick="window.location.href=\'/add_chairman\'">+</button>';
                             }
                        }
                    }
                    
                    else{
                        //More than 1 chairman not allowed
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
                                            window.location.href = window.location.pathname;
                                        }
                                        
                                        else if (result.isDismissed) {
                                             window.location.href = window.location.pathname;
                                        }
                                    });
                          </script>';
                }
            }
                   
          function showContent(){
            global $conn, $sectionID, $coordinatorValue, $acadYearValue, $title, $titleDesc;
            
            // Debug session values
            echo "<script>console.log('Section: " . addslashes($sectionID) . "');</script>";
            echo "<script>console.log('Coordinator: " . addslashes($coordinatorValue) . "');</script>";
            echo "<script>console.log('Academic Year: " . addslashes($acadYearValue) . "');</script>";
            echo "<script>console.log('Title: " . addslashes($title) . "');</script>";
            
            // Retrieve tags associated with the projectID
            $stmt = $conn->prepare("SELECT tag FROM title_tags WHERE projectID = ?");
            $stmt->execute([$_SESSION["projectIDValue"]]);
            $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            $sql = "SELECT CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName, 
                            s.specialization, 
                            s.semester,
                            s.yearLevel,
                            u.surname, 
                            u.firstname, 
                            u.middlename, 
                            a.start_year, 
                            a.end_year 
                        FROM sections s 
                        JOIN users u ON s.coordinatorID = u.id
                        JOIN academic_year a ON s.academicYearID = a.id
                        WHERE s.sectionID = ? AND s.coordinatorID = ? AND s.academicYearID = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$sectionID, $coordinatorValue, $acadYearValue]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if($result){
                $section = $result["sectionName"];
                $specialization = $result["specialization"];
                $semester = $result["semester"] == "1" ? "1st" : "2nd";
                $academicYear = $result["start_year"] . "-" . $result["end_year"] . " (". $semester ." Semester)";
                $coordinator = $result["surname"] . ", " . $result["firstname"] . " " . $result["middlename"];
                $_SESSION["yearLevel"] = $result["yearLevel"];
                
                
                
                //Get title description
                $sql = "SELECT title_description FROM capstone_projects WHERE projectID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["projectIDValue"]]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($result){
                    
                     $titleDesc = $result["title_description"];
        
                        echo '
                      
                                <div class="class-header" >
                                <div class="folder">
                                 <div class="circle"></div></div>
                                    <div class="header-content">
                                     
        
                                        <div>
                                          
                                            <h1>'.htmlspecialchars($section, ENT_QUOTES) .'</h1>
                                            <br>
                                            <h3>'.htmlspecialchars($specialization, ENT_QUOTES) .'</h3>
                                            <br>
                                            <h3>Coordinator: '. htmlspecialchars($coordinator, ENT_QUOTES) .'</h3>
                                        </div>
                                        <p>'.htmlspecialchars($academicYear, ENT_QUOTES) .'</p>
                                    </div>
                                </div>
                                
                                 <div class="semester-info">
                                    <span style="font-size: 1.5em; color: #6BA3C1;">'.htmlspecialchars($title, ENT_QUOTES).'</span>
                                    
                                    <span style="font-size: 1em;">'.htmlspecialchars($titleDesc, ENT_QUOTES).'</span>
                                <div class="tags-container">
                                    <button class="edit-button" title="Edit Tags" name="modal-button" style="display: none;"  disabled>

                                    </button>';
                                    
                                    // Loop through the tags and echo each tag in a <span> element
                                    foreach ($tags as $tag) {
                                        $formattedTag = ucwords(str_replace('_', ' ', $tag['tag'])); // Replace _ with space and capitalize each word
                                        echo '<span class="tag">' . htmlspecialchars($formattedTag, ENT_QUOTES) . '</span>';
                                    }
                                    
                                    echo '
                                            </div>
                                        </div>';
                    }
            } 
            
            else {
                // Debugging message
                echo "<script>console.log('Query returned no results');</script>";
                die("Error: No results found for the given section, coordinator, and academic year.");
            }
        }
        ?>
        
        
        
        
        
        
        <!-- Tab navigation -->
        <div class="tab-container">
            <button class="tab-button active" id="streamTab" onclick="showTab('stream')">Stream</button>
            <button class="tab-button" id="peopleTab" onclick="showTab('people')">People</button>
        </div>
        
      
        
        
        <div id="peopleContent" style="display: none;">
            <!-- Content for the "People" tab -->
            <div class="people-list">
                <div class="section">
                    <div class="header">
                        <label>Adviser</label>
                        
                        <?php
                            $stmt = $conn->prepare("SELECT accessLevel FROM faculty WHERE id = ?");
                            $stmt->execute([$_SESSION["userID"]]);
                            $super = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $access = $super["accessLevel"];
                            
                            if($_SESSION["userID"] == $_SESSION["coordinatorValue"] || $access >= 3){
                                
                                 if(isset($_SESSION["previousYear"])){
                    
                                    $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();
                                    $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if($result2){
                                        $latest_year = $result2["id"];
                                        
                                        if($_SESSION["acadYearValue"] == $latest_year){
                                                echo '<button class="add-btn" name="addAdviser" onclick="window.location.href=\'/add_adviser\'">+</button>';
                                        }
                                    }
                                    
                                 }
                                 
                                 else{
                                     echo '<button class="add-btn" name="addAdviser" onclick="window.location.href=\'/add_adviser\'">+</button>'; 
                                 }
                            }
                        ?>
                            
                    </div>
                    <div class="people-container">
                            <?php
                                getAdvisers();
                            ?>
                        </div>
                    </div>
                
                    <div class="section">
                        <div class="header">
                            <label>Panelist Chairman</label>
                            <?php
                               countChairman();
                            ?>
                        </div>
                        <div class="people-container">
                            <?php
                                getChairman();
                            ?>
                        </div>
                    </div>
                
                    <div class="section">
                        <div class="header">
                            <label>Panelists</label>
                
                            <?php
                                $stmt = $conn->prepare("SELECT accessLevel FROM faculty WHERE id = ?");
                                $stmt->execute([$_SESSION["userID"]]);
                                $super = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                $access = $super["accessLevel"];
                                
                                if($_SESSION["userID"] == $_SESSION["coordinatorValue"] || $access >= 3){
                                    if(isset($_SESSION["previousYear"])){
                    
                                        $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute();
                                        $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
                                        
                                        if($result2){
                                            $latest_year = $result2["id"];
                                            
                                            if($_SESSION["acadYearValue"] == $latest_year){
                                                echo '<button class="add-btn" name="addPanelist" onclick="window.location.href=\'/add_panelist\'">+</button>';
                                            }
                                        }
                                    }
                                    
                                    else{
                                       echo '<button class="add-btn" name="addPanelist" onclick="window.location.href=\'/add_panelist\'">+</button>'; 
                                    }
                                }
                            ?>
                        </div>
                        <div class="people-container">
                            <?php
                                getPanelists();
                            ?>
                        </div>
                    </div>
                
                    <div class="section">
                        <div class="header">
                            <label>Students</label>
                            
                           <?php
                                $stmt = $conn->prepare("SELECT accessLevel FROM faculty WHERE id = ?");
                                $stmt->execute([$_SESSION["userID"]]);
                                $super = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                $access = $super["accessLevel"];
                                
                                if($_SESSION["userID"] == $_SESSION["coordinatorValue"] || $access >= 3){
                                    if(isset($_SESSION["previousYear"])){
                    
                                        $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute();
                                        $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
                                        
                                        if($result2){
                                            $latest_year = $result2["id"];
                                            
                                            if($_SESSION["acadYearValue"] == $latest_year){
                                                echo '<button class="add-btn" name="addStudent" onclick="window.location.href=\'/add_student\'">+</button>';
                                            }
                                        }
                                    }
                                    
                                    else{
                                       echo '<button class="add-btn" name="addStudent" onclick="window.location.href=\'/add_student\'">+</button>'; 
                                    }
                                }
                           ?>
                        </div>
                        <div class="people-container">
                            <?php
                                getStudents();
                            ?>
                        </div>
                    </div>
                </div>
        </div>
        

        <div id="streamContent">
            <?php showContent(); ?>
            <div class="content-area">
                    <?php
                        $yearLevel = $_SESSION["yearLevel"];
                            
                        $sql = "SELECT * FROM tasks WHERE (yearLevel = ? OR yearLevel = ?) AND status = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$yearLevel,"all","enabled"]);
                        $result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if(count($result2) >= 1){
                            foreach ($result2 as $task){
                                $taskID = $task["id"];
                                $taskName = $task["taskName"];
                                $taskUrl = "answer_" . str_replace(" ", "_", $taskName);
                                
                                 echo '
                                    <form action="/'.$taskUrl.'" method="POST">
                                        <input type="hidden" name="taskID" value="'.$taskID.'">
                                        <div class="stream-tab" onclick="this.closest(\'form\').submit();">
                                                <div class="assignment-card">
                                                  <h5><i class="fas fa-file-alt"></i>'.ucwords($taskName).'</h5>
                                               </div>
                                        </div>
                                    </form>
                                ';
                                
                            }
                        }
                        
                    ?>
            </div>
        </div>

        
        <br><br><br><br><br>

        
        <!-- JavaScript for tab switching -->
        <script>
            function showTab(tab) {
                const streamTab = document.getElementById('streamTab');
                const peopleTab = document.getElementById('peopleTab');
                const streamContent = document.getElementById('streamContent');
                const peopleContent = document.getElementById('peopleContent');

                if (tab === 'stream') {
                    streamTab.classList.add('active');
                    peopleTab.classList.remove('active');
                    streamContent.style.display = 'block';
                    peopleContent.style.display = 'none';
                } 
                
                else if (tab === 'people') {
                    peopleTab.classList.add('active');
                    streamTab.classList.remove('active');
                    peopleContent.style.display = 'block';
                    streamContent.style.display = 'none';
                }
            }

            function toggleDropdown(id) {
                var dropdown = document.getElementById('dropdown-' + id);
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';
                } else {
                    dropdown.style.display = 'block';
                }
            }
    
            // Close the dropdown if clicked outside
            window.onclick = function(event) {
                if (!event.target.matches('.hamburger')) {
                    var dropdowns = document.getElementsByClassName('dropdown-content');
                    for (var i = 0; i < dropdowns.length; i++) {
                        if (dropdowns[i].style.display === 'block') {
                            dropdowns[i].style.display = 'none';
                        }
                    }
                }
            }
        </script>
        
        <?php require 'footer.php'; ?> 
    </body>
</html>
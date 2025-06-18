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
        <?php 
           require "connection.php";
           session_start();
        ?>
        
        <?php require 'header.php'; ?> <!-- This is for the topbar -->
        <?php require 'menu.php'; ?> <!-- This is for the menu -->
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        
        <?php
       
           $projectID = 0;
           $userID = isset($_SESSION["userID"]) ? $_SESSION["userID"] : ""; // or a default value
           $sectionID = 0;
           $courseID = "";
           $acadYearID = 0;
           $coordinatorID = "";
           $title = "";
           $titleDesc = "";
           $_SESSION["selectedTags"];
  
           
           echo '
                <script>
                    console.log("User ID in class: ' . addslashes($_SESSION["userID"]) . '");
                    console.log("Cookie: ' . (isset($_COOKIE["remember_me"]) ? addslashes($_COOKIE["remember_me"]) : 'No remember_me cookie') . '");
                </script>
            ';
           
           getValues();
           
           function getValues(){
               global $conn, $projectID, $userID, $sectionID, $courseID, $acadYearID, $coordinatorID, $title, $titleDesc;
               
               
               $sql = "SELECT new_projectID FROM students WHERE id = ?";
               $stmt = $conn->prepare($sql);
               $stmt->execute([$userID]);
               $result = $stmt->fetch(PDO::FETCH_ASSOC);
               
               $new_projectID = $result["new_projectID"];
               
               $sql = "";
               $colName1 = "";
               $colName2 = "";
               
               if($new_projectID == NULL){
                    $colName1 = "projectID";
                    $colName2 = "sectionID";
                    
                    $_SESSION["yearLevel"] = 3; //DO NOT REMOVE THIS
                     
                    $sql = "SELECT s.projectID, 
                        s.sectionID,
                        sec.courseID,
                        sec.coordinatorID,
                        sec.academicYearID,
                        cp.title,
                        cp.title_description
                        FROM students s 
                        JOIN sections sec ON s.sectionID = sec.sectionID 
                        JOIN capstone_projects cp ON s.projectID = cp.projectID
                        WHERE s.id = ?";
               }
               
               else{
                   $colName1 = "new_projectID";
                   $colName2 = "new_sectionID";
                   
                   $_SESSION["yearLevel"] = 4; //DO NOT REMOVE THIS
                   
                    $sql = "SELECT s.new_projectID, 
                        s.new_sectionID,
                        sec.courseID,
                        sec.coordinatorID,
                        sec.academicYearID,
                        cp.title,
                        cp.title_description
                        FROM students s 
                        JOIN sections sec ON s.new_sectionID = sec.sectionID 
                        JOIN capstone_projects cp ON s.new_projectID = cp.projectID
                        WHERE s.id = ?";
               }
               
            
               $stmt = $conn->prepare($sql);
               $stmt->execute([$userID]);
               $result = $stmt->fetch(PDO::FETCH_ASSOC);
               
               if($result){
                   $projectID = $result[$colName1];
                   $sectionID = $result[$colName2];
                   $courseID = $result["courseID"];
                   $acadYearID = $result["academicYearID"];
                   $coordinatorID = $result["coordinatorID"];
                   $title = $result["title"];
                   $titleDesc = $result["title_description"];
                   
                   $_SESSION["courseID"] = $courseID;
                   $_SESSION["projectID"] = $projectID;
                   $_SESSION["sectionID"] = $sectionID;
               }
               
               else{
                   die("Missing values");
               }
           }
          
           
           function getStudents(){
               global $conn, $projectID;
               
               if($projectID >= 1){
                   // Fetch students
                   
                    echo'
                        <script>
                            console.log('.$projectID.');
                        </script>
                    ';
                   

                   
                    $sql = ""; //declare empty
           
                    
                    if($_SESSION["yearLevel"] == 3){
                         $sql = "SELECT u.surname, u.firstname, u.id
                            FROM users u
                            JOIN students s
                            ON u.id = s.id
                            WHERE s.projectID = ?";
                    }
                    
                    else if($_SESSION["yearLevel"] == 4){
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
    
                        echo "<div class='person'>
                                <span class='name'>$studentName</span>
                            </div>";
    
                        $index++;
                    }
               }
           }
                    
           function getAdvisers(){
               global $conn, $projectID;
               
               if($projectID >= 1){
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
    
                        echo "<div class='person'>
                                <span class='name'>$adviserName</span>
                            </div>";
    
                        $index++;
                    }
               }
           }
           
           function getChairman(){
               global $conn, $projectID;
               
               if($projectID >= 1){
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
    
                        echo "<div class='person'>
                                <span class='name'>$chairmanName</span>
                            </div>";
    
                        $index++;
                    }
               }
           }
           
           
           function getPanelists(){
               global $conn, $projectID;
               
               if($projectID >= 1){
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
            
                       echo "<div class='person'>
                               <span class='name'>$panelistName</span>
                             </div>";
            
                       $index++;
                   }
               }
           }
           
           
          function showContent(){
                global $conn, $projectID, $sectionID, $coordinatorID, $acadYearID, $title, $titleDesc;
                
                // Debug session values
                echo "<script>console.log('Project: " . addslashes($projectID) . "');</script>";
                echo "<script>console.log('Section: " . addslashes($sectionID) . "');</script>";
                echo "<script>console.log('Coordinator: " . addslashes($coordinatorID) . "');</script>";
                echo "<script>console.log('Academic Year: " . addslashes($acadYearID) . "');</script>";
                echo "<script>console.log('Title: " . addslashes($title) . "');</script>";
                
                // Retrieve tags associated with the projectID
                $stmt = $conn->prepare("SELECT tag FROM title_tags WHERE projectID = ?");
                $stmt->execute([$_SESSION["projectID"]]);
                $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $_SESSION["selectedTags"] = array_column($tags, 'tag');
                
                // Query to retrieve section details
                $sql = "SELECT CONCAT(s.courseID, ' ', s.yearLevel, s.section_letter, s.section_group) AS sectionName, 
                                s.specialization, 
                                s.semester, 
                                u.surname, 
                                u.firstname, 
                                u.middlename, 
                                a.id,
                                a.start_year, 
                                a.end_year 
                            FROM sections s 
                            JOIN users u ON s.coordinatorID = u.id
                            JOIN academic_year a ON s.academicYearID = a.id
                            WHERE s.sectionID = ? AND s.coordinatorID = ? AND s.academicYearID = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID, $coordinatorID, $acadYearID]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result) {
                    $section = $result["sectionName"];
                    $specialization = $result["specialization"];
                    $semester = $result["semester"] == "1" ? "1st" : "2nd";
                    $academicYear = $result["start_year"] . "-" . $result["end_year"] . " (" . $semester . " Semester)";
                    $coordinator = $result["surname"] . ", " . $result["firstname"] . " " . $result["middlename"];
                    
                    $_SESSION["acadYearID"] = $result["id"];
                    
                    echo '
                        <div class="class-header">
                            <div class="folder">
                                <div class="circle"></div>
                            </div>
                            <div class="header-content">
                                <div>
                                    <h1>' . htmlspecialchars($section, ENT_QUOTES) . '</h1>
                                    <br>
                                    <h3>' . htmlspecialchars($specialization, ENT_QUOTES) . '</h3>
                                    <br>
                                    <h3>' . htmlspecialchars($coordinator, ENT_QUOTES) . '</h3>
                                </div>
                                <p>' . htmlspecialchars($academicYear, ENT_QUOTES) . '</p>
                            </div>
                        </div>
                        
                        <div class="semester-info">
                            <span style="font-size: 1.5em; color: #6BA3C1;">' . htmlspecialchars($title, ENT_QUOTES) . '</span>
                            <span style="font-size: 1em;">' . htmlspecialchars($titleDesc, ENT_QUOTES) . '</span>
                            
                            <div class="tags-container">
                                <button class="edit-button" title="Edit Tags" name="modal-button" onclick="openEditModal()">
                                    <span class="edit-icon"></span>
                                    <span class="edit-label">Edit Title Tags</span>
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
                
                else {
                    // Debugging message
                    echo "<script>console.log('Query returned no results');</script>";
                    die("Error: No results found for the given section, coordinator, and academic year.");
                }
            }

        
        
            function getArchivedFiles(){
                global $conn;
                
                try{
                    $stmt = $conn->prepare("SELECT * FROM previous_documents WHERE projectID = ?");
                    $stmt->execute([$_SESSION["projectID"]]);
                    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if($files){
                        
                        foreach($files as $file){
                            $filepath = $file["filepath"];
                            $filename = basename($filepath);
                            
                            echo'
                                <div class="stream-tab" onclick="downloadFile(\''.$filepath.'\')">
                                   <div class="assignment-card">
                                      <h5><i class="fas fa-file-pdf" style="color: red; font-size: 28px;"></i> '.$filename.'</h5>
                                   </div>
                                </div>
                            ';
                        }
                    }
                }
                
                catch(Exception $e){
                    
                }
            }
            
            
            function saveTags() {
                global $conn;
            
                try {
                    // Fetch all existing tags for the current project from the database
                    $stmt = $conn->prepare("SELECT tag FROM title_tags WHERE projectID = ?");
                    $stmt->execute([$_SESSION["projectID"]]);
                    $existingTags = $stmt->fetchAll(PDO::FETCH_COLUMN); // Get an array of tag names
            
                    // Loop through the selected checkboxes and insert new ones into the database
                    foreach ($_POST as $key => $value) {
                        // Check if the checkbox is checked (value will be the name of the tag)
                        if (!empty($value)) {
                            // If the tag is not already in the database, insert it
                            if (!in_array($key, $existingTags)) {
                                $stmt = $conn->prepare("INSERT INTO title_tags (projectID, tag) VALUES(?, ?)");
                                $result = $stmt->execute([$_SESSION["projectID"], $key]);
            
                                if (!$result) {
                                    throw new Exception("Failed to insert tag: " . $key);
                                }
                            }
                        }
                    }
            
                    // Now delete the tags that were unchecked (not in the selected POST data)
                    foreach ($existingTags as $tag) {
                        // If the tag is not in the currently selected checkboxes, delete it
                        if (!in_array($tag, array_keys($_POST))) {
                            $stmt = $conn->prepare("DELETE FROM title_tags WHERE projectID = ? AND tag = ?");
                            $result = $stmt->execute([$_SESSION["projectID"], $tag]);
            
                            if (!$result) {
                                throw new Exception("Failed to delete tag: " . $tag);
                            }
                        }
                    }
            
                    // If everything is successful, you can perform any additional actions
                    echo '
                        <script>
                            window.location.href = "/class_view";
                        </script>
                    ';
            
                } 
                
                catch (Exception $e) {
                    // Handle exceptions, log error or show a message
                    echo "Error: " . $e->getMessage();
                }
            }
        
        
        ?>
        
        
        
        
        
        
        <!-- Tab navigation -->
        <div class="tab-container">
            <button class="tab-button active" id="streamTab" onclick="showTab('stream')">Stream</button>
            <button class="tab-button" id="peopleTab" onclick="showTab('people')">People</button>
            <button class="tab-button" id="fileTab" onclick="showTab('file')" style="display: none;">Archived Files</button>
            
            <?php
                $stmt = $conn->prepare("SELECT mode FROM academic_year WHERE id = ?");
                $stmt->execute([$_SESSION["acadYearID"]]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($result){
                    $mode = $result["mode"];
                    
                    if($mode >= 2){
                        $stmt = $conn->prepare("SELECT semester FROM sections WHERE sectionID = ?");
                        $stmt->execute([$_SESSION["sectionID"]]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if($result){
                            $semester = $result["semester"];
                            
                            if($semester == 2){
                               echo '
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            // Show the Archived Files tab
                                            document.getElementById("fileTab").style.display = "block";
                                        });
                                    </script>
                                ';
                            }
                            
                            else{
                                echo '
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            // Hide the Archived Files tab
                                            document.getElementById("fileTab").style.display = "none";
                                        });
                                    </script>
                                ';
                            }
                        }
                    }
                }

            ?>
           
        </div>
        
        <div id="peopleContent" style="display: none;">
            <!-- Content for the "People" tab -->
            <div class="people-list">
                <div class="section">
                    <div class="header">
                        <label>Adviser</label>
                       
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
                           
                        </div>
                        <div class="people-container">
                            <?php
                                getStudents();
                            ?>
                        </div>
                    </div>
                </div>
        </div>
        
        <div id="fileContent" style="display: none;">
            <div class="people-list">
                <?php getArchivedFiles(); ?>
            </div>
        </div>

        <div id="streamContent">
            <?php showContent(); ?>
            <div class="content-area">
                     <?php
                        if($projectID >= 1){
                            
                            $SQL = "";
                            
                            if($_SESSION["yearLevel"] == 3){
                                $sql = "SELECT sec.yearLevel, sec.semester FROM sections sec JOIN students s ON sec.sectionID = s.sectionID WHERE s.id = ?";
                            }
                            
                            else if($_SESSION["yearLevel"] == 4){
                                $sql = "SELECT sec.yearLevel, sec.semester FROM sections sec JOIN students s ON sec.sectionID = s.new_sectionID WHERE s.id = ?";
                            }
                            
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["userID"]]);
                            $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $stmt = $conn->prepare("SELECT mode FROM academic_year ORDER BY id DESC Limit 1");
                            $stmt->execute();
                            $acadyear = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $mode = $acadyear["mode"];

                            
                            if($result2){
                                $yearLevel = $result2["yearLevel"];
                                $semester = $result2["semester"];
                                
                                $sql = "";
                                
                                if($mode == 1){
                                    if($yearLevel == 4 && $semester == 1){
                                        $sql = "SELECT * FROM tasks WHERE (yearLevel = ? OR yearLevel = ?) AND (id <> 1 AND taskName <> 'title evaluation') AND status = ?";
                                    }
                                    
                                    else{
                                        $sql = "SELECT * FROM tasks WHERE (yearLevel = ? OR yearLevel = ?) AND status = ?";
                                    }
                                }
                                
                                else if($mode == 2){
                                    if($yearLevel == 3 && $semester == 1){
                                        $sql = "SELECT * FROM tasks WHERE (yearLevel = ? OR yearLevel = ?) AND status = ?";
                                    }
                                    
                                    else if($yearLevel == 3 && $semester == 2){
                                         $sql = "SELECT * FROM tasks WHERE (yearLevel = ? OR yearLevel = ?) AND (id <> 1 AND taskName <> 'title evaluation') AND status = ?";
                                    }
                                }
                                
                                else if($mode == 3){
                                    if($yearLevel == 4 && $semester == 1){
                                        $sql = "SELECT * FROM tasks WHERE (yearLevel = ? OR yearLevel = ?) AND status = ?";
                                    }
                                    
                                    else if($yearLevel == 4 && $semester == 2){
                                         $sql = "SELECT * FROM tasks WHERE (yearLevel = ? OR yearLevel = ?) AND (id <> 1 AND taskName <> 'title evaluation') AND status = ?";
                                    }
                                }

                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$yearLevel,"all","enabled"]);
                                $result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if(count($result2) >= 1){
                                    foreach ($result2 as $task){
                                        $taskID = $task["id"];
                                        $taskName = $task["taskName"];
                                        $taskUrl = "edit_" . str_replace(" ", "_", $taskName);
                                        $taskType = $task["type"];
                                        
                                        
                                        if($taskType == "static"){
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
                                        
                                        else{
                                            echo '
                                                    <form action="/task" method="POST">
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
                                }
                            }

                        }
                        
                        else{
                            echo '
                                <div style="margin-top: 80px;">
                                    <h1 style="text-align: center;">Not yet added in a Capstone Group</h1>
                                    <h1 style="text-align: center;">Contact your Capstone Coordinator for more information</h1>
                                </div>
                            ';
                        }
                    ?>
            </div>
        </div>
        
        <div id="editTagsModal" class="modal">
            <div class="modal-content">
                <h2>Edit Tags</h2>
                <p>Select up to 3 tags for your capstone title:</p>
                
                <form id="tagsForm" action="" method="POST" onsubmit="return validateCheckboxes()">
                    <div class="checkbox-group">
                        <?php
                            
                           $sql = "SELECT tag FROM tags";
                          
                           $stmt = $conn->prepare($sql);
                           $stmt->execute();
                           $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
                           
                           foreach($tags as $tag){
                                // Remove underscores and capitalize the first letter of each word
                                $formattedTagName = ucwords(str_replace('_', ' ', $tag["tag"]));
                                
                                // Generate checkbox
                                echo '
                                    <label>
                                        <input type="checkbox" name="' . htmlspecialchars($tag["tag"], ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($tag["tag"], ENT_QUOTES, 'UTF-8') . '"' .
                                        (isset($_SESSION["selectedTags"]) && in_array($tag["tag"], $_SESSION["selectedTags"]) ? ' checked' : '') .
                                        '> ' . htmlspecialchars($formattedTagName, ENT_QUOTES, 'UTF-8') . '
                                    </label>
                                '; 
                           }
                           
                           
                        ?>
                    </div>
                    
                    <div class="modal-buttons">
                        <button type="submit" name="save-button">Save</button>
                        <button type="button" onclick="closeEditModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        
        <br><br><br><br><br>
        
        <script>
            function openEditModal() {
                const modal = document.getElementById('editTagsModal');
                modal.style.display = 'flex';
            
                // Pre-check existing tags (Replace with dynamic values from PHP)
                const currentTags = ['Machine Learning', 'Web Development', 'Data Science']; // Example
                const checkboxes = document.querySelectorAll('input[name="tags[]"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = currentTags.includes(checkbox.value);
                });
            }

            function closeEditModal() {
                const modal = document.getElementById('editTagsModal');
                modal.style.display = 'none';
                
                const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                window.location.href = "/class_view";
            }
            
            function validateCheckboxes() {
                // Get all checkboxes in the form
                const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                
                // Count how many checkboxes are checked
                let checkedCount = 0;
                checkboxes.forEach(function(checkbox) {
                    if (checkbox.checked) {
                        checkedCount++;
                    }
                });
            
                // If more than three checkboxes are selected, show an alert and prevent form submission
                if (checkedCount > 3) {
                    alert("You can only select up to 3 tags.");
                    return false; // Prevent form submission
                }
            
                return true; // Allow form submission if 3 or fewer checkboxes are selected
            }
            
        </script>

        <!-- JavaScript for tab switching -->
        <script>
            function showTab(tab) {
                const streamTab = document.getElementById('streamTab');
                const peopleTab = document.getElementById('peopleTab');
                const fileTab = document.getElementById('fileTab');
                const streamContent = document.getElementById('streamContent');
                const peopleContent = document.getElementById('peopleContent');
                const fileContent = document.getElementById('fileContent');

                if (tab === 'stream') {
                    streamTab.classList.add('active');
                    peopleTab.classList.remove('active');
                    fileTab.classList.remove('active');
                    streamContent.style.display = 'block';
                    peopleContent.style.display = 'none';
                    fileContent.style.display = 'none';
                } 
                
                else if (tab === 'people') {
                    peopleTab.classList.add('active');
                    streamTab.classList.remove('active');
                    fileTab.classList.remove('active');
                    peopleContent.style.display = 'block';
                    streamContent.style.display = 'none';
                    fileContent.style.display = 'none';
                }
                
                else if (tab === 'file') {
                    fileTab.classList.add('active');
                    streamTab.classList.remove('active');
                    peopleTab.classList.remove('active');
                    fileContent.style.display = 'block';
                    streamContent.style.display = 'none';
                    peopleContent.style.display = 'none';
                }
            }
            
            function downloadFile(filepath) {
                // Create a temporary link element
                var link = document.createElement('a');
                
                // Set the download attribute and the href to the file path
                link.href = filepath;
                link.download = filepath.split('/').pop();  // Automatically use the file name for downloading
                
                // Append the link to the body, trigger the click, and then remove the link
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

        </script>
        
        
        
        <?php
            if($_SERVER["REQUEST_METHOD"] == "POST"){
               if(isset($_POST["save-button"])){
                   saveTags();
               }
            }
        ?>
        
        <?php require 'footer.php'; ?> 
    </body>
</html>
<?php
    require_once "connection.php";
    session_start();
    
    $_SESSION["canViewFile"] = true;
    
    
    $yearLevel = 0;
        

    $selectedPanel = isset($_POST["panelComments"]) ? $_POST["panelComments"] : '';
    
    function getSectionID(){
        global $conn;
        
        try{
            $sql = "";
            
            if($_SESSION["yearLevel"] == 3){
                
                $sql = "SELECT sectionID FROM students WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["userID"]]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($result){
                    $_SESSION["sectionID"] = $result["sectionID"];
                }
                
                else{
                    throw new Exception("failed to get sectionID");
                }
            }
            
            else{
                
                $sql = "SELECT new_sectionID FROM students WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["userID"]]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($result){
                    $_SESSION["sectionID"] = $result["new_sectionID"];
                }
                
                else{
                    throw new Exception("failed to get sectionID");
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
                                    window.location.href = "/edit_defense";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/edit_defense";
                                }
                        });
                  </script>';
        }
    }
    
    function checkUser(){
        global $conn, $isPanelist, $panelLevel;
        
        try{
            $sql = "SELECT cp.*, s.yearLevel FROM capstone_projects cp JOIN sections s ON cp.sectionID = s.sectionID WHERE cp.projectID = ? AND cp.academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectID"], $_SESSION["acadYearID"]]);
            $capResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($capResult){
       
                //TAKE NOTE USE $capResult and not $result 
                
                $defToDisplay = "Capstone Defense";

                echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const verdictSection = document.getElementById("verdict-section");
                
                            // Determine colors based on verdict type
                            const verdict = ' . json_encode($capResult["defense"]) . ';
                            let cardColor = "#EAEAEA", textColor = "#333", icon = "fa-info-circle";
                
                            if (verdict == "redefense") {
                                cardColor = "#EAEAEA"; textColor = "#d32f2f"; icon = "fa-redo";
                            } 
                            
                            else if (verdict == "approved with revisions" || verdict == "approved") {
                                cardColor = "#EAEAEA"; textColor = "#388e3c"; icon = "fa-check-circle";
                            }
                            
                            else if (verdict == "pending") {
                                    cardColor = "#EAEAEA"; textColor = "#ff9800"; icon = "fa-clock";
                            }
                
                            // Adding the styled verdict card
                            verdictSection.innerHTML += 
                                "<div class=\'verdict-card\' style=\'background-color:" + cardColor + "\'>" +
                                    "<h1 class=\'verdict-result\' style=\'color:" + textColor + "\'>" +
                                        "<i class=\'fas " + icon + "\'></i> " +
                                        "'.$defToDisplay. ' Verdict is " + verdict +
                                    "</h1>" +
                                "</div>";
                        });
                    
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
                                    window.location.href = "/edit_defense";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/edit_defense";
                                }
                        });
                  </script>';
        }
    }
    
    
    
    function getProjectInfo(){
        global $conn, $yearLevel;
        
        try{
            echo '
                <script>
                    console.log('.$_SESSION["projectID"].');
                    console.log('.$_SESSION["sectionID"].');
                    console.log('.$_SESSION["acadYearID"].');
                </script>
            ';
            
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
            $stmt->execute([$_SESSION["projectID"], $_SESSION["sectionID"], $_SESSION["acadYearID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                $groupInfo = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                $groupNum = "Group " . $result["groupNum"];
                $specialization = $result["specialization"];
                $title = $result["title"];
                
                $section = $groupInfo . "<p></p>" . $groupNum;
                
                $semester = "";
                
                $yearLevel = $result["yearLevel"];
                
                if($result["semester"] == 2){
                    $semester = "2nd Semester";
                } else {
                    $semester = "1st Semester";
                }
                
                $acadYear = $result["start_year"] . "-" . $result["end_year"] . " (" . $semester . ")";
                
                echo '
                    <script>
                          document.getElementById("sectionText").innerHTML = "'.$section.'";
                          document.getElementById("specializationText").innerHTML = "'.$specialization.'";
                          document.getElementById("acadyearText").innerHTML = "'.$acadYear.'";
                          document.getElementById("titleText").innerHTML = "'.$title.'";
                    </script>
                ';
                
                // Capstone Paper Check
                $sql = "SELECT * FROM capstone_papers WHERE projectID = ? AND academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["projectID"], $_SESSION["acadYearID"]]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result && !empty($result["filepath"])) {
                    $filepath = $result["filepath"];
                    $filename = basename($filepath);
                    
                    // Check if file exists before displaying
                    if (file_exists($filepath)) {
                        $submitDate = $result["submit_date"];
                        $date = new DateTime($submitDate);
                        $formattedDate = $date->format('F j, Y g:i A');
                        
                        $status = $result["status"];
                        $label = "Capstone Paper:  ";
                        
                        // Output the file path and name as JSON for JavaScript to handle
                        echo "<script>
                             document.addEventListener('DOMContentLoaded', function() {
                                      document.getElementById('filesAttached').innerHTML += '<li data-filepath=\"$filepath\" onclick=\"openFile(this)\">' +
                                         '<span class=\"file-label\" style=\"color: black;\">$label</span> <i class=\"fas fa-paperclip\"></i> $filename' +
                                      '</li>';
                            });
                        </script>";
                    } else {
                        echo "<script>
                             document.addEventListener('DOMContentLoaded', function() {
                                      document.getElementById('filesAttached').innerHTML += '<li>No Capstone Paper File Found</li>';
                            });
                        </script>";
                    }
                } else {
                    echo '<script>console.log("no capstone file");</script>';
                }
                
                // Invitation Files Check
                $sql = "SELECT * FROM invitations WHERE projectID = ? AND academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["projectID"], $_SESSION["acadYearID"]]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if(count ($results) >= 1){
                    foreach($results as $result){
                        if ($result && !empty($result["filepath"])) {
                            $filepath = $result["filepath"];
                            $filename = basename($filepath);
                            
                            // Check if file exists before displaying
                            if (file_exists($filepath)) {
                                $submitDate = $result["submit_date"];
                                $date = new DateTime($submitDate);
                                $formattedDate = $date->format('F j, Y g:i A');
                                
                                $status = $result["status"];
                                $label = "Invitation:  ";
                                
                                // Output the file path and name as JSON for JavaScript to handle
                                echo "<script>
                                     document.addEventListener('DOMContentLoaded', function() {
                                            document.getElementById('filesAttached').innerHTML += '<li data-filepath=\"$filepath\" onclick=\"openFile(this)\">' +
                                                '<span class=\"file-label\" style=\"color: black;\">$label</span> <i class=\"fas fa-paperclip\"></i> $filename' +
                                            '</li>';
                                    });
                                </script>";
                            } else {
                                echo '<script>console.log("no invitation file");</script>';
                            }
                        }
                    }
                }
            } else {
                throw new Exception("Error retrieving group info");
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
                                    window.location.href = "/edit_defense";
                                }
                                else if (result.isDismissed) {
                                    window.location.href = "/edit_defense";
                                }
                        });
                  </script>';
        }
    }

    
    
    
    function getPanelists(){
        global $conn, $selectedPanel;
        
        try{
            $sql = "SELECT * FROM defense_answers WHERE projectID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectID"]]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count ($results) >= 1) {
                
                foreach ($results as $result){
                    $panelID = $result["panelistID"];
                    
                    $sql = "SELECT * FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$panelID]);
                    $name = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($name){
                        $fullname = $name["surname"] . ", " . $name["firstname"] . " " . $name["middlename"];
                        
                        $submitDate = $result["date_answer"];
                        $date = new DateTime($submitDate);
                        $formattedDate = $date->format('F j, Y g:i A');
                        
                        $isSelected = ($selectedPanel == $panelID) ? ' selected' : '';
                        echo "<option value=\"$panelID\" $isSelected>$fullname</option>";
                    }
                    
                    else{
                        throw new Exception("Failed to get fullname of panelist");
                    }
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
                                    window.location.href = "/edit_defense";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/edit_defense";
                                }
                        });
                  </script>';
        }
    }
    
    
    function viewComments($panelID){
        global $conn;
        
        try{
            if($panelID != "none"){
                if(empty($panelID) || $panelID == null){
                    $sql = "SELECT * FROM defense_answers WHERE projectID = ? AND level = ? ";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["projectID"], 2]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result){
                        $comment = $result["comment"];
                        
                        echo '
                            <script>
                                document.getElementById("comment").value = ' . json_encode($comment) . ';
                            </script>
                        ';
                    }
                }
                
                else{
                    $sql = "SELECT * FROM defense_answers WHERE projectID = ? AND panelistID = ? ";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["projectID"], $panelID]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result){
                        $comment = $result["comment"];
                        
                        echo '
                            <script>
                                document.getElementById("comment").value = ' . json_encode($comment) . ';
                            </script>
                        ';
                    }
                }
            }
            
            else{
                echo '
                    <script>
                        document.getElementById("comment").value = "";
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
                                    window.location.href = "/edit_defense";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/edit_defense";
                                }
                        });
                  </script>';
        }
    }
    
    
    
    function getDefenseDate(){
        global $conn;
        
        try{
            $sql = "SELECT * FROM defense_dates WHERE projectID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                $date = $result["date"];
                $formattedDate = date('F d, Y g:i A', strtotime($date));
                
                echo '
                    <h2 id="defenseDate" style="text-align: left; "> Capstone Defense Date: <span style="color: #066BA3;">'.$formattedDate.'</span></h2>
                ';
            }
        }
        
        catch(Exception $e){
            
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
        <link rel="stylesheet" href="pages/defense.css">
        <script src="pages/session_tracker.js"></script>
        
        <title>Defense</title>
    </head>
    
    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
        
        <?php getDefenseDate(); ?>
        
        <form action="" method="POST">
            <div class="defense-content">
                <div class="left-panel">
                    <div class="group-info">
                        <h3 id="sectionText"><p></p></h3>
                          <div class="bgs">
                        <div class="folder">
 
                         <div class="circle"> </div>
                    </div>
                        <div class="group-details">
                            <p id="specializationText" class="service-management">Service Management</p>
                            <p id="acadyearText" class="semester-info">2023-2024 (2nd Semester)</p>
                        </div>
                    </div>
        </div>
        
                    <div class="project-title">
                        <p id="titleText"></p>
                    </div>
        
                    <div class="verdict">
                        <textarea id="comment" name="comment" class="verdict-textbox" placeholder="Capstone Comments/Verdicts Soon to Appear..." disabled></textarea>
                    </div>
                    
                    <div id="dropdown" class="semester-container" style="display: flex; align-items: center; gap: 250px;">
                        <!-- Label and Dropdown Container -->
                        <div style="display: flex; flex-direction: row; align-items: center; gap: 10px; margin-top: 25px;">
                            <label id="comment-label" for="panelComments" style="font-size: 18px;">Select Stored Panelist Comment: </label>
                            
                                <select name="panelComments" id="panelComments" class="semester-container" style="font-size: 18px;" onchange="this.form.submit();">
                                    <option value="none">Select A Panelist</option>;
                                    <?php getPanelists(); ?>
                                </select>
                        </div>

                    </div>
                </div>
        
                <div class="right-panel">
                    <div class="files-attached">
                        <h4>Files Attached</h4>
                        <ul id="filesAttached">
                            <!--Dynamic clickable files will appear here-->
                        </ul>
                    </div>
                    
                    <br>
                    <br>
                    
                    <div class="divider"></div> <!-- Custom styled divider -->
                    
                    <div id="verdict-section" class="verdict-section">
                        <!-- Verdict content will be injected here -->
                    </div>
                    
                    <br>
                    <br>
                    
                    <div id="button-section" class="left-buttons">

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
            const fileDisplay = document.getElementById('file-display');
            
            // Display file in modal on <li> click
            function openFile(listItem) {
                const filepath = listItem.getAttribute('data-filepath');
                openModal(filepath);
            }
            
            // Modal functions for viewing files
            function openModal(filePath) {
                const modal = document.getElementById('pdf-modal');
                const pdfFrame = document.getElementById('pdf-frame');
                
                pdfFrame.src = filePath; // Load the file path in the iframe
                modal.style.display = 'block';
            }
            
            function closeModal() {
                const modal = document.getElementById('pdf-modal');
                modal.style.display = 'none';
                document.getElementById('pdf-frame').src = ''; // Clear iframe src on close
            }   
        </script>
        
        <?php getSectionID(); ?>
        <?php checkUser(); ?>
        <?php getProjectInfo(); ?>
        <!--<?php viewComments(null); ?>-->
        
        
        <?php 
            // Check if the form has been submitted and a file is uploaded
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if(isset($_POST["taskID"])){
                    $_SESSION["taskID"] = $_POST["taskID"];
                    getSectionID();
                }
                
                else if(isset($_POST["panelComments"])){
                    viewComments($_POST["panelComments"]);
                }
            }

        ?>
        
        <?php require 'footer.php'; ?>
    </body>
</html>
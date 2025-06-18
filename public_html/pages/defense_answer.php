<?php
    require "connection.php";
    session_start();
    
    $_SESSION["canViewFile"] = true;
    
    $yearLevel = 0;
    $isPanelist = false;
    $panelLevel = 0;
    
    $selectedPanel = isset($_POST["panelComments"]) ? $_POST["panelComments"] : '';
    
    function checkUser(){
        global $conn, $isPanelist, $panelLevel;
        
        try{
            $sql = "SELECT * FROM panelists WHERE panelistID = ? AND projectID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"], $_SESSION["projectIDValue"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT cp.*, s.yearLevel FROM capstone_projects cp JOIN sections s ON cp.sectionID = s.sectionID WHERE cp.projectID = ? AND cp.academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["acadYearValue"]]);
            $capResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo '
                <script>
                    console.log("'.$_SESSION["acadYearValue"].'")
                </script>
            ';
            
            if($result){
                $isPanelist = true;
                
                $panelLevel = $result["level"];
                
                if($panelLevel >= 2){ //If user is the chairman panelist
   
                     
                     if($capResult["defense"] == "redefense"){
                             //Make the radio button for redefense selected
                             
                             echo '<script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const verdictSection = document.getElementById("verdict-section");
                            
                                    verdictSection.innerHTML += "<input type=\"radio\" id=\"approve\" name=\"verdict\" value=\"approved\">" +
                                        "<label for=\"approve\" style=\"font-size: 20px;\">Approve</label>" +
                                        
                                        "<input type=\"radio\" id=\"approve-revision\" name=\"verdict\" value=\"approved with revisions\">" +
                                        "<label for=\"approve-revision\" style=\"font-size: 20px;\">Approve with revisions</label>" +
                                        
                                        "<input type=\"radio\" id=\"redefense\" name=\"verdict\" value=\"redefense\" checked>" +
                                        "<label for=\"redefense\" style=\"font-size: 20px;\">Redefense</label>";
                                });
                            </script>';
                            
                            echo '<script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const buttonSection = document.getElementById("button-section");
                            
                                    buttonSection.innerHTML += "<button name=\"submitBtn\" id=\"submitBtn\" class=\"verdict-btn\"></i>Re-Submit Verdict</button>" +
                                                                "<h2 style=\"text-align: center; color: red;\">Previous Verdict is Redefense</h2>";
                                });
                            </script>';
                    }
                         
                    else if($capResult["defense"] == "pending"){ //If it is still pending for verdict
                    
                         echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const verdictSection = document.getElementById("verdict-section");
                        
                                verdictSection.innerHTML += "<input type=\"radio\" id=\"approve\" name=\"verdict\" value=\"approved\">" +
                                    "<label for=\"approve\" style=\"font-size: 20px;\">Approve</label>" +
                                    
                                    "<input type=\"radio\" id=\"approve-revision\" name=\"verdict\" value=\"approved with revisions\">" +
                                    "<label for=\"approve-revision\" style=\"font-size: 20px;\">Approve with revisions</label>" +
                                    
                                    "<input type=\"radio\" id=\"redefense\" name=\"verdict\" value=\"redefense\">" +
                                    "<label for=\"redefense\" style=\"font-size: 20px;\">Redefense</label>";
                            });
                        </script>';
                    
                        echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const buttonSection = document.getElementById("button-section");
                        
                                buttonSection.innerHTML += "<button name=\"submitBtn\" id=\"submitBtn\" class=\"verdict-btn\"></i>Submit Verdict</button>";
                            });
                        </script>';
                    }
                    
                    else{
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
                                    } else if (verdict == "approved with revisions" || verdict == "approved") {
                                        cardColor = "#EAEAEA"; textColor = "#388e3c"; icon = "fa-check-circle";
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
                                
                                document.addEventListener("DOMContentLoaded", function() {
                                    const buttonSection = document.getElementById("dropdown");
                                    
                                    buttonSection.innerHTML += "<h1 style=\'color: green; font-weight: bold; margin-top: 20px; \'>Verdict Submitted ✅</h1>";
                                });
                            </script>';

                    }
                }
                
                else{ //If normal panelist and not chairman
                    
                    if($capResult["yearLevel"] == 3){
                        
                           echo '<script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const verdictSection = document.getElementById("verdict-section");
                        
                                    // Retrieve the title defense verdict dynamically
                                    const verdict = ' . json_encode($capResult["defense"]) . ';
                                    let cardColor = "#EAEAEA", textColor = "#333", icon = "fa-info-circle";
                                    
                                    console.log("'.$capResult["defense"].'");
                        
                                    // Set colors and icon based on the title defense verdict
                                    if (verdict == "redefense") {
                                        cardColor = "#EAEAEA"; textColor = "#d32f2f"; icon = "fa-redo";
                                    } else if (verdict == "approved with revisions" || verdict == "approved") {
                                        cardColor = "#EAEAEA"; textColor = "#388e3c"; icon = "fa-check-circle";
                                    } else if (verdict == "pending") {
                                        cardColor = "#EAEAEA"; textColor = "#ff9800"; icon = "fa-clock";
                                    }
                        
                                    // Adding the styled title verdict card
                                    verdictSection.innerHTML += 
                                        "<div class=\'verdict-card\' style=\'background-color:" + cardColor + "\'>" +
                                            "<h1 class=\'verdict-result\' style=\'color:" + textColor + "\'>" +
                                                "<i class=\'fas " + icon + "\'></i> " +
                                                "Title Defense Verdict is " + verdict +
                                            "</h1>" +
                                        "</div>";
                                });
                            </script>';
                    }
                    
                    else if($capResult["yearLevel"] == 4){
                        
                         echo '<script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const verdictSection = document.getElementById("verdict-section");
                            
                                        // Retrieve the final defense verdict dynamically
                                        const verdict = ' . json_encode($capResult["defense"]) . ';
                                        let cardColor = "#EAEAEA", textColor = "#333", icon = "fa-info-circle";
                            
                                        // Set colors and icon based on the final defense verdict
                                        if (verdict == "redefense") {
                                            cardColor = "#EAEAEA"; textColor = "#d32f2f"; icon = "fa-redo";
                                        } else if (verdict == "approved with revisions" || verdict == "approved") {
                                            cardColor = "#EAEAEA"; textColor = "#388e3c"; icon = "fa-check-circle";
                                        } else if (verdict == "pending") {
                                            cardColor = "#EAEAEA"; textColor = "#ff9800"; icon = "fa-clock";
                                        }
                            
                                        // Adding the styled final verdict card
                                        verdictSection.innerHTML += 
                                            "<div class=\'verdict-card\' style=\'background-color:" + cardColor + "\'>" +
                                                "<h1 class=\'verdict-result\' style=\'color:" + textColor + "\'>" +
                                                    "<i class=\'fas " + icon + "\'></i> " +
                                                    "Final Defense Verdict is " + verdict +
                                                "</h1>" +
                                            "</div>";
                                    });
                                </script>';
                        
                    }
                    
                    
                    $sql = "SELECT * FROM defense_answers WHERE projectID = ? AND panelistID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["userID"]]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if($result){
                        echo '<script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const buttonSection = document.getElementById("dropdown");
                                    
                                    buttonSection.innerHTML += "<h1 style=\'color: green; font-weight: bold; margin-top: 20px; \'>Comment Submitted ✅</h1>";
                                });
                            </script>';
                    }
                    
                    else{
                        echo '<script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const buttonSection = document.getElementById("dropdown");
                                    
                                    buttonSection.innerHTML += "<button name=\"commentBtn\" id=\"commentBtn\" class=\"verdict-btn\" style=\"margin-top: 20px;\"></i>Comment</button>";
                                });
                            </script>';
                    }
                }
            }
            
            else{ //If user is not a panelist
                
                if($capResult["yearLevel"] == 3){
                    
                      echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const verdictSection = document.getElementById("verdict-section");
                    
                                // Retrieve the title defense verdict dynamically
                                const verdict = ' . json_encode($capResult["defense"]) . ';
                                let cardColor = "#EAEAEA", textColor = "#333", icon = "fa-info-circle";
                    
                                // Set colors and icon based on the title defense verdict
                                if (verdict == "redefense") {
                                    cardColor = "#EAEAEA"; textColor = "#d32f2f"; icon = "fa-redo";
                                } else if (verdict == "approved with revisions" || verdict == "approved") {
                                    cardColor = "#EAEAEA"; textColor = "#388e3c"; icon = "fa-check-circle";
                                } else if (verdict == "pending") {
                                    cardColor = "#EAEAEA"; textColor = "#ff9800"; icon = "fa-clock";
                                }
                    
                                // Adding the styled title verdict card
                                verdictSection.innerHTML += 
                                    "<div class=\'verdict-card\' style=\'background-color:" + cardColor + "\'>" +
                                        "<h1 class=\'verdict-result\' style=\'color:" + textColor + "\'>" +
                                            "<i class=\'fas " + icon + "\'></i> " +
                                            "Title Defense Verdict is " + verdict +
                                        "</h1>" +
                                    "</div>";
                            });
                            
                            document.getElementById("comment").disabled = true;
                            document.getElementById("comment").placeholder = "Defense Verdict Comments/Suggestions..";
                        </script>';
                    
                }
                
                else if($capResult["yearLevel"] == 4){
                    
                    echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const verdictSection = document.getElementById("verdict-section");
                    
                                // Retrieve the final defense verdict dynamically
                                const verdict = ' . json_encode($capResult["defense"]) . ';
                                let cardColor = "#EAEAEA", textColor = "#333", icon = "fa-info-circle";
                    
                                // Set colors and icon based on the final defense verdict
                                if (verdict == "redefense") {
                                    cardColor = "#EAEAEA"; textColor = "#d32f2f"; icon = "fa-redo";
                                } else if (verdict == "approved with revisions" || verdict == "approved") {
                                    cardColor = "#EAEAEA"; textColor = "#388e3c"; icon = "fa-check-circle";
                                } else if (verdict == "pending") {
                                    cardColor = "#EAEAEA"; textColor = "#ff9800"; icon = "fa-clock";
                                }
                    
                                // Adding the styled title verdict card
                                verdictSection.innerHTML += 
                                    "<div class=\'verdict-card\' style=\'background-color:" + cardColor + "\'>" +
                                        "<h1 class=\'verdict-result\' style=\'color:" + textColor + "\'>" +
                                            "<i class=\'fas " + icon + "\'></i> " +
                                            "Final Defense Verdict is " + verdict +
                                        "</h1>" +
                                    "</div>";
                            });
                            
                            document.getElementById("comment").disabled = true;
                            document.getElementById("comment").placeholder = "Defense Verdict Comments/Suggestions..";
                        </script>';
                }
            
            
            //MAKE SEPERATE DROP DOWN SELECT FOR COMMENTS
            
                echo '<script>
                            document.getElementById("panelComments").disabled = false;
                            document.getElementById("panelComments").hidden = false;
                            document.getElementById("comment-label").hidden = false;
                            document.getElementById("comment-label").disabled = false;
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
                                    window.location.href = "/answer_defense";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/answer_defense";
                                }
                        });
                  </script>';
        }
    }
    
    
    
    function getProjectInfo(){
        global $conn, $isCoordinator, $yearLevel;
        
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
                $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["acadYearValue"]]);
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
                $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["acadYearValue"]]);
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
                                    window.location.href = "/answer_defense";
                                }
                                else if (result.isDismissed) {
                                    window.location.href = "/answer_defense";
                                }
                        });
                  </script>';
        }
    }

    
    
    function setVerdict($verdict, $comment){
        global $conn, $yearLevel, $panelLevel;
        
        try{
            $conn->beginTransaction();
            
            echo '
                <script>
                    console.log("'.$verdict.'");
                </script>
            ';

            $sql = "SELECT * FROM defense_answers WHERE projectID = ? AND panelistID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["userID"]]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
      
            if($result){ //Update ang previous na sagot kung meron na
                $sql = "UPDATE defense_answers SET answer = ?, comment = ? WHERE projectID = ? AND panelistID = ?";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([$verdict, $comment, $_SESSION["projectIDValue"], $_SESSION["userID"]]);
                
                if(!$result){
                    throw new Exception("Failed to insert defense answer");
                }
            }
            
            else{//Insert ng bagong sagot kung wala pang existing
                $sql = "INSERT INTO defense_answers (projectID, panelistID, level, answer, comment, category, academicYearID, date_answer) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["userID"], $panelLevel, $verdict, $comment, "defense", $_SESSION["acadYearValue"], date("Y-m-d H:i:s")]);
                
                if(!$result){
                    throw new Exception("Failed to insert defense answer");
                }
            }
                
                
            $result = true; //mag false to pag may nag fail na other result variable sa ibaba

            
            if($panelLevel >= 2){
                
                if($yearLevel == 3){
                    $sql = "UPDATE capstone_projects SET defense = ? WHERE projectID = ? AND academicYearID = ?";
                    $stmt = $conn->prepare($sql);
                    $result = $stmt->execute([$verdict, $_SESSION["projectIDValue"], $_SESSION["acadYearValue"]]);
                }
                
                else if($yearLevel == 4){
                    
                    $newStatus = "";
                    
                    if($verdict != "redefense"){
                        $newStatus = "finished";
                    }
                    
                    else{ //meaning redefense
                        $newStatus = "active";
                    }
                    
                     echo '
                    <script>
                        console.log("'.$verdict.'");
                    </script>
                    ';
                    
                     $sql = "UPDATE capstone_projects SET defense = ?, status = ? WHERE projectID = ? AND academicYearID = ?";
                     $stmt = $conn->prepare($sql);
                     $result = $stmt->execute([$verdict, $newStatus, $_SESSION["projectIDValue"], $_SESSION["acadYearValue"]]);
                     
                }
            }
            

            if($result){

                date_default_timezone_set('Asia/Manila');
                $date = date('Y-m-d H:i:s');
                
                $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                 $stmt = $conn->prepare($sql);
                 $stmt->execute([$_SESSION["userID"]]);
                 $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                 $panel_name = $user["firstname"] . " " . $user["surname"] ;
                 $desc = "Chairman Panelist: " . $panel_name . " provided their capstone defense verdict with: " . $verdict;
                
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
                
                 echo '
                    <script>
                        console.log("'.$verdict.'");
                    </script>
                    ';
                
                unset($_POST["submitBtn"]);
            
                echo '<script>
                        Swal.fire({
                             title: "Success",
                            text: "Capstone Defense Verdict Submitted!",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/answer_defense";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/answer_defense";
                                }
                        });
                </script>';
            }
            
            else{
                throw new Exception("Failed to update defense status of capstone project");
            }
        }
        
        
        catch(Exception $e){
             $conn->rollBack();
            
             unset($_POST["submitBtn"]);
            
             echo '<script>
                        Swal.fire({
                             title: "Error",
                            text: "Error Message:'.$e->getMessage().'",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/answer_defense";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/answer_defense";
                                }
                        });
                  </script>';
        }
    }
    
    function setComment($comment){
        global $conn, $yearLevel, $panelLevel;
        
        try{
            $conn->beginTransaction();
            

            $sql = "SELECT * FROM defense_answers WHERE projectID = ? AND panelistID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["userID"]]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
      
            if($result){ //Update ang previous na sagot kung meron na
                $sql = "UPDATE defense_answers SET answer = ?, comment = ? WHERE projectID = ? AND panelistID = ?";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute(["", $comment, $_SESSION["projectIDValue"], $_SESSION["userID"]]);
                
                if(!$result){
                    throw new Exception("Failed to insert defense answer");
                }
            }
            
            else{//Insert ng bagong sagot kung wala pang existing
                $sql = "INSERT INTO defense_answers (projectID, panelistID, level, answer, comment, category, academicYearID, date_answer) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["userID"], $panelLevel, "", $comment, "defense", $_SESSION["acadYearValue"], date("Y-m-d H:i:s")]);
                
                if(!$result){
                    throw new Exception("Failed to insert defense answer");
                }
            }
                
              
           
            if($result){
                
                $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["userID"]]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $panel_name = $user["firstname"] . " " . $user["surname"] ;
              
                $desc = "Panelist: " . $panel_name . " provided their comments/suggestions on the capstone defense";
                        
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
                
                unset($_POST["commentBtn"]);
            
                echo '<script>
                        Swal.fire({
                             title: "Success",
                            text: "Capstone Defense Comment Submitted!",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/answer_defense";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/answer_defense";
                                }
                        });
                </script>';
            }
            
            else{
                throw new Exception("Failed to update defense comment of capstone project");
            }
        }
        
        
        catch(Exception $e){
             $conn->rollBack();
            
             unset($_POST["commentBtn"]);
            
             echo '<script>
                        Swal.fire({
                             title: "Error",
                            text: "Error Message:'.$e->getMessage().'",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/answer_defense";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/answer_defense";
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
            $stmt->execute([$_SESSION["projectIDValue"]]);
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
                                    window.location.href = "/answer_defense";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/answer_defense";
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
                    $stmt->execute([$_SESSION["projectIDValue"], 2]);
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
                    $stmt->execute([$_SESSION["projectIDValue"], $panelID]);
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
            $stmt->execute([$_SESSION["projectIDValue"]]);
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
                        <h3 id="sectionText">BSIT 3C-G2 <p></p> Group 2</h3>
                             <div class="bgs">
                        <div class="folder">
                       
                         <div class="circle"> </div>
                    </div>
                        <div class="group-details">
                             <p id="specializationText" class="service-management">Service Management</p>
                            <p id="acadyearText" class="semester-info">2023-2024 (2nd Semester)</p>
                             
                             
                           
                       
        </div></div></div>
        
                    <div class="project-title">
                        <p id="titleText">Payroll Management System</p>
                    </div>
        
                    <div class="verdict">
                        <textarea id="comment" name="comment" class="verdict-textbox" placeholder="Enter verdict and comments here..."></textarea>
                    </div>
                    
                    <div id="dropdown" class="semester-container" style="display: flex; align-items: center; gap: 250px;">
                        <!-- Label and Dropdown Container -->
                        <div style="display: flex; flex-direction: row; align-items: center; gap: 10px; margin-top: 25px;">
                            <label id="comment-label" for="panelComments" style="font-size: 18px;" hidden disabled>Select Panelist Comment: </label>
                            
                                <select name="panelComments" id="panelComments" class="semester-container" style="font-size: 18px;" onchange="this.form.submit();">
                                    <option value="none">Select A Panelist</option>;
                                    <?php getPanelists(); ?>
                                </select>
                        </div>
                        
                        <!-- Dynamically add button -->
                      
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
                  
                
                    
                    <div id="verdict-section" class="verdict-section">
                        <!-- Verdict content will be injected here -->
                    </div>
                    
                    <br>
                    <br>
                    
                    <div id="button-section" class="left-buttons">
                        <!--<button name="submitBtn" id="submitBtn" class="verdict-btn"></i>Submit Verdict</button>-->
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
        
        <?php checkUser(); ?>
        <?php getProjectInfo(); ?>

        
        <?php 
            // Check if the form has been submitted and a file is uploaded
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if(isset($_POST["taskID"])){
                    $_SESSION["taskID"] = $_POST["taskID"];
                }
                
                else if(isset($_POST["submitBtn"])){
                    if(isset($_POST["verdict"]) && isset($_POST["comment"])){
                        setVerdict($_POST["verdict"], $_POST["comment"]);
                    }
                }
                
                else if(isset($_POST["commentBtn"])){
                    if(isset($_POST["comment"])){
                        setComment($_POST["comment"]);
                    }
                }
                
                else if(isset($_POST["panelComments"])){
                     echo '
                            <script>
                                console.log("'.$_POST["panelComments"].'");
                            </script>
                        ';
                    viewComments($_POST["panelComments"]);
                }
            }

        ?>
        
        <?php require 'footer.php'; ?>
    </body>
</html>

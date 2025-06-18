<?php
  //take note meron palang session na name is projectIDValue pwedeng alternative para di na ulit mag sql query to get projectID

  $_SESSION["titleNum"]; //Make sure na naka declare first para accessible si session
  
  if (!isset($_SESSION["titleNum"])) {
      $_SESSION["titleNum"] = "1"; //default value na 1 para pag first time mag bukas nung page may value na at hindi empty
  }
  
  function showGroupInfo(){
      global $conn;
      
      try{
          $sql = "SELECT sectionID FROM students WHERE id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$_SESSION["userID"]]);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          
          $sectionID = $result["sectionID"];
          
          $_SESSION["sectionID"] = $sectionID;
          
          if($result){
              $sql = "SELECT * FROM sections s
                      JOIN capstone_projects cp ON cp.sectionID = s.sectionID
                      JOIN academic_year ay ON cp.academicYearID = ay.id
                      JOIN users u ON cp.coordinatorID = u.id
                      WHERE s.sectionID = ?
              ";
              
              $stmt = $conn->prepare($sql);
              $stmt->execute([$sectionID]);
              $result = $stmt->fetch(PDO::FETCH_ASSOC);
              
              if($result){
                  $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"] . ", Group " . $result["groupNum"] . ", ". $result["specialization"];
                  
                  $semester = $result["semester"] == 1 ? "1st Semester" : ($result["semester"] == 2 ? "2nd Semester" : "Unknown Semester");
                  
                  $year = $result["start_year"] . "-" . $result["end_year"] . " (" . $semester . ") ";
                  
                  $coordinator = $result["surname"] . ", " . $result["firstname"];
                  
                  echo'
                      <script>
                           document.getElementById("courseInfoID").innerHTML = "'. htmlspecialchars($section, ENT_QUOTES) .'";
                           document.getElementById("coordinatorInfoID").innerHTML = "Capstone Coordinator: '. htmlspecialchars($coordinator, ENT_QUOTES) .'";
                           document.getElementById("semesterInfoID").innerHTML = "'. htmlspecialchars($year, ENT_QUOTES) .'";
                           
                           console.log("'.addslashes($section).'");
                           console.log("'.addslashes($year).'");
                      </script>
                    ';
              }
              
              else{
                  throw new Exception("error 2");
              }
          }
          
           else{
                  throw new Exception("error 1");
              }
      }
      
      catch(Exception $e){
          echo'
              <script>
                   console.log("'.addslashes($e->getMessage()).'");
              </script>
            ';
      }
  }
  
  function showTitleValues(){
      global $conn;
      
      try{
          $sql = "SELECT projectID from students WHERE id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$_SESSION["userID"]]);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          
          if($result){
              
              $projectID = $result["projectID"];
              
              $_SESSION["projectID"] = $projectID;
             
              $sql = "SELECT * FROM title_proposal WHERE projectID = ? AND titleNum = ?";
              $stmt = $conn->prepare($sql);
              $stmt->execute([$projectID, $_SESSION["titleNum"]]);
              $result = $stmt->fetch(PDO::FETCH_ASSOC);
              
              if($result){
                  $title = htmlspecialchars($result["title"], ENT_QUOTES); // Only escape special characters without adding <br> tags
                  $titleDesc = htmlspecialchars($result["title_description"], ENT_QUOTES); // Only escape special characters without adding <br> tags
                  $intro = htmlspecialchars($result["introduction"], ENT_QUOTES); // Only escape special characters without adding <br> tags
                  $background = htmlspecialchars($result["background"], ENT_QUOTES); // Only escape special characters without adding <br> tags    
                  $importance = htmlspecialchars($result["importance"], ENT_QUOTES); // Only escape special characters without adding <br> tags
                  $scope = htmlspecialchars($result["scope"], ENT_QUOTES); // Only escape special characters without adding <br> tags
                  
                  
                    
                    
                    
                  echo'
                      <script>
                            document.getElementById("titleBoxID").value = ' . json_encode($title) . ';
                            document.getElementById("titleDescBoxID").value = ' . json_encode($titleDesc) . ';
                            document.getElementById("introBoxID").value = ' . json_encode($intro) . ';
                            document.getElementById("backgroundBoxID").value = ' . json_encode($background) . ';
                            document.getElementById("importanceBoxID").value = ' . json_encode($importance) . ';
                            document.getElementById("scopeBoxID").value = ' . json_encode($scope) . ';
                      </script>
                    ';
              }
              
          }
          
          else{
              throw new Exception("error here 1");
          }
          
      }
      
      catch(Exception $e){
          echo'
              <script>
                   console.log("'.addslashes($e->getMessage()).'");
              </script>
            ';
      }
  }
  
  function showTrackingNumber(){
      global $conn;
      
      try{
          $sql = "SELECT * FROM tracking WHERE projectID = ? AND taskID = ?";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$_SESSION["projectID"], $_SESSION["taskID"]]);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          
          if($result){
              $trackingNum = $result["number"];
              
              $_SESSION["trackingNum"] = $trackingNum;
              
              echo'
                    <script>
                        document.getElementById("trackingNum-label").innerHTML = "'.$trackingNum.'";
                    </script>
              ';
          }
          
          else{
              throw new Exception("Error getting tracking number");
          }
      }
      
      
      catch(Exception $e){
         echo'
              <script>
                   console.log("'.addslashes($e->getMessage()).'");
              </script>
            '; 
      }
  }
  
  function saveValues(){
      global $conn;
      
      try{
          $conn->beginTransaction();
          
          $sql = "UPDATE title_proposal SET title = ?, title_description = ?, introduction = ?, background = ?, importance = ?, scope = ? WHERE projectID = ? AND titleNum = ?";
          $stmt = $conn->prepare($sql);
          $result = $stmt->execute([$_SESSION["title"], $_SESSION["title_desc"],  $_SESSION["intro"], $_SESSION["background"], $_SESSION["importance"], $_SESSION["scope"] , $_SESSION["projectID"], $_SESSION["titleNum"]]);
          
          if($result){
              $sql = "SELECT * FROM users WHERE id = ?";
              $stmt = $conn->prepare($sql);
              $stmt->execute([$_SESSION["userID"]]);
              $result = $stmt->fetch(PDO::FETCH_ASSOC);
              
              if($result){
                    
                    $firstname = $result["firstname"];
                    $surname = $result["surname"];
                    
                    $sql = "INSERT INTO activity_logs (userID, projectID, taskID, description, date, time, trackingNum) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    
                    $description = "Student: " . $surname . ", " . $firstname . " has edited the title evaluation: title number: " . $_SESSION["titleNum"];
                    
                    date_default_timezone_set('Asia/Manila');
                    
                    $result = $stmt->execute([
                        $_SESSION["userID"], 
                        $_SESSION["projectID"], 
                        $_SESSION["taskID"], 
                        $description, 
                        date("Y-m-d"), 
                        date("H:i:s"), 
                        $_SESSION["trackingNum"]
                    ]);
                      
                      
                    $sql = "UPDATE tracking SET status = ? WHERE number = ? AND status = 'started'";
                    $stmt = $conn->prepare($sql);
                    $result = $stmt->execute(["submitted", $_SESSION["trackingNum"]]);
                    
                      if($result){
                          
                          $sql = "INSERT INTO title_proposal_inputs (projectID, titleNum, title, title_description, introduction, background, importance, scope, input_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                          
                          $stmt = $conn->prepare($sql);
                          $result = $stmt->execute([$_SESSION["projectID"], $_SESSION["titleNum"], $_SESSION["title"], $_SESSION["title_desc"],  $_SESSION["intro"], $_SESSION["background"], $_SESSION["importance"], $_SESSION["scope"], date("Y-m-d H:i:s")]);
                          
                          if($result){
                              
                                $sql = "SELECT * FROM sections s
                                      JOIN capstone_projects cp ON cp.sectionID = s.sectionID
                                      JOIN academic_year ay ON cp.academicYearID = ay.id
                                      JOIN users u ON cp.coordinatorID = u.id
                                      WHERE s.sectionID = ?
                                ";
                              
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$_SESSION["sectionID"]]);
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                if($result){
                                    
                                    $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                                    
                                    $sql =  "SELECT groupNum FROM capstone_projects WHERE projectID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute([$_SESSION["projectID"]]);
                                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if($result){
                                        
                                        $groupNum = $result["groupNum"];
                                        
                                        $desc =  $section . " Group " . $groupNum . " updated their title evaluation content" ;
                                        
                                        date_default_timezone_set('Asia/Manila');
                                        $date = date('Y-m-d H:i:s');
                                        
                                        
                                        
                                        $sql = "SELECT panelistID FROM panelists WHERE projectID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute([$_SESSION["projectID"]]);
                                        $panelists = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if(count ($panelists) >= 1){
                                            foreach($panelists as $panelist){
                                                $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                                $stmt = $conn->prepare($sql);
                                                $result = $stmt->execute([$panelist["panelistID"], $desc, $date]);
                                                
                                                if(!$result){
                                                    throw new Exception("Failed to insert notifications");  
                                                }
                                            }
                                        }
                                        
                                        
                                        $sql = "SELECT adviserID FROM advisers WHERE projectID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute([$_SESSION["projectID"]]);
                                        $advisers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if(count ($advisers) >= 1){
                                            foreach($advisers as $adviser){
                                                $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                                $stmt = $conn->prepare($sql);
                                                $result = $stmt->execute([$adviser["adviserID"], $desc, $date]);
                                                
                                                if(!$result){
                                                    throw new Exception("Failed to insert notifications");  
                                                }
                                            }
                                        }
                                        
                                        $sql = "SELECT facultyID FROM coordinators WHERE sectionID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute([$_SESSION["sectionID"]]);
                                        $coordinator = $stmt->fetch(PDO::FETCH_ASSOC);
                                        
                                        if($coordinator){

                                            $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                            $stmt = $conn->prepare($sql);
                                            $result = $stmt->execute([$coordinator["facultyID"], $desc, $date]);
                                            
                                            if(!$result){
                                                throw new Exception("Failed to insert notifications");  
                                            }
                                            
                                        }
                                        
                                         $sql = "SELECT * FROM users WHERE id = ?";
                                         $stmt = $conn->prepare($sql);
                                         $stmt->execute([$_SESSION["userID"]]);
                                         $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                          
                                         $firstname = $result["firstname"];
                                         $surname = $result["surname"];
                                         $middlename = $result["middlename"];
                                                
                                         $action = "". $surname . ", " . $firstname . " " . $middlename . " edited their title evaluation: title number: " . $_SESSION["titleNum"];
                    
                                        $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                                        $stmt = $conn->prepare($sql);
                                        $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                                        
                                        if(!$result){
                                            throw new Exception("Failed to insert action logs");  
                                        }
                                        

                                          $conn->commit();
                                                
                                          unset($_POST["save-button"]);
                                          
                                          unset($_SESSION["title"]);
                                          unset($_SESSION["title_desc"]);
                                          unset($_SESSION["intro"]);
                                          unset($_SESSION["background"]);
                                          unset($_SESSION["importance"]);
                                          unset($_SESSION["scope"]);
                                         
                                          echo '<script>
                                                Swal.fire({
                                                     title: "Success",
                                                    text: "Title Number '.addslashes($_SESSION["titleNum"]) . ' Succesfuly Saved!",
                                                    icon: "success",
                                                    confirmButtonText: "OK"
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location.href = "/edit_title_evaluation";
                                                    }
                                                    
                                                    else if (result.isDismissed) {
                                                         window.location.href = "/edit_title_evaluation";
                                                    }
                                                });
                                              </script>';
                                        
                                    }
                                }
                          }
                          
                          else{
                              throw new Exception("Failed to insert into title proposal input history");  
                          }
                      }
                      
                      else{
                          throw new Exception("Failed to update activity logs");
                      }
              }
          }
      }
      
      catch(Exception $e){
         echo'
              <script>
                   console.log("'.addslashes($e->getMessage()).'");
              </script>
            '; 
            
        $conn->rollBack();
            
        unset($_POST["save-button"]);
            
            echo '<script>
                Swal.fire({
                     title: "Error Saving Title Number '.addslashes($_SESSION["titleNum"]) . ' ",
                    text: "Error: '.addslashes($e->getMessage()).'",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "/edit_title_evaluation";
                    }
                    
                    else if (result.isDismissed) {
                         window.location.href = "/edit_title_evaluation";
                    }
                });
            </script>';
      }
  }
  
  function getPanelAnswers(){
      global $conn;
      
        // Fetch panelist answers
        $sql = "SELECT * FROM title_proposal_answers ta
                JOIN users u ON ta.panelistID = u.id
                WHERE ta.projectID = ? AND ta.titleNum = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_SESSION["projectID"], $_SESSION["titleNum"]]);
    
        // Use fetchAll and loop over the results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as $result) {
            $panelName = $result["surname"] . ", " . $result["firstname"] . " " . $result["middlename"];
            $answer = $result["answer"];
            $comment = $result["comment"];
            $panelLevel = $result["level"];
        
            // Display panelist evaluation answers
            
            if($answer == "accepted"){ // Color it green if accepted
            
                //Use of pre-wrap to maintain original spaces of comment
                
                if($panelLevel <= "1"){
                    echo '
                        <button class="collapsible" aria-expanded="false" type="Button" style="background-color: green;">Panelist: ' . htmlspecialchars($panelName) . ' ---- Evaluation Answers</button>
                    ';
                }
                
                else{
                    echo '
                        <button class="collapsible" aria-expanded="false" type="Button" style="background-color: green;">CHAIRMAN Panelist: ' . htmlspecialchars($panelName) . ' ---- Evaluation Answers</button>
                    ';
                }
                
                echo '
                    <div class="content-collapsible">
                       <p name="answerBox" id="answerBoxID">
                            Answer: <br><br>
                            <span style="color: green; font-weight: bold">' . htmlspecialchars(strtoupper($answer)) . '</span><br><br>
                            
                            Comment: <br><br>
                            <span style="white-space: pre-wrap;">' . htmlspecialchars($comment) . '</span>
                       </p>
                    </div>
                ';
            }
            
            else if($answer == "rejected"){ // Color it red if rejected
            
                //Use of pre-wrap to maintain original spaces of comment
                
                if($panelLevel <= "1"){
                    echo '
                        <button class="collapsible" aria-expanded="false" type="Button" style="background-color: red;">Panelist: ' . htmlspecialchars($panelName) . ' ---- Evaluation Answers</button>
                    ';
                }
                
                else{
                    echo '
                        <button class="collapsible" aria-expanded="false" type="Button" style="background-color: red;">CHAIRMAN Panelist: ' . htmlspecialchars($panelName) . ' ---- Evaluation Answers</button>
                    ';
                }
                
                echo '
                    <div class="content-collapsible">
                       <p name="answerBox" id="answerBoxID">
                            Answer: <br><br>
                            <span style="color: red; font-weight: bold">' . htmlspecialchars(strtoupper($answer)) . '</span><br><br>
                            
                            Comment: <br><br>
                            <span style="white-space: pre-wrap;">' . htmlspecialchars($comment) . '</span>
                       </p>
                    </div>
                ';
            }
            
            else if($answer == "needs improvement"){ // Color it orange if needs improvement
            
                //Use of pre-wrap to maintain original spaces of comment
                if($panelLevel <= "1"){
                    echo '
                        <button class="collapsible" aria-expanded="false" type="Button" style="background-color: #FF6600;">Panelist: ' . htmlspecialchars($panelName) . ' ---- Evaluation Answers</button>
                    ';
                }
                
                else{
                    echo '
                        <button class="collapsible" aria-expanded="false" type="Button" style="background-color: #FF6600;">CHAIRMAN Panelist: ' . htmlspecialchars($panelName) . ' ---- Evaluation Answers</button>
                    ';
                }
                
                echo '
                    <div class="content-collapsible">
                       <p name="answerBox" id="answerBoxID">
                            Answer: <br><br>
                            <span style="color: #FF6600; font-weight: bold">' . htmlspecialchars(strtoupper($answer)) . '</span><br><br>
                            
                            Comment: <br><br>
                            <span style="white-space: pre-wrap;">' . htmlspecialchars($comment) . '</span>
                       </p>
                    </div>
                ';
            }
        }
  }
  
  
  
  function checkTitleStatus(){
    global $conn;

    $projectID = $_SESSION["projectID"];

    // Log the selected title number in JavaScript
    echo '
        <script>
            console.log("Selected Title Number With session: ' . htmlspecialchars($_SESSION["titleNum"], ENT_QUOTES) . '");
        </script>
    ';

    // Fetch the title proposal based on projectID and titleNum
    $sql = "SELECT * FROM title_proposal WHERE projectID = ? AND titleNum = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$projectID, $_SESSION["titleNum"]]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $titleResult = $result["result"];

        echo '
            <script>
                console.log("Result is: ' . htmlspecialchars($titleResult, ENT_QUOTES) . '");
            </script>
        ';

        if ($titleResult == "pending") {
            
            getPanelAnswers();
            
            $sql = "SELECT * FROM title_proposal WHERE projectID = ? AND result = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$projectID, "accepted"]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
             if($result){
                 
                //If this title number is still pending, but there is already an accepted result from the chairman panelist, then a title has already been selected for the group
                echo '
                    <h1 style="margin-top: 80px; font-size: 40px; font-weight: bold; text-align: center; color: green;">A Title Has Already Been Selected For This Group &#x2705;</h1>
                ';
             }
             
             else{
                 $sql = "SELECT * FROM title_proposal_answers WHERE projectID = ? AND titleNum = ?";
                 $stmt = $conn->prepare($sql);
                 $stmt->execute([$projectID, $_SESSION["titleNum"]]);
                 $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                 
                 if(count($results) >= 1){
                     //Dont display the save button
                     
                     foreach($results as $result){
                         if($result["answer"] == "needs improvement" && $result["level"] >= 2){
                              echo '
                                <div class="button-container">
                                    <button class="save-button" name="save-button" id="saveButtonID">Save</button>
                                </div>
                              ';
                              
                              break;
                         }
                     }
                 }
                 
                 else{
                     // Display Save button if the title is pending and no answer yet from any panelists
                    echo '
                        <div class="button-container">
                            <button class="save-button" name="save-button" id="saveButtonID">Save</button>
                        </div>
                    ';
                 }
             }
        } 
        
        else {
            getPanelAnswers();
            
            
            //THIS IS INDEPENDENT FROM THE BLOCK OF CODES ABOVE DO NOT CONFUSE IT
            if($titleResult == "accepted"){
                
                $display = "<span style='color: green;'>accepted</span>";
                
                echo'
                     <h1 style="margin-top: 30px; font-size: 40px; font-weight: bold; text-align: center;"> Evaluation Result: '.strtoupper($display).' &#x2705</h1>
                ';
            }
            
            else if($titleResult == "rejected"){
                
                $display = "<span style='color: red;'>rejected ✕</span>";
                
                echo'
                     <h1 style="margin-top: 30px; font-size: 40px; font-weight: bold; text-align: center;"> Evaluation Result: '.strtoupper($display).'</h1>
                ';
            }
            
            else if($titleResult == "needs improvement"){
                
                $display = "<span style='color: #FF6600;'>needs improvement ⚠</span>";
                
                echo'
                     <h1 style="margin-top: 30px; font-size: 40px; font-weight: bold; text-align: center;"> Evaluation Result: '.strtoupper($display).' </h1>
                ';
            }
        }
    }
}

  
    
?>





<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        
        <link rel="stylesheet" href="pages/title_evaluation.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <script src="pages/session_tracker.js"></script>

        <title>Title Evaluation</title>
    </head>
    
    <body>

        <?php 
            require_once "connection.php";
            session_start();
        ?>
        
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>

        <div class="heading-container">
            <h2 class="section-title">Title Evaluations</h2>
            
            <div>
                <h3 class="tracker-info" style="color: black;">Tracking Number: </h3>
                <h3 id="trackingNum-label" class="tracker-info"></h3>
                <button class="copy-button" onclick="copyTrackingNum()">
                    <img src="pages/images/copy.png" alt="Copy Icon">
                </button>
            </div>
        </div>

        <p class="course-info" id="courseInfoID"></p> 
        <p class="course-info" id="coordinatorInfoID"></p>
        <p class="semester-info" id="semesterInfoID"></p>
        
        <div class="title-option-container">
            <form action="" id="selectFormID" method="POST">
                <div class="title-dropdown">
                    <select id="title-select" name="titleNumBox">
                        <option value="1" <?php echo isset($_SESSION["titleNum"]) && $_SESSION["titleNum"] == "1" ? 'selected' : ''; ?>>Title Number 1</option>
                        <option value="2" <?php echo isset($_SESSION["titleNum"]) && $_SESSION["titleNum"] == "2" ? 'selected' : ''; ?>>Title Number 2</option>
                        <option value="3" <?php echo isset($_SESSION["titleNum"]) && $_SESSION["titleNum"] == "3" ? 'selected' : ''; ?>>Title Number 3</option>
                    </select>
                </div>
            </form>
            
            <button class="refresh-button" onclick="location.reload()">
                    <img src="pages/images/refresh.png" alt="Refresh Icon">
                    Refresh
            </button>
        </div>
        
        
        <form action="" method="POST" id="evalFormID">
            <button class="collapsible" aria-expanded="false" type="Button">Capstone Title</button>
            <div class="content-title-collapsible">
                <textarea style="height: 50px; padding: 10px;" name="titleBox" id="titleBoxID" placeholder="Capstone Title.."></textarea>
            </div>

            <button class="collapsible" aria-expanded="false" type="Button">Title Description</button>
            <div class="content-title-collapsible">
                <textarea style="height: 50px; padding: 10px;" name="titleDescBox" id="titleDescBoxID" placeholder="Capstone Title DESCRIPTION.."></textarea>
            </div>
            
            <button class="collapsible" aria-expanded="false" type="Button">Introduction</button>
            <div class="content-collapsible">
                <textarea style="padding: 10px;" name="introBox" id="introBoxID" placeholder="Introduction.."></textarea>
            </div>
    
            <button class="collapsible" aria-expanded="false" type="Button">Background of the Study</button>
            <div class="content-collapsible">
                <textarea style="padding: 10px;" name="backgroundBox" id="backgroundBoxID" placeholder="Background of the Study.."></textarea>
            </div>
    
            <button class="collapsible" aria-expanded="false" type="Button">Importance of the Study</button>
            <div class="content-collapsible">
                <textarea style="padding: 10px;" name="importanceBox" id="importanceBoxID" placeholder="Importance of the Study.."></textarea>
            </div>
    
            <button class="collapsible" aria-expanded="false" type="Button">Scope and Limitations</button>
            <div class="content-collapsible">
                <textarea style="padding: 10px;" name="scopeBox" id="scopeBoxID" placeholder="Scope and Limitations.."></textarea>
            </div>
            
        <form>
            
        <?php 
            showGroupInfo();
            showTitleValues();
            showTrackingNumber();
        ?>
            
        <div id="result-content">
            <?php
                checkTitleStatus();
            ?>
        </div>
        
        
        <!--In this file always put the java script tag to the very last, in order for the php function to load properly first-->
        
        <script>
        
            function copyTrackingNum() {
                // Get the text element
                var textElement = document.getElementById("trackingNum-label");
                
                // Create a temporary input element to copy the text
                var tempInput = document.createElement("input");
                tempInput.value = textElement.innerText;
                document.body.appendChild(tempInput);
                
                // Select the text and copy it to the clipboard
                tempInput.select();
                document.execCommand("copy");
                
                // Remove the temporary input
                document.body.removeChild(tempInput);
                
                // Alert the user (optional)
                alert("Copied Tracking Number: " + tempInput.value);
            }
   
            document.addEventListener('DOMContentLoaded', function() {
                    const dropdown = document.getElementById('title-select');
                    const form = document.getElementById('selectFormID');
                
                    dropdown.addEventListener('click', function() {
                         // Check if the dropdown is not yet expanded
                        if (!dropdown.classList.contains('open')) {
                            dropdown.classList.add('open');
                        } else {
                            // Submit the form when an option is selected
                            form.submit();
                        }
                    });
            });
                
            document.addEventListener("DOMContentLoaded", function() {
                var coll = document.getElementsByClassName("collapsible");
                for (var i = 0; i < coll.length; i++) {
                    coll[i].addEventListener("click", function() {
                        this.classList.toggle("active");
                        var content = this.nextElementSibling;
                        this.setAttribute("aria-expanded", content.style.display === "block");
                        if (content.style.display === "block") {
                            content.style.display = "none";
                        } else {
                            content.style.display = "block";
                        }
                    });
                }
            });
            
            
           document.addEventListener("DOMContentLoaded", function() {
                    var submitButton = document.getElementById("saveButtonID");
                    
                    if (submitButton) { // Ensure the element exists before adding the event listener
                        submitButton.addEventListener("click", function() {
                            document.getElementById("evalFormID").submit();
                        });
                    } 
                    
                    else {
                        //Do nothing for now
                    }
            });
        </script>
        
        
        <?php 
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                
                if(isset($_POST["save-button"])){
                    if (!empty($_SESSION["titleNum"]) && 
                        !empty($_POST["titleBox"]) && 
                        !empty($_POST["titleDescBox"]) && 
                        !empty($_POST["introBox"]) && 
                        !empty($_POST["backgroundBox"]) && 
                        !empty($_POST["importanceBox"]) && 
                        !empty($_POST["scopeBox"])) {
                    
                        // Store values in session if all fields are not empty
                        $_SESSION["title"] = $_POST["titleBox"];
                        $_SESSION["title_desc"] = $_POST["titleDescBox"];
                        $_SESSION["intro"] = $_POST["introBox"];
                        $_SESSION["background"] = $_POST["backgroundBox"];
                        $_SESSION["importance"] = $_POST["importanceBox"];
                        $_SESSION["scope"] = $_POST["scopeBox"];
                    
                        // Call functions to save and display the values
                        saveValues();
                        showTitleValues();
                    } 
                    
                    else {
                            // Handle case when one or more fields are empty
                            echo '
                                <script>
                                
                                
                                    //RE POPULATE THE TEXT AREAS WITH THE USER INPUTS
                                    
                                    document.getElementById("titleBoxID").value = "' . htmlspecialchars($_POST["titleBox"]) . '";
                                    document.getElementById("titleDescBoxID").value = "' . htmlspecialchars($_POST["titleDescBox"]) . '";
                                    document.getElementById("introBoxID").value = "' . htmlspecialchars($_POST["introBox"]) . '";
                                    document.getElementById("backgroundBoxID").value = "' . htmlspecialchars($_POST["backgroundBox"]) . '";
                                    document.getElementById("importanceBoxID").value = "' . htmlspecialchars($_POST["importanceBox"]) . '";
                                    document.getElementById("scopeBoxID").value = "' . htmlspecialchars($_POST["scopeBox"]) . '";
                                      
                        
                                    Toastify({
                                        text: "Please complete all title evaluation inputs for this title",
                                        duration: 3000,
                                        close: true,
                                        gravity: "bottom",
                                        position: "center",
                                        offset: { y: "100px" },
                                        backgroundColor: "red",
                                    }).showToast();
                                </script>
                            ';
                    }
                }
                
                
                if(isset($_POST["titleNumBox"])){
                     $selectedValue = $_POST["titleNumBox"];
                     
                     global $titleNum;
                    
                     $titleNum = $selectedValue;
                    
                     $_SESSION["titleNum"] = $selectedValue;
                     
                     // Use JavaScript to set the selected value of the select element
                    echo '
                        <script>
                            // Change the selected value of the select box based on the session value
                            document.getElementById("title-select").value = "' . addslashes($selectedValue) . '";
                            console.log("Title Number: ' . addslashes($_SESSION["titleNum"]) . '");
                            
                            document.getElementById("result-content").innerHTML = ""; 
                        </script>
                    ';
                     
                     showTitleValues();
                     checkTitleStatus();
                }
                
                if(isset($_POST["taskID"])){
                    $_SESSION["taskID"] = $_POST["taskID"];
                    
                    echo '
                        <script>
                                console.log("Task ID: ' . addslashes($_SESSION["taskID"]) . '");
                        </script>
                    ';
                    
                    showTrackingNumber();
                }
            }
        ?>

        <?php require 'footer.php'; ?>
    </body>
</html>

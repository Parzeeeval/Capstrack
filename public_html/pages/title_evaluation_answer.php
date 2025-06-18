<?php
  $_SESSION["titleNum"]; //Make sure na naka declare first para accessible si session
  $_SESSION["title_proposal_date"];
  
  $rejectCount;
  
  
  function getDates(){
      global $conn;
      
      try{
          $sql = "SELECT * FROM title_proposal_inputs WHERE projectID = ? AND titleNum = ? ORDER BY id DESC";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["titleNum"]]);
          $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          if(count ($results) >= 1){
              
              foreach($results as $result){
                     $id = $result["id"];
        
                     $date = $result["input_date"]; // Example: "2024-11-19 17:45:30"
                     $formattedDate = date("F j, Y -- g:i A", strtotime($date));
                     
                      
                    echo '
                        <option value="' . htmlspecialchars($id) . '" ' . $selected . '>'.htmlspecialchars($formattedDate).'</option>;
                    ';
              }
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
  
  
  
  function checkTracking(){
      global $conn;
      
      try{
          $conn->beginTransaction();
          
          $sql = "SELECT * FROM tracking WHERE projectID = ? AND taskID = ?";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["taskID"]]);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          
          if($result){
              $status = $result["status"];
              
              if($status == "submitted"){
                  $sql = "UPDATE tracking SET status = ? WHERE projectID = ? AND taskID = ?";
                  $stmt = $conn->prepare($sql);
                  $result = $stmt->execute(["evaluating", $_SESSION["projectIDValue"], $_SESSION["taskID"]]);
                  
                  if($result){
                      $conn->commit();
                  }
                  
                  else{
                      throw new Exception("Failed to update tracking status");
                  }
              }
              
              else{
                  //Do nothing
              }
          }
          
          else{
              throw new Exception("Failed to retrieve tracking status");
          }
          
      }
      
      catch(Exception $e){
          $conn->rollBack();
          
          echo'
              <script>
                   console.log("'.addslashes($e->getMessage()).'");
              </script>
            ';
      }
  }
  
  function showGroupInfo(){
      global $conn;
      
      try{
          $sectionID = $_SESSION["sectionID"];
          
          $sql = "SELECT * FROM sections s
                  JOIN capstone_projects cp ON cp.sectionID = s.sectionID
                  JOIN academic_year ay ON cp.academicYearID = ay.id
                  JOIN users u ON cp.coordinatorID = u.id
                  WHERE s.sectionID = ? AND cp.projectID = ?
          ";
          
          $stmt = $conn->prepare($sql);
          $stmt->execute([$sectionID, $_SESSION["projectIDValue"]]);
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
      global $conn, $rejectCount;
      
      try{

          $projectID = $_SESSION["projectIDValue"];

          
          $sql = "SELECT * FROM title_proposal WHERE projectID = ? AND titleNum = ?";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$projectID, $_SESSION["titleNum"]]);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          
          if($result){
              
              $titleResult = htmlspecialchars($result["result"]);
              
              $title = nl2br(htmlspecialchars($result["title"], ENT_QUOTES));
              $titleDesc = nl2br(htmlspecialchars($result["title_description"], ENT_QUOTES));
              $intro = nl2br(htmlspecialchars($result["introduction"], ENT_QUOTES));
              $background = nl2br(htmlspecialchars($result["background"], ENT_QUOTES));
              $importance = nl2br(htmlspecialchars($result["importance"], ENT_QUOTES));
              $scope = nl2br(htmlspecialchars($result["scope"], ENT_QUOTES));
              
              // Set placeholder values if any variable is empty
              $title = !empty($title) ? $title : "Capstone Title pending.."; //DO NOT CHANGE THIS, IF YOU DO THEN CTRL+F AND SEARCH FOR EVAL-VERIFY AND CHANGE ITS VALUE TOO
              $titleDesc = !empty($titleDesc) ? $titleDesc : "Capstone Title DESCRIPTION pending..";
              $intro = !empty($intro) ? $intro : "Introduction pending..";
              $background = !empty($background) ? $background : "Background of The Study pending..";
              $importance = !empty($importance) ? $importance : "Importance of The Study pending..";
              $scope = !empty($scope) ? $scope : "Scope and Limitations pending..";
            
              $_SESSION["title"] = $title;
              $_SESSION["title_desc"] = $titleDesc;
                
                
              echo'
                  <script>
                        document.getElementById("titleBoxID").innerHTML = ' . json_encode($title) . ';
                        document.getElementById("titleDescBoxID").innerHTML = ' . json_encode($titleDesc) . ';
                        document.getElementById("introBoxID").innerHTML = ' . json_encode($intro) . ';
                        document.getElementById("backgroundBoxID").innerHTML = ' . json_encode($background) . ';
                        document.getElementById("importanceBoxID").innerHTML = ' . json_encode($importance) . ';
                        document.getElementById("scopeBoxID").innerHTML = ' . json_encode($scope) . ';
                  </script>
                ';
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
  
  function showPreviousTitleValues($id){
      global $conn, $rejectCount;
      
      try{

          $sql = "SELECT * FROM title_proposal_inputs WHERE id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$id]);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          
          if($result){
              
              $titleResult = htmlspecialchars($result["result"]);
              
              $title = nl2br(htmlspecialchars($result["title"], ENT_QUOTES));
              $titleDesc = nl2br(htmlspecialchars($result["title_description"], ENT_QUOTES));
              $intro = nl2br(htmlspecialchars($result["introduction"], ENT_QUOTES));
              $background = nl2br(htmlspecialchars($result["background"], ENT_QUOTES));
              $importance = nl2br(htmlspecialchars($result["importance"], ENT_QUOTES));
              $scope = nl2br(htmlspecialchars($result["scope"], ENT_QUOTES));
              
              // Set placeholder values if any variable is empty
              $title = !empty($title) ? $title : "Capstone Title pending.."; //DO NOT CHANGE THIS, IF YOU DO THEN CTRL+F AND SEARCH FOR EVAL-VERIFY AND CHANGE ITS VALUE TOO
              $titleDesc = !empty($titleDesc) ? $titleDesc : "Capstone Title DESCRIPTION pending..";
              $intro = !empty($intro) ? $intro : "Introduction pending..";
              $background = !empty($background) ? $background : "Background of The Study pending..";
              $importance = !empty($importance) ? $importance : "Importance of The Study pending..";
              $scope = !empty($scope) ? $scope : "Scope and Limitations pending..";
            
              $_SESSION["title"] = $title;
              $_SESSION["title_desc"] = $titleDesc;
                
                
              echo '
                    <script>
                        document.getElementById("titleBoxID").innerHTML = ' . json_encode($title) . ';
                        document.getElementById("titleBoxID").style.backgroundColor = "orange"; // Subtle orange
                
                        document.getElementById("titleDescBoxID").innerHTML = ' . json_encode($titleDesc) . ';
                        document.getElementById("titleDescBoxID").style.backgroundColor = "orange";
                
                        document.getElementById("introBoxID").innerHTML = ' . json_encode($intro) . ';
                        document.getElementById("introBoxID").style.backgroundColor = "orange";
                
                        document.getElementById("backgroundBoxID").innerHTML = ' . json_encode($background) . ';
                        document.getElementById("backgroundBoxID").style.backgroundColor = "orange";
                
                        document.getElementById("importanceBoxID").innerHTML = ' . json_encode($importance) . ';
                        document.getElementById("importanceBoxID").style.backgroundColor = "orange";
                
                        document.getElementById("scopeBoxID").innerHTML = ' . json_encode($scope) . ';
                        document.getElementById("scopeBoxID").style.backgroundColor = "orange";
                    </script>
                ';
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
  
  
  function submitAnswer(){
      global $conn;
      
      try{
          $conn->beginTransaction();
          
          $eval = $_SESSION["evaluation"];
          $comment = $_SESSION["evalComment"];
          
          
          $sql = "SELECT level FROM panelists WHERE panelistID = ? AND projectID = ?";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$_SESSION["userID"], $_SESSION["projectIDValue"]]);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          
          $panelistLevel = $result["level"];
          
   
          if($panelistLevel >= 2){
    
              if($eval == "accepted"){
                    if($_SESSION["title"] != "" && $_SESSION["title"] != "Capstone Title pending.."){ //CHANGE THIS TOO EVAL-VERIFY
                          $sql = "UPDATE title_proposal SET result = ? WHERE projectID = ? AND titleNum = ?";
                          $stmt = $conn->prepare($sql);
                          $result = $stmt->execute([$eval, $_SESSION["projectIDValue"], $_SESSION["titleNum"]]);
                          
                          if($result){
                              
                              $sql = "UPDATE title_proposal SET result = ? WHERE projectID = ? AND titleNum <> ?";
                              $stmt = $conn->prepare($sql);
                              $result = $stmt->execute(["rejected", $_SESSION["projectIDValue"], $_SESSION["titleNum"]]);
                              
                              if($result){
                                  
                                  $sql = "UPDATE capstone_projects SET title = ?, title_description = ? WHERE projectID = ?";
                                  $stmt = $conn->prepare($sql);
                                  $result = $stmt->execute([$_SESSION["title"], $_SESSION["title_desc"], $_SESSION["projectIDValue"]]);
                                  
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
                                            
                                            $description = "Chairman Panelist: " . $surname . ", " . $firstname . " evaluated the title evaluation: title number: " . $_SESSION["titleNum"] . " with the evaluation: " . strtoupper($eval);
                                            
                                            date_default_timezone_set('Asia/Manila');
                                            
                                            $result = $stmt->execute([
                                                $_SESSION["userID"], 
                                                $_SESSION["projectIDValue"], 
                                                $_SESSION["taskID"], 
                                                $description, 
                                                date("Y-m-d"), 
                                                date("H:i:s"), 
                                                $_SESSION["trackingNum"]
                                            ]);
                                              
                                              
                                            $sql = "UPDATE tracking SET status = ? WHERE number = ? AND status = 'submitted'";
                                            $stmt = $conn->prepare($sql);
                                            $result = $stmt->execute(["completed", $_SESSION["trackingNum"]]);
                                       }
                                  }
                              }
                              
                              else{
                                  throw new Exception("Failed to update title of capstone group");
                              }
                          }
                          
                          else{
                              throw new Exception("Failed to update status of other proposed titles");
                          }
                    }
                    
                    else{
                        $conn->rollBack();
                        
                      echo '<script>
                                Swal.fire({
                                     title: "Cannot Accept Empty Capstone Title",
                                    text: "The Capstone Title of selected title number is currently empty",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/answer_title_evaluation";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/answer_title_evaluation";
                                    }
                                });
                            </script>';
                                
                        exit; //stop or exit the code para di mag execute code sa ibaba
                    }
              }
              
              else if($eval == "rejected"){
                  
                   echo '<script>
                           console.log("here 1");
                        </script>';
                        
                    $sql = "SELECT COUNT(*) as rejectCount FROM title_proposal_answers WHERE projectID = ? AND panelistID = ? AND answer = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["userID"], "rejected"]);
                    $rejectCount = $stmt->fetchColumn(); // Returns the count directly
                    
                    if($rejectCount == 2){
                        
                        echo '<script>
                           console.log("here 2");
                        </script>';
                        
                         $conn->rollBack();
                        
                          echo '<script>
                                    Swal.fire({
                                         title: "Cannot Reject Title",
                                        text: "Two titles have already been rejected, please select Accept or Needs Improvement",
                                        icon: "error",
                                        confirmButtonText: "OK"
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "/answer_title_evaluation";
                                        }
                                        
                                        else if (result.isDismissed) {
                                             window.location.href = "/answer_title_evaluation";
                                        }
                                    });
                                </script>';
                                
                        exit; //stop or exit the code para di mag execute code sa ibaba
                    }
              }
          }
          
          $sql = "SELECT * FROM title_proposal_answers WHERE panelistID = ? AND projectID = ? AND titleNum = ? AND answer = ?";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$_SESSION["userID"], $_SESSION["projectIDValue"], $_SESSION["titleNum"], "needs improvement"]);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          
          if($result){ //Update ang title proposal answer if meron na existing (mainly used for "needs improvement" na answers)
              
              $sql = "UPDATE title_proposal_answers SET answer = ?, comment = ? WHERE panelistID = ? AND projectID = ? AND titleNum = ?";
              $stmt = $conn->prepare($sql);
              $result = $stmt->execute([$eval, $comment, $_SESSION["userID"], $_SESSION["projectIDValue"], $_SESSION["titleNum"]]);
              
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
                        
                        $description = "Panelist: " . $surname . ", " . $firstname . " evaluated the title evaluation: title number: " . $_SESSION["titleNum"] . " with the evaluation: " . strtoupper($eval);
                        
                        date_default_timezone_set('Asia/Manila');
                        
                        $result = $stmt->execute([
                            $_SESSION["userID"], 
                            $_SESSION["projectIDValue"], 
                            $_SESSION["taskID"], 
                            $description, 
                            date("Y-m-d"), 
                            date("H:i:s"), 
                            $_SESSION["trackingNum"]
                        ]);
                          
                          
                        $sql = "UPDATE tracking SET status = ? WHERE number = ? AND status = 'submitted'";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute(["evaluating", $_SESSION["trackingNum"]]);
                        
                          if($result){
                              
                                $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$_SESSION["userID"]]);
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                $panel_name = $user["firstname"] . " " . $user["surname"] ;
                              
                                $desc = "Chairman Panelist: " . $panel_name . " evaluated the title evaluation: title number: " . $_SESSION["titleNum"] . " with the evaluation: " . strtoupper($eval);
                                        
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
                              
                       
                              unset($_POST["submit-button"]);
                
                              unset($_SESSION["evaluation"]);
                              unset($_SESSION["evalComment"]);
                             
                              echo '<script>
                                        Swal.fire({
                                             title: "Success",
                                            text: "Title Number '.addslashes($_SESSION["titleNum"]) . ' Re-Evaluation Succesfuly Submitted!",
                                            icon: "success",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "/answer_title_evaluation";
                                            }
                                            
                                            else if (result.isDismissed) {
                                                 window.location.href = "/answer_title_evaluation";
                                            }
                                        });
                                    </script>';
                          }
                  }
              }
              
              else{
                  throw new Exception("Failed to update existing needs improvement evaluation answer");
              }
          }
          
          
          else{ //kapag wala pang existing evaluation answer si panelist sa specific title number na selected
              
              $sql = "INSERT INTO title_proposal_answers (projectID, panelistID, titleNum, answer, comment, level) VALUES(?, ?, ?, ?, ?, ?)";
              $stmt = $conn->prepare($sql);
              $result = $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["userID"], $_SESSION["titleNum"], $eval, $comment, $panelistLevel]);
             
             
              $sql = "SELECT * FROM users WHERE id = ?";
              $stmt = $conn->prepare($sql);
              $stmt->execute([$_SESSION["userID"]]);
              $result = $stmt->fetch(PDO::FETCH_ASSOC);
              
              if($result){
                    
                    $firstname = $result["firstname"];
                    $surname = $result["surname"];
                    
                    $sql = "INSERT INTO activity_logs (userID, projectID, taskID, description, date, time, trackingNum) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    
                    $description = "Panelist: " . $surname . ", " . $firstname . " evaluated the title evaluation: title number: " . $_SESSION["titleNum"] . " with the evaluation: " . strtoupper($eval);
                    
                    date_default_timezone_set('Asia/Manila');
                    
                    $result = $stmt->execute([
                        $_SESSION["userID"], 
                        $_SESSION["projectIDValue"], 
                        $_SESSION["taskID"], 
                        $description, 
                        date("Y-m-d"), 
                        date("H:i:s"), 
                        $_SESSION["trackingNum"]
                    ]);
                      
                      
                    $sql = "UPDATE tracking SET status = ? WHERE number = ? AND status = 'submitted'";
                    $stmt = $conn->prepare($sql);
                    $result = $stmt->execute(["evaluating", $_SESSION["trackingNum"]]);
             
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
                            
                            $description = "Panelist: " . $surname . ", " . $firstname . " evaluated the title evaluation: title number: " . $_SESSION["titleNum"] . " with the evaluation: " . strtoupper($eval);
                            
                            date_default_timezone_set('Asia/Manila');
                            
                            $result = $stmt->execute([
                                $_SESSION["userID"], 
                                $_SESSION["projectIDValue"], 
                                $_SESSION["taskID"], 
                                $description, 
                                date("Y-m-d"), 
                                date("H:i:s"), 
                                $_SESSION["trackingNum"]
                            ]);
                              
                              
                            $sql = "UPDATE tracking SET status = ? WHERE number = ? AND status = 'submitted'";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute(["evaluating", $_SESSION["trackingNum"]]);
                            
                          if($result){
                              
                                $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$_SESSION["userID"]]);
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                $panel_name = $user["firstname"] . " " . $user["surname"] ;
                              
                                $desc = "Panelist: " . $panel_name . " evaluated the title evaluation: title number: " . $_SESSION["titleNum"] . " with the evaluation: " . strtoupper($eval);
                                        
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
                       
                             unset($_POST["submit-button"]);
                
                             unset($_SESSION["evaluation"]);
                             unset($_SESSION["evalComment"]);
                             
                             echo '<script>
                                        Swal.fire({
                                             title: "Success",
                                            text: "Title Number '.addslashes($_SESSION["titleNum"]) . ' Evaluation Succesfuly Submitted!",
                                            icon: "success",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "/answer_title_evaluation";
                                                }
                                                
                                                else if (result.isDismissed) {
                                                     window.location.href = "/answer_title_evaluation";
                                                }
                                            });
                                    </script>';
                         }
                         
                         else{
                             throw new Exception("Failed to insert title answer proposal");
                         } 
                      }
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
            
        unset($_POST["submit-button"]);
            
            echo '<script>
                Swal.fire({
                     title: "Error Submiting Evaluation of Title Number '.addslashes($_SESSION["titleNum"]) . ' ",
                    text: "Error: '.addslashes($e->getMessage()).'",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "/answer_title_evaluation";
                    }
                    
                    else if (result.isDismissed) {
                         window.location.href = "/answer_title_evaluation";
                    }
                });
            </script>';
      }
  }
  
  
  
  
  function checkTitleStatus(){
       global $conn;
      
       //LOGIC FOR NOT IMPLEMENTING THE EVALUATION DROPDOWN AND SUBMIT BUTTON WHEN THE USER VIEWING
       //THE TITLE EVALUATION IS NOT ONE OF THE ASSIGNED PANELISTS OF THE CAPSTONE GROUP
            
            
        $projectID = $_SESSION["projectIDValue"];
        
          
        echo '
            <script>
                console.log("Selected Title Number With session: ' . addslashes($_SESSION["titleNum"]) . '");
            </script>
        ';
        
        $sql = "SELECT * FROM title_proposal WHERE projectID = ? AND result = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$projectID, "accepted"]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($result){
            
            $sql = "SELECT * FROM title_proposal_answers WHERE panelistID = ? AND projectID = ? AND titleNum = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"], $_SESSION["projectIDValue"], $_SESSION["titleNum"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $answer = $result["answer"];
            $comment = $result["comment"];
            
            $color = "";
            
            if($answer == "accepted"){
                $color = "#4CAF50";
            }
            
            else if($answer == "rejected"){
                $color = "red";
            }
            
            else if($answer == "needs improvement"){
                $color = "#FF6600";
            }
            
            
            //Show their previous evaluation response
            echo '
                <button class="collapsible" aria-expanded="false" type="button" style="background-color: ' . htmlspecialchars($color, ENT_QUOTES) . ';">Your Evaluation Response</button>
            ';
            
            echo '
                <div class="content-collapsible">
                   <p name="answerBox" id="answerBoxID">
                        Answer: <br><br>
                        <span style="color:'.htmlspecialchars($color, ENT_QUOTES).';" font-weight: bold">' . htmlspecialchars(strtoupper($answer)) . '</span><br><br>
                        
                        Comment: <br><br>
                        <span style="white-space: pre-wrap;">' . htmlspecialchars($comment) . '</span>
                   </p>
                </div>
            ';
            
            echo '
                <h1 style="margin-top: 20px; font-size: 20px; font-weight: bold; text-align: center; color: #4CAF50;">A Title Has Already Been Selected For This Group &#x2705;</h1>
            ';
        }
          
        else{  
            $sql = "SELECT * FROM title_proposal WHERE projectID = ? AND titleNum = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$projectID, $_SESSION["titleNum"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
              
            if($result){
              
                $titleResult = htmlspecialchars($result["result"]);
                
                 echo '
                    <script>
                        console.log("Result is: ' . addslashes($titleResult) . '");
                    </script>
                 ';
                
                if($titleResult == "pending"){
            
                    $sql = "SELECT * FROM panelists WHERE panelistID = ? AND projectID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["userID"], $_SESSION["projectIDValue"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result) {
                        
                        $sql = "SELECT * FROM title_proposal_answers WHERE panelistID = ? AND projectID = ? AND titleNum = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_SESSION["userID"], $_SESSION["projectIDValue"], $_SESSION["titleNum"]]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if(!$result){ //If false meaning wala pang nabibigay na answer si panelist
                        
                            echo '
                            
                                <form action="" method="POST">
                                        <button class="collapsible" aria-expanded="false" type="Button" style="background-color: #4CAF50;">Panelist Comments/Suggestions</button>
                                        <div class="content-collapsible">
                                            <textarea style="padding: 10px;" name="commentBox" id="commentBoxID" placeholder="Place Any Comments/Suggestions Here.."></textarea>
                                        </div>
                                        <button class="collapsible" aria-expanded="false" type="Button" style="background-color: #4CAF50;">Evaluation</button>
                                        <div class="content-collapsible">
                                            <p>Please choose one option to evaluate this title:</p>
                                            <br>
                                            <div class="evaluation-options">
                                                <label>
                                                    <input type="radio" name="evaluation" value="accepted">
                                                    <span class="custom-radio accept"></span> Accept
                                                </label>
                                                <label>
                                                    <input type="radio" name="evaluation" value="rejected">
                                                    <span class="custom-radio reject"></span> Reject
                                                </label>
                                                <label>
                                                    <input type="radio" name="evaluation" value="needs improvement">
                                                    <span class="custom-radio needs-improvement"></span> Needs Improvement
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="button-container">
                                            <button class="save-button" name="submit-button" type="Submit">Submit</button>
                                        </div>
                                </form>
                                
                                ';
                        }
                        
                        else{
                            $answer = $result["answer"]; //Get the current evaluation response of the panelist to a specific title, that result variable is outside this else block
                            $comment = $result["comment"];
                            
                            $sql = "SELECT * FROM panelists WHERE panelistID = ? AND projectID = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["userID"], $_SESSION["projectIDValue"]]);
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $panelLevel = $result["level"];
                            
                            if($panelLevel >= 2){
                                
                                if($answer != "needs improvement"){ //If the panelist evaluation response to a certain title is accepted or rejected 
                                    
                                    $color = "";
                    
                                    if($answer == "accepted"){
                                        $color = "#4CAF50";
                                    }
                                    
                                    else if($answer == "rejected"){
                                        $color = "red";
                                    }
                                    
                                    else if($answer == "needs improvement"){
                                        $color = "#FF6600";
                                    }
                                    
                                    
                                    //Show their previous evaluation response
                                    echo '
                                        <button class="collapsible" aria-expanded="false" type="button" style="background-color: ' . htmlspecialchars($color, ENT_QUOTES) . ';">Your Evaluation Response</button>
                                    ';
                                    
                                    echo '
                                        <div class="content-collapsible">
                                           <p name="answerBox" id="answerBoxID">
                                                Answer: <br><br>
                                                <span style="color:'.htmlspecialchars($color, ENT_QUOTES).';" font-weight: bold">' . htmlspecialchars(strtoupper($answer)) . '</span><br><br>
                                                
                                                Comment: <br><br>
                                                <span style="white-space: pre-wrap;">' . htmlspecialchars($comment) . '</span>
                                           </p>
                                        </div>
                                    ';
                                    
                                    echo '
                                        <h1 style="margin-top: 20px; font-size: 20px; font-weight: bold; text-align: center; color: #4CAF50;">Title Number: '.addslashes($_SESSION["titleNum"]).' Has Already Been Evaluated By The Panelist Chairman &#x2705;</h1>
                                    ';
                                }
                                
                                else if ($answer == "needs improvement"){
                                    
                                    //Show their previous evaluation response that is for needs improvement
                                    echo '
                                        <button class="collapsible" aria-expanded="false" type="Button" style="background-color: #FF6600;">Your Previous Evaluation Response -- For Needs Improvement</button>
                                    ';
                                    
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
                                    
                                    
                                    echo '
                            
                                        <form action="" method="POST">
                                                <button class="collapsible" aria-expanded="false" type="Button" style="background-color: #4CAF50;">New Panelist Comments/Suggestions</button>
                                                <div class="content-collapsible">
                                                    <textarea style="padding: 10px;" name="commentBox" id="commentBoxID" placeholder="Place Any Comments/Suggestions Here.."></textarea>
                                                </div>
                                                <button class="collapsible" aria-expanded="false" type="Button" style="background-color: #4CAF50;">New Evaluation</button>
                                                <div class="content-collapsible">
                                                    <p>Please choose one option to evaluate this title:</p>
                                                    <br>
                                                    <div class="evaluation-options">
                                                        <label>
                                                            <input type="radio" name="evaluation" value="accepted">
                                                            <span class="custom-radio accept"></span> Accept
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="evaluation" value="rejected">
                                                            <span class="custom-radio reject"></span> Reject
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="evaluation" value="needs improvement">
                                                            <span class="custom-radio needs-improvement"></span> Needs Improvement
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div class="button-container">
                                                    <button class="save-button" name="submit-button" type="Submit">Submit</button>
                                                </div>
                                        </form>
                                        
                                        ';
                                }
                            }
                            
                            else{
                                
                                $color = "";
                    
                                if($answer == "accepted"){
                                    $color = "#4CAF50";
                                }
                                
                                else if($answer == "rejected"){
                                    $color = "red";
                                }
                                
                                else if($answer == "needs improvement"){
                                    $color = "#FF6600";
                                }
                                
                                
                                //Show their previous evaluation response
                                echo '
                                    <button class="collapsible" aria-expanded="false" type="button" style="background-color: ' . htmlspecialchars($color, ENT_QUOTES) . ';">Your Evaluation Response</button>
                                ';
                                
                                echo '
                                    <div class="content-collapsible">
                                       <p name="answerBox" id="answerBoxID">
                                            Answer: <br><br>
                                            <span style="color:'.htmlspecialchars($color, ENT_QUOTES).';" font-weight: bold">' . htmlspecialchars(strtoupper($answer)) . '</span><br><br>
                                            
                                            Comment: <br><br>
                                            <span style="white-space: pre-wrap;">' . htmlspecialchars($comment) . '</span>
                                       </p>
                                    </div>
                                ';
                                    
                                echo '
                                    <h1 style="margin-top: 20px; font-size: 20px; font-weight: bold; text-align: center; color: #4CAF50;">You Have Already Submitted A Response For Title Number: '.addslashes($_SESSION["titleNum"]).' &#x2705;</h1>
                                ';
                            }
                        }
                    }
                }
                
                else{ //If si title result ay hindi na pending, meaning may result na
                    
                    $sql = "SELECT * FROM panelists WHERE panelistID = ? AND projectID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["userID"], $_SESSION["projectIDValue"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $panelLevel = $result["level"];
                    
                    
                    $sql = "SELECT * FROM title_proposal_answers WHERE panelistID = ? AND projectID = ? AND titleNum = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["userID"], $_SESSION["projectIDValue"], $_SESSION["titleNum"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $answer = $result["answer"];
                    $comment = $result["comment"];
                    
                    $color = "";
                    
                    if($answer == "accepted"){
                        $color = "#4CAF50";
                    }
                    
                    else if($answer == "rejected"){
                        $color = "red";
                    }
                    
                    else if($answer == "needs improvement"){
                        $color = "#FF6600";
                    }
                    
                    
                    //Show their previous evaluation response
                    echo '
                        <button class="collapsible" aria-expanded="false" type="button" style="background-color: ' . htmlspecialchars($color, ENT_QUOTES) . ';">Your Evaluation Response</button>
                    ';
                    
                    echo '
                        <div class="content-collapsible">
                           <p name="answerBox" id="answerBoxID">
                                Answer: <br><br>
                                <span style="color:'.htmlspecialchars($color, ENT_QUOTES).';" font-weight: bold">' . htmlspecialchars(strtoupper($answer)) . '</span><br><br>
                                
                                Comment: <br><br>
                                <span style="white-space: pre-wrap;">' . htmlspecialchars($comment) . '</span>
                           </p>
                        </div>
                    ';
                    
                    if($panelLevel >= 2){
                        echo '
                            <h1 style="margin-top: 20px; font-size: 20px; font-weight: bold; text-align: center; color: #4CAF50;">Title Number: '.addslashes($_SESSION["titleNum"]).' Has Already Been Evaluated By The Panelist Chairman &#x2705;</h1>
                        ';
                    }
                    
                    else{
                        echo '
                            <h1 style="margin-top: 20px; font-size: 20px; font-weight: bold; text-align: center; color: #4CAF50;">You Have Already Submitted A Response For Title Number: '.addslashes($_SESSION["titleNum"]).' &#x2705;</h1>
                        ';
                    }
                }
            }
        }
  }
  
  
  function showTrackingNumber(){
      global $conn;
      
      try{
          $sql = "SELECT * FROM tracking WHERE projectID = ? AND taskID = ?";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$_SESSION["projectIDValue"], $_SESSION["taskID"]]);
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
        <link rel="stylesheet" href="pages/title_evaluation.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <script src="pages/session_tracker.js"></script>

        <title>Answer Title Evaluation</title>
    </head>
    
    <body>

        <?php 
            require "connection.php";
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
            
            <form action="" id="dateFormID" method="GET">
                <div class="title-dropdown">
                    <select id="date-select" name="dateBox">
                        <?php getDates(); ?>
                    </select>
                </div>
            </form>
            
            
            <button class="refresh-button" onclick="hardReload()">
                <img src="pages/images/refresh.png" alt="Refresh Icon">
                Refresh
            </button>
        </div>
        
        <button class="collapsible" aria-expanded="false" type="Button" id="title-button">Capstone Title</button>
        <div class="content-title-collapsible">
            <p style="height: 50px;" name="titleBox" id="titleBoxID">Capstone Title..</p>
        </div>

        <button class="collapsible" aria-expanded="false" type="Button" id="desc-button">Title Description</button>
        <div class="content-title-collapsible">
            <p style="height: 50px;" name="titleDescBox" id="titleDescBoxID">Capstone Title DESCRIPTION..</p>
        </div>
        
        <button class="collapsible" aria-expanded="false" type="Button" id="intro-button">Introduction</button>
        <div class="content-collapsible">
            <p name="introBox" id="introBoxID">Introduction..</p>
        </div>

        <button class="collapsible" aria-expanded="false" type="Button" id="background-button">Background of the Study</button>
        <div class="content-collapsible">
            <p name="backgroundBox" id="backgroundBoxID">Background of the Study</p>
        </div>

        <button class="collapsible" aria-expanded="false" type="Button" id="importance-button">Importance of the Study</button>
        <div class="content-collapsible">
            <p name="importanceBox" id="importanceBoxID">Importance of the Study</p>
        </div>

        <button class="collapsible" aria-expanded="false" type="Button" id="scope-button">Scope and Limitations</button>
        <div class="content-collapsible">
            <p name="scopeBox" id="scopeBoxID">Scope and Limitations..</p>
        </div>
        
        <div id="result-content">
            <?php
                checkTitleStatus();
                showTrackingNumber();
            ?>
        </div>
        
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
            
            
            document.addEventListener('DOMContentLoaded', function() {
                    const dropdown2 = document.getElementById('date-select');
                    const form2 = document.getElementById('dateFormID');
                
                    dropdown2.addEventListener('click', function() {
                         // Check if the dropdown is not yet expanded
                        if (!dropdown2.classList.contains('open')) {
                            dropdown2.classList.add('open');
                        } else {
                            // Submit the form when an option is selected
                            form2.submit();
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
            
            
            function hardReload() {
                window.location.href = "/answer_title_evaluation"; // Forces reload from the server
            }
        </script>
        
        
        
        <?php 
            showGroupInfo();
            showTitleValues();
        ?>
        
        
        
         <?php 
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
                if(isset($_SESSION["titleNum"]) && isset($_POST["commentBox"]) && isset($_POST["evaluation"])) {
                    
                    $_SESSION["evalComment"] = $_POST["commentBox"];
                    $_SESSION["evaluation"] = $_POST["evaluation"];
                    
                    echo '
                        <script>
                            console.log("Evaluation: ' . addslashes($_SESSION["evaluation"]) . '");
                            console.log("Comment: ' . addslashes($_SESSION["evalComment"]) . '");
                        </script>
                    ';
                    
                    submitAnswer();
                    showTitleValues();
                }
                
                else if(isset($_POST["titleNumBox"])){
                     $selectedValue = $_POST["titleNumBox"];
                     
                     global $titleNum;
                    
                     $titleNum = $selectedValue;
                    
                     $_SESSION["titleNum"] = $selectedValue;
                     
                     // Use JavaScript to set the selected value of the select element
                    echo '
                        <script>
 
                            document.getElementById("title-select").value = "' . addslashes($selectedValue) . '";
                            console.log("Selected Title Number: ' . addslashes($_SESSION["titleNum"]) . '");
                            
                            document.getElementById("result-content").innerHTML = ""; 
                            
                        </script>
                    ';
                     
                     showTitleValues();
                     checkTitleStatus(); //This here appends the html structue for the panelist comment box and evaluation selection and submit button
                                         //If something breaks, put it back again as a php echo BEFORE the opening tag of the javascript ( <script> )
                }
                
                else if(isset($_POST["taskID"])){
                    $_SESSION["taskID"] = $_POST["taskID"];
                    
                    showTrackingNumber();
                    checkTracking();
                }
            }
            
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
               if(isset($_GET["dateBox"])){
                     $selectedDate = $_GET["dateBox"];
                     
                     $_SESSION["title_proposal_date"] = $_GET["dateBox"];
                     
                     showPreviousTitleValues($selectedDate);
                     
                     echo '
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const selectElement = document.getElementById("date-select");
                        
                                // Set the selected value
                                selectElement.value = "' . $selectedDate . '";
                        
                                // Function to update background text color based on selected option
                                function updateBackgroundTextColor() {
                                    const firstOptionValue = selectElement.options[0].value; // Get the value of the first option
                                    if (selectElement.value === firstOptionValue) {
      
                                        document.getElementById("titleBoxID").style.backgroundColor = "white"; 
                                
                                        document.getElementById("titleDescBoxID").style.backgroundColor = "white"; 
                                
                                        document.getElementById("introBoxID").style.backgroundColor = "white"; 
                                
                                        document.getElementById("backgroundBoxID").style.backgroundColor = "white"; 
                                
                                        document.getElementById("importanceBoxID").style.backgroundColor = "white"; 
                                
                                        document.getElementById("scopeBoxID").style.backgroundColor = "white";
                                    } 
                                    
                                    else {
                                        document.getElementById("titleBoxID").style.backgroundColor = "orange"; // Subtle orange
                                
                                        document.getElementById("titleDescBoxID").style.backgroundColor = "orange";
                                
                                        document.getElementById("introBoxID").style.backgroundColor = "orange";
                                
                                        document.getElementById("backgroundBoxID").style.backgroundColor = "orange";
                                
                                        document.getElementById("importanceBoxID").style.backgroundColor = "orange";
                                
                                        document.getElementById("scopeBoxID").style.backgroundColor = "orange";
                                        
                                        
                                        textvalue = selectElement.options[selectElement.selectedIndex].text;
                                        
                                        document.getElementById("title-button").innerText = "Capstone Title" + "\n\n" + textvalue;
                                        document.getElementById("desc-button").innerText = "Title Description" + "\n\n" + textvalue;
                                        document.getElementById("intro-button").innerText = "Introduction" + "\n\n" + textvalue;
                                        document.getElementById("background-button").innerText = "Background of the Study" + "\n\n" + textvalue;
                                        document.getElementById("importance-button").innerText = "Importance of the Study" + "\n\n" + textvalue;
                                        document.getElementById("scope-button").innerText = "Scope and Limitations" + "\n\n" + textvalue;
                                    }
                                }
                        
                                // Run the function initially and on change
                                updateBackgroundTextColor();
                                selectElement.addEventListener("change", updateBackgroundTextColor);
                            });
                        </script>
                        ';
               }
            }
        ?>

        <?php require 'footer.php'; ?>
    </body>
</html>

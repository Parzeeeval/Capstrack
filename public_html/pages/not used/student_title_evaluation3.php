<?php
    $_SESSION["titleNum"];
    
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
                      WHERE s.sectionID = ?
              ";
              
              $stmt = $conn->prepare($sql);
              $stmt->execute([$sectionID]);
              $result = $stmt->fetch(PDO::FETCH_ASSOC);
              
              if($result){
                  $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"] . ", Group " . $result["groupNum"] . ", ". $result["specialization"];
                  
                  $semester = $result["semester"] == 1 ? "1st Semester" : ($result["semester"] == 2 ? "2nd Semester" : "Unknown Semester");
                  
                  $year = $result["start_year"] . "-" . $result["end_year"] . " (" . $semester . ") ";
                  
                  echo'
                      <script>
                           document.getElementById("courseInfoID").innerHTML = "'. htmlspecialchars($section, ENT_QUOTES) .'";
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
  
  function showImportanceValues(){
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
                  $importance = htmlspecialchars($result["importance"], ENT_QUOTES); // Only escape special characters without adding <br> tags
    
                  echo'
                      <script>
                            document.getElementById("headerID").innerHTML = "' . addslashes($title) . '";
                            document.getElementById("importanceID").value = ' . json_encode($importance) . ';
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
  
  function saveImportance(){
      global $conn;
      
      try{
          $conn->beginTransaction();
          
          $sql = "UPDATE title_proposal SET importance = ? WHERE projectID = ? AND titleNum = ?";
          $stmt = $conn->prepare($sql);
          $result = $stmt->execute([$_SESSION["importance"], $_SESSION["projectID"], $_SESSION["titleNum"]]);
          
          if($result){
              $conn->commit();
                    
              unset($_POST["save-button"]);
             
              echo '<script>
                    Swal.fire({
                         title: "Success",
                        text: "Importance of the Study Succesfuly Saved!",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/edit_title_evaluation_importance";
                        }
                    });
                  </script>';
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
                     title: "Error Saving Importance of the Study",
                    text: "Error: '.addslashes($e->getMessage()).'",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            </script>';
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
        <link rel="stylesheet" href="pages/title_evaluation1.css">
        <link rel="stylesheet" href="pages/title_evaluation_content.css">
        
        
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <title>Title Evaluation-Page 1</title>
        
        <script src="pages/session_tracker.js"></script>
        
        <style>
            textarea {
                width: 100%; /* Takes up full width of the container */
                box-sizing: border-box; /* Includes padding and border in the element's width */
                border: none; /* Removes the border */
                padding: 0px; /* Optional: Adds some padding inside the textarea */
                outline: none; /* Removes the focus outline */
            }
        </style>
    </head>
    
    <body>
        <?php 
            require_once "connection.php";
            session_start();
        ?>
        
        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>
        

    
            <h2 class="section-title">Title Evaluations</h2>
            <p class="course-info" id="courseInfoID"> </p>
            <p class="semester-info" id="semesterInfoID"> </p>
            
            <form action="" id="selectFormID" method="POST">
                <div class="title-dropdown">
                    <select id="title-select" name="titleBox">
                         <option value="1" <?php echo isset($_SESSION["titleNum"]) && $_SESSION["titleNum"] == "1" ? 'selected' : ''; ?>>Title Number 1</option>
                        <option value="2" <?php echo isset($_SESSION["titleNum"]) && $_SESSION["titleNum"] == "2" ? 'selected' : ''; ?>>Title Number 2</option>
                        <option value="3" <?php echo isset($_SESSION["titleNum"]) && $_SESSION["titleNum"] == "3" ? 'selected' : ''; ?>>Title Number 3</option>
                    </select>
                </div>
            </form>
            
            <form method="POST" action="">
                <div class="section-introduction">
                    <h3>Importance of the Study</h3>
                    <div class="introduction-title"></div>
                    <p class="project-title" id="headerID"></p>
                </div>
            
                <div class="team-info">
                    <textarea id="importanceID" name="importance" rows="8" style="height: 300px; font-size: 17px; font-weight: bold;" placeholder="Importance of the Study Paragraph..."></textarea>
                </div>
                
                <br>
                
                <div>
                    <button class="save-button" name="save-button" type="Submit">Save Importance of Study</button>
                </div>
            
            </form>

            <div class="pagination">
                 <a href="/edit_title_evaluation_introduction">1</a>
                 <a href="/edit_title_evaluation_background">2</a>
                 <a href="#" class="current">3</a>
                 <a href="/edit_title_evaluation_scope">4</a>
            </div>
            
     
     
            <script>
        
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

            </script>
            
            
        <?php 
            showGroupInfo();
            showImportanceValues();
        ?>
        
        <?php 
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
                if(isset($_POST["importance"])) {
                    $_SESSION["importance"] = $_POST["importance"];

                    saveImportance();
                }
                
                else if(isset($_POST["titleBox"])){
                     $selectedValue = $_POST["titleBox"];
                     
                     global $titleNum;
                    
                     $titleNum = $selectedValue;
                    
                     $_SESSION["titleNum"] = $selectedValue;
                     
                     // Use JavaScript to set the selected value of the select element
                    echo '
                        <script>
                            // Change the selected value of the select box based on the session value
                            document.getElementById("title-select").value = "' . addslashes($selectedValue) . '";
                            console.log("Selected Title Number: ' . addslashes($selectedValue) . '");
                        </script>
                    ';
                     
                     showImportanceValues();
                }
            }
        ?>
    
        <?php include 'footer.php'; ?>
    </body>
</html>
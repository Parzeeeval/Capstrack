<?php
    $titleNum = 1;
    $_SESSION["titleNum"] = $titleNum;
    
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
  
  function showIntroValues(){
      global $conn, $titleNum;
      
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
              $stmt->execute([$projectID, $titleNum]);
              $result = $stmt->fetch(PDO::FETCH_ASSOC);
              
              if($result){
                  $title = htmlspecialchars($result["title"], ENT_QUOTES); // Only escape special characters without adding <br> tags
                  $intro = htmlspecialchars($result["introduction"], ENT_QUOTES); // Only escape special characters without adding <br> tags

                  echo'
                      <script>
                            document.getElementById("titleID").value = ' . json_encode($title) . ';
                            document.getElementById("introID").value = ' . json_encode($intro) . ';
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
  
  function saveIntro(){
      global $conn;
      
      try{
          $conn->beginTransaction();
          
          $sql = "UPDATE title_proposal SET title = ?, introduction = ? WHERE projectID = ? AND titleNum = ?";
          $stmt = $conn->prepare($sql);
          $result = $stmt->execute([$_SESSION["title"], $_SESSION["intro"], $_SESSION["projectID"], $_SESSION["titleNum"]]);
          
          if($result){
              $conn->commit();
                    
              unset($_POST["save-button"]);
             
              echo '<script>
                    Swal.fire({
                         title: "Success",
                        text: "Introduction Succesfuly Saved!",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/edit_title_evaluation_introduction";
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
                     title: "Error Saving Introduction",
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
            
            <div class="title-dropdown">
                <select id="title-select">
                    <option value="1">Title Number 1</option>
                    <option value="2">Title Number 2</option>
                    <option value="3">Title Number 3</option>
                </select>
            </div>
            
            <form method="POST" action="" id="introFormID">
                <div class="section-introduction">
                    <h3>Introduction</h3>
                    <div class="introduction-title"></div>
                    <textarea class="project-title" id="titleID" name="title" placeholder="Capstone Title..."></textarea>
                </div>
            
                <div class="team-info">
                    <textarea id="introID" name="introduction" rows="8" style="height: 300px; font-size: 17px; font-weight: bold;" placeholder="Introduction Paragraph..."> </textarea>
                </div>
                
                <br>
                
                <div>
                    <button class="save-button" name="save-button" type="Submit">Save Introduction</button>
                </div>
            
            </form>

            <div class="pagination">
                 <a href="#" class="current">1</a>
                 <a href="/title_evaluation_rationale">2</a>
                 <a href="/title_evaluation_importance">3</a>
                 <a href="/title_evaluation_scope">4</a>
                 <a href="title_evaluation_result">5</a>
            </div>
            
     
     
            <script>
                  function setupDropdownListener() {
                        const dropdown = document.getElementById('title-select');
            
                        dropdown.addEventListener('change', function() {
                            const selectedValue = dropdown.value;
            
                            if (selectedValue) {
                                // Send the selected value to the PHP script using fetch
                                fetch('', { // PHP script is in the same file
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: 'selectedValue=' + encodeURIComponent(selectedValue),
                                })
                            } 
                            
                            else {
                                alert('Please select an option.');
                            }
                        });
                  }
                  
  
                // Call the function after the DOM is fully loaded
                 document.addEventListener('DOMContentLoaded', setupDropdownListener);
            </script>
            
            
        <?php 
            showGroupInfo();
            showIntroValues();
        ?>
        
        <?php 
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
                if (isset($_POST['selectedValue'])) {
                    $selectedValue = $_POST['selectedValue'];
                    
                    global $titleNum;
                    
                    $titleNum = $selectedValue;
                    
                    $_SESSION["titleNum"] = $titleNum;
                    
                    echo '
                        <script>
                            console.log("'.addslashes($selectedValue).'");
                        </script>
                    
                    ';
                } 
                
                else if(isset($_POST["title"]) && isset($_POST["introduction"])) {
                    $_SESSION["title"] = $_POST["title"];
                    $_SESSION["intro"] = $_POST["introduction"];
                    
                    saveIntro();
                }
            }
        ?>
    
        <?php include 'footer.php'; ?>
    </body>
</html>

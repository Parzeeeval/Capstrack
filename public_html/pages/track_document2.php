<?php
    function checkTracking($trackingNum){
      global $conn;
      
      try{
          $conn->beginTransaction();
          
          $sql = "SELECT 
                    t.status AS track_status, 
                    t.taskID as task_ID, 
                    ts.taskName, 
                    ts.id,
                    cp.*, 
                    sec.*, 
                    ay.*
                FROM tracking t 
                JOIN tasks ts ON t.taskID = ts.id
                JOIN capstone_projects cp ON cp.projectID = t.projectID
                JOIN sections sec ON sec.sectionID = cp.sectionID
                JOIN academic_year ay ON cp.academicYearID = ay.id
                WHERE t.number = ?";
        
          $stmt = $conn->prepare($sql);
          $stmt->execute([trim($trackingNum)]);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          
          if($result){
              $status = $result["track_status"];
              
              $taskName = $result["taskName"];
              
              $title = $result["title"];
              
              $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"] . ", Group " . $result["groupNum"] . ", ". $result["specialization"];
              
              $semester = $result["semester"] == 1 ? "1st Semester" : ($result["semester"] == 2 ? "2nd Semester" : "Unknown Semester");
              
              $year = $result["start_year"] . "-" . $result["end_year"] . " (" . $semester . ") ";
              
              
              echo '
                    <script>
                        // Unhide the table with id "timeline"
                        var timelineTable = document.getElementById("timeline");
                        if (timelineTable) {
                            timelineTable.style.display = "table"; // Make sure to use "table" for a table element
                        }
                    
                        // Hide the h1 element with id "logsLabel"
                        var logsLabel = document.getElementById("logsLabel");
                        if (logsLabel) {
                            logsLabel.style.display = "none";
                        }
                    </script>
                ';

              echo '
                    <script>
                        document.getElementById("inputDiv").innerHTML += 
                            "<br>" +
                            "<h3>' . $title . '</h3>" +
                            "<h3>' . $section . '</h3>" +
                            "<h3>' . $year . '</h3>" +
                            "<h2 style=\"color: #ca6431;\">' . ucwords($taskName) . '</h2>";
                    </script>
                ';
              
              if($status == "started"){
                  echo '
                        <script>
                             document.getElementById("step1").classList.add("completed");
                        </script>
                  ';
              }
              
              else if($status == "submitted"){
                  echo '
                        <script>
                             document.getElementById("step1").classList.add("completed");
                        
                             document.getElementById("line1").classList.add("completed")
                             document.getElementById("step2").classList.add("completed");
                        </script>
                  ';
              }
              
              else if($status == "evaluating"){
                  echo '
                        <script>
                             document.getElementById("step1").classList.add("completed");
                            
                             document.getElementById("line1").classList.add("completed")
                             document.getElementById("step2").classList.add("completed");
                             
                             document.getElementById("line2").classList.add("completed")
                             document.getElementById("step3").classList.add("completed");
                        </script>
                  ';
              }
              
 
              else if($status == "completed" || $status == "approved" ){
                  echo '
                        <script>
                             document.getElementById("step1").classList.add("completed");
                        
                             document.getElementById("line1").classList.add("completed")
                             document.getElementById("step2").classList.add("completed");
                             
                             document.getElementById("line2").classList.add("completed")
                             document.getElementById("step3").classList.add("completed");
                             
                             document.getElementById("line3").classList.add("completed")
                             document.getElementById("step4").classList.add("completed");
                             
                             document.getElementById("line4").classList.add("completed")
                             document.getElementById("step5").classList.add("completed");
                        </script>
                  ';
              }
              
              $conn->commit();
          }
          
          else{
              throw new Exception("Failed to retrieve tracking status or Invalid tracking number");
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
  
   function getLogs($trackingNum){
        global $conn;
    
        try{
            $sql = "SELECT * FROM activity_logs WHERE trackingNum = ? ORDER BY id DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([trim($trackingNum)]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if(count($results) >= 1){
                echo '<script>';
                echo 'var rows = "";';  // Initialize a variable to hold the rows
                
                $firstRow = true; // Flag to check if it's the first row
    
                foreach($results as $result){
                    $desc = $result["description"];
    
                    // Format the date as "October 21, 2024"
                    $date = DateTime::createFromFormat('Y-m-d', $result["date"])->format('F j, Y');
    
                    // Format the time as 12-hour with AM/PM
                    $time = DateTime::createFromFormat('H:i:s', $result["time"])->format('h:i A');
    
                    // For the first row, make the item active, others remain normal
                    $itemClass = $firstRow ? 'item active' : 'item';
                    $itemClass2 = $firstRow ? 'active' : '';
    
                    // Append each row to the 'rows' JavaScript variable
                    echo 'rows += `<tr>
                                    <td><div class="' . $itemClass . '"></div></td>
                                    <td class="' . $itemClass2 . '">' . $date . '</td>
                                    <td class="' . $itemClass2 . '">' . $time . '</td>
                                    <td class="' . $itemClass2 . '">' . $desc . '</td>
                                 </tr>`;';
    
                    // Set the flag to false after the first row
                    $firstRow = false;
                }
    
                // Insert rows into the table
                echo 'document.addEventListener("DOMContentLoaded", function() {
                          document.querySelector("#timeline tbody").innerHTML += rows;
                      });';
                echo '</script>';
            }
            
            else {
                    echo '
                        <script>
                            // Hide the table with id "timeline"
                            var timelineTable = document.getElementById("timeline");
                            if (timelineTable) {
                                timelineTable.style.display = "none"; // Hides the table
                            }
                        
                            // Unhide the h1 element with id "logsLabel"
                            var logsLabel = document.getElementById("logsLabel");
                            if (logsLabel) {
                                document.getElementById("logsLabel").textContent = "Invalid Tracking Number or No Activity Logs Yet";
                            }
                        </script>
                    ';
            }
        }
        catch(Exception $e){
            echo '<script>console.log("' . addslashes($e->getMessage()) . '");</script>';
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
        <link rel="stylesheet" href="pages/track_document.css">
           <link rel="stylesheet" href="pages/headerfortrack.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <script src="pages/session_tracker.js"></script>
        <title>Document Tracking</title>
        
        
       
    </head>
    
    <body>
        
        <?php require 'headerfortrack.php'; ?> <!-- This is for the topbar -->
        <?php 
            require "connection.php";
            session_start();
        ?>
        
       <div id="content">
            
            <div>
                <br>
                <div id="inputDiv">
                    <h2 style="font-size: 24px;">Document Tracking Number</h2>  
             
                    <form action="" method="POST">
                        
                      
    
                        <label for="trackNum"></label>
                      
                        <input type="text" name="trackNum" id="trackNum" class="num-textfield"placeholder="Enter the document tracking number." value="<?php echo isset($_POST['trackNum']) ? $_POST['trackNum'] : ''; ?>" required> 
                          <button class="track-button" type="submit">
                            <img src="pages/images/refresh.png" alt="Track Icon">
                            Track Document
                        </button>
                    </form>
                </div>
        
                <div class="progress-container">
                    <div class="progress-bar" id="progressBar">
                        <div class="step">
                            <div class="circle" id="step1">
                                <div class="inner-circle">1</div>
                            </div>
                            <span class="label">Started</span>
                        </div>
                        <div class="line" id="line1"></div>
                        
                        <div class="step">
                            <div class="circle" id="step2">
                                <div class="inner-circle">2</div>
                            </div>
                            <span class="label">Submitted</span>
                        </div>
                        <div class="line" id="line2"></div>
                        
                        <div class="step">
                            <div class="circle" id="step3">
                                <div class="inner-circle">3</div>
                            </div>
                            <span class="label">Evaluating</span>
                        </div>
                        <div class="line" id="line3"></div>
                        
                        <div class="step">
                            <div class="circle" id="step4">
                                <div class="inner-circle">4</div>
                            </div>
                            <span class="label">Approved</span>
                        </div>
                        <div class="line" id="line4"></div>
                        
                        <div class="step">
                            <div class="circle" id="step5">
                                <div class="inner-circle">5</div>
                            </div>
                            <span class="label">Completed</span>
                        </div>
                    </div>
                </div>
        
                <!-- Separator line -->
                <div class="separator"></div>
        
                <div id="list">
                   <h1 style="text-align: center; color: #B0B0B0; margin-top: 150px; font-size: 20px;" id="logsLabel">Activity Logs Will Be Displayed Here</h1>
                   <table id="timeline" hidden>
                       <thead>
                          <tr>
                             <th></th>
                             <th>Date</th>
                             <th>Time</th>
                             <th>Description</th>
                          </tr>
                       </thead>
                       <tbody>
                          <!-- Rows will be inserted here -->
                       </tbody>
                    </table>
                </div>
            </div>
    </div>
            
        <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if(isset($_POST["trackNum"])){
                    checkTracking($_POST["trackNum"]);
                    getLogs($_POST["trackNum"]);
                }
                
                else if(isset($_POST["track-btn"])){
                    if(isset($_POST["trackingNum"])){
                        checkTracking($_POST["trackingNum"]);
                        getLogs($_POST["trackingNum"]); 
                        
                        echo '
                            <script>
                                document.getElementById("trackNum").value = "' . $_POST["trackingNum"] . '";
                            </script>
                        ';
                    }
    
                }
            }
        ?>
         <?php require 'footerfortrack.php'; ?> 
    </body>
</html>

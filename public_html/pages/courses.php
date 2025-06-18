<?php
    session_start();
    require_once 'connection.php'; 

    $userID = $_SESSION["userID"];
    $accountType = $_SESSION["accountType"];
    
    //removes these session variables so everytime the user comesback to the courses page, if they manually typed /specializations or /sections in the URL
    //they will be redirected back to the courses page to avoid potential confusion as to which course those specialization/sections belongs
    unset($_SESSION["courseID"]);
    unset($_SESSION["specialization"]);
    unset($_SESSION["sectionID"]);
    
    $sql = "SELECT accessLevel FROM faculty where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userID]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $accessLevel = $result["accessLevel"];


    function showCourses(){
        global $conn, $userID, $accessLevel;

        if($accessLevel >= 3){
            $sql = "SELECT * FROM courses";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '
                <script>

                    var button = document.createElement("button");
                    button.innerHTML = "+"; 
                    button.className = "create-btn-design";
                    button.onclick = function() {
                        window.location.href = "create_courses"; 
                    };
                

                    document.getElementById("createDiv").appendChild(button);
                </script>
            ';
        }

        else if($accessLevel == 2){
            $sql = "SELECT * FROM courses WHERE adminID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            
            //Get courses with the faculty membering being a capstone coordinator
            $sql = "SELECT * FROM courses c JOIN sections s ON s.courseID = c.courseID WHERE s.coordinatorID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $result = array_merge($result, $secondResult);  // Combine both results
            
            
            //Get courses with the faculty membering being an adviser
            $sql = "SELECT cr.*
                    FROM capstone_projects cp
                    JOIN sections s ON cp.sectionID = s.sectionID
                    JOIN advisers ad ON cp.projectID = ad.projectID
                    JOIN courses cr ON cr.courseID = s.courseID
                    WHERE ad.adviserID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch the result of the second query
            
            $result = array_merge($result, $thirdResult);  // Combine both results
            
            
            //Get courses with the faculty membering being a panelist
            $sql = "SELECT cr.*
                    FROM capstone_projects cp
                    JOIN sections s ON cp.sectionID = s.sectionID
                    JOIN panelists ps ON cp.projectID = ps.projectID
                    JOIN courses cr ON cr.courseID = s.courseID
                    WHERE ps.panelistID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $fourthResult = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch the result of the third query
            
            $result = array_merge($result, $fourthResult);  // Combine both results
        }
        
        else if($accessLevel <= 1 ){
            
            //Get courses with the faculty membering being a capstone coordinator
            $sql = "SELECT * FROM courses c JOIN sections s ON s.courseID = c.courseID WHERE s.coordinatorID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            
            //Get courses with the faculty membering being an adviser
            $sql = "SELECT cr.*
                    FROM capstone_projects cp
                    JOIN sections s ON cp.sectionID = s.sectionID
                    JOIN advisers ad ON cp.projectID = ad.projectID
                    JOIN courses cr ON cr.courseID = s.courseID
                    WHERE ad.adviserID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $secondResult = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch the result of the second query
            
            $result = array_merge($result, $secondResult);  // Combine both results
            
            
            //Get courses with the faculty membering being a panelist
            $sql = "SELECT cr.*
                    FROM capstone_projects cp
                    JOIN sections s ON cp.sectionID = s.sectionID
                    JOIN panelists ps ON cp.projectID = ps.projectID
                    JOIN courses cr ON cr.courseID = s.courseID
                    WHERE ps.panelistID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $thirdResult = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch the result of the third query
            
            $result = array_merge($result, $thirdResult);  // Combine both results
            

        }
            
        //This array below is for removing duplicate entries in cards
        $courseIDs = [];
        
        for ($i = 0; $i < count($result); $i++) {
            $courseTitle = $result[$i]["courseName"];
            $courseAbbreviation = $result[$i]["courseID"];

            if($courseAbbreviation == "N/A"){
                continue;
            }
            
            if(in_array($courseAbbreviation, $courseIDs)){
                continue;
            }
            
           
            $courseIDs[] = $courseAbbreviation;
            
            
            echo '
            <form action="/specializations" method="POST">
                <div class="card-container" onclick="this.closest(\'form\').submit();">
                    <input type="hidden" name="cardValue" value="'.$courseAbbreviation.'">
                    <div class="card" style="background-color:#6BA3C1;">
                        <div class="folder-bottom">
                            <div class="circle"></div>
                        </div>
                        <div class="course-content">
                            <h2>' . $courseTitle . '</h2>
                            <p>' . $courseAbbreviation . '</p>
                        </div>
                    </div>
                </div>
            </form>';
        }
        
        // Check if $courseIDs is empty
        if (empty($courseIDs)) {
            echo '
            <h1 style="
                font-size: 2.5em;
                color: #B22222; /* Dark red for visibility */
                background-color: #FFE4E1; /* Light pink background for warning */
                padding: 20px;
                border: 2px solid #B22222;
                border-radius: 8px;
                text-align: center;
                width: 80%;
                margin: 20px auto;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            ">
                ⚠️ Not yet a Panelist/ Coordinator/ Adviser ⚠️
            </h1>';
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Courses</title>
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="pages/card_layout.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="pages/session_tracker.js"></script>
    </head>


    <body>
        <?php include 'header.php'; ?> <!--This is for the topbar -->
        <?php include 'menu.php'; ?> <!--This is for the menu -->
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        
        <div class="create-button" id="createDiv">
           
        </div>
        
        
        <div class="card-container">
            <?php
                
                showCourses();
                                
            ?>
          
        </div>
     
        <script>
            document.querySelectorAll('.options').forEach(options => {
                options.addEventListener('click', function () {
                    this.querySelector('.dropdown').classList.toggle('show');
                });
            });
    
            window.addEventListener('click', function (e) {
                if (!e.target.matches('.hamburger')) {
                    document.querySelectorAll('.dropdown').forEach(dropdown => {
                        if (dropdown.classList.contains('show')) {
                            dropdown.classList.remove('show');
                        }
                    });
                }
            });
            
        </script>
        
        <?php include 'footer.php'; ?> 
    </body>
</html>

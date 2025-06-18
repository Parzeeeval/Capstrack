<?php
    require "connection.php";
    session_start();
    
    
    function getCurrentYear(){
        global $conn;
        
        $sql = "SELECT start_year, end_year FROM academic_year ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $start = $result["start_year"];
        $end = $result["end_year"];
        
        $academicYear = "Academic Year " . $start . " - " . $end;
        
        echo '
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    document.getElementById("acadYearLabel").innerText = "' . $academicYear . '";
                });
            </script>
        ';
    }
    
    function viewAnnouncement(){
        global $conn;
        
        try {
            $sql = "SELECT * FROM announcement WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([1]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $announceContent = $result["content"];
                $save_date = $result["date"];
                
                // Format the date in a more readable format
                $formattedDate = date('F d, Y', strtotime($save_date));
                
                echo '
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            document.getElementById("announcement-header").innerText = "Announcement: " + ' . json_encode($formattedDate) . ';
                            document.getElementById("announcement").value = ' . json_encode($announceContent) . ';
                        });
                    </script>
                ';
            }
        } 
        
        catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
    
    function getDefenseStatus(){
        global $conn;
        
        try{
            $defVerdict = "";
            $defType = "";
            
            $sql = "SELECT * FROM students WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                
                $projectID = 0;
                $sectionID = 0;
                
                $prev_projectID = $result["projectID"];
                $prev_sectionID = $result["sectionID"];
                
                $new_projectID = $result["new_projectID"];
                $new_sectionID = $result["new_sectionID"];
                
                if($new_projectID != NULL || $new_sectionID != NULL){
                    $projectID = $new_projectID;
                    $sectionID = $new_sectionID;
                }
                
                else{
                    $projectID = $prev_projectID;
                    $sectionID = $prev_sectionID;
                }
                
                $sql = "SELECT yearLevel FROM sections WHERE sectionID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($result){
                    
                    $yearLevel = $result["yearLevel"];
                    
                    $defType = "Capstone Defense Status";
                    
                    if($projectID >= 1){
                        
                        $sql = ""; //declare empty first
                        $identifier = "defense";
                        
                        if($new_projectID != NULL || $new_sectionID != NULL){
                            $sql = "SELECT defense FROM capstone_projects WHERE new_projectID = ?"; 
                        }
                        
                        else if($new_projectID == NULL || $new_sectionID == NULL){
                            $sql = "SELECT defense FROM capstone_projects WHERE projectID = ?"; 
                        }
    
                        
                        
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$projectID]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if($result){
                            $defVerdict = $result[$identifier];
                        }
                        
                    }
                    
                    else if($projectID <= 0){
                        $defVerdict = "Not yet in a capstone group";
                    }
                    
                    
                }
            }
            
            if($defVerdict != ""){
                echo '
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            document.getElementById("defTypeLabel").innerText = "' . $defType . '";
                            document.getElementById("defValue").innerText = "' . strtoupper($defVerdict) . '";
                        });
                    </script>
                ';
            }
        }
        
        catch(Exception $e){
            error_log($e->getMessage());
        }
    }
    
    
    function getTasks(){
        global $conn;
        
        try{
            $sql = "SELECT * FROM students WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                
                $projectID = $result["projectID"];
                $sectionID = $result["sectionID"];
                
                $sql = "SELECT tr.*, ts.taskName 
                        FROM tracking tr
                        JOIN tasks ts ON tr.taskID = ts.id
                        WHERE tr.projectID = ? AND ts.status = ? AND ts.taskName <> 'defense'";
                        
                $stmt = $conn->prepare($sql);
                $stmt->execute([$projectID, "enabled"]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if($results){
                    
                    foreach ($results as $result){
                       echo '
                            <tr class="table-row">
                                <td class="table-cell">'.ucwords($result["taskName"]).'</td>
                                <td class="table-cell">'.ucwords($result["status"]).'</td>
                                <td class="table-cell">'.$result["number"].' 
                                    <button class="copy-button" onclick="copyTrackingNum(\''.$result["number"].'\')"> 
                                        <img src="pages/images/copy.png" alt="Copy Icon"> 
                                    </button>
                                </td>
                            </tr>
                        ';
                    }
                }
            
            }
        }
        
        catch(Exception $e){
            error_log($e->getMessage());
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
        <script src="pages/session_tracker.js"></script>
        
        <title>Dashboard</title>
        
        
        <link rel="stylesheet" href="pages/dashboard_files/ContentPages.css"> 
        <link rel="stylesheet" href="pages/dashboard_files/Student.css"> 
        <link rel="stylesheet" href="pages/dashboard_files/calendar.css"> 
    </head>
    
    <body>
        <?php require "dashboard_header.php" ?>
        <?php require "dashboard_menu.php" ?>
        
            <div class="card-container">
                <!-- Top Bar Line Seen Above Container -->
             
                <!-- Accounts, Pie Charts, Academic Yr & Announcements -->
                <div class="card-border">
                    <div class="card-wrapper-flex-wrap"> <!-- WRAPS CONTENT HORIZONTALLY, FLEX (left - right, next line, left - right) -->
                        <div class="card-announcements"> <!-- Announcements -->
                            <div class="card-header">
                                <div class="card-announce" > 
                                    <span id="announcement-header" class="title">Announcements</span>
                                    <textarea id="announcement"  name="announcement" class="announce-area" disabled></textarea>   
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper-column"> <!--WRAPS CONTENT VERTICALLY -->
                        <!-- Access to Class Page // ADD LINK TO CLASS PAGE -->
                        <button class="card-class-page" onclick="window.location.href='/class_view';">
                            <div class="card-header">
                                <div class="card-acc" style="align-items: center;">
                                    <span class="title" style="font-size: 20px;">Go to Class Page</span>
                                  <span class="fas fa-chalkboard-teacher" style="margin-left: 5px; font-size: 20px;"><span  class="fas fa-arrow-right"></span></span>
                                    <a><!-- ADD LINK HERE (?)--></a>
                                </div>
                            </div>
                        </button>
                        <!-- Title Evaluation -->
                        <div class="card-pending-title">
                            <div class="card-header">
                                <div class="card-acc-defense">
                                    <span id="defTypeLabel" class="title">Defense Evaluation Pending</span>
                                    <br>
                                    <span id="defValue" class="acc-value"></span>
                                </div>
                            </div>
                        </div>                    
                    </div>
                    <div class="card-wrapper-column-2"> <!-- VERTICALLY ALIGN, CONTAINS ACAD YR & PROFILE PAGE -->
                        <div class="card-academicyr-acc" style="height: 50px;">
                            <div class="card-header">
                                <div class="card-acadyr">
                                    <span id="acadYearLabel" class="title"></span> 
                                </div>
                            </div>
                        </div>
                        <button class="card-profile-link" onclick="window.location.href='/profile';"> 
                            <div class="card-header">
                                <div class="card-profile">
                                    <span class="title style="font-size: 20px;"">Go To Profile Page</span> 
                                    <span class="fas fa-user" style="margin-left: 5px; font-size: 20px;"><span  class="fas fa-arrow-right"></span></span> 
                                </div>
                            </div>
                        </button>
                    </div>
                </div>  
                
                <!-- Student Requirements, Calendar -->
                <div class="card-border" style = "grid-template-columns: 3fr 1fr;">  <!-- 75% 25% grid percentage -->                  
                    <div class="card-wrapper-flex-wrap" style="display: block;">
                        <button class="collapsible" aria-expanded="false">Student Requirements
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: 40%;"></div>
                            </div>
                        </button>
                        <div class="content-collapsible"> <!--  area to reflect yung student requirements -->
                             <table class="table-container">
                                <thead>
                                    <tr class="table-row">
                                        <th class="table-header">Task</th>
                                        <th class="table-header">Status</th>
                                        <th class="table-header">Tracking Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php getTasks(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                    
                    <div class="card-wrapper-column-2">  <!-- WRAPS THE COLUMN VERTICALLY, CONTAINS CALENDAR AREA -->
                        <div class="card-academicyr-acc">
                            <div class="card-header">
                                <div class="calendar-wrapper">
                                    <header>
                                      <p class="current-date"></p>
                                      <div class="icons">
                                        <span id="prev" class="material-symbols-rounded">&lt</span>
                                        <span id="next" class="material-symbols-rounded">&gt</span>
                                      </div>
                                    </header>
                                    <div class="calendar">
                                      <ul class="weeks">
                                        <li>Sun</li>
                                        <li>Mon</li>
                                        <li>Tue</li>
                                        <li>Wed</li>
                                        <li>Thu</li>
                                        <li>Fri</li>
                                        <li>Sat</li>
                                      </ul>
                                      <ul class="days"></ul>
                                    </div>
                                  </div>
                            </div>
                        </div>
                    </div>
                </div>  
        </div>
        
        <?php
            getCurrentYear();
            getDefenseStatus();
            viewAnnouncement();
        ?>
   
    
        <!--<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="pages/dashboard_files/chart.js"></script>-->
        
        <script>
        
            function copyTrackingNum(trackingNumber) {
                // Create a temporary input element to hold the tracking number
                const tempInput = document.createElement('input');
                tempInput.value = trackingNumber; // Set the input value to the tracking number
                document.body.appendChild(tempInput); // Append the input to the body (not visible to users)
            
                // Select the input value
                tempInput.select();
                tempInput.setSelectionRange(0, 99999); // For mobile devices
            
                // Copy the selected text
                document.execCommand("copy");
            
                // Remove the temporary input from the document
                document.body.removeChild(tempInput);
            
                // Optionally, you can show a confirmation message
                alert("Copied tracking number: " + trackingNumber);
            }
            
            var menu = document.getElementById('menu');
            var hamburger = document.querySelector('.hamburger');
        
            hamburger.addEventListener('click', function() {
                menu.classList.toggle('expanded');
            });
        
        </script>
      
        <script defer> // CALENDAR
            const daysTag = document.querySelector(".days"),
            currentDate = document.querySelector(".current-date"),
            prevNextIcon = document.querySelectorAll(".icons span");
    
            // getting new date, current year and month
            let date = new Date(),
            currYear = date.getFullYear(),
            currMonth = date.getMonth();
    
            // storing full name of all months in array
            const months = ["January", "February", "March", "April", "May", "June", "July",
                        "August", "September", "October", "November", "December"];
    
            const renderCalendar = () => {
                let firstDayofMonth = new Date(currYear, currMonth, 1).getDay(), // getting first day of month
                lastDateofMonth = new Date(currYear, currMonth + 1, 0).getDate(), // getting last date of month
                lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay(), // getting last day of month
                lastDateofLastMonth = new Date(currYear, currMonth, 0).getDate(); // getting last date of previous month
                let liTag = "";
    
                for (let i = firstDayofMonth; i > 0; i--) { // creating li of previous month last days
                    liTag += `<li class="inactive">${lastDateofLastMonth - i + 1}</li>`;
                }
    
                for (let i = 1; i <= lastDateofMonth; i++) { // creating li of all days of current month
                    // adding active class to li if the current day, month, and year matched
                    let isToday = i === date.getDate() && currMonth === new Date().getMonth() 
                                && currYear === new Date().getFullYear() ? "active" : "";
                    liTag += `<li class="${isToday}">${i}</li>`;
                }
    
                for (let i = lastDayofMonth; i < 6; i++) { // creating li of next month first days
                    liTag += `<li class="inactive">${i - lastDayofMonth + 1}</li>`
                }
                currentDate.innerText = `${months[currMonth]} ${currYear}`; // passing current mon and yr as currentDate text
                daysTag.innerHTML = liTag;
            }
            renderCalendar();
    
            prevNextIcon.forEach(icon => { // getting prev and next icons
                icon.addEventListener("click", () => { // adding click event on both icons
                    // if clicked icon is previous icon then decrement current month by 1 else increment it by 1
                    currMonth = icon.id === "prev" ? currMonth - 1 : currMonth + 1;
    
                    if(currMonth < 0 || currMonth > 11) { // if current month is less than 0 or greater than 11
                        // creating a new date of current year & month and pass it as date value
                        date = new Date(currYear, currMonth, new Date().getDate());
                        currYear = date.getFullYear(); // updating current year with new date year
                        currMonth = date.getMonth(); // updating current month with new date month
                    } else {
                        date = new Date(); // pass the current date as date value
                    }
                    renderCalendar(); // calling renderCalendar function
                });
            });
        </script>
        
        <script> //BUTTON ON STUDENT REQUIREMENTS
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
            var menu = document.getElementById('menu');
                var hamburger = document.querySelector('.hamburger');
    
                hamburger.addEventListener('click', function() {
                    menu.classList.toggle('expanded');
                });
            });
        </script>
        
    <?php require "footer.php" ?>
    </body>
</html>

<?php
    require "connection.php";
    session_start();
    
    $current_yearLvl = 0;
    
    $selectedSection= isset($_GET['sectionValue']) ? $_GET['sectionValue'] : '';
    
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
    
    function countAccounts(){
        global $conn;
        
        try{
            $sql = "SELECT COUNT(*) FROM users WHERE status = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute(["active"]);
            $activeCount = $stmt->fetchColumn();
            
            $sql = "SELECT COUNT(*) FROM users WHERE status = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute(["pending"]);
            $pendingCount = $stmt->fetchColumn();
            
            $sql = "SELECT COUNT(*) FROM users WHERE status = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute(["inactive"]);
            $inactiveCount = $stmt->fetchColumn();
            
             echo '
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        document.getElementById("activeLabel").innerText = "' . $activeCount . '";
                        document.getElementById("pendingLabel").innerText = "' . $pendingCount . '";
                        document.getElementById("inactiveLabel").innerText = "' . $inactiveCount . '";
                    });
                </script>
            ';
        }
        
        catch(Exception $e){
            error_log($e->getMessage());
        }
    }
    
    function getSections(){
        global $conn, $current_yearLvl, $selectedSection;
        
        try{
            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $acadYear = $result["id"];
            $startYear = $result["start_year"];
            
            $currentYear = date("Y"); // Get the current year
            $nextYear = $currentYear + 1;
            
            if($currentYear > $startYear){
                $sql = "SELECT * FROM sections WHERE academicYearID = ? AND yearLevel = ? AND sectionID <> ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$acadYear, 3, 0]); //3rd years only
                $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if(count($sections) >= 1){
                    foreach($sections as $section){
                        $sectionName = $section["courseID"] . " " .$section["yearLevel"] . $section["section_letter"] . $section["section_group"];
                        $sectionID = $section["sectionID"];
                        
                        echo '<option name="sectionValue" value="' . htmlspecialchars($sectionID) . '">' . htmlspecialchars($sectionName) . '</option>';
                    }
                    
                    $current_yearLvl = 3;
                    
                    echo '
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                document.getElementById("title-paper-header").innerHTML = "Capstone Papers"
                            });
                        </script>
                    ';
                }
            }
            
            //CHANGE FROM 4 TO 3 AFTER TESTING
            
            else if($currentYear <= $startYear){
                $sql = "SELECT * FROM sections WHERE academicYearID = ? AND yearLevel = ? AND sectionID <> ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$acadYear, 4, 0]); //4th years only
                $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if(count($sections) >= 1){
                    foreach($sections as $section){
                        $sectionName = $section["courseID"] . " " .$section["yearLevel"] . $section["section_letter"] . $section["section_group"];
                        $sectionID = $section["sectionID"];
                        
                       
                        $isSelected = ($selectedSection == $sectionID) ? ' selected' : '';
                        echo "<option value=\"$sectionID\"$isSelected>$sectionName</option>";
                    }
                    
                    $current_yearLvl = 4;
                    
                    echo '
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                document.getElementById("title-paper-header").innerHTML = "Capstone Papers"

                            });
                        </script>
                    ';
                }
            }
        }
        
        catch(Exception $e){
            error_log($e->getMessage());
        }
    }
    
    
    function getTitleEvaluations($sectionID) {
        global $conn;
    
        try {
            if($sectionID != "none"){
                // Fetch the latest academic year
                $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $acadYear = $result["id"];
        
                // Prepare the SQL for fetching project IDs
                $sql = "SELECT cp.projectID FROM capstone_projects cp
                        JOIN sections sec ON cp.sectionID = sec.sectionID
                        WHERE sec.sectionID = ? 
                        AND sec.academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID, $acadYear]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                // Initialize counts
                $counts = [
                    'accept' => 0,
                    'pending' => 0,
                    'rejected' => 0
                ];
        
                // Prepare counting SQL
                $countSQL = "SELECT COUNT(*) FROM title_proposal WHERE projectID = ? AND result = ?";
                
                foreach ($results as $result) {
                    $projectID = $result["projectID"];
                    
                    // Count accepted
                    $stmt = $conn->prepare($countSQL);
                    $stmt->execute([$projectID, "accept"]);
                    $counts['accept'] += $stmt->fetchColumn();
        
                    // Count pending
                    $stmt->execute([$projectID, "pending"]);
                    $counts['pending'] += $stmt->fetchColumn();
        
                    // Count rejected
                    $stmt->execute([$projectID, "rejected"]);
                    $counts['rejected'] += $stmt->fetchColumn();
                }
        
                // Output the counts
                echo '
                    <script>
                        console.log("Accepted: " + ' . $counts['accept'] . ');
                        console.log("Rejected: " + ' . $counts['rejected'] . ');
                        console.log("Pending: " + ' . $counts['pending'] . ');
                    </script>
                ';
                
                 echo '
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                document.getElementById("title-paper-header").innerHTML = "Title Evaluations"
                                document.getElementById("accepted-count").innerHTML = "Accepted: <span style=\'color: white;\'> ' . $counts['accept'] . '</span>";
                                document.getElementById("rejected-submitted-count").innerHTML = "Rejected: <span style=\'color: white;\'> ' . $counts['rejected'] . '</span>";
                                document.getElementById("pending-evaluating-count").innerHTML = "Pending: <span style=\'color: white;\'> ' . $counts['pending'] . '</span>";
                                
                                // Change the display style to block or inline to show them
                                document.getElementById("accepted-count").style.display = "block";
                                document.getElementById("rejected-submitted-count").style.display = "block";
                                document.getElementById("pending-evaluating-count").style.display = "block";
                            });
                        </script>
                    ';
                    
                    //comeback here
            }
        } 
        
        catch (Exception $e) {
            error_log($e->getMessage());
            echo '
                <script>
                    console.log("Error: " + ' . json_encode($e->getMessage()) . ');
                </script>
            ';
        }
    }
    
    function getDefenseCount($sectionID) {
        global $conn, $current_yearLvl ;
    
        try {
            if($sectionID != "none"){
                // Fetch the latest academic year
                $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $acadYear = $result["id"];
        
                // Prepare the SQL for fetching project IDs
                $sql = "SELECT cp.projectID FROM capstone_projects cp
                        JOIN sections sec ON cp.sectionID = sec.sectionID
                        WHERE sec.sectionID = ? 
                        AND sec.academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID, $acadYear]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                // Initialize counts
                $counts = [
                    'approved' => 0,
                    'revisions' => 0,
                    'redefense' => 0
                ];
        
                // Prepare counting SQL
                $countSQL = "";
                

                $countSQL = "SELECT COUNT(*) FROM capstone_projects WHERE projectID = ? AND academicYearID = ? AND defense = ?";
           
                foreach ($results as $result) {
                    $projectID = $result["projectID"];
                    
                    // Count approved
                    $stmt = $conn->prepare($countSQL);
                    $stmt->execute([$projectID, $acadYear, "approved"]);
                    $counts['approved'] += $stmt->fetchColumn();
        
                    // Count revisions
                    $stmt->execute([$projectID, $acadYear, "approved with revisions"]);
                    $counts['revisions'] += $stmt->fetchColumn();
        
                    // Count redefense
                    $stmt->execute([$projectID, $acadYear, "redefense"]);
                    $counts['redefense'] += $stmt->fetchColumn();
                }
        
                // Output the counts
                echo '
                    <script>
                        console.log("Approved: " + ' . $counts['approved'] . ');
                        console.log("Redefense: " + ' . $counts['redefense'] . ');
                        console.log("Revisions: " + ' . $counts['revisions'] . ');
                        console.log("yearlevel: "  + ' . $current_yearLvl . ' )
                    </script>
                ';
                
                $isEmpty = $counts['approved'] == 0 && $counts['revisions'] == 0 && $counts['redefense'] == 0;
                
                 // Convert $counts array values into a JavaScript array
                $data = [$counts['approved'], $counts['revisions'], $counts['redefense']];
                
                echo '
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const isEmpty = ' . json_encode($isEmpty) . '; // Empty flag from PHP
                            const newData = [' . implode(", ", $data) . ']; // Data from PHP
    
                            if (isEmpty) {
                                // Handle empty data case
                                myPieChart2.data.datasets[0].data = [1]; // Placeholder slice
                                myPieChart2.data.labels = ["No Available Data Yet"]; // Placeholder label
                                myPieChart2.data.datasets[0].backgroundColor = ["#d3d3d3"]; // Placeholder color
                            } 
                            
                            else {
                                // Update chart with real data
                                myPieChart2.data.datasets[0].data = newData;
                                myPieChart2.data.labels = ["Approved", "Revisions", "Redefense"]; // Update labels
                                myPieChart2.data.datasets[0].backgroundColor = ["#066BA3", "#D1642E", "#1E1E1E"]; // Update colors
                            }
    
                            // Re-render the chart
                            myPieChart2.update();
                        });
                    </script>
                ';
            }
        } 
        
        catch (Exception $e) {
            error_log($e->getMessage());
            echo '
                <script>
                    console.log("Error in defense count: " + ' . json_encode($e->getMessage()) . ');
                </script>
            ';
        }
    }
    
    function getPaperCount($sectionID) {
        global $conn;
    
        try {
            if($sectionID != "none"){
                // Fetch the latest academic year
                $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $acadYear = $result["id"];
        
                // Prepare the SQL for fetching project IDs
                $sql = "SELECT cp.projectID FROM capstone_projects cp
                        JOIN sections sec ON cp.sectionID = sec.sectionID
                        WHERE sec.sectionID = ? 
                        AND sec.academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID, $acadYear]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                // Initialize counts
                $counts = [
                    'accept' => 0,
                    'evaluating' => 0,
                    'submitted' => 0
                ];
        
                // Prepare counting SQL
                $countSQL = "SELECT COUNT(*) FROM capstone_papers WHERE projectID = ? AND academicYearID = ? AND status = ?";
                
                foreach ($results as $result) {
                    $projectID = $result["projectID"];
                    
                    // Count accepted
                    $stmt = $conn->prepare($countSQL);
                    $stmt->execute([$projectID, $acadYear, "accepted"]);
                    $counts['accept'] += $stmt->fetchColumn();
        
                    // Count pending
                    $stmt->execute([$projectID, $acadYear, "evaluating"]);
                    $counts['evaluating'] += $stmt->fetchColumn();
        
                    // Count rejected
                    $stmt->execute([$projectID, $acadYear, "submitted"]);
                    $counts['submitted'] += $stmt->fetchColumn();
                }
        
                // Output the counts
                echo '
                    <script>
                        console.log("Accepted: " + ' . $counts['accept'] . ');
                        console.log("Evaluating: " + ' . $counts['evaluating'] . ');
                        console.log("Submitted: " + ' . $counts['submitted'] . ');
                    </script>
                ';
                
                 // Check if all counts are zero
                $isEmpty = $counts['accept'] == 0 && $counts['evaluating'] == 0 && $counts['submitted'] == 0;
                
                // Convert $counts array values into a JavaScript array
                $data = [$counts['accept'], $counts['evaluating'], $counts['submitted']];
                
                echo '
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const isEmpty = ' . json_encode($isEmpty) . '; // Empty flag from PHP
                            const newData = [' . implode(", ", $data) . ']; // Data from PHP
    
                            if (isEmpty) {
                                // Handle empty data case
                                myPieChart.data.datasets[0].data = [1]; // Placeholder slice
                                myPieChart.data.labels = ["No Available Data Yet"]; // Placeholder label
                                myPieChart.data.datasets[0].backgroundColor = ["#d3d3d3"]; // Placeholder color
                            } 
                            
                            else {
                                // Update chart with real data
                                myPieChart.data.datasets[0].data = newData;
                                myPieChart.data.labels = ["Accepted", "Evaluating", "Submitted"]; // Update labels
                                myPieChart.data.datasets[0].backgroundColor = ["#066BA3", "#D1642E", "#1E1E1E"]; // Update colors
                            }
    
                            // Re-render the chart
                            myPieChart.update();
                        });
                    </script>
                ';
            }
        } 
        
        catch (Exception $e) {
            error_log($e->getMessage());
            echo '
                <script>
                    console.log("Error: " + ' . json_encode($e->getMessage()) . ');
                </script>
            ';
        }
    }
    
    
    
    
    
    
    function getDefenseStatus(){ //PANG STUDENT NA FUNCTION NASALIT LANG DITO
        global $conn;
        
        try{
            $defVerdict = "";
            $defType = "";
            
            $sql = "SELECT * FROM students WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                
                $projectID = $result["projectID"];
                $sectionID = $result["sectionID"];
                
                $sql = "SELECT yearLevel FROM sections WHERE sectionID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($result){
                    
                    $yearLevel = $result["yearLevel"];
                    
                    $defType = ($yearLevel == 3) ? "Title Defense" : (($yearLevel == 4) ? "Final Defense" : "Defense");
                    
                    if($projectID >= 1){
                        
                        $sql = ""; //declare empty first
                        $identifier = "defense";
                        
                        $sql = "SELECT final_defense FROM capstone_projects WHERE projectID = ?"; 
                           
                        
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
                        WHERE tr.projectID = ? AND ts.status = ?";
                        
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
    
    function getTitles($sectionID) {
        global $conn;
    
        try {
            if ($sectionID != "none") {
                // Fetch the latest academic year
                $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
                $acadYear = $result["id"];
    
                // Fetch the section details
                $sql = "SELECT * FROM sections WHERE academicYearID = ? AND sectionID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$acadYear, $sectionID]);
                $section = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch a single section
    
                // Construct the section name
                $sectionName = $section["courseID"] . " " . $section["yearLevel"] . $section["section_letter"] . $section["section_group"];
    
                // Fetch the capstone projects
                $sql = "SELECT * FROM capstone_projects WHERE sectionID = ? AND academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID, $acadYear]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                  if ($results) {
                    // Begin JavaScript output
                    echo '<script>';
                    echo 'window.addEventListener("DOMContentLoaded", function() {';
                    echo 'let table = document.getElementById("title-table");';
    
                    // Create a function to append rows
                    echo 'function addRow(sectionName, groupNum, title) {
                        let row = document.createElement("tr");
                        row.className = "table-row";
                        row.innerHTML = `
                            <td class="table-cell">${sectionName}</td>
                            <td class="table-cell">${groupNum}</td>
                            <td class="table-cell">${title}</td>
                        `;
                        table.appendChild(row);
                    };';
    
                    // Loop through results and call the addRow function
                    foreach ($results as $result) {
                        echo 'addRow(' . json_encode($sectionName) . ', ' . json_encode($result["groupNum"]) . ', ' . json_encode($result["title"]) . ');';
                    }
    
                    echo '});';
                    echo '</script>';
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
    
    function viewAnnouncement(){
        global $conn;
        
        try {
            $sql = "SELECT * FROM announcement WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([1]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                
                date_default_timezone_set('Asia/Manila'); // Set to your desired timezone
                
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
    
    function saveAnnouncement($content){
        global $conn;
        
        try{
            date_default_timezone_set('Asia/Manila'); // Set to your desired timezone
            
            $sql = "UPDATE announcement SET content = ?, date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$content, date('Y-m-d'), 1]);
            
            if($result){
                viewAnnouncement();
            }
        }
        
        catch(Exception $e){
            error_log($e->getMessage());
        }
    }
    
    
    function getFacultyCount(){
        global $conn;
        $html = '';
    
        try {
            // Fetch the latest academic year
            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            $acadYear = $result["id"];
            
            $sql = "SELECT * FROM faculty_count";
            $stmt = $conn->prepare($sql);
            $stmt->execute([]);
            $faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if (count($faculties) >= 1) {
                foreach ($faculties as $faculty) {
                    $sql = "SELECT id, surname, firstname, middlename, email FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$faculty["facultyID"]]);
                    $getUser = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $userID = $getUser["id"];
                    $name = $getUser["surname"] . ", " . $getUser["firstname"] . " " . $getUser["middlename"];
                    $email = $getUser["email"];
    
                    $cordCount = $faculty["coordinator_count"] . "/" . $faculty["coordinator_limit"];
                    $panelCount = $faculty["panelist_count"] . "/" . $faculty["panelist_limit"];
                    $adviseCount = $faculty["adviser_count"] . "/" . $faculty["adviser_limit"];
    
                    // Begin JavaScript output
                    echo '<script>';
                    echo 'window.addEventListener("DOMContentLoaded", function() {';
                    echo 'let table = document.getElementById("faculty-table");'; // Use the correct table ID
                    
                    // Create a function to append rows
                    echo 'function addRow(userID, name, coordCount, panelCount, adviseCount, email) {
                        let row = document.createElement("tr");
                        row.className = "table-row";
                        row.innerHTML = `
                            <td class="table-cell"><input type="checkbox" class="toggle-buttons"></td>
                            <td class="table-cell">${userID}</td>
                            <td class="table-cell">${name}</td>
                            <td class="table-cell">
                                <div class="icon-container">
                                    <span class="status coordinated">${coordCount}</span>
                                    <button class="icon add" name="coordinator_add_${userID}_${name}">+</button>
                                    <button class="icon remove" name="coordinator_subtract_${userID}_${name}">-</button>
                                </div>
                            </td>
                            <td class="table-cell">
                                <div class="icon-container">
                                    <span class="status paneled">${panelCount}</span>
                                    <button class="icon add" name="panelist_add_${userID}_${name}">+</button>
                                    <button class="icon remove" name="panelist_subtract_${userID}_${name}">-</button>
                                </div>
                            </td>
                            <td class="table-cell">
                                <div class="icon-container">
                                    <span class="status advised">${adviseCount}</span>
                                    <button class="icon add" name="adviser_add_${userID}_${name}">+</button>
                                    <button class="icon remove" name="adviser_subtract_${userID}_${name}">-</button>
                                </div>
                            </td>
                            <td class="table-cell">${email}</td>
                        `;
                        table.appendChild(row);
                        
                        // Add event listener for checkbox in each row to toggle button visibility
                        const checkbox = row.querySelector(".toggle-buttons");
                        const buttons = row.querySelectorAll(".icon.add, .icon.remove");
                    
                        checkbox.addEventListener("change", function() {
                            if (this.checked) {
                                buttons.forEach(button => button.style.display = "inline-block");
                            } else {
                                buttons.forEach(button => button.style.display = "none");
                            }
                        });
                    };';
                    
                    echo 'addRow(' . json_encode($userID) . ', ' . json_encode($name) . ', ' . json_encode($cordCount) . ', ' . json_encode($panelCount) . ', ' . json_encode($adviseCount) . ', ' . json_encode($email) . ');';
                    
                    echo '});';
                    echo '</script>';
                }
            }
        } 
        
        catch(Exception $e) {
            error_log($e->getMessage());
        }
    
        return $html; //do not remove this
    }
    
    function console_log($message) {
        echo "<script>console.log('$message');</script>";
    }
    
   function updateCount() {
        global $conn;
    
        // Start capturing console logs
        console_log('Starting updateCount');
    
        try {
            // Fetch the latest academic year
            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            $acadYear = $result["id"];
            console_log("Academic Year: $acadYear");
    
            $conn->beginTransaction();
    
            foreach ($_POST as $key => $value) {
                // Use regex to match the button names
                if (preg_match('/(coordinator|panelist|adviser)_(add|subtract)_(\d+)_(.+)/', $key, $matches)) {
                    // Extracting values
                    $type = $matches[1]; // "coordinator", "panelist", or "adviser"
                    $action = $matches[2]; // "add" or "subtract"
                    $facultyID = $matches[3]; // Faculty ID from the button name
                    $facultyName = str_replace('_', ' ', $matches[4]); // Replace underscores with whitespace
    
                    console_log("Processing: type=$type, action=$action, facultyID=$facultyID");
    
                    // Retrieve the current counts from the database
                    $sql = "SELECT * FROM faculty_count WHERE facultyID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$facultyID]);
                    $counts = $stmt->fetch(PDO::FETCH_ASSOC);
    
                    if ($counts) {
                        $currentCount = 0;
                        $limit = 0;
    
                        // Determine the current count to update
                        if ($type == 'coordinator') {
                            $currentCount = $counts['coordinator_count'];
                            $limit = $counts["coordinator_limit"];
                        } else if ($type == 'panelist') {
                            $currentCount = $counts['panelist_count'];
                            $limit = $counts["panelist_limit"];
                        } else if ($type == 'adviser') {
                            $currentCount = $counts['adviser_count'];
                            $limit = $counts["adviser_limit"];
                        }
    
                        console_log("Current Count: $currentCount, Current Limit: $limit");
    
                        // Update the counts based on action
                        if ($action == 'add') {
                            $limit++;
                            console_log("Increasing limit: new limit = $limit");
                        } 
                        
                        else if ($action == 'subtract' && $limit > $currentCount && $limit > 1) {
                            $limit--;
                            console_log("Decreasing limit: new limit = $limit");
                        } 
                        
                        else {
                            // If trying to subtract but limit is at the current count or 0, do not change limit
                            console_log("No change to limit for facultyID $facultyID (action: $action)");
                            continue; // Skip to the next iteration
                        }
    
                        // Update the database
                        $updateSQL = "UPDATE faculty_count SET {$type}_limit = ? WHERE facultyID = ?";
                        $updateStmt = $conn->prepare($updateSQL);
                        $result = $updateStmt->execute([$limit, $facultyID]);
    
                        if ($result) {
                            $conn->commit();
                            console_log("Successfully updated faculty count for facultyID $facultyID");
                            
                            unset($_POST[$key]);
                            unset($_POST["source"]);
                            
                            echo'
                                <script>
                                    Swal.fire({
                                        title: "Success",
                                         text: "' . ucwords($action) . ' ' . ucwords($type) . ' Limit of Faculty: ' .$facultyName. ' Successful!",
                                        icon: "success",
                                        confirmButtonText: "OK"
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "/dashboard_admin";
                                        }
                                        
                                        else if (result.isDismissed) {
                                             window.location.href = "/dashboard_admin";
                                        }
                                    });
                                </script>
                            ';
                            
                            break; // Exit loop if successful
                        } 
                        
                        else {
                            throw new Exception("Failed to update faculty count for facultyID $facultyID");
                        }
                    } 
                    
                    else {
                        console_log("No records found for facultyID $facultyID");
                    }
                }
            }
        } 
        catch (Exception $e) {
            $conn->rollBack();
            console_log("Error: " . addslashes($e->getMessage()));
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
        
        <link rel="stylesheet" href="pages/dashboard_files/dashboard-header.css"> <!-- WENDRIC GALING ALA AKONG GINALAW -->
        <link rel="stylesheet" href="pages/dashboard_files/calendar.css"> <!-- CALENDAR -->
        <link rel="stylesheet" href="pages/dashboard_files/ContentPages.css"> <!-- MOST CONTENTS ARE HERE -->
        
         <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   
       
    </head>
    
    <body>
        <?php require "dashboard_header.php" ?>
        <?php require "dashboard_menu.php" ?>
        
              
                <!-- Top Bar Line Seen Above Container -->
             
                  <!-- Accounts, Pie Charts, Academic Yr & Announcements -->
                <div class="card-border">
                    <div class="card-wrapper-column"> <!--WRAPS CONTENT VERTICALLY -->
                        <div class="card-accounts-statuses">
                        <!-- Active Account -->
                        <h3 style="text-align: center;">Account Status Counts</h3>
                        <div class="card-active-acc">
                            <div class="card-header">
                                <div class="card-acc">
                                        <i class="fas fa-user-check"></i> <!-- Icon for Active Account -->
                                    <span class="title1">Active Accounts</span>
                                    <span id="activeLabel" class="acc-value"></span>
                                </div>
                            </div>
                        </div>
                        <!-- Pending Account -->
                        <div class="card-pending-acc">
                            <div class="card-header">
                                <div class="card-acc">
                                        <i class="fas fa-user-clock"></i> <!-- Icon for Pending Account -->
                                    <span class="title2">Pending Accounts</span>
                                    <span id="pendingLabel" class="acc-value"></span>
                                </div>
                            </div>
                        </div>
                        <!-- Inactive Account -->
                        <div class="card-suspended-acc">
                            <div class="card-header">
                                <div class="card-acc">
                                     <i class="fas fa-user-slash"></i>
                                    <span class="title3">   <!-- Icon for Inactive Account -->Inactive Accounts</span>
                                    <span id="inactiveLabel" class="acc-value"></span>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    </div>
                    <!--CapPaper-->
                    <div class="card-wrapper-flex-wrap"> <!-- WRAPS CONTENT HORIZONTALLY, FLEX (left - right, next line, left - right) -->
                        <div class="card-pie-chart-title-eval"> <!-- Title Evaluation -->
                            <div class="card-header">
                                <div class="title-dropdown">
                                    <form action="" method="GET">
                                        <select id="title-select-evaluation" name="sectionValue" onchange="this.form.submit()" style="width: 150px;">
                                            <option value="none">Select Section</option>
                                                <?php getSections(); ?>
                                            </select>
                                            </form>
                                     </div>
                                    <div class="card-acc">
                                    <!-- CONTENT HERE -->
                                     
                                    <div class="card-chart">
                                        <div class="left-content">
                                         
                                            <span id="title-paper-header" class="chart-heading">Capstone Papers</span>
                                            
                                        </div>
                                    
                                        <div class="programming-stats">
                                            <div class="chart-container">
                                                 <canvas id="myPieChart" width="300" height="300" style="width: 300px; height: 300px;"></canvas>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                     <!--DefCount-->
                    <div class="card-wrapper-flex-wrap"> <!-- WRAPS CONTENT HORIZONTALLY, FLEX (left - right, next line, left - right) -->
                        <div class="card-pie-chart-title-eval"> <!-- Title Evaluation -->
                            <div class="card-header">
                                <div class="title-dropdown">
                                <br><br>
                     </div>
                     
                     <div class="card-acc">
                                    <!-- CONTENT HERE -->
                                     
                                    <div class="card-chart">
                                        <div class="left-content">
                                         
                                            <span id="defense-header" class="chart-heading">Total Defense Count</span>
                                            
                                        </div>
                                    
                                        <div class="programming-stats">
                                            <div class="chart-container">
                                                <canvas id="myPieChart2" width="300" height="300" style="width: 300px; height: 300px;"></canvas>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="card-wrapper-column-2"> <!-- Academsic Yr & Announcements (button to be added) -->
                        <div class="card-announcements" style="position: relative;">
                            <div class="card-header">
                                <form action="" method="POST">
                                    
                                    <div class="card-announce">
                                        <div class="announcement-header-container">
                                            <i class="fas fa-bullhorn"></i>
                                            <span id="announcement-header" class="title"></span>
                                        </div>
                                        
                                        <br>
                                        
                                        <textarea id="announcement" name="announcement" class="announce-area"></textarea>
                                    </div>
                                    <button class="announce-button" name="saveAnnounceBtn">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>  
                
                <!-- Student Requirements, Faculty Table, Calendar, //Activity Logs (to be added) -->
                <div class="card-border" style = "grid-template-columns: 3fr 1fr;">                   
                    <div class="card-wrapper-flex-wrap" style="display: block;">
                        <button class="collapsible" aria-expanded="false">Capstone Group Titles
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: 60%;"></div>
                            </div>
                        </button>
                        <div class="content-collapsible">
                            <table id="title-table" class="table-container">
                                <thead>
                                    <tr class="table-row">
                                        <th class="table-header">Section</th>
                                        <th class="table-header">Group Number</th>
                                        <th class="table-header">Capstone Title</th>
                                    </tr>
                                </thead>
                                <tbody>
                        
                                </tbody>
                            </table>
                        </div>
                        
                        </div>
                         <div class="card-wrapper-column-2" style="max-height: 50px;">
                            <div class="card-academicyr-acc">
                                <div class="card-header">
                                    <div class="card-acadyr">
                                        <span id="acadYearLabel" class="title"></span> 
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        
                        <div class="card-border" style="grid-template-columns: 3fr 1fr;">                   
                            <div class="card-wrapper-flex-wrap" style="display: block;">
                                <div class="card-table-faculty">
                                    <div class="card-header">
                                        <div class="card-acc">
                                              <!-- CONTENT HERE -->
                                            <form action="" method="POST">
                                                 <input type="hidden" name="source" value="faculty_count_table"> <!-- Hidden input -->
                                                 <table id="faculty-table" class="report-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="table-header"></th>
                                                            <th class="table-header">Faculty ID</th>
                                                            <th class="table-header">Name</th>
                                                            <th class="table-header">Coordinated</th>
                                                            <th class="table-header">Paneled</th>
                                                            <th class="table-header">Advised</th>
                                                            <th class="table-header">Email</th>
                                                        </tr>
                                                    </thead>
                                                    
                                                    <tbody>
        
                                                    </tbody>
                                                </table>
                                            </form>
                                        </div>
                                        
                                    </div>
                                </div>
                        </div>
                    <div class="card-wrapper-column-3">
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
            countAccounts();
            viewAnnouncement();
            getFacultyCount();
        ?>
            

        
        <script>
                //This is for the form submit using the + or - buttons
                document.querySelectorAll('.icon.add, .icon.remove').forEach(button => {
                    button.addEventListener('click', function(event) {
                        // Prevent default behavior
                        event.preventDefault();
                        // Find the closest form and submit it
                        this.closest('form').submit();
                    });
                });
        </script>
        
        <script>//Animation ng list pag nag select dropdown
            const evaluationDropdown = document.getElementById("title-select-evaluation");
            const listItems = document.querySelectorAll(".chart-outputs li"); // Select all list items
            
            // Function to apply hover effect to list items
            function applyHoverEffect() {
                // Add the hover class to each list item
                listItems.forEach(item => {
                    item.classList.add('hover-effect');
                });
            
                // Optionally, remove the effect after a certain period
                setTimeout(() => {
                    listItems.forEach(item => {
                        item.classList.remove('hover-effect');
                    });
                }, 1000); // Duration of the hover effect (in milliseconds)
            }
            
            // Event listener for the select element
            evaluationDropdown.addEventListener("change", function() {
                applyHoverEffect(); // Call the function to apply hover effect
            });
        </script>

        <script> // KAY WENDRIC PARA SA MENU
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
        
        
        
        <script>
            // Initialize the first chart with default data
            const ctx = document.getElementById('myPieChart').getContext('2d');
            const myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['No Section Selected'], // Placeholder label
                    datasets: [{
                        data: [1], // Default value for the placeholder slice
                        backgroundColor: ['#d3d3d3'], // Placeholder color (light gray)
                    }]
                },
                options: {
                    responsive: false, // Disable responsive resizing
                    maintainAspectRatio: false, // Disable aspect ratio maintenance
                    plugins: {
                        legend: {
                            position: 'bottom', // Legend position at the bottom
                            labels: {
                                boxWidth: 20, // Reduce the size of the color box
                                padding: 15, // Adjust padding between legend items
                            }
                        }
                    },
                    layout: {
                        padding: {
                            bottom: 30, // Add space below the legend
                        }
                    }
                }
            });
        
            // Initialize the second chart with default data
            const ctx2 = document.getElementById('myPieChart2').getContext('2d');
            const myPieChart2 = new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: ['No Section Selected'], // Placeholder label
                    datasets: [{
                        data: [1], // Default value for the placeholder slice
                        backgroundColor: ['#d3d3d3'], // Placeholder color (light gray)
                    }]
                },
                options: {
                    responsive: false, // Disable responsive resizing
                    maintainAspectRatio: false, // Disable aspect ratio maintenance
                    plugins: {
                        legend: {
                            position: 'bottom', // Legend position at the bottom
                            labels: {
                                boxWidth: 20, // Reduce the size of the color box
                                padding: 15, // Adjust padding between legend items
                            }
                        }
                    },
                    layout: {
                        padding: {
                            bottom: 30, // Add space below the legend
                        }
                    }
                }
            });
        </script>
        
        
    <?php
    
        if($_SERVER["REQUEST_METHOD"] == "GET"){
            if(isset($_GET["sectionValue"])){
                //getTitleEvaluations($_GET["sectionValue"]);
                getDefenseCount($_GET["sectionValue"]);
                getPaperCount($_GET["sectionValue"]);
                getTitles($_GET["sectionValue"]);
            }
        }
        
         if($_SERVER["REQUEST_METHOD"] == "POST"){
             if(isset($_POST["saveAnnounceBtn"])){
                  if (isset($_POST["announcement"])) {
                    saveAnnouncement($_POST["announcement"]);
                  }
             }
             
             if(isset($_POST['source']) && $_POST['source'] == 'faculty_count_table'){
                 updateCount();
             }
         }
    
    ?>
    
        
    <?php require "footer.php" ?>
    </body>
</html>

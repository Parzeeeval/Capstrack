<?php

    require 'vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    
    require "connection.php";
    
    
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["report-btn"])){
            generateReport();
        }
    }

    
   function generateReport() {
        global $conn;
    
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Advisers_Report_' . date('F_d_Y') . '.xlsx"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: private');
            
            // Fetch all users
            $sql = "SELECT u.id, u.surname, u.firstname, u.middlename, u.email FROM users u JOIN faculty f ON f.id = u.id WHERE u.id <> '0'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Create a new spreadsheet
            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0); // Remove the default sheet
    
            foreach ($users as $user) {
                $userID = $user["id"];
                $fullname = $user["surname"] . ", " . $user["firstname"] . " " . $user["middlename"];
                $email = $user["email"];
    
                // Fetch academic year
                $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $acadyear = $stmt->fetch(PDO::FETCH_ASSOC);
                $acadyearID = $acadyear["id"];
                $year = $acadyear["start_year"] . "-" . $acadyear["end_year"];
                
    
                $sql = "SELECT projectID FROM advisers WHERE adviserID = ? AND academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$userID, $acadyearID ]);
                $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                
                // Create a new sheet for this user
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle(substr($fullname, 0, 30)); // Limit sheet title to 30 characters
    
                // Add headers to the sheet
                $sheet->setCellValue('A1', 'Course ID');
                $sheet->setCellValue('B1', 'Specialization');
                $sheet->setCellValue('C1', 'Section');
                $sheet->setCellValue('D1', 'Academic Year');
    
                // Apply styles to the headers
                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'size' => 22, // Set font size for headers to 22
                        'color' => ['argb' => '000000'], // Black text color
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFA500'], // Orange background color
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];
    
                // Apply the header styles
                $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
    
                // Populate the sheet with section data
                $row = 2; // Start from the second row
                
                
                foreach ($projects as $project){
                    $projectID = $project["projectID"];
                    
                    $sql = "SELECT sectionID FROM capstone_projects WHERE projectID = ? AND academicYearID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$projectID, $acadyearID ]);
                    $getSection = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $sectionID = $getSection["sectionID"];
                    
                    
                    $sql = "SELECT * FROM sections WHERE sectionID = ?  AND academicYearID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$sectionID, $acadyearID ]);
                    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                   
                    foreach ($sections as $section) {
                        $sectionName = $section["courseID"] . " " . $section["yearLevel"] . $section["section_letter"] . $section["section_group"];
                        $sheet->setCellValue("A$row", $section["courseID"]);
                        $sheet->setCellValue("B$row", $section["specialization"]);
                        $sheet->setCellValue("C$row", $sectionName);
                        $sheet->setCellValue("D$row", $year);
                        $row++;
                    }
                    
                }
                
                
                // Adjust column widths for better readability
                foreach (range('A', 'D') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true); // Auto-size columns A-D
                }
    
                // Set general font size for content (rows) to 18
                $sheet->getStyle('A2:D' . $row)->getFont()->setSize(18);
    
                // Apply border styles to the table content
                $sheet->getStyle('A1:D' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
            }
    
            // Write the file to output
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
            
        } 
        
        catch (Exception $e) {
            echo '
                <script>
                    console.log('.$e->getMessage().');
                </script>
            ';
        }
    }


    
    function getAccess(){
        global $conn;
        
        try{
            $sql = "SELECT accessLevel FROM faculty WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                
                if($result["accessLevel"] <= 0){
                    echo '
                        <script>
                            window.location.href = "/404";
                        </script>
                    ';
                }
            }
        }
        
        catch(Exception $e) {
           
        }
    }
    
    
    
    $userID = NULL;
    
    // Check if a dropdown value has been sent via GET
    if (isset($_GET['facultyID'])) {
        
        // Sanitize the input to prevent XSS
        $userID = htmlspecialchars($_GET['facultyID']);
        
        // Store the selected value in a session variable

        // Example response
        echo '
            <script>
                console.log("ID: ' . $userID . '");
            </script>
        ';
        
        displayUserInfo();
        
        exit; // Important to exit after sending a response
    }
    
   
    function displayUserInfo(){
        global $conn, $userID;
        
        try{
            $sql = "SELECT surname, firstname, middlename, email FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $fullname = $user["surname"] . ", " . $user["firstname"] . " " . $user["middlename"] . " (" . $user["email"] . ")";
            
            echo '
                <script>
                    document.getElementById("userLabel").innerText = ' . json_encode($fullname) . ';
                </script>
            ';

            
            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $acadyear = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $acadyearid = $acadyear["id"];
            
            $year = $acadyear["start_year"] . "-" . $acadyear["end_year"];
            
            
            $sql = "SELECT projectID FROM advisers WHERE adviserID = ? AND academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID, $acadyearid]);
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<script> 
                    document.getElementById("sectionsTable").querySelector("tbody").innerHTML = "";
                    document.getElementById("sectionsTable").querySelector("tbody");
                </script>';
            
            
            foreach ($projects as $project){
                $projectID = $project["projectID"];
                
                $sql = "SELECT sectionID FROM capstone_projects WHERE projectID = ? AND academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$projectID, $acadyearid]);
                $getSection = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $sectionID = $getSection["sectionID"];
                
                
                $sql = "SELECT * FROM sections WHERE sectionID = ?  AND academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID, $acadyearid]);
                $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
    
                foreach ($sections as $section) {
                    $section_name = $section["courseID"] . " " . $section["yearLevel"] . $section["section_letter"] . $section["section_group"];
                
                    echo '<script>
                        document.getElementById("sectionsTable").querySelector("tbody").appendChild(
                            Object.assign(document.createElement("tr"), {
                                innerHTML: `
                                    <td>' . htmlspecialchars($section["courseID"]) . '</td>
                                    <td>' . htmlspecialchars($section["specialization"]) . '</td>
                                    <td>' . htmlspecialchars($section_name) . '</td>
                                    <td>' . htmlspecialchars($year) . '</td>
                                `
                            })
                        );
                    </script>';
                }
            }
        }
        
        catch(Exception $e){
             error_log($e->getMessage()); // Log the error for debugging
        }
    }
    
    
    function getAdviserValues(){
          global $conn;
          
        try{
              $sql = "SELECT users.id, users.email, users.firstname, users.middlename, users.surname
                        FROM users
                        JOIN faculty ON faculty.id = users.id
                        WHERE faculty.id <> '0' ";
        
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                

                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $facultyID = htmlspecialchars($result["id"]);
                    
                    echo '<option value="'.$facultyID.'">' 
                         . htmlspecialchars($result["surname"]) . ', ' . htmlspecialchars($result["firstname"]) . ' ' 
                         . htmlspecialchars($result["middlename"]) . ' (' . htmlspecialchars($result["email"]) . ') ' .
                         '</option>';
                 }
        }
          
        catch(Exception $e){
             error_log($e->getMessage()); // Log the error for debugging
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
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <script src="pages/session_tracker.js"></script>
        
        <link rel="stylesheet" href="pages/accounts.css">

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <title>Advisers Reports</title>
        
        <style>
            .accounts-container {
                background-color: #fff;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 6px 5px rgba(112, 112, 112, 0.2);
                max-width: 1000px;
                margin: 0 auto;
                margin-top: 20px;
                font-family: 'Lexend', sans-serif;
            }
            #content h2 {
                font-size: 18px;
                font-weight: 600;
                margin-bottom: 50px;
                padding-bottom: 4px;
                position: relative;
                border-bottom: 2px solid #DEDEDE;
            }
            .adviser-section label {
                font-weight: bold;
                font-size: 18px;
            }
            .adviser-box {
                background-color: #f0f0f0;
                padding: 12px;
                border-radius: 5px;
                display: flex;
                align-items: center;
                gap: 10px;
                height: 48px;
                box-sizing: border-box;
                border: 1px solid #ccc;
                color: #494949;
            }
            .adviser-box i {
                font-size: 30px;
                color: gray;
            }
            .adviser-box p {
                font-size: 16px;
                font-weight: bold;
            }
            .search-access-section label {
                font-weight: bold;
                margin-bottom: 5px;
            }
            .search-box {
                position: relative;
                width: 250px;
            }
            .search-box i {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: gray;
                font-size: 16px;
                pointer-events: none;
            }
            .search-box input {
                padding-left: 30px;
                width: 100%;
                height: 48px;
                border-radius: 5px;
                border: 1px solid #ccc;
                font-size: 16px;
                box-sizing: border-box;
                outline: none;
            }
            .search-box input:focus {
                border-color: #66afe9;
            }
            .custom-dropdown {
                height: 150px;
                width: 100%;
                font-size: 16px;
                border: 1px solid #ccc;
                border-radius: 5px;
                padding: 5px;
                box-sizing: border-box;
            }
            .accounts-body {
                display: flex;
                gap: 20px;
            }
            .left-section {
                flex: 1;
            }
            .right-section {
                flex: 2;
            }
            .accounts-footer {
                display: flex;
                justify-content: flex-end;
                margin-top: 20px;
            }
            .btn-update {
                background-color: #066BA3;
                color: white;
                padding: 12px 25px;
                border: none;
                border-radius: 5px;
                font-size: 16px;
                cursor: pointer;
            }
            .btn-update:hover {
                background-color: #055b88;
            }
            
            .divider-label{
                text-align: center;
                font-size: 20px;
                font-weight: bold;
            }
            
            .divider {
                width: 50%;
                margin: 20px auto;
                border-top: 2px solid #ffad60;
            }
            
           /* Table container styling */
            .table-container {
                margin-top: 20px;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                background-color: #fff;
                max-width: 100%;
                max-height: 450px; /* Limit the container's height */
                overflow-x: auto; /* Allow horizontal scrolling if needed */
                overflow-y: auto; /* Allow vertical scrolling if needed */
            }
            
            /* Table styling */
            table {
                margin-top: 20px;
                width: 100%;
                border-collapse: collapse;
                text-align: left;
                font-family: Arial, sans-serif;
                font-size: 16px;
            }
            
            /* Header styling */
            table thead tr {
                background-color: orange;
                color: white;
            }
            
            table th {
                padding: 12px 15px;
                font-weight: bold;
                font-size: 24px;
                text-align: center;
                border: 1px solid #ddd;
            }
            
            th, td {
                padding: 12px 20px;
                text-align: center;
                border: 1px solid #ddd;
                font-size: 20px;
            }
            th {
                background-color: #ffad60;
                color: white;
                font-weight: 500;
            }
            tr:nth-child(even) {
                background-color: lightgray;
            }
            tr:hover {
                background-color: #f1f1f1;
                transform: scale(1.01);
                transition: transform 0.3s ease;
            }

        </style>
    </head>
    
    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
        <?php getAccess(); ?>
        
        
        <div id="content2">
            <div class="profile-tabs">
                <div class="tabs">
                    <button onclick="window.location.href='/reports_coordinators';">Coordinators</button>
                    <button onclick="window.location.href='/reports_panelists';">Panelists</button>
                    <button class="active" onclick="window.location.href='/reports_advisers';">Advisers</button>
                    <button onclick="window.location.href='/reports_titles';">Capstone Titles</button>
                    <button onclick="window.location.href='/reports_defense';">Defense Verdicts and Schedules</button>
                </div>
            </div>

       <!--<h2>Accounts</h2>
        <div class="accounts-header">-->
        
            <br>
            
           <div id="result" style="display: none;"></div> <!-- This div will be updated with the response but is hidden -->
            

        <div class="accounts-body">
            <div class="left-section">
                
                <div class="panelist-access-group">
                    <div class="adviser-section">
                        <label for="adviser">Adviser</label>
                        <div id="adviser" class="adviser-box">
                            <i class="fas fa-user-circle"></i>
                            <p id="userLabel"></p>
                        </div>
                    </div>
                </div>
                
                <br><br><br>
                
                <label for="adviserSearch">Search an Adviser using 
                    <span style="color: #41A763;">Name</span> or
                    <span style="color: #41A763;">Email</span>
                </label>
                
                <br>
                
                <input type="text" id="adviserSearch" placeholder="Enter Name or Email" onkeyup="filterAdvisers()" style="font-size: 20px; width: 100%;">
                
                <br><br>
                
                <select class="custom-dropdown" name="adviserBox" id="adviserResults" size="10"  style="height: 300px;" required>
                    <?php getAdviserValues(); ?>
                </select>
            </div>
            <div class="right-section">
                
                <p class="divider-label">Advised Capstone Groups</p>
                <div class="divider"></div>

                <div class="table-container">
                    <table id="sectionsTable">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Specialization</th>
                                <th>Section</th>
                                <th>Academic Year</th>
                            </tr>
                        </thead>
                        <tbody>
    
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="accounts-footer">
            <form action="" method="POST">
                <button type="submit" name="report-btn" class="btn-update">Generate Advisers Report</button>
            </form>
        </div>

    <script>
    
        $(document).ready(function() {
            $('#adviserResults').click(function() { //Change or Click depends on situation
                const selectedValue = $(this).val(); // Get the selected value
                
                // Send an AJAX GET request
                $.get('', { facultyID: selectedValue }, function(response) {
                    $('#result').html(response); // Update the #result div with the response
                });
            });
        });
        
        function filterAdvisers() {
            const input = document.getElementById('adviserSearch');
            const filter = input.value.toLowerCase();
            const select = document.getElementById('adviserResults');
            const options = select.options;

            // Loop through all options and hide those that don't match the input
            for (let i = 0; i < options.length; i++) {
                const optionText = options[i].text.toLowerCase();
                if (optionText.includes(filter)) {
                    options[i].style.display = ""; // Show option
                } else {
                    options[i].style.display = "none"; // Hide option
                }
            }
        }
    </script>
    
    
    <?php require 'footer.php'; ?>
    </body>
</html>
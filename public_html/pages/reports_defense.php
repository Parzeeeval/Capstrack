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
            header('Content-Disposition: attachment; filename="Defense_Verdicts_Report ' . date('F_d_Y') . '.xlsx"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: private');
            
            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0); // Remove the default sheet
            
            // Fetch academic year
            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $acadyear = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $acadyearID = $acadyear["id"];
            $year = $acadyear["start_year"] . "-" . $acadyear["end_year"];
            
            
            $sql = "SELECT * FROM sections WHERE sectionID <> '0' AND academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$acadyearID]);
            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            

            foreach ($sections as $section){
                $sectionID = $section["sectionID"];
                
                $name_of_section = htmlspecialchars(
                    $section["courseID"] . "-" . $section["yearLevel"] . $section["section_letter"] . 
                    $section["section_group"],
                    ENT_QUOTES,
                    'UTF-8'
                );
                
                // Remove invalid characters for Excel sheet names
                $name_of_section = preg_replace('/[:\\/?*[\]]/', '', $name_of_section);
                
                // Trim to 31 characters to meet Excel's title length limit
                $sanitized_section_name = substr($name_of_section, 0, 31);
                
                if (empty($sanitized_section_name)) {
                    $sanitized_section_name = "Untitled"; // Fallback if name becomes empty
                }
                
                // Create a new sheet and set its title
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle($sanitized_section_name);
                

                // Add headers to the sheet
                $sheet->setCellValue('A1', 'Section');
                $sheet->setCellValue('B1', 'Group No.');
                $sheet->setCellValue('C1', 'Title');
                $sheet->setCellValue('D1', 'Defense Date');
                $sheet->setCellValue('E1', 'Defense Verdict');
                $sheet->setCellValue('F1', 'Academic Year');
                
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
                $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
                
                $row = 2; // Start from the second row
                
                
                $sql = "SELECT * FROM capstone_projects WHERE projectID <> '0' AND sectionID = ? AND academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID, $acadyearID]);
                $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                 
                foreach ($projects as $project){
                    $projectID = $project["projectID"];
                    
                    $sql = "SELECT date FROM defense_dates WHERE projectID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$projectID]);
                    $date = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if(!empty($date["date"])){
                        $timestamp = strtotime($date["date"]);
                        $formattedDate = date('F j, Y', $timestamp);
                    }
                    
                    else{
                        $formattedDate = "TBD";
                    }
                    
      
                    $sheet->setCellValue("A$row", $name_of_section);
                    $sheet->setCellValue("B$row", $project["groupNum"]);
                    $sheet->setCellValue("C$row", $project["title"]);
                    $sheet->setCellValue("D$row", $formattedDate);
                    $sheet->setCellValue("E$row", $project["defense"]);
                    $sheet->setCellValue("F$row", $year);
                    
                    $row++;
                }
                
                 // Adjust column widths for better readability
                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true); // Auto-size columns A-D
                }
                
                // Center align the content in A2:C$row
                $dataAlignment = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];
                
                $sheet->getStyle('A2:F' . $row)->applyFromArray($dataAlignment);
            
    
                // Set general font size for content (rows) to 18
                $sheet->getStyle('A2:F' . $row)->getFont()->setSize(18);
    
                // Apply border styles to the table content
                $sheet->getStyle('A1:F' . $row)->applyFromArray([
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
            error_log($e->getMessage()); // Log the error instead of printing it
            exit; // Stop further execution
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
    
    
    
    $sectionID = NULL;
    
    // Check if a dropdown value has been sent via GET
    if (isset($_GET['sectionID'])) {
        
        // Sanitize the input to prevent XSS
        $sectionID = htmlspecialchars($_GET['sectionID']);
        
        // Store the selected value in a session variable

        // Example response
        echo '
            <script>
                console.log("ID: ' . $sectionID . '");
            </script>
        ';
        
        displayInfo();
        
        exit; // Important to exit after sending a response
    }
    
   
    function displayInfo(){
        global $conn, $sectionID;
        
        try{

            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $acadyear = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $acadyearid = $acadyear["id"];
            
            $year = $acadyear["start_year"] . "-" . $acadyear["end_year"];
            
            
            $sql = "SELECT * FROM capstone_projects WHERE projectID <> '0' AND sectionID = ? AND academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$sectionID, $acadyearid]);
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
                
                
                $sql = "SELECT * FROM sections WHERE sectionID = ? AND academicYearID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$sectionID, $acadyearid]);
                $section = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $sql = "SELECT date FROM defense_dates WHERE projectID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$projectID]);
                $date = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($date["date"] != "" || $date["date"] != NULL){
                    $timestamp = strtotime($date["date"]);
                    $formattedDate = date('F j, Y', $timestamp);
                }
                
                else{
                    $formattedDate = "TBD";
                }
                
                
                $section_name = $section["courseID"] . " " . $section["yearLevel"] . $section["section_letter"] . $section["section_group"];
                
                
                echo '<script>
                    document.getElementById("sectionsTable").querySelector("tbody").appendChild(
                        Object.assign(document.createElement("tr"), {
                            innerHTML: `
                                <td>' . htmlspecialchars($section_name) . '</td>
                                <td>' . htmlspecialchars($project["groupNum"]) . '</td>
                                <td>' . htmlspecialchars($project["title"]) . '</td>
                                <td>' . htmlspecialchars($formattedDate) . '</td>
                                <td>' . htmlspecialchars($project["defense"]) . '</td>
                                <td>' . htmlspecialchars($year) . '</td>
                            `
                        })
                    );
                </script>';
                
                echo '
                    <script>
                        document.getElementById("userLabel").innerText = ' . json_encode($section_name) . ';
                    </script>
                ';
            }
        }
        
        catch(Exception $e){
             error_log($e->getMessage()); // Log the error for debugging
        }
    }
    
    
    function getSections(){
          global $conn;
          
        try{
            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $acadyear = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $acadyearid = $acadyear["id"];
        
            $sql = "SELECT * FROM sections WHERE sectionID <> '0' AND academicYearID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$acadyearid]);
            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($sections as $section){
                
                echo '<option value="'.$section["sectionID"].'">' 
                     . htmlspecialchars($section["courseID"]) . ' ' . htmlspecialchars($section["yearLevel"]) . htmlspecialchars($section["section_letter"]) . htmlspecialchars($section["section_group"]) .
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
        
        <title>Defense & Verdicts Reports</title>
        
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
            .sections-section label {
                font-weight: bold;
                font-size: 18px;
            }
            .section-box {
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
            .section-box i {
                font-size: 30px;
                color: gray;
            }
            .section-box p {
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
                    <button onclick="window.location.href='/reports_advisers';">Advisers</button>
                    <button onclick="window.location.href='/reports_titles';">Capstone Titles</button>
                    <button class="active" onclick="window.location.href='/reports_defense';">Defense Verdicts and Schedules</button>
                </div>
            </div>

       <!--<h2>Accounts</h2>
        <div class="accounts-header">-->
        
            <br>
            
           <div id="result" style="display: none;"></div> <!-- This div will be updated with the response but is hidden -->
            

        <div class="accounts-body">
            <div class="left-section">
                
                <div class="section-access-group">
                    <div class="sections-section">
                        <label for="section">Section</label>
                        <div id="section" class="section-box">
                            <i class="fas fa-user-circle"></i>
                            <p id="userLabel"></p>
                        </div>
                    </div>
                </div>
                
                <br><br><br>
                
                <label for="sectionSearch">Search a Section using
                    <span style="color: #41A763;">Course</span> or
                    <span style="color: #41A763;">Section letter / Section Group No.</span>
                </label>
                
                <br>
                
                <input type="text" id="sectionSearch" placeholder="Enter Section" onkeyup="filterSections()" style="font-size: 20px; width: 100%;">
                
                <br><br>
                
                <select class="custom-dropdown" name="sectionBox" id="sectionsResults" size="10"  style="height: 300px;" required>
                    <?php getSections(); ?>
                </select>
            </div>
            <div class="right-section">
                
                <p class="divider-label">Defense Dates & Verdicts</p>
                <div class="divider"></div>
                
                
                <div class="table-container">
                    <table id="sectionsTable">
                        <thead>
                            <tr>
                                <th>Section</th>
                                <th>Group No.</th>
                                <th>Title</th>
                                <th>Defense Date</th>
                                <th>Defense Verdict</th>
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
                <button type="submit" name="report-btn" class="btn-update">Generate Defense & Verdicts Report</button>
            </form>
        </div>

    <script>
    
        $(document).ready(function() {
            $('#sectionsResults').click(function() { //Change or Click depends on situation
                const selectedValue = $(this).val(); // Get the selected value
                
                // Send an AJAX GET request
                $.get('', { sectionID: selectedValue }, function(response) {
                    $('#result').html(response); // Update the #result div with the response
                });
            });
        });
        
        function filterSections() {
            const input = document.getElementById('sectionSearch');
            const filter = input.value.toLowerCase();
            const select = document.getElementById('sectionsResults');
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
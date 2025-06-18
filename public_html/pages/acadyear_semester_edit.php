<?php 
    require "connection.php";
    session_start();
    
    function getSettings(){
        global $conn;
        
        try{
            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                $acadyearID = $result["id"];
            
                $start_year = $result["start_year"];
                $end_year = $result["end_year"];
                
                $start_month = $result["start_month"];
                $end_month = $result["end_month"];
                
                $start_day = $result["start_day"];
                $end_day = $result["end_day"];
                
                
                $nextsem_year = $result["nextsem_year"];
                $nextsem_month = $result["nextsem_month"];
                $nextsem_day = $result["nextsem_day"];
                
                $mode = $result["mode"];
                
                
                
                $startYear = "{$result['start_year']}-{$result['start_month']}-{$result['start_day']}";
                $endYear = "{$result['end_year']}-{$result['end_month']}-{$result['end_day']}";
                $nextSemester = "{$result['nextsem_year']}-{$result['nextsem_month']}-{$result['nextsem_day']}";
    
                // Echo JavaScript to update the Flatpickr values
                echo "
                    <script>
                        // Set values for Flatpickr calendars
                        document.getElementById('capDuration').value = '$mode';
                        document.getElementById('startYear')._flatpickr.setDate('$startYear');
                        document.getElementById('endYear')._flatpickr.setDate('$endYear');
                        document.getElementById('startSemester')._flatpickr.setDate('$nextSemester');
                    </script>
                ";
                
                
            }
        }
        
        catch(Exception $e){
            
        }
    }    
    
    
    
    
    
    
    
    
    
    function saveSettings($start_date, $end_date, $nextsem_date, $mode){
        global $conn;
        
        try{
            $conn->beginTransaction();
            
            $sql = "SELECT id FROM academic_year ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                $id = $result["id"];
                
                $date1 = new DateTime($start_date);
                $date2 = new DateTime($end_date);
                $date3 = new DateTime($nextsem_date);
                
                $start_year = $date1->format('Y'); // Get the year
                $start_month = $date1->format('m'); // Get the month
                $start_day = $date1->format('d'); // Get the day
                 
                $end_year = $date2->format('Y');
                $end_month = $date2->format('m');
                $end_day = $date2->format('d');
                
                $nextsem_year = $date3->format('Y');
                $nextsem_month = $date3->format('m');
                $nextsem_day = $date3->format('d');
                
                $sql = "UPDATE academic_year SET start_year = ?, start_month = ?, start_day = ?,  end_year = ?, end_month = ?, end_day = ?, nextsem_year = ?, nextsem_month = ?, nextsem_day = ?, mode = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([$start_year, $start_month, $start_day, $end_year, $end_month, $end_day, $nextsem_year, $nextsem_month, $nextsem_day, $mode, $id]);
                
                if($result){
                    
                    $sql = "SELECT surname, firstname, middlename FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["userID"]]);
                    $name = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $surname = $name["surname"];
                    $firstname = $name["firstname"];
                    $middlename = $name["middlename"];
                    
                    $start_month_name = date('F', mktime(0, 0, 0, $start_month, 10)); $end_month_name = date('F', mktime(0, 0, 0, $end_month, 10)); $nextsem_month_name = date('F', mktime(0, 0, 0, $nextsem_month, 10));
                    
                    $settings = $start_year . "-" . $end_year . " / Start month: " . $start_month_name . ", " . $start_day . " / End month: " . $end_month_name . ", " . $end_day . " / Next sem year and month: " . $nextsem_month_name . ", " . $nextsem_day . ", " . $nextsem_year . " / Mode: " . $mode;
                    
                    $action = "". $surname . ", " . $firstname . " " . $middlename . " updated the academic year with the settings of: " . $settings;
                                     
                     date_default_timezone_set('Asia/Manila');
                     $date = date('Y-m-d H:i:s');
            
                     $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                     $stmt = $conn->prepare($sql);
                     $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                     
                     $conn->commit();
                     
                     
                    
                    unset($_POST["save-btn"]);
                    
                    echo '<script>
                            Swal.fire({
                                title: "Success",
                                text: "Academic Year Settings Saved!",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                   window.location.href = window.location.pathname;
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = window.location.pathname;
                                }
                            });
                          </script>';
                }
                
                else{
                    throw new Exception("Failed to save academic year settings");
                }
            }
            
            else{
                throw new Exception("Failed to get academic year");
            }
            
        }
        
        catch(Exception $e){
            $conn->rollBack();
            
            unset($_POST["save-btn"]);
            // Log or display the error
            echo '<script>
                    Swal.fire({
                        title: "Error",
                        text: "Error in saving settings: '.$e->getMessage().'",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                           window.location.href = window.location.pathname;
                        }
                        
                        else if (result.isDismissed) {
                            window.location.href = window.location.pathname;
                        }
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
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="pages/session_tracker.js"></script>
        <title>Academic Year Date Selection</title>
        
        <!-- Include Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/flatpickr.min.css">
        <!-- Include the monthSelectPlugin CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/plugins/monthSelect/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
        
        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/plugins/monthSelect/index.js"></script>
        
        
        
        <!-- Styling -->
        <style>
            .parent-container {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                justify-content: center;
                margin: 20px;
            }
        
            .parent-container {
                display: flex;
                flex-wrap: wrap;
                gap: 30px;
                justify-content: center;
                margin: 20px;
            }

            .container {
                background: white(145deg, #ffffff, #e6e6e6);
                border: 1px solid black;
                border-radius: 10px;
                padding: 30px;
                width: 45%; /* Two containers per row */
                text-align: center;
                min-width: 300px; /* Ensure good display on smaller screens */
            }

            h1 {
                font-size: 24px;
                color: #333;
                margin-bottom: 20px;
            }

            label {
                font-size: 16px;
                color: #555;
                display: block;
                margin-bottom: 10px;
                text-align: left;
            }

            input[type="text"], select {
                /*background-color: #007bff; */
                background-color: #066BA3;
                color: white;
                width: 100%;
                padding: 15px;
                border: 1px solid black;
                border-radius: 5px;
                font-size: 24px;
                margin-bottom: 20px;
            }
            
            input[type="text"]::placeholder, select::placeholder {
                color: white; /* Change placeholder text color to white */
            }

            .save-button, .refresh-button {
                padding: 15px 25px;
                font-size: 18px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: all 0.3s ease;
                width: 200px;
            }

            .save-button {
                background-color: #28a745;
                color: #fff;
            }

            .save-button:hover {
                background-color: #218838;
            }

            .refresh-button {
                background-color: #D1642E;
                color: #fff;
            }

            .refresh-button:hover {
                background-color: #D1642E;
            }

            .button-section {
                display: flex;
                justify-content: center;
                gap: 30px;
                margin-top: 40px;
            }

            @media (max-width: 768px) {
                .container {
                    width: 90%; /* Full width on smaller screens */
                }
            }
            
            
            
           /* Calendar Body Background */
            .flatpickr-calendar {
                background-color: #e6f7ff; /* Light blue background for the entire calendar */
                border: 1px solid #87ceeb; /* Light blue border */
                color: #333; /* Text color */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
            }
            
            
            /* Days Grid Styling */
            .flatpickr-days {
                background-color: #d1ecf8; /* Slightly darker blue for the days grid */
                border-radius: 5px; /* Smooth corners for the grid */
            }
            
            /* Individual Day Cells */
            .flatpickr-day {
                background-color: #e6f7ff; /* Matching light blue background */
                color: #333; /* Default text color for dates */
                border-radius: 3px; /* Slight rounding of each day */
                transition: background-color 0.2s ease, color 0.2s ease; /* Smooth hover effect */
            }
            
            /* Hover Effect on Days */
            .flatpickr-day:hover {
                background-color: #87cefa; /* Light sky blue on hover */
                color: #fff; /* White text when hovered */
            }
            
            /* Selected Day */
            .flatpickr-day.selected {
                background-color: #4682b4; /* Steel blue for selected date */
                color: #fff; /* White text */
            }
            
            /* Today's Date */
            .flatpickr-day.today {
                border: 1px solid #1e90ff; /* Dodger blue outline for today's date */
                background-color: #b0e0e6; /* Powder blue for today's background */
                color: #000; /* Black text for contrast */
                font-weight: bold;
            }
            
            /* Weekdays Header (e.g., Mon, Tue) */
            .flatpickr-weekdays {

                color: #fff; /* White text for headers */
                font-weight: bold;
            }
            
            /* Week Numbers (if enabled) */
            .flatpickr-weekday {
                color: #005f87; /* Deep blue for the weekday labels */
            }

        </style>
    </head>
    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
        
        <form action="" method="POST">
            
            <!-- HTML Structure -->
            <div class="parent-container">
                <div class="container">
                    <h1>Capstone Duration</h1>
                    <label for="capDuration">Capstone Duration:</label>
                    <select id="capDuration" name="mode">
                        <option value="1">3rd Year 2nd semester -- 4th Year 1st semester</option>
                        <option value="2">3rd Year 1st semester -- 3rd Year 2nd semester</option>
                        <option value="3">4th Year 1st semester -- 4th Year 2nd semester</option>
                    </select>
                </div>
            
                <div class="container">
                    <h1>Start of Academic Year</h1>
                    <label for="startYear">Start Date:</label>
                    <input type="text" id="startYear" name="startyear" placeholder="Select start date" required>
                </div>
            
                <div class="container">
                    <h1>End of Academic Year</h1>
                    <label for="endYear">End Date:</label>
                    <input type="text" id="endYear" name="endyear" placeholder="Select end date" required>
                </div>
            
                <div class="container">
                    <h1>Start of New Semester</h1>
                    <label for="startSemester">Start Date:</label>
                    <input type="text" id="startSemester" name="nextsem" placeholder="Select semester start date" required>
                </div>
            </div>
            
            <div class="button-section">
                <button type="submit" name="save-btn" class="save-button">Save Settings</button>
                <button class="refresh-button" onclick="location.reload()">Undo Changes</button>
            </div>
        </form>
    
        
       <script>
            function updateEndYearAndStartSemesterValues(selectedYear) {
                const nextYear = selectedYear + 1;
        
                // Update #endYear limits and value
                endYearPicker.set({
                    minDate: `${selectedYear}-01-01`,
                    maxDate: `${nextYear}-12-31`
                });
                const endYearValue = endYearPicker.input.value;
                const endYearDate = new Date(endYearValue);
                if (
                    !endYearValue || 
                    endYearDate.getFullYear() < selectedYear || 
                    endYearDate.getFullYear() > nextYear
                ) {
                    const adjustedEndYearDate = new Date(selectedYear, 5, 1); // Set default to June of selected year
                    endYearPicker.setDate(adjustedEndYearDate);
                }
        
                // Update #startSemester limits and value
                startSemesterPicker.set({
                    minDate: `${selectedYear}-01-01`,
                    maxDate: `${nextYear}-12-31`
                });
                const startSemesterValue = startSemesterPicker.input.value;
                const startSemesterDate = new Date(startSemesterValue);
                if (
                    !startSemesterValue || 
                    startSemesterDate.getFullYear() < selectedYear || 
                    startSemesterDate.getFullYear() > nextYear
                ) {
                    const adjustedStartSemesterDate = new Date(nextYear, 0, 1); // Set default to January of next year
                    startSemesterPicker.setDate(adjustedStartSemesterDate);
                }
            }
        
            // Initialize Flatpickr for #startYear
            const startYearPicker = flatpickr("#startYear", {
                defaultDate: new Date(new Date().getFullYear(), 7), // Default: August (8th month)
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F d Y",
                mode: "single",
                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        const selectedYear = selectedDates[0].getFullYear();
                        updateEndYearAndStartSemesterValues(selectedYear); // Update limits and values dynamically
                        markAsUnsaved(instance.altInput); // Mark unsaved changes
                    }
                }
            });
        
            // Initialize Flatpickr for #endYear
            const endYearPicker = flatpickr("#endYear", {
                defaultDate: new Date(new Date().getFullYear() + 1, 5), // Default: June next year
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F d Y",
                mode: "single",
                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        markAsUnsaved(instance.altInput); // Mark unsaved changes
                    }
                }
            });
        
            // Initialize Flatpickr for #startSemester
            const startSemesterPicker = flatpickr("#startSemester", {
                defaultDate: new Date(new Date().getFullYear() + 1, 0), // Default: January next year
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F d Y",
                mode: "single",
                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        markAsUnsaved(instance.altInput); // Mark unsaved changes
                    }
                }
            });
        
            // Function to mark inputs as unsaved by changing background color
            function markAsUnsaved(input) {
                input.style.backgroundColor = "#d1642e"; // Set background color to orange
            }
        
            // Run on page load
            document.addEventListener("DOMContentLoaded", () => {
                const startYearInput = document.querySelector("#startYear");
                if (startYearInput.value) {
                    const existingDate = new Date(startYearInput.value);
                    const existingYear = existingDate.getFullYear();
                    updateEndYearAndStartSemesterValues(existingYear); // Initialize limits and values
                }
            });
        </script>


        
            
        <?php require 'footer.php'; ?>
        
        <?php getSettings(); ?>
        
        
        <?php
        
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["save-btn"])){
                    if(isset($_POST["startyear"]) && isset($_POST["endyear"]) && isset($_POST["nextsem"]) && isset($_POST["mode"])){
                        saveSettings($_POST["startyear"], $_POST["endyear"], $_POST["nextsem"], $_POST["mode"]);
                    }
                }
            }
        
        ?>
    </body>
</html>

<?php 
    session_start();
    require 'connection.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveBtn'])) {
        $projectID = $_POST['saveBtn']; // Single projectID from the button clicked
        $dates = $_POST['date'];     // Array of all dates submitted
    
        // Loop through project IDs and dates to find the specific projectID
        foreach ($_POST['projectID'] as $index => $id) {
            if ($id == $projectID) {
                $selectedDate = $dates[$index];
                break;
            }
        }
    
        // Save the selected date for the specific projectID
        try {
            $conn->beginTransaction();
            
            $sql = "UPDATE defense_dates SET date = ? WHERE projectID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$selectedDate, $projectID]);
            
            if ($stmt->rowCount() > 0) {
                // Update succeeded
                echo '
                    <script>
                        console.log("Date saved successfully");
                    </script>
                ';
            } 
            
            else {
                // No rows updated; insert a new record
                $sql = "INSERT INTO defense_dates (projectID, date) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([$projectID, $selectedDate]);
                
                if($result){
                    echo '
                        <script>
                            console.log("New date inserted");
                        </script>
                    ';
                }
                
                else{
                    throw new Exception("Error inserting defense date");
                }
            }
            
             $sql = "SELECT * FROM sections WHERE sectionID = ?";
             $stmt = $conn->prepare($sql);
             $stmt->execute([$_SESSION["sectionID"]]);
             $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
             $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
             
             
             $sql = "SELECT groupNum FROM capstone_projects WHERE projectID = ?";
             $stmt = $conn->prepare($sql);
             $stmt->execute([$projectID]);
             $result = $stmt->fetch(PDO::FETCH_ASSOC);
             
             $groupNum = $result["groupNum"];
            
            
             $sql = "SELECT * FROM users WHERE id = ?";
             $stmt = $conn->prepare($sql);
             $stmt->execute([$_SESSION["userID"]]);
             $result = $stmt->fetch(PDO::FETCH_ASSOC);
              
             $firstname = $result["firstname"];
             $surname = $result["surname"];
             $middlename = $result["middlename"];
             
             
             $readableDate = date("F j, Y g:i A", strtotime($selectedDate));
                    
             $action = "". $surname . ", " . $firstname . " " . $middlename . " updated the defense date of Group " . $groupNum . " in " . $section . " into " . $readableDate;
             
             date_default_timezone_set('Asia/Manila');
             $date = date('Y-m-d H:i:s');
    
             $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
             $stmt = $conn->prepare($sql);
             $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
            
             if(!$result){
                 throw new Exception("Failed to insert action logs");  
             }
            
            $conn->commit(); // Commit the transaction
        } 
        
        catch (Exception $e) {
            $conn->rollBack(); // Rollback transaction on error
            
            echo '<script>
                console.error("Error: ' . addslashes($e->getMessage()) . '");
            </script>';
        }
    }
    
    function getGroups(){
        global $conn;
        
        try{
            $sql = "SELECT * FROM capstone_projects WHERE sectionID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["sectionID"]]);
            $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if(count ($groups) >= 1){
                foreach($groups as $group){
                    $title = $group["title"];
                    $groupNum = $group["groupNum"];
                    $projectID = $group["projectID"];
                    
                    $sql = "SELECT * FROM defense_dates WHERE projectID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$projectID]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $date = "";
                    
                    if($result){
                        $date = $result["date"];
                    }
                    
                    else if(!$result){
                        $date = NULL;
                    }
                    
                    echo "<tr>";
                        echo "<td>" . "Group " . htmlspecialchars($groupNum) . "<br>" . htmlspecialchars($title) . "</td>";
                        
                        // Hidden input for projectID
                        echo "<input type='hidden' name='projectID[]' value='" . htmlspecialchars($projectID) . "'>";
                        
                        // Flatpickr input for date and time
                        echo "<td><input type='text' name='date[]' class='flatpickr' value='" . htmlspecialchars($date) . "' placeholder='Select Date & Time'></td>";
                        
                        // Save button (optional, but you don't need a button inside a form row for submission)
                        echo "<td><button type='submit' name='saveBtn' value='" . htmlspecialchars($projectID) . "' class='save-btn'>Save</button></td>";
                    echo "</tr>";
                }
            }
        }
        
        catch(Exception $e){
            
        }
    }
    
    function getSection(){
         global $conn;
        
        try{
            $section = "";
            
            $sql = "SELECT * FROM sections WHERE sectionID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["sectionID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
            }
          
            return $section;
        }
        
        catch(Exception $e){
            
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Capstone Groups</title>
        <link rel="stylesheet" href="pages/card_layout.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="pages/session_tracker.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        
        <!-- Toastify CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        
        <!-- Toastify JS -->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        
        <style>
            /* Table styles */
            table {
                width: 95%; /* Maximize table width */
                margin: 20px;
                border-collapse: collapse;
                background-color: #ffffff;
                border-radius: 10px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            }

            th, td {
                padding: 20px;
                text-align: center;
                font-size: 18px;
                border: 2px solid #dcdcdc;
            }

            th {
                background-color: #ffad60; /* Orange color for header */
                color: white;
            }

            td {
                background-color: #f8f8f8; /* Light gray for table cells */
            }

            tr:nth-child(even) td {
                background-color: #f1f1f1; /* Zebra stripe effect */
            }

            /* Input and button styles */
            input[type="text"] {
                width: 100%;
                padding: 12px;
                font-size: 16px;
                border: 2px solid #dcdcdc;
                border-radius: 8px;
                box-sizing: border-box;
                margin: 5px 0;
            }

            button {
                padding: 12px 20px;
                background-color: #ffad60; /* Orange color for button */
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                cursor: pointer;
            }

            button:hover {
                background-color: #e68a3b; /* Darker orange on hover */
            }

            /* Flatpickr styles */
            .flatpickr-input {
                padding: 12px;
                font-size: 16px;
                width: 100%;
                border-radius: 8px;
                border: 2px solid #dcdcdc;
                box-sizing: border-box;
            }

            .flatpickr-calendar {
                z-index: 9999; /* Ensure the calendar dropdown appears on top */
            }
            
            /* Divider line styles */
            .divider {
                width: 100%;
                height: 5px;
                background-color: #dcdcdc; /* Light gray color for the divider */
                margin: 20px 0;  /* Add some space around the divider */
            }

        </style>
    </head>
    
    <body>
        <?php require 'header.php'; ?> <!--This is for the topbar -->
        <?php require 'menu.php'; ?> <!--This is for the menu -->
    
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <h1 style="text-align: center;">Capstone Defense Dates</h1>
        <h1 style="text-align: center;"><?php echo getSection(); ?></h1>
        
         <!-- Divider Line -->
        <div class="divider"></div>  <!-- Line divider -->
        
        <form action="" method="POST">
            <table id="dynamic-table" border="1">
                <thead>
                    <tr>
                        <th>Capstone Group and Title</th>
                        <th>Capstone Defense Date</th>
                        <th>Save</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows will be dynamically generated here -->
                    
                    <?php getGroups(); ?>
                </tbody>
            </table>
        </form>
        
       <script>
        // Initialize Flatpickr for all calendar inputs
        flatpickr('.flatpickr', {
            enableTime: true,
            noCalendar: false,
            dateFormat: 'Y-m-d H:i', // 24-hour format
            altFormat: 'F j, Y h:i K', // Display format
            altInput: true, // Display the formatted date in the input field
            onReady: function(selectedDates, dateStr, instance) {
                // Store the initial valid value (empty if not set)
                instance.config._lastValidValue = instance.input.value;
    
                // If the input already has a date (e.g., retrieved from database)
                if (instance.input.value) {
                    const currentDate = new Date();
                    const inputDate = new Date(instance.input.value);
                    
                    // If the input date is in the past, allow it, else restrict to today's date
                    if (inputDate < currentDate) {
                        instance.config.minDate = inputDate; // Allow past date (no restriction)
                        
                        // Disable dates between the retrieved date and today
                        const disableRangeStart = new Date(inputDate);
                        const disableRangeEnd = new Date(currentDate);
                        instance.config.disable = [
                            function(date) {
                                return date > disableRangeStart && date < disableRangeEnd; // Disable dates between
                            }
                        ];
                    } else {
                        instance.config.minDate = "today"; // Restrict to today's date and onward
                    }
                } else {
                    instance.config.minDate = "today"; // Default to today's date for empty inputs
                }
            },
            onChange: function(selectedDates, dateStr, instance) {
                const now = new Date(); // Current date and time
                const selectedDate = selectedDates[0]; // Selected date from Flatpickr
    
                // Store the current valid value
                instance.config._lastValidValue = dateStr;
            },
        });
    
        document.querySelectorAll('.save-btn').forEach(function(button) {
            button.addEventListener('click', function(e) {
                // Get the parent row of the clicked button
                const row = e.target.closest('tr');
                const flatpickrInput = row.querySelector('.flatpickr');
                const saveButton = row.querySelector('button[type="submit"]');
                
                if (flatpickrInput.value.trim() === '') {
                    // If the date is empty, prevent form submission for this row
                    e.preventDefault();  // Prevent the button's form submission
                    row.style.backgroundColor = '#f8d7da';  // Highlight the row in red
                    flatpickrInput.style.borderColor = 'red';  // Highlight the flatpickr input
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please fill in the defense date for this group.',
                    });
                } 
                else {
                    // If date is not empty, allow the form to submit normally
                    row.style.backgroundColor = '';  // Reset row background color
                    flatpickrInput.style.borderColor = '#dcdcdc';  // Reset border color
                }
            });
        });
    </script>


        
        <?php require 'footer.php'; ?> 
    </body>
</html>

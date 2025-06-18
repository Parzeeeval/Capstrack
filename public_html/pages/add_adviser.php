<!DOCTYPE html>
<html>

<?php 
    require "connection.php";
    session_start();
    
    function getSectionValue() {
        global $conn;
        
        try {
            $sectionID = $_SESSION["sectionID"];
            
            $sql = "SELECT * FROM sections WHERE sectionID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$sectionID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $section = htmlspecialchars($result['courseID']) . ' ' . htmlspecialchars($result['yearLevel']) . htmlspecialchars($result['section_letter']) . htmlspecialchars($result['section_group']);
            
            return $section;
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
    
    function getFaculties() {
        global $conn;
    
        try {
            // First, get all panelists and advisers for the current user
            $userID = $_SESSION["userID"];
            $projectID = $_SESSION["projectIDValue"];
            
            $sql = "SELECT panelistID FROM panelists WHERE projectID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$projectID]);
            $isPanelist = $stmt->fetchAll(PDO::FETCH_COLUMN); // Fetching only the column of panelist IDs
            
            $sql = "SELECT adviserID FROM advisers WHERE projectID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$projectID]);
            $isAdviser = $stmt->fetchAll(PDO::FETCH_COLUMN); // Fetching only the column of adviser IDs
    
            // Now, fetch all faculties who are not the current user, not ID 0, and are of type 'faculty'
            $sql = "SELECT * FROM users WHERE id <> ? AND id <> ? AND type = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID, 0, "faculty"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($results as $result) {
                $facultyID = htmlspecialchars($result["id"]);
                $facultyName = htmlspecialchars($result["surname"]) . ', ' . htmlspecialchars($result["firstname"]) . ' ' . htmlspecialchars($result["middlename"]);
                $facultyEmail = htmlspecialchars($result["email"]);
    
                // Check if the faculty is either a panelist or an adviser
                if (!in_array($facultyID, $isPanelist) && !in_array($facultyID, $isAdviser)) {
                    // Echo an option element with faculty details
                    echo "<option value=\"$facultyID\">$facultyName - ($facultyEmail)</option>";
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage()); // Log the error message
        }
    }

        
    
    function addAdviser() {
        global $conn;
        
        try {
            $conn->beginTransaction();
            
            $adviserID = isset($_POST["facultyBox"]) ? htmlspecialchars($_POST["facultyBox"]) : '';
            
            if ($adviserID != "None") {
                $projectID = $_SESSION["projectIDValue"];
                
                
                $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $yearResult = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $acadYear = $yearResult["id"];
                
                $sql = "SELECT surname, firstname, middlename FROM users WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$adviserID]);
                $getUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
                $name = $getUser["surname"] . ", " . $getUser["firstname"] . " " . $getUser["middlename"];

                
                $sql = "SELECT * FROM faculty_count WHERE facultyID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$adviserID]);
                $countResult = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($countResult){
                    $current_count = $countResult["adviser_count"];
                    $current_limit = $countResult["adviser_limit"];
                    
                    if(($current_count + 1) <= $current_limit){
                        
                        $newCount = $current_count + 1;
                        
                        $sql = "UPDATE faculty_count SET adviser_count = ? WHERE facultyID = ?";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$newCount, $adviserID]);
                        
                        if($result){
                        
                            $sql = "INSERT INTO advisers (adviserID, projectID, academicYearID) VALUES (?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$adviserID, $projectID, $acadYear]);
                            
                            if ($result) {
                                
                                $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$_SESSION["userID"]]);
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                $coord_name = $user["firstname"] . " " . $user["surname"] ;
                                
                                $section = getSectionValue();
                                
                                $desc = $coord_name . " added you as a CAPSTONE ADVISER for a capstone group in " . $section;
                                
                                date_default_timezone_set('Asia/Manila');
                                $date = date('Y-m-d H:i:s');
                                
                                $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$adviserID, $desc, $date]);
                                
                                if($result){
                                    
                                    
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
                                     $stmt->execute([$adviserID]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                      
                                     $adviser_name = "". $result["surname"] . ", " . $result["firstname"]  . " " . $result["middlename"] ;
                                    
                                    
                                     $sql = "SELECT * FROM users WHERE id = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["userID"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                      
                                     $firstname = $result["firstname"];
                                     $surname = $result["surname"];
                                     $middlename = $result["middlename"];
    
    
                                     $action = "". $surname . ", " . $firstname . " " . $middlename . " added " . $adviser_name . " as a capstone adviser of Group " . $groupNum . " in " . $section;
                                     
                                     date_default_timezone_set('Asia/Manila');
                                     $date = date('Y-m-d H:i:s');
                            
                                     $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                                     $stmt = $conn->prepare($sql);
                                     $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                                    
                                    
                                    $conn->commit();
                                    
                                    unset($_POST["add-button"]);
                                    
                                    echo '<script>
                                            Swal.fire({
                                                title: "Success",
                                                text: "Capstone Adviser successfully added!",
                                                icon: "success",
                                                confirmButtonText: "OK"
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "/add_adviser";
                                                }
                                                
                                                else if (result.isDismissed) {
                                                    window.location.href = "/add_adviser";
                                                }
                                            });
                                          </script>';
                                }
                                
                                else{
                                    throw new Exception("Error inserting in notifications");
                                }
                            } 
                            
                            else {
                                throw new Exception("Error inserting new adviser");
                            }
                        }
                        
                        else{
                             throw new Exception("Failed to update adviser count of selected faculty");
                        }
                    }
                    
                    else if(($current_count + 1) > $current_limit){
                        throw new Exception("Adviser Limit of Faculty: " . ucwords($name) . " Has Been Reached");
                    }
                }
            } 
            
            else {
                throw new Exception("No Advisers Currently Available");
            }
        } 
        
        catch (Exception $e) {
            $conn->rollBack();
            error_log("Error inserting adviser: " . $e->getMessage());
            
            unset($_POST["add-button"]);
            
            echo '<script>
                    Swal.fire({
                        title: "Error Adding Adviser",
                        text: "Adviser could not be added: ' . addslashes($e->getMessage()) . '",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/add_adviser";
                        }
                        
                        else if (result.isDismissed) {
                            window.location.href = "/add_adviser";
                        }
                    });
                  </script>';
        }
    }

    function getAdviserValues() {
        global $conn;

        $advisers = [];

        $sql = "SELECT adviserID FROM advisers WHERE projectID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_SESSION["projectIDValue"]]);

        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $advisers[] = $result["adviserID"];
        }

        $panelists = [];
        
        $sql = "SELECT panelistID FROM panelists WHERE projectID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_SESSION["projectIDValue"]]);
        
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $panelists[] = $result["panelistID"];
        }
        
        $sql = "SELECT users.id, users.email, users.firstname, users.middlename, users.surname
                FROM users
                JOIN faculty ON users.id = faculty.id WHERE faculty.id > 0";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $resultCounter = 0;
        
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $adviserID = htmlspecialchars($result["id"]);

            if (in_array($adviserID, $advisers) || in_array($adviserID, $panelists)) {
                continue;
            }
       
            echo '
                <option value="' . $adviserID . '">' 
                . htmlspecialchars($result["email"]) . '( ' . htmlspecialchars($result["surname"]) . ', ' . htmlspecialchars($result["firstname"]) . ' ' . htmlspecialchars($result["middlename"]) . ')
                </option>
            ';
            
            $resultCounter++;
        }
        
        if ($resultCounter <= 0) {
            echo '
                <option value="None">
                    No advisers available to select
                </option>';
        }
    }
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
          <link rel="stylesheet" href="pages/dashboard_files/header.css">
    <title>Add Capstone Adviser</title>
    <script src="pages/session_tracker.js"></script>
    
    <style>
      
        .header-group {
            display: flex;
            gap: 20px; 
            justify-content: center; 
            align-items: center; 
        }
            
        .header-group h2 {
            font-size: 33px; 
            margin: 0; 
            margin-bottom: -10px;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
    
            width: 95%;
            height: auto;
            margin: 0 auto; 
        }

        h2 {
            text-align: center;
            color: #333;
            margin: 10px 0;
            font-size: 33px;
        }

        .divider {
            border-top: 5px solid #6BA3C1;
            margin: 20px 0;
        }

        .optionDiv {
            display: flex;
            flex-direction: column; /* Stack elements vertically */
            gap: 20px;
            margin: 30px 0;
        }

        .form-group {
            margin: 0;
        }

        label {
            font-size: 20px;
            color: #555;
        }

        select, input[type="text"] {
            width: 100%; /* Make full width */
            padding: 15px;
            font-size: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        select:focus, input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .button-group {
            text-align: center;
            margin-top: 20px; 
        }
        
        .form-button {
            background-color: #41A763;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 15px 30px; /* Adjusted padding for smaller button */
            font-size: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-button:hover {
            background-color: #277542;
        }

        /* Responsive design adjustments */
        @media (max-width: 600px) {
            .header-group {
                flex-direction: column; /* Stack header elements on small screens */
                align-items: flex-start;
            }
            .container {
                padding: 20px; /* Adjust padding on smaller screens */
            }
            h2 {
                font-size: 28px; /* Adjust font size for smaller screens */
            }
        }
    </style>
</head>

<body>
    <?php require 'header.php'; ?>
    <?php require 'menu.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

  
        <div class="header-group">
            <h2>Adding Adviser For:</h2>
            <h2><?php echo getSectionValue(); ?></h2>
            <h2><?php echo $_SESSION["titleValue"]; ?></h2>
        </div>
        
        <div class="divider"></div> 
        
        
        <form action="" method="POST">
            <div class="optionDiv">
                <div class="right-section">
                    <div class="form-group">
                        <label for="facultySearch">Search a Faculty using <span style="color: #41A763;"> Name </span> or <span style="color:  #41A763;"> Email </span></label>
                        
                        <input type="text" id="facultySearch" placeholder="Enter Faculty Name or Email" onkeyup="filterFaculty()">
                        <br><br>
                        <select name="facultyBox" id="facultyResults" size="10" required>
                            <?php getFaculties(); ?>
                        </select>
                    </div>
                    
                    <div class="button-group">
                        <button class="form-button" type="submit" name="add-button">Add Adviser</button>
                    </div>
                </div>
            </div> 
        </form>
        
        <script>
            function filterFaculty() {
                const input = document.getElementById('facultySearch');
                const filter = input.value.toLowerCase();
                const select = document.getElementById('facultyResults');
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
        
                
        <?php 
             if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["add-button"])) {
                    addAdviser();
                }
            }
        ?>

        <?php require 'footer.php'; ?>
</body>
</html>


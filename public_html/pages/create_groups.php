<?php
    require 'connection.php'; 
    session_start(); // Ensure session is started
    
    
    function generateTrackingNumber(){
        // Get today's date in YYYYMMDD format
        $date = date('Ymd');
    
        // Generate a 16-character random string
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $randomString = '';
        for ($i = 0; $i < 16; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
    
        // Combine date and random string to create tracking number
        $trackingNumber = $date . $randomString;
    
        return $trackingNumber;
    }
    
    // Function to check if the tracking number already exists
    function trackingNumberExists($conn, $trackingNumber) {
        global $conn;
        
        $sql = "SELECT COUNT(*) FROM tracking WHERE number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$trackingNumber]);
        return $stmt->fetchColumn() > 0; // Returns true if it exists
    }


?>


<!DOCTYPE html>

<html>
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <title>Create New Capstone Group</title>
        <script src="pages/session_tracker.js"></script>
        
        <style>
            /* Root variables for easy color and size management */
            :root {
                --primary-color: #333;
                --secondary-color: #f3f3f3;
                --accent-color: #4CAF50;
                --text-color: #444;
                --input-height: 60px;
                --button-height: 70px;
                --font-large: 2rem;
                --font-medium: 1.5rem;
                --font-small: 1.2rem;
            }
            
            /* Main Container */
            .container {
                max-width: 80%;
                margin: 0 auto;
                padding: 3em 2em;
               
                border-radius: 12px;
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
                text-align: center;
            }
            
            /* Header Styling */
            .container h2 {
                font-size: 33px;
                color: var(--primary-color);
                margin-bottom: -20px;
            }
            
            .container h3 {
              font-size: 33px;
                color: var(--text-color);
                   margin-bottom: -10px;
            }
            
            /* Divider */
            .divider {
                width: 100%;
                height: 4px;
                background-color: var(--accent-color);
                margin: 1em 0;
                border-radius: 2px;
            }
            
            /* Form Inputs */
            .textInput {
                width: 90%;
                height: var(--input-height);
                font-size: 20px;
                padding: 1em;
               
                border: 1px solid #ccc;
                border-radius: 8px;
                text-align: center;
            }
            
            /* Button Group */
            .button-group {
                display: flex;
                justify-content: center;
                margin-top: -5px;
            }
            
            .form-button {
               
                background-color: #41A763;
                color: #fff;
                border: none;
                padding: 10px 30px;
                font-size: 20px;
                cursor: pointer;
                border-radius: 5px;
                transition: background-color 0.3s;
            }
            
            .form-button:hover {
                background-color: #388E3C;
            }
            
            /* Responsive Styling */
            @media (max-width: 768px) {
                .container {
                    padding: 2em 1em;
                }
            
                .textInput, .form-button {
                    width: 100%;
                }
            }
            
            
                        
            
        </style>
       
    </head>

    <body>

        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
    

        <form action="" method="POST">
           <div class="container">
                <h2>Create New Capstone Group</h2>
                <h2>For</h2>
                
                <?php
                
                    $sql = "SELECT *, a.start_year, a.end_year FROM sections s JOIN academic_year a ON s.academicYearID = a.id WHERE s.sectionID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["sectionID"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                    $academicYear = "(" . $result["start_year"] . "-" . $result["end_year"] . ")";
                    
                    echo'
                        <h3 style="text-align: center; font-size: 25px;">'.addslashes($result["specialization"]) .'</h3>
                    ';
                    
                    echo'
                        <h3 style="text-align: center; font-size: 25px;">'.addslashes($section)." ". addslashes($academicYear) .'</h3>
                    ';
                ?>

                <div class="divider"></div> <!-- Divider styled above -->
            
                <br>
            
                <div class="form-group">
                    <input type="text" name="titleBox" id="titleBoxID" class="textInput"
                           placeholder="Capstone Title To Be Determined Through Title Evaluation" disabled>
                </div>
            
                <br>
            
                <div class="button-group">
                    <button class="form-button" type="submit" name="create-button">Create</button>
                </div>
            </div>
          
        </form>

        <?php 
            if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create-button"])){
                
                // Define the function outside for better practice, but keeping it here for simplicity
                function createGroup(){
                    try{
                        global $conn;

                        $conn->beginTransaction();
                        
                        $sql = "SELECT COUNT(*) FROM capstone_projects WHERE sectionID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_SESSION["sectionID"]]);
                        $count = $stmt->fetchColumn();
                        
                        $groupNum = $count + 1;
                        
                        // Use lastInsertId() instead of manual increment
                        $sql = "INSERT INTO capstone_projects (sectionID, groupNum, academicYearID, coordinatorID, title, title_description, status, defense) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$_SESSION["sectionID"], $groupNum, $_SESSION["acadYearID"], $_SESSION["coordinator"], "TBD", "TBD", "active", "pending"]); //TBD is the default placeholder title of newly created capstone groups, until the title evaluation where they will have their own title
                        
                        if($result){
                            // Get the last inserted projectID
                            $projectID = $conn->lastInsertId();
                            
                            $counter = 0;
                            
                            for($count = 1; $count < 4; $count++){
                            
                                $sql = "INSERT INTO title_proposal (projectID, titleNum, title, title_description, introduction, background, importance, scope, result) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$projectID, $count, "", "", "", "", "", "", "pending"]); //Default value of result is pending all lower case (do not change it)
                                
                                $counter++;
                            }
                            
                            if($counter == 3){
                                
                                $sql = "SELECT * FROM tasks";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if($result)
                                    
                                    foreach($result as $task){
                                         // Prepare your SQL statement for insertion
                                        $sql = "INSERT INTO tracking (projectID, taskID, academicYearID, status, number) VALUES (?, ?, ?, ?, ?)";
                                        $stmt = $conn->prepare($sql);
                                        
                                        // Generate a unique tracking number
                                        do {
                                            $trackingNumber = generateTrackingNumber();
                                        } while (trackingNumberExists($conn, $trackingNumber));
                                        
                                        // Execute the insert
                                        $result = $stmt->execute([$projectID, $task["id"], $_SESSION["acadYearID"], "started", $trackingNumber]);
                                         
                                         if($result){
                                             //continue as the foreach loop not finished yet
                                         }
                                         
                                         else{
                                             throw new Exception("Error inserting values in tracking number");
                                             break;
                                         }
                                    }
                                    
                                     $sql = "SELECT * FROM sections WHERE sectionID = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["sectionID"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                     $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                                    
                                    
                                     $sql = "SELECT * FROM users WHERE id = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["userID"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                      
                                     $firstname = $result["firstname"];
                                     $surname = $result["surname"];
                                     $middlename = $result["middlename"];
                                            
                                     $action = "". $surname . ", " . $firstname . " " . $middlename . " created a new capstone group in " . $section;
                                     
                                     date_default_timezone_set('Asia/Manila');
                                     $date = date('Y-m-d H:i:s');
                    
                                     $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                                     $stmt = $conn->prepare($sql);
                                     $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                                    
                                     if(!$result){
                                         throw new Exception("Failed to insert action logs");  
                                     }
                                    
                                    
                                    $conn->commit();
                                                
                                    unset($_POST["create-button"]);
                                    
                                    echo '<script>
                                        Swal.fire({
                                             title: "Success",
                                            text: "New Capstone Group successfully created!",
                                            icon: "success",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "/create_groups";
                                            }
                                            
                                            else if (result.isDismissed) {
                                                 window.location.href = "/create_groups";
                                            }
                                        });
                                    </script>';
                            }
                            
                            else{
                                throw new Exception("Error in the title proposal counter");
                            }
                        }
                        
                        else{
                            throw new Exception("Error in creating new capstone group");
                        }
                    }
                        
                    
                    catch(Exception $e){
                        $conn->rollBack();
                        error_log("Error creating capstone group: " . $e->getMessage());
                        
                        unset($_POST["create-button"]);

                        echo '<script>
                            Swal.fire({
                                 title: "Error Creating Capstone Group",
                                text: "The capstone group could not be created: '.addslashes($e->getMessage()).'",
                                icon: "error",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/create_groups";
                                }
                                
                                else if (result.isDismissed) {
                                     window.location.href = "/create_groups";
                                }
                            });
                        </script>';
                    }
                    
                }

                // Call the function to create the group
                createGroup();
            }
        ?>
        </div>
   
      <?php require 'footer.php'; ?>
    </body>
       
</html>
    
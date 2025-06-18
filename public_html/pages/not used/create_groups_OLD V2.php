<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="pages/creation_form.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <title>Create New Capstone Group</title>
        <script src="pages/session_tracker.js"></script>
        
        <style>
            input::placeholder { /* For modern browsers */
                color: black; /* Light gray color */
                font-size: 14px;
                font-weight: bold;
            }
            
            .textInput{
                background-color: #fd5c63; 
                color: black; 
                width: 100%;
                font-size: 1.5em; 
                display: flex; 
                justify-content: center; 
                align-items: center;
            }
        </style>
    </head>

    <body>
        <?php 
            require_once 'connection.php'; 
            session_start(); // Ensure session is started
        ?>
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>

        <?php
            // Fetch form data for pre-filling (if needed)
            $capstoneTitle = isset($_POST['titleBox']) ? htmlspecialchars($_POST['titleBox']) : '';
        ?>

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
                        <h3 style="text-align: center; font-size: 25px;">'.addslashes($section)." ". addslashes($academicYear) .'</h3>
                    ';
                ?>
                
                
                <div class="divider"></div> <!-- Custom styled divider -->
                
                <br>
                
                <div class="form-group">
                    <label for="titleOption" style="text-align: center">Title Mode Selection</label>
                    
                    <select id="titleOption" name="titleOption" onchange="changeSelectBackground(this); toggleTitleInput();" style="font-weight: bold;">
                              <option value="without" selected style="color: #fd5c63; background-color: white; font-weight: bold;">Without Assigned Title</option>
                              <option value="with" style="color: green; background-color: white; font-weight: bold;">With Assigned Title</option>
                    </select>
                </div>
                
                <br><br>
                
     
                    
                    <div class="form-group">
                        <label for="titleBox" style="text-align: center">Capstone Title</label>
                        <input type="text" name="titleBox" id="titleBoxID" class="textInput" placeholder="Capstone Title To Be Determined Through Title Evaluation" value="<?php echo $capstoneTitle; ?>" disabled> 
                    </div>
                      
                    <br>
                        
                    <div class="button-group">
                        <button class="form-button" type="submit" name="create-button">Create</button>
                                <!--edit functionality, to return previous page --><button class="form-button" type="submit" name="create-button">Cancel</button>
                    </div>
          
        </form>

        <?php 
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                
                $title = isset($_POST["titleBox"]) ? htmlspecialchars($_POST["titleBox"]) : 'TBD';
                
                // Define the function outside for better practice, but keeping it here for simplicity
                function createGroup(){
                    try{
                        global $conn, $title;

                        $conn->beginTransaction();
                        
                        //Final Step 1
                        if($title != "TBD"){
                            
                            //Checks to see if the capstone group being created with a TITLE is a duplicate 
                            
                            $sql = "SELECT * FROM capstone_projects WHERE sectionID = ? AND academicYearID = ? AND coordinatorID = ? AND title = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["sectionID"], $_SESSION["acadYearID"], $_SESSION["coordinator"], $title]);
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        }
                        
                        else{
                            //Not duplicate
                            
                            $result = false;
                        }
                        
                        
                        //Final Step 2    
                        if(!$result){
                            
                            $sql = "SELECT COUNT(*) FROM capstone_projects WHERE sectionID = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["sectionID"]]);
                            $count = $stmt->fetchColumn();
                            
                            $groupNum = $count + 1;
                            
                            // Use lastInsertId() instead of manual increment
                            $sql = "INSERT INTO capstone_projects (sectionID, groupNum, academicYearID, coordinatorID, title, status) VALUES(?, ?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$_SESSION["sectionID"], $groupNum, $_SESSION["acadYearID"], $_SESSION["coordinator"], $title, "active"]);
                            
                            if($result){
                                // Get the last inserted projectID
                                $projectID = $conn->lastInsertId();
                                
                                $counter = 0;
                                
                                for($count = 1; $count < 4; $count++){
                                
                                    $sql = "INSERT INTO title_proposal (projectID, titleNum, title, introduction, background, importance, scope, result) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
                                    $stmt = $conn->prepare($sql);
                                    $result = $stmt->execute([$projectID, $count, $title, "", "", "", "", "pending"]); //Default value of result is pending all lower case (do not change it)
                                    
                                    $counter++;
                                }
                                
                                if($counter == 3){
                                
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
                        
                        else if($result){ //if a capstone project is duplicated
                            throw new Exception("Duplication of capstone project");
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
                            });
                        </script>';
                    }
                    
                }

                // Call the function to create the group
                createGroup();
            }
        ?>
        
    <?php include 'footer.php'; ?>
    
    <script>
        // JavaScript to toggle the textbox enabled/disabled based on select option
        function toggleTitleInput() {
            const titleInput = document.getElementById('titleBoxID');
            const titleOption = document.getElementById('titleOption');
            
            if (titleOption.value === "with") {
                titleInput.disabled = false;
                titleInput.style.backgroundColor = "#98FB98";
                titleInput.placeholder = "Provide Capstone Title – For Groups with an Assigned Title From Faculty";
            } 
            
            else {
                titleInput.disabled = true;
                titleInput.value = ""; // Clear the value when disabled
                titleInput.placeholder = "Capstone Title To Be Determined Through Title Evaluation";
                titleInput.style.backgroundColor = "#fd5c63";
            }
        }
    
    
        // Function to change the select background color based on selected option value
        function changeSelectBackground(select) {
            const selectedValue = select.value; // Get the value of the selected option
            
            // Apply background color based on option value using if-else
            if (selectedValue === "without") {
                select.style.backgroundColor = "#fd5c63"; // Change to red for "without" value
            } else if (selectedValue === "with") {
                select.style.backgroundColor = "#98FB98"; // Change to green for "with" value
            } else {
                select.style.backgroundColor = "gray"; // Default background color
            }
        }
    
        // Set the initial background color to match the selected option on page load
        window.onload = function() {
            const selectElement = document.getElementById('titleOption');
            changeSelectBackground(selectElement); // Call the function on page load
        };
    </script>
        
    </body>
</html>
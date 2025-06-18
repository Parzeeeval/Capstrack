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
            $selectedadviser = isset($_POST['adviserBox']) ? htmlspecialchars($_POST['adviserBox']) : '';
            $selectedchair = isset($_POST['panelchairBox']) ? htmlspecialchars($_POST['panelchairBox']) : '';
            $selectedpanel1= isset($_POST['panelBox1']) ? htmlspecialchars($_POST['panelBox1']) : '';
            $selectedpanel2= isset($_POST['panelBox2']) ? htmlspecialchars($_POST['panelBox2']) : '';
        ?>

        <form action="" method="POST">
            <div class="container">
                <h2>Create New Capstone Group</h2>
        
                <div class="optionDiv">
                    
                    <div class="form-group">
                        <label for="titleBox">Capstone Title</label>
                        <input type="text" name="titleBox" id="titleBoxID" placeholder="Capstone Title" required style="width: 600px; font-size: 1.5em;" value="<?php echo $capstoneTitle; ?>"> 
                    </div>
                        <!--regards sa break, di ko sure if makaka apekto siya sa grid-column,
                        ginagawa kasi ng <br> nilalagay niya sa baba yung element; referencing
                        sa create_courses/create_sections, ala silang <br> tag in-between "form-groups"
                        -->
                    <br>
                        
                    <div class="form-group">
                        <label for="adviserBox">Adviser</label>
                        <select name="adviserBox" id="" style="font-size: 1.5em;">
                            <?php 
                                $sql = "SELECT users.id, users.email, users.firstname, users.middlename, users.surname, faculty.id
                                        FROM users
                                        JOIN faculty ON users.id = faculty.id WHERE faculty.id > 0";

                                $stmt = $conn->prepare($sql);
                                $stmt->execute();

                                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $adviserID = htmlspecialchars($result['id']);

                                    if($adviserID == $_SESSION["coordinator"]){
                                        continue; 
                                    }

                                    $selected = ($adviserID == $selectedadviser) ? 'selected' : '';
                                    echo '<option value="' . $adviserID . '" ' . $selected . '>' . 
                                        htmlspecialchars($result['email']) . '   (' . 
                                        htmlspecialchars($result['surname']) . ', ' . 
                                        htmlspecialchars($result['firstname']) . ' ' . 
                                        htmlspecialchars($result['middlename']) . ') </option>'; 
                                }
                            ?>
                        </select>
                    </div>
                    
                    <br>
                    
                    <div class="form-group">
                        <label for="panelchairBox">Panelist Chairman</label>
                        <select name="panelchairBox" id="" style="font-size: 1.5em;">
                            <?php 
                                $sql = "SELECT users.id, users.email, users.firstname, users.middlename, users.surname, faculty.id
                                        FROM users
                                        JOIN faculty ON users.id = faculty.id WHERE faculty.id > 0";

                                $stmt = $conn->prepare($sql);
                                $stmt->execute();

                                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $chairID = htmlspecialchars($result['id']);

                                    if($chairID == $_SESSION["coordinator"]){
                                        continue; 
                                    }

                                    $selected = ($chairID == $selectedchair) ? 'selected' : '';
                                    echo '<option value="' . $chairID . '" ' . $selected . '>' . 
                                        htmlspecialchars($result['email']) . '   (' . 
                                        htmlspecialchars($result['surname']) . ', ' . 
                                        htmlspecialchars($result['firstname']) . ' ' . 
                                        htmlspecialchars($result['middlename']) . ') </option>'; 
                                }
                            ?>
                        </select>
                    </div>
                    
                    <br>
                    
                    <div class="form-group">
                        <label for="panelBox1">Panelist #2</label>
                        <select name="panelBox1" id="" style="font-size: 1.5em;">
                            <?php 
                                $sql = "SELECT users.id, users.email, users.firstname, users.middlename, users.surname, faculty.id
                                        FROM users
                                        JOIN faculty ON users.id = faculty.id WHERE faculty.id > 0";

                                $stmt = $conn->prepare($sql);
                                $stmt->execute();

                                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $panel1 = htmlspecialchars($result['id']);

                                    if($panel1 == $_SESSION["coordinator"]){
                                        continue; 
                                    }

                                    $selected = ($panel1 == $selectedpanel1) ? 'selected' : '';
                                    echo '<option value="' . $panel1 . '" ' . $selected . '>' . 
                                        htmlspecialchars($result['email']) . '   (' . 
                                        htmlspecialchars($result['surname']) . ', ' . 
                                        htmlspecialchars($result['firstname']) . ' ' . 
                                        htmlspecialchars($result['middlename']) . ') </option>'; 
                                }
                            ?>
                        </select>
                    </div>
                    
                    <br>
                    
                    <div class="form-group">
                        <label for="panelBox2">Panelist #3</label>
                        <select name="panelBox2" id="" style="font-size: 1.5em;">
                            <?php 
                                $sql = "SELECT users.id, users.email, users.firstname, users.middlename, users.surname, faculty.id
                                        FROM users
                                        JOIN faculty ON users.id = faculty.id WHERE faculty.id > 0";

                                $stmt = $conn->prepare($sql);
                                $stmt->execute();

                                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $panel2 = htmlspecialchars($result['id']);

                                    if($panel2 == $_SESSION["coordinator"]){
                                        continue; 
                                    }

                                    $selected = ($panel2 == $selectedpanel2) ? 'selected' : '';
                                    echo '<option value="' . $panel2 . '" ' . $selected . '>' . 
                                        htmlspecialchars($result['email']) . '   (' . 
                                        htmlspecialchars($result['surname']) . ', ' . 
                                        htmlspecialchars($result['firstname']) . ' ' . 
                                        htmlspecialchars($result['middlename']) . ') </option>'; 
                                }
                            ?>
                        </select>
                    </div>
                    
                </div>
        
                <div class="button-group">
                    <button class="form-button" type="submit" name="create-button">Create</button>
                            <!--edit functionality, to return previous page --><button class="form-button" type="submit" name="create-button">Cancel</button>
                </div>
            </div>
        </form>

        <?php 
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                $title = isset($_POST["titleBox"]) ? htmlspecialchars($_POST["titleBox"]) : '';
                $adviser = isset($_POST["adviserBox"]) ? htmlspecialchars($_POST["adviserBox"]) : '';
                $chairman = isset($_POST["panelchairBox"]) ? htmlspecialchars($_POST["panelchairBox"]) : '';
                $panel2 = isset($_POST["panelBox1"]) ? htmlspecialchars($_POST["panelBox1"]) : '';
                $panel3 = isset($_POST["panelBox2"]) ? htmlspecialchars($_POST["panelBox2"]) : '';
                
                $panels = [$chairman, $panel2, $panel3];

                // Optional: Add server-side validation here
                // e.g., ensure no duplicate panelists
                if(count(array_unique($panels)) !== count($panels)){
                    echo '<script>
                        Swal.fire({
                             title: "Error",
                            text: "Duplicate panelist selections detected. Please select unique panelists.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    </script>';
                }
                else{
                    // Define the function outside for better practice, but keeping it here for simplicity
                    function createGroup(){
                        try{
                            global $conn, $title, $adviser, $panels;

                            $conn->beginTransaction();
                            
                            $sql = "SELECT * FROM capstone_projects WHERE sectionID = ? AND academicYearID = ? AND coordinatorID = ? AND title = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["sectionID"], $_SESSION["acadYearID"], $_SESSION["coordinator"], $title]);
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                            if(!$result){
                                // Use lastInsertId() instead of manual increment
                                $sql = "INSERT INTO capstone_projects (sectionID, academicYearID, coordinatorID, title, status) VALUES(?, ?, ?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$_SESSION["sectionID"], $_SESSION["acadYearID"], $_SESSION["coordinator"], $title, "active"]);
                                
                                if($result){
                                    // Get the last inserted projectID
                                    $projectID = $conn->lastInsertId();
    
                                    $resultCounter = 0;
                                    
                                    foreach($panels as $index => $panelistID){
                                        $level = ($index == 0) ? 2 : 1;
                                        
                                        $sql = "INSERT INTO panelists (panelistID, projectID, level) VALUES (?, ?, ?)";
                                        $stmt = $conn->prepare($sql);
                                        $panelResult = $stmt->execute([$panelistID, $projectID, $level]);
                                        
                                        if($panelResult){
                                            $resultCounter++;
                                        }
                                        
                                        else{
                                            throw new Exception("Failed to insert panelist ID: " . $panelistID);
                                        }
                                    }
                                    
                                    if($resultCounter === 3){
                                        
                                        $sql = "INSERT INTO advisers (adviserID, projectID) VALUES(?, ?)";
                                        $stmt = $conn->prepare($sql);
                                        $result = $stmt->execute([$adviser, $projectID]);
                                        
                                        if($result){
                                            $conn->commit();
                                            
                                            unset($_POST["create-button"]);
                                            
                                            echo '<script>
                                                Swal.fire({
                                                     title: "Success",
                                                    text: "New Capstone Group for section: '. addslashes($_SESSION["section"]) . ' successfully created!",
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
                                            throw new Exception("Error in inserting adviser");
                                        }
                                    }
                                    
                                    else{
                                        throw new Exception("Not all panelists were inserted successfully.");
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
            }
        ?>
        
    <?php include 'footer.php'; ?>
    
    </body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
     <link rel="stylesheet" href="pages/create_sections.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <title>Create New Section</title>
    <script src="pages/session_tracker.js"></script>

    
  
<body>
    <?php 
        require 'connection.php'; 
        session_start();
    ?>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <?php require 'header.php'; ?>
    <?php require 'menu.php'; ?>

    <?php
        $selectedYear = isset($_POST['yearLevelBox']) ? htmlspecialchars($_POST['yearLevelBox']) : '';
        $selectedSection = isset($_POST['sectionBox']) ? htmlspecialchars($_POST['sectionBox']) : '';
        $selectedGroupNum = isset($_POST['groupNumBox']) ? htmlspecialchars($_POST['groupNumBox']) : '';
        $selectedCoordinator = isset($_POST['coordinatorBox']) ? htmlspecialchars($_POST['coordinatorBox']) : '';
    ?>

    <form action="" method="POST">
       
            <div class="header-group">
                <h2>Create New Section</h2>
                <h2>For</h2>
                
                <?php
                    echo '
                        <h2>' . addslashes($_SESSION["courseID"]) . '</h2>
                        <h2>' . addslashes($_SESSION["specialization"]) . '</h2>
                    ';
                ?>
            </div>
            
            <div class="divider"></div>

            <div class="optionDiv">
                <div class="left-section">
                    <div class="form-group">
                        <label for="yearLevelBox">Year Level</label>
                        <select name="yearLevelBox" id="yearLevelBox">
                            
                            <?php
                                $sql = "SELECT mode FROM academic_year ORDER BY id DESC LIMIT 1";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                $mode = 0;
                                
                                if($result){
                                    $mode = $result["mode"];
                                }
                                
                                if($mode == 1 || $mode == 2){
                                    echo '
                                         <option value="3" <?php echo ($selectedYear == "3") ? "selected" : ""; ?>3rd</option>
                                    ';
                                }
                                
                                else if($mode == 3){
                                    echo '
                                         <option value="4" <?php echo ($selectedYear == "4") ? "selected" : ""; ?>4th</option>
                                    ';
                                }
                            ?>
                           
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sectionBox">Section</label>
                        <select name="sectionBox" id="sectionBox">
                            <option value="A" <?php echo ($selectedSection == 'A') ? 'selected' : ''; ?>>A</option>
                            <option value="B" <?php echo ($selectedSection == 'B') ? 'selected' : ''; ?>>B</option>
                            <option value="C" <?php echo ($selectedSection == 'C') ? 'selected' : ''; ?>>C</option>
                            <option value="D" <?php echo ($selectedSection == 'D') ? 'selected' : ''; ?>>D</option>
                            <option value="E" <?php echo ($selectedSection == 'E') ? 'selected' : ''; ?>>E</option>
                            <option value="F" <?php echo ($selectedSection == 'F') ? 'selected' : ''; ?>>F</option>
                            <option value="G" <?php echo ($selectedSection == 'G') ? 'selected' : ''; ?>>G</option>
                            <option value="H" <?php echo ($selectedSection == 'H') ? 'selected' : ''; ?>>H</option>
                            <option value="I" <?php echo ($selectedSection == 'I') ? 'selected' : ''; ?>>I</option>
                            <option value="J" <?php echo ($selectedSection == 'J') ? 'selected' : ''; ?>>J</option>
                            <option value="K" <?php echo ($selectedSection == 'K') ? 'selected' : ''; ?>>K</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="groupNumBox">Group Number</label>
                        <select name="groupNumBox" id="groupNumBox">
                            <option value="" <?php echo ($selectedGroupNum == '') ? 'selected' : ''; ?>>None</option>
                            <option value="G1" <?php echo ($selectedGroupNum == 'G1') ? 'selected' : ''; ?>>G1</option>
                            <option value="G2" <?php echo ($selectedGroupNum == 'G2') ? 'selected' : ''; ?>>G2</option>
                            <option value="G3" <?php echo ($selectedGroupNum == 'G3') ? 'selected' : ''; ?>>G3</option>
                        </select>
                    </div>
                    
                    <div class="button-group">
                        <button class="form-button" type="submit" name="create-button">Create</button>
                    </div>
                </div>

                <div class="right-section">
                    <div class="form-group">
                        
                        <label for="facultySearch">Search a Faculty using <span style="color: #41A763;"> Name </span> or <span style="color: #41A763;"> Email </span></label>
                        
                        <input type="text" id="facultySearch" placeholder="Enter Faculty Name or Email" onkeyup="filterFaculty()" style="font-size: 22px;">
                        
                        <select name="facultyBox" id="facultyResults" size="10" style="font-size: 22px; margin-top: 10px;" required>
                           <?php getFaculties(); ?>
                        </select>
                    </div>
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
    
        function getFaculties(){
            global $conn;
            
            try{
                $sql = "SELECT * FROM users WHERE id <> ? AND type = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([0, "faculty"]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach($results as $result){
                     // Get faculty information
                    $facultyID = htmlspecialchars($result["id"]);
                    $facultyName = htmlspecialchars($result["surname"]) . ', ' . htmlspecialchars($result["firstname"]) . ' ' . htmlspecialchars($result["middlename"]);
                    $facultyEmail = htmlspecialchars($result["email"]);
                    
                    // Echo an option element with faculty details
                    echo "<option value=\"$facultyID\">$facultyName - ($facultyEmail)</option>";
                }
            }
            
            catch(Exception $e){
                
            }
        }
        
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            
            if(isset($_POST["create-button"])){
                
                $specialization = $_SESSION["specialization"];
                $course = $_SESSION["courseID"];
                $year = htmlspecialchars($_POST["yearLevelBox"]);
                $section = htmlspecialchars($_POST["sectionBox"]);
                $groupNum = htmlspecialchars($_POST["groupNumBox"]);
                $semester = "";
                $coordinator = htmlspecialchars($_POST["facultyBox"]);
                

                function createSection() {
                   
                    global $conn;
                    global $course, $specialization, $year, $section, $groupNum, $semester, $coordinator;
            
                    // Fetch academic year
                    $sql = "SELECT id, mode FROM academic_year ORDER BY id DESC LIMIT 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $acadYear = $result["id"];
                    $mode = $result["mode"];
                    $semester = 0;
                    
                     // Define the semester based on year level
                    if($mode == 1){
                        $semester = ($year == "3") ? "2" : "1";
                    }
                    
                    else if($mode == 2){
                        $semester = "1";
                    }
                    
                    else if($mode == 3){
                        $semester = "1";
                    }
                    
                    $sql = "SELECT * FROM faculty_count WHERE facultyID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$coordinator]);
                    $cordcount = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($cordcount){
                           
                        $curr_count = $cordcount["coordinator_count"];
                        $curr_limit = $cordcount["coordinator_limit"];
                        
                        
                        if(($curr_count + 1) <= $curr_limit){
                            // Insert into the `coordinators` table first
                            $sql = "INSERT INTO coordinators (facultyID, sectionID, academicYearID, semester) VALUES (?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$coordinator, 0, $acadYear, $semester]);
                        
                            
                            if ($result) {
                                // Now insert into the `sections` table after coordinator insert succeeds
                                $sql = "INSERT INTO sections (coordinatorID, courseID, yearLevel, section_letter, section_group, specialization, academicYearID, semester) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$coordinator, $course, $year, $section, $groupNum, $specialization, $acadYear, $semester]);
                                
                                if($result){
                                    $sectionID = $conn->lastInsertId();
                                    
                                    //The need for an update query for the coordinator table is because that the foreign key for Sections and Coordinators table are circular and not one way key
                                    
                                    $sql = "UPDATE coordinators SET sectionID = ? WHERE facultyID = ? AND sectionID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $result = $stmt->execute([$sectionID, $coordinator, 0]); 
        
                                    if ($result) {
                                        
                                         $sql = "UPDATE faculty_count SET coordinator_count = coordinator_count + 1 WHERE facultyID = ?";
                                         $stmt = $conn->prepare($sql);
                                         $result = $stmt->execute([$coordinator]); 
                                         
                                         if($result){
                                        
                                                echo '<script>
                                                    Swal.fire({
                                                        title: "Success",
                                                        text: "'.addslashes($course).' '. addslashes($year) . addslashes($section). addslashes($groupNum) . ' successfully created!",
                                                        icon: "success",
                                                        confirmButtonText: "OK"
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            window.location.href = "/create_sections";
                                                        }
                                                        
                                                        else if (result.isDismissed) {
                                                             window.location.href = "/create_sections";
                                                        }
                                                    });
                                                </script>';
                                         }
                                    
                                    
                                        else{
                                            echo '<script>
                                                Swal.fire({
                                                     title: "Error In Creating Section",
                                                    text: "The section: '.addslashes($course).' '. addslashes($year) . addslashes($section). addslashes($groupNum) . ' may be a duplicate of an existing one, please check details and try again",
                                                    icon: "error",
                                                    confirmButtonText: "OK"
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location.href = "/create_sections";
                                                    }
                                                    
                                                    else if (result.isDismissed) {
                                                         window.location.href = "/create_sections";
                                                    }
                                                });
                                             </script>';
                                        
                                            throw new Exception("Error creating section, possibility of duplication or error in details");
                                        }
                                    }
                                }
                            } 
                            
                            else {
                                throw new Exception("Error inserting into coordinators table");
                            }
                        }
                        
                        else if(($curr_count + 1) > $curr_limit){
                            $conn->rollBack();
                            
                            unset($_POST["create-button"]);
                            
                            echo '<script>
                                Swal.fire({
                                     title: "Error In Creating Section",
                                      text: "Coordinator Count limit for this faculty member has been reached",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/create_sections";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/create_sections";
                                    }
                                });
                            </script>';
                        }
                    }
                    
                    else{
                        throw new Exception("Failed to get faculty count");
                    }
                }
                
                
                //try block now uses the function createSection()
                
                try{
                    $conn->beginTransaction();
                    
                    if($specialization != "N/A" || $specialization != ""){
                        $sql = "SELECT * FROM specializations WHERE courseID = ? AND name = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$course, $specialization]);
    
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                        if($result){
                            createSection();
                            
                            
                             $sql = "SELECT courseName FROM courses WHERE courseID = ?";
                             $stmt = $conn->prepare($sql);
                             $stmt->execute([$course]);
                             $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                             $courseName = $result["courseName"];
                            
                             $sql = "SELECT * FROM users WHERE id = ?";
                             $stmt = $conn->prepare($sql);
                             $stmt->execute([$_SESSION["userID"]]);
                             $result = $stmt->fetch(PDO::FETCH_ASSOC);
                              
                             $firstname = $result["firstname"];
                             $surname = $result["surname"];
                             $middlename = $result["middlename"];
    
    
                             $action = "". $surname . ", " . $firstname . " " . $middlename . " created a new section in  " . $specialization . " belonging to " . $courseName;
                             
                             date_default_timezone_set('Asia/Manila');
                             $date = date('Y-m-d H:i:s');
                    
                             $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                             $stmt = $conn->prepare($sql);
                             $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                             
                            $conn->commit();
                            
                            unset($_POST["create-button"]);
                            
                            header("Location: /create_sections");
                            exit;
                        }
    
                        else{
                            echo '<script>
                                Swal.fire({
                                     title: "Wrong Specialization",
                                    text: "'. addslashes($specialization) . ' does not belong to '. addslashes($course) .' Please select another specialization or choose N/A",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "/create_sections";
                                        }
                                        
                                        else if (result.isDismissed) {
                                             window.location.href = "/create_sections";
                                        }
                                    });
                            </script>';
                            
                            throw new Exception("Selected specialization does not belong to selected course");
                        }
                     }
                     
                     else if($specialization == "N/A"){
                        //Selected specialization none
    
                        $sql = "SELECT courseID FROM specializations WHERE courseID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$course]);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                        if($result){
                             throw new Exception("Selected course needs a specialization");
                        }
    
                        else{
                            createSection();
                            
                             $sql = "SELECT courseName FROM courses WHERE courseID = ?";
                             $stmt = $conn->prepare($sql);
                             $stmt->execute([$course]);
                             $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                             $courseName = $result["courseName"];
                            
                             $sql = "SELECT * FROM users WHERE id = ?";
                             $stmt = $conn->prepare($sql);
                             $stmt->execute([$_SESSION["userID"]]);
                             $result = $stmt->fetch(PDO::FETCH_ASSOC);
                              
                             $firstname = $result["firstname"];
                             $surname = $result["surname"];
                             $middlename = $result["middlename"];
    
    
                             $action = "". $surname . ", " . $firstname . " " . $middlename . " created a new section with no specialization, belonging to " . $courseName;
                             
                             date_default_timezone_set('Asia/Manila');
                             $date = date('Y-m-d H:i:s');
                    
                             $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                             $stmt = $conn->prepare($sql);
                             $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                            $conn->commit();
                            
                            unset($_POST["create-button"]);
                            
                            header("Location: /create_sections");
                            exit;
                        }
                        
                     }
                
                }

                catch(Exception $e){
                    $conn->rollBack();
                    
                    echo '<script>';
                    echo 'console.log(' . json_encode("Error: " . $e->getMessage()) . ');';
                    echo '</script>';
                    
                    unset($_POST["create-button"]);
                    
                    
                    echo '<script>
                            Swal.fire({
                                 title: "Error In Creating Section",
                                  text: "The section: '.addslashes($course).' '. addslashes($year) . addslashes($section). addslashes($groupNum) . ' may be a duplicate of an existing one, please check details and try again",
                                icon: "error",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/create_sections";
                                }
                                
                                else if (result.isDismissed) {
                                     window.location.href = "/create_sections";
                                }
                            });
                        </script>';
                }
                
                
            }
        }
                
    ?>

    <?php require 'footer.php'; ?>
</body>
</html>


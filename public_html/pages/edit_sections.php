<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
         <link rel="stylesheet" href="pages/create_sections.css">
        <link rel="stylesheet" href="pages/accounts.css">
        <link rel="stylesheet" href="pages/accounts_student.css">
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
            $selectedSem= isset($_POST['semesterBox']) ? htmlspecialchars($_POST['semesterBox']) : '';
            $selectedGroupNum = isset($_POST['groupNumBox']) ? htmlspecialchars($_POST['groupNumBox']) : '';
            $selectedCoordinator = isset($_POST['coordinatorBox']) ? htmlspecialchars($_POST['coordinatorBox']) : '';
        ?>
    
    
    
        <div id="content2">
            <div class="profile-tabs">
                <div class="tabs">
                    <button onclick="window.location.href='/edit_courses';">Edit Courses</button>
                    <button onclick="window.location.href='/edit_specializations';">Edit Specializations</button>
                    <button class="active" onclick="window.location.href='/edit_sections';">Edit Sections</button>
                    <button onclick="window.location.href='/edit_tags';">Edit Title Tags</button>
                </div>
            </div>
                
        <form id="mainForm" action="" method="POST">
           
                <div class="optionDiv">
                    <div class="left-section">
                        
                        <div class="form-group">
                            <label for="sectionBox">Section</label>
                                <select name="sectionBox" id="sectionBox" style="width: 820px; background-color: #fff9ee;" required onchange="this.form.submit()">
                                    <?php getSections(); ?>
                                </select>
                        </div>
                        
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
                            <label for="semesterBox">Year Level</label>
                            <select name="semesterBox" id="semesterBox">
                                <option value="1" <?php echo ($selectedSem == '1') ? 'selected' : ''; ?>>1st Semester</option>
                                <option value="2" <?php echo ($selectedSem == '2') ? 'selected' : ''; ?>>2nd Semester</option>
                            </select>
                        </div>
    
                        <div class="form-group">
                            <label for="letterBox">Section</label>
                            <select name="letterBox" id="letterBox">
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
                        
                        <div class="form-group">
                            <label for="specializationBox">Specialization</label>
                            <select name="specializationBox" id="specializationBox">

                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="courseBox">Course</label>
                            <select name="courseBox" id="courseBox">
                               <?php getCourses(); ?>
                            </select>
                        </div>
                        
                        <div class="button-group" style="justify-content: left; gap: 20px;">
                            <button class="form-button" type="submit" name="update-button">Update</button>
                            <button class="form-button" type="button" name="cancel-button" onclick="window.location.href = '/edit_sections'" style="background-color: gray;">Cancel</button>
                        </div>
                    </div>
    
                    <div class="right-section">
                        <div class="form-group">
                            
                            <h2>Select New Capstone Coordinator</h2>
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
            
            document.addEventListener('DOMContentLoaded', function () {
                const sectionBox = document.getElementById('sectionBox');
                const courseBox = document.getElementById('courseBox');
                const yearLevelBox = document.getElementById('yearLevelBox');
                const letterBox = document.getElementById('letterBox');
                const groupNumBox = document.getElementById('groupNumBox');
                const specializationBox = document.getElementById('specializationBox');
            
                // Function to extract section details
                function extractSectionDetails(optionText) {
                    const parts = optionText.split('-'); // Split by the hyphen
                    if (parts.length < 2) {
                        return {}; // Return an empty object if the format is invalid
                    }
            
                    const [courseAndYear, specialization] = parts.map(part => part.trim());
                    const match = courseAndYear.match(/^([A-Z]+)\s(\d)([A-Z])([A-Z0-9]+)$/);
            
                    if (!match) {
                        return {}; // Return an empty object if the format doesn't match
                    }
            
                    const [, course, year, letter, groupNum] = match;
            
                    return {
                        course: course,
                        yearLevel: year,
                        letter: letter,
                        groupNum: groupNum,
                        specialization: specialization,
                    };
                }
            
                // Set the input fields based on the selected section
                function updateFields() {
                    const selectedOption = sectionBox.options[sectionBox.selectedIndex];
                    if (selectedOption) {
                        const details = extractSectionDetails(selectedOption.textContent);
                        courseBox.value = details.course || '';
                        yearLevelBox.value = details.yearLevel || '';
                        letterBox.value = details.letter || '';
                        groupNumBox.value = details.groupNum || '';
            
                        if (details.specialization) {
                            // Directly select the option in the specializationBox by matching the value
                            const options = Array.from(specializationBox.options);
                            const match = options.find(option => option.textContent.trim() === details.specialization.trim());
                            if (match) {
                                specializationBox.value = match.value; // Set the value of the matching option (using actual value)
                            } else {
                                specializationBox.value = ''; // Default to an empty value if no match is found
                            }
                        } else {
                            specializationBox.value = ''; // Default if no specialization is provided
                        }
                    }
                }
            
                // Set fields when the page loads
                updateFields();
            
                // Update fields when the dropdown value changes
                sectionBox.addEventListener('change', updateFields);
            });

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
            
            
            function getSections(){
               global $conn;
                
                try{
                    $stmt = $conn->prepare("SELECT id FROM academic_year ORDER BY id DESC LIMIT 1");
                    $stmt->execute();
                    $year = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $acadYearID = $year["id"];
                    
                    $sql = "SELECT * FROM sections WHERE academicYearID = ? AND sectionID <> '0'";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$acadYearID]);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $selectedSection= isset($_POST['sectionBox']) ? htmlspecialchars($_POST['sectionBox']) : '';
                    
                    $course = "";
                    $coordinators = [];
                    $sems = [];
                    
                    foreach($results as $result){
                         // Get faculty information
                        $sectionID = htmlspecialchars($result["sectionID"]);
                        $section = htmlspecialchars($result["courseID"]) . ' ' . htmlspecialchars($result["yearLevel"]) . '' . htmlspecialchars($result["section_letter"]) . '' . htmlspecialchars($result["section_group"]);
                        $special = $result["specialization"];
                        $coordID = $result["coordinatorID"];
                        $coordinators[] = $coordID;
                        $course = $result["courseID"];
                        
                        $semester = $result["semester"];
                        $sems[] = $semester;
                        
                        $isSelected = $selectedSection === $sectionID ? 'selected' : '';

                        // Echo an option element with faculty details
                        echo "<option value=\"$sectionID\" $isSelected>$section - $special</option>";
  
                    }
                    
                      echo "
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                     document.getElementById('semesterBox').value = '{$sems[0]}';
                                     document.getElementById('facultyResults').value = '{$coordinators[0]}';
                                });
                            </script>
                        ";
                    
                    getSpecializations($course);
                    
                }
                
                catch(Exception $e){
                    
                } 
            }
            
            function getCourses(){
                global $conn;
                
                try{
                    $sql = "SELECT c.*, u.firstname, u.middlename, u.surname, u.id 
                            FROM courses c
                            JOIN users u ON c.adminID = u.id
                            WHERE courseID <> ?";
                            
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(["N/A"]);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach($results as $result){
                        $courseID = htmlspecialchars($result["courseID"]);
                        $facultyName = htmlspecialchars($result["surname"]) . ', ' . htmlspecialchars($result["firstname"]) . ' ' . htmlspecialchars($result["middlename"]);
                        $courseName = htmlspecialchars($result["courseName"]);
                        
                        // Echo an option element with faculty details
                        echo "<option value=\"$courseID\">($courseID) $courseName</option>";
                    }
                }
                
                catch(Exception $e){
                    
                }
            }
            
            
            function getSpecializations($course){
                global $conn;
                
                try {
                     $stmt = $conn->prepare("SELECT * FROM specializations WHERE courseID = ? AND id <> '0'");
                     $stmt->execute([$course]);
                     $specials = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    

                     foreach($specials as $special){
                        $special_id = $special["id"];
                        $special_name = $special["name"];
                        
                        echo "
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    document.getElementById('specializationBox').add(new Option('$special_name', '$special_id'));
                                });
                            </script>
                        ";
                    
                    }
                } 
                
                catch (Exception $e) {
                    // Handle exception
                }
            }
            
            
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                
                if(isset($_POST["update-button"])){
                    
                    $specializationID = htmlspecialchars($_POST["specializationBox"]);
                    $course = htmlspecialchars($_POST["courseBox"]);
                    $year = htmlspecialchars($_POST["yearLevelBox"]);
                    $section = htmlspecialchars($_POST["sectionBox"]);
                    $letter = htmlspecialchars($_POST["letterBox"]);
                    $groupNum = htmlspecialchars($_POST["groupNumBox"]);
                    $semester = htmlspecialchars($_POST["semesterBox"]);
                    $coordinator = htmlspecialchars($_POST["facultyBox"]);
                    
                    $stmt = $conn->prepare("SELECT name FROM specializations WHERE id = ?");
                    $stmt->execute([$specializationID ]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $specialization = $result["name"];
                    
    
                    function updateSection() {
                       
                        global $conn;
                        global $course, $specialization, $year, $section, $groupNum, $semester, $coordinator, $letter;
                        
                        try{
                            $conn->beginTransaction();
                            
                            $stmt = $conn->prepare("SELECT coordinatorID FROM sections WHERE sectionID = ?");
                            $stmt->execute([$section]);
                            $old = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $old_coordinator = $old["coordinatorID"];
                            
                            $stmt = $conn->prepare("UPDATE sections SET courseID = ?, yearLevel = ?, semester = ?, section_letter = ?, section_group = ?, specialization = ?, coordinatorID = ? WHERE sectionID = ?");
                            $result = $stmt->execute([$course, $year, $semester, $letter, $groupNum, $specialization, $coordinator, $section]);
                            
                            if($result){
                                
                                $stmt = $conn->prepare("SELECT coordinator_count FROM faculty_count WHERE facultyID = ?");
                                $stmt->execute([$old_coordinator]);
                                $old_count = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                $stmt = $conn->prepare("SELECT coordinator_count, coordinator_limit FROM faculty_count WHERE facultyID = ?");
                                $stmt->execute([$coordinator]);
                                $cord_count = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                $count = $cord_count["coordinator_count"];
                                $limit = $cord_count["coordinator_limit"];
                                
                                $old_cord_count = $old_count["coordinator_count"];
                                
                                if(($count + 1) <= $limit){
                                    
                                    $newcount = $count + 1;
                                    $newcount2 = $old_cord_count - 1;
                                    
                                    $stmt = $conn->prepare("UPDATE faculty_count SET coordinator_count = ? WHERE facultyID = ?");
                                    $result = $stmt->execute([$newcount, $coordinator]);
                                    
                                    if(!$result){
                                        throw new Exception("Error in updating coordinator count of selected faculty member");
                                    }
                                    
                                    
                                    $stmt = $conn->prepare("UPDATE faculty_count SET coordinator_count = ? WHERE facultyID = ?");
                                    $result = $stmt->execute([$newcount2, $old_coordinator]);
                                    
                                    if(!$result){
                                        throw new Exception("Error in updating coordinator count of previous coordinator");
                                    }
                                    
                                    $conn->commit();
                                    
                                    echo '<script>
                                        Swal.fire({
                                             title: "Section Updated",
                                              text: "Section Succesfuly Updated!",
                                            icon: "success",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "/edit_sections";
                                            }
                                            
                                            else if (result.isDismissed) {
                                                 window.location.href = "/edit_sections";
                                            }
                                        });
                                    </script>';
                                }
                                
                                else{
                                    throw new Exception("The selected faculty member has reached their coordinator limit! Please select another faculty or increase their coordinator limit");
                                }
                            }
                            
                            else{
                                throw new Exception("Possibility of duplicated sections or incorrect input values. Please try again");
                            }
                        }
                        
                        catch(Exception $e){
                            $conn->rollBack();
                            
                            echo '<script>';
                            echo 'console.log(' . json_encode("Error: " . $e->getMessage()) . ');';
                            echo '</script>';
                            
                            unset($_POST["update-button"]);
                            
                            
                            echo '<script>
                                    Swal.fire({
                                         title: "Error In Updating Section",
                                          text: "'.$e->getMessage().'",
                                        icon: "error",
                                        confirmButtonText: "OK"
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "/edit_sections";
                                        }
                                        
                                        else if (result.isDismissed) {
                                             window.location.href = "/edit_sections";
                                        }
                                    });
                                </script>';
                        }
                    }
                    
                    updateSection();
                    
                }

                if(isset($_POST["sectionBox"])){
                    try{
                        $sql = "SELECT s.*, sp.id AS specializationID, u.firstname, u.middlename, u.surname, u.id 
                                FROM sections s
                                JOIN users u ON s.coordinatorID = u.id
                                JOIN specializations sp ON s.specialization = sp.name
                                WHERE s.sectionID = ?";
                                
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_POST["sectionBox"]]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        
                        $year = $result["yearLevel"];
                        $sem = $result["semester"];
                        $letter = $result["section_letter"];
                        $num = $result["section_group"];
                        $specialization = $result["specialization"];
                        $specializationID = $result["specializationID"];
                        $course = $result["courseID"];
                        
                        $coordinator = $result["id"];
                        
                        
                        $stmt = $conn->prepare("SELECT * FROM specializations WHERE courseID = ? AND id <> '0'");
                        $stmt->execute([$course]);
                        $specials = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        

                        foreach($specials as $special){
                            $special_id = $special["id"];
                            $special_name = $special["name"];
                            
                            echo "
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        document.getElementById('specializationBox').add(new Option('$special_name', '$special_id'));
                                    });
                                </script>
                            ";
                        }
                        
                        echo "
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    document.getElementById('semesterBox').value = ".json_encode($sem).";
                                    document.getElementById('facultyResults').value = '$coordinator';
                                });
                            </script>
                        ";

                        
                        echo '
                        
                            <script>
                                document.getElementById("courseBox").value = '.json_encode($course).';
                                document.getElementById("yearLevelBox").value = '.json_encode($year).';
                                document.getElementById("letterBox").value = '.json_encode($letter).';
                                document.getElementById("groupNumBox").value = '.json_encode($num).';
                                document.getElementById("specializationBox").value = '.json_encode($specializationID).';
                            </script>
                        ';
        
                    }
                    
                    catch(Exception $e){
                        echo'
                            <script>
                                console.log('.$e->getMessage().')
                            </script>
                        ';
                    }
                }
            }
                    
        ?>
    
        <?php require 'footer.php'; ?>
    </body>
</html>



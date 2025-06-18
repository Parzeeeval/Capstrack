<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="pages/create_courses.css">
        <link rel="stylesheet" href="pages/accounts.css">
        <link rel="stylesheet" href="pages/accounts_student.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <title>Edit Course</title>
        <script src="pages/session_tracker.js"></script>
        
      
    </head>
    
    <body>
        <?php 
            require 'connection.php'; 
            session_start();
        ?>
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
    
        <?php
            $selectedAdmin = isset($_POST['adminBox']) ? htmlspecialchars($_POST['adminBox']) : '';
            $selectedCourse = isset($_POST['course']) ? htmlspecialchars($_POST['courseBox']) : '';
        ?>
    
        <div id="content2">
            <div class="profile-tabs">
                <div class="tabs">
                    <button class="active">Edit Courses</button>
                    <button onclick="window.location.href='/edit_specializations';">Edit Specializations</button>
                    <button onclick="window.location.href='/edit_sections';">Edit Sections</button>
                    <button onclick="window.location.href='/edit_tags';">Edit Title Tags</button>
                </div>
            </div>
            
        <form action="" method="POST">
                <div class="optionDiv">
                    <div class="left-section">
                        <div class="form-group">
                            <label for="courseBox">Course</label>
                                <select name="courseBox" id="courseBox" style="width: 820px; background-color: #fff9ee;" required onchange="this.form.submit()">
                                   <?php getCourses(); ?>
                                </select>
                        </div>
                        
                         <div class="form-group">
                            <label for="courseName">Course Name</label>
                            <input type="text" pattern="[A-Za-z\s]*" oninput="validateInput(this)" name="courseName" id="courseName" placeholder="Ex: Bachelor of Science in Information Technology" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="courseAbbrev">Course Abbreviation</label>
                            <input type="text" pattern="[A-Za-z\s]*" oninput="validateInput(this)" name="courseAbbrev" id="courseAbbrev" placeholder="Ex: BSIT" required>
                        </div>

                        <div class="button-group" style="justify-content: left; gap: 20px;">
                            <button class="form-button" type="submit" name="update-button">Update</button>
                            <button class="form-button" type="button" name="cancel-button" onclick="window.location.href = '/edit_courses'" style="background-color: gray;">Cancel</button>
                        </div>
                    </div>
                    
                     <div class="right-section">
                            <div class="form-group">
                                
                                <h2>Select New Course Administrator</h2>
                                <label for="facultySearch">Search a Faculty using <span style="color: #41A763;"> Name </span> or <span style="color: #41A763;"> Email </span></label>
                                
                                <input type="text" id="facultySearch" placeholder="Enter Faculty Name or Email" onkeyup="filterFaculty()" style="font-size: 20px;">
                                
                                <select name="facultyBox" id="facultyResults" size="10" style="font-size: 20px; margin-top: 10px;" required>
                                   <?php getFaculties(); ?>
                                </select>
                            </div>
                    </div>
                </div>
        </form>
        
        
        <script>
            function validateInput(input) {
                const regex = /[0-9]/g;
                if (regex.test(input.value)) {
                    input.value = input.value.replace(regex, ''); // Remove numeric digits
                }
            }
            
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
                    $sql = "SELECT * FROM users WHERE id <> ? AND id <> ? AND type = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["userID"], 0, "faculty"]);
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
                    
                    $admins = [];
                    $names = [];
                    $ids = [];
                    
                    foreach($results as $result){
                        $courseID = htmlspecialchars($result["courseID"]);
                        $ids[] = $courseID;
                        
                        $facultyID = $result["adminID"];
                        $admins[] = $facultyID;
                        
                        $courseName = htmlspecialchars($result["courseName"]);
                        $names[] = $courseName;
                        
                        // Echo an option element with faculty details
                        echo "<option value=\"$courseID\">($courseID) $courseName</option>";

                    }
                    
                    echo "
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    document.getElementById('courseName').value = '{$names[0]}';
                                    document.getElementById('courseAbbrev').value = '{$ids[0]}';
                                    document.getElementById('facultyResults').value = '{$admins[0]}';
                                });
                            </script>
                        ";
                }
                
                catch(Exception $e){
                    
                }
            }
            
            
        
        
        
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["update-button"])) {
                    $selectedCourse = htmlspecialchars($_POST["courseBox"]);
                    
                    // Get the course name and abbreviation from the POST data
                    $courseName = htmlspecialchars($_POST["courseName"]);
                    $courseAbbrev = htmlspecialchars($_POST["courseAbbrev"]);
                    
                    // Convert the course name to lowercase first
                    $courseName = strtolower($courseName);
                    
                   // List of exceptions
                    $exceptions = ['of', 'in', 'and'];
                    
                    // Split the course name into words
                    $words = explode(' ', $courseName);
                    
                    // Capitalize the first word and others unless they are exceptions
                    foreach ($words as $key => $word) {
                        // Always capitalize the first word, and capitalize others if they're not in the exception list
                        if ($key == 0 || !in_array(strtolower($word), $exceptions)) {
                            $words[$key] = ucfirst(strtolower($word)); // Capitalize the word
                        } else {
                            $words[$key] = strtolower($word); // Leave the word in lowercase if it's in the exception list
                        }
                    }
                    
                    // Rejoin the words back into a string
                    $courseName = implode(' ', $words);

                    // Convert course abbreviation to all uppercase
                    $courseAbbrev = strtoupper($courseAbbrev);
                    
                    // Remove any special characters (e.g., punctuation, non-alphanumeric characters) from course name and abbreviation
                    $courseName = preg_replace("/[^a-zA-Z0-9\s]/", "", $courseName);
                    $courseAbbrev = preg_replace("/[^a-zA-Z0-9\s]/", "", $courseAbbrev);
                    
                    $adminID = htmlspecialchars($_POST["facultyBox"]);
                    
                 
                    global $conn;
                    
                    try {
                        $conn->beginTransaction();
                        
                        $sql = "UPDATE courses SET courseID = ?, courseName = ?, adminID = ? WHERE courseID = ?";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$courseAbbrev, $courseName, $adminID, $selectedCourse]);
                        
                        if ($result) {
                            
                            $sql = "UPDATE specializations SET courseID = ? WHERE courseID = ?";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$courseAbbrev, $selectedCourse]);
                            
                            if(!$result){
                                throw new Exception("Error");
                            }
                            
                            $conn->commit();

                            unset($_POST["update-button"]);
                            
                            echo '<script>
                                Swal.fire({
                                     title: "Success",
                                    text: "Course: ' . addslashes($courseName) . ' updated!",
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/edit_courses";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/edit_courses";
                                    }
                                });
                            </script>';
                                     
                        } 
                        
                        else {
                            throw new Exception("Error in creating course, possibility of duplication");
                        }
                    } 
                    
                    catch (Exception $e) {
                        $conn->rollBack();
                            
                        echo '<script>';
                        echo 'console.log(' . json_encode("Error: " . $e->getMessage()) . ');';
                        echo '</script>';
                        
                        unset($_POST["update-button"]);
                        
                        
                        echo '<script>
                                Swal.fire({
                                     title: "Error In Updating Course",
                                      text: "The course may be a duplicate of an existing one or incorrect input values, please check details and try again",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/edit_courses";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/edit_courses";
                                    }
                                });
                            </script>';
                    }
                }
                
                else if(isset($_POST["courseBox"])){
                    try{
                        $sql = "SELECT c.*, u.firstname, u.middlename, u.surname, u.id 
                                FROM courses c
                                JOIN users u ON c.adminID = u.id
                                WHERE courseID = ?";
                                
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_POST["courseBox"]]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
              
                        $courseAbbrev = htmlspecialchars($result["courseID"]);
                        $courseAdmin = htmlspecialchars($result["adminID"]);
                        $courseName = htmlspecialchars($result["courseName"]);
                        
                        echo '
                        
                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    document.getElementById("courseBox").value = '.json_encode($courseAbbrev).';
                                    document.getElementById("courseName").value = '.json_encode($courseName).';
                                    document.getElementById("courseAbbrev").value = '.json_encode($courseAbbrev).';
                                    document.getElementById("facultyResults").value = '.json_encode($courseAdmin).';
                                });
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



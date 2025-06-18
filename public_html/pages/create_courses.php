<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="pages/create_courses.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <title>Create New Course</title>
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
    ?>

    <form action="" method="POST">
   
            <div class="header-group">
                <h2>Create New Course</h2>
                <h2>For</h2>
                <h2>College of Information and Communications Technology</h2>
            </div>

            <div class="divider"></div> <!-- Divider added here -->
            
            <div class="optionDiv">
                <div class="left-section">
                    <div class="form-group">
                        <label for="courseName">Course Name</label>
                        <input type="text" pattern="[A-Za-z\s]*" oninput="validateInput(this)" name="courseName" id="courseName" placeholder="Course Name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="courseAbbrev">Course Abbreviation</label>
                        <input type="text" pattern="[A-Za-z\s]*" oninput="validateInput(this)" name="courseAbbrev" id="courseAbbrev" placeholder="BSIT" required>
                    </div>
                    
                    <div class="button-group">
                        <button class="form-button" type="submit" name="create-button">Create</button>
                    </div>
                </div>
                
                 <div class="right-section">
                        <div class="form-group">
                            
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
    
    
    
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["create-button"])) {
                $courseName = htmlspecialchars($_POST["courseName"]);
                $courseAbbrev = htmlspecialchars($_POST["courseAbbrev"]);
                $adminID = htmlspecialchars($_POST["facultyBox"]);
             
                global $conn;
                
                try {
                    $conn->beginTransaction();
                    
                    $sql = "SELECT * FROM courses WHERE courseID = ? AND courseName = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$courseAbbrev, $courseName]);
                    $rowCount = $stmt->rowCount();
                    
                    if ($rowCount <= 0) {
                        $sql = "INSERT INTO courses (courseID, courseName, adminID, created_at) VALUES (?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$courseAbbrev, $courseName, $adminID, date("Y-m-d")]);
                        
                        if ($result) {
                            $sql = "INSERT INTO specializations (courseID, name, created_at) VALUES (?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$courseAbbrev, "No Specialization", date("Y-m-d")]);
                            
                            if ($result) {
                                $sql = "UPDATE faculty SET accessLevel = ? WHERE id = ?";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([2, $adminID]);
                                
                                if ($result) {
                                    $conn->commit();

                                    unset($_POST["create-button"]);
                                    
                                    echo '<script>
                                        Swal.fire({
                                             title: "Success",
                                            text: "' . addslashes($courseName) . ' successfully created!",
                                            icon: "success",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "/create_courses";
                                            }
                                            
                                            else if (result.isDismissed) {
                                                 window.location.href = "/create_courses";
                                            }
                                        });
                                    </script>';
                                } else {
                                    throw new Exception("Error in updating administrator access of faculty");
                                }
                            } else {
                                throw new Exception("Error in creating its initial specialization");
                            }
                        } else {
                            throw new Exception("Error in creating new course");
                        }
                    } else {
                        throw new Exception("Error in creating course, possibility of duplication");
                    }
                } catch (Exception $e) {
                    $conn->rollBack();
                    
                    unset($_POST["create-button"]);
                    
                    echo '<script>
                            Swal.fire({
                                 title: "Error Creating Course",
                                text: "The course being created may have been a duplicate of an already existing course",
                                icon: "error",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/create_courses";
                                }
                                
                                else if (result.isDismissed) {
                                     window.location.href = "/create_courses";
                                }
                            });
                    </script>';
                }
            }
        }
    ?>
    
 
</body>
   <?php require 'footer.php'; ?>  
</html>


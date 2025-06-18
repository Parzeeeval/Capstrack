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
        <title>Edit Specialization</title>
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
            $selectedSpecialization = isset($_POST['specializationBox']) ? htmlspecialchars($_POST['specializationBox']) : '';
        ?>
    

            <div id="content2">
            <div class="profile-tabs">
                <div class="tabs">
                    <button onclick="window.location.href='/edit_courses';">Edit Courses</button>
                    <button class="active" onclick="window.location.href='/edit_specializations';">Edit Specializations</button>
                    <button onclick="window.location.href='/edit_sections';">Edit Sections</button>
                    <button onclick="window.location.href='/edit_tags';">Edit Title Tags</button>
                </div>
            </div>
            
            <form action="" method="POST">
                <div class="optionDiv">
                    <div class="left-section">
                        <div class="form-group">
                            <label for="courseBox">Specialization</label>
                            
                            <br>
                            
                            <select name="specializationBox" id="specializationBox" style="width: 820px; background-color: #fff9ee;" required onchange="this.form.submit()">
                               <?php getSpecializations(); ?>
                            </select>
                        </div>
                        
                        <br>
                        
                         <div class="form-group">
                            <label for="specializationName">Specialization Name</label>
                            
                            <br>
                            
                            <input type="text" pattern="[A-Za-z\s]*" oninput="validateInput(this)" style="width: 820px;" name="specializationName" id="specializationName" placeholder="Ex: Web And Mobile Development" required>
                        </div>

                        <div class="form-group">
                            <label for="courseBox">Parent Course</label>
                            
                            <br>
                            
                            <select name="courseBox" id="courseBox" style="width: 820px; background-color: #fff9ee;" required>
                               <?php getCourses(); ?>
                            </select>
                        </div>
                        
                        <div class="button-group" style="justify-content: left; gap: 20px;">
                            <button class="form-button" type="submit" name="update-button">Update</button>
                            <button class="form-button" type="button" name="cancel-button" onclick="window.location.href = '/edit_specializations'" style="background-color: gray;">Cancel</button>
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
            
            document.addEventListener('DOMContentLoaded', function () {
                const specializationBox = document.getElementById('specializationBox');
                const specializationNameInput = document.getElementById('specializationName');
            
                // Function to extract specialization name from the option text
                function extractSpecializationName(optionText) {
                    const parts = optionText.split('-'); // Split by the hyphen
                    return parts.length > 1 ? parts[1].trim() : ''; // Return the part after the hyphen
                }
            
                // Set the specialization name when the page loads
                const selectedOption = specializationBox.options[specializationBox.selectedIndex];
                if (selectedOption) {
                    specializationNameInput.value = extractSpecializationName(selectedOption.textContent);
                }
            
                // Update the specialization name when the dropdown value changes
                specializationBox.addEventListener('change', function () {
                    const selectedOption = specializationBox.options[specializationBox.selectedIndex];
                    if (selectedOption) {
                        specializationNameInput.value = extractSpecializationName(selectedOption.textContent);
                    }
                });
            });
        </script>
    
        <?php
             
            function getCourses(){
                global $conn;
                
                try {
                    $sql = "SELECT c.*, u.firstname, u.middlename, u.surname, u.id 
                            FROM courses c
                            JOIN users u ON c.adminID = u.id
                            WHERE courseID <> ?";
                            
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(["N/A"]);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
                    $selectedCourse = isset($_POST['courseBox']) ? htmlspecialchars($_POST['courseBox']) : '';
                    
                    foreach ($results as $result) {
                        $courseID = htmlspecialchars($result["courseID"]);
                        $facultyName = htmlspecialchars($result["surname"]) . ', ' . htmlspecialchars($result["firstname"]) . ' ' . htmlspecialchars($result["middlename"]);
                        $courseName = htmlspecialchars($result["courseName"]);
                        
                        $isSelected = $selectedCourse === $courseID ? 'selected' : '';
                        
                        echo "<option value=\"$courseID\" $isSelected>($courseID) $courseName</option>";
                    }
                } catch (Exception $e) {
                    // Handle exception
                }
            }

            
            function getSpecializations(){
                global $conn;
                
                try {
                    $sql = "SELECT id, courseID, name FROM specializations WHERE name <> ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(["No Specialization"]);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
                    $selectedSpecialization = isset($_POST['specializationBox']) ? htmlspecialchars($_POST['specializationBox']) : '';
                    
                    $ids = [];
            
                    foreach ($results as $result) {
                        $specializationID = htmlspecialchars($result["id"]);
                        
                        $courseID = htmlspecialchars($result["courseID"]);
                        $ids[] = $courseID;
                        
                        $specializationName = htmlspecialchars($result["name"]);
                        
                        $isSelected = $selectedSpecialization === $specializationID ? 'selected' : '';
                        
                        echo "
                            <option value=\"$specializationID\" $isSelected>($courseID) - $specializationName</option>
                        ";
                    }
                    
                    echo "
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                 document.getElementById('courseBox').value = '{$ids[0]}';
                            });
                        </script>
                    ";
                    
                } 
                
                catch (Exception $e) {
                    // Handle exception
                }
            }
            
        
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["update-button"])) {
                    $selectedSpecialization = htmlspecialchars($_POST["specializationBox"]);
                    
                    // Get the specialization name from the POST data
                    $specializationName = htmlspecialchars($_POST["specializationName"]);
                    
                    // Capitalize the first letter of each word
                    $specializationName = ucwords(strtolower($specializationName));
                    
                    // Remove any special characters (e.g., punctuation, non-alphanumeric characters)
                    $specializationName = preg_replace("/[^a-zA-Z0-9\s]/", "", $specializationName);
                    
                    $courseID= htmlspecialchars($_POST["courseBox"]);

                    global $conn;
                    
                    try {
                        $conn->beginTransaction();
                    
                        // Check if the new specialization name already exists for the same courseID
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM specializations WHERE name = ? AND courseID = ?");
                        $stmt->execute([$specializationName, $courseID]);
                        $duplicateCheck = $stmt->fetchColumn();
                        
                        // If there's already a specialization with the same name for the same course, throw an exception
                        if ($duplicateCheck > 0) {
                            throw new Exception("A specialization with the same name already exists for this course.");
                        }
                        
                        // Get the old name of the specialization before updating
                        $stmt = $conn->prepare("SELECT name FROM specializations WHERE id = ?");
                        $stmt->execute([$selectedSpecialization]);
                        $sp_name = $stmt->fetch(PDO::FETCH_ASSOC);
                        $old_name = $sp_name["name"];
                    
                        // Update the specialization record
                        $sql = "UPDATE specializations SET courseID = ?, name = ? WHERE id= ?";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$courseID, $specializationName, $selectedSpecialization]);
                    
                        if ($result) {
                            // Update the related sections where the specialization name was previously used
                            $sql = "UPDATE sections SET specialization = ? WHERE specialization = ?";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$specializationName, $old_name]);
                    
                            if ($result) {
                                // Commit the transaction if everything is successful
                                $conn->commit();
                                unset($_POST["update-button"]);
                                
                                echo '<script>
                                    Swal.fire({
                                        title: "Success",
                                        text: "Specialization: ' . addslashes($specializationName) . ' updated!",
                                        icon: "success",
                                        confirmButtonText: "OK"
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "/edit_specializations";
                                        } else if (result.isDismissed) {
                                            window.location.href = "/edit_specializations";
                                        }
                                    });
                                </script>';
                            } 
                            
                            else {
                                throw new Exception("Error in updating specializations of sections");
                            }
                        } 
                        
                        else {
                            throw new Exception("Error in updating specialization, possibility of duplication");
                        }
                    } 
                    
                    catch (Exception $e) {
                        // Rollback if there's any error
                        $conn->rollBack();
                        
                        echo '<script>';
                        echo 'console.log(' . json_encode("Error: " . $e->getMessage()) . ');';
                        echo '</script>';
                        
                        unset($_POST["update-button"]);
                        
                        echo '<script>
                            Swal.fire({
                                title: "Error In Updating Specialization",
                                text: "The specialization may be a duplicate of an existing one or incorrect input values, please check details and try again",
                                icon: "error",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/edit_specializations";
                                } else if (result.isDismissed) {
                                    window.location.href = "/edit_specializations";
                                }
                            });
                        </script>';
                    }

                }
                
                else if(isset($_POST["specializationBox"])){
                    try{
                        $sql = "SELECT * FROM specializations WHERE id = ?";
                                
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_POST["specializationBox"]]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
              
                        $courseID = htmlspecialchars($result["courseID"]);
                        $specializationName = htmlspecialchars($result["name"]);
                        
                        echo '
                        
                            <script>
                                document.getElementById("specializationName").value = '.json_encode($specializationName).';
                            </script>
                        ';
                        
                        echo '
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                // Get the selected option text from the specializationBox
                                const specializationBox = document.getElementById("specializationBox");
                                const selectedText = specializationBox.options[specializationBox.selectedIndex].text;
                    
                                // Extract the courseID from the format (BSIT) - Service Management
                                const match = selectedText.match(/^\((\w+)\) -/);
                                if (match && match[1]) {
                                    const courseID = match[1];
                    
                                    // Set the courseBox value with the extracted courseID
                                    document.getElementById("courseBox").value = courseID;
                                }
                    
                                // Set the specialization name in the specializationName input
                                document.getElementById("specializationName").value = ' . json_encode($specializationName) . ';
                            });
                        </script>';
        
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

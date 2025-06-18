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
        <title>Edit Title Tags</title>
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
            $selectedTag= isset($_POST['tagBox']) ? htmlspecialchars($_POST['tagBox']) : '';
        ?>
    

            <div id="content2">
            <div class="profile-tabs">
                <div class="tabs">
                    <button onclick="window.location.href='/edit_courses';">Edit Courses</button>
                    <button onclick="window.location.href='/edit_specializations';">Edit Specializations</button>
                    <button onclick="window.location.href='/edit_sections';">Edit Sections</button>
                    <button class="active" onclick="window.location.href='/edit_tags';">Edit Title Tags</button>
                </div>
            </div>
            
            
            <div class="optionDiv">
                
                <form action="" method="POST">
                    <div class="left-section">
                        <div class="form-group">
                            <label for="courseBox">Title Tags</label>
                            
                            <br>
                            
                            <select name="tagBox" id="tagBox" style="width: 820px; background-color: #fff9ee;" required onchange="this.form.submit()">
                               <?php getTags(); ?>
                            </select>
                        </div>
                        
                        <br>
                        
                         <div class="form-group">
                            <label for="tagName">Selected Title Tag</label>
                            
                            <br>
                            
                            <input type="text" pattern="[A-Za-z\s]*" oninput="validateInput(this)" style="width: 820px;" name="tagName" id="tagName" placeholder="Ex: Web Development" required>
                        </div>

       
                        <div class="button-group" style="justify-content: left; gap: 20px;">
                            <button class="form-button" type="submit" name="update-button">Update</button>
                            <button class="form-button" type="button" name="cancel-button" onclick="window.location.href = '/edit_tags'" style="background-color: gray;">Cancel</button>
                        </div>
                    </div>
                </form>
                
                <form action="" method="POST">
                    <div class="right-section">
                        <div class="form-group">
                            <div class="form-group">
                                <label for="tagInput">New Title Tag</label>
                                
                                <br>
                                
                                <input type="text" pattern="[A-Za-z\s]*" oninput="validateInput(this)" style="width: 820px;" name="tagInput" id="tagInput" placeholder="Ex: Web Development" required>
                            </div>
                        </div>
                        
                        <div class="button-group" style="justify-content: left; gap: 20px;">
                            <button class="form-button" type="submit" name="add-button">Add New Tag</button>
                        </div>
                    </div>
                </form>
                
            </div>
        
        
        
        <script>
            function validateInput(input) {
                const regex = /[0-9]/g;
                if (regex.test(input.value)) {
                    input.value = input.value.replace(regex, ''); // Remove numeric digits
                }
            }
            
            document.addEventListener('DOMContentLoaded', function () {
                const tagBox = document.getElementById('tagBox');
                const tagNameInput = document.getElementById('tagName');
            
            
                // Set the specialization name when the page loads
                const selectedOption = tagBox.options[tagBox.selectedIndex];
                if (selectedOption) {
                    tagNameInput.value = selectedOption.textContent;
                }
            
                // Update the specialization name when the dropdown value changes
                tagBox.addEventListener('change', function () {
                    const selectedOption = tagBox.options[tagBox.selectedIndex];
                    
                    if (selectedOption) {
                        tagNameInput.value = selectedOption.textContent;
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

            
            function getTags(){
                global $conn;
                
                try {
                    $sql = "SELECT * FROM tags";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
                    $selectedTag= isset($_POST['tagBox']) ? htmlspecialchars($_POST['tagBox']) : '';
                    
   
                    foreach ($results as $result) {
                        $tagID = htmlspecialchars($result["id"]);
                        
                        $tagName = ucwords(str_replace('_', ' ', htmlspecialchars($result["tag"])));
                        
                        $isSelected = $selectedTag === $tagID ? 'selected' : '';
                        
                        echo "
                            <option value=\"$tagID\" $isSelected>$tagName</option>
                        ";
                    }
                } 
                
                catch (Exception $e) {
                    // Handle exception
                }
            }
            
        
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["update-button"])) {
                    $selectedTag= htmlspecialchars($_POST["tagBox"]);
                    
                    // Get the specialization name from the POST data
                    $tagName = htmlspecialchars($_POST["tagName"]);
 
                    // Remove any special characters (e.g., punctuation, non-alphanumeric characters)
                    $tagName = preg_replace("/[^a-zA-Z0-9\s]/", "", $tagName);
                    
                    $tagName = trim($tagName);
                    
                    $tagName = strtolower(str_replace(' ', '_', $tagName));
                    

                    try {
                        $conn->beginTransaction();
                        
                        $sql = "SELECT tag FROM tags WHERE tag = '$tagName'";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if(!$result){
                            
                            $sql = "SELECT tag FROM tags WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$selectedTag]);
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $old_tag = $result["tag"];
                            
                        
                            $sql = "UPDATE tags SET tag = ? WHERE id = ?";
                            $stmt  = $conn->prepare($sql);
                            $result = $stmt->execute([$tagName, $selectedTag]);
                            
                            if($result){
                                
                                $sql = "UPDATE title_tags SET tag = ? WHERE tag = ?";
                                $stmt  = $conn->prepare($sql);
                                $result = $stmt->execute([$tagName, $old_tag]);
                                
                                if($result){
                                     $conn->commit();
                                    
                                     echo '<script>
                                            Swal.fire({
                                                title: "Success",
                                                text: "Title Tag: ' . addslashes($_POST["tagName"]) . ' updated!",
                                                icon: "success",
                                                confirmButtonText: "OK"
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "/edit_tags";
                                                } else if (result.isDismissed) {
                                                    window.location.href = "/edit_tags";
                                                }
                                            });
                                        </script>';
                                }
                                
                                else{
                                    throw new Exception("Failed to update tags of titles with selected tag " . $_POST["tagName"]);
                                }
                            }
                            
                            else{
                                throw new Exception("Failed to update tag into " . $_POST["tagName"]);
                            }
                        }
                        
                        else{
                            throw new Exception("Failed to update tag into " . $_POST["tagName"] . " for a similar tag already exists");
                        }
                    } 
                    
                    catch (Exception $e) {
                        $conn->rollBack();
                        
                        echo '<script>
                                Swal.fire({
                                    title: "Error",
                                    text: "'.$e->getMessage() .'",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/edit_tags";
                                    } else if (result.isDismissed) {
                                        window.location.href = "/edit_tags";
                                    }
                                });
                            </script>';
                    }

                }
                
                else if(isset($_POST["add-button"]) && isset($_POST["tagInput"])){
                    try{
                        $conn->beginTransaction();
                        
                        $tag= trim($tag);
                        
                        $tag = strtolower(str_replace(' ', '_', htmlspecialchars($_POST["tagInput"])));
                        
                        $sql = "SELECT tag FROM tags WHERE tag = '$tag'";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if(!$result){
                            
                            $sql = "INSERT INTO tags (tag) VALUES(?)";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$tag]);
                            
                            if($result){
                                
                                $conn->commit();
                                
                                 echo '<script>
                                        Swal.fire({
                                            title: "Success",
                                            text: "Title Tag: ' . addslashes($_POST["tagInput"]) . ' added!",
                                            icon: "success",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "/edit_tags";
                                            } else if (result.isDismissed) {
                                                window.location.href = "/edit_tags";
                                            }
                                        });
                                    </script>';
                            }
                            
                            else{
                                throw new Exception("Error adding new tag, failed insertion");
                            }
                        }
                        
                        else{
                            throw new Exception("Error adding new tag " . $_POST['tagInput'] . ", a similar tag already exist");
                        }
                    }
                    
                    catch(Exception $e){
                        $conn->rollBack();
                        
                        echo '<script>
                                Swal.fire({
                                    title: "Error",
                                    text: "'.$e->getMessage() .'",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/edit_tags";
                                    } else if (result.isDismissed) {
                                        window.location.href = "/edit_tags";
                                    }
                                });
                            </script>';
                    }
                }
                
                else if(isset($_POST["tagBox"])){
                    try{
                        $sql = "SELECT * FROM tags WHERE id = ?";
                                
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$_POST["tagBox"]]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);

                        $tagName = ucwords(str_replace('_', ' ', htmlspecialchars($result["tag"])));
                        
                        echo '
                        
                            <script>
                                document.getElementById("tagName").value = '.json_encode($tagName).';
                            </script>
                        ';
                        
                        echo '
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                // Get the selected option text from the tagBox
                                const tagBox = document.getElementById("tagBox");
                                const selectedText = tagBox.options[tagBox.selectedIndex].text;
                    
                                // Set the tag name in the tagName input
                                document.getElementById("tagName").value = ' . json_encode($tagName) . ';
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

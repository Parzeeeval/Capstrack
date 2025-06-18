<?php 
    require "connection.php";
    session_start();
    
    $currentEmail = "";
    
    function getUserInfo(){
        global $conn, $currentEmail;
        
        try{
            $sql = "SELECT type FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"]]);
            $getType = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $type = $getType["type"];
            
            if($type == "student"){
                $sql = "SELECT new_projectID, new_sectionID FROM students WHERE id =?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["userID"]]);
                $verifier = $stmt->fetch(PDO::FETCH_ASSOC);
                
                
                $sql = ""; //declare empty
                
                if($verifier["new_projectID"] == NULL && $verifier["new_sectionID"] == NULL){
                    
                    echo '
                        <script>
                            console.log("here 3rd year still");
                        </script>
                    ';
                
                    $sql = "SELECT u.*, st.*, sec.*, cp.*, cr.* FROM users u
                            JOIN students st ON st.id = u.id
                            JOIN sections sec ON st.sectionID = sec.sectionID
                            JOIN courses cr ON sec.courseID = cr.courseID
                            JOIN capstone_projects cp ON cp.projectID = st.projectID
                            WHERE u.id = ?";
                }
                
                else if($verifier["new_projectID"] != NULL && $verifier["new_sectionID"] != NULL){
                    
                    echo '
                        <script>
                            console.log("here 4th year now");
                        </script>
                    ';
                
                    $sql = "SELECT u.*, st.*, sec.*, cp.*, cr.* FROM users u
                            JOIN students st ON st.id = u.id
                            JOIN sections sec ON st.new_sectionID = sec.sectionID
                            JOIN courses cr ON sec.courseID = cr.courseID
                            JOIN capstone_projects cp ON cp.projectID = st.new_projectID
                            WHERE u.id = ?";
                }
                
                echo '
                    <script>
                        console.log(' . json_encode($sql) . ');
                    </script>
                ';
               
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["userID"]]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($user){
                    
                    $currentEmail = $user["email"];
                    
                    echo '
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                document.getElementById("last-name").value = "'.$user["surname"].'";
                                document.getElementById("first-name").value = "'.$user["firstname"].'";
                                document.getElementById("middle-name").value = "'.$user["middlename"].'";
                                document.getElementById("email").value = "'.$user["email"].'";
                                
                                document.getElementById("course").value = "'.$user["courseName"].'";
                                document.getElementById("specialization").value = "'.$user["specialization"].'";
                                
                                document.getElementById("section").value = "'.$user["yearLevel"].''.$user["section_letter"].'-'.$user["section_group"].'";
                                document.getElementById("capstone-group").value = "Group '.$user["groupNum"].'";
                                
                                document.getElementById("student-id").value = "'.$user["studentNo"].'";
                            });
                        </script>
                    ';
                }
            }
            
            else if($type == "faculty"){
                
                $sql = "SELECT u.*, f.* FROM users u
                        JOIN faculty f ON f.id = u.id
                        WHERE u.id = ?";
                        
                $stmt = $conn->prepare($sql);
                $stmt->execute([$_SESSION["userID"]]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($user){
                    
                    $currentEmail = $user["email"];
                    
                    echo '
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                document.getElementById("last-name").value = "'.$user["surname"].'";
                                document.getElementById("first-name").value = "'.$user["firstname"].'";
                                document.getElementById("middle-name").value = "'.$user["middlename"].'";
                                document.getElementById("email").value = "'.$user["email"].'";
                                
                                document.getElementById("course-label").innerText = "Faculty Category and Access Level";
                                document.getElementById("course").value = "'.$user["category"].' CICT Faculty   -   Access Level: '.$user["accessLevel"].'";
                                document.getElementById("specialization").style.display = "none";
                                
                                document.getElementById("section").style.display = "none";
                                document.getElementById("capstone-group").style.display = "none";
                                
                                document.getElementById("student-id").value = "'.$user["id"].'";
                                document.getElementById("studentnum-facultyid-label").innerText = "User ID";
                                
                                document.getElementById("special-label").innerText = "";
                                document.getElementById("section-label").innerText = "";
                                document.getElementById("group-label").innerText = "";
                            });
                        </script>
                        ';
                }
            }
        }
        
        catch(Exception $e){
            echo '
                <script>
                    console.log("'.$e->getMessage().'");
                </script>
            ';
        }
    }
    
    function updateEmail($email){
        global $conn, $currentEmail;
        
        try{
            if($currentEmail != $email){
                $conn->beginTransaction();
                
                $sql = "SELECT email FROM users WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$email]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if($result) {
                    $messages = "<strong>Email:</strong> <span style=\"color: red; font-weight: bold;\">" . htmlspecialchars($email) . "</span> <em>is already in use by another user";
                    
                     echo "<script>
                            Swal.fire({
                                title: 'Email Unavailable',
                                html: '$messages',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                     window.location.href = '/profile';
                                }
                                
                                else if (result.isDismissed) {
                                     window.location.href = '/profile';
                                }
                            });
                        </script>";
                } 
                
                else { 
                    $sql = "UPDATE users SET email = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $result = $stmt->execute([$email, $_SESSION["userID"]]);
                    
                    if($result){
                         $messages = "Updated <strong>Email:</strong> <span style=\"color: green; font-weight: bold;\">" . htmlspecialchars($email) . "</span>";
                         
                         
                         $sql = "SELECT * FROM users WHERE id = ?";
                         $stmt = $conn->prepare($sql);
                         $stmt->execute([$_SESSION["userID"]]);
                         $result = $stmt->fetch(PDO::FETCH_ASSOC);
                          
                         $firstname = $result["firstname"];
                         $surname = $result["surname"];
                         $middlename = $result["middlename"];
                                
                         $action = "". $surname . ", " . $firstname . " " . $middlename . " updated their email address into: " . $email;
                         
                         date_default_timezone_set('Asia/Manila');
                         $date = date('Y-m-d H:i:s');
        
                         $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                         $stmt = $conn->prepare($sql);
                         $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                        
                         if(!$result){
                             throw new Exception("Failed to insert action logs");  
                         }
                         
                         $conn->commit();
                         
                         echo "<script>
                            Swal.fire({
                                title: 'Email Update Succesful',
                                html: '$messages',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                     window.location.href = '/profile';
                                }
                                
                                else if (result.isDismissed) {
                                     window.location.href = '/profile';
                                }
                            });
                        </script>";
                    }
                    
                    else{
                        throw new Exception("Failed to update user email");
                    }
                }
            }
            
            else{
                $messages = "Current <strong>Email:</strong> <span style=\"color: green; font-weight: bold;\">" . htmlspecialchars($email) . "</span> cannot be the same as the new email";
                     
                 echo "<script>
                    Swal.fire({
                        title: 'No Update',
                        html: '$messages',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                             window.location.href = '/profile';
                        }
                        
                        else if (result.isDismissed) {
                             window.location.href = '/profile';
                        }
                    });
                </script>";
            }
        }
        
        catch(Exception $e){
            $conn->rollBack();

            unset($_POST["save-button"]);
            
            echo '<script>
                Swal.fire({
                     title: "Error Updating Profile",
                    text: "Profile updated failed: '.addslashes($e->getMessage()).'",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "/profile";
                    }
                    
                    else if (result.isDismissed) {
                         window.location.href = "/profile";
                    }
                });
            </script>';
        }
    }
    
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
       
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <script src="pages/session_tracker.js"></script>
    
        <link rel="stylesheet" href="pages/profile.css">
    
        <title>Profile Page</title>
    </head>
    
    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
        

        <div id="content2">
            <div class="profile-tabs">
                <div class="tabs">
                    <button class="active">Profile</button>
                    <button onclick="window.location.href='/password';">Password</button>
                </div>
            </div>
            
        <form class="profile-form" action="" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" readonly>
                </div>
                <div class="form-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" readonly>
                </div>
                <div class="form-group">
                    <label for="middle-name">Middle Name</label>
                    <input type="text" id="middle-name" readonly>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="course" id="course-label">Course</label>
                    <input type="text" id="course" readonly>
                </div>
                <div class="form-group">
                    <label for="student-id" id="studentnum-facultyid-label">Student No.</label>
                    <input type="text" id="student-id" readonly>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="specialization" id="special-label">Specialization</label>
                    <input type="text" id="specialization" readonly>
                </div>
                <div class="form-group">
                    <label for="section" id="section-label">Section</label>
                    <input type="text" id="section" readonly>
                </div>
                <div class="form-group">
                    <label for="capstone-group" id="group-label">Capstone Group No.</label>
                    <input type="text" id="capstone-group" readonly>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>
            
            <div class="form-buttons">
   
                    <!--<button type="button" class="edit-btn">Edit</button>-->
                <button type="submit" class="save-btn" name="save-button">Save</button>

            </div>
        </form>
            
    <?php getUserInfo(); ?>
    
    <?php 
         if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["save-button"]) && isset($_POST["email"])) {
                updateEmail($_POST["email"]);
            }
        }
    ?>
  
    <?php require 'footer.php'; ?>
    </body>
</html>

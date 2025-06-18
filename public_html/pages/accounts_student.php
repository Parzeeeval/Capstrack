<?php
    require "connection.php";
    session_start();
    
    function getAccess(){
        global $conn;
        
        try{
            $sql = "SELECT accessLevel FROM faculty WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                
                if($result["accessLevel"] <= 2){
                    echo '
                        <script>
                            window.location.href = "/404";
                        </script>
                    ';
                }
            }
        }
        
        catch(Exception $e) {
           
        }
    }
    
    
    
    
    
    
    $userID = NULL;
    
    // Check if a dropdown value has been sent via GET
    if (isset($_GET['studentID'])) {
        
        // Sanitize the input to prevent XSS
        $userID = htmlspecialchars($_GET['studentID']);
        
        // Store the selected value in a session variable

        // Example response
        echo '
            <script>
                console.log("ID: ' . $userID . '");
            </script>
        ';
        
        displayUserInfo();
        

        exit; // Important to exit after sending a response
    }
    

   
    function displayUserInfo(){
        global $conn, $userID;
        
        try{
            $sql = "SELECT * FROM students WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $verify = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($verify){
                $yearLevel = 0;
                $secIdentify = "";
                $projIdentify = "";
                
                
                
                if($verify["new_sectionID"] == NULL && $verify["new_projectID"] == NULL){
                    $yearLevel = 3;
                }
                
                else{
                    $yearLevel = 4;
                }
                
                
                $sql = ""; //declare empty
                
                
                if($yearLevel == 3){
                    $sql = "SELECT u.*, st.*, sec.*, cr.*, cp.*
                            FROM users u 
                            JOIN students st ON u.id = st.id
                            JOIN sections sec ON sec.sectionID = st.sectionID
                            JOIN capstone_projects cp ON st.projectID = cp.projectID
                            JOIN courses cr ON sec.courseID = cr.courseID
                            WHERE u.id = ?";
                            
                    $secIdentify = "sectionID";
                    $projIdentify = "projectID";
                }
                
                else{
                    $sql = "SELECT u.*, st.*, sec.*, cr.*, cp.* 
                            FROM users u 
                            JOIN students st ON u.id = st.id
                            JOIN sections sec ON sec.sectionID = st.new_sectionID
                            JOIN capstone_projects cp ON st.new_projectID = cp.projectID
                            JOIN courses cr ON sec.courseID = cr.courseID
                            WHERE u.id = ?";
                            
                    $secIdentify = "new_sectionID";
                    $projIdentify = "new_projectID";
                }
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([$userID]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($user){
                    $surname = $user["surname"];
                    $firstname = $user["firstname"];
                    $middlename = $user["middlename"];
                    $email = $user["email"];
                    $studentNo = $user["studentNo"];
                    $course = $user["courseName"];
                    $specialization = $user["specialization"];
                    
                    $section = $user[$secIdentify];
                    
                    $sectionName = $user["courseID"] . " " . $user["yearLevel"] . $user["section_letter"] . $user["section_group"];
                    
                    
                    
                    $groupStatus = "";
                    
                    if($user[$projIdentify] <= 0){
                        $groupStatus = "Not Yet in a Capstone Group";
                    }
                    
                    else{
                        $groupStatus = "Group :" . $user["groupNum"] . " " . $user["title"];
                    }
                    
                    
                    
     
                    $projectID = $user[$projIdentify];
                    
                    
                    

                    //Take note dont use domcontentloaded, since this is inside AJAX GET
                    
                    echo '
                        <script>
                                document.getElementById("last-name").value = '.json_encode($surname).';
                                document.getElementById("first-name").value = '.json_encode($firstname).';
                                document.getElementById("middle-name").value = '.json_encode($middlename).';
                                document.getElementById("first-name").value = '.json_encode($firstname).';
                                document.getElementById("student-id").value = '.json_encode($studentNo).';
                                document.getElementById("email").value = '.json_encode($email).';
                                document.getElementById("group").value = '.json_encode($groupStatus).';
                                document.getElementById("section").value = '.json_encode($sectionName).';
                                document.getElementById("specialization").value = '.json_encode($specialization).';
                                document.getElementById("course").value = '.json_encode($course).';
                                
                                document.getElementById("userLabel").innerText = '.json_encode($surname. ", " . $firstname . " " . $middlename).';
                        </script>
                    ';
                }
            }
        }
        
        catch(Exception $e){
             error_log($e->getMessage()); // Log the error for debugging
        }
    }
    
    function getStudentValues(){
          global $conn;
          
        try{
          
              $query = htmlspecialchars($_GET['query']);
              
              $studentSection = $_SESSION["sectionID"];
            
              $sql = "SELECT users.id, users.email, users.firstname, users.middlename, users.surname, students.studentNo, students.sectionID
                        FROM users
                        JOIN students ON students.id = users.id
                        WHERE 
                         (
                            students.studentNo LIKE ? 
                            OR users.id LIKE ? 
                            OR users.email LIKE ? 
                            OR users.surname LIKE ? 
                            OR users.firstname LIKE ?
                            OR CONCAT(users.firstname, ' ', users.surname) LIKE ?
                            OR CONCAT(users.firstname, ' ', users.surname, ' ', users.middlename) LIKE ?
                            OR CONCAT(users.surname, ', ', users.firstname, ' ', users.middlename) LIKE ?
                        )";
        
                $stmt = $conn->prepare($sql);
                $likeQuery = "%$query%";
                $stmt->execute([$likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery]);
                
                $resultCounter = 0;
            
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $studentID = htmlspecialchars($result["id"]);
                    
                    echo '<option value="'.$studentID.'">' 
                         . htmlspecialchars($result["surname"]) . ', ' . htmlspecialchars($result["firstname"]) . ' ' 
                         . htmlspecialchars($result["middlename"]) . ' - (' . htmlspecialchars($result["studentNo"]) . ')
                         </option>';
                         
                    $resultCounter++;
                }
                
                if($resultCounter <= 0){
                    echo '<option value="None">
                                Search value returned 0 students
                          </option>';
                }
        }
          
        catch(Exception $e){
             error_log($e->getMessage()); // Log the error for debugging
        }
            
    }
    
    
    
    
    function updateInfo($surname, $firstname, $middlename, $email, $userID){
        global $conn;
        
        try{
            $sql = "SELECT email FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID]);
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $curr_email = $user["email"];
            
            echo '
                <script>
                    console.log("user id '.$userID.'");
                </script>
            ';
            
            
            $conn->beginTransaction();
            
            if($curr_email == $email){
                
                $sql = "UPDATE users SET surname = ?, firstname = ?, middlename = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([ucwords($surname), ucwords($firstname), ucwords($middlename), $userID]);
                
                if($result){
                    $conn->commit();
                    
                    unset($_POST["update-btn"]);
                    
                    echo '<script>
                            Swal.fire({
                                title: "Success",
                                text: "Update User Information Succes!",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/accounts";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/accounts";
                                }
                            });
                          </script>';
                }
               
            
                else{
                    echo '<script>
                            Swal.fire({
                                title: "Email Unavailable",
                                text: "Email is already in use by another account",
                                icon: "error",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/accounts";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/accounts";
                                }
                            });
                          </script>';
                }
            }   
            
            
            else{
                $sql = "SELECT * FROM users WHERE email = ? AND id <> ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$email, $userID]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if(!$result){
                    
                    $sql = "UPDATE users SET surname = ?, firstname = ?, middlename = ?, email = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $result = $stmt->execute([ucwords($surname), ucwords($firstname), ucwords($middlename), $email, $userID]);
                    
                    if($result){
                        $conn->commit();
                    
                        unset($_POST["update-btn"]);
                    
                        echo '<script>
                                Swal.fire({
                                    title: "Success",
                                    text: "Update User Information Success!",
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/accounts";
                                    }
                                    
                                    else if (result.isDismissed) {
                                        window.location.href = "/accounts";
                                    }
                                });
                              </script>';
                    }
                    
                }
                
                else{
                    $conn->rollBack();
                    
                    echo '<script>
                            Swal.fire({
                                title: "Email Unavailable",
                                text: "Email is already in use by another account",
                                icon: "error",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/accounts";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/accounts";
                                }
                            });
                          </script>';
                }
            }
        }
        
        catch(Exception $e){
            $conn->rollBack();

            
            unset($_POST["udate-btn"]);
            
            echo '<script>
                    Swal.fire({
                        title: "Error",
                        text: "Error updating info: ' . addslashes($e->getMessage()) . '",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/accounts";
                        }
                        
                        else if (result.isDismissed) {
                            window.location.href = "/accounts";
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
        
        <link rel="stylesheet" href="pages/accounts.css">
        <link rel="stylesheet" href="pages/accounts_student.css">
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <title>Student Accounts</title>
    </head>
    
    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
        <?php getAccess(); ?>
        
        
        <div id="content2">
            <div class="profile-tabs">
                <div class="tabs">
                    <button class="active">Students</button>
                    <button onclick="window.location.href='/accounts_faculty';">Faculty</button>
                </div>
            </div>

       <!--<h2>Accounts</h2>
        <div class="accounts-header">-->
        
            <div class="student-access-group">
                <div class="student-section">
                    <label for="student">Student</label>
                    <div id="student" class="student-box">
                        <i class="fas fa-user-circle"></i>
                        <p id="userLabel"></p>
                    </div>
                </div>
            </div>
            
            <br>
            
           <div id="result" style="display: none;"></div> <!-- This div will be updated with the response but is hidden -->
            
           <form action="" method="POST">
                <div class="search-access-section">
                    <div>
                        <div>
                            <div>
                                <div>
                                    <label for="studentSearch">Search a Student using 
                                        <span style="color: #41A763;">Student No</span>,
                                        <span style="color: #41A763;">Name</span>, or
                                        <span style="color: #41A763;">Email</span>
                                    </label>
                                    <input type="text" id="studentSearch" placeholder="Enter Student Name or Email" onkeyup="filterStudents()" style="font-size: 20px;">
                                    
                                    <br><br>
                                    
                                    <select class="custom-dropdown" name="studentBox" id="studentResults" size="10" style="height: 150px; width: 180%;" required>
                                        <?php getStudentValues(); ?>
                                    </select>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
                
                <br><br>
                
                <div class="accounts-body">
                    <div class="left-section">
                        <div class="input-group">
                            <label for="last-name">Last Name</label>
                            <input type="text" id="last-name" name="surname" required>
                        </div>
                        <div class="input-group">
                            <label for="first-name">First Name</label>
                            <input type="text" id="first-name" name="firstname" required>
                        </div>
                        <div class="input-group">
                            <label for="student-id">Student ID</label>
                            <input type="text" id="student-id" name="studentID" disabled>
                        </div>
                        <div class="input-group">
                            <label for="group">Group</label>
                            <input type="text" id="group" value="" disabled>
                        </div>
                        <div class="input-group">
                            <label for="course">Course</label>
                            <input type="text" id="course" value="" disabled>
                        </div>
                    </div>
                    <div class="right-section">
                        <div class="input-group">
                            <label for="middle-name">Middle Name</label>
                            <input type="text" id="middle-name" name="middlename" required>
                        </div>
                        <div class="input-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="input-group">
                            <label for="section">Section</label>
                            <input type="text" id="section" value="" disabled>
                        </div>
                        <div class="input-group">
                            <label for="specialization">Specialization</label>
                            <input type="text" id="specialization" value="" disabled>
                        </div>
                    </div>
                </div>
                <div class="accounts-footer">
                    <div class="action-buttons">
                        <button type="submit" name="update-btn" class="btn-update">Update</button>
                        <button type="button" class="btn-cancel" onclick="window.location.href='/dashboard'">Cancel</button>
                    </div>
                </div>
            </form>

    <script>
    
        $(document).ready(function() {
            $('#studentResults').click(function() { //Change or Click depends on situation
                const selectedValue = $(this).val(); // Get the selected value
                
                // Send an AJAX GET request
                $.get('', { studentID: selectedValue }, function(response) {
                    $('#result').html(response); // Update the #result div with the response
                });
            });
        });
        
        function filterStudents() {
            const input = document.getElementById('studentSearch');
            const filter = input.value.toLowerCase();
            const select = document.getElementById('studentResults');
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
        
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(isset($_POST["update-btn"])){
                if(isset($_POST["studentBox"]) && isset($_POST["surname"]) && isset($_POST["firstname"]) && isset($_POST["middlename"]) && isset($_POST["email"])){
                    updateInfo($_POST["surname"], $_POST["firstname"], $_POST["middlename"], $_POST["email"], $_POST["studentBox"]);
                }
            }
        }
        
    ?>
    
    <?php require 'footer.php'; ?>
    </body>
</html>

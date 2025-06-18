<?php
    require "connection.php";
    session_start();
    
    
    // Check if a dropdown value has been sent via GET
    if (isset($_GET['facultyID'])) {
        
        // Sanitize the input to prevent XSS
        $userID = htmlspecialchars($_GET['facultyID']);
        
        $_SESSION["userToEdit"] = $userID;
        
        // Store the selected value in a session variable

        // Example response
        
        echo '
            <script>
                console.log("ID: ' . $userID . '");
            </script>
        ';
        
        displayUserInfo($userID);
        

        exit; // Important to exit after sending a response
    }
    
    
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
            error_log($e->getMessage()); // Log the error for debugging
        }
    }
    
    
    
    
    function transferAdminAccess($userID, $password, $retype_pass){
        global $conn;
        
        try{
            $conn->beginTransaction();
            
            $sql = "SELECT password FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                
                if($password == $retype_pass){
                
                    $stored_password = $result["password"];
                    
                    if(password_verify($password, $stored_password)){
                    
                        $sql = "UPDATE faculty SET accessLevel = ? WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([3, $userID]);
                        
                        if($result){
                            
                            $sql = "UPDATE faculty SET accessLevel = ? WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([1, $_SESSION["userID"]]);
                            
                            if($result){
                                $_SESSION["accessLevel"] = 1;
                                
                                $conn->commit();
                                    
                                unset($_POST["transfer-btn"]);
                            
                                echo '<script>
                                        Swal.fire({
                                            title: "Success",
                                            text: "Super Administrator Access Transfered!",
                                            icon: "success",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "/dashboard";
                                            }
                                            
                                            else if (result.isDismissed) {
                                                window.location.href = "/dashboard";
                                            }
                                        });
                                      </script>';
                            }
                            
                            else{
                                throw new Exception("Failed to transfer admin access");
                            }
                        }
                        
                        else{
                            throw new Exception("Failed to update selected faculty");
                        }
                    }
                    
                    else if(!password_verify($password, $stored_password)){
                        throw new Exception("Incorrect Password");
                    }
                }
                
                else{
                    throw new Exception("Password and Retype password does not match");
                }
            }
            
        }
        
        catch(Exception $e) {
            $conn->rollback();
                
            unset($_POST["transfer-btn"]);
                            
             echo '<script>
                    Swal.fire({
                        title: "Error",
                        text: "'.addslashes($e->getMessage()).'",
                        icon: "error",
                        confirmButtonText: "OK"
                    })
            </script>';
        }
    }
    
    
    
    function displayUserInfo($userID){
        global $conn;
        
        try{
            $sql = "SELECT * FROM users WHERE id = ? AND type = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userID, "faculty"]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($user){

                $surname = $user["surname"];
                $firstname = $user["firstname"];
                $middlename = $user["middlename"];
                $email = $user["email"];
             

                //Take note dont use domcontentloaded, since this is inside AJAX GET
                
                echo '
                    <script>
                            document.getElementById("last-name").value = '.json_encode($surname).';
                            document.getElementById("first-name").value = '.json_encode($firstname).';
                            document.getElementById("middle-name").value = '.json_encode($middlename).';
                            document.getElementById("email").value = '.json_encode($email).';
   
                            document.getElementById("userLabel").innerText = '.json_encode($surname. ", " . $firstname . " " . $middlename).';
                    </script>
                ';
            
            }
        }
        
        catch(Exception $e){
             error_log($e->getMessage()); // Log the error for debugging
        }
    }
    
    
    
    
    function getFacultyValues(){
        global $conn;
          
        try{
          
              $query = htmlspecialchars($_GET['query']);
              

              $sql = "SELECT users.id, users.email, users.firstname, users.middlename, users.surname
                        FROM users
                        JOIN faculty ON faculty.id = users.id
                        WHERE faculty.id <> ? AND faculty.id <> ?
                         AND(
                            users.id LIKE ? 
                            OR users.email LIKE ? 
                            OR users.surname LIKE ? 
                            OR users.firstname LIKE ?
                            OR CONCAT(users.firstname, ' ', users.surname) LIKE ?
                            OR CONCAT(users.firstname, ' ', users.surname, ' ', users.middlename) LIKE ?
                            OR CONCAT(users.surname, ', ', users.firstname, ' ', users.middlename) LIKE ?
                        )";
        
                $stmt = $conn->prepare($sql);
                $likeQuery = "%$query%";
                $stmt->execute([0, $_SESSION["userID"], $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery]);
                
                $resultCounter = 0;
            
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $facultyID = htmlspecialchars($result["id"]);
                    
                    echo '<option value="'.$facultyID.'">' 
                         . htmlspecialchars($result["surname"]) . ', ' . htmlspecialchars($result["firstname"]) . ' ' 
                         . htmlspecialchars($result["middlename"]) . '
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
                                    window.location.href = "/accounts_faculty";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/accounts_faculty";
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
                                    window.location.href = "/accounts_faculty";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/accounts_faculty";
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
                                        window.location.href = "/accounts_faculty";
                                    }
                                    
                                    else if (result.isDismissed) {
                                        window.location.href = "/accounts_faculty";
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
                                    window.location.href = "/accounts_faculty";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/accounts_faculty";
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
                            window.location.href = "/accounts_faculty";
                        }
                        
                        else if (result.isDismissed) {
                            window.location.href = "/accounts_faculty";
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
        <link rel="stylesheet" href="pages/accounts_faculty.css">
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        

        <title>Faculty Accounts</title>
    </head>
    
    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
        <?php getAccess(); ?>
        
         <div id="content2">
            <div class="profile-tabs">
                <div class="tabs">
                     <button onclick="window.location.href='/accounts';">Students</button>
                     <button class="active">Faculty</button>
                </div>
            </div>
            
            <div class="admin-access-group">
                <div class="admin-section">
                    <label for="admin">CICT Faculty</label>
                    <div id="admin" class="admin-box">
                        <i class="fas fa-user-circle"></i>
                        <p id="userLabel">Faculty</p>
                    </div>
                </div>

                <div class="access-group">
                    <label for="transfer-btn">Access</label>
                    
                    <!--<div class="access-controls">
                        <select id="access">
                            <option>Administrator</option>
                        </select>-->
                        
                        <button id="transfer-btn" class="btn-transfer" type="button" onclick="openModal()">
                            <i class="fa fa-exchange-alt"></i> Transfer Super Admin Access
                        </button>
                    </div>
                </div>
            </div>
            
        <div id="result" style="display: none;"></div> <!-- This div will be updated with the response but is hidden -->
             
      <form action="" method="POST">
        <div class="search-access-section">
            <div>
                <label for="facultySearch">Search Faculty using 
                    <span style="color: #41A763;">Name</span>, or
                    <span style="color: #41A763;">Email</span>
                </label>
                <input type="text" id="facultySearch" placeholder="Enter Faculty Name or Email" onkeyup="filterFaculty()" style="font-size: 20px;">
                <br><br>
                <select class="custom-dropdown" name="facultyBox" id="facultyResults" size="10" style="height: 150px; width: 210%;" required>
                    <?php getFacultyValues(); ?>
                </select>
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
            </div>
        </div>
    
        <div class="accounts-footer">
            <div class="action-buttons">
                 <button type="submit" name="update-btn" class="btn-update">Update</button>
                 <button type="button" class="btn-cancel" onclick="window.location.href='/dashboard'">Cancel</button>
            </div>
        </div>
    </form>
    
    <form action = "" method="POST">
        <!-- Modal for password confirmation -->
        <div id="transferModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Confirm Super Admin Access Transfer</h2>
                <p>Enter your account password below:</p>
                
                <div class="modal-input">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <input name="password" type="password" id="password" placeholder="Password" required>
                        <i class="fas fa-lock"></i> 
                    </div>
                </div>
                
                <div class="modal-input">
                    <label for="confirm-password">Re-type your password</label>
                    <div class="input-with-icon">
                        <input name="retype_pass" type="password" id="confirm-password" placeholder="Re-type your password" required>
                        <i class="fas fa-lock"></i> 
                    </div>
                </div>
                
                 <!-- Hidden input to store selected value from dropdown -->
                <input type="hidden" name="facultyBoxSelected" id="facultyBoxSelected"> 
                
                <div class="modal-actions">
                    <button type="submit" class="btn-transfer-confirm" name="transfer-btn">Transfer</button>
                    <button type="button" class="btn-cancel-modal" onclick="closeModal()">Cancel</button>
                </div>
            </div>
        </div>
    </form>
 
        
        
        <script>
            $(document).ready(function() {
                $('#facultyResults').click(function() { //Change or Click depends on situation
                    const selectedValue = $(this).val(); // Get the selected value
                    
                    // Send an AJAX GET request
                    $.get('', { facultyID: selectedValue }, function(response) {
                        $('#result').html(response); // Update the #result div with the response
                    });
                });
            });
            
            
            
            document.addEventListener('DOMContentLoaded', function() {
                // Get the select element
                const facultyResults = document.getElementById('facultyResults');
            
                // Add an event listener for the 'change' event
                facultyResults.addEventListener('change', function() {
                    // Get the selected value from the dropdown
                    const selectedValue = facultyResults.value;
            
                    // Set the hidden input field to the selected value
                    document.getElementById('facultyBoxSelected').value = selectedValue;
                });
            });
            
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
        
        <script>
            function openModal() {
                document.getElementById('transferModal').style.display = "block";
            }
    
            function closeModal() {
                document.getElementById('transferModal').style.display = "none";
            }

        </script>
        
    <?php
        
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(isset($_POST["update-btn"])){
                if(isset($_POST["facultyBox"]) && isset($_POST["surname"]) && isset($_POST["firstname"]) && isset($_POST["middlename"]) && isset($_POST["email"])){
                    updateInfo($_POST["surname"], $_POST["firstname"], $_POST["middlename"], $_POST["email"], $_POST["facultyBox"]);
                }
            }
            
            if(isset($_POST["transfer-btn"])){
                if(isset($_POST["facultyBoxSelected"]) && isset($_POST["password"]) && isset($_POST["retype_pass"])){
                    if(strlen($_POST["facultyBoxSelected"]) > 1){
                        
                        echo '
                            <script>
                                console.log("'.$_POST["facultyBoxSelected"].'");
                            </script>
                        ';
                        transferAdminAccess($_POST["facultyBoxSelected"], $_POST["password"], $_POST["retype_pass"] );
                    }
                    
                    else{
                        echo '<script>
                            Swal.fire({
                                title: "",
                                text: "No Faculty Selected",
                                icon: "info",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/accounts_faculty";
                                }
                                
                                else if (result.isDismissed) {
                                    window.location.href = "/accounts_faculty";
                                }
                            });
                          </script>';
                    }
                }
            }
        }
        
    ?>
        
    <?php require 'footer.php'; ?>
    </body>
</html>

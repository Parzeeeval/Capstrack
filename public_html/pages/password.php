<?php
    
    require "connection.php";
    session_start();
    
   function validatePassword($password) {
        // Regular expression to enforce the password policy
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]).{8,}$/', $password);
    }
    
    function updatePassword($curr_pass, $new_pass, $retype_pass){
        global $conn;
        
        try {
            $conn->beginTransaction();
                    
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"]]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user) {
                $stored_pass = $user["password"];
    
                if ($stored_pass && password_verify($curr_pass, $stored_pass)) {
                    if ($curr_pass != $new_pass) {
                        if ($new_pass == $retype_pass) {
                            
                            // Validate new password for the given standards
                            if (validatePassword($new_pass)) {
                                
                                $sql = "UPDATE users SET password = ? WHERE id = ?";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([password_hash($new_pass, PASSWORD_DEFAULT), $_SESSION["userID"]]);
    
                                if ($result) {
                                    
                                     $sql = "SELECT * FROM users WHERE id = ?";
                                     $stmt = $conn->prepare($sql);
                                     $stmt->execute([$_SESSION["userID"]]);
                                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                      
                                     $firstname = $result["firstname"];
                                     $surname = $result["surname"];
                                     $middlename = $result["middlename"];
                                            
                                     $action = "". $surname . ", " . $firstname . " " . $middlename . " changed their password";
                                     
                                     date_default_timezone_set('Asia/Manila');
                                     $date = date('Y-m-d H:i:s');
                    
                                     $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                                     $stmt = $conn->prepare($sql);
                                     $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                                    
                                     if(!$result){
                                         throw new Exception("Failed to insert action logs");  
                                     }
                         
                                    $conn->commit();
                                    
                                    unset($_POST["save-button"]);
                                    
                                    echo '<script>
                                        Swal.fire({
                                            title: "Success!",
                                            text: "Successfully Changed Password!",
                                            icon: "success",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "/password";
                                            } else if (result.isDismissed) {
                                                window.location.href = "/password";
                                            }
                                        });
                                    </script>';
                                } 
                                
                                else {
                                    throw new Exception("Error in changing password");
                                }
                            } 
                            
                            else {
                                throw new Exception("Password must be at least 8 characters long, contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.");
                            }
                        } 
                        
                        else {
                            throw new Exception("New password and re-type password do not match");
                        }
                    } 
                    
                    else {
                        throw new Exception("New password cannot be the same as current password");
                    }
                } 
                
                else {
                    throw new Exception("Current Password Incorrect");
                }
            }
        } 
        
        catch (Exception $e) {
            $conn->rollBack();
                    
            unset($_POST["save-button"]);
            
            echo '<script>
                Swal.fire({
                    title: "Error Changing Password",
                    text: "'.addslashes($e->getMessage()).'",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "/password";
                    } else if (result.isDismissed) {
                        window.location.href = "/password";
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
        <link rel="stylesheet" href="pages/password.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <script src="pages/session_tracker.js"></script>
        
        <title>Change Passwod</title>
    </head>
    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
        
            <div id="content2">
                <div class="profile-tabs">
                    <div class="tabs">
                        <button onclick="window.location.href='/profile';"> Profile</button>
                        <button class="active">Password</button>
                    </div>
                </div>
                <form class="profile-form" action="" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="current-password">Current Password</label>
                            <input type="password" id="current-password" placeholder="Current Password" name="curr_pass" required>
                        </div>
                        <div class="form-group">
                            <label for="new-password">New Password</label>
                            <input type="password" id="new-password" placeholder="New Password" name="new_pass" required>
                        </div>
                        <div class="form-group">
                            <label for="reenter-password">Re-type Password</label>
                            <input type="password" id="reenter-password" placeholder="Re-type New Password" name="retype_pass" required>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <button type="submit" class="save-btn" name="save-button">Save</button>
                    </div>
                </form>
            </div>
        </div>
        
    <?php require 'footer.php'; ?>
    
    <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if(isset($_POST["save-button"])){
                    if(isset($_POST["curr_pass"]) && isset($_POST["new_pass"]) && isset($_POST["retype_pass"])){
                        updatePassword($_POST["curr_pass"], $_POST["new_pass"], $_POST["retype_pass"]);
                    }
                }
          }
    ?>
    </body>
</html>

<!DOCTYPE html>
<html>

    <head>
        <link rel="stylesheet" href="pages/activate_account.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="pages/session_tracker.js"></script>
        
        <title>Activate Account</title>
    </head>
    
    <body>

        <div class="container">
            
            <div class="activation_form">
                  <div class="wrapper">
            <div class="logo">
                <img src="pages/images/Logo.png" alt="">
                <img style="width: 75px; height: 75px;" src="pages/images/CapstrackLogo.png" alt="">
                
            </div>
                <form action="" id="activation_form" name="activation_form" method="POST">
                    <h2>Activate Account</h2>
    
                    <div class="form-field">
                        <input type="password" name="password1" placeholder="Generated Password" required>
                    </div>
    
                    <div class="form-field">
                        <input type="password" name="password2" placeholder="New Password..." required>
                    </div>
    
                    <div class="form-field">
                        <input type="password" name="password3" placeholder="Re-Type New Password..." required>
                    </div>
    
                    <input type="submit" value="Activate" name="activate-btn" class="btn">
                    <div id="messenger"></div>
                </form>
            </div></div>
             <div class="powered">
            <a>Powered by: CapsTrack</a>
        </div>
        </div>
    
        <?php
            require 'connection.php';
            
            $id = "";
            $token = "";
            
            function validatePassword($password) {
                // Regular expression to enforce the password policy
                return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]).{8,}$/', $password);
            }
    
            if (isset($_GET["id"]) && isset($_GET["token"])) {
                $id = $_GET["id"];
                $token = $_GET["token"];
                
                if (!empty($id) && !empty($token)) {
                    $sql = "SELECT status FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$id]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($user){
                        if ($user["status"] == "active" || $user["status"] == "inactive") {
                             $errorMessage = "User Already Activated";
                             header("Location: 404.php?error=" . urlencode($errorMessage));
                             exit();
                        } 
                        
                        else if ($user["status"] == "pending") {
                            $sql = "SELECT token, activated FROM creation_tokens WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$id]);
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
                            if ($user["token"] != $token || $user["activated"] != "false") {
                                $errorMessage = "Token is invalid or already used";
                                header("Location: 404.php?error=" . urlencode($errorMessage));
                                exit();
                            }
                        }
    
                        else{
                             $errorMessage = "Something went wrong with activation";
                             header("Location: 404.php?error=" . urlencode($errorMessage));
                             exit();
                        }
                    }
                    
                    else{
                        $errorMessage = "User not found.";
                        header("Location: 404.php?error=" . urlencode($errorMessage));
                        exit();
                    }


                } 
                
                else if (empty($id) || empty($token)) {
                    header("Location: /");
                    exit();
                }
            }
    
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $curr_pass = $_POST["password1"];
                $new_pass = $_POST["password2"];
                $retype_pass = $_POST["password3"];
                
                try {
                    $conn->beginTransaction();
                    
                    $sql = "SELECT * FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$id]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
                    if($user) {
                        $generated_pass = $user["password"];
    
                        if ($generated_pass && password_verify($curr_pass, $generated_pass)) { //check if a generated password exists on that user, and then verifies user inputted password
                            if ($curr_pass != $new_pass) {
                                
                                if ($new_pass == $retype_pass) {
                                    
                                    if (validatePassword($new_pass)) {
                                    
                                        $sql = "UPDATE users SET password = ?, status = ? WHERE id = ?";
                                        $stmt = $conn->prepare($sql);
                                        $result1 = $stmt->execute([password_hash($new_pass, PASSWORD_DEFAULT), "active", $id]);
        
                                        $sql = "UPDATE creation_tokens SET activated = ? WHERE id = ?";
                                        $stmt = $conn->prepare($sql);
                                        $result2 = $stmt->execute(["true", $id]);
                                        
                                        if ($result1 && $result2) {
                                            $conn->commit();
                                            
                                            echo '<script>
                                                Swal.fire({
                                                    title: "Success!",
                                                    text: "Successfully Changed Password!",
                                                    icon: "success",
                                                    confirmButtonText: "Login"
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location.href = "/login";
                                                    }
                                                    
                                                    else if (result.isDismissed) {
                                                        window.location.href = "/login";
                                                    }
                                                });
                                            </script>';
                                            
                                            unset($_POST["activate-btn"]);
                                        } 
                                        
                                        else {
                                            throw new Exception("Error in activating account");
                                        }
                                    }
                                    
                                    else{
                                        throw new Exception("Password must be at least 8 characters long, contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.");
                                    }
                                } 
                                
                                else {
                                    throw new Exception("New password and re-type password does not match");
                                }
                            } 
                            
                            else {
                                throw new Exception("New password cannot be the same as generated password");
                            }
                        } 
                        
                        else {
                           throw new Exception("Generated Password Incorrect");
                        }
                    }
                } 
                
                catch (Exception $e) {
                    $conn->rollBack();
                    
                    unset($_POST["activate-btn"]);
                    
                    echo '<script>
                        var id = '.json_encode($id).';
                        var token = '.json_encode($token).';
                        
                        Swal.fire({
                            title: "Error",
                            text: "'.addslashes($e->getMessage()).'",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "/activate?id=" + id + "&token=" + token;
                            }
                            
                            else if (result.isDismissed) {
                                window.location.href = "/activate?id=" + id + "&token=" + token;
                            }
                        });
                   </script>';

                }
            }
        ?>
    </body>

</html>

<!DOCTYPE html>
<html>

    <head>
        <link rel="stylesheet" href="pages/activate_account.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="pages/session_tracker.js"></script>
        
        <title>Forgot Password</title>
    </head>
    
    <body>
        <div class="container">
            <div class="activation_form">
                <form action="" id="activation_form" name="activation_form" method="POST">
                    <h2>Forgot Password</h2>
                    
                    <div class="form-field">
                        <input type="password" name="confirm_code" placeholder="Confirmation Code" required>
                    </div>

                    <div class="form-field">
                        <input type="password" name="new_pass" placeholder="New Password..." required>
                    </div>
    
                    <div class="form-field">
                        <input type="password" name="retype_pass" placeholder="Re-Type New Password..." required>
                    </div>
    
                    <button type="submit" value="Activate" name="reset-btn" class="btn">Reset</button>
                    <div id="messenger"></div>
                </form>
            </div>
        </div>
    
        <?php
            require 'connection.php';
            
            $id = "";
            $code = "";
            $token = "";
            
            function validatePassword($password) {
                // Regular expression to enforce the password policy
                return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]).{8,}$/', $password);
            }
    
            if (isset($_GET["id"], $_GET["code"], $_GET["token"])) {
    
                $id = $_GET["id"];
                $code = $_GET["code"];
                $token = $_GET["token"];
                
                if (!empty($id) && !empty($code) && !empty($token)) {
                    
                    $sql = "SELECT status FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$id]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($user) {
                        
                        if ($user["status"] == "inactive" || $user["status"] == "pending") {
                           // Set a custom error message if desired
                            $errorMessage = "Your account is inactive or pending. Please contact support.";
                            header("Location: 404.php?error=" . urlencode($errorMessage));
                            exit();
                        } 
                        
                        else if ($user["status"] == "active") {
                            $sql = "SELECT * FROM forgotpass_tokens WHERE token = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$token]);
                            $verify = $stmt->fetch(PDO::FETCH_ASSOC);
            
                            if ($verify) {
                                if ($verify["activated"] == "false") {
                                    
                                    if ($verify["confirm_code"] != $code) {
                                        $errorMessage = "Confirmation Code invalid.";
                                        header("Location: 404.php?error=" . urlencode($errorMessage));
                                        exit();
                                    }
                                    
                                    if ($verify["token"] != $token) {
                                        $errorMessage = "Token invalid.";
                                        header("Location: 404.php?error=" . urlencode($errorMessage));
                                        exit();
                                    }
                                } 
                                
                                else if ($verify["activated"] == "true") {
                                     $errorMessage = "Token is invalid/activated.";
                                     header("Location: 404.php?error=" . urlencode($errorMessage));
                                     exit();
                                }
                            }
                            
                            else{
                                
                                if ($verify["token"] != $token) {
                                    $errorMessage = "Token invalid.";
                                    header("Location: 404.php?error=" . urlencode($errorMessage));
                                    exit();
                                }
                            }
                        }
                    } 
                    
                    else {
                         $errorMessage = "User not found.";
                         header("Location: 404.php?error=" . urlencode($errorMessage));
                         exit();
                    }
                } 
                
                else {
                    $errorMessage = "User ID/ Token / Confirmation Code is invalid";
                    header("Location: 404.php?error=" . urlencode($errorMessage));
                    exit();
                }

            }
    
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $confirm_code = $_POST["confirm_code"];
                $new_pass = $_POST["new_pass"];
                $retype_pass = $_POST["retype_pass"];
                
                try {
                    $conn->beginTransaction();
                    
                    $sql = "SELECT * FROM forgotpass_tokens WHERE token = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$token]);
                    $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
    
                    if($tokenData) {
                        
                        $generated_code = $tokenData["confirm_code"];
    
                        if ($generated_code == $confirm_code) { 

                                if ($new_pass == $retype_pass) {
                                    
                                    if (validatePassword($new_pass)) {
                                    
                                        $sql = "UPDATE users SET password = ? WHERE id = ?";
                                        $stmt = $conn->prepare($sql);
                                        $result1 = $stmt->execute([password_hash($new_pass, PASSWORD_DEFAULT), $id]);
        
                                        $sql = "UPDATE forgotpass_tokens SET activated = ? WHERE token = ?";
                                        $stmt = $conn->prepare($sql);
                                        $result2 = $stmt->execute(["true", $token]);
                                        
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
                                            
                                            unset($_POST["reset-btn"]);
                                        } 
                                        
                                        else {
                                            throw new Exception("Error in reseting password");
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
                           throw new Exception("Confirmation Code Incorrect");
                        }
                    }
                } 
                
                catch (Exception $e) {
                    $conn->rollBack();
                    
                    unset($_POST["reset-btn"]);
                    
                    echo '<script>
                        var id = '.json_encode($id).';
                        var code = '.json_encode($code).';
                        var token = '.json_encode($token).';
                        
                        Swal.fire({
                            title: "Error",
                            text: "'.addslashes($e->getMessage()).'",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "/forgot_pass?id=" + id + "&code=" + code + "&token=" + token;
                            }
                            
                            else if (result.isDismissed) {
                                window.location.href = "/forgot_pass?id=" + id + "&code=" + code + "&token=" + token;
                            }
                        });
                   </script>';

                }
            }
        ?>
    </body>

</html>

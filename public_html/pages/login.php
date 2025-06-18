<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="pages/login.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <!-- FontAwesome for icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
        <title>Capstrack Login</title>
    </head>

    <body>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        
        <div class="wrapper">
            <div class="logo">
                <img src="pages/images/Logo.png" alt="">
                <img style="width: 75px; height: 75px;" src="pages/images/CapstrackLogo.png" alt="">
                
            </div>
            <div class="login-label-top">
 <h2>Login Account</h2>
 </div>
            <br>
            
            <form class="p-3 mt-3" method="POST">
                
                <div class="form-field d-flex align-items-center">
                   
                    <input type="text" name="email" placeholder="Email" autocomplete="username" required>
                </div>
                <div class="form-field d-flex align-items-center">
                    
                    <input type="password" name="password" id="password" placeholder="Password" autocomplete="current-password" required>
                    <!-- Show/Hide password toggle -->
                    <span class="toggle-password" onclick="togglePasswordVisibility()">
                        <i class="fas fa-eye" id="password-icon"></i>
                    </span>
                </div>
                
                <button class="btn mt-3" type="submit" value="Login" name="login-btn" style="text-align: center;">Login</button>

                <!-- New container for Remember me and Forgot Password -->
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember_me" name="remember_me" <?php echo isset($_COOKIE['remember_me']) ? 'checked' : ''; ?>> 
                        <label for="remember_me">Remember me</label>
                    </div>
                    <a href="/forgot_pass_email">Forgot Password?</a>
                </div>
            </form>
            
        </div>
        <div class="powered">
            <a>Powered by: CapsTrack</a>
        </div>

        <!-- JavaScript for Show/Hide Password -->
        <script>
            function togglePasswordVisibility() {
                const passwordField = document.getElementById('password');
                const passwordIcon = document.getElementById('password-icon');
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    passwordIcon.classList.remove('fa-eye');
                    passwordIcon.classList.add('fa-eye-slash');
                } else {
                    passwordField.type = 'password';
                    passwordIcon.classList.remove('fa-eye-slash');
                    passwordIcon.classList.add('fa-eye');
                }
            }
        </script>
    </body>
</html>


<?php 
    require 'connection.php';
    session_start();
    
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        
        if(isset($_POST["login-btn"])){
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            $conn->beginTransaction();
    
            try{
                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($user) {  
                    $status = $user["status"];
                    $sessionState = $user["session"];
                    
                    if($status == "active"){
                        
                
                        $stored_password = $user["password"];
                        
                        if ($stored_password && password_verify($password, $stored_password)) {
                            $_SESSION["userID"] = $id = $user["id"];
                            $_SESSION["accountType"] = $acountType = $user["type"];
                            
                            if($_SESSION["accountType"] == "faculty"){
                                $sql = "SELECT accessLevel FROM faculty WHERE id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$_SESSION["userID"]]);
                                $accessResult = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                $_SESSION["accessLevel"] = $accessResult["accessLevel"];
                            }
    
                            if(isset($_POST["remember_me"])){
                                $token = bin2hex(random_bytes(16)); // Generate a unique token
    
                                $sql = "SELECT token FROM remember_tokens WHERE token = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$token]);
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
                                if(!$result){
                                    $sql = "INSERT into remember_tokens (token, id, created_at) VALUES (?, ?, ?)";
                                    $stmt = $conn->prepare($sql);
                                    $result = $stmt->execute([$token, $id, date("Y-m-d")]);
    
                                    if($result){
                                       setcookie("remember_me", $token, time() + (86400 * 30), "/"); 
                                    }
                                    
                                }
                            }
                            
                            $sql = "UPDATE users SET session = ? WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute(["online", $id]);
                            
                            if($result){
                                
                                $sessionID = session_id();
    
                                // Insert the new session into user_sessions table
                                $sql = "INSERT INTO user_sessions (userID, session_id, last_active, device_info) VALUES (?, ?, NOW(), ?)";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$id, $sessionID, $_SERVER['HTTP_USER_AGENT']]); 
                                
                                $conn->commit();
                                
                                unset($_POST["login-btn"]);
                                
                                echo '<script>
                                    Swal.fire({
                                        title: "Success!",
                                        text: "Succesfully Logged in!",
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
                                throw new exception("Error logging in");
                            }
                        } 
                        
                        else if (!$stored_password || !password_verify($password, $stored_password)) {
                            throw new Exception("Error: Incorrect Password");
                        }
                    }
                    
                    else if($status == "pending"){
                        throw new Exception("Error: Account Activation Required");
                    }
                    
                    else if($status == "inactive"){
                        throw new Exception("Error: Account Inactive");
                    }
                    
                    
                }
    
                else{
                    throw new Exception("Error: User does not exist");
                }
    
            }
    
            catch(Exception $e){
                $conn->rollback();
                
                unset($_POST["login-btn"]);
                                
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
    }
?>

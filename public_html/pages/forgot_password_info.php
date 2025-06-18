<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="pages/login.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <!-- FontAwesome for icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
        
        <script src="pages/session_tracker.js"></script>
        <title>Forgot Password</title>
        
    </head>

    <body>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        
        <div class="wrapper">
            <div class="logo">
                <img src="pages/images/Logo.png" alt="">
                <img style="width: 75px; height: 75px;" src="pages/images/CapstrackLogo.png" alt="">
            </div>

            <br>
            
            <form class="p-3 mt-3" action="" method="POST">
                <div class="form-field d-flex align-items-center">
                   
                    <input type="text" name="email" placeholder="Email" autocomplete="username" required>
                </div>
               
                <button class="btn mt-3" type="submit" name="forgot-btn" style="text-align: center;">Reset</button>

                <div class="form-options2">
                    <a href="/login">Back to login</a>
                </div>
            </form>
            
        </div>
        <div class="powered">
            <a>Powered by: CapsTrack</a>
        </div>
    
    <?php
    
        //ini_set('display_errors', 1); // Display errors on the page (only for development)
        //ini_set('display_startup_errors', 1); 
        //error_reporting(E_ALL); // Report all types of errors
        
        // Import PHPMailer classes into the global namespace
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;
        use PHPMailer\PHPMailer\Exception;

        // Load Composer's autoloader
        require 'vendor/autoload.php';
        
        require 'connection.php';
        session_start();
        
        
        function generateConfirmCode($length = 8) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $gen_code = '';
    
            for ($i = 0; $i < $length; $i++) {
                $randomIndex = random_int(0, $charactersLength - 1);
                $gen_code .= $characters[$randomIndex];
            }
    
            return $gen_code;
        }
            
    
       function generateToken($length = 16) {
            // Get the current date in the format YYYYMMDD
            $currentDate = date('Ymd');
            
            // Generate a random token
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $gen_token = '';
        
            for ($i = 0; $i < $length; $i++) {
                $randomIndex = random_int(0, $charactersLength - 1);
                $gen_token .= $characters[$randomIndex];
            }
        
            // Prepend the date to the generated token
            return $currentDate . $gen_token;
        }
        
        function sendForgetPass($email){
            global $conn;
            
            try{
   
                $sql = "SELECT email FROM users WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$email]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if ($result) {
                    
                    $sql = "SELECT * FROM users WHERE email = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$email]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result){
                        
                        $mail = new PHPMailer(true);
                        
                        $userID = $result["id"];
                        $surname = $result["surname"];
                        $firstname = $result["firstname"];
                        
                        $generated_token = generateToken();
                        $generated_code = generateConfirmCode();
                        
                        $current_date = date("Y-m-d");
                        
                        $conn->beginTransaction();
                        
                        $sql = "INSERT INTO forgotpass_tokens (token, confirm_code, created_at, userID, activated) VALUES(?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $insertToken = $stmt->execute([$generated_token, $generated_code, $current_date, $userID, "false"]);
                        
                        if($insertToken){
                            if (file_exists('pages/email_forgotpass.php')) {
                                    require 'email_forgotpass.php';
                            } 
                            
                            else {
                                throw new Exception("Email forgot script not found.");
                            }
                        }
                    }
                }
                
                else{
                    echo "<script>
                        Swal.fire({
                            title: 'Forgot Password Request',
                            text: 'Email does not exist',
                            icon: 'info',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                 window.location.href = '/forgot_pass_email';
                            }
                            
                            else if (result.isDismissed) {
                                 window.location.href = '/forgot_pass_email';
                            }
                        });
                    </script>";
                }
            }
            
            catch (Exception $e) {
                $conn->rollBack();
                
                unset($_POST["forgot-btn"]);
                
                echo '<script>
    
                    Swal.fire({
                        title: "Error",
                        text: "'.addslashes($e->getMessage()).'",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/forgot_pass_email";
                        }
                        
                        else if (result.isDismissed) {
                            window.location.href = "/forgot_pass_email";
                        }
                    });
               </script>';
             }
        }
        
            
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST["forgot-btn"])){
                if(isset($_POST["email"])){
                    sendForgetPass($_POST["email"]);
                }
            }
        }
    ?>
    
    </body>
</html>

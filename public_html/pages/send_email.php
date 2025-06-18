<html>
    <head>
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <title>Account Email Invitations</title>
        <script src="pages/session_tracker.js"></script>
    </head>
    
    <body>
 
        <?php
        ini_set('display_errors', 1); // Display errors on the page (only for development)
        ini_set('display_startup_errors', 1); 
        error_reporting(E_ALL); // Report all types of errors

        require_once 'connection.php';
        require "header.php";
        require "menu.php";

        // Import PHPMailer classes into the global namespace
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;
        use PHPMailer\PHPMailer\Exception;

        // Load Composer's autoloader
        require 'vendor/autoload.php';

        function generatePassword($length = 8) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $gen_pass = '';

            for ($i = 0; $i < $length; $i++) {
                $randomIndex = random_int(0, $charactersLength - 1);
                $gen_pass .= $characters[$randomIndex];
            }

            return $gen_pass;
        }

        function generateToken($length = 16) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $gen_token = '';

            for ($i = 0; $i < $length; $i++) {
                $randomIndex = random_int(0, $charactersLength - 1);
                $gen_token .= $characters[$randomIndex];
            }

            return $gen_token;
        }

        function generateID(){
            global $conn; //Always use the keyword global to get the value of conn variable for sql connection
            
            $gen_id = "";

            try{
                $curr_year = date("Y");
                $curr_month = date("m");   

                $query = "SELECT * FROM sequence_tracker";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);


                if($result){
                    if($curr_year == $result["current_year"]){
                        $next_sequence = $result["last_sequence"];

                        $next_sequence = $next_sequence + 1;

                        //echo "$curr_year" . "$next_sequence";

                        $gen_id = $curr_year . $next_sequence;
                    }

                    else if($curr_year != $result["current_year"]){
                        $query = "UPDATE sequence_tracker SET current_year = ?, last_sequence = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->execute([$curr_year, 1000]);

                        $query = "SELECT * FROM sequence_tracker";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);

                        $next_sequence = $result["last_sequence"];

                        $next_sequence = $next_sequence + 1;

                        $gen_id = $curr_year . $next_sequence;
                    }
                }

            } 

            catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

            return $gen_id;
        }

        function getLastSequence(){
            global $conn; //Always use the keyword global to get the value of conn variable for sql connection
            
            $last_sequence = 0;

            try{
                $query = "SELECT last_sequence FROM sequence_tracker";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if($result){
                    $last_sequence = $result["last_sequence"] + 1;
                }
            }

            catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

            return $last_sequence;
        }


        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(isset($_POST['userInfo'])) { //The POST with userInfo is data sent from the previous php file (upload.php)
                $userInfoJson = $_POST['userInfo'];
                $userInfo = json_decode($userInfoJson, true);

                $accountType = isset($_POST['account_type']) ? $_POST['account_type'] : '';
                $sectionID = isset($_POST['section_value']) ? $_POST['section_value'] : '';
                
                 // Find the position of the space character
                //$spacePos = strpos($section, ' ');

                // Extract the substring after the space
                //$substring = substr($section, $spacePos + 1);

                //$yearLvl = intval($substring[0]);

 
                //put back database info here if global doesnt work


                // Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);

            
                for ($r =0; $r < count($userInfo); $r++) {
                    try {
                        $sql = "SELECT email FROM users WHERE email=?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$userInfo[$r][0]]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                        if ($result) {
                            echo "The email: " . $userInfo[$r][0] . " is already in use by another user, hence an email is not sent<br>";
                        } 
                        
                        else {
                            $generated_password = generatePassword();
                            $generated_id = generateID();
                            $generated_token = generateToken();
                
                            $conn->beginTransaction(); // Start transaction
                            
                            // Insert to users table
                            $sql = "INSERT INTO users (id, email, password, firstname, middlename, surname, status, session, type, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$generated_id, $userInfo[$r][0], password_hash($generated_password, PASSWORD_DEFAULT), $userInfo[$r][1], $userInfo[$r][2], $userInfo[$r][3], "pending", "offline", $accountType, date("Y-m-d")]);
                            

                            if ($accountType == "student") {
                                //Insert STUDENT
                                $sql = "INSERT INTO students (id, projectID, studentNo, sectionID) VALUES (?, ?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$generated_id, "", $userInfo[$r][4], $sectionID]);
                                
                                if (!$result) {
                                    throw new Exception("Failed to insert user data of " . $userInfo[$r][0]);
                                } 
                            }
                            
                            else if($accountType == "faculty"){
                                //Insert FACULTY
                                $sql = "INSERT INTO faculty (id, accessLevel, category) VALUES (?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$generated_id, $userInfo[$r][4], $userInfo[$r][5]]);

                                if (!$result) {
                                    throw new Exception("Failed to insert user data of " . $userInfo[$r][0] . "");
                                }
                            }

                            if (!$result) {
                                throw new Exception("Failed to insert user data of " . $userInfo[$r][0] . "");
                            }
                
                            // Insert creation token
                            $current_date = date("Y-m-d");
                            $sql = "INSERT INTO creation_tokens (id, token, created_at, activated) VALUES (?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $result2 = $stmt->execute([$generated_id, $generated_token, $current_date, "false"]);
                
                            if (!$result2) {
                                throw new Exception("Failed to insert creation token.");
                            }
                
                            // Send the activation email
                            // Mail setup
                            require 'email_activation.php';
                        }
                    } 
                    
                    catch (Exception $e) {
                        // Roll back the transaction in case of any failure
                        $conn->rollBack();
                        echo '<script>';
                        echo 'console.log(' . json_encode("Error: " . $e->getMessage()) . ');';
                        echo '</script>';
                    }
                }
            }
        }
        
        require "footer.php";
        ?>        
    
   

    </body>
</html>
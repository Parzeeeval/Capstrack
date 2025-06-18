<?php
        ini_set('display_errors', 1); // Display errors on the page (only for development)
        ini_set('display_startup_errors', 1); 
        error_reporting(E_ALL); // Report all types of errors
        
         // Import PHPMailer classes into the global namespace
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;
        use PHPMailer\PHPMailer\Exception;

        // Load Composer's autoloader
        require 'vendor/autoload.php';


        require 'connection.php';
        session_start();
        
        
       
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
                                 window.location.href = "/upload";
                            }
                            
                            else if (result.isDismissed) {
                                 window.location.href = "/upload";
                            }
                        });
                   </script>';
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
                                 window.location.href = "/upload";
                            }
                            
                            else if (result.isDismissed) {
                                 window.location.href = "/upload";
                            }
                        });
                   </script>';
            }

            return $last_sequence;
        }
        
        
        function sendEmail(){
            global $conn;
            
            try{
                $userInfoJson = $_POST['userInfo'];
                $userInfo = json_decode($userInfoJson, true);

                
                 // Find the position of the space character
                //$spacePos = strpos($section, ' ');
    
                // Extract the substring after the space
                //$substring = substr($section, $spacePos + 1);
    
                //$yearLvl = intval($substring[0]);
    
    
                //put back database info here if global doesnt work
    
    
                // Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);
                
                $messages = [];
            
                for ($r =0; $r < count($userInfo); $r++) {
                    
                    $sql = "SELECT email FROM users WHERE email=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$userInfo[$r][0]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
                    if ($result) {
                        $messages[] = "<li><strong>Email:</strong> <span style=\"color: red; font-weight: bold;\">" . htmlspecialchars($userInfo[$r][0]) . "</span> <em>is already in use by another user, hence an email is not sent.</em></li>";
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
                        

          
                        //Insert FACULTY
                        $sql = "INSERT INTO faculty (id, accessLevel, category) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$generated_id, $userInfo[$r][4], $userInfo[$r][5]]);
                        
                        if($result){
                            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $yearResult = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $acadYear = $yearResult["id"];
                            
                            
                            $sql = "INSERT INTO faculty_count (facultyID, panelist_count, panelist_limit, adviser_count, adviser_limit, coordinator_count, coordinator_limit, academicYearID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$generated_id, 0, 5, 0, 5, 0, 5, $acadYear]);
                            
                            if(!$result){
                                throw new Exception("Failed to insert faculty count of " . $userInfo[$r][0] . "");
                            }
                            
                        }
                        
                        else{
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
                        if (file_exists('pages/email_activation.php')) {
                            require 'email_activation.php';
                        } 
                        
                        else {
                            throw new Exception("Email activation script not found.");
                        }
                    }
                }
                
                 // After the loop, check if there are any messages to display
                if (!empty($messages)) {

                    // Convert messages to a single string
                     $messageString = "<ul style=\"list-style-type: disc; padding-left: 20px; line-height: 1.5;\">" . implode('', $messages) . "</ul>";
                    
                    // Use SweetAlert to display the messages
                    echo "<script>
                        Swal.fire({
                            title: 'Email Registration Status',
                            html: '$messageString',
                            icon: 'info',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                 window.location.href = '/upload';
                            }
                            
                            else if (result.isDismissed) {
                                 window.location.href = '/upload';
                            }
                        });
                    </script>";
                }
                
                else{
                    // Convert messages to a single string
                     $messageString = "<ul style=\"list-style-type: disc; padding-left: 20px; line-height: 1.5;\">" . implode('', $messages) . "</ul>";
                    
                    // Use SweetAlert to display the messages
                    echo "<script>
                        Swal.fire({
                            title: 'Email Registration Status',
                            html: '$messageString',
                            icon: 'info',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                 window.location.href = '/upload';
                            }
                            
                            else if (result.isDismissed) {
                                 window.location.href = '/upload';
                            }
                        });
                    </script>";
                }
            }
                
            catch (Exception $e) {
                // Roll back the transaction in case of any failure
                $conn->rollBack();
                echo '<script>';
                echo 'console.log(' . json_encode("Error: " . $e->getMessage()) . ');';
                echo '</script>';
                
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
                                 window.location.href = "/upload";
                            }
                            
                            else if (result.isDismissed) {
                                 window.location.href = "/upload";
                            }
                        });
                   </script>';
            }
        }

    ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Account Email Invitations</title>
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="pages/upload.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="pages/session_tracker.js"></script>

        <!-- Include xlsx library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    </head>

    <body>
        <?php require 'header.php'; ?> <!-- Topbar -->
        <?php require 'menu.php'; ?> <!-- Menu -->

                <div> <!-- Removed white box container -->
                    <h2 class="invite-header">Invite Capstrack Accounts</h2>
                
                        <div class="invite-radio-buttons">
                            <label>
                                <input class="invite-input-radio-faculty" type="radio" name="account_type" value="faculty" onclick="toggleInput('hide')" checked>
                                <span class="custom-bullet faculty"></span> Faculty
                            </label>
                </div>

              
             <div class="upload-box" id="upload-box">
                    <input type="file" id="file" accept=".xlsx, .csv" class="invite-file-input" required>
                    <label for="file" class="upload-label">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="0 0 24 24" width="40" fill="#2c3e50">
                                <path d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 20h14v-2H5v2zm7-18l-7 7h4v6h6v-6h4l-7-7z"/>
                            </svg>
                        </div>
                        <p>Drag .xlsx or .csv file</p>
                    </label>
                    <p class="file-name" id="file-name">No file chosen</p>
                </div>
                
                    <br>
                    
                    <button type="button" onclick="handleFile()" class="upload-button">Upload</button>
                </div>

            <br>

            <div id="tableContainer" class="invite-table-container">
                <table id="excelTable" class="invite-table">
                    <thead>
                        <!-- Headers will be dynamically inserted here -->
                    </thead>
                    <tbody>
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
            </div>

            <form id="userInfoForm" action="" method="POST">
                <input type="hidden" id="userInfoValues" name="userInfo" required>

                <button type="button" onclick="extractTableData()" class="invite-button">Send Invite</button>
            </form>
        </div>

        <script>

            function handleFile() {
                const fileInput = document.getElementById('file');
                const file = fileInput.files[0];

                if (file && file.name.endsWith('.xlsx')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, { type: 'array' });
                        const firstSheetName = workbook.SheetNames[0];
                        const worksheet = workbook.Sheets[firstSheetName];

                        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                        displayTable(jsonData);
                    };
                    reader.readAsArrayBuffer(file);
                } 
                
                else {
                    alert('Please upload a valid .xlsx file.');
                }
            }

            function displayTable(data) {
                const tableHead = document.getElementById('excelTable').querySelector('thead');
                const tableBody = document.getElementById('excelTable').querySelector('tbody');
                tableHead.innerHTML = ''; // Clear existing headers
                tableBody.innerHTML = ''; // Clear existing body

                // Populate the header
                const headerRow = document.createElement('tr');
                data[0].forEach(cell => {
                    const th = document.createElement('th');
                    th.textContent = cell;
                    headerRow.appendChild(th);
                });
                tableHead.appendChild(headerRow);

                // Populate the body
                for (let i = 1; i < data.length; i++) {
                    const row = document.createElement('tr');
                    data[i].forEach(cell => {
                        const td = document.createElement('td');
                        td.contentEditable = 'true'; // Make cell editable
                        td.textContent = cell;
                        row.appendChild(td);
                    });
                    tableBody.appendChild(row);
                }

                document.getElementById('tableContainer').style.display = 'block'; // Show table
            }
            
            function isValidUserInfo() {
                const userInfoElement = document.getElementById('userInfoValues');
                if (!userInfoElement) return false;
            
                const userInfoValue = userInfoElement.value.trim();
                if (userInfoValue === "") return false;
            
                try {
                    const parsedData = JSON.parse(userInfoValue);
                    return Array.isArray(parsedData) && parsedData.length > 0;
                } catch (e) {
                    return false; // Invalid JSON format
                }
            }

            function extractTableData() {
                const rows = document.querySelectorAll('#excelTable tbody tr');
                const tableData = [];
            
                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i].cells;
                    const rowData = [];
                    for (let j = 0; j < row.length; j++) {
                        rowData.push(row[j].textContent);
                    }
                    tableData.push(rowData);
                }
            
                document.getElementById('userInfoValues').value = JSON.stringify(tableData);

                if (isValidUserInfo()) {
                    document.getElementById('userInfoForm').submit();
                } 
                
                else {
                    alert('Please fill and/or select in all required fields before submitting.');
                }
            }
            

            const fileInput = document.getElementById("file");
            const uploadBox = document.getElementById("upload-box");
            const fileNameDisplay = document.getElementById("file-name");
            
            // Update file name when a file is selected
            fileInput.addEventListener("change", function () {
                const fileName = fileInput.files[0] ? fileInput.files[0].name : "No file chosen";
                fileNameDisplay.textContent = fileName;
            });
            
            // Handle drag-and-drop events
            uploadBox.addEventListener("dragover", (event) => {
                event.preventDefault();
                uploadBox.classList.add("dragover");
            });
            
            uploadBox.addEventListener("dragleave", () => {
                uploadBox.classList.remove("dragover");
            });
            
            uploadBox.addEventListener("drop", (event) => {
                event.preventDefault();
                uploadBox.classList.remove("dragover");
            
                // Access the dropped files
                if (event.dataTransfer.files.length > 0) {
                    fileInput.files = event.dataTransfer.files;
                    fileNameDisplay.textContent = event.dataTransfer.files[0].name;
                }
            });
        </script>
        
        
        <?php
             if($_SERVER["REQUEST_METHOD"] == "POST"){
                    if(isset($_POST["userInfo"])){ //The POST with userInfo is data sent from the previous php file (upload.php)
                        sendEmail();
                    }
            }
        ?>
        
        <?php require 'footer.php'; ?>
    </body>
</html>
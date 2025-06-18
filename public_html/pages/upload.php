<?php
        //ini_set('display_errors', 1); // Display errors on the page (only for development)
        //ini_set('display_startup_errors', 1); 
       //error_reporting(E_ALL); // Report all types of errors
        
        //DO NOT REMOVE THE COMMENTS IN THOSE ERROR REPORTING ABOVE UNLESS MAY IDE-DEBUG KA, DAHIL DI GUMAGANA MENU BAR PAG HINDI NAKA COMMENTED OUT
        
         // Import PHPMailer classes into the global namespace
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;
        use PHPMailer\PHPMailer\Exception;

        // Load Composer's autoloader
        require 'vendor/autoload.php';


        require 'connection.php';
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
        
        
        function getSections() {
            global $conn;
        
            // Escape the query to prevent XSS or injection
            $query = htmlspecialchars($_GET['query']);
        
            // Get the most recent academic year
            $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
            $acad_yearID = $result['id'];
            $start_year = $result['start_year'];
            $end_year = $result['end_year'];
        
            // Query to search sections by course, section_letter, section_group
            $sql = "SELECT * FROM sections 
                    WHERE academicYearID = ? AND sectionID <> ?
                    AND (courseID LIKE ? 
                    OR yearLevel LIKE ? 
                    OR section_letter LIKE ? 
                    OR section_group LIKE ? 
                    OR CONCAT(courseID, ' ', yearLevel, section_letter, section_group) LIKE ?)";
            
            $stmt = $conn->prepare($sql);
            $likeQuery = "%$query%";
            $stmt->execute([$acad_yearID, 0, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery]);
        
            $resultCounter = 0;
        
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $sectionID = htmlspecialchars($result['sectionID']);
                $section = htmlspecialchars($result['courseID']) . ' ' . htmlspecialchars($result['yearLevel']) . htmlspecialchars($result['section_letter']) . htmlspecialchars($result['section_group']) . '  (' . htmlspecialchars($result['specialization']) . ')   (' . $start_year . '-' . $end_year . ')';
                
                // Output valid <option> elements
                echo '<option value="' . $sectionID . '">' . $section . '</option>';
                $resultCounter++;
            }
        
            // If no results were found, return a "None" option
            if ($resultCounter <= 0) {
                echo '<option value="None">Search value returned 0 results</option>';
            }
        
           // exit(); // Terminate script after AJAX response
        }
        

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
        
        
        function sendEmail() {
            global $conn;
        
            try {
                // Decode user info from JSON
                $userInfoJson = $_POST['userInfo'];
                $userInfo = json_decode($userInfoJson, true);
        
                // Check for JSON parsing errors
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Failed to parse userInfo JSON: " . json_last_error_msg());
                }
        
                $accountType = isset($_POST['account_type']) ? $_POST['account_type'] : '';
                $sectionID = isset($_POST['section_value']) ? $_POST['section_value'] : '';
        
                // Initialize PHPMailer
                $mail = new PHPMailer(true);
        
                $messages = [];
                $successCount = 0;
        
                for ($r = 0; $r < count($userInfo); $r++) {
                    // Skip empty rows
                    if (empty(array_filter($userInfo[$r]))) {
                        continue;
                    }
        
                    // Trim and validate email
                    $email = trim($userInfo[$r][0]);
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        throw new Exception("Invalid or empty email at row " . ($r + 1));
                    }
        
                    // Check if email already exists
                    $sql = "SELECT email FROM users WHERE email = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$email]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
                    if ($result) {
                        $messages[] = "<li><strong>Email:</strong> <span style=\"color: red; font-weight: bold;\">" . htmlspecialchars($email) . "</span> <em>is already in use by another user, hence an email is not sent.</em></li>";
                        continue;
                    }
        
                    // Generate necessary values
                    $generated_password = generatePassword();
                    $generated_id = generateID();
                    $generated_token = generateToken();
        
                    $conn->beginTransaction(); // Start transaction
        
                    try {
                        // Insert into users table
                        $sql = "INSERT INTO users (id, email, password, firstname, middlename, surname, status, session, type, created_at) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([
                            $generated_id,
                            $email,
                            password_hash($generated_password, PASSWORD_DEFAULT),
                            $userInfo[$r][1],
                            $userInfo[$r][2],
                            $userInfo[$r][3],
                            "pending",
                            "offline",
                            $accountType,
                            date("Y-m-d")
                        ]);
        
                        if (!$result) {
                            throw new Exception("Failed to insert user data for email: $email");
                        }
        
                        // Additional logic based on account type
                        if ($accountType == "student") {
                            $sql = "INSERT INTO students (id, projectID, new_projectID, studentNo, sectionID, new_sectionID) 
                                    VALUES (?, ?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$generated_id, 0, NULL, $userInfo[$r][4], $sectionID, NULL]);
        
                            if (!$result) {
                                throw new Exception("Failed to insert student data for email: $email");
                            }
                        } elseif ($accountType == "faculty") {
                            $sql = "INSERT INTO faculty (id, accessLevel, category) VALUES (?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $result = $stmt->execute([$generated_id, $userInfo[$r][4], $userInfo[$r][5]]);
        
                            if ($result) {
                                $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $yearResult = $stmt->fetch(PDO::FETCH_ASSOC);
        
                                if (!$yearResult) {
                                    throw new Exception("Failed to retrieve academic year for faculty email: $email");
                                }
        
                                $acadYear = $yearResult["id"];
        
                                $sql = "INSERT INTO faculty_count (facultyID, panelist_count, panelist_limit, adviser_count, adviser_limit, coordinator_count, coordinator_limit, academicYearID) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $result = $stmt->execute([$generated_id, 0, 5, 0, 5, 0, 5, $acadYear]);
        
                                if (!$result) {
                                    throw new Exception("Failed to insert faculty count for email: $email");
                                }
                            } else {
                                throw new Exception("Failed to insert faculty data for email: $email");
                            }
                        }
        
                        // Insert creation token
                        $current_date = date("Y-m-d");
                        $sql = "INSERT INTO creation_tokens (id, token, created_at, activated) VALUES (?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $result2 = $stmt->execute([$generated_id, $generated_token, $current_date, "false"]);
        
                        if (!$result2) {
                            throw new Exception("Failed to insert creation token for email: $email");
                        }
        
                        // Send activation email
                        if (file_exists('pages/email_activation.php')) {
                            require 'email_activation.php';
                        } else {
                            throw new Exception("Email activation script not found.");
                        }
        
                        // Check if transaction is active before committing
                        if ($conn->inTransaction()) {
                            $conn->commit(); // Commit the transaction if it's active
                        } 
                        
                        else {
                            //nothing for now
                        }
                        
                        $successCount++;
                        //$messages[] = "<li><strong>Email:</strong> <span style=\"color: green; font-weight: bold;\">" . htmlspecialchars($email) . "</span> <em>has been successfully registered and notified.</em></li>";
        
                    } 
                    
                    catch (Exception $innerException) {
                        if ($conn->inTransaction()) {
                            $conn->rollBack();
                        }
                        throw $innerException;
                    }
                }
        
                // Display status messages
                if (!empty($messages)) {
                    $messageString = "<ul style=\"list-style-type: disc; padding-left: 20px; line-height: 1.5;\">" . implode('', $messages) . "</ul>";
                    echo "<script>
                        Swal.fire({
                            title: 'Email Registration Status',
                            html: '$messageString',
                            icon: 'info',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.href = '/upload';
                        });
                    </script>";
                }
        
            } 
            
            catch (Exception $e) {
                if ($conn->inTransaction()) {
                    $conn->rollBack(); // Roll back transaction on failure
                }
                echo "<script>
                    Swal.fire({
                        title: 'Error',
                        text: '" . addslashes($e->getMessage()) . "',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '/upload';
                    });
                </script>";
            }
        }



    ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Account Email Invitations</title>
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="pages/InvitePage.css">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="pages/session_tracker.js"></script>

        <!-- Include xlsx library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    </head>

    <body>
        <?php require 'header.php'; ?> <!-- Topbar -->
        <?php require 'menu.php'; ?> <!-- Menu -->
        
        <?php getAccess(); ?>

            <h2 class="invite-header">Invite</h2>
            
            
            <div class="button-container">
                <button type="button" class="icon-button" aria-label="Student with File" onclick="downloadFile('STUDENT TEMPLATE.xlsx')">
                    <i class="fas fa-user-graduate"></i> <!-- Student icon -->
                    <span>Student Template</span> <!-- Label -->
                </button>
                
                <button type="button" class="icon-button" aria-label="Teacher with File" onclick="downloadFile('FACULTY TEMPLATE.xlsx')">
                    <i class="fas fa-chalkboard-teacher"></i> <!-- Teacher icon -->
                    <span>Faculty Template</span> <!-- Label -->
                </button>
            </div>
                <div class="grid">
                    
                    <div class="Left">

                    <div id="radioDiv" class="invite-radio-buttons">
                        <label>

                            <input class="invite-input-radio-student" type="radio" name="account_type" value="student" onclick="toggleInput('show')" 
                             <?php 
                                    if (isset($_POST['account_type']) && $_POST['account_type'] == 'student') {
                                        echo 'checked';
                                    } 
                                    
                                    else if (!isset($_POST['account_type'])) {
                                        echo 'checked';
                                    }
                                ?> required>
                            <span class="custom-bullet student"></span> Student
                        </label>
    
                        <label>
                             <!-- PHP CONTENT GOES INSIDE THIS PART -->
                            <input  class="invite-input-radio-faculty" type="radio" name="account_type" value="faculty" onclick="toggleInput('hide')" 
                             <?php 
                                    if (isset($_POST['account_type']) && $_POST['account_type'] == 'faculty') {
                                        echo 'checked';
                                    }
                            ?> required>
                            <span class="custom-bullet faculty"></span> Faculty
                        </label>
                    </div>   
                    
                    <h3 id="sectionLabel" class="label">Section</h3>
                    
                    <div id="studentInputDiv" class="invite-section-box">
                        <input type ="text" class="sectionInput" id="sectionInput" name="sectionInput" onkeyup="searchSections()"placeholder="Enter Section" class="invite-section-input" required style="font-size: 20px; width: 100%;">
                        
                        <h3 class="label">Select</h3>
                        
                        <select name="sectionBox" id="sectionResults" size="5" class="invite-section-select" onchange="checkSelection()"required=""><?php getSections(); ?></select>
                    </div>
                 
                 </div>
                 
                 <div id="uploadDiv" class="Right">
                    <div class="upload-box" id="upload-box">
                        
                        <input type="file"id="file" accept=".xlsx, .csv" class="invite-file-input" required>
                        
                        <label for="file" class="upload-label">
                            
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="0 0 24 24" width="40" fill="#2c3e50">
                                    <path d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M5 20h14v-2H5v2zm7-18l-7 7h4v6h6v-6h4l-7-7z"></path>
                                </svg>
                            </div>
                            <p>Drag .xlsx or .csv files here</p>
                        </label>
                        <p class="file-name" id="file-name">No File Chosen</p>
                    </div>
                    
                    <div class="align-buttons">
                        <button type="button" onclick="handleFile()" class="upload-button">Extract</button>
                        
                        <form id="userInfoForm" action="" method="POST">
                            <input type="hidden" id="userInfoValues" name="userInfo" required="">
                            <input type="hidden" id="accountType" name="account_type" required="">
                            <input type="hidden" id="sectionValue" name="section_value" required="">
                            
                            <button type="button" onclick="extractTableData()" class="invite-button">Invite</button>
                        </form>
                    </div>
                 </div>
                 
             </div> <!-- end of Grid div -->
    
                 
    
                <div id="tableContainer" class="invite-table-container">
                    <table id="excelTable" class="invite-table">
                        <thead>
                            <tr id="headerRow"></tr> <!-- Empty row for headers -->
                        </thead>
                        <tbody>
                           
                        </tbody>
                    </table>
                </div>
        </div>

        <script>
        
            function downloadFile(filename) {
                const filepath = 'pages/excel_templates/' + filename; //Filename nakalagay sa may button, change nalang don pag need
                const link = document.createElement('a'); // Create an anchor element
                link.href = filepath; // Set the href to the file path
                link.download = filename; // Set the download attribute with the filename
                document.body.appendChild(link); // Append the link to the document
                link.click(); // Trigger the click event to start the download
                document.body.removeChild(link); // Remove the link after triggering the download
            }
        
            const expectedHeadersMap = {
                student: ["Email", "Firstname", "Middlename", "Surname", "Student-Number"],
                faculty: ["Email", "Firstname", "Middlename", "Surname", "Access-Level", "Category"]
            };
        
           window.addEventListener('pageshow', function(event) {
                // If the page is loaded from cache, we still need to check and set the default radio button
                const studentRadio = document.querySelector('input[name="account_type"][value="student"]');
                const facultyRadio = document.querySelector('input[name="account_type"][value="faculty"]');
                
                // If neither are checked (or after a form reset), set 'student' as default
                if (!studentRadio.checked && !facultyRadio.checked) {
                    studentRadio.checked = true;
                    toggleInput('show');  // Show relevant input fields
                }
                
                // Ensure the correct input fields are displayed based on the checked radio button
                if (studentRadio.checked) {
                    toggleInput('show');
                } 
                
                else if (facultyRadio.checked) {
                    toggleInput('hide');
                }
            });
            
            
            function searchSections() {
                const input = document.getElementById('sectionInput');
                const filter = input.value.toLowerCase();
                const select = document.getElementById('sectionResults');
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
                
                /*if (query.length >= 1) { // Only search if the input is at least 2 characters long
                     const xhr = new XMLHttpRequest();
                     xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            document.getElementById('sectionResults').innerHTML = xhr.responseText;
                        }
                     };
                     xhr.open('GET', '?query=' + encodeURIComponent(query), true);
                     xhr.send();
                }
                
                else {
                    document.getElementById('sectionResults').innerHTML = ''; // Clear results if less than 3 characters
                }*/
            }

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
                    Swal.fire({
                        icon: 'error',
                        title: 'No Excel File Uploaded',
                        text: 'Please upload a valid excel file',
                        confirmButtonText: 'Okay'
                    });

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
            
            
            function setHeaders(accountType) {
                const headers = expectedHeadersMap[accountType];
                const headerRow = document.getElementById('headerRow');
                headerRow.innerHTML = ''; // Clear existing headers
            
                headers.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header; // Set header text
                    headerRow.appendChild(th); // Append to the header row
                });
            }
            
            

          function extractTableData() {
            const rows = document.querySelectorAll('#excelTable tbody tr');
            const tableData = [];
        
            // Get account type from radio button selection
            const accountType = document.querySelector('input[name="account_type"]:checked').value;
        
            // Check if there are any rows
            if (rows.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Data',
                    text: 'No data found in the table.',
                    confirmButtonText: 'Okay'
                });
                return;
            }
        
            // Extract the headers from the first row (index 0)
            //const firstRow = rows[0].cells;
            //const headers = Array.from(firstRow).map(cell => cell.textContent.trim());
            
            // Extract the headers from the <thead> section
            const headerRow = document.querySelector('#excelTable thead tr');
            const headers = Array.from(headerRow.cells).map(cell => cell.textContent.trim());
        
            // Verify the extracted headers against the expected headers
            const expectedHeaders = expectedHeadersMap[accountType];
        
            // Check for header verification
            if (!verifyHeaders(headers, expectedHeaders)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Incorrect Headers',
                    text: `The uploaded file does not have the correct column headers for ${accountType}. Expected headers: ${expectedHeaders.join(", ")}`,
                    confirmButtonText: 'Okay'
                });
                return; // Stop the execution if headers do not match
            }
        
            // Extract user information starting from the second row (index 1)
            for (let i = 0; i < rows.length; i++) { // Start from 0 now, OLD VERSION WAS 1 WHEN HEADERS WAS A <tr>
                const row = rows[i].cells;
                const rowData = [];
                for (let j = 0; j < row.length; j++) {
                    rowData.push(row[j].textContent.trim()); // Trim whitespace
                }
                tableData.push(rowData);
            }
        
            // Set values for hidden form inputs
            document.getElementById('userInfoValues').value = JSON.stringify(tableData);
            document.getElementById('accountType').value = accountType;
            document.getElementById('sectionValue').value = document.getElementById('sectionResults').value;
        
            // Submission logic based on account type
            if (accountType === "student") {
                if (document.getElementById('sectionValue').value.trim() !== "" && isValidUserInfo()) {
                    document.getElementById('userInfoForm').submit();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Information',
                        text: 'Please fill and/or select all required fields before submitting.',
                        confirmButtonText: 'Okay'
                    });
                }
            } else if (accountType === "faculty") {
                if (isValidUserInfo()) {
                    document.getElementById('userInfoForm').submit();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Information',
                        text: 'Please fill and/or select all required fields before submitting.',
                        confirmButtonText: 'Okay'
                    });
                }
            }
        }
            
            
            
            
            function verifyHeaders(headers, expectedHeaders) {
                const trimmedHeaders = headers.map(header => header.trim());
                const trimmedExpectedHeaders = expectedHeaders.map(header => header.trim());
            
                console.log("Trimmed Extracted Headers:", trimmedHeaders);
                console.log("Trimmed Expected Headers:", trimmedExpectedHeaders);
            
                if (trimmedHeaders.length !== trimmedExpectedHeaders.length) {
                    console.log("Header length mismatch!");
                    return false;
                }
            
                for (let i = 0; i < trimmedHeaders.length; i++) {
                    if (trimmedHeaders[i] !== trimmedExpectedHeaders[i]) {
                        console.log(`Mismatch at index ${i}: ${trimmedHeaders[i]} !== ${trimmedExpectedHeaders[i]}`);
                        return false;
                    }
                }
                return true;
            }
                                    
            
            
            
            
            function checkSelection() {
                const select = document.getElementById('sectionResults');
                const selectedValue = select.value;

                if (selectedValue === 'None') {
                    alert('No section found. Please try again.');
                }
            }
            
            
            

            function toggleInput(display) {
                if (display === 'show') {
                    //document.getElementById('studentInputDiv').style.display = 'block';
                    document.getElementById('sectionInput').disabled = false;
                    document.getElementById('sectionResults').disabled = false;
                    document.getElementById('sectionLabel').innerText = "Sections"
                    
                     // Clear existing table
                    var thead = document.querySelector('#excelTable thead');
                    var tbody = document.querySelector('#excelTable tbody');
                    thead.innerHTML = '';
                    tbody.innerHTML = '';
                } 
                
                else {
                    //document.getElementById('studentInputDiv').style.display = 'none';
                    document.getElementById('sectionInput').disabled = true;
                    document.getElementById('sectionResults').disabled = true;
                    document.getElementById('sectionLabel').innerText = "Sections"; //Unicode for non-breaking space
                    
                     // Clear existing table
                    var thead = document.querySelector('#excelTable thead');
                    var tbody = document.querySelector('#excelTable tbody');
                    thead.innerHTML = '';
                    tbody.innerHTML = '';
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
                    if(isset($_POST["userInfo"]) && isset($_POST["account_type"])){ //The POST with userInfo is data sent from the previous php file (upload.php)
                            if($_POST["account_type"] == "student"){
                                    if(isset($_POST["section_value"])){
                                        sendEmail();
                                    }
                            }
                            
                            else if($_POST["account_type"] == "faculty"){
                                sendEmail();
                            }
                    }
            }
        
        ?>
        
        <?php require 'footer.php'; ?>
    </body>
</html>

<?php
    require_once "connection.php";
    session_start();
    
    $fileExists = false;
    $fileRemoved = false;
    
    $_SESSION["selectedTask"] = 2;
    $selectedOption = isset($_GET['taskOptions']) ? $_GET['taskOptions'] : $_SESSION["selectedTask"];
    
  
    function getTemplate($taskID){
        global $conn, $fileExists;
        
        try{
            echo '<script>
                        console.log('.$taskID.')
                   </script>';

            $sql = "SELECT * FROM document_templates WHERE taskID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$taskID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && !empty($result["filepath"])) {
                $filepath = $result["filepath"];
                $filename = basename($filepath);
                
                $_SESSION["filepath"] = $filepath;
                

                // Output the file path and name as JSON for JavaScript to handle
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        displayExistingFile('{$filename}', '{$filepath}');
                    });
                    

                    document.getElementById('submit-btn').innerText = 'Unsubmit';
                    document.getElementById('submit-btn').style.backgroundColor = '#b22222';
                    document.getElementById('submit-btn').onclick = enableButton; // Change function
                    document.getElementById('remove-file').style.display = 'none'; // Hide the button
                    document.getElementById('remove-file').disabled = true; // Disables the button

                    
                    document.getElementById('file-upload').onclick = ''; // Disables the file upload
                </script>";
                
                $fileExists = true;
            }
            
            else{
                 echo '
                    <script>
                        document.getElementById("file-upload-text").textContent = "No Template Uploaded";
                    </script>
                ';
            }
        }
        
        catch(Exception $e){
            echo '<script>
                    console.log("'.$e->getMessage().'");
            </script>';
        }
    }
   
    function uploadFile($taskID){
        global $conn, $fileExists;
        
        try{
            $result = "";
            $conn->beginTransaction();
            
            echo '<script>
                        console.log('.$taskID.')
                   </script>';
            
            if($fileExists == false){
                
                $targetDir = "document_templates/";  // Make sure this folder exists and has the correct permissions
        
                // Get file details
                $fileTmpPath = $_FILES['uploaded_file']['tmp_name'];
                $fileName = basename($_FILES['uploaded_file']['name']);
                $fileSize = $_FILES['uploaded_file']['size'];
                $fileType = $_FILES['uploaded_file']['type'];
        
                // Ensure the file is a PDF
                $allowedFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

                if (!in_array($fileType, $allowedFileTypes)) {
                    throw new Exception("Only PDF and Word files are allowed");
                }
                                    
                else {
                    // Create a directory with the projectID if it does not exist
                    $templateDir = $targetDir . $taskID;
                    
                    if (!is_dir($templateDir)) {
                        if (!mkdir($templateDir, 0777, true)) {
                            throw new Exception("Failed to create directory: " . $templateDir);
                        }
                    }
                    
                    // Define the target path for the uploaded file
                    $targetFilePath = $templateDir . '/' . $fileName;
        
                    // Move the file from the temporary location to the target directory
                    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
    
                       
                        $sql = "INSERT INTO document_templates (taskID, filepath) VALUES(?, ?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$taskID, $targetFilePath]);
                        
                        if($result){
                            $conn->commit();
                            
                            unset($_POST["submit-btn"]);
                            unset($_FILES["uploaded_file"]);
                            
                            echo '<script>
                                        Swal.fire({
                                            title: "Success",
                                            text: "Document Template Uploaded!",
                                            icon: "success",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "/create_templates";
                                            }
                                            
                                            else if (result.isDismissed) {
                                                 window.location.href = "/create_templates";
                                            }
                                        });
                                   </script>';
                                   
                            getTemplate($taskID);
                        }
                        
                        else{
                             throw new Exception("Error uploading");
                        }
                    }
                }
                        
                
            }
            
            else if($fileExists == true){
                $targetDir = "document_templates/";  // Make sure this folder exists and has the correct permissions
        
                // Get file details
                $fileTmpPath = $_FILES['uploaded_file']['tmp_name'];
                $fileName = basename($_FILES['uploaded_file']['name']);
                $fileSize = $_FILES['uploaded_file']['size'];
                $fileType = $_FILES['uploaded_file']['type'];
        
                $allowedFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

                if (!in_array($fileType, $allowedFileTypes)) {
                    throw new Exception("Only PDF files are allowed");
                }
                
                else {
                    // Create a directory with the recepientID if it does not exist
                    $templateDir = $targetDir . $taskID;
                    
                    // Define the target path for the uploaded file
                    $targetFilePath = $templateDir . '/' . $fileName;
        
                    // Move the file from the temporary location to the target directory
                    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
    
                        $sql = "UPDATE document_templates SET filepath = ? WHERE taskID = ?";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$targetFilePath, $taskID]);
                        
                        if($result){
                            $conn->commit();
                            
                            unset($_POST["submit-btn"]);
                            unset($_FILES["uploaded_file"]);
                            
                            echo '<script>
                                        Swal.fire({
                                            title: "Success",
                                            text: "Document Template Uploaded!",
                                            icon: "success",
                                            confirmButtonText: "OK"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "/create_templates";
                                            }
                                            
                                            else if (result.isDismissed) {
                                                window.location.href = "/create_templates";
                                            }
                                        });
                                   </script>';
                                   
                            getTemplate($taskID);
                        }
                        
                        else{
                            throw new Exception("Error uploading");
                        }
                    }
                }
            }
            
        }
        
        catch(Exception $e){
            $conn->rollBack();
            
            unset($_POST["submit-btn"]);
            
            echo '<script>
                    Swal.fire({
                        title: "Error",
                        text: "'.$e->getMessage().'",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/create_templates";
                        }
                        
                        else if (result.isDismissed) {
                            window.location.href = "/create_templates";
                        }
                    });
               </script>';
        }
    }
    
    function getTasks(){
        global $conn, $selectedOption;
        
        try{
            $sql = "SELECT * FROM tasks WHERE taskName <> 'defense' AND taskName <> 'title evaluation'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if(count($results) >= 1){
                
                foreach($results as $result){
                    
                    $taskID = $result["id"];
                    $taskName = ucwords($result["taskName"]);
                    $isSelected = ($selectedOption == $taskID) ? ' selected' : '';

                    echo "<option value=\"$taskID\"$isSelected>$taskName</option>";
                }
            }
        }
        
        catch(Exception $e){
            $conn->rollBack();
            
            unset($_POST["submit-btn"]);
            
            echo '<script>
                    console.log("'.$e->getMessage().'");
            </script>';
        }
    }
    
?>


<!DOCTYPE html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="pages/title_evaluation.css">
    <link rel="stylesheet" href="pages/capstone_paper.css">
    <script src="pages/session_tracker.js"></script>

    <title>Document Template</title>

    <style>
        /* File Upload Card */
        .file-upload-card {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            justify-content: space-between;
            width: 95%; /* Keep width consistent */
            max-width: 1000px;
            background: #fff;
            border: 2px solid #066ba3;
            border-radius: 10px;
            padding: 30px; /* 1.5x padding */
            margin: 30px auto; /* 1.5x margin */
        }

        /* Left Side: File Upload Section */
        .file-upload-section {
            flex: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-right: 2px solid #e0e0e0;
            padding: 30px; /* 1.5x padding */
        }

        #file-upload {
            cursor: pointer;
            text-align: center;
            border: 2px dashed #066ba3;
            border-radius: 8px;
            padding: 45px; /* 1.5x padding */
            width: 100%;
            max-width: 600px; /* 1.5x max-width */
            transition: all 0.3s ease;
        }

        #file-upload:hover {
            background: #f1f9ff;
        }

        #file-upload-text {
            font-size: 24px; /* 1.5x font size */
            color: #555;
            margin-top: 15px; /* 1.5x margin */
        }

        .file-upload i {
            font-size: 72px; /* 1.5x icon size */
            color: #066ba3;
        }

        /* Right Side: File Display Section */
        .file-view-section {
            flex: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px; /* 1.5x padding */
        }

        #file-display {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 600px; /* 1.5x max-width */
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px; /* 1.5x padding */
            background: #f9f9f9;
        }

        #file-display i {
            font-size: 36px; /* 1.5x icon size */
            color: red;
        }

        #file-name {
            margin-left: 15px; /* 1.5x margin */
            flex: 1;
            color: #333;
            font-size: 24px; /* 1.5x font size */
            font-weight: 500;
        }

        #remove-file {
            border: none;
            background: none;
            cursor: pointer;
            color: #b22222;
            font-size: 30px; /* 1.5x font size */
        }

        #remove-file:hover {
            color: #ff4d4d;
        }

        /* Button Styles */
        .button-submit {
            margin-top: 30px; /* 1.5x margin */
            padding: 15px 30px; /* 1.5x padding */
            background-color: #066ba3;
            color: white;
            border: none;
            border-radius: 8px; /* 1.5x border radius */
            font-size: 24px; /* 1.5x font size */
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .button-submit:disabled {
            background-color: gray;
            cursor: not-allowed;
        }

        .button-submit:hover:not(:disabled) {
            background-color: #054f7a;
        }

        /* Section Container */
        .section-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px; /* 1.5x gap */
            text-align: center;
            padding: 30px; /* 1.5x padding */
            width: 100%;
        }

        .section-title {
            font-size: 34px; /* 1.5x font size */
            font-weight: 600;
            color: #333;
            margin-bottom: 15px; /* 1.5x margin */
        }

        #dropdown {
            width: 100%;
        }

        #dropdown form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        #dropdown label {
            font-size: 27px; /* 1.5x font size */
            margin-bottom: 15px; /* 1.5x margin */
            color: #555;
        }

        #taskOptions {
            width: 100%;
            max-width: 600px; /* 1.5x max-width */
            padding: 15px; /* 1.5x padding */
            font-size: 24px; /* 1.5x font size */
            border: 2px solid #ccc; /* 1.5x border width */
            border-radius: 8px; /* 1.5x border radius */
            background: #fdfdfd;
        }

        #taskOptions:focus {
            outline: none;
            border-color: #066BA3;
            box-shadow: 0 0 10px rgba(6, 107, 163, 0.5);
        }
    </style>
</head>


    
    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>
        

        <div class="section-container">
            <div id="dropdown">
                <form action="" method="GET">
                    <label for="taskOptions">Select Task To Upload Template</label>
                    <select name="taskOptions" id="taskOptions" onchange="this.form.submit();">
                        <?php getTasks(); ?>
                    </select>
                </form>
            </div>
        </div>
        
       <form action="" method="POST" enctype="multipart/form-data" id="fileUploadForm">
            <div class="horizontal-container">
                <div class="right-side">
                    <div class="file-section" style="margin-top: 20px;">
                        <!-- Input for file upload -->
                        <input type="file" id="file-upload-input" name="uploaded_file" accept="application/pdf" style="display: none;" required>
                        
                        <div class="file-upload-card">
                            <!-- File Upload Section -->
                            <div class="file-upload-section">
                                <div id="file-upload" onclick="document.getElementById('file-upload-input').click();">
                                    <i class="fas fa-upload"></i>
                                    <span id="file-upload-text">Click to upload a PDF file</span>
                                </div>
                            </div>
        
                            <!-- File Display Section -->
                            <div class="file-view-section">
                                <div id="file-display" style="display: none;">
                                    <i class="fas fa-file-pdf"></i>
                                    <span id="file-name" class="highlight"></span>
                                    <button id="remove-file" class="remove-file" type="button">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="panelist-section">
                        <br>
                        <div class="panelist-info-buttons">
                            <!-- Submit Button -->
                            <button id="submit-btn" name="submit-btn" class="button-submit" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        
        <!-- Modal for displaying PDF -->
        <div id="pdf-modal" class="modal">
            <div class="modal-content">
                 <!--<h2 id="modal-file-name"></h2>-->
                <span class="close-button" onclick="closeModal()">&times;</span>
                <iframe id="pdf-frame" name="" src="" width="100%" height="100%" style="border:none;"></iframe>
            </div>
        </div>
        
        
        <script>
            const fileInput = document.getElementById('file-upload-input');
            const fileDisplay = document.getElementById('file-display');
            const fileNameSpan = document.getElementById('file-name');
            const fileUploadText = document.getElementById('file-upload-text');
            const removeFileButton = document.getElementById('remove-file');
            let tempPdfUrl = ''; // Temporary URL for the uploaded PDF
            
            
              // Display the selected file name
            document.getElementById('file-upload-input').addEventListener('change', function() {
                var fileName = this.files[0].name;
                document.getElementById('file-name').textContent = fileName;
                document.getElementById('file-display').style.display = 'block';
            });
        
            // Remove selected file
            document.getElementById('remove-file').addEventListener('click', function() {
                document.getElementById('file-upload-input').value = '';
                document.getElementById('file-name').textContent = '';
                document.getElementById('file-display').style.display = 'none';
            });
            
            // Handle new file selection and display
            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const fileName = file.name;
                    fileNameSpan.textContent = file.name;
                    fileDisplay.style.display = 'flex'; // Show file display
                    fileUploadText.textContent = 'Template Uploaded: ' + file.name;
                    
                    // Create a temporary URL for preview
                    tempPdfUrl = URL.createObjectURL(file);
                    
                    displayExistingFile(fileName, tempPdfUrl);
                    
                    //Re-enabled the submit button again if there is now an uploaded file
                    document.getElementById('submit-btn').disabled = false;
                    document.getElementById('submit-btn').style.backgroundColor = '#066BA3';
                }
            });
            
            // Handle file removal
            removeFileButton.addEventListener('click', function() {
                fileInput.value = ''; // Clear the file input
                fileDisplay.style.display = 'none'; // Hide file display
                fileUploadText.textContent = 'Click to upload a PDF file';
                tempPdfUrl = ''; // Clear temporary URL
                
                //Disable submit button since there is no file
                document.getElementById('submit-btn').style.backgroundColor = 'gray';
                document.getElementById('submit-btn').disabled = true;
            });
            
            // Click to view the selected or fetched file
            fileNameSpan.addEventListener('click', function() {
                if (tempPdfUrl) {
                    openModal(tempPdfUrl); // For newly uploaded file
                }
            });
            
            // Display existing file from database
            function displayExistingFile(filename, filepath) {
                fileNameSpan.textContent = filename;
                fileDisplay.style.display = 'flex';
                fileUploadText.textContent = 'Template Uploaded';
                
                // Open existing file in modal on click
                fileNameSpan.addEventListener('click', function () {
                    openModal(filepath);  // Direct path from database
                });
            }
            
            // Modal functions for viewing PDFs
            function openModal(filePath) {
                const modal = document.getElementById('pdf-modal');
                const pdfFrame = document.getElementById('pdf-frame');
                
                //const modalFileName = document.getElementById('modal-file-name'); //Get the element for the filename
                //modalFileName.textContent = filename; // Display the filename in the modal
                
                
                pdfFrame.src = filePath;
                modal.style.display = 'block';
            }
            
            function closeModal() {
                const modal = document.getElementById('pdf-modal');
                modal.style.display = 'none';
                document.getElementById('pdf-frame').src = '';  // Clear iframe src on close
            }
            
            function enableButton(){
                
                document.getElementById('submit-btn').onclick= function(){
                    document.getElementById('fileUploadForm').submit();
                }

                document.getElementById('submit-btn').innerText = 'Submit';
                document.getElementById('submit-btn').style.backgroundColor = '#066BA3';
                document.getElementById('remove-file').style.display = 'block'; 
                document.getElementById('remove-file').disabled = false;

                document.getElementById('file-upload').onclick = function() {
                    document.getElementById('file-upload-input').click();
                };
            }
        </script>
        
        
        <?php getTemplate($_SESSION["selectedTask"]); ?>


        <?php 
            // Check if the form has been submitted and a file is uploaded
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                global $fileExists, $selectedOption;
        
                if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] === UPLOAD_ERR_OK) {
                    uploadFile($selectedOption);
                }
                else if (!isset($_FILES['uploaded_file']) || $_FILES['uploaded_file']['error'] !== UPLOAD_ERR_OK){
                    echo "Error uploading file: " . $_FILES['uploaded_file']['error']; // Debugging
        
                    if($fileExists == false){
                        echo '<script>
                            Swal.fire({
                                 title: "No File Selected",
                                text: "Please select a PDF or Word File to upload",
                                icon: "error",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/create_templates";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/create_templates";
                                    }
                                });
                        </script>';
                        unset($_POST["submit-btn"]);
                    }
                    else if($fileExists == true){
                        uploadFile($selectedOption);
                    }
                }
            }
        
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                global $fileExists, $selectedOption;
                
                if(isset($_GET["taskOptions"])){
                    getTemplate($_GET["taskOptions"]);
                    $selectedOption = $_GET["taskOptions"];
                    
                    $_SESSION["selectedTask"] = $_GET["taskOptions"];
                }   
            }
        ?>

        
        <?php require 'footer.php'; ?>
    </body>
</html>
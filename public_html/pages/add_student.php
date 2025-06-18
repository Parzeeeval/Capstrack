<!DOCTYPE html>

<?php 
    require "connection.php";
    session_start();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
        getStudentValues();
    }
    
    function getSectionValue(){
        global $conn;
        
        try{
            $sectionID = $_SESSION["sectionID"];
            
            $sql = "SELECT * FROM sections WHERE sectionID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$sectionID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $section = htmlspecialchars($result['courseID']) . ' ' . htmlspecialchars($result['yearLevel']) . htmlspecialchars($result['section_letter']) . htmlspecialchars($result['section_group']);
            
            return $section;
        }
        
        catch(Exception $e){
            error_log($e->getMessage());
        }
    }
    
    function addStudent(){
        global $conn;
        
        try {
            $conn->beginTransaction();
            
            $studentID = isset($_POST["studentBox"]) && $_POST["studentBox"] != "" ? htmlspecialchars($_POST["studentBox"]) : '';
            
            if($studentID != "None"){
                $projectID = $_SESSION["projectIDValue"];
                
             
                $sql = "SELECT mode FROM academic_year ORDER BY id DESC LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $mode = 0;
   
                
                if($result){
                    $mode = $result["mode"];
                }
                
                
                $result = "";
                
                if($mode == 1 || $mode == 2){
                    $sql = "UPDATE students SET projectID = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $result = $stmt->execute([$projectID, $studentID]);
                }
                
                else if($mode == 3){
                     $sql = "UPDATE students SET projectID = ?, new_projectID = ? WHERE id = ?";
                     $stmt = $conn->prepare($sql);
                     $result = $stmt->execute([$projectID, $projectID, $studentID]);
                }
            
                
                if ($result) {
                    
                    $sql =  "SELECT firstname, surname FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["userID"]]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $coord_name = $user["firstname"] . " " . $user["surname"] ;
                    
                    $section = getSectionValue();
                    
                    $desc = $coord_name . " added you in a capstone group in " . $section;
                    
                    date_default_timezone_set('Asia/Manila');
                    $date = date('Y-m-d H:i:s');
                    
                    $sql = "INSERT INTO notifications (userID, description, date) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $result = $stmt->execute([$studentID, $desc, $date]);
                    
                    if($result){
                        
                         $sql = "SELECT * FROM sections WHERE sectionID = ?";
                         $stmt = $conn->prepare($sql);
                         $stmt->execute([$_SESSION["sectionID"]]);
                         $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                         $section = $result["courseID"] . " " . $result["yearLevel"] . $result["section_letter"] . $result["section_group"];
                         
                         
                         $sql = "SELECT groupNum FROM capstone_projects WHERE projectID = ?";
                         $stmt = $conn->prepare($sql);
                         $stmt->execute([$projectID]);
                         $result = $stmt->fetch(PDO::FETCH_ASSOC);
                         
                         $groupNum = $result["groupNum"];
                         
                         
                         
                         $sql = "SELECT * FROM users WHERE id = ?";
                         $stmt = $conn->prepare($sql);
                         $stmt->execute([$studentID]);
                         $result = $stmt->fetch(PDO::FETCH_ASSOC);
                          
                         $student_name = "". $result["surname"] . ", " . $result["firstname"]  . " " . $result["middlename"] ;
                        
                        
                         $sql = "SELECT * FROM users WHERE id = ?";
                         $stmt = $conn->prepare($sql);
                         $stmt->execute([$_SESSION["userID"]]);
                         $result = $stmt->fetch(PDO::FETCH_ASSOC);
                          
                         $firstname = $result["firstname"];
                         $surname = $result["surname"];
                         $middlename = $result["middlename"];


                         $action = "". $surname . ", " . $firstname . " " . $middlename . " added " . $student_name . " as a member of Group " . $groupNum . " in " . $section;
                         
                         date_default_timezone_set('Asia/Manila');
                         $date = date('Y-m-d H:i:s');
                
                         $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                         $stmt = $conn->prepare($sql);
                         $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                                     
                        $conn->commit();
                        
                        unset($_POST["add-button"]);
                                            
                        echo '<script>
                                Swal.fire({
                                     title: "Success",
                                    text: "Student successfully added!",
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/add_student";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/add_student";
                                    }
                                });
                              </script>';
                    }
                    
                    else{
                        throw new Exception("Error inserting in notifications");
                    }
                } 
                
                else {
                    throw new Exception("Please try again");
                }
            }
            
            else{
                throw new Exception("No student available to add with the given search value");
            }
        
        } 
        
        catch(Exception $e) {
            $conn->rollBack();
            
            error_log("Error inserting Student: " . $e->getMessage());
            
            unset($_POST["add-button"]);
            
            echo '<script>
                Swal.fire({
                     title: "Error Adding Student",
                    text: "Error: '.addslashes($e->getMessage()).'",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "/add_student";
                    }
                    
                    else if (result.isDismissed) {
                         window.location.href = "/add_student";
                    }
                });
            </script>';
        }
    }
    
    
    function getStudentValues(){
      global $conn;
      
      $query = htmlspecialchars($_GET['query']);
      
      $studentSection = $_SESSION["sectionID"];
    
       $sql = "SELECT users.id, users.email, users.firstname, users.middlename, users.surname, students.studentNo, students.sectionID
                FROM users
                JOIN students ON students.id = users.id
                WHERE students.projectID = ? AND students.sectionID = ? 
                AND (
                    students.studentNo LIKE ? 
                    OR users.id LIKE ? 
                    OR users.email LIKE ? 
                    OR users.surname LIKE ? 
                    OR users.firstname LIKE ?
                    OR CONCAT(users.firstname, ' ', users.surname) LIKE ?
                    OR CONCAT(users.firstname, ' ', users.surname, ' ', users.middlename) LIKE ?
                    OR CONCAT(users.surname, ', ', users.firstname, ' ', users.middlename) LIKE ?
                )";

        $stmt = $conn->prepare($sql);
        $likeQuery = "%$query%";
        $stmt->execute([0, $studentSection, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery]);
        
        $resultCounter = 0;
    
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $studentID = htmlspecialchars($result["id"]);
            
            echo '<option value="'.$studentID.'">' 
                 . htmlspecialchars($result["surname"]) . ', ' . htmlspecialchars($result["firstname"]) . ' ' 
                 . htmlspecialchars($result["middlename"]) . ' - (' . htmlspecialchars($result["studentNo"]) . ')
                 </option>';
                 
            $resultCounter++;
        }
        
        if($resultCounter <= 0){
            echo '<option value="None">
                        Search value returned 0 students
                  </option>';
        }
    }
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
     <link rel="stylesheet" href="pages/dashboard_files/header.css">
    <title>Add Student</title>
    <script src="pages/session_tracker.js"></script>
    
    <style>
     
      .header-group {
            display: flex;
            gap: 20px; 
            justify-content: center; 
            align-items: center; 
        }
            
        .header-group h2 {
            font-size: 33px; 
            margin: 0; 
            margin-bottom: -10px;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
    
            width: 95%;
            height: auto;
            margin: 0 auto; 
        }

        h2 {
            text-align: center;
            color: #333;
            margin: 10px 0;
            font-size: 33px;
        }

        .divider {
            border-top: 5px solid #6BA3C1;
            margin: 20px 0;
        }

        .optionDiv {
            display: flex;
            flex-direction: column; /* Stack elements vertically */
            gap: 20px;
            margin: 30px 0;
        }

        .form-group {
            margin: 0;
        }

        label {
            font-size: 20px;
            color: #555;
        }

        select, input[type="text"] {
            width: 100%; /* Make full width */
            padding: 15px;
            font-size: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        select:focus, input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .button-group {
            text-align: center;
            margin-top: 20px; 
        }
        
        .form-button {
            background-color: #41A763;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 15px 30px; /* Adjusted padding for smaller button */
            font-size: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-button:hover {
            background-color: #277542;
        }

        /* Responsive design adjustments */
        @media (max-width: 600px) {
            .header-group {
                flex-direction: column; /* Stack header elements on small screens */
                align-items: flex-start;
            }
            .container {
                padding: 20px; /* Adjust padding on smaller screens */
            }
            h2 {
                font-size: 28px; /* Adjust font size for smaller screens */
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'menu.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

   
        <div class="header-group">
            <h2>Adding Student For:</h2>
            <h2><?php echo getSectionValue(); ?></h2>
            <h2><?php echo $_SESSION["titleValue"]; ?></h2>
        </div>
        
        <div class="divider"></div> 
        
        
        <form action="" method="POST">
            <div class="optionDiv">
                <div class="right-section">
                    <div class="form-group">
                        <label for="studentSearch">Search a Student using <span style="color: #41A763;">Student No. <span style="color: #41A763;"> Name </span> or <span style="color:  #41A763;"> Email </span></label>
                        
                        <input type="text" id="studentSearch" placeholder="Enter Student Name or Email" onkeyup="filterStudents()">
                        <br><br>
                        <select name="studentBox" id="studentResults" size="10" required>
                            <?php getStudentValues(); ?>
                        </select>
                    </div>
                    
                    <div class="button-group">
                        <button class="form-button" type="submit" name="add-button">Add Student</button>
                    </div>
                </div>
            </div> 
        </form>
        
        <script>
            function filterStudents() {
                const input = document.getElementById('studentSearch');
                const filter = input.value.toLowerCase();
                const select = document.getElementById('studentResults');
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
        
                
        <?php 
             if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["add-button"])) {
                    addStudent();
                }
            }
        ?>

        <?php include 'footer.php'; ?>
</body>
</html>


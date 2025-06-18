<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
     <link rel="stylesheet" href="pages/create_specializations.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <title>Create New Specialization</title>
    <script src="pages/session_tracker.js"></script>
    
</head>

<body>
    <?php 
        require 'connection.php'; 
        session_start();
        
        $courseID = $_SESSION["courseID"];
    ?>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <?php require 'header.php'; ?>
    <?php require 'menu.php'; ?>

    <?php
        $selectedAdmin = isset($_POST["adminBox"]) ? htmlspecialchars($_POST["adminBox"]) : "";
    ?>

    <form action="" method="POST">
       
            <div class="header-container">
                <h2>Create New Specialization</h2>
                <h2>For</h2>
                
                <?php
                    $sql = "SELECT courseName FROM courses WHERE courseID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$courseID]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $courseName = $result["courseName"];
                    
                    echo '
                        <h2>' . addslashes($courseID) . '</h2>
                        <h2>(' . addslashes($courseName) . ')</h2>
                    ';
                ?>
        </div>
            
            <div class="divider"></div>
   <div class="container">
            <div class="form-group">
                <label for="specializationName">Specialization Name</label>
                <input type="text" pattern="[A-Za-z\s]*" oninput="validateInput(this)" name="specializationName" id="specializationName" placeholder="Input specialization name" required>
            </div>
                            
            <div class="button-group">
                <button class="form-button" type="submit" name="create-button">Create</button>
            </div>
        </div>
    </form>

    <?php 
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["create-button"])) {
                global $conn;
                
                $specializationName = htmlspecialchars($_POST["specializationName"]);

                try {
                    $conn->beginTransaction();
                    
                    $sql = "SELECT * FROM specializations WHERE courseID = ? AND name = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$courseID, $specializationName]);
                    $rowCount = $stmt->rowCount();
                    
                    if ($rowCount <= 0) {
                        $sql = "INSERT INTO specializations (courseID, name, created_at) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->execute([$courseID, $specializationName, date("Y-m-d")]);
                        
                        if ($result) {
                            
                             $sql = "SELECT courseName FROM courses WHERE courseID = ?";
                             $stmt = $conn->prepare($sql);
                             $stmt->execute([$courseID]);
                             $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                             $courseName = $result["courseName"];
                            
                             $sql = "SELECT * FROM users WHERE id = ?";
                             $stmt = $conn->prepare($sql);
                             $stmt->execute([$_SESSION["userID"]]);
                             $result = $stmt->fetch(PDO::FETCH_ASSOC);
                              
                             $firstname = $result["firstname"];
                             $surname = $result["surname"];
                             $middlename = $result["middlename"];


                             $action = "". $surname . ", " . $firstname . " " . $middlename . " created the specialization: " . $specializationName . " belonging to " . $courseName;
                             
                             date_default_timezone_set('Asia/Manila');
                             $date = date('Y-m-d H:i:s');
                    
                             $sql = "INSERT INTO action_logs (userID, action, date) VALUES (?, ?, ?)";
                             $stmt = $conn->prepare($sql);
                             $result = $stmt->execute([$_SESSION["userID"], $action, $date]);
                                     
                            $conn->commit();
                            
                            unset($_POST["create-button"]);
                            
                            echo '<script>
                                Swal.fire({
                                    title: "Success",
                                    text: "' . addslashes($specializationName) . ' successfully created!",
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "/create_specializations";
                                    }
                                    
                                    else if (result.isDismissed) {
                                         window.location.href = "/create_specializations";
                                    }
                                });
                            </script>';
                        } else {
                            throw new Exception("Error in creating new specialization");
                        }
                    } else {
                        throw new Exception("Error: the specialization being created may have been a duplicate of an existing one");
                    }
                } catch (Exception $e) {
                    $conn->rollBack();
                    unset($_POST["create-button"]);
                    
                    echo '<script>
                        Swal.fire({
                            title: "Error Creating Specialization",
                            text: "' . addslashes($e->getMessage()) . '",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/create_specializations";
                                }
                                
                                else if (result.isDismissed) {
                                     window.location.href = "/create_specializations";
                                }
                            });
                    </script>';
                }
            }
        }
    ?>

    <?php require 'footer.php'; ?>

    <script>
        function validateInput(input) {
            const regex = /[0-9]/g;
            if (regex.test(input.value)) {
                input.value = input.value.replace(regex, ''); // Remove numeric digits
            }
        }
    </script>
</body>
</html>

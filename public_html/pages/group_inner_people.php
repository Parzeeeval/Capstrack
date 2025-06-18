<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>People Section UI</title>
        <link rel="stylesheet" href="pages/group_inner_people.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <script src="pages/session_tracker.js"></script>
    </head>
    
    <body>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        
        <?php 
        
            require "connection.php";
            session_start();
        
            // Only fetch the projectID once
            $projectID = $_SESSION["projectIDValue"];
            
            function getAdvisers(){
                global $conn, $projectID;;
            }
            
            
            function getPanelists(){
                global $conn, $projectID;;
                
             // Fetch panelists (level 1)
                $sql = "SELECT u.surname, u.firstname, u.id
                        FROM users u
                        JOIN panelists p
                        ON u.id = p.panelistID
                        WHERE p.projectID = ? AND p.level = 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$projectID]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                // Display panelists
                $index = 0;
                foreach ($result as $row) {
                    $panelistName = $row["surname"] . ", " . $row["firstname"];
                    $panelistID = $row["id"];
        
                    echo "<div class='person'>
                            <span class='name'>$panelistName</span>
                            <div class='dropdown'>
                                <button class='hamburger' onclick='toggleDropdown(\"panelist-$index\")'>☰</button>
                                <div class='dropdown-content' id='dropdown-panelist-$index'>
                                   <form action='' method='GET'>
                                        <input type='hidden' name='panelistID' value='$panelistID'>
                                        <button class ='flat-button' type='submit' name='remove-panelist-btn'>Remove</button>
                                   </form>
                                </div>
                            </div>
                          </div>";
        
                    $index++;
                }
            }
            
             function removePanelist(){
                global $conn, $projectID;
                
                $panelID = $_GET["panelistID"];
                
                try{
                    $conn->beginTransaction();
                    
                    $sql = "DELETE FROM panelists WHERE panelistID = ? AND projectID = ?";
                    $stmt = $conn->prepare($sql);
                    $result = $stmt->execute([$panelID, $projectID]);
                    
                    if($result){
                        $conn->commit();
                        
                        unset($_GET["remove-panelist-btn"]);
                    }
                    
                }
                
                catch(Exception $e){
                    $conn->rollBack();
                    
                    unset($_GET["remove-panelist-btn"]);
                    
                    echo '<script>
                            Swal.fire({
                                 title: "Error",
                                text: "Failed to remove panelist",
                                icon: "error",
                                confirmButtonText: "OK"
                            }).then(function() {
                                window.location.href = window.location.pathname;
                            });
                        </script>';
                }
            }
                    
            if($_SERVER["REQUEST_METHOD"] == "GET"){
                if(isset($_GET["remove-panelist-btn"])){
                   removePanelist();
                }
            }
    ?>
    
        <div class="section">
            <div class="header">
                <label>Adviser</label>
                <button class="add-btn" name="addAdviser" onclick="window.location.href='/add_adviser'">Add</button>
            </div>
            <div class="people-container">
                <?php
                    // Fetch advisers
                    $sql = "SELECT u.surname, u.firstname, u.id
                            FROM users u
                            JOIN advisers a
                            ON u.id = a.adviserID
                            WHERE a.projectID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$projectID]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                    // Display advisers
                    $index = 0;
                    foreach ($result as $row) {
                        $adviserName = $row["surname"] . ", " . $row["firstname"];
    
                        echo "<div class='person'>
                                <span class='name'>$adviserName</span>
                                <div class='dropdown'>
                                    <button class='hamburger' onclick='toggleDropdown(\"adviser-$index\")'>☰</button>
                                    <div class='dropdown-content' id='dropdown-adviser-$index'>
                                        <a href='#'>Remove</a>
                                        <a href='#'>Edit</a>
                                    </div>
                                </div>
                              </div>";
    
                        $index++;
                    }
                ?>
            </div>
        </div>
    
        <div class="section">
            <div class="header">
                <label>Panelist Chairman</label>
    
                <button class="add-btn" name="addAdviser" onclick="window.location.href='/add_chairman'">Add</button>
            </div>
            <div class="people-container">
                <?php
                    // Fetch panelist chairman (level 2)
                    $sql = "SELECT u.surname, u.firstname, u.id
                            FROM users u
                            JOIN panelists p
                            ON u.id = p.panelistID
                            WHERE p.projectID = ? AND p.level = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$projectID, 2]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                    // Display panelist chairman
                    $index = 0;
                    foreach ($result as $row) {
                        $chairmanName = $row["surname"] . ", " . $row["firstname"];
    
                        echo "<div class='person'>
                                <span class='name'>$chairmanName</span>
                                <div class='dropdown'>
                                    <button class='hamburger' onclick='toggleDropdown(\"chairman-$index\")'>☰</button>
                                    <div class='dropdown-content' id='dropdown-chairman-$index'>
                                        <a href='#'>Remove</a>
                                        <a href='#'>Edit</a>
                                    </div>
                                </div>
                              </div>";
    
                        $index++;
                    }
                ?>
            </div>
        </div>
    
        <div class="section">
            <div class="header">
                <label>Panelists</label>
    
                <button class="add-btn" name="addPanelist" onclick="window.location.href='/add_panelist'">Add</button>
            </div>
            <div class="people-container">
                <?php
                    getPanelists();
                ?>
            </div>
        </div>
    
        <div class="section">
            <div class="header">
                <label>Students</label>
                <button class="add-btn">Add</button>
            </div>
            <div class="people-container">
                <?php
                    // Example PHP data for students - replace with actual database results
                    $students = ['Alice Johnson', 'Bob Brown', 'Carol White'];
                    foreach ($students as $index => $student) {
                        echo "<div class='person'>
                                <span class='name'>$student</span>
                                <div class='dropdown'>
                                    <button class='hamburger' onclick='toggleDropdown(\"student-$index\")'>☰</button>
                                    <div class='dropdown-content' id='dropdown-student-$index'>
                                        <a href='#'>Remove</a>
                                        <a href='#'>Edit</a>
                                    </div>
                                </div>
                              </div>";
                    }
                ?>
            </div>
        </div>
    
        <script>
            function toggleDropdown(id) {
                var dropdown = document.getElementById('dropdown-' + id);
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';
                } else {
                    dropdown.style.display = 'block';
                }
            }
    
            // Close the dropdown if clicked outside
            window.onclick = function(event) {
                if (!event.target.matches('.hamburger')) {
                    var dropdowns = document.getElementsByClassName('dropdown-content');
                    for (var i = 0; i < dropdowns.length; i++) {
                        if (dropdowns[i].style.display === 'block') {
                            dropdowns[i].style.display = 'none';
                        }
                    }
                }
            }
        </script>
    </body>
</html>



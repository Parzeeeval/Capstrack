
<!DOCTYPE html>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="pages/header.css">
        <link rel="icon" href="pages/images/CapstrackLogo.png" type="image/png">
    </head>
    
    <body>
    
    <div id="topbar">
        <div class="topbar-left">
            <img src="pages/images/CapstrackLogo.png" alt="Logo" class="logo">
            <h2 class="university-name">CAPSTRACK</h2>
        </div>
        <div class="topbar-right">
            <!--<i class="fas fa-bell notification-icon"></i>--> 
            
            <div class="notification-container">
                <div class="bell" onclick="toggleNotifications()">
                  <i class="fas fa-bell notification-icon"></i>
                  <span class="notification-count" id="notificationCount">3</span>
                </div>
            
                <div class="notification-dropdown" id="notificationDropdown">
                      <?php
                            require_once("connection.php");
                            session_start();
                            
                             try {
                                $sql = "SELECT * FROM notifications WHERE userID = ? ORDER BY id DESC";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$_SESSION["userID"]]);
                                
                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                 if (count( $results) >= 1) {
                                    
                                    $counter = 0;
                                    
                                    foreach($results as $result){
                                        $counter++;
                                        $desc = $result["description"];
                                        $date = $result["date"];
                                        $dateTime = new DateTime($date);
                                        $formattedDate = $dateTime->format('F j, Y, g:i A');
                                        
                                        echo '
                                            <div class="notification-item">
                                                <div class="notification-separator">
                                                    <span class="notification-number">'.$counter.'</span>
                                                </div>
                                                
                                                <div>
                                                  <div class="notification-content">'.$desc.'</div>
                                                  <div class="notification-date">'.$formattedDate.'</div>
                                                </div>
                                            </div>
                                            
                                            <script>
                                                document.getElementById("notificationCount").innerText = '.count($results).';
                                            </script>
                                        ';
                                    }
                                } 
                                
                                else {
                                    echo '
                                        <div class="notification-item">
                                            <div class="notification-separator"></div>
                                            <div>
                                              <div class="notification-content">No Notifications Yet</div>
                                            </div>
                                        </div>
                                        
                                        <script>
                                            document.getElementById("notificationCount").innerText = 0;
                                        </script>
                                    ';
                                }
                            } 
                            
                            catch (Exception $e) {
                                echo '<script>console.log("' . $e->getMessage() . '");</script>';
                            }
                            
                        ?>
                </div>
          </div>
            <a href="/profile" style="text-decoration: none; color: white; display: flex; align-items: center;">
                <i class="fas fa-user-circle" style="font-size: 18px; color: white; margin-right: 8px;"></i>
                <span style="font-size: 16px; color: white; font-weight: 500;">
                    <?php
                        require_once("connection.php");
                        session_start();
                        
                        try {
                            $sql = "SELECT * FROM users WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["userID"]]);
                            
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($user) {
                                $fullname = $user["surname"] . ", " . $user["firstname"] . " " . $user["middlename"];
                                echo htmlspecialchars($fullname); // Sanitize output
                            } else {
                                echo "User not found";
                            }
                        } catch (Exception $e) {
                            echo '<script>console.log("' . $e->getMessage() . '");</script>';
                        }
                    ?>
                </span>
            </a>
        </div>
    </div>
    
    
    
    
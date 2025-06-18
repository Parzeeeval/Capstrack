<?php 
    require_once 'connection.php';
    session_start();
    
    $id = $_SESSION["userID"];
    $type = $_SESSION["accountType"];
    
    try{
        $conn->beginTransaction();
        
        $updateStatus = "UPDATE users u
                 SET u.session = 'offline'
                 WHERE NOT EXISTS (
                     SELECT 1 FROM user_sessions us
                     WHERE us.userID = u.id
                 )";
        $updateStmt = $conn->prepare($updateStatus);
        $updateStmt->execute();
        
       
        /*$sql = "UPDATE users SET session = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute(["offline", $id]);*/
        
        
        //if($result){

            session_unset();
            session_destroy();
            
            // Unset the cookie
            if (isset($_COOKIE["remember_me"])) {
                $token = $_COOKIE["remember_me"];
                setcookie("remember_me", '', time() - 3600, "/"); // Unset the cookie
                
                // Delete remember_me token from database
                $sql = "DELETE FROM remember_tokens WHERE token = ?";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([$token]);
                
                if($result){
                    $conn->commit();
                    
                    echo '<script>
                        window.location.href = "/login";
                      </script>';
                }
                
                else{
                    throw new exception("Error on deleting remember me token");
                }
              
            }
            
            else{
                $conn->commit();
                    
                echo '<script>
                    window.location.href = "/login";
                  </script>';
            }
        //}
    }
    
    catch(Exception $e){
        $conn->rollback();
        die("Connection failed: ". $e->getMessage());
    }

?>
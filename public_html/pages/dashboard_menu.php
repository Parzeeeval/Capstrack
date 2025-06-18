<?php 
    require "connection.php";
    session_start();
                
   
    function menuBar(){
        global $conn;
        
        $type = $_SESSION["accountType"];
        
      
        if($type == "faculty"){
            $sql = "SELECT accessLevel FROM faculty WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_SESSION["userID"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                $level = $result["accessLevel"];
                
                if($level >= 3){
                    echo'
                        <li> <a href="/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a> </li>
                        <li> <a href="/tracking"><i class="fas fa-list"></i> Status</a></li>
                        <li> <a href="/courses"><i class="fas fa-chalkboard-teacher"></i> Class</a> </li>
                        <li> <a href="/upload"><i class="fas fa-user-plus"></i> Invite</a> </li>
                        <li> <a href="/accounts"><i class="fas fa-user-cog"></i> Accounts</a> </li>
                        <li> <a href="/edit_courses"><i class="fas fa-book"></i>Courses</a> </li>
                        <li> <a href="/academicyear_editor"><i class="fas fa-calendar"></i>Year/Semester</a> </li>
                        <li> <a href="/create_templates"><i class="fas fa-paperclip"></i>Templates</a> </li>
                        <li> <a href="/action_logs"><i class="fas fa-history"></i>Action Logs</a> </li>
                        <li> <a href="/reports_coordinators"><i class="fas fa-chart-bar"></i>Reports</a></li>
                        
                        <li class="logout"> <a href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a> </li>
                    ';
                }
                
                else if($level == 2){ 
                    echo'
                        <li> <a href="/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a> </li>
                        <li> <a href="/tracking"><i class="fas fa-list"></i> Status</a></li>
                        <li> <a href="/courses"><i class="fas fa-chalkboard-teacher"></i> Class</a> </li>
                        <li> <a href="/upload"><i class="fas fa-user-plus"></i> Invite</a> </li>
                        <li> <a href="/reports_coordinators"><i class="fas fa-chart-bar"></i>Reports</a></li>
                        
                        <li class="logout"> <a href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a> </li>
                    ';
                }
                
                else{
                     echo'
                        <li> <a href="/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a> </li>
                        <li> <a href="/tracking"><i class="fas fa-list"></i> Status</a></li>
                        <li> <a href="/courses"><i class="fas fa-chalkboard-teacher"></i> Class</a> </li>
                        <li> <a href="/reports_coordinators"><i class="fas fa-chart-bar"></i>Reports</a></li>
                        
                        <li class="logout"> <a href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a> </li>
                    ';
                }
            }
        }
        
        else{
             echo'
                <li> <a href="/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a> </li>
                <li> <a href="/tracking"><i class="fas fa-list"></i> Status</a></li>
                <li> <a href="/class_view"><i class="fas fa-chalkboard-teacher"></i> Class</a> </li>
                <li class="logout"> <a href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a> </li>
            ';
        }
    }
?>

<div id="menu">
    <div class="hamburger">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
    </div>
    <div class="menu-inner">
        <ul>
            <?php 
               menuBar();
            ?>
        </ul>
    </div>
</div>

<div id="content">
    
    

    

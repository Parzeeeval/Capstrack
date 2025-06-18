<?php
    
    // Increase memory limit to 512 megabytes
    ini_set('memory_limit', '1G');

    // Enable error reporting for debugging (remove this in production)
    //error_reporting(E_ALL);
    
    // Get the requested URL
    $request = $_SERVER['REQUEST_URI'];

    // Remove query string
    $request = strtok($request, '?');

    // Remove leading slash
    $request = ltrim($request, '/');
    
    

    session_start();
    
   
    if(isset($_COOKIE["remember_me"])){
        
        if(!isset($_SESSION["userID"])){
            try{    
            
                $token = $_COOKIE["remember_me"];
          
                require_once "pages/connection.php";
                
                $sql = "SELECT t.id, t.token, u.type FROM remember_tokens t JOIN users u ON t.id = u.id WHERE t.token = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$token]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                       
                if($result){
                    $id = $result["id"];
                    $type = $result["type"];
                    
                    $_SESSION["userID"] = $id;
                    $_SESSION["accountType"] = $type;
                    
                    $sql = "SELECT * FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["userID"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result){
                        $type = $result["type"];
                        
                        if($type == "faculty"){
                            
                            $sql = "SELECT * FROM faculty WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["userID"]]);
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if($result){
                                $level = $result["accessLevel"];
                                
                                if($level >= 3){
                                    header("Location: /dashboard_admin");
                                    exit;
                            
                                    echo '
                                        <script>
                                            console.log("here remember me");
                                            console.log("user id in index:'.addslashes($_SESSION["userID"]).'");
                                            history.pushState({}, "", "/dashboard_admin");
                                        </script>
                                    ';
                                }
                                
                                else if($level <= 2){
                                    header("Location: /dashboard_faculty");
                                    exit;
                                    
                                    echo '
                                        <script>
                                            console.log("here remember me");
                                            console.log("user id in index:'.addslashes($_SESSION["userID"]).'");
                                            history.pushState({}, "", "/dashboard_faculty");
                                        </script>
                                    ';
                                }
                            }
                            
                        }
                        
                        else if($type == "student"){
                            header("Location: /dashboard_student");
                            exit;
                            
                            
                            echo '
                                <script>
                                    console.log("here remember me");
                                    console.log("user id in index:'.addslashes($_SESSION["userID"]).'");
                                    history.pushState({}, "", "/dashboard");
                                </script>
                            ';
                        }
                            
        
                        exit;

                        }
                    }
            }
            
            catch(Exception $e){
                echo '<script>
                        console.log("'.addslashes($e->getMessage()).'");
                    </script>';
            }
        }
        
        else if($request == "activate"){
             require 'pages/activate_404.php';
        }
        
        else{
            switch ($request) {
                    case '':
                        if (!isset($_COOKIE["remember_me"]) && !isset($_SESSION["userID"])) {
                            require 'pages/landing.php';
                        } 
                        
                        else {
                            header("Location: /dashboard");
                            echo '<script>history.pushState({}, "", "");</script>';
                        }
                        exit;
                
                    case 'login':
                        if (!isset($_COOKIE["remember_me"]) && !isset($_SESSION["userID"])) {
                            require 'pages/login.php';
                        } else {
                            require 'pages/dashboard.php';
                            echo '<script>history.pushState({}, "", "/dashboard");</script>';
                        }
                        exit;
                
                    case 'logout':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"])) {
                            require 'pages/logout.php';
                        }
                        exit;
                
                    case 'dashboard':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"])) {
                            require_once "pages/connection.php";
        
                            $sql = "SELECT * FROM users WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["userID"]]);
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if($result){
                                $type = $result["type"];
                                
                                if($type == "faculty"){
                                    
                                    $sql = "SELECT * FROM faculty WHERE id = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute([$_SESSION["userID"]]);
                                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if($result){
                                        $level = $result["accessLevel"];
                                        
                                        if($level >= 3){
                                             header("Location: /dashboard_admin");
                                             exit;
                                    
                                            echo '
                                                <script>
                                                    console.log("here remember me");
                                                    console.log("user id in index:'.addslashes($_SESSION["userID"]).'");
                                                    history.pushState({}, "", "/dashboard_admin");
                                                </script>
                                            ';
                                        }
                                        
                                        else if($level <= 2){
                                            header("Location: /dashboard_faculty");
                                            exit;
                                            
                                            echo '
                                                <script>
                                                    console.log("here remember me");
                                                    console.log("user id in index:'.addslashes($_SESSION["userID"]).'");
                                                    history.pushState({}, "", "/dashboard_faculty");
                                                </script>
                                            ';
                                        }
                                    }
                                    
                                }
                                
                                else if($type == "student"){
                                    header("Location: /dashboard_student");
                                    exit;
                                    
                                    echo '
                                        <script>
                                            console.log("here remember me");
                                            console.log("user id in index:'.addslashes($_SESSION["userID"]).'");
                                            history.pushState({}, "", "/dashboard_student");
                                        </script>
                                    ';
                                }
                            } 
                        }
                        
                        else {
                            require 'pages/404.php';    
                        }
                        exit;
                
                    case 'activate':
                        if (!isset($_COOKIE["remember_me"]) && !isset($_SESSION["userID"])) {
                            if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['token']) && !empty($_GET['token'])) {
                                require 'pages/activate_account.php';
                            } else {
                                require 'pages/404.php'; 
                            }
                        } else if (isset($_SESSION["userID"])) {
                            require 'pages/activate_404.php';
                        } else {
                            require 'pages/dashboard.php';
                            echo '<script>history.pushState({}, "", "/dashboard");</script>';
                        }
                        exit;
                    
                    case 'forgot_pass_email':
                        if (!isset($_COOKIE["remember_me"]) && !isset($_SESSION["userID"])) {
                            require 'pages/forgot_password_info.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                    
                        
                    case 'forgot_pass':
                        if (!isset($_COOKIE["remember_me"]) && !isset($_SESSION["userID"])) {
                            if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['code']) && !empty($_GET['code']) && isset($_GET['token']) && !empty($_GET['token'])) {
                                require 'pages/forgot_password.php';
                            } else {
                                require 'pages/404.php'; 
                            }
                        } else if (isset($_SESSION["userID"])) {
                            require 'pages/forgot_404.php';
                        } else {
                            require 'pages/dashboard.php';
                            echo '<script>history.pushState({}, "", "/dashboard");</script>';
                        }
                        exit;
                
                    case 'class_view':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                            require 'pages/student_class_view.php';
                        } else {
                            require 'pages/404.php';    
                        }
                        exit;
                
                    case 'upload':
                    case 'send_email':
                    case 'courses':
                    case 'specializations':
                    case 'sections':
                    case 'groups':
                    case 'group_inner':
                    case 'create_groups':
                    case 'create_sections':
                    case 'create_templates':
                    case 'create_specializations':
                    case 'create_courses':
                    case 'add_adviser':
                    case 'add_panelist':
                    case 'add_chairman':
                    case 'add_student':
                    case 'remove_panelist':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                            require "pages/{$request}.php";
                        } else {
                            require 'pages/404.php';    
                        }
                        exit;
                
                    case 'edit_title_evaluation':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                            require 'pages/title_evaluation_edit.php';
                        } else {
                            require 'pages/404.php';    
                        }
                        exit;
                
                    case 'answer_title_evaluation':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                            require 'pages/title_evaluation_answer.php';
                        } else {
                            require 'pages/404.php';    
                        }
                        exit;
                
                    case 'tracking':
                        if (isset($_SESSION["userID"])) {
                            require 'pages/track_document.php';
                        } else {
                            require 'pages/404.php';    
                        }
                        exit;
                        
                    case 'task':
                        if (isset($_SESSION["userID"])) {
                            require 'pages/student_task.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'edit_defense_invitation':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                             require 'pages/view_invite_recipients.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'send_defense_invitation':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                            require 'pages/invitation_edit.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'answer_defense_invitation':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                            require 'pages/invitation_answer.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                        
                    case 'edit_defense':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                            require 'pages/defense_edit.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                    
                    case 'answer_defense':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                            require 'pages/defense_answer.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                        
                     case 'dashboard_student':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                            require 'pages/dashboard_student.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'dashboard_faculty':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                            require 'pages/dashboard_faculty.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                    
                    case 'dashboard_admin':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                            require 'pages/dashboard_admin.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'profile':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"])) {
                            require 'pages/profile.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'password':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"])) {
                            require 'pages/password.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    //Accounts view for Super Admin
            
                    case 'accounts':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                            require 'pages/accounts_student.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                    
                    case 'accounts_faculty':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                            require 'pages/accounts_faculty.php';
                        } 
                        
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                    //End of accounts view for super admin
                    
                    
                        
                        
                    //Academic year and semester selection
                    case 'academicyear_editor':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/acadyear_semester_edit.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;    
                    //End of academic year and semester selection
                    
                    
                    case 'defense_date':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/defense_date.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit; 
                     
    
                        
                    case 'tracking_number':
                          require 'pages/track_document2.php';
                          exit;
                          
                          
                    case 'edit_courses':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/edit_courses.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'edit_specializations':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/edit_specializations.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'edit_sections':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/edit_sections.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'edit_tags':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/edit_tags.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'action_logs':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/action_logs.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'reports_coordinators':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/reports_coordinators.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'reports_panelists':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/reports_panelists.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'reports_advisers':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/reports_advisers.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                    
                    case 'reports_titles':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/reports_titles.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
                    case 'reports_defense':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/reports_defense.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                                    
                
                    default:
                        require 'pages/404.php'; 
                        exit;
                }
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
     //NEW SWITCH CASE BELOW, DONT CONFUSE THE ONE ABOVE

    else if(!isset($_COOKIE["remember_me"])){
       switch ($request) {
            case '':
                if (!isset($_COOKIE["remember_me"]) && !isset($_SESSION["userID"])) {
                    require 'pages/landing.php';
                } 
                
                else {
                    header("Location: /dashboard");
                    echo '<script>history.pushState({}, "", "");</script>';
                }
                exit;
            
            case 'login':
                if (!isset($_COOKIE["remember_me"]) && !isset($_SESSION["userID"])) {
                    require 'pages/login.php';
                } 
                
                else {
                    require 'pages/dashboard.php';
                    echo '<script>history.pushState({}, "", "/dashboard");</script>';
                }
                exit;
        
            case 'logout':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"])) {
                    require 'pages/logout.php';
                }
                exit;
        
            case 'dashboard':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"])) {
                    require_once "pages/connection.php";

                    $sql = "SELECT * FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$_SESSION["userID"]]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result){
                        $type = $result["type"];
                        
                        if($type == "faculty"){
                            
                            $sql = "SELECT * FROM faculty WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$_SESSION["userID"]]);
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if($result){
                                $level = $result["accessLevel"];
                                
                                if($level >= 3){
                                    header("Location: /dashboard_admin");
                                    exit;
                            
                                    echo '
                                        <script>
                                            console.log("here remember me");
                                            console.log("user id in index:'.addslashes($_SESSION["userID"]).'");
                                            history.pushState({}, "", "/dashboard_admin");
                                        </script>
                                    ';
                                }
                                
                                else if($level <= 2){
                                    header("Location: /dashboard_faculty");
                                    exit;
                                    
                                    echo '
                                        <script>
                                            console.log("here remember me");
                                            console.log("user id in index:'.addslashes($_SESSION["userID"]).'");
                                            history.pushState({}, "", "/dashboard_faculty");
                                        </script>
                                    ';
                                }
                            }
                            
                        }
                        
                        else if($type == "student"){
                            header("Location: /dashboard_student");
                            exit;
                            
                            echo '
                                <script>
                                    console.log("here remember me");
                                    console.log("user id in index:'.addslashes($_SESSION["userID"]).'");
                                    history.pushState({}, "", "/dashboard_student");
                                </script>
                            ';
                        }
                    } 
                }
                
                else {
                    require 'pages/404.php';    
                }
                exit;
        
            case 'activate':
                if (!isset($_COOKIE["remember_me"]) && !isset($_SESSION["userID"])) {
                    if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['token']) && !empty($_GET['token'])) {
                        require 'pages/activate_account.php';
                    } else {
                        require 'pages/404.php'; 
                    }
                } 
                
                else if (isset($_SESSION["userID"])) {
                    require 'pages/activate_404.php';
                } 
                
                else {
                    require 'pages/dashboard.php';
                    echo '<script>history.pushState({}, "", "/dashboard");</script>';
                }
                exit;
                
                
            case 'forgot_pass_email':
                if (!isset($_COOKIE["remember_me"]) && !isset($_SESSION["userID"])) {
                    require 'pages/forgot_password_info.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'forgot_pass':
                if (!isset($_COOKIE["remember_me"]) && !isset($_SESSION["userID"])) {
                    if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['code']) && !empty($_GET['code']) && isset($_GET['token']) && !empty($_GET['token'])) {
                        require 'pages/forgot_password.php';
                    } else {
                        require 'pages/404.php'; 
                    }
                } else if (isset($_SESSION["userID"])) {
                    require 'pages/forgot_404.php';
                } else {
                    require 'pages/dashboard.php';
                    echo '<script>history.pushState({}, "", "/dashboard");</script>';
                }
                exit;
        
            case 'class_view':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                    require 'pages/student_class_view.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                exit;
        
            case 'upload':
            case 'send_email':
            case 'courses':
            case 'specializations':
            case 'sections':
            case 'groups':
            case 'group_inner':
            case 'create_groups':
            case 'create_sections':
            case 'create_specializations':
            case 'create_courses':
            case 'create_templates':
            case 'add_adviser':
            case 'add_panelist':
            case 'add_chairman':
            case 'add_student':
            case 'remove_panelist':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                    require "pages/{$request}.php";
                } else {
                    require 'pages/404.php';    
                }
                exit;
        
            case 'edit_title_evaluation':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                    require 'pages/title_evaluation_edit.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                exit;
        
            case 'answer_title_evaluation':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                    require 'pages/title_evaluation_answer.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                exit;
        
            case 'tracking':
                if (isset($_SESSION["userID"])) {
                    require 'pages/track_document.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                exit;
                
            case 'task':
                if (isset($_SESSION["userID"])) {
                    require 'pages/student_task.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'edit_defense_invitation':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                    require 'pages/view_invite_recipients.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'send_defense_invitation':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                    require 'pages/invitation_edit.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'answer_defense_invitation':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                    require 'pages/invitation_answer.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
                
            case 'edit_capstone_paper':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                    require 'pages/capstone_paper_edit.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'answer_capstone_paper':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                    require 'pages/capstone_paper_answer.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
                
            case 'edit_defense':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                    require 'pages/defense_edit.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
            
            case 'answer_defense':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                    require 'pages/defense_answer.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'dashboard_student':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "student") {
                    require 'pages/dashboard_student.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'dashboard_faculty':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                    require 'pages/dashboard_faculty.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
            
            case 'dashboard_admin':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                    require 'pages/dashboard_admin.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
            
            
            case 'profile':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"])) {
                    require 'pages/profile.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'password':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"])) {
                    require 'pages/password.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
                
            
            //Accounts view for Super Admin
            case 'accounts':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                    require 'pages/accounts_student.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'accounts_faculty':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                    require 'pages/accounts_faculty.php';
                } 
                
                else {
                    require 'pages/404.php';    
                }
                
                exit;
            //End of accounts view for super admin 
                
                
                
                
                
            //Academic year and semester selection
            case 'academicyear_editor':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                     require 'pages/acadyear_semester_edit.php';
                } 
            
                else {
                    require 'pages/404.php';    
                }
                
                exit;    
            //End of academic year and semester selection
            
            

            case 'defense_date':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                     require 'pages/defense_date.php';
                } 
            
                else {
                    require 'pages/404.php';    
                }
                
                exit;    

                
                
                
            case 'tracking_number':
                require 'pages/track_document2.php';
                exit;
                
            
            case 'edit_courses':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                     require 'pages/edit_courses.php';
                } 
            
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'edit_specializations':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                     require 'pages/edit_specializations.php';
                } 
            
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'edit_sections':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                     require 'pages/edit_sections.php';
                } 
            
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'edit_tags':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                     require 'pages/edit_tags.php';
                } 
            
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'action_logs':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                     require 'pages/action_logs.php';
                } 
            
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'reports_coordinators':
                        if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                             require 'pages/reports_coordinators.php';
                        } 
                    
                        else {
                            require 'pages/404.php';    
                        }
                        
                        exit;
                        
            case 'reports_panelists':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                     require 'pages/reports_panelists.php';
                } 
            
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'reports_advisers':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                     require 'pages/reports_advisers.php';
                } 
            
                else {
                    require 'pages/404.php';    
                }
                
                exit;
            
            case 'reports_titles':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                     require 'pages/reports_titles.php';
                } 
            
                else {
                    require 'pages/404.php';    
                }
                
                exit;
                
            case 'reports_defense':
                if (isset($_SESSION["userID"]) && isset($_SESSION["accountType"]) && $_SESSION["accountType"] == "faculty") {
                     require 'pages/reports_defense.php';
                } 
            
                else {
                    require 'pages/404.php';    
                }
                
                exit;
        
            

            default:
                require 'pages/404.php'; 
                exit;
        }

    }
     
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="pages/title_evaluation4.css">
        <link rel="stylesheet" href="pages/title_evaluation_content.css">
        
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <title>Title Evaluation-Page4</title>
            
        <script src="pages/session_tracker.js"></script>
    </head>
    
    <body>
        <?php 
            require_once 'connection.php'; 
            session_start();
        ?>
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        
        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>
            
            <h2 class="section-title">Title Evaluations</h2>
            <p class="course-info">BSIT 3DG1, Web and Mobile Development</p>
            <p class="semester-info">2023 - 2024 (2nd Semester)</p>
            
            <div class="title-dropdown">
                <select id="title-select">
                    <option value="1">Title Number 1</option>
                    <option value="2">Title Number 2</option>
                    <option value="3">Title Number 3</option>
                </select>
            </div>
            
            <div class="Rationale-introduction">
                <h3>Rationale</h3>
            <div class="introduction-title"></div>
                <p class="project-title">Scope and Limitations</p>
            </div>
            
            <div class="bg-info">
                <p>The proposed project, “Capstrack: Capstone Document Tracker and Records Organizer System for  College of Information and Communications Technology” is designed as a web-based project intended to be implemented exclusively within the  College of Information and Communications Technology  of the Bulacan State University (BulSU) - Main Campus. The primary objective of Capstrack is to manage and keep track of the studies produced by the students/undergraduates under the aforementioned college. This proposed system would serve the purpose as a repository and a management ground across the submitted research-related documents. The principal users of this system are  the faculty members assigned under the Research and Development committee of the said college. The undergraduates aspiring to continue the works of their seniors, on the other hand, are only to be granted viewing access in the reserved research materials. 
    
                    <p>However, the proposed project will strictly be implemented under the vice that it shall only archive and track research documents that are under the  College of Information and Communications Technology. Further documents that concern other studies within the university shall only be observed as further addition once the proposed system has been approved and refined. </p>
            </div>
            
            <div class="pagination">
                 <a href="/title_evaluation_introduction">1</a>
                 <a href="/title_evaluation_rationale">2</a>
                 <a href="/title_evaluation_importance">3</a>
                 <a href="#" class="current">4</a>
                 <a href="title_evaluation_result">5</a>
            </div>
    
        <?php include 'footer.php'; ?>
    </body>
</html>

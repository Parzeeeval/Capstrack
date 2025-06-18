<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="pages/title_evaluation1.css">
        <link rel="stylesheet" href="pages/title_evaluation_content.css">
        
        
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <title>Title Evaluation-Page 1</title>
        
        <script src="pages/session_tracker.js"></script>
    </head>
    
    <body>
        <?php 
            require_once 'connection.php'; 
            session_start();
        ?>
        
        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
            
    
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
            
            <div class="section-introduction">
                <h3>Introduction</h3>
            <div class="introduction-title"></div>
                <p class="project-title">Capstrack: Capstone Document Tracker and Records Organizer System for College of Information and Communications Technology</p>
            </div>
            
            <div class="team-info">
                <p>As part of our capstone project, our group have drafted three title proposals and would greatly appreciate your feedback on them. Your expertise and insights would be invaluable to us as we refine our project focus.</p>
                <p><strong>Team Leader:</strong> <a href="#">Last, Name</a></p>
                <p><strong>Members:</strong></p>
                <ul class="team-members">
                    <li><a href="#">Last, Name</a></li>
                    <li><a href="#">Last, Name</a></li>
                    <li><a href="#">Last, Name</a></li>
                    <li><a href="#">Last, Name</a></li>
                </ul>
                <p><strong>Capstone Coordinator:</strong> <a href="#">Last, Name, Title</a></p>
            </div>


            <div class="pagination">
                 <a href="#" class="current">1</a>
                 <a href="/title_evaluation_rationale">2</a>
                 <a href="/title_evaluation_importance">3</a>
                 <a href="/title_evaluation_scope">4</a>
                 <a href="title_evaluation_result">5</a>
            </div>
    
        <?php include 'footer.php'; ?>
    </body>
</html>

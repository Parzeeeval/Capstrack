<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="pages/title_evaluation2.css">
        <link rel="stylesheet" href="pages/title_evaluation_content.css">
        
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <title>Title Evaluation-Page 2</title>
        
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
                <p class="project-title">Background of the Study</p>
            </div>
            
            <div class="bg-info">
                <p>Within the College of Information and Communications Technology at Bulacan State University (BulSU) - Main Campus, third-year students participate in the Research and Capstone Project, a core academic endeavor. This project involves developing a practical system to meet the needs of a client. As time progresses, a substantial number of project documents have accumulated. However, these documents lack organization, making it difficult to effectively access and track them.</p>
            
                <p>To address this issue, implementing a digital archival system is critically important. Such a system would serve as a centralized platform for storing and retrieving project documents. By digitizing the archival process, the risk of document loss or misplacement would be minimized and the overall efficiency of document management would be enhanced. Moreover, beyond the immediate benefits of improved accessibility and organization, adopting a digital archival system carries broader implications for the academic community. It would promote collaboration and innovation by facilitating easier access to project resources.</p>
            
                <p>In essence, introducing a digital archival system represents a practical solution to the challenges of document management within the College of Information and Communications Technology through the process of streamlining access of project documentation.</p>
            </div>
            
            <div class="pagination">
                <a href="/title_evaluation_introduction">1</a>
                <a href="#" class="current">2</a>
                <a href="/title_evaluation_importance">3</a>
                <a href="/title_evaluation_scope">4</a>
                <a href="title_evaluation_result">5</a>
            </div>
    
        <?php include 'footer.php'; ?>
    </body>
</html>

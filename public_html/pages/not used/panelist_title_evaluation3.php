<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pages/title_evaluation3.css">
    <link rel="stylesheet" href="pages/title_evaluation_content.css">
    
    <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
    <title>Title Evaluation-Page 3</title>
        
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
            <p class="project-title">Importance of the Study</p>
        </div>
        
        <div class="bg-info">
            <p>The significance of Capstrack lies in its capacity to address a significant challenge faced by the College of Information and Communications Technology at Bulacan State University. With a burgeoning number of student-produced capstone research documents, there's a pressing need for an efficient system to manage and track these materials. Capstrack serves this purpose by providing a centralized, web-based platform for storing, organizing, and retrieving capstone research documents. It ensures that faculty members can oversee capstone initiatives effectively and that undergraduates have access to valuable scholarly resources. This system not only streamlines administrative tasks but also supports collaboration and knowledge sharing within the academic community, ultimately contributing to the college's commitment to academic excellence.</p>
            <p>Additionally, Capstrack’s tracking capabilities enable administrators to monitor the progress of projects and assist those who would delve to further research in pinpointing the improvements that can be made to enhance the quality of papers that they have provided. With proper refinement of such works, it will be made clearer for other students as they use these archived documents to form innovative ideas that can add unto developing a new system that differs from what has been made and has ironed out the flaws.</p>
        </div>
        
        <div class="pagination">
             <a href="/title_evaluation_introduction">1</a>
             <a href="/title_evaluation_rationale">2</a>
             <a href="#" class="current">3</a>
             <a href="/title_evaluation_scope">4</a>
             <a href="title_evaluation_result">5</a>
        </div>

    <?php include 'footer.php'; ?>
</body>
</html>

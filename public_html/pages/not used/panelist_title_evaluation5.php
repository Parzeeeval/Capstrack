<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pages/title_evaluation5.css">
    <link rel="stylesheet" href="pages/title_evaluation_content.css">
    
    <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
    <title>Title Evaluation-Page 5</title>
        
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
        
        <div class="Evaluation-introduction">
            <h3>Evaluation</h3>
            <p class="project-title">Please choose one option:</p> 
            <div class="introduction-title">
                <label>
                    <input type="radio" name="evaluation" value="accept">
                    <span class="custom-bullet accept"></span> Accept
                </label>
                <label>
                    <input type="radio" name="evaluation" value="reject">
                    <span class="custom-bullet reject"></span> Reject
                </label>
                <label>
                    <input type="radio" name="evaluation" value="needs-improvement">
                    <span class="custom-bullet needs-improvement"></span> Needs Improvement
                </label>
            </div>
        </div>

        <div class="bg-info">
             <textarea id="feedback" name="feedback" rows="8" placeholder="Optional: Type your feedback or comments..."></textarea>
        </div>
        
        <div class="pagination">
             <a href="/title_evaluation_introduction">1</a>
             <a href="/title_evaluation_rationale">2</a>
             <a href="/title_evaluation_importance">3</a>
             <a href="/title_evaluation_scope">4</a>
             <a href="#" class="current">5</a>
        </div>
        
        <button class="form-button" type="submit" name="submit-button">Submit</button>


    <?php include 'footer.php'; ?>
</body>
</html>

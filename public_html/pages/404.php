<html>
    <head>
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 Not Found</title>
        
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background: #f2f2f2;
                color: #333;
            }
            .container {
                text-align: center;
            }
            .container h1 {
                font-size: 10em;
                margin: 0;
            }
            .container h2 {
                font-size: 2em;
                margin: 0;
            }
            .container p {
                margin: 20px 0;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                font-size: 1em;
                color: #fff;
                background: #007bff;
                text-decoration: none;
                border-radius: 5px;
                transition: background 0.3s ease;
            }
            .btn:hover {
                background: #0056b3;
            }
            .image-container img {
                max-width: 400%;
                width: 500px;
                margin-bottom: 20px; /* Adjust if needed */
            }
        </style>
    </head>
    
    <body>
        <?php 
            require_once "connection.php";
            session_start();
            
            $defaultErrorMessage = "Sorry, but the page you are looking for does not exist, has been removed, or is temporarily unavailable.";
            $errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : $defaultErrorMessage;
        ?>

        <div class="container">
            <!-- Image for 404 error -->
            <div class="image-container">
                <img src="pages/images/404ICON.png" alt="404 Error Icon">
            </div>

            <h2>Page Not Found</h2>
            <br>
            
            <h3 style="color: red;"><?php echo $errorMessage; ?></h3>
            
            <br>
            <br>
            
            <?php 
                if(isset($_SESSION["canViewFile"]) && $_SESSION["canViewFile"] == true){
                    //dont put a button
                }
                
                else{
                    echo '
                        <a href="/" class="btn">Go to Homepage</a>
                    ';
                }
            ?>

        </div>
    </body>
</html>

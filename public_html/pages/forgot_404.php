<html>
    <head>
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 Not Found</title>
        
        <script src="pages/session_tracker.js"></script>
        
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
                flex-direction: column;
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
                font-size: 1.1em;
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
                max-width: 500px;
                width: 300%;
                margin-top: -30px; /* Adjust this if needed */
            }
        </style>
    </head>
    
    <body> 
        <!-- Image for 404 error -->
        <div class="image-container">
            <img src="pages/images/404ICON.png" alt="404 Error Icon">
        </div>

        <div class="container">
            <!-- Title Text -->
         
            <h2>Page Not Found</h2>

            <!-- Description Text -->
            <p>An account is currently logged into this browser/device. Please log out before resetting the password.</p>

            <!-- Homepage Link -->
            <a href="/" class="btn">Go to Homepage</a>
        </div>
    </body>
</html>

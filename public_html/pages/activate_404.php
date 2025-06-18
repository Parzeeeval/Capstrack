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
                max-width: 500%;
                width: 500px;
                margin-bottom: 20px; /* Adjust this if needed */
            }
        </style>
    </head>
    
    <body>
        <div class="container">
            <!-- Image for 404 error or session issue -->
            <div class="image-container">
                <img src="pages/images/404ICON.png" alt="Error Icon">
            </div>

            <h1>Another Account is Currently Logged in</h1>
            <h2>Page Not Found</h2>
            
            <br>
            
            <p style="font-size: 20px; color: red;">An account is currently logged into this browser/device. Make sure to log out first, before activating an account.</p>
            <a href="/" class="btn">Go to Homepage</a>
        </div>
    </body>
</html>

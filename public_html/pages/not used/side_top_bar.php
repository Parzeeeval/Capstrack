<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar with Top Bar</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: Arial, sans-serif;
        }

        /* Topbar Styles */
        #topbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 80px;
            background-color: #444;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-sizing: border-box;
            z-index: 1000;
        }

        #topbar .topbar-left {
            display: flex;
            align-items: center;
        }

        #topbar .logo {
            margin-top: 20px;
            width: 80px;
            height: 80px;
        }

        #topbar .university-name {
            color: white;
            margin-left: 15px;
            font-size: 24px;
            line-height: 80px; /* Center align the text vertically */
        }

        #topbar .dropdown {
            position: relative;
            display: inline-block;
        }

        #topbar .dropdown button {
            background-color: #333;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }

        #topbar .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        #topbar .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        #topbar .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        #topbar .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Sidebar Styles */
        #menu {
            position: fixed;
            top: 80px; /* Position the sidebar just below the topbar */
            left: 0;
            width: 80px;
            height: calc(100vh - 80px); /* Adjust height to exclude the topbar */
            background-color: #333;
            transition: width 0.3s;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
            box-sizing: border-box;
            z-index: 1000;
        }

        #menu.expanded {
            width: 200px;
        }

        .logo {
            width: 60px;
            height: 60px;
            transition: width 0.3s, margin-left 0.3s;
            margin-bottom: 20px;
        }

        #menu.expanded .logo {
            width: 60px;
            height: 60px;
            margin-left: auto;
            margin-right: auto;
        }

        .hamburger {
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 24px;
            margin-bottom: 20px;
            transition: margin-left 0.3s;
            z-index: 1;
        }

        #menu.expanded .hamburger {
            margin-left: auto;
        }

        .line {
            width: 30px;
            height: 3px;
            background-color: white;
            margin: 3px 0;
        }

        .menu-inner {
            opacity: 0;
            transition: opacity 0.3s;
            width: 100%;
            padding: 0 10px;
        }

        #menu.expanded .menu-inner {
            opacity: 1;
        }

        .menu-inner ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .menu-inner ul li {
            margin: 10px 0;
            cursor: pointer;
            text-align: center;
            color: white;
        }

        /* Content Area Styles */
        #content {
            margin-top: 100px; /* Space for topbar */
            margin-left: 100px; /* Space for sidebar */
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div id="topbar">
        <div class="topbar-left">
            <img src="images/Logo.png" alt="Logo" class="logo">
            <h2 class="university-name">Bulacan State University</h2>
        </div>
        <div class="dropdown">
            <button>Menu</button>
            <div class="dropdown-content">
                <a href="#">My Profile</a>
                <a href="#">Logout</a>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div id="menu">
        <img src="images/CapstrackLogo.png" alt="Logo" class="logo">
        <div class="hamburger">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
        <div class="menu-inner">
            <ul>
                <li>Menu Item 1</li>
                <li>Menu Item 2</li>
                <li>Menu Item 3</li>
                <li>Menu Item 4</li>
                <li>Menu Item 5</li>
                <li>Menu Item 6</li>
            </ul>
        </div>
    </div>

    <!-- Main Content Area -->
    <div id="content">
        <h1>Main Content</h1>
        <p>This is the main content area. Replace this with your actual page content.</p>
    </div>

    <script>
        var menu = document.getElementById('menu');
        var hamburger = document.querySelector('.hamburger');

        hamburger.addEventListener('click', function() {
            menu.classList.toggle('expanded');
        });
    </script>
</body>
</html>

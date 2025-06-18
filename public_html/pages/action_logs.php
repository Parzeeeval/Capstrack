<?php 
    session_start();
    require 'connection.php';
    
    function getLogs(){
        global $conn;
        
        try{
            $sql = "SELECT * FROM action_logs ORDER BY date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $logs= $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if(count ($logs) >= 1){
                foreach($logs as $log){
                    $action = $log["action"];
                    $user= $log["userID"];
                    $date= $log["date"];
                    
                    $stmt = $conn->prepare("SELECT email, firstname, middlename, surname FROM users WHERE id = ?");
                    $stmt->execute([$user]);
                    $name = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $fullname = $name["surname"] . ", " . $name["firstname"] . " " . $name["middlename"] . " - ({$name["email"]})";
                    

                    $readableDate = date("F j, Y g:i A", strtotime($date));
                    
                    echo "<tr class='log-row'>"
                        . "<td class='action'>" .  htmlspecialchars($action)  . "</td>"
                        . "<td class='fullname-email'>" . htmlspecialchars($fullname) . "</td>"
                        . "<td class='date'>" . htmlspecialchars($readableDate) . "</td>"
                    . "</tr>";
                }
            }
        }
        
        catch(Exception $e){
            // Handle exception (optional)
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Activity Logs</title>
        <link rel="stylesheet" href="pages/card_layout.css">
        <link rel="icon" href="pages/images/favicon.ico" type="image/x-icon">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f4f7fc;
                color: #333;
                margin: 0;
                padding: 0;
            }
            h1 {
                text-align: center;
                font-size: 32px;
                color: #333;
                margin-top: 40px;
                font-weight: 600;
                letter-spacing: 1px;
            }
            .divider {
                width: 50%;
                margin: 20px auto;
                border-top: 2px solid #ffad60;
            }
            table {
                width: 95%;
                margin: 20px auto;
                border-collapse: collapse;
                background-color: #ffffff;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            th, td {
                padding: 12px 20px;
                text-align: left;
                border: 1px solid #ddd;
                font-size: 16px;
            }
            th {
                background-color: #ffad60;
                color: white;
                font-weight: 500;
                font-size: 26px;
                text-align: center;
            }
            tr:nth-child(even) {
                background-color: lightgray;
            }
            tr:hover {
                background-color: #f1f1f1;
                transform: scale(1.01);
                transition: transform 0.3s ease;
            }
            .search-input {
                width: 70%;
                margin: 10px auto;
                padding: 12px;
                border: 1px solid black;
                border-radius: 5px;
                font-size: 22px;
                display: block;
                box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
            }
            
            .pagination {
                display: flex;
                justify-content: center;
                margin: 20px auto;
                margin-top: 40px;
            }
            .pagination button {
                padding: 8px 16px;
                margin: 0 5px;
                background-color: #ffad60;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                transition: background-color 0.3s ease;
            }
            .pagination button:hover {
                background-color: #e68a3b;
            }
            .pagination button.disabled {
                background-color: #ddd;
                cursor: not-allowed;
            }
            .pagination button.active {
                background-color: #ff8c42;
            }
        </style>
    </head>
    <body>
        <?php require 'header.php'; ?>
        <?php require 'menu.php'; ?>

        <h1>Capstrack Accounts Activity Logs</h1>
        <div class="divider"></div>

        <input type="text" id="search-input" class="search-input" placeholder="Search logs by action, action with a person's name, fullname, email, or date...">

        <table id="logs-table">
            <thead>
                <tr>
                    <th>Action Logged</th>
                    <th>Fullname and Email Address</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php getLogs(); ?>
            </tbody>
        </table>

        <div class="pagination" id="pagination"></div>

        <script>
            const rowsPerPage = 10;
            const tableBody = document.querySelector('#logs-table tbody');
            const rows = Array.from(document.querySelectorAll('.log-row'));
            const pagination = document.getElementById('pagination');

            function renderTable(page) {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                rows.forEach((row, index) => {
                    row.style.display = index >= start && index < end ? '' : 'none';
                });
            }

            function renderPagination(totalRows) {
                const totalPages = Math.ceil(totalRows / rowsPerPage);
                pagination.innerHTML = '';

                for (let i = 1; i <= totalPages; i++) {
                    const button = document.createElement('button');
                    button.textContent = i;
                    button.addEventListener('click', () => {
                        renderTable(i);
                        document.querySelectorAll('.pagination button').forEach(btn => btn.classList.remove('active'));
                        button.classList.add('active');
                    });
                    if (i === 1) button.classList.add('active');
                    pagination.appendChild(button);
                }
            }

            function searchTable() {
                const query = document.getElementById('search-input').value.toLowerCase();
                let visibleRowIndex = 0;
            
                rows.forEach(row => {
                    const action = row.querySelector('.action').textContent.toLowerCase();
                    const fullnameEmail = row.querySelector('.fullname-email').textContent.toLowerCase();
                    const date = row.querySelector('.date').textContent.toLowerCase();
            
                    const match = 
                        action.includes(query) ||
                        fullnameEmail.includes(query) ||
                        date.includes(query);
            
                    if (match) {
                        row.style.display = '';
                        row.style.backgroundColor = (visibleRowIndex % 2 === 0) ? 'white' : 'lightgray';
                        visibleRowIndex++;
                    } else {
                        row.style.display = 'none';
                    }
                });
            
                renderPagination(visibleRowIndex);
            }

            document.getElementById('search-input').addEventListener('input', searchTable);

            renderTable(1);
            renderPagination(rows.length);
        </script>

        <?php require 'footer.php'; ?>
    </body>
</html>


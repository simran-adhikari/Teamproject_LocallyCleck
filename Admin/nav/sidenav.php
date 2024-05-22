<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <title>Trader Dashboard</title>
    <style>
        .side-nav {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
            top: 50;
            left: 0;
            padding-top: 20px;
        }
        .side-nav a {
            display: block;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
        }
        .side-nav a:hover {
            background-color: #34495e;
        }
        .notification {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
        }
        .notification.success {
            background-color: #d4edda;
            color: #155724;
        }
        .notification.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .update-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
        }
        .update-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="side-nav">
    <a href="adminhome.php">Approve Trader</a>
    <a href="adminproduct.php">Approve Product</a>
    <a href="http://127.0.0.1:8080/apex/f?p=101:LOGIN_DESKTOP:1878367224011:::::">Management Dashboard</a>
</div>
</body>
</html>
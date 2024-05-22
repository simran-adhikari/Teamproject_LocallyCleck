<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/traderstyles.css">
    <title>Signin/Signup</title>
</head>
<body>
        <!---------------------------------SIGNIN----------------------------------------- -->
        <div class="wrapper">
            <nav class="nav">
                <div class="nav-logo">
                    <a href="../Home.php">
                        <img src="../resource/logo.png" alt="Logo">
                      </a>
                </div>
                <div class="nav-menu-btn">
                    <i class="bx bx-menu" onclick="myMenuFunction()"></i>
                </div>
            </nav>
            <div class="nav-button">
                <button class="btn white-btn" id="loginBtn" onclick="login()">Admin</button>
            </div>
            <form action="adminsignin.php" method="post">
            <div class="form-box">
                <div class="login-container" id="login">
                    <div class="top">
                    
                    <div class="input-box">
                        <input type="text" class="input-field" placeholder="Username or Email" name="username_or_email" required>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="password" class="input-field" placeholder="Password" name="password" required>
                        <i class="bx bx-lock-alt"></i>
                    </div>
                    <div class="input-box">
                        <input type="submit" class="submit" value="Sign In">
                    </div>
                </div>
                </div>
                </form>
                


        


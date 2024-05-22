<nav class="navbar">
        <div class="navdiv">
            <div class="logo">
                <a href="TraderDashboard.php"><img src="traderresource/logo.png" alt="logo"></a>
            </div>
            <div class="extra">
                <p><a href="TraderDashboard.php">Locally<span>Cleck</span></a></p>
            </div>
            <div class="welcome">
                <p>Welcome, <?php echo $traderName; ?></p> <!-- Display trader's name -->
            </div>

            <ul>
                <li><a href="traderProfile.php"><i class="fa-solid fa-user"></i>Edit Profile</a></li>
                <li><button><a href="http://127.0.0.1:8080/apex/f?p=101:LOGIN_DESKTOP:1878367224011:::::">Report</a></button></li>
                <li><button><a href="../Logout.php">Logout</a></button></li>
               
            </ul>
        </div>
    </nav>
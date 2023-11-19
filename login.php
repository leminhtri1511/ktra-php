<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style_login.css">
</head>

<body>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <form id="loginForm" method="post" action="home.php" onsubmit="return validateLogin()">
        <h3>Login Here</h3>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="password" required>

        <button type="submit" name="login">Log In</button>
        <p id="errorMessage" style="color: red; display: none;">Invalid username or password</p>
        <!-- <br><br> -->

        <!-- <p>Don't have an account? <a href="register.php">Register</a></p> -->
    </form>
    <script>
        function validateLogin() {
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;

            if (username !== "letri" || password !== "123") {
                document.getElementById("errorMessage").style.display = "block";
                return false;
            }
            return true;
        }
    </script>
</body>

</html>
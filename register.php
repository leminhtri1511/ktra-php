<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link rel="stylesheet" href="style_login.css">
</head>

<body>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <form id="registerForm" method="post" action="register.php" onsubmit="return validateRegister()">
        <h3>Register here</h3>

        <label for="newUsername">New Username</label>
        <input type="text" id="newUsername" name="newUsername" placeholder="New username" required>

        <label for="newPassword">New Password</label>
        <input type="password" id="newPassword" name="newPassword" placeholder="New password" required>

        <button type="submit" name="register">Register</button>
        <br><br>
        <p>Already have an account? <a href="login.php">Login</a></p>

        <!-- Dòng thông báo thành công -->
        <p id="registerSuccessMessage" style="color: green; display: none;">Registration successful!</p>

        <!-- Dòng thông báo lỗi -->
        <p id="registerErrorMessage" style="color: red; display: none;">Username already exists</p>

        <script>
            function validateRegister() {
                var newUsername = document.getElementById("newUsername").value;

                // Đọc nội dung tệp văn bản
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'registered_accounts.txt', true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var existingAccounts = xhr.responseText.split("\n");

                        // Kiểm tra xem username đã tồn tại hay chưa
                        if (existingAccounts.includes(newUsername)) {
                            document.getElementById("registerErrorMessage").style.display = "block";
                            document.getElementById("registerSuccessMessage").style.display = "none";
                        } else {
                            document.getElementById("registerErrorMessage").style.display = "none";
                            document.getElementById("registerSuccessMessage").style.display = "block";
                        }
                    }
                };
                xhr.send();

                return !document.getElementById("registerErrorMessage").style.display === "block";
            }
        </script>
    </form>
</body>

</html>
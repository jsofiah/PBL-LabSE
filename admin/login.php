<?php
require '../config.php';
session_start();

$message = "";

$saved_username = isset($_COOKIE['username']) ? $_COOKIE['username'] : "";
$saved_password = isset($_COOKIE['password']) ? $_COOKIE['password'] : "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM vw_admin_user WHERE username = $1 AND password = $2";
    $result = pg_query_params($conn, $sql, [$username, $password]);

    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);

        $_SESSION['id_admin'] = $row['id'];
        $_SESSION['username'] = $row['username'];

        if (isset($_POST['remember'])) {
            setcookie("username", $username, time() + (86400 * 30), "/");
            setcookie("password", $_POST["password"], time() + (86400 * 30), "/");
        } else {
            setcookie("username", $username, time() + (86400 * 15), "/");
            setcookie("password", $_POST["password"], time() + (86400 * 15), "/");

        }

        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Username atau Password salah";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Lab SE</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/styleLogin.css">

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("eyeIcon");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            }
        }
    </script>
</head>

<body>
    <div class="container-fluid overflow-hidden main-wrapper">
        <div class="row g-0 align-items-center justify-content-center">
            <div class="col-lg-7 col-md-12 left-section">

                <div class="logo-container">
                    <img src="../img/Logo-login.png" alt="Lab SE Logo" class="logo-img">
                </div>

                <div class="login-card">
                    <form method="POST">
                        <div class="mb-4">
                            <label for="username" class="form-label">Username</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="username" 
                                name="username" 
                                placeholder="Masukkan username"
                                value="<?php echo htmlspecialchars($saved_username); ?>"
                                required>
                        </div>

                        <div class="mb-4 position-relative">
                            <label for="password" class="form-label">Password</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="Masukkan password"
                                value="<?php echo htmlspecialchars($saved_password); ?>"
                                required>

                            <span class="toggle-password" onclick="togglePassword()">
                                <i id="eyeIcon" class="fa-solid fa-eye-slash"></i>
                            </span>
                        </div>

                        <div class="mb-4 form-check">
                            <input 
                                type="checkbox" 
                                class="form-check-input" 
                                id="rememberMe" 
                                name="remember"
                                <?php if ($saved_username) echo "checked"; ?>>
                            <label class="form-check-label" for="rememberMe">Remember Me</label>
                        </div>

                        <button type="submit" name="login" class="btn btn-login">Login</button>

                        <?php if ($message): ?>
                            <p style="color:red; margin-top:12px; font-size:0.9rem;"><?= $message ?></p>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-flex right-section">
                <svg width="717" height="809" viewBox="0 0 717 809" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <clipPath id="clip-shape">
                            <path d="M0 175.585C0 147.971 22.3858 125.585 50 125.585H87.8741C114.946 125.585 137.1 104.039 137.855 76.9778L138.645 48.6071C139.4 21.5458 161.554 0 188.626 0H667C694.614 0 717 22.3858 717 50V650.158C717 677.316 695.322 699.508 668.172 700.144L637.828 700.856C610.678 701.492 589 723.684 589 750.842V759C589 786.614 566.614 809 539 809H50C22.3858 809 0 786.614 0 759V175.585Z"/>
                        </clipPath>
                    </defs>
                    <image href="../img/bg_login.jpg" width="717" height="809" preserveAspectRatio="xMidYMid slice" clip-path="url(#clip-shape)" />
                </svg>
            </div>
        </div>
    </div>
</body>
</html>

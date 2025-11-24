<?php
require '../config.php';
session_start();

$message = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM vw_admin_user WHERE username = $1 AND password = $2";
    $result = pg_query_params($conn, $sql, [$username, $password]);

    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);

        $_SESSION['id_admin'] = $row['id'];  // ambil id dari tabel/view
        $_SESSION['username'] = $row['username'];

        header("Location: dashboard.php");
        exit;
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link rel="stylesheet" href="css/styleLogin.css">
</head>
<body>

<div class="login-box">
    <h2>Login Admin</h2>

    <form method="POST">
        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <?php if ($message): ?>
        <p class="error"><?= $message ?></p>
        <?php endif; ?>

        <button type="submit" name="login" class="btn">Login</button>
    </form>
</div>

</body>
</html>

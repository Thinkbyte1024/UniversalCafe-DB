<?php
require "../include/connections.php";
require_once "../include/UCEncryption.php";

// Mulai sesi
ob_start();
session_start();

// Periksa jika sesi yang berisi nilai ID atau cookie "Ingat Saya" ada
if (isset($_SESSION["id"])) {
    header("location: /");
    exit();
} elseif (isset($_COOKIE['MEMBERID_']) && !empty($_COOKIE["MEMBERID_"])) {
    $cookie_user_id = UCEncryption::decrypt_cookie($_COOKIE["MEMBERID_"]);
    $cookie_query = "SELECT user_id, full_name, user_name FROM accounts WHERE user_id = $1";

    if ($cookie_stmt = pg_prepare($connection, "validate_cookie_check", $cookie_query)) {
        if ($cookie_query_result = pg_execute($connection, "validate_cookie_check", array($cookie_user_id))) {

            // Jika akun ada maka masukkan informasi akun kedalam sesi
            if (pg_num_rows($cookie_query_result) == 1) {
                $cookie_result_array = pg_fetch_assoc($cookie_query_result);
                $_SESSION["id"] = $cookie_result_array["user_id"];
                $_SESSION["fullname"] = $cookie_result_array["full_name"];
                $_SESSION["username"] = $cookie_result_array["user_name"];

                pg_close($connection); // Tutup koneksi
                header("location: /");
                exit();
            }
        }
    }
}

// Variabel
$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validasi jika nama pengguna kosong
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your user name";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validasi jika kata sandi kosong
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validasi kredensial
    if (empty($username_err) && empty($password_err)) {
        $query = "SELECT full_name, user_id, user_name, user_pass FROM accounts WHERE user_name = $1";

        if ($query_stmt = pg_prepare($connection, "validate_info", $query)) {
            if ($result = pg_execute($connection, "validate_info", array(trim($_POST["username"])))) {
                if (pg_num_rows($result) == 1) {
                    $cred_result = pg_fetch_assoc($result);

                    // Verifikasi kata sandi dan tambahkan pada session
                    if (password_verify(trim($_POST["password"]), $cred_result["user_pass"])) {
                        session_start();

                        $_SESSION["loggedin"] = true;
                        $_SESSION["fullname"] = $cred_result["full_name"];
                        $_SESSION["id"] = $cred_result["user_id"];
                        $_SESSION["username"] = $cred_result["user_name"];

                        // Periksa jika "Ingat Saya" telah menyala
                        if ($_POST["remember-user"] == '1' || $_POST["remember-user"] == 'on') {
                            $cookie_days = 30;
                            $cookie_value = UCEncryption::encrypt_cookie($cred_result["user_id"]);
                            setcookie("MEMBERID_", $cookie_value, time() + ($cookie_days * 24 * 60 * 60), "/");
                        }

                        header("location: /");
                        exit();
                    } else {
                        $login_err = "Invalid username or password";
                    }
                } else {
                    $login_err = "Invalid username or password";
                }
            } else {
                echo "Something went wrong, please try again";
            }
        }
    }
    pg_close($connection);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Universal Cafe | Login</title>

    <!-- Stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/custom-style.css">
</head>
<body class="site">

<!-- Navigation Bar -->
<nav class="navbar">
    <div class="navbar-brand">
        <div class="navbar-item">
            <h1 class="subtitle">Login</h1>
        </div>
    </div>
</nav>
<div class="hero is-primary">
    <div class="hero-body">
        <h1 class="title is-2 is-spaced has-text-centered">Universal Cafe</h1>
        <h2 class="subtitle has-text-centered">Cafe Database</h2>
    </div>
</div>

<!-- Main Content -->
<section class="section site-content">
    <div class="container is-fullwidth has-text-centered">
        <span <?php if (!empty($login_err)) {
            echo "class=\"tag is-large is-danger is-light\"";
        } ?>><?php echo $login_err; ?></span>
    </div>

    <div class="container">
        <form method="post" action="">
            <div class="field">
                <label class="label" for="username">Username</label>
                <div class="control">
                    <input class="input <?php if (!empty($username_err)) {
                        echo "is-danger";
                    } ?>" name="username" type="text"/>
                </div>
                <span class="help <?php if (!empty($username_err)) {
                    echo "is-danger";
                } ?>"><?php echo $username_err; ?></span>
            </div>
            <div class="field">
                <label class="label" for="password">Password</label>
                <div class="control">
                    <input class="input <?php if (!empty($password_err)) {
                        echo "is-danger";
                    } ?>" name="password" type="password">
                </div>
                <span class="help <?php if (!empty($password_err)) {
                    echo "is-danger";
                } ?>"><?php echo $password_err; ?></span>
            </div>
            <div class="field">
                <label class="checkbox">
                    <input name="remember-user" type="checkbox">
                    Remember me
                </label>
            </div>
            <div class="field is-grouped">
                <button class="button is-info" type="submit">Login</button>
            </div>
            <div class="field">
                <p class="subtitle is-6">Don't have an account yet? <a href="../register/">Sign Up</a></p>
            </div>
        </form>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="content has-text-centered">
        <p>
            Copyright &copy; 2021 <strong>Muhammad Aditya P. D.</strong>
        </p>
    </div>
</footer>
</body>
</html>
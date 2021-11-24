<?php
require_once "../include/connections.php";

// Variabel
$fullname = $username = $password = $verify_password = "";
$fullname_err = $username_err = $password_err = $verify_pass_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please provide a username";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        $query = "SELECT user_id FROM accounts WHERE user_name = $1 ";

        // Periksa jika username telah digunakan
        if ($user_stmt = pg_prepare($connection, "check_user", $query)) {
            if ($result = pg_execute($connection, "check_user", array(trim($_POST["username"])))) {
                if (pg_num_rows($result) == 1) {
                    $username_err = "Username is already taken";
                } else {
                    $username = trim($_POST["username"]);
                }
            }
        }
    }

    // Validasi nama lengkap
    if (empty(trim($_POST["fullname"]))) {
        $fullname_err = "Please enter your name";
    } else {
        $fullname = trim($_POST['fullname']);
    }

    // Validasi kata sandi
    if (empty(trim($_POST['password']))) {
        $password_err = "Please enter a password";
    } elseif (strlen(trim($_POST['password'])) < 8) {
        $password_err = "Password must have at least 8 characters or more";
    } else {
        $password = trim($_POST['password']);
    }

    // Validasi konfirmasi kata sandi
    if (empty(trim($_POST['verify-pass']))) {
        $verify_pass_err = "Please enter your password again";
    } else {
        $verify_password = trim($_POST['verify-pass']);
        if (empty($password_err) && ($password != $verify_password)) {
            unset($_POST);
            $verify_pass_err = "Password did not match";
        }
    }

    // Periksa jika ada kesalahan input sebelum masuk kedalam database
    if (empty($fullname_err) && empty($username_err) && empty($password_err) && empty($verify_pass_err)) {
        $query = "INSERT INTO accounts (full_name, user_name, user_pass) VALUES ($1, $2, $3)";

        // Menyimpan kredensial pengguna dan enkripsi kata sandi
        $stmt = pg_query_params($connection, $query, array($fullname, $username, password_hash($password, PASSWORD_BCRYPT)));

        if ($stmt != false) {
            header("location: /login");
        } else {
            echo "Something went wrong, please try again.";
        }
    }
    pg_close($connection); // Tutup koneksi
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Universal Cafe | Register</title>

    <!-- Stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/custom-style.css">
</head>
<body class="site">

<!--  Navigation Bar  -->
<nav class="navbar">
    <div class="navbar-brand">
        <div class="navbar-item">
            <h1 class="subtitle">Register</h1>
        </div>
    </div>
</nav>

<!-- Main Content -->
<section class="section site-content">
    <div class="container">
        <form class="control" method="post" action="">
            <div class="field">
                <label class="label" for="fullname">Your full name</label>
                <div class="control">
                    <input class="input <?php if (!empty($fullname_err)) {
                        echo "is-danger";
                    } ?>" name="fullname" type="text" placeholder="Ex.: John Doe">
                </div>
                <span <?php if (!empty($fullname_err)) {
                    echo "class=\"help is-danger\"";
                } ?>><?php echo $fullname_err; ?></span>
            </div>
            <div class="field">
                <label class="label" for="username">Username</label>
                <div class="control">
                    <input class="input <?php if (!empty($username_err)) {
                        echo "is-danger";
                    } ?>" name="username" type="text" placeholder="Ex.: john_doe">
                </div>
                <span <?php if (!empty($username_err)) {
                    echo "class=\"help is-danger\"";
                } ?>><?php echo $username_err; ?></span>
            </div>
            <div class="field">
                <label class="label" for="password">Password</label>
                <input class="input <?php if (!empty($password_err)) {
                    echo "is-danger";
                } ?>" name="password" type="password">
                <span <?php if (!empty($password_err)) {
                    echo "class=\"help is-danger\"";
                } ?>><?php echo $password_err; ?></span>
            </div>
            <div class="field">
                <label class="label" for="verfiy-pass">Verify password</label>
                <input class="input <?php if (!empty($verify_pass_err)) {
                    echo "is-danger";
                } ?>" name="verify-pass" type="password">
                <span <?php if (!empty($verify_pass_err)) {
                    echo "class=\"help is-danger\"";
                } ?>><?php echo $verify_pass_err; ?></span>
            </div>
            <div class="field is-grouped">
                <div class="control">
                    <button class="button is-primary" type="submit">Register</button>
                    <button class="button" onclick="window.location.href = '/login'" type="reset">Go back</button>
                </div>
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
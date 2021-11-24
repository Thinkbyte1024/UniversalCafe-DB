<?php
// Memulai sesi
session_start();

if (!isset($_SESSION["id"])) {
    header("location: login/");
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Universal-Cafe</title>

    <!-- Stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/custom-style.css">

    <!-- JavaScript -->
    <script src="js/navbar-control.js"></script>
</head>
<body class="site">
<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <div class="navbar-item">
            <h1 class="subtitle">Universal Cafe Database</h1>
        </div>
        <a role="button" class="navbar-burger" data-target="navMenu" aria-label="menu" aria-expanded="false">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>
    <div class="navbar-menu" id="navMenu">
        <div class="navbar-end">
            <div class="navbar-item">
            <span class="icon-text">
                <span class="icon">
                    <i class="bi-person-fill"></i>
                </span>
                <span><?php echo $_SESSION["fullname"]; ?></span>
            </span>
            </div>
            <div class="navbar-item">
                <a class="button is-danger is-light" href="logout">Logout</a>
            </div>
        </div>
    </div>
</nav>

<section class="section site-content">
    <div class="table-container">
        <table class="table is-striped is-fullwidth is-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
            </tr>
            </thead>
            <tbody>
            <?php
            require_once "include/connections.php";

            $query = "SELECT * FROM places ORDER BY place_id";

            // Mengambil data tabel tempat cafe (places) dari database
            if ($result = pg_query($connection, $query)) {
                for ($num = 0; $num <= pg_num_rows($result) - 1; $num++) {
                    if ($resultArray = pg_fetch_assoc($result, $num)) {
                        echo "<tr>\n";
                        foreach ($resultArray as $value) {
                            if ($resultArray != false) {
                                echo "<td>{$value}</td>";
                            } else {
                                break;
                            }
                        }
                        echo "</tr>\n";
                    }
                }
                pg_close($connection);
            }
            ?>
            </tbody>
        </table>
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
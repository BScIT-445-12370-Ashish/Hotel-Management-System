<?php

$server   = "localhost";
$username = "bluebird_user";
$password = "password";
$database = "bluebirdhotel";

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    $err = mysqli_connect_error();
    die("
    <div style='font-family:sans-serif;max-width:600px;margin:60px auto;padding:30px;border:2px solid #e74c3c;border-radius:10px;background:#fff5f5;'>
        <h2 style='color:#e74c3c;'>&#9888; Database Not Set Up</h2>
        <p><strong>Error:</strong> " . htmlspecialchars($err) . "</p>
        <hr>
        <h3>How to fix this:</h3>
        <ol>
            <li>Open <strong>phpMyAdmin</strong> (go to <code>http://localhost/phpmyadmin</code>)</li>
            <li>Click the <strong>Import</strong> tab at the top</li>
            <li>Click <strong>Choose File</strong> and select <code>bluebirdhotel.sql</code> from this project folder</li>
            <li>Click <strong>Go / Import</strong></li>
            <li>Refresh this page &mdash; it should work now!</li>
        </ol>
        <p style='color:#888;font-size:13px;'>Default staff login: <strong>Admin@gmail.com</strong> / password: <strong>1234</strong></p>
    </div>
    ");
}
?>

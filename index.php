<?php
$env = parse_ini_file(".env");


$conn = new mysqli($env["MYSQL_HOST"], $env["MYSQL_USER"], $env["MYSQL_PASS"], $env["MYSQL_DB"]) or die("Cannot connect to server");
$query = "CREATE TABLE IF NOT EXISTS rants (uname VARCHAR(50), content VARCHAR(100), time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";

$result = $conn->query($query);

if ($result) echo "<script>console.log('Table added successfully')</script>";
else echo "<script>console.log('There are problems: " . $conn->error . "')</script>";

if (isset($_POST['btn_submit'])) {
    $uname = $_POST['uname'];
    $cont = $_POST['cont'];

    $query = "INSERT INTO rants (uname, content) VALUES ('" . $uname . "', '" . $cont . "')";
    $result = $conn->query($query); 
    if ($result) echo "<script>console.log('Contents added successfully')</script>";
    else echo "<script>console.log('There are problems: " . $conn->error . "')</script>";
}


?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ranters</title>
        <script>
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        </script>
    </head>
    <body>
        <center>
            <form method="POST">
                <input type="text" name="uname" id="uname" placeholder="Enter your stage name" autocomplete="off"/>
                <br />
                <textarea name="cont" id="cont" autocomplete="off" placeholder="Enter your rant+"></textarea>
                <br />
                <input type="submit" name="btn_submit" value="Submit" />
            </form>
        </center>
    </body>
</html>
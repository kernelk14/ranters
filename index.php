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

function printCard($row) {
    $uname = htmlspecialchars(trim($row['uname']));
    $cont = htmlspecialchars(trim($row['content']));

    if (strlen(trim($cont)) > 50) {
        echo <<<EOT
            <script>
                console.log('$uname exceeded 50 words');
            </script>
            <style>
                .cardscroll {
                    overflow-y: scroll;
                    scrollbar-color: #383838 #181818;;
                    scrollbar-width: thin;
                }
                .cardscroll::-webkit-scrollbar-track {background-color: #181818;}
            </style>
            <div class="card cardscroll">
                <h4>$uname</h4>
                <p>$cont</p>
            </div>
        EOT;
    }
    else {
        echo <<<EOT
        <div class="card">
            <h4>$uname</h4>
            <p>$cont</p>
        </div>
        EOT;
    }
}


?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ranters</title>
        <link rel="stylesheet" href="style.css" />
        <script>
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        </script>
    </head>
    <body>
        <center>
            <form method="POST">
                <input class="textbox" type="text" name="uname" id="uname" placeholder="Enter your stage name" autocomplete="off"/>
                <br />
                <textarea class="textarea" name="cont" id="cont" autocomplete="off" placeholder="Enter your rant"></textarea>
                <br />
                <input type="submit" name="btn_submit" value="Submit" />
            </form>
        </center>
        <hr />
        <div class="content-container">
            <?php
                $query = "SELECT * FROM rants ORDER BY time_created DESC";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    printCard($row);
                }
            ?>
        </div>
    </body>
</html>
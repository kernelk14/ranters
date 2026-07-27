<?php
include "./config.php";

$env = parse_ini_file(".env");
$conn = new mysqli($env["MYSQL_HOST"], $env["MYSQL_USER"], $env["MYSQL_PASS"], $env["MYSQL_DB"]) or die("Cannot connect to server");

$query = "CREATE TABLE IF NOT EXISTS rants (uname VARCHAR(50), content VARCHAR(100), time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";

$result = $conn->query($query);

if ($result) echo "<script>console.log('Table added successfully')</script>";
else echo "<script>console.log('There are problems: " . $conn->error . "')</script>";

if (isset($_POST['btn_submit'])) {
    $uname = htmlspecialchars($_POST['uname']);
    $cont = htmlspecialchars($_POST['cont']);

    if (strlen($cont) > 0) {
        if (strlen($uname) < 1) {
            echo <<<EOT
                <script>
                    window.alert("PLEASE ENTER A USERNAME!");
                </script>
            EOT;
        } else {
            $query = "INSERT INTO rants (uname, content) VALUES ('" . $uname . "', '" . $cont . "')";
            $result = $conn->query($query); 
            if ($result) echo "<script>console.log('Contents added successfully')</script>";
            else echo "<script>console.log('There are problems: " . $conn->error . "')</script>";
        }
    } else {
        echo <<<EOT
            <script>
                window.alert("PLEASE ENTER SOMETHING!");
            </script>
        EOT;
    }
}



function printCard($row) {
    $time_ident = "";
    $dbTime = $row['time_created']; // This is in UTC
    
    // Parse as UTC
    $createdDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dbTime, new DateTimeZone('UTC'));

    if ($createdDateTime === false) {
        error_log("Failed to parse: $dbTime");
        $createdTime = time();
    } else {
        $createdDateTime->setTimezone(new DateTimeZone('Asia/Manila'));
        $createdTime = $createdDateTime->getTimestamp();
    }

    $currentTime = time();
    $diffSeconds = $currentTime - $createdTime;

    
    if ($diffSeconds < 0) {
        $diffSeconds = 0;
    }

    $diffMinutes = floor($diffSeconds / 60);
    $diffHours   = floor($diffSeconds / 3600);
    $diffDays    = floor($diffSeconds / 86400);

    echo "<script>console.log('". $diffMinutes . ", " . $diffHours . ", " . $diffDays . "')</script>";

    if ($diffDays >= 1) {
        $time_ident = $diffDays . " day" . ($diffDays == 1 ? "" : "s") . " ago";
    } elseif ($diffHours >= 1) {
        $time_ident = $diffHours . " hour" . ($diffHours == 1 ? "" : "s") . " ago";
    } else {
        $time_ident = $diffMinutes . " " . ($diffMinutes == 1 ? "minute" : "minutes") . " ago";
    }

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
                <div class="content-dets">
                    <h4 class="uname-color">$uname</h4>
                    <p class="subtle">Time is not implemented yet</p>
                </div>
                <div class="main-content">
                    <p class="main-content-text">$cont</p>
                </div>
            </div>
        EOT;
    }
    else {
        echo <<<EOT
        <div class="card">
            <div class="content-dets">
                <h4 class="uname-color">$uname</h4>
                <p class="subtle">Time is not implemented yet</p>
            </div>
            <div class="main-content">
                <p class="main-content-text">$cont</p>
            </div>
        </div>
        EOT;
    }
}


?>

<!DOCTYPE html>

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
                <input class="action-btn" type="submit" name="btn_submit" value="Submit" />
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
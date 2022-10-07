<?php
// Shutdown page for Raspberry PI using Python and NGINX
// =========================================================
// https://www.pihome.eu/2017/10/11/enable-rebootshutdown-rpi-web/
//
?>
<html>
    <head>
        <title>Baby Logger &#x1F476; - Turn off page</title>
    </head>
    <body>

        <p>Shutdown using php</p>
        <p>Result:</p>
            <?php 
            // Try via PHP
            try {
                exec("/usr/bin/sudo /sbin/shutdown -h now", $output, $status);
                print("Result:". $status);
                }
            catch(Exception $e) {
                print("Failed to shutdown via PHP: <br>". $e->getMessage());
                }
            ?>

        <p>Shutdown using python</p>
        <p>Result:</p>
    
            <?php
            // Try via Python
            try {
                exec("python3 /var/www/shutdown.py");
                }
            catch(Exception $e) {
                print("Failed to shutdown with Python: <br>". $e->getMessage());
                }

        ?>

    </body>
</html>
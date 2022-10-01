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
        <?php 
            //Execute Shutdown
            print("Shutdown using exec");
            try {
                exec("python /var/www/shutdown.py");
                }
            catch(Exception $e) {
                print("Failed to shutdown with exec: <br>". $e->getMessage());
            }
        ?>
    </body>
</html>
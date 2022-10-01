<?php 
// Shutdown page for Raspberry PI using Python and NGINX
// =========================================================
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
                exec('sudo shutdown -h now');
                }
            catch(Exception $e) {
                print("Failed to shutdown with exec: <br>". $e->getMessage());
            }

            //Execute Shutdown
            print("Shutdown using system");
            try {
                system('sudo shutdown -h now');
                }
            catch(Exception $e) {
                print("Failed to shutdown with system: <br>". $e->getMessage());
                }
        ?>
    </body>
</html>
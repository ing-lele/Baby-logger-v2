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
            exec('sudo shutdown -h now');
            //Execute Shutdown
            print("Shutdown using system");
            system('sudo shutdown -h now');
        ?>
    </body>
</html>
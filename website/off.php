<?php
// Shutdown page for Raspberry PI using Python and NGINX
// =========================================================
// https://www.pihome.eu/2017/10/11/enable-rebootshutdown-rpi-web/
//

print("<html><head><title>Baby Logger &#x1F476; - Turn off page</title></head><body>")

//Execute Shutdown
print("Shutdown using php");
    
// Try via PHP
exec('/usr/bin/sudo /sbin/shutdown -h now', $output);
print("Result:", $output);

// Try via Python
print("Shutdown using python script");
try {
    exec("python /var/www/shutdown.py");
    }
catch(Exception $e) {
    print("Failed to shutdown with exec: <br>". $e->getMessage());
}

print("</body></html>")
?>
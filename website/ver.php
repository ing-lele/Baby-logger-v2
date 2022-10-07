<?php 
// PHP Test page
// =========================================================

// Save executed command
$output = exec('ls -la');

//Print the return value
echo $output;

phpinfo(); 
?>
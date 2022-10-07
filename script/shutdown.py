# =========================================================
# Scope: 
# -- Shutdown Raspberry Pi
# =========================================================
command = "/usr/bin/sudo /sbin/shutdown -h now"
import subprocess

# Executing shell
process = subprocess.Popen(command.split(), stdout=subprocess.PIPE)
output = process.communicate()[0]

# Show log 
print ("Executing:", command)
print ("Output of command:", output)
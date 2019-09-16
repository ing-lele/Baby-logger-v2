# Baby-logger
Logs bodily functions and displays them on a webpage.

The project was forked from [tenmonkeys' Babby-logger project](https://github.com/tenmonkeys/Baby-logger).

You'll need a Raspberry Pi running Raspbian for this project. I used a Pi Zero W, but most any model would do.
For the physical part you'll also require some pushbuttons. I used 3 30mm arcade-style pushbuttons for ease of use. (Big buttons let the user see and press the button rather than small ones - small ones work fine for testing.)

For this project, I connected the buttons to pins 13, 19, and 26 and to a common ground. You may choose any GPIO pins you want, but be sure to note which ones you used and adjust the numbers in the Python script accordingly.

I configured the pi to run headless since it does not require any rich user feedback. I did install a red 3mm LED on pin 16 as a status light. It gives the user feedback as to which event was logged and could be setup to alert the user if there was an error.

**Updating OS and installing necessary packages.**

Run
```
sudo apt-get update
sudo apt-get upgrade -y
```

That might take some time.
Install Python 3's PIP tool for installing Pythong libraries.
```
sudo apt-get install python3-pip
```
Using PIP, install the Python 3 MySQL library.
```
sudo pip3 install pymysql
```

**Creating a MySQL database.**

Log into your MySQL dashboard, then create a database with your user interface or issue the SQL command below.
```
CREATE DATABASE babylogger;
```
I called mine "babylogger". If you choose another name be sure to substitute it in the python script and the webpage.

Select the database and create a user that will be submitting data to the table.
```
USE babylogger;
CREATE USER 'logger'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON babylogger.* TO 'logger'@'localhost';
FLUSH PRIVILEGES;
quit
```
This will create a user "logger" and give them the priveleges to work with our database. Substitute the `password` for your actual password.
Next, create a table that will be used for logging events.
```
USE babylogger;
CREATE TABLE buttondata (id INT, tdate DATE, ttime TIME, type TEXT);
quit
```
Now you have a MySQL database setup, all that's left is to populate it.

**Setting up the python script.**

Now either copy the python script to your Pi, or create one and copypaste the code by hand. Create a nice and neat folder for the project (who knows, maybe you'll expand it later on).
```
cd ~
mkdir Logger
cd Logger
```
Use your editor of choice to create the script. Personally, I prefer vi/vim but if you aren't sure just use nano.
```
nano buttons_script.py
```

Paste in the contents of the python script and CTRL-X then Y to exit and save. Don't forget to change GPIO numbers, database name, and password to match what you created. At this point you should also change the button names so that they correspond to the labels whatever you decide those to be.
You can try running the script with
```
sudo python3 babylogger.py
```
Push any of your buttons, if you don't get any error messages - CTRL-C to end the script.

Then open up your MySQL database again and check if anything has been written to the database.
Hopefully it will now show you the date, time, and which button has been pressed. Now you can close the MySQL interface and move on to setting up the webpage.

**Webpage.**

On your webhost, create a subdirectory for your project. 
```
mkdir babylogger
```
Now you can either copy the index.php page directly into the folder or with your editor of choice manually copy-paste it in.
```
touch index.php
nano index.php
```
Paste the contents there and edit the appropriate fields:
* db_host
* db_user
* db_passwd
* database
* GPIO pins

Ctrl-x and then Y to save the file (in nano)/

At this point you should be greeted with a webpage with a table whenever you go to your webhost.

If everything is working well, there's one more thing to do.

**Making sure it runs in the background on Pi startup**

If you want to have it automatically run itself whenever your Pi starts up, you can create a SystemD service file.
Create an empty file with your editor of choice:
```
sudo nano /lib/systemd/system/babylogger.service
```
and then paste in the following (changing the name and the path if you've changed any of those)
```
[Unit]
Description=Baby Logger Service
After=multi-user.target

[Service]
Type=simple
ExecStart=/usr/bin/python3 /home/pi/Logger/buttons_script.py
User=pi
Restart=always

[Install]
WantedBy=multi-user.target
```
Ctrl-X and then Y to save the file.
Check permissions on the file, you may need to 

Tell the system to use this serviced service:
```
sudo systemctl daemon-reload
```

Now start the script
```
sudo systemctl start babylogger.service
```

Enable autorun
```
sudo systemctl enable babylogger.service
```
and you're done.

To check on the status of your service (ensure it's running)
```
sudo journalctl -u babylogger.status
```

This should have you up and running. Issue a graceful reboot and your system should come back up with your ```babylogger``` service running

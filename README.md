# Baby-logger
Logs bodily functions and displays them on a locally hosted webpage.

The MySQL part of this project was almost completely borrowed from [jjpfin's excellent Raspberry Pi temperature logger](https://www.instructables.com/id/Raspberry-PI-and-DHT22-temperature-and-humidity-lo/).

You'll need a Raspberry Pi running the latest version of Raspbian for this project. I used a Pi 2, but pretty much any model would do. A Pi Zero W would be ideal for the task with its on-board WiFi and low current draw. For the physical part you'll also require a number of pushbuttons. We used 3, but changing that number would be fairly trivial. In the original project they were configured in a pulled-up configuration, meaning that one contact was connected to the ground and one to the GPIO pin. You could easily change that to an active-high and pull the GPIO pins down instead.

In my case the buttons were connected to GPIO pins 20, 16, and 26 and to a common GND on the other terminal. You may choose any GPIO pins you want, but be sure to note which ones you used and adjust the numbers in the Python script accordingly.

You can run the Pi headless for this or you can have a full set-up. At this point I assume you already have a Pi running the latest Raspbian and are either logged in via SSH or in the terminal directly on the Pi.

**Updating Raspbian and installing needed packages.**

Run
```
sudo apt-get update
sudo apt-get upgrade
```

That might take some time. Then install the compiler and some necessary Python libraries.
```
sudo apt-get install build-essential python-dev python-openssl
```
Install pip and wiringpi if you're running Raspbian Lite. I'm running my script on Python 3, but if you don't need to run those pesky NeoPixels - everything else can easily run on Python 2.
```
sudo apt-get install python3-pip wiringpi
```
Next install MySQL and Apache+addons.
```
sudo apt-get install mysql-server apache2 php7.0 php7.0-curl php7.0-gd php7.0-imap php7.0-json php7.0-mcrypt php7.0-mysql php7.0-opcache php7.0-xmlrpc libapache2-mod-php7.0
```
The last one is a Python 3 MySQL library that will have to be installed via pip.
```
sudo pip3 install pymysql
```

**Creating a MySQL database.**

Log into the MariaDB console with
```
sudo mysql -u root -p -h localhost
```
The root password is empty by default, so just hit enter. Then create a database with
```
CREATE DATABASE buttons;
```
Mine is called "buttons", if you choose any other name - be sure to substitute it in the python script and the webpage. Then select the database and create a user that will be submitting data to the table.
```
USE buttons;
CREATE USER 'logger'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON buttons.* TO 'logger'@'localhost';
FLUSH PRIVILEGES;
quit
```
This will create a user "logger" and give them the priveleges to work with our database. Substitute the `password` for your actual password.
Next you can log in as the new user, check if credential are correct and create a table that will be used to the job.
```
sudo mysql -u logger -p -h localhost
USE buttons;
CREATE TABLE buttondata (tdate DATE, ttime TIME, type TEXT);
quit
```
Now you have a MySQL database, all that's left is to fill it.

**Setting up the python script.**

Now either copy the python script to your Pi, or create one and copypaste the code by hand. Create a nice and neat folder for the project (who knows, maybe you'll expand it later on).
```
cd ~
mkdir Logger
cd Logger
nano buttons_script.py
```
Then paste in the contents of the python script and ctrl-x then y to exit and save. Don't forget to change GPIO numbers, database name and password. At this point you should also change the button names so that they correspond to the labels whatever you decide those to be.
You can try running the script with
```
sudo python3 buttons_script.py
```
Push any of your buttons, if you don't get any error messages - ctrl-c to end the script.

Then open up MariaDB again and check if anything has been written to the database.
```
sudo mysql -u logger -p -h localhost
USE buttons;
SELECT * FROM buttondata;
```
Hopefully it will now show you the date, time and which button has been pressed. Now you can close the MariaDB console with
```
quit
```
and move on to setting up the webpage.

**Webpage.**

First reload Apache with
```
sudo service apache2 restart
```
Now you can either copy the index.php page directly into the /var/www/html/ folder or manually copy-paste it with
```
cd /var/www/html/
sudo nano index.php
```
and paste the contents there. Be sure to change your password from "password" to whatever you chose it to be and change the database name if you changed it. Ctrl-x and then y to save the file. Now you can delete the old index.html file with

```
sudo rm index.html
```
At this point you should be greeted with a webpage with a table whenever you go to your Pi's IP address. If everything is working well, there's one more thing to do.

**Making sure it runs in the background on Pi startup**

If you want to have it automatically run itself whenever your Pi starts up, you can create a SystemD service file.
Create an empty file with
```
sudo nano /lib/systemd/system/button.service
```
and then paste in the following (changing the name and the path if you've changed any of those)
```
[Unit]
Description=Button Service
After=multi-user.target
Conflicts=getty@tty1.service

[Service]
Type=simple
ExecStart=/usr/bin/python3 /home/pi/Logger/buttons_script.py
StandardInput=tty-force

[Install]
WantedBy=multi-user.target
```
Ctrl-x and then y to save the file. Run
```
sudo systemctl daemon-reload
```
to reload the daemon and now you can start the script with
```
sudo systemctl start button.service
```
and enable autorun with
```
sudo systemctl enable button.service
```
and you're done. Hopefully nothing went horribly wrong in the process. And if it did, a quick Google was able to save you.

Almost forgot. If you do want to use a NeoPixel to give some visual feedback on which exact button was pushed - you'll have to [install Adafruit python libraries for it](https://learn.adafruit.com/adafruit-neopixel-uberguide/python-circuitpython) and uncomment the relevant lines in the python script. We found it quite useful.

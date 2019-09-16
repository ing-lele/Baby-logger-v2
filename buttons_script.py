#! /usr/bin/python3
#IMPORT STATEMENTS
import RPi.GPIO as GPIO
import os
import sys
import time
import pymysql
import datetime
pymysql.install_as_MySQLdb()
import MySQLdb

#----------------------------------------------------------
#CONFIGURATION SETTINGS, edit these to reflect your project
led_pin = 16
pee_pin = 13
poo_pin = 19
fed_pin = 26

db_host = "mysql.webserver.com"
db_user = "logger"
db_pass = "password"
db_name = "babylogger"

#----------------------------------------------------------
#SETUP
db = MySQLdb.connect(db_host, db_user, db_pass, db_name)
curs = db.cursor()

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
GPIO.setup(pee_pin, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(poo_pin, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(fed_pin, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(led_pin, GPIO.OUT)

#----------------------------------------------------------

def blink(sec):
    p = GPIO.PWM(led_pin, 1000)
    p.start(50)
    time.sleep(sec)
    p.stop()
    time.sleep(.1)
    
#---------------------------------------------------------

def fade(dir=1):
    p = GPIO.PWM(led_pin, 1000)
    p.start(100)
    if (dir > 0):
        for duty in range(75, -1, -3):
            p.ChangeDutyCycle(duty)
            time.sleep(.05)
    else:
        for duty in range(0, 75, 3):
            p.ChangeDutyCycle(duty)
            time.sleep(.05)

    p.stop()
    
#---------------------------------------------------------

time.sleep(1.0)
#STATUS LED:
#blinks .-. = R in Morse Code ("Ready")
blink(.1)
blink(.3)
blink(.1)
print("Baby Logger running...")
    
while True:
    input_state1 = GPIO.input(13) #PEE
    input_state2 = GPIO.input(19) #POO
    input_state3 = GPIO.input(26) #FED

#CHECK FOR SHUTDOWN REQ - press all three buttons to trigger clean shutdown
    if ((input_state1 == False) and (input_state2 == False) and (input_state3 == False)):
        #STATUS LED:
        # blinks for 5 seconds then stays on. Turns off when shutdown is complete.
        blink(1)
        blink(1)
        blink(1)
        blink(1)
        blink(1)
        GPIO.output(led_pin, GPIO.HIGH)
        os.system("sudo shutdown -h now")
#PEE
    elif input_state1 == False:
        curr_date = datetime.datetime.now().strftime("%Y-%m-%d")
        curr_time = datetime.datetime.now().strftime("%H:%M:%S")
        print(curr_date + " " + curr_time + " - Event logged: Pee")
        try:
            curs.execute("""INSERT INTO babylogger (tdate, ttime, type) VALUES (%s, %s, 'pee')""", (curr_date, curr_time))
            #STATUS LED:
            # blinks .--. = P in Morse Code ("Pee")
            blink(.1)
            blink(.3)
            blink(.3)
            blink(.1)
            db.commit()
        except Exception as ex:
            print(ex)
#POO
    elif input_state2 == False:
        curr_date = datetime.datetime.now().strftime("%Y-%m-%d")
        curr_time = datetime.datetime.now().strftime("%H:%M:%S")
        print(curr_date + " " + curr_time + " - Event logged: Poo")
        try:
            curs.execute("""INSERT INTO babylogger (tdate, ttime, type) VALUES (%s, %s, 'poo')""", (curr_date, curr_time))
            #STATUS LED:
            # blinks --- = O in Morse Code ("pOop")
            blink(.3)
            blink(.3)
            blink(.3)
            db.commit()
        except:
            print("Error: the database is being rolled back")

#FED
    elif input_state3 == False:
        curr_date = datetime.datetime.now().strftime("%Y-%m-%d")
        curr_time = datetime.datetime.now().strftime("%H:%M:%S")
        print(curr_date + " " + curr_time + " - Event logged: Fed")
        try:
            curs.execute("""INSERT INTO babylogger (tdate, ttime, type) VALUES (%s, %s, 'fed')""", (curr_date, curr_time))
            #STATUS LED:
            # fades to bright then back to dim ("F for fade?")
            fade(-1)
            fade(1)
            db.commit()
        except:
            print("Error: the database is being rolled back")
            sys.exit(0)

    time.sleep(0.1)
db.close()

GPIO.cleanup()

### IngLele Fork at 26 Jun 2022 from https://github.com/tommygober/Baby-logger
# =========================================================
# Changes: 
# * Updated 3 colored switches
# * Use RGB LED for confirmation
# * Record ON / OFF time for the switches
# * [TO DO] Pi Camera v1.1 screenshot during every switch
# =========================================================

#! /usr/bin/python3
#IMPORT STATEMENTS
from asyncio.windows_events import NULL
from turtle import end_fill
import RPi.GPIO as GPIO
import os
import sys
import time
import pymysql
import datetime
pymysql.install_as_MySQLdb()
import MySQLdb
import mysql_variables          #Import MySQL variable

#----------------------------------------------------------
#CONFIGURATION SETTINGS (via GPIO number)
print('Config Settings')

pee_led_pin = 20        #Green LED
#         PIN#34        #GND
fed_led_pin = 16        #Blue LED
poo_led_pin = 12        #Red LED

#            PIN#9      GND
pee_switch_pin = 17     #Green Switch
fed_switch_pin = 27     #Blue Switch
poo_switch_pin = 22     #Red Switch
#            PIN#17     VCC +3.3V

# MySQL variable are defined in MySQLdb.py module
# MySQLdb.db_host 
# MySQLdb.db_user 
# MySQLdb.db_pass 
# MySQLdb.db_name 

#----------------------------------------------------------
#SETUP
db = MySQLdb.connect(mysql_variables.db_host, mysql_variables.db_user, mysql_variables.db_pass, mysql_variables.db_name)
curs = db.cursor()

GPIO.setwarnings(False)
GPIO.cleanup()
GPIO.setmode(GPIO.BCM)

# Setup Switch (normally closed) - PullDown configuration
# Switch Open = GPIO.HIGH = +3.3V (VCC) = End event
# Switch Closed = GPIO.LOW = 0V (GND via PullDown) = Active event
# Info https://electrosome.com/using-switch-raspberry-pi/
GPIO.setup(pee_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_DOWN) 
GPIO.setup(fed_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)
GPIO.setup(poo_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)

#Setup LED
GPIO.setup(pee_led_pin, GPIO.OUT)
GPIO.setup(fed_led_pin, GPIO.OUT)
GPIO.setup(poo_led_pin, GPIO.OUT)


#----------------------------------------------------------
# Flash RGB LED
def flash_led(category, state):
    # pee
    if (category=="pee", state=="start"): GPIO.output(pee_led_pin, GPIO.HIGH)
    elif (category=="pee", state=="stop"): GPIO.output(pee_led_pin, GPIO.LOW)
    # fed
    elif (category=="fed", state=="start"): GPIO.output(fed_led_pin, GPIO.HIGH)
    elif (category=="fed", state=="stop"): GPIO.output(fed_led_pin, GPIO.LOW)
    #poo
    elif (category=="poo", state=="start"): GPIO.output(poo_led_pin, GPIO.HIGH)
    elif (category=="poo", state=="stop"): GPIO.output(poo_led_pin, GPIO.LOW)

    else:
        #Flash loop
        print("Flash RGB LED: ", category)
        n = 5
        while n:
            GPIO.output(pee_led_pin, GPIO.HIGH)
            time.sleep(.5)
            GPIO.output(pee_led_pin, GPIO.LOW)

            GPIO.output(fed_led_pin, GPIO.HIGH)
            time.sleep(.5)
            GPIO.output(fed_led_pin, GPIO.LOW)

            GPIO.output(poo_led_pin, GPIO.HIGH)
            time.sleep(.5)
            GPIO.output(poo_led_pin, GPIO.LOW)

            time.sleep(.5)
            n = n-1

#Start up of LED
flash_led("starting","")

#---------------------------------------------------------
# Write DB information
def write_event(category, state):
    print("Event logged: ", category.upper() , " - ", state.upper()," at ", datetime.datetime.now())
    try:
        curs.execute("""INSERT INTO babylogger (category, state) VALUES ('%s','%s')""", (category.lower(), state.lower()))
        #Set LED
        flash_led(category, state)
        db.commit()
    except Exception as ex:
        print(ex)
        print("Error: the database is being rolled back --- ", category.upper(), " ", state.upper(), " @ " datetime.datetime.now())
        #Set LED
        flash_led("Error writing DB","")
        sys.exit(0)

#---------------------------------------------------------

time.sleep(1)
print("Baby Logger running...")

while True:
    # DEBUG - print value
    print(datetime.datetime.now())
    input_state_pee = GPIO.input(pee_switch_pin) #PEE
    input_state_fed = GPIO.input(fed_switch_pin) #FED
    input_state_poo = GPIO.input(poo_switch_pin) #POO

    # read value
    input_state_pee = GPIO.input(pee_switch_pin) #PEE
    input_state_fed = GPIO.input(fed_switch_pin) #FED
    input_state_poo = GPIO.input(poo_switch_pin) #POO
    
    #---------------------------------------------------------
    #PEE - START
    if (input_state_pee == GPIO.LOW):
        print('Event logged: PEE - Start at ' datetime.datetime.now())
        write_event("pee","start")

        #/try:
        #    curs.execute("""INSERT INTO babylogger (category, state) VALUES ('pee','start')""")
        #    #STATUS LED:
        #    GPIO.output(pee_led_pin, GPIO.HIGH)
        #    db.commit()
        #except Exception as ex:
        #    print(ex)
        #    print("Error: the database is being rolled back --- PEE START @ " datetime.datetime.now())
        #    reset_led()
        #    sys.exit(0)
        #
    #---------------------------------------------------------
    #PEE - Stop
    elif (input_state_pee == GPIO.HIGH):
        print('Event logged: PEE - Stop at ' datetime.datetime.now())
        try:
            curs.execute("""INSERT INTO babylogger (category, state) VALUES ('pee','stop')""")
            GPIO.output(pee_led_pin, GPIO.LOW)
            db.commit()
        except Exception as ex:
            print(ex)
            print("Error: the database is being rolled back --- PEE STOP @ " datetime.datetime.now())
            reset_led()
            sys.exit(0)

    #---------------------------------------------------------
    #FED - START
    if (input_state_fed == GPIO.LOW):
        print('Event logged: FED - Start at ' datetime.datetime.now())
        try:
            curs.execute("""INSERT INTO babylogger (category, state) VALUES ('fed','start')""")
            GPIO.output(fed_led_pin, GPIO.HIGH)
            db.commit()
        except:
            print(ex)
            print("Error: the database is being rolled back --- FED START @ " datetime.datetime.now())
            reset_led()
            sys.exit(0)

    #---------------------------------------------------------
    #FED - STOP
    if (input_state_fed == GPIO.HIGH):
        print('Event logged: FED - Stop at ' datetime.datetime.now())
        try:
            curs.execute("""INSERT INTO babylogger (category, state) VALUES ('fed','stop')""")
            GPIO.output(fed_led_pin, GPIO.LOW)
            db.commit()
        except:
            print(ex)
            print("Error: the database is being rolled back --- FED STOP @ " datetime.datetime.now())
            reset_led()
            sys.exit(0)

    #---------------------------------------------------------
    #POO - START
    if (input_state_poo == GPIO.LOW):
        print('Event logged: POO - Start at ' datetime.datetime.now())
        try:
            curs.execute("""INSERT INTO babylogger (category, state) VALUES ('poo','start')""")
            GPIO.output(poo_led_pin, GPIO.HIGH)
            db.commit()
        except:
            print(ex)
            print("Error: the database is being rolled back --- POO START @ " datetime.datetime.now())
            reset_led()
            sys.exit(0)

    #---------------------------------------------------------
    #POD - STOP
    if (input_state_poo == GPIO.HIGH):
        print('Event logged: POO - Stop at ' datetime.datetime.now())
        try:
            curs.execute("""INSERT INTO babylogger (category, state) VALUES ('poo','stop')""")
            GPIO.output(poo_led_pin, GPIO.LOW)
            db.commit()
        except:
            print(ex)
            print("Error: the database is being rolled back --- POO STOP @ " datetime.datetime.now())
            reset_led()
            sys.exit(0)

    time.sleep(0.1)

# Close DB connection
db.close()

# Clean up GPIO configuration
GPIO.cleanup()

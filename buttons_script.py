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
# from asyncio.windows_events import NULL
from turtle import end_fill
import RPi.GPIO as GPIO
import os
import sys
import time
import datetime
import pymysql
pymysql.install_as_MySQLdb()
import MySQLdb
import mysql_variables          #Import MySQL variable

debug_on = 1            #DEBUG - Enable debug print

#----------------------------------------------------------
#GPIO PIN CONFIGURATION (via GPIO number)
if(debug_on): print('DEBUG - Set GPIO PIN configuration')

pee_led_pin = 20        #Green LED
#         PIN#34        #GND
fed_led_pin = 16        #Blue LED
poo_led_pin = 12        #Red LED

#             PIN#9     GND
pee_switch_pin = 17     #Green Switch
fed_switch_pin = 27     #Blue Switch
poo_switch_pin = 22     #Red Switch
#            PIN#17     VCC +3.3V

# MySQL variable are defined in mysql_variables.py module
# MySQLdb.db_host 
# MySQLdb.db_user 
# MySQLdb.db_pass 
# MySQLdb.db_name 

#----------------------------------------------------------
#Setup DB
if(debug_on): print("DB Connection settings:", mysql_variables.db_host, mysql_variables.db_user, mysql_variables.db_pass, mysql_variables.db_name)     # DEBUG - Print DB info

try:
    db = MySQLdb.connect(host=mysql_variables.db_host, user=mysql_variables.db_user, password=mysql_variables.db_pass, database=mysql_variables.db_name)
    curs = db.cursor()
except MySQLdb.Error as er:
        print("ERROR - Error connecting to MariaDB Platform: {e}")
        sys.exit(1)

#----------------------------------------------------------
#Setup GPIO
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
# FUNCTION: Flash RGB LED
def flash_led(category, state):
    if(debug_on): print("DEBUG - Flash RGB LED - Category: ", category.upper(), " State:", state.upper())    # DEBUG - Print Flash LED

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
        #Flash loop for startup / error / other
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
# FUNCTION: Write to DB
def write_event(category, state):
    now = datetime.datetime.now()

    if(debug_on): print("Creating new entry in DB: ", category.upper() , " - ", state.upper()," at ", now)      # DEBUG - Print DB info

    try:
        curs.execute("""INSERT INTO babylogger.buttondata (category, state) VALUES ('%s','%s')""", (category.lower(), state.lower()))
        db.commit()
    except Exception as ex:
        print("ERROR - Database is being rolled back --- Category: ", category.upper(), " State: ", state.upper(), " @ ", now)
        print(ex)
        #Flash LED
        flash_led("Error writing DB","")
        sys.exit(0)

#---------------------------------------------------------

time.sleep(1)

if(debug_on): print("DEBUG - Baby Logger running...")

# Reset start status
start_state_pee = 0 #PEE
start_state_fed = 0 #FED
start_state_poo = 0 #POO

while True:
    now = datetime.datetime.now()           # Update NOW

    if(debug_on): print("Debug - ", now)    # DEBUG - print time
    
    #---------------------------------------------------------
    #PEE - START
    if (GPIO.input(pee_switch_pin) == GPIO.LOW & (start_state_pee == 0)):
        if(debug_on): print("DEBUG - Event logged - PEE - Start at ", now)
        
        start_state_pee = 1         # Start flag - ON

        write_event("pee","start")  # Write DB
        flash_led("pee","start")    # Turn LED - ON
        #/try:
        #    curs.execute("""INSERT INTO babylogger (category, state) VALUES ('pee','start')""")
        #    #STATUS LED:
        #    GPIO.output(pee_led_pin, GPIO.HIGH)
        #    db.commit()
        #except Exception as ex:
        #    print(ex)
        #    print("Error: the database is being rolled back --- PEE START @ " now)
        #    reset_led()
        #    sys.exit(0)
        #
    #---------------------------------------------------------
    #PEE - Stop
    elif (GPIO.input(pee_switch_pin) == GPIO.HIGH & (start_state_pee == 1)):
        if(debug_on): print("Event logged: PEE - Stop at ", now)

        start_state_pee = 0                 # Start flag - OFF

        try:
            curs.execute("""INSERT INTO babylogger.buttondata (category, state) VALUES ('pee','stop')""")   # Write DB
            db.commit()
            GPIO.output(pee_led_pin, GPIO.LOW)  # Turn LED - OFF
        except Exception as ex:
            print(ex)
            print("Error: the database is being rolled back --- PEE STOP @ ", now)
            flash_led("Error writing DB","")
            sys.exit(0)

    #---------------------------------------------------------
    #FED - START
    if (GPIO.input(fed_switch_pin) == GPIO.LOW & (start_state_fed == 0)):
        if(debug_on): print("Event logged: FED - Start at ", now)

        start_state_fed = 1                 # Start flag - ON

        try:
            curs.execute("""INSERT INTO babylogger.buttondata (category, state) VALUES ('fed','start')""")
            GPIO.output(fed_led_pin, GPIO.HIGH)
            db.commit()
        except:
            print(ex)
            print("Error: the database is being rolled back --- FED START @ ", now)
            flash_led("Error writing DB","")
            sys.exit(0)

    #---------------------------------------------------------
    #FED - STOP
    if (GPIO.input(fed_switch_pin) == GPIO.HIGH & (start_state_fed == 1)):
        if(debug_on): print("Event logged: FED - Stop at ", now)
        
        start_state_fed = 0                 # Start flag - OFF

        try:
            curs.execute("""INSERT INTO babylogger.buttondata (category, state) VALUES ('fed','stop')""")
            GPIO.output(fed_led_pin, GPIO.LOW)
            db.commit()
        except:
            print(ex)
            print("Error: the database is being rolled back --- FED STOP @ ", now)
            flash_led("Error writing DB","")
            sys.exit(0)

    #---------------------------------------------------------
    #POO - START
    if (GPIO.input(poo_switch_pin) == GPIO.LOW & (start_state_poo == 0)):
        if(debug_on): print("Event logged: POO - Start at ", now)

        start_state_poo = 1                 # Start flag - ON

        try:
            curs.execute("""INSERT INTO babylogger.buttondata (category, state) VALUES ('poo','start')""")
            GPIO.output(poo_led_pin, GPIO.HIGH)
            db.commit()
        except:
            print(ex)
            print("Error: the database is being rolled back --- POO START @ ", now)
            flash_led("Error writing DB","")
            sys.exit(0)

    #---------------------------------------------------------
    #POO - STOP
    if (GPIO.input(poo_switch_pin) == GPIO.HIGH & (start_state_poo == 1)):
        if(debug_on): print("Event logged: POO - Stop at ", now)
        
        start_state_poo = 0                 # Start flag - OFF
        
        try:
            curs.execute("""INSERT INTO babylogger.buttondata (category, state) VALUES ('poo','stop')""")
            GPIO.output(poo_led_pin, GPIO.LOW)
            db.commit()
        except:
            print(ex)
            print("Error: the database is being rolled back --- POO STOP @ ", now)
            flash_led("Error writing DB","")
            sys.exit(0)

    time.sleep(0.1)

# Close DB connection
db.close()

# Clean up GPIO configuration
GPIO.cleanup()

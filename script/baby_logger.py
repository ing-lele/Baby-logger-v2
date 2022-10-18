# =========================================================
# Scope:
# --- Main Python script
# --- Use RGB LED for confirmation
# --- Record timestamp ON / OFF time for the 3 switches (pee,poo,fee)
# --- Daily backup locally and on OneDrive
# --- UI with raw data, stats and CSV export
#
# =========================================================

# IMPORT STATEMENTS
from turtle import end_fill
import RPi.GPIO as GPIO
import os
import importlib
import sys
import time
import datetime

#! /usr/bin/python3
backup_path = "/home/pi/Baby-logger-v2/backup/"
script_path = "/home/pi/Baby-logger-v2/script/"
sys.path.insert(0, script_path)    # Add script folder to default import search

import pymysql
pymysql.install_as_MySQLdb()
import MySQLdb
import mysql_variables      #Import MySQL variable
from export_data import *   #Import Export to CSV functions

# MySQL variable are defined in mysql_variables.py module
# mysql_variables.db_host 
# mysql_variables.db_user 
# mysql_variables.db_pass 
# mysql_variables.db_name 
# mysql_variables.db_table

#DEBUG - Enable debug print
debug_on = 1

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

#----------------------------------------------------------
#Setup GPIO
GPIO.setwarnings(False)
GPIO.cleanup()
GPIO.setmode(GPIO.BCM)

# Setup Switch (normally closed) - PullDown configuration
# Switch Open = GPIO.HIGH = +3.3V (VCC) = End event
# Switch Closed = GPIO.LOW = 0V (GND via PullDown) = Active event
# Info https://electrosome.com/using-switch-raspberry-pi/
if(debug_on): print("DEBUG - Set INPUT GPIO")
GPIO.setup(pee_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_DOWN) 
GPIO.setup(fed_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)
GPIO.setup(poo_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)

#Setup LED
if(debug_on): print("DEBUG - Setup LED GPIO")
GPIO.setup(pee_led_pin, GPIO.OUT)
GPIO.setup(fed_led_pin, GPIO.OUT)
GPIO.setup(poo_led_pin, GPIO.OUT)

#Reset LED
if(debug_on): print("DEBUG - Reset LED GPIO")
GPIO.output(pee_led_pin, GPIO.LOW)
GPIO.output(fed_led_pin, GPIO.LOW)
GPIO.output(poo_led_pin, GPIO.LOW)

#----------------------------------------------------------
# FUNCTION: Flash RGB LED
def flash_led(category, state):
    if(debug_on): print("DEBUG - Flash RGB LED - Category:", category.upper(), "-", state.upper())    # DEBUG - Print Flash LED

    # pee
    if (category=="pee" and state=="start"):
        GPIO.output(pee_led_pin, GPIO.HIGH)
    elif (category=="pee" and state=="stop"):
        GPIO.output(pee_led_pin, GPIO.LOW)
    # fed
    elif (category=="fed" and state=="start"):
        GPIO.output(fed_led_pin, GPIO.HIGH)
    elif (category=="fed" and state=="stop"):
        GPIO.output(fed_led_pin, GPIO.LOW)
    #poo
    elif (category=="poo" and state=="start"):
        GPIO.output(poo_led_pin, GPIO.HIGH)
    elif (category=="poo" and state=="stop"):
        GPIO.output(poo_led_pin, GPIO.LOW)
    else:
        #Flash loop for startup / error / other
        n = 0
        while n<5:
            n += 1

            GPIO.output(poo_led_pin, GPIO.HIGH)
            time.sleep(.1)
            GPIO.output(poo_led_pin, GPIO.LOW)

            GPIO.output(fed_led_pin, GPIO.HIGH)
            time.sleep(.1)
            GPIO.output(fed_led_pin, GPIO.LOW)

            GPIO.output(pee_led_pin, GPIO.HIGH)
            time.sleep(.1)
            GPIO.output(pee_led_pin, GPIO.LOW)

            time.sleep(.1)

#Start up of LED
flash_led("starting","")

#---------------------------------------------------------
# FUNCTION: Write to DB
#
# Table structure: CREATE TABLE switchdata(
#	id INT PRIMARY KEY auto_increment NOT NULL,
#	ts_start TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
#	ts_end TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
#   category TEXT
#
#---------------------------------------------------------
def write_event(ts_start, ts_end, category):
    if(debug_on): print("DEBUG - Creating new entry in DB:", category.upper(), "- from", ts_start,"to", ts_end)      # DEBUG - Print DB info

    # Connect to DB
    try:
        db = MySQLdb.connect(host=mysql_variables.db_host, user=mysql_variables.db_user, password=mysql_variables.db_pass, database=mysql_variables.db_name)
        curs = db.cursor()
    except MySQLdb.Error as er:
            print("ERROR - Error connecting to MariaDB Platform: {e}")
            sys.exit(1)

    # Write entry to DB
    try:
        curs.execute("""INSERT INTO babylogger.switchdata (ts_start, ts_end, category) VALUES (%s,%s,%s)""", (ts_start, ts_end, category))
        db.commit()
    except Exception as ex:
        print("ERROR - Database is being rolled back --- ", category.upper(), "- from", ts_start,"to", ts_end)
        print(ex)
        #Flash LED
        flash_led("Error writing DB")
        sys.exit(0)

    # Close DB connection
    db.close()

#---------------------------------------------------------

time.sleep(1)

print("LOG - Baby Logger running...")

# Reset start time
start_state_pee = 0 #PEE
start_state_fed = 0 #FED
start_state_poo = 0 #POO

# Reset last backup
last_backup = datetime.date(2000,1,1)

try: 
    while True:
        now = datetime.datetime.now() #.strftime("%Y-%m-%d %H:%M:%S")
        
        #---------------------------------------------------------
        #PEE - Start
        if (GPIO.input(pee_switch_pin) == GPIO.LOW and (start_state_pee == 0)):
            if(debug_on): print("DEBUG - PEE start at", now)
            start_state_pee = 1         # Update Start State
            ts_start_pee = now          # Save Start time
            flash_led("pee","start")    # Turn LED On
            
        #---------------------------------------------------------
        #PEE - Stop
        elif (GPIO.input(pee_switch_pin) == GPIO.HIGH and (start_state_pee == 1)):
            print("LOG - Event logged: PEE - from", ts_start_pee," to ", now)
            start_state_pee = 0                     # Start flag - OFF
            write_event(ts_start_pee, now, "pee")   # Write DB
            flash_led("pee","stop")                 # Turn LED Off

        #---------------------------------------------------------
        #FED - START
        if (GPIO.input(fed_switch_pin) == GPIO.LOW and (start_state_fed == 0)):
            if(debug_on): print("DEBUG - FED start at", now)
            start_state_fed = 1         # Update Start State
            ts_start_fed = now          # Save Start time
            flash_led("fed","start")    # Turn LED On

        #---------------------------------------------------------
        #FED - STOP
        if (GPIO.input(fed_switch_pin) == GPIO.HIGH and (start_state_fed == 1)):
            print("LOG - Event logged: FED - from", ts_start_pee," to ", now)
            start_state_fed = 0                     # Update Start State
            write_event(ts_start_fed, now, "fed")   # Write DB
            flash_led("fed","stop")                 # Turn LED Off

        #---------------------------------------------------------
        #POO - START
        if (GPIO.input(poo_switch_pin) == GPIO.LOW and (start_state_poo == 0)):
            if(debug_on): print("DEBUG - POO start at", now)
            start_state_poo = 1         # Update Start State
            ts_start_poo = now          # Save Start time
            flash_led("poo","start")    # Turn LED On

        #---------------------------------------------------------
        #POO - STOP
        if (GPIO.input(poo_switch_pin) == GPIO.HIGH and (start_state_poo == 1)):
            print("LOG - Event logged: POO - from", ts_start_pee," to ", now)
            start_state_poo = 0                     # Update Start State
            write_event(ts_start_poo, now, "poo")   # Write DB
            flash_led("poo","stop")                 # Turn LED Off

        #---------------------------------------------------------
        # Daily export at midnight
        #---------------------------------------------------------
        if (last_backup < datetime.date.today()):
            # Update names and variables
            last_backup = datetime.date.today()
            file_name = backup_path + mysql_variables.db_table + "_" + last_backup.strftime("%Y-%m-%d")  + ".csv"
            
            if(debug_on): print("DEBUG - File:", file_name)

            # Call write function
            print("LOG - Backup to", file_name)
            export_file(mysql_variables.db_table, file_name)

            time.sleep(1)

        time.sleep(0.1)
    #---------------------------------------------------------

finally:

    # Clean up GPIO configuration
    GPIO.cleanup()

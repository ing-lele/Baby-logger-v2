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
from turtle import end_fill
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
pee_led_pin = 12        #Green LED
#             34        #GND
fed_led_pin = 16        #Blue LED
poo_led_pin = 20        #Red LED

pee_switch_pin = 2     #Green Switch
fed_switch_pin = 3     #Blue Switch
poo_switch_pin = 4     #Red Switch

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
# Setup Switch - PullUp configuration
# Switch OFF = GND  
# Switch ON = +3.3V
# Info https://electrosome.com/using-switch-raspberry-pi/
GPIO.setup(pee_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_UP) 
GPIO.setup(fed_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(poo_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_UP)
#Setup LED
GPIO.setup(pee_led_pin, GPIO.OUT)
GPIO.setup(fed_led_pin, GPIO.OUT)
GPIO.setup(poo_led_pin, GPIO.OUT)

#----------------------------------------------------------
# Control RGB LED 

def show_led(input_type): #### --- TO BE UPDATED
    if (input_type == pee_led_pin):
        GPIO.output(pee_led_pin, GPIO.LOW)
    elif (input_type == fed_led_pin):
        GPIO.output(fed_led_pin, GPIO.LOW)
    elif (input_type == poo_led_pin):
        GPIO.output(poo_led_pin, GPIO.LOW)
    else:
        #Flash Error!
        GPIO.output(pee_led_pin, GPIO.LOW)
        GPIO.output(fed_led_pin, GPIO.LOW)
        GPIO.output(poo_led_pin, GPIO.LOW)  

    time.sleep(.1)

    
#---------------------------------------------------------

time.sleep(1.0)
print("Baby Logger running...")
    
while True:
    input_state_pee = GPIO.input(pee_switch_pin) #PEE
    input_state_fed = GPIO.input(fed_switch_pin) #FED
    input_state_poo = GPIO.input(poo_switch_pin) #POO

    curr_date = datetime.datetime.now().strftime("%Y-%m-%d")
    curr_time = datetime.datetime.now().strftime("%H:%M:%S")

#PEE
    if input_state_pee == GPIO.LOW:

        print(curr_date + " " + curr_time + " - Event logged: Pee")
        try:
            curs.execute("""INSERT INTO babylogger (tdate, ttime, type) VALUES (%s, %s, 'pee')""", (curr_date, curr_time))
            #STATUS LED:
            show_led(pee_led_pin)
            db.commit()
        except Exception as ex:
            print(ex)
            print("Error: the database is being rolled back --- PEE @ curr_date, curr_time ")
            sys.exit(0)
#FED
    elif input_state3 == GPIO.LOW:
        print(curr_date + " " + curr_time + " - Event logged: Fed")
        try:
            curs.execute("""INSERT INTO babylogger (tdate, ttime, type) VALUES (%s, %s, 'fed')""", (curr_date, curr_time))
            #STATUS LED:
            show_led(fed_led_pin)
        except:
            print("Error: the database is being rolled back --- FED @ curr_date, curr_time ")
            sys.exit(0)

#POO
    elif input_state2 == GPIO.LOW:
        print(curr_date + " " + curr_time + " - Event logged: Poo")
        try:
            curs.execute("""INSERT INTO babylogger (tdate, ttime, type) VALUES (%s, %s, 'poo')""", (curr_date, curr_time))
            show_led(poo_led_pin)   #Show LED
            db.commit()
        except:
            print("Error: the database is being rolled back --- FED @ curr_date, curr_time ")
            sys.exit(0)


    time.sleep(0.1)
db.close()

GPIO.cleanup()

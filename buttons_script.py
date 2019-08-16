#!/usr/bin/python3
import RPi.GPIO as GPIO
import sys
import time
import pymysql
pymysql.install_as_MySQLdb()
import MySQLdb
#import board #uncomment those lines if you want to have a NeoPixel
#import neopixel 
#pixels = neopixel.NeoPixel(board.D21, 1)
db = MySQLdb.connect("localhost", "logger", "password", "buttons")
curs=db.cursor()
GPIO.setmode(GPIO.BCM)
GPIO.setup(26, GPIO.IN, pull_up_down=GPIO.PUD_UP) #switch to the GPIO numbers you're going to be using and to the pull-up or pull-down resistor you're using
GPIO.setup(16, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(20, GPIO.IN, pull_up_down=GPIO.PUD_UP)

while True:
    input_state = GPIO.input(20)
    input_state2 = GPIO.input(16)
    input_state3 = GPIO.input(26)
    if input_state == False:
        try:
            
            curs.execute ("""INSERT INTO buttondata 
                         values(CURRENT_DATE(), NOW(), 'poo')""") #Change the button name here
#            pixels.fill((20, 15, 10)) #blinks the NeoPixel, uncomment if you want that
#            time.sleep(1)
#            pixels.fill((0, 0, 0))
            db.commit()

        except:
            print ("Error: the database is being rolled back")

    if input_state2 == False:
        try:
            
            curs.execute ("""INSERT INTO buttondata 
                         values(CURRENT_DATE(), NOW(), 'pee')""") #Change the button name here
#            pixels.fill((20, 20, 0))
#            time.sleep(1)
#            pixels.fill((0, 0, 0))
            db.commit()
        except:
            print ("Error: the database is being rolled back")
            
    if input_state3 == False:
        try:
            curs.execute ("""INSERT INTO buttondata 
                         values(CURRENT_DATE(), NOW(), 'fed')""")      #Change the button name here     
#            pixels.fill((0, 20, 0))
#            time.sleep(1)
#            pixels.fill((0, 0, 0))
            db.commit()

        except:
            print ("Error: the database is being rolled back")
            sys.exit(0)
    time.sleep(0.1)
db.close()

GPIO.cleanup()




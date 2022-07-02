print('Import library')
import RPi.GPIO as GPIO          #Import GPIO library
import time                      #Import time library
import datetime

#----------------------------------------------------------
#CONFIGURATION SETTINGS
print('Config Settings')

pee_led_pin = 12        #Green LED
#             34        #GND
fed_led_pin = 16        #Blue LED
poo_led_pin = 20        #Red LED

pee_switch_pin = 2     #Green Switch
fed_switch_pin = 3     #Blue Switch
poo_switch_pin = 4     #Red Switch


#----------------------------------------------------------
#SETUP GPIO
print('setup GPIO')

GPIO.cleanup()  #cleanup any previous config 
#GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
# Setup Switch - PullUp configuration
# Switch OFF = +3.3V  
# Switch ON = GND
# Info https://electrosome.com/using-switch-raspberry-pi/
GPIO.setup(pee_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_UP) 
GPIO.setup(fed_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(poo_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_UP)
#Setup LED
GPIO.setup(pee_led_pin, GPIO.OUT)
GPIO.setup(fed_led_pin, GPIO.OUT)
GPIO.setup(poo_led_pin, GPIO.OUT)

#----------------------------------------------------------
# Test Switch -> LED
print('Test Switch')

while True:
    print('GPIO.input(pee_switch_pin)')
    print('GPIO.input(fee_switch_pin)')
    print('GPIO.input(poo_switch_pin)')
    
    input_state_pee = GPIO.input(pee_switch_pin) #PEE
    input_state_fed = GPIO.input(fed_switch_pin) #FED
    input_state_poo = GPIO.input(poo_switch_pin) #POO

    curr_date = datetime.datetime.now().strftime("%Y-%m-%d")
    curr_time = datetime.datetime.now().strftime("%H:%M:%S")

    if (input_state_pee == GPIO.LOW):
        print('PEE Switch - curr_date @ curr_time')     #Print 'PEE Switch'
        GPIO.output(pee_led_pin, GPIO.HIGH)        

    elif (input_state_fed == GPIO.LOW):
        print('FED Switch - curr_date @ curr_time')     #Print 'FED Switch'
        GPIO.output(fed_led_pin, GPIO.HIGH)

    elif (input_state_poo == GPIO.LOW):
        print('POO Switch - curr_date @ curr_time')     #Print 'POO Switch'
        GPIO.output(poo_led_pin, GPIO.HIGH)

    time.sleep(0.3)           #Delay of 1s

GPIO.cleanup()
print('Import library')
import RPi.GPIO as GPIO          #Import GPIO library
import time                      #Import time library
import datetime

#----------------------------------------------------------
#CONFIGURATION SETTINGS (via GPIO number)
print('Config Settings')

pee_led_pin = 12        #Green LED
#         PIN#34        #GND
fed_led_pin = 16        #Blue LED
poo_led_pin = 20        #Red LED

#            PIN#1      VCC +3.3V
pee_switch_pin = 2      #Green Switch
fed_switch_pin = 3      #Blue Switch
poo_switch_pin = 4      #Red Switch
#            PIN#9      GND

#----------------------------------------------------------
#SETUP GPIO
print('setup GPIO')

GPIO.cleanup()  #cleanup any previous config 
#GPIO.setwarnings(False)

GPIO.setmode(GPIO.BCM) #setup via GPIO pin number

# Setup Switch - PullDown configuration
# Switch Closed = +3.3V (VCC)
# Switch Open = 0V (GND via PullDown)
# Info https://electrosome.com/using-switch-raspberry-pi/
GPIO.setup(pee_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_DOWN) 
GPIO.setup(fed_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)
GPIO.setup(poo_switch_pin, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)

#Setup LED
GPIO.setup(pee_led_pin, GPIO.OUT)
GPIO.setup(fed_led_pin, GPIO.OUT)
GPIO.setup(poo_led_pin, GPIO.OUT)

#----------------------------------------------------------
# Test LED
print('Test LED')

# reset LED
GPIO.output(pee_led_pin, GPIO.LOW)
GPIO.output(fed_led_pin, GPIO.LOW)
GPIO.output(poo_led_pin, GPIO.LOW)

# test PEE
print('Test RED')
GPIO.output(pee_led_pin, GPIO.HIGH)
time.sleep(5)
GPIO.output(pee_led_pin, GPIO.LOW)

# test FED
print('Test BLUE')
GPIO.output(fed_led_pin, GPIO.HIGH)
time.sleep(5)
GPIO.output(fed_led_pin, GPIO.LOW)

# test POO
print('Test GREEN')
GPIO.output(poo_led_pin, GPIO.HIGH)
time.sleep(5)
GPIO.output(poo_led_pin, GPIO.LOW)

#----------------------------------------------------------
# Test Switch -> LED
print('Test Switch')

while True:
    # Print current status
    print('Pee switch: ',GPIO.input(pee_switch_pin))
    print('Fed switch: ',GPIO.input(fed_switch_pin))
    print('Poo switch: ',GPIO.input(poo_switch_pin))
    
    # read value
    input_state_pee = GPIO.input(pee_switch_pin) #PEE
    input_state_fed = GPIO.input(fed_switch_pin) #FED
    input_state_poo = GPIO.input(poo_switch_pin) #POO

    # Set time + date
    curr_date = datetime.datetime.now().strftime("%Y-%m-%d")
    curr_time = datetime.datetime.now().strftime("%H:%M:%S")

    # Active switch
    if (input_state_pee == GPIO.LOW):
        print('PEE Active - ', curr_date , curr_time)     #Print 'PEE Active'
        GPIO.output(pee_led_pin, GPIO.HIGH)

    if (input_state_fed == GPIO.LOW):
        print('FED Active - ', curr_date, curr_time)     #Print 'FED Active'
        GPIO.output(fed_led_pin, GPIO.HIGH)

    if (input_state_poo == GPIO.LOW):
        print('POO Active - ', curr_date, curr_time)     #Print 'POO Active'
        GPIO.output(poo_led_pin, GPIO.HIGH)
    
    # Clear switch
    if (input_state_pee == GPIO.HIGH):
        print('PEE Clear - ', curr_date , curr_time)     #Print 'PEE Clear'
        GPIO.output(pee_led_pin, GPIO.LOW)

    if (input_state_fed == GPIO.HIGH):
        print('FED Clear - ', curr_date, curr_time)     #Print 'FED Clear'
        GPIO.output(fed_led_pin, GPIO.LOW)

    if (input_state_poo == GPIO.HIGH):
        print('POO Clear - ', curr_date, curr_time)     #Print 'POO Clear'
        GPIO.output(poo_led_pin, GPIO.LOW)

    time.sleep(5)           #Delay of 5s

GPIO.cleanup()
# Baby-logger
Logs bodily functions and displays them on a locally hosted webpage.

The MySQL part of this project was heavily borrowed from [jjpfin's excellent Raspberry Pi temperature logger](https://www.instructables.com/id/Raspberry-PI-and-DHT22-temperature-and-humidity-lo/).

You'll need a Raspberry Pi running the latest version of Raspbian for this project. I used a Pi 2, but pretty much any model would do. A Pi Zero W would be ideal for the task with its on-board WiFi and low current draw. For the physical part you'll also require a number of pushbuttons. We used 3, but changing that number would be fairly trivial. In the original project they were configured in a pulled-up configuration, meaning that one contact was connected to the ground and one to the GPIO pin. You could easily change that to an active-high and pull the GPIO pins down instead.

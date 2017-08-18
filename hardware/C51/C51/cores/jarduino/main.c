/*
  main.cpp - Main loop for Arduino sketches
  Copyright (c) 2005-2013 Arduino Team.  All right reserved.

*/

#include <Arduino.h>


void main(void)
{
	setup();
    
	while(true) {
		loop();
		//if (serialEventRun) serialEventRun();
	}
        
	//return 0;
}


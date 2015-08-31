coinflip
========

A Symfony project created on August 22, 2015, 6:00 pm.


Installing ZeroMQ
==================

Go to http://zeromq.org/bindings:php

On this page it will tell you to do the following 

1. Download and build ZeroMQ (see http://zeromq.org/intro:get-the-software)

2. If step 2 goes to plan run "sudo pecl install zmq-beta" to add the zeroMQ extension to PHP (without the quotes).

3. Add "extension=zmq.so" to your php.ini (without the quotes). Although, for me step 2 already did this

4. Restart php-fpm 
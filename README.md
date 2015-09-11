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


Running Vagrant
==================

Before you do anything you will need to add the following line to your hosts file (on a mac this is at: /etc/hosts)

127.0.0.1       test.coinflip.dev

To run vagrant, use the following command: vagrant up

Once the vagrant box is up and running, you will need to do the following (FIRST TIME ONLY):

1. vagrant ssh

2. Go to the project directory: cd /vagrant

2. Clear symfony's cache (should only be necessary if you have been running the project on your local computer):

php app/console cache:clear --env=prod

php app/console cache:clear --env=test

php app/console cache:clear --env=dev

3. Set up the database. Do the following to achieve this:

php app/console doctrine:database:create

php app/console doctrine:schema:update --force

./fixturesrunner.sh

Note ./fixturesrunner.sh is a shell script the loads in the project default data

4. Setup the socket server. Note that you will need to keep the vagrant window that runs the socket open as it runs in the command prompt that created it. Use another vagrant window to do anything else with the rest of the project. If you close this window the socket server will stop.

php ./socketbin/game-push-server.php

When you run the socket script it should look like the command prompt is hanging (why you need to open a new vagrant window to work on the project). 

5. You should now be able to access the site by going to: http://test.coinflip.dev:5000 . Note you need to add the port number 5000 to run the vagrant project.
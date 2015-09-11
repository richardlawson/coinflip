#!/bin/sh
php app/console cache:clear --env=prod
php app/console cache:clear --env=test
php app/console cache:clear --env=dev
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
./fixturesrunner.sh
php ./socketbin/game-push-server.php
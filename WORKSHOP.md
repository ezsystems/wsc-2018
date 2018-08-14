# Install

1. Git clone this repository
2. composer install
3. run php bin/console ezplatform:install clean
4. run php bin/console doctrine:schema:update --force (Required for each step, after STEP-2)
5. run php bin/console assets:install
6. run php bin/console assetic:dump
7. run php bin/console clear:cache
8. run php bin/console server:run

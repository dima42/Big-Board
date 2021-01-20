web: composer dump-autoload && propel config:convert && propel migrate && vendor/bin/heroku-php-apache2 /
worker: composer dump-autoload && propel config:convert && cd /app/ && bash worker.sh

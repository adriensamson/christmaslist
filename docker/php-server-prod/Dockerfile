FROM christmaslist-front

ADD data /var/www/

RUN mkdir -p /var/www/app/cache /var/www/app/logs
RUN chgrp -R www-data /var/www/app/cache /var/www/app/logs
RUN chmod -R g+w /var/www/app/cache /var/www/app/logs

RUN rm /var/www/web/app_dev.php


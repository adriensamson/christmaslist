FROM christmaslist-front

ADD data /var/www/

RUN chgrp -R www-data app/cache app/logs
RUN chmod -R g+w app/cache app/logs

RUN rm /var/www/web/app_dev.php


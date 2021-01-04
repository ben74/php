FROM devilbox/php-fpm-7.4:latest
#stop sendmail;docker-compose build sendmail;fig up -d sendmail;log sendmail;
#stop sendmail;fig up -d sendmail;log sendmail
#expose php curl api for sending or retrieving mails by messageId ..

RUN apt-get update && apt-get install -y sendmail bash apache2
RUN rm -rf /var/lib/apt/lists/*
RUN echo "sendmail_path=/usr/sbin/sendmail -t -i" >> /usr/local/etc/php/conf.d/sendmail.ini

RUN echo '#!/bin/sh' > /usr/local/bin/docker-php-entrypoint
RUN printf "\n\n" >> /usr/local/bin/docker-php-entrypoint
RUN echo 'set -e;if [ "${1#-}" != "$1" ]; then set -- php-fpm "$@";fi;   echo "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts;' >> /usr/local/bin/docker-php-entrypoint

RUN printf "\n\n" >> /usr/local/bin/docker-php-entrypoint
RUN echo "service sendmail start &" >> /usr/local/bin/docker-php-entrypoint
RUN printf "\n\n" >> /usr/local/bin/docker-php-entrypoint
RUN echo "exec "$@";tail -f /dev/null;" >> /usr/local/bin/docker-php-entrypoint

#then launched service sendmail start &
#CMD ["sendmail"]
#ENTRYPOINT ["docker-php-entrypoint"]
#WORKDIR /

#RUN echo "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts
#RUN service sendmail start

#RUN sed -i '/#!\/bin\/sh/aservice sendmail restart' /usr/local/bin/docker-php-entrypoint
#RUN sed -i '/#!\/bin\/sh/aecho "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts' /usr/local/bin/docker-php-entrypoint

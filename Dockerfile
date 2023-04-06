FROM shinsenter/phpfpm-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV HTTP_PROXY=""
ENV HTTPS_PROXY=""
#ENV max_execution_time=120
#ENV post_max_size=1024M                                                                                                            
#ENV upload_max_filesize=1024M
#ENV UPLOAD_LIMIT: 64M 
#RUN apt install unzip
#ENV NO_PROXY=agencia-citas.xutil.cu,ticket.xutil.net,oficina-vhabitat.prod.xetid.cu,api.enzona.net,apis-fuc.minjus.gob.cu,wso2am.xutil.cu

COPY ./src /var/www/html
COPY ./citas.conf /etc/apache2/sites-enabled/
COPY ./root / 

WORKDIR /var/www/html
RUN unzip -o /var/www/html/vendor.zip

RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
RUN chmod -R 777 /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 777 /var/www/html/public
RUN chown -R www-data:www-data /var/www/html/public
RUN chmod -R 777 /var/www/html/bootstrap/cache 
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache
RUN chmod -R 755 storage bootstrap/cache
# RUN chmod -R 777 .
# RUN chown -R www-data:www-data .
RUN ls
#CMD php startserver.php start -d
# RUN php artisan telescope:publish

EXPOSE 6006
CMD php startserver.php start
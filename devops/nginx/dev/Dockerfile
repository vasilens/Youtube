FROM nginx:stable

EXPOSE 80
EXPOSE 443

RUN rm /etc/nginx/conf.d/default.conf
COPY config/ /etc/nginx/conf.d/
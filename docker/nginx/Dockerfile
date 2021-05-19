FROM alpine:3.10

RUN apk add --update --no-cache nginx

COPY nginx.conf /etc/nginx/
COPY app.conf /etc/nginx/conf.d/default.conf
COPY app.local.crt /etc/ssl/certs/app.local.crt
COPY app.local.key /etc/ssl/private/app.local.key

RUN echo "upstream php-upstream { server php-fpm:9001; }" > /etc/nginx/conf.d/upstream.conf

RUN adduser -D -g '' -G www-data www-data

CMD ["nginx"]

EXPOSE 80
EXPOSE 443

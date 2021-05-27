#aws s3 rm s3://codepipeline-us-east-1-397194491914/app.dascrypto.farm/SourceArti/** --recursive


chown www-data:www-data /app

service nginx restart
service php7.4-fpm restart

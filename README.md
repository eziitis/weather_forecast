### Deployment notes:

1) make sure you have all necessary prerequisites for a symfony project (see documentation: https://symfony.com/doc/current/setup.html)
2) after cloning reposatory, setup custom virtual host (https://docs.rackspace.com/support/how-to/set-up-apache-virtual-hosts-on-the-ubuntu-operating-system/) .conf file ex.

`<VirtualHost *:80>`

ServerName weather.local

    DocumentRoot /var/www/weather_forecast/public
    <Directory /var/www/weather_forecast/public>
        AllowOverride None
        Order Allow,Deny
        Allow from All

        <IfModule mod_rewrite.c>
            Options -MultiViews
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^(.*)$ index.php [QSA,L]
        </IfModule>
    </Directory>

    # uncomment the following lines if you install assets as symlinks
    # or run into problems when compiling LESS/Sass/CoffeeScript assets
    # <Directory /var/www/example.com>
    #     Options FollowSymlinks
    # </Directory>

    ErrorLog /var/log/apache2/exampl_error.log
    CustomLog /var/log/apache2/example_access.log combined
`</VirtualHost>`

3) install PostgreSQL and setup DB (.env file needs DB credential update)
4) run `symfony console doctrine:migrations:migrate`
5) In .env file API keys (ipstack and openweather) are functonal, but have limited uses (free option)
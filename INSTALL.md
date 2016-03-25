IslamKosh Installation
----------------------

1. Clone the repository to your local

    ```
    git clone git@github.com:IslamKosh/core.git islamkosh
    ```

2. Create a VirtualHost:

    ```
    <VirtualHost *:80>

        ServerName islamkosh.dev

        <Directory "/var/www/islamkosh/web">
           Options Indexes FollowSymLinks MultiViews
           AllowOverride All
           Order allow,deny
           Allow from all
        </Directory>

        DocumentRoot "/var/www/islamkosh/web"

    </VirtualHost>
    ```

3. Create `.htaccess` file and modify to match your environment:

    ```
    cp web/.htaccess.dist web/.htaccess
    ```

4. Prepare the directories:

    ```
    chmod -R 0777 var/cache var/logs
    chmod -R 0777 web/uploads web/media
    ```

5. Download the vendor libraries:

    ```
    composer install -v --prefer-dist
    ```

6. Dump the assets:

    ```
    php app/console assetic:dump --env=prod
    php app/console assets:install --symlink --relative
    ```

7. Create the DB schema

    ```
    php app/console doctrine:schema:create
    ```

8. Run the app by visiting: http://islamkosh.dev

Enjoy!

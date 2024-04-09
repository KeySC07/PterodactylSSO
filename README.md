# PterodactylSSO
Used to maintain SSO for ANVAR's Pterodactyl Panel

Dependencies Required:
NPM
Yarn

Build Instructions:
1. composer require laravel/socialite
2. composer require socialiteproviders/authentik
3. php artisan view:clear && php artisan config:clear
4. cd /var/www/pterodactyl
5. export NODE_OPTIONS=--openssl-legacy-provider
6. yarn build:production

Troubleshooting:

If a Laravel error occurs for the clockwise index it is likely a permisson error:

sudo chown -R www-data:www-data /var/www/pterodactyl/storage/clockwork/index

The above command should solve the problem.

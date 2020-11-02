# Contributing

If you'd like a relatively simple way to set up your own development environment for contributing to this project, here are some instructions using Docker containers.

## Method 1: Docker Compose
1. Clone this repo or fork it and clone your fork. Install Docker (and `docker-compose`)
2. Inside the cloned repo directory, run `docker-compose up -d` (this will take a while as it needs to download the database and images)
3. In the `src` directory, make copies of `.sample` files and remove the suffixes on them. Change the following in `config.php` and save:
```php
  $servername = "mhct-db"
  $username = "admin"
  $password = "admin" // unless you change these, which is more secure
  $dbname = "mhhunthelper"
```
4. You should now be able to access the local server via http://127.0.0.1 or http://localhost on your browser, connect to the database via port 3306, and modify the code on your host machine.

To add other databases, uncomment them in `docker-compose.yml` and run `docker-compose up -d` again

## Method 2: Docker Run
1. `docker pull tsitu/mhct-db-docker:latest` - This command takes a while, as it downloads the MHCT DB images.
2. `docker run -p 3306:3306 --name mhct-db -d tsitu/mhct-db-docker` (`-p 3306:3306` is optional but maps the Docker port to your localhost for easy access)
3. `docker pull richarvey/nginx-php-fpm:1.8.2`
4. `docker run -d -e 'GIT_EMAIL=' -e 'GIT_NAME=Your Name' -e 'GIT_USERNAME=' -e 'GIT_REPO=github.com/mh-community-tools/mh-hunt-helper' -e 'GIT_PERSONAL_TOKEN=Long String' -e 'WEBROOT=/var/www/html/src' --name mhct richarvey/nginx-php-fpm:1.8.` - The Git stuff is part of your account. You may want to create a new personal token.
5. `docker exec -it mhct bash` - This gets you a shell inside the server container. You'll need to create and edit `src/config.php` via the `.sample` file:
```php
  $servername = "IPADDRESS" // check via `docker inspect mhct-db` (e.g. 172.17.0.2)
  $username = "admin"
  $password = "admin" // unless you change these, which is more secure
  $dbname = "mhhunthelper"
```
6. You can now access this at `http://IP/`. IP is found with `docker inspect mhct`. If you're using Docker Toolbox, you may need to use `http://192.168.99.100/` and port forward the guest's port 80 to your host port of choice via VirtualBox.
7. `index.php` is attraction rates, `loot.php` is drop rates. `mhhunthelper` data includes attractions, drops, and some stats.

NOTE: To use the other databases, pull and run them (where `[TAG]` can be `converter` or `mapspotter`):

1. `docker pull tsitu/mhct-db-docker:[TAG]`
2. `docker run -d --name mhct-[TAG] tsitu/mhct-db-docker:[TAG]`

This will start each database in its own container with its own IP address (use `docker inspect` to find it). You will use that IP address in `config.php`. Alternatively you can restore each database from [its backup](https://keybase.pub/devjacksmith/mh_backups/) into your `mhct-db` container.

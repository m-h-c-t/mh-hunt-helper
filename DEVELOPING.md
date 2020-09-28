If you'd like a relatively simple way to set up your own development environment so you can contribute to this project, here are some instructions using docker containers.

We have two ways of doing it: via docker-compose or via docker run.

# Docker-Compose way
1. clone this repo or fork it and clone your fork, install docker (and docker-compose)
1. inside the cloned repo directory, run `docker-compose up -d` (this will take a while as it needs to download database and images)
1. In the Server directory copy `config.php.samle` to `config.php`, change the following and save:  
  a - servername: `mhct-db`  
  b - username: `admin`  
  c - password: `admin` (unless you change these, which is more secure)  
  d - dbname: `mhhunthelper`  
1. You should now be able to access server on http://127.0.0.1 or http://localhost on your browser, and connect to database via port 3306 on your localhost or 127.0.0.1, and modify the code on your host machine.

To add other databases uncomment them in docker-compose.yml and run `docker-compose up -d` again

# Docker Run way
1. `docker pull tsitu/mhct-db-docker:latest` (or `bavovanachte/mousehunt-jacksdb`)
2. `docker run -p 3306:3306 --name mhct-db -d tsitu/mhct-db-docker` (`-p 3306:3306` is optional but maps the docker port to your localhost for easy access). This command takes a while, it loads the MHCT db
3. `docker pull richarvey/nginx-php-fpm:1.8.2`
4. `docker run -d -e 'GIT_EMAIL=' -e 'GIT_NAME=Your Name' -e 'GIT_USERNAME=' -e 'GIT_REPO=github.com/mh-community-tools/mh-hunt-helper' -e 'GIT_PERSONAL_TOKEN=Long String' -e 'WEBROOT=/var/www/html/Server' --name mhct richarvey/nginx-php-fpm:1.8.` -- the GIT stuff is part of your account; you may want to make a new personal token
5. `docker exec -it mhct bash` . This gets you a shell inside. You'll need to edit config.php (which may be in the Server directory).  
  a - servername is an IP address (`docker inspect mhct-db`)  
  b - username is `admin`  
  c - password is `admin` (unless you change these, which is more secure)  
  d - dbname is `mhhunthelper`  
6. You can access this at `http://IP/` -- IP is found with `docker inspect mhct`. `index.php` is attraction rates, `loot.php` is drop rates (use the files, 'MHCT Tools' points to agiletravels)
NOTE: Only the mhhunthelper stuff is there by default - attractions, drops, some of stats. If you want the other pieces you will have to import the database then supply the credentials.

To add the other databases:

1. `docker run -d --name mhct-converter tsitu/mhct-db-docker:converter`
2. `docker run -d --name mhct-maphelper tsitu/mhct-db-docker:maphelper`
3. `docker run -d --name mhct-mapspotter tsitu/mhct-db-docker:mapspotter`

This will start each database in its own container with its own IP address (use `docker inspect` to find it). You will use that IP address in `config.php`. Alternatively you can restore each database from [its backups](https://keybase.pub/devjacksmith/mh_backups/) into your mhct-db container.

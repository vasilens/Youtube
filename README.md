# PHP dev environment
### Setup
- PHP 7.3
- Nginx
- MySQL 5.7
- Composer
- Git
- PhpUnit

### Additional goodies, i.e. PHP code quality tools.
The major tool which is used is GrumPHP https://github.com/phpro/grumphp and it is configured to run the following tools:
- phpmd - https://github.com/phpro/grumphp/blob/master/doc/tasks/phpmd.md
- phpcs - https://github.com/phpro/grumphp/blob/master/doc/tasks/phpcs.md 
- phpstan - https://github.com/phpro/grumphp/blob/master/doc/tasks/phpstan.md
- phpunit - https://github.com/phpro/grumphp/blob/master/doc/tasks/phpunit.md

The upper tools are fired in a pre-commit git hook and are configured by the following files in main dir:
- grumphp.yml
- phpmd_rulesets.xml

### Prerequisites
- You must have the following tools installed:
    - Git
    - Docker - https://docs.docker.com/install/linux/docker-ce/ubuntu/
    - Docker Compose - https://docs.docker.com/compose/install/
- You must add the proper virtual host record to your /etc/hosts file, i.e.
    - 127.0.0.1	php.local
    - In case you want a different name, you must specify it in ./devops/nginx/dev/config/server.conf

### Configuration
- Configuration is in .env(will be created for you based on .env-dist) and there you can tweak database config and some Docker params.
- In case your uid and gid are not 1000 but say 1001, you must change the USER_ID and GROUP_ID vars in .env file. Type the `id` command in your terminal in order to find out.
- When created, your containers' names will be prefixed with COMPOSE_PROJECT_NAME env var, e.g. `php7`. You can change this as per your preference.
- Nginx logs are accessible in ./volumes/nginx/logs
- MySQL data is persisted via a Docker volume.
- Composer cache is persisted via a Docker volume.
- You can write code by loading your project in your favourite IDE, but in order to use Composer or to take advantage of the code quality tools you must work in the PHP container.

### Database
- By using db_init/initial_setup.sql, Docker will create a sample table for you automatically on the first run of the MySQL container. 
This can be used to import the whole database for a living project which we want to run with Docker containers.

### Start the Docker ecosystem for a first time
- `mkdir my_project` - create a new project dir
- `cd my_project` - get into it
- `git clone https://github.com/ebalkanski/php-nginx-mysql.git .` - clone code from repo
- `rm -rf .git` - cleanup git data. Now you can init a new fresh repo if you want and work with it.
- `cp .env-dist .env` - create the .env file
- Now you would want to run `id` command and set USER_ID and GROUP_ID env vars in .env file as per your needs.
- `docker-compose build` - build Docker images and volumes
- `docker-compose run --rm php-dev composer install` - install Composer packages
- `docker-compose up -d` - start the whole ecosystem
- `docker-compose ps` - verify all containers are up and running
- open `http://php.local` in your favourite browser and you should see phpinfo() output there.
- `docker-compose exec php-dev /bin/bash` - enter the php container.

### Useful commands
- `docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' container` - gets container's IP
- `docker kill -s HUP container` - can be used to reload Nginx configuration dynamically

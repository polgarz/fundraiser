<p align="center">
    <h1 align="center">Fundraiser</h1>
    <br>
</p>

### Install with Docker

Start the container

    docker-compose up -d --build

Install dependencies

    docker-compose run --rm php composer install

    docker-compose run --rm node npm install

Run database migration

    docker-compose run --rm php yii migrate

    docker-compose run --rm php yii migrate --migrationPath=@yii/log/migrations/

Build the assets

    docker-compose run --rm node node_modules/gulp/bin/gulp.js

You can then access the application through the following URL:

    http://127.0.0.1:8000

**NOTES:**
- Minimum required Docker engine version `17.04` for development (see [Performance tuning for volume mounts](https://docs.docker.com/docker-for-mac/osxfs-caching/))

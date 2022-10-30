<p align="center">
    <h1 align="center">Fundraiser</h1>
    <br>
</p>

## What is this?

It's a web application. It helps organizations fundraise.

## Features

- General donation form
- Campaigns with or without ambassadors
- Payments by card (simplepay) or transfer.

## Install with Docker

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

## Configuration

Open the `.env` file in the root directory and fill the missing entries.

SimplePay IPN should be: https://yourdomain.tld/donate/ipn.

### Recurring payment

To use the recurring payment you should add a cronjob entry to your crontab file, for example:

    0 9 * * * php INSTALLATION_DIR/yii donate/recurring

## Theming

You can change the colors of the theme by modifying the `web/src/scss/_variables.scss` file, then rebuilding the assets. Or you can change anything - it's up to you.

# WordPress Playground

This project contains a plugin named `news` in order to import articles from an external API using a custom post type.

- Register the `news_article` post type.
- Creates meta data to save the `external_url`
- Add a custom WP CLI command to run the importing process.

Note: This project uses a mock-server as external API. You can find the code in the `mock-news-api` folder.

## Run

Run the WordPress and Mock Server using `docker-compose`:

```
docker-compose up
```

## Set Up Wordpress Site

The following script installs the WordPress site and activates the `news` plugin automatically.

```
./setup.sh
```

Once the script finishes you can log in using the following credentials on http://localhost:8080/wp-admin:

- **Username**: admin
- **Password**: admin


## Run the sync

The following command runs the custom WP CLI command using the `wordpress` docker container.

```
./sync.sh
```

## TODO

- Get Mock API URL from an WordPress option or environment variable

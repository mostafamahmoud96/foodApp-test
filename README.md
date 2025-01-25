## Installation

1. To start the project's containers, run:

    ```sh
    docker compose up -d
    ```

2. To install composer, run the following

    ```sh
    docker exec foodics_app composer install
    ```

3. Copy the example environment file:

    ```sh
    docker exec foodics_app cp .env.example .env
    ```


4. Generate the application key:

    ```sh
    docker exec foodics_app php artisan key:generate
    ```

4. To run migrations:

    ```sh
    docker exec foodics_app php artisan migrate
    ```

5. To run seeders:
    ```sh
    docker exec foodics_app php artisan db:seed
    ```

## Unit Testing

1. Exec into the container:

    ```sh
    docker exec -it foodics_app bash
    ```

2. Then run:
    ```sh
    ./vendor/bin/pest
    ```

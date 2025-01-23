## Installation

-   To start the project's contianers run `docker compose up -d`
    Then Exec into the container using `docker exec -it foodics-app bash`
    and run `bash ./dev-ops/start-container`
-   To stop running containers run `docker compose down`

*   To run unit testing
    -   Exec into the container `docker exec -it foodics-app bash`
    -   Then run `./vendor/bin/pest`

## Cashier-app



### Prerequisites

Docker must be instaled to run the app other way you need a local server and postgres sql database running.

* to build the image (` cashier_app:latest `) run this command in the project root directory
  ```bash
  docker build --pull --rm -f "Dockerfile" -t cashier_app:latest "."
  ```

* run this command in the project root to build the env and run the app
  ```bash
  docker-compose -f "docker-compose.yaml" up -d
  ```

### Installation

* after building the environment link up to the cashier-app container and run this cmd to create a database

   ```bash
   docker exec -it cashier-app bash

   // this will link you to the container and run

   composer install

   // this will install the vendor file localy fo the idea can auto complete code
   ```
* after building the environment link up to the cashier-app container and run this cmd to create a database

   ```bash
   php bin/console doctrine:database:create
   ```
* run migration to built database tables

   ```bash
   php bin/console doctrine:migrations:migrate
   ```
* run fixtures to load data and a test user

   ```bash
   php bin/console doctrine:fixtures:load
   ```


Your app is ready and can be accessed from (` localhost:8011 `) :

a pg-admin instance was created to access the database

 ---
 - username     : (` user@domain.com `) this can be user as api auth two
 - password     : (` SuperSecret `) this can be user as api auth two
 - accessed on  : (` localhost:8012 `)
---
 - database sever : (` cashier_postgres `)
 - database port : (` 5432 `)
 - database user : (` postgres `)
 - database pass : (` postgres `)
 

## Cashier-app



### Prerequisites

Docker must be instaled to run the app other way you need a local server and postgres sql database running.
* run this command in the project root to build the env and run the app
  ```bash
  docker-compose -f "docker-compose.yaml" up -d --build
  ```

### Installation

1. after building the environment link up to the cashier-app container and run this cmd to create a database
   ```bash
   symfony database create
   ```
2. run migration to built databse tables
   ```bash
   symfony cmd to build schema and create tables
   ```


Your app is ready and can be accessed from (` localhost:8011 `) :

a pg-admin instance was created to access the database
 
 - username     : (` postgres `)
 - password     : (` postgres `)
 - accessed on  : (` localhost:8012 `)
 

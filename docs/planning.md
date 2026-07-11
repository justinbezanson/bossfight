## Models

CREATE TABLE kids (
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    name VARCHAR(32) NOT NULL,
    user_id INT NOT NULL,
    CONSTRAINT fk_kids_users 
        FOREIGN KEY (user_id) 
        REFERENCES users(id)
);

CREATE TABLE logs (
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    log_date DATETIME NOT NULL DEFAULT NOW(),
    kid_id INT NOT NULL,
    game_id INT NULL,
    user_id INT NOT NULL,
    message VARCHAR(512) NOT NULL,
    CONSTRAINT fk_logs_kids 
        FOREIGN KEY (kid_id) 
        REFERENCES kids(id),
    CONSTRAINT fk_logs_games
        FOREIGN KEY (game_id) 
        REFERENCES games(id),
    CONSTRAINT fk_logs_users
        FOREIGN KEY (user_id) 
        REFERENCES users(id)
);

CREATE TABLE games (
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    name VARCHAR(128) NOT NULL,
    processes TEXT NOT NULL, #system process names the game may run as, comman delimited
    user_id INT NOT NULL,
    CONSTRAINT fk_games_users
        FOREIGN KEY (user_id) 
        REFERENCES users(id)
);

## Administration Routes (web.php)

All routes need authentication

GET "/" => DashboardController::index => dashboard

GET "/kids" => KidController::index => list of kids with edit and delete buttons, kid name is editable inline, new kids are added inline

POST "/kid/create" => KidController::create => create new kid

PUT "/kid/{id}" => KidController::update => update kid 

DELETE "/kid/{id}" => KidController::delete => delete kid

GET "/logs" => LogController::index => list logs in paginated table

GET "/games" => GameController::index => list of games with edit and delete buttons, new games are added inline

POST "/game/create" => GameController::create => create new game

PUT "/game/{id}" => GameController::update => update game

DELETE "/game/{id}" => GameController::delete => delete game

## API Routes (api.php)

All routes require API KEY for authentication

POST "/log/create" => LogController::create => create new log entry

GET /games => GameController::index => returns list of games for the specificed user_id and API KEY

## Architecture

All controller routes must use FormRequest and Action classes

All model actions must be governed by a Policy class

## Devices

I will be testing this on Windows 11, Ubuntu 26.04, Omarchy (Arch).

The device scripts will need to run every 1 to 5 minutes. I will manually set the scheduler/cron for now.

The scripts need to reach out to the /games api endpoint to get a list of games to search the device for. After getting the list of games, the script will search for the specified process names to see if they are running. If none of the process names are running the script will end. For each of the process names that are found to be running, the script will send a request to the /log/create endpoint to log the same.

## Test Notes

Test: 4|45EoMTXbWuKnAyuRBRY1dbpndB7Vx5f21Or99lBA28fff3c3

curl -X POST http://localhost/api/log/create \
  -H "Authorization: Bearer 4|45EoMTXbWuKnAyuRBRY1dbpndB7Vx5f21Or99lBA28fff3c3" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"kid_id": 1, "message": "Your log message here"}'

curl http://localhost/api/games \
  -H "Authorization: Bearer 4|45EoMTXbWuKnAyuRBRY1dbpndB7Vx5f21Or99lBA28fff3c3" \
  -H "Accept: application/json"

  ## TODO

  - dashboard widgets
  - log table pagination

## games 
  - minecraft processes: java:minecraft, java:bedrock_server, javaw, Minecraft.Windows
  - BadNorth.exe
  - Vintagestory
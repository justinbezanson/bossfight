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
    user_id INT NOT NULL,
    message VARCHAR(512) NOT NULL,
    CONSTRAINT fk_logs_kids 
        FOREIGN KEY (kid_id) 
        REFERENCES kids(id),
    CONSTRAINT fk_logs_users
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

## API Routes (api.php)

All routes require API KEY for authentication

POST "/log/create" => LogController::create => create new log entry

## Architecture

All controller routes must use FormRequest and Action classes

All model actions must be governed by a Policy class
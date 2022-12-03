DROP TABLE IF EXISTS request;
DROP TABLE IF EXISTS response;

CREATE TABLE if not exists request
(
    id INT AUTO_INCREMENT,
    request_url TEXT NOT NULL,
    created_at timestamp default current_timestamp,
    PRIMARY KEY(id)
);

CREATE TABLE if not exists response
(
    id INT AUTO_INCREMENT,
    request_id INT NOT NULL,
    status_code TINYINT,
    body TEXT,
    header_key VARCHAR(50),
    header_value VARCHAR(100),
    created_at timestamp default current_timestamp,
    PRIMARY KEY(id),
    CONSTRAINT fk_request_id FOREIGN KEY (request_id) REFERENCES request (id) ON DELETE CASCADE
);

CREATE INDEX key_value ON response (header_key, header_value)
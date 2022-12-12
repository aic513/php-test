DROP TABLE IF EXISTS request;
DROP TABLE IF EXISTS response_headers;

CREATE TABLE if not exists request
(
    id INT AUTO_INCREMENT,
    request_url TEXT NOT NULL,
    body TEXT,
    status_code INT,
    curl_status TEXT,
    created_at timestamp default current_timestamp,
    updated_at timestamp,
    PRIMARY KEY(id)
);

CREATE TABLE if not exists response_headers
(
    id INT AUTO_INCREMENT,
    request_id INT NOT NULL,
    header_key VARCHAR(100),
    header_value VARCHAR(500),
    created_at timestamp default current_timestamp,
    updated_at timestamp,
    PRIMARY KEY(id),
    CONSTRAINT fk_request_id FOREIGN KEY (request_id) REFERENCES request (id) ON DELETE CASCADE
);

CREATE INDEX key_value ON response_headers (header_key, header_value)
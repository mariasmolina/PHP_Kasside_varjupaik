CREATE TABLE toug (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    toug_nimetus VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE loom (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    looma_nimi VARCHAR(50) NOT NULL,
    kaal DECIMAL(10,2) CHECK (kaal > 0),
    synniaeg DATE,
    sugu VARCHAR(20),
    toug_id INT,
    FOREIGN KEY (toug_id) REFERENCES toug(id)
);

CREATE TABLE toitmisajalugu (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    kuupaev DATETIME NOT NULL,
    kogus INT NOT NULL,
    toit_id INT NOT NULL,
    loom_id INT NOT NULL,
    tootaja_id INT NOT NULL,
    FOREIGN KEY (loom_id) REFERENCES loom(id),
    FOREIGN KEY (tootaja_id) REFERENCES ab_kasutajad(id),
    FOREIGN KEY (toit_id) REFERENCES toit(id)
);

CREATE TABLE toit (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    toidu_nimetus VARCHAR(100) NOT NULL UNIQUE,
    tootja VARCHAR (100) NOT NULL,
    sailivus_paevad INT,
    tyyp VARCHAR(100) NOT NULL UNIQUE
);





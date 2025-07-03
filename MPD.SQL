CREATE TABLE user (
                      id INT AUTO_INCREMENT PRIMARY KEY,
                      firstname VARCHAR(100) NOT NULL,
                      lastname VARCHAR(100) NOT NULL,
                      username VARCHAR(100) NOT NULL UNIQUE,
                      email VARCHAR(180) NOT NULL UNIQUE,
                      password VARCHAR(255) NOT NULL,
                      birth_date DATE NOT NULL,
                      slug VARCHAR(150) NOT NULL,
                      roles JSON NOT NULL,
                      newsletter BOOLEAN DEFAULT FALSE,
                      presentation TEXT,
                      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE banner (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        title VARCHAR(255),
                        description TEXT,
                        link VARCHAR(255),
                        button BOOLEAN DEFAULT FALSE,
                        button_text VARCHAR(255),
                        is_active BOOLEAN DEFAULT TRUE,
                        slug VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE recipe_category (
                                 id INT AUTO_INCREMENT PRIMARY KEY,
                                 name VARCHAR(100) NOT NULL UNIQUE,
                                 slug VARCHAR(150) NOT NULL UNIQUE
);

CREATE TABLE ingredient (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            name VARCHAR(100) NOT NULL UNIQUE,
                            slug VARCHAR(150) NOT NULL UNIQUE
);

CREATE TABLE unit (
                      id INT AUTO_INCREMENT PRIMARY KEY,
                      name VARCHAR(100) NOT NULL UNIQUE,
                      slug VARCHAR(150) NOT NULL UNIQUE
);

CREATE TABLE recipe (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        author_id INT NOT NULL,
                        title VARCHAR(255) NOT NULL,
                        slug VARCHAR(150) NOT NULL UNIQUE,
                        description TEXT NOT NULL,
                        preparation_time TIME,
                        serving INT,
                        create_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (author_id) REFERENCES user(id)
);

CREATE TABLE recipe_recipe_category (
                                        recipe_id INT NOT NULL,
                                        recipe_category_id INT NOT NULL,
                                        PRIMARY KEY (recipe_id, recipe_category_id),
                                        FOREIGN KEY (recipe_id) REFERENCES recipe(id) ON DELETE CASCADE,
                                        FOREIGN KEY (recipe_category_id) REFERENCES recipe_category(id) ON DELETE CASCADE
);

CREATE TABLE comment (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         recipe_id INT NOT NULL,
                         user_id INT NOT NULL,
                         content TEXT NOT NULL,
                         created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                         slug VARCHAR(255) NOT NULL UNIQUE,
                         FOREIGN KEY (recipe_id) REFERENCES recipe(id) ON DELETE CASCADE,
                         FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE TABLE media (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       file_name VARCHAR(255),
                       path VARCHAR(255),
                       url VARCHAR(255),
                       banner_id INT,
                       recipe_id INT,
                       FOREIGN KEY (banner_id) REFERENCES banner(id),
                       FOREIGN KEY (recipe_id) REFERENCES recipe(id)
);

CREATE TABLE recipe_ingredient (
                                   id INT AUTO_INCREMENT PRIMARY KEY,
                                   recipe_id INT NOT NULL,
                                   ingredient_id INT NOT NULL,
                                   quantity DECIMAL(8,2),
                                   unit_id INT,
                                   FOREIGN KEY (recipe_id) REFERENCES recipe(id) ON DELETE CASCADE,
                                   FOREIGN KEY (ingredient_id) REFERENCES ingredient(id),
                                   FOREIGN KEY (unit_id) REFERENCES unit(id) ON DELETE SET NULL
);

CREATE TABLE step (
                      id INT AUTO_INCREMENT PRIMARY KEY,
                      recipe_id INT NOT NULL,
                      content TEXT NOT NULL,
                      step_number INT NOT NULL,
                      FOREIGN KEY (recipe_id) REFERENCES recipe(id) ON DELETE CASCADE
);

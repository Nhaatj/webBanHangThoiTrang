CREATE TABLE `Role` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(20) NOT NULL
);

CREATE TABLE `User` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `fullname` varchar(50),
  `email` varchar(150),
  `phone_number` varchar(20),
  `address` varchar(200),
  `password` varchar(32),
  `role_id` int,
  `created_at` datetime,
  `updated_at` datetime,
  `deleted` int
);

CREATE TABLE `Tokens` (
  `user_id` int,
  `token` varchar(32),
  `created_at` datetime,
  PRIMARY KEY (`user_id`, `token`)
);

CREATE TABLE `Category` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(100) NOT NULL
);

CREATE TABLE `Product` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `category_id` int,
  `title` varchar(350),
  `price` int,
  `discount` int,
  `thumbnail` varchar(500),
  `description` longtext,
  `created_at` datetime,
  `updated_at` datetime,
  `deleted` int
);

CREATE TABLE `Galery` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `product_id` int,
  `thumbnail` varchar(500) NOT NULL
);

CREATE TABLE `FeedBack` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `firstname` varchar(30),
  `lastname` varchar(30),
  `email` varchar(150),
  `phone_number` varchar(20),
  `subject_name` varchar(200),
  `note` varchar(750),
  `status` int DEFAULT 0,
  `created_at` datetime,
  `updated_at` datetime
);

CREATE TABLE `Orders` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `fullname` varchar(50),
  `email` varchar(150),
  `phone_number` varchar(20),
  `address` varchar(200),
  `note` varchar(255),
  `order_date` datetime,
  `status` int,
  `total_money` int
);

CREATE TABLE `Order_Details` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `order_id` int,
  `product_id` int,
  `price` int,
  `num` int,
  `total_money` int
);

ALTER TABLE `User` ADD FOREIGN KEY (`role_id`) REFERENCES `Role` (`id`);

ALTER TABLE `Product` ADD FOREIGN KEY (`category_id`) REFERENCES `Category` (`id`);

ALTER TABLE `Order_Details` ADD FOREIGN KEY (`product_id`) REFERENCES `Product` (`id`);

ALTER TABLE `Galery` ADD FOREIGN KEY (`product_id`) REFERENCES `Product` (`id`);

ALTER TABLE `Order_Details` ADD FOREIGN KEY (`order_id`) REFERENCES `Orders` (`id`);

ALTER TABLE `Orders` ADD FOREIGN KEY (`user_id`) REFERENCES `User` (`id`);

ALTER TABLE `Tokens` ADD FOREIGN KEY (`user_id`) REFERENCES `User` (`id`);

ALTER TABLE Category ADD banner VARCHAR(500) NULL;

ALTER TABLE `Product` ADD `sizes` VARCHAR(255) NULL AFTER `description`;

ALTER TABLE FeedBack ADD COLUMN fullname VARCHAR(100) AFTER id;

ALTER TABLE FeedBack DROP COLUMN firstname;
ALTER TABLE FeedBack DROP COLUMN lastname;

ALTER TABLE Order_Details ADD size VARCHAR(50) NULL;

-- lưu ngày khách nhận hàng thành công
ALTER TABLE Orders ADD received_date DATETIME NULL;

ALTER TABLE Orders ADD COLUMN payment_method VARCHAR(50) DEFAULT 'COD';

CREATE TABLE Cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    num INT DEFAULT 1,
    size VARCHAR(20) DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Product(id) ON DELETE CASCADE
);

-- quản lý tổng lượng hàng tồn kho cho SP có size và lượng hàng tồn kho cho SP không có size
ALTER TABLE Product ADD COLUMN inventory_num INT DEFAULT 0;

-- quản lý lượng hàng tồn kho theo từng size của từng SP
CREATE TABLE Product_Size (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    size_name VARCHAR(50) NOT NULL,
    inventory_num INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES Product(id) ON DELETE CASCADE
);

ALTER TABLE Product DROP COLUMN sizes;

-- Thêm cột fullname mới
ALTER TABLE Feedback ADD COLUMN fullname VARCHAR(200) DEFAULT NULL AFTER id;
-- Xóa 2 cột cũ đi
ALTER TABLE Feedback DROP COLUMN firstname;
ALTER TABLE Feedback DROP COLUMN lastname;

ALTER TABLE User ADD COLUMN reset_token VARCHAR(255) DEFAULT NULL;
ALTER TABLE User ADD COLUMN reset_token_exp DATETIME DEFAULT NULL;
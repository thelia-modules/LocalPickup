SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- local_pickup_shipping
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `local_pickup_shipping`;

CREATE TABLE `local_pickup_shipping`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `price` DOUBLE NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT INTO `local_pickup_shipping`(`price`, `created_at`, `updated_at`)
   VALUES(0.0, NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;

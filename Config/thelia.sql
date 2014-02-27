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

-- ---------------------------------------------------------------------
-- Mail templates for localpickup
-- ---------------------------------------------------------------------
-- First, delete existing entries
SET @var := 0;
SELECT @var := `id` FROM `message` WHERE name="order_confirmation_localpickup";
DELETE FROM `message` WHERE `id`=@var;
-- Try if ON DELETE constraint isn't set
DELETE FROM `message_i18n` WHERE `id`=@var;

-- Then add new entries
SELECT @max := MAX(`id`) FROM `message`;
SET @max := @max+1;
-- insert message
INSERT INTO `message` (`id`, `name`, `secured`) VALUES
  (@max,
   'order_confirmation_localpickup',
   '0'
  );
-- and template fr_FR
INSERT INTO `message_i18n` (`id`, `locale`, `title`, `subject`, `text_message`, `html_message`) VALUES
  (@max,
   'fr_FR',
   'order confirmation_localpickup',
   'Reception de la commande : {$order_ref}',
   'Votre commande: {$order_ref} est disponible à l\'adresse suivante :\r\n{$store_name}\r\n{$store_address1}\r\n{$store_address2}\r\n{$store_address3}\r\n{$store_zipcode} {$store_city}\r\n{$store_country} ',
   '<html><head></head><body><p>Votre commande: {$order_ref} est disponible à l\'adresse suivante :<br/>{$store_name}<br/>{$store_address1}{if !empty($store_address2)}<br/>{$store_address2}{/if}{if !empty($store_address3)}<br/>{$store_address3}{/if}<br/>{$store_zipcode} {$store_city}<br/>{$store_country}</p></body></html>  '
  );
-- and en_US
INSERT INTO `message_i18n` (`id`, `locale`, `title`, `subject`, `text_message`, `html_message`) VALUES
  (@max,
   'en_US',
   'order confirmation_localpickup',
   'Reception de la commande : {$order_ref}',
   'Votre commande: {$order_ref} est disponible à l\'adresse suivante :\r\n{$store_name}\r\n{$store_address1}\r\n{$store_address2}\r\n{$store_address3}\r\n{$store_zipcode} {$store_city}\r\n{$store_country} ',
   '<html><head></head><body><p>Votre commande: {$order_ref} est disponible à l\'adresse suivante :<br/>{$store_name}<br/>{$store_address1}{if !empty($store_address2)}<br/>{$store_address2}{/if}{if !empty($store_address3)}<br/>{$store_address3}{/if}<br/>{$store_zipcode} {$store_city}<br/>{$store_country}</p></body></html>'
  );

SET FOREIGN_KEY_CHECKS = 1;

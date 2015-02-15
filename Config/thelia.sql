SET FOREIGN_KEY_CHECKS = 0;

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

INSERT INTO `message_i18n` (`id`, `locale`, `title`, `subject`, `text_message`, `html_message`) VALUES
(@max, 'en_US', 'Local Pickup notification email', 'Reception de la commande : {$order_ref}', 'Hello,\r\nYour order {$order_ref} is ready for pickup at the following address :\r\n    {$store_name}\r\n    {$store_address1}\r\n    {if !empty($store_address2)}{$store_address2}{/if}\r\n    {if !empty($store_address3)}{$store_address3}{/if}\r\n    {$store_zipcode} {$store_city}\r\n    {$store_country} \r\nRegards.', '<html><head></head><body>\r\n<p>Hello,</p>\r\n<p>Your order {$order_ref} is ready for pickup at the following address:</p>\r\n    <p>{$store_name}<br/>\r\n    {$store_address1}<br />\r\n    {if !empty($store_address2)}{$store_address2}<br/>{/if}\r\n    {if !empty($store_address3)}{$store_address3}<br/>{/if}\r\n    {$store_zipcode} {$store_city}<br/>\r\n    {$store_country}</p>\r\n<p>Regards.</p>\r\n</body></html>'),
(@max, 'fr_FR', 'Email de notification de retrait en magasin', 'Reception de la commande : {$order_ref}', 'Bonjour,\r\nVotre commande {$order_ref} est disponible à l''adresse suivante :\r\n    {$store_name}\r\n    {$store_address1}\r\n    {if !empty($store_address2)}{$store_address2}{/if}\r\n    {if !empty($store_address3)}{$store_address3}{/if}\r\n    {$store_zipcode} {$store_city}\r\n    {$store_country} \r\nCordialement.', '<html><head></head><body>\r\n<p>Bonjour,</p>\r\n<p>Votre commande: {$order_ref} est disponible à l''adresse suivante :<br/>\r\n{$store_name}<br/>\r\n{$store_address1}<br/>\r\n{if !empty($store_address2)}{$store_address2}<br/>{/if}\r\n{if !empty($store_address3)}{$store_address3}<br/>{/if}\r\n{$store_zipcode} {$store_city}<br/>\r\n{$store_country}</p>\r\n<p>Cordialement</p>\r\n</body></html>');

SET FOREIGN_KEY_CHECKS = 1;

LocalPickup module
==================
author: Thelia <info@thelia.net>

Summary
-------

fr_FR:
1.  Installation
2.  Utilisation
3.  Boucles
4.  Intégration

en_US:
1.  Install notes
2.  How to use
3.  Loops
4.  Integration

fr_FR
-----

### Installation

#### Manually

* Copiez le module dans le dossier ```<thelia_root>/local/modules/```  et assurez-vous que le nom du module est bien LocalPickup.
* Activez le depuis votre interface d'administration Thelia.

#### Composer

Ajoutez le module à votre fichier composer.json principal :

```
composer require thelia/local-pickup-module:~1.0
```

### Utilisation

Pour utiliser le module de retrait sur place, allez dans le back-office, onglet Modules, et activez le,
puis cliquez sur "Configurer" sur la ligne du module. Renseignez le prix que vous souhaitez donner au retrait sur place
et enregistrez.

### Boucles

1.  `address.local`
    Même sorties que la boucle `address`, mais avec l'adresse du magasin au lieu de celle du client.
    - Arguments:
        1. id | obligatoire | id de l'adresse du client
    - Sorties:
        Les mêmes variables que la boucle address, mais l'adresse donnée est celle du magasin.
    - Utilisation:
        ```
        {loop type="address.local" name="yourloopname" id="1"}
            <!-- your template -->
        {/loop}```

### Intégration

L'integration utilise les hooks et ne nécessite pas de travaux particuliers.

## Envoi de SMS

Ce module utilise le composant symfony/notifier. Lors de l'installation du module, un fichier notifier.yaml sera ajouté 
dans votre configuration avec une configuration par défaut.

Si vous ne souhaitez pas envoyer de SMS avec ce module, il vous suffit de configurer le fichier notifier.yaml pour ne pas utiliser de texter:


    framework:
        notifier:
            texter_transports:

Vous pouvez spécifier un canal SMS particulier pour votre notifier (https://symfony.com/doc/current/notifier.html#sms-channel
) et mettre à jour votre configuration.

Par exemple, si vous utilisez Brevo :

    framework:
    notifier:
        texter_transports:
            brevo: '%env(BREVO_DSN)%'

Ensuite, mettez à jour votre fichier .env avec votre clé API :

    ###> symfony/brevo-notifier ###
    BREVO_DSN=brevo://API_KEY@default?sender=SENDER
    ###< symfony/brevo-notifier ###

en_US
-----

### Installation notes

#### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is LocalPickup.
* Activate it in your thelia administration panel

#### Composer

Add it in your main thelia composer.json file:

```
composer require thelia/local-pickup-module:~1.0
```

### Usage

To use the module, you first need to activate it in the back-office, tab Modules, and click on "Configure" on the line
of the module. Enter the price you want for local pickup and save.

### Loops
1.  `address.local`
    Same output as the `address` loop, with the store adresse instead of the customer address. 
    - Arguments:
        1. id | mandatory | id of the customer's address
    - Output:
        The same variables as address loop, but the given address is the store's address.
    - Usage:
        ```
        {loop type="address.local" name="yourloopname" id="1"}
            <!-- your template -->
        {/loop}
        ```

### Integration

The modules uses hooks, and does not require specific work.

## Sending SMS

This module use the symfony/notifier component. 
When requiring the module, it will add a notifier.yaml file in your config with a default config. 

If you do not wish to send SMS with this module, just configure the `notifier.yaml` file to not use any texter:

    framework:
        notifier:
            texter_transports:

You can require a specific sms channel for your notifier (https://symfony.com/doc/current/notifier.html#sms-channel) and 
then update your configuration. 
For instance, if you use Brevo:

    framework:
    notifier:
        texter_transports:
            brevo: '%env(BREVO_DSN)%'

And then update your .env file with your api key:

    ###> symfony/brevo-notifier ###
    BREVO_DSN=brevo://API_KEY@default?sender=SENDER
    ###< symfony/brevo-notifier ###



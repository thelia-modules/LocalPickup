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

1.  address.local
    - Arguments:
        1. id | obligatoire | id de l'adresse du client
    - Sorties:
        Les mêmes variables que la boucle address, mais l'adresse donnée est celle du magasin.
    - Utilisation:
        ```{loop type="address.local" name="yourloopname" id="1"}
            <!-- your template -->
        {/loop}```
2.  localpickupid
    - Arguments: pas d'arguments
    -Sorties:
        1. \$MODULE_ID : id du module retrait sur place
    -Utilisation:
        ```{loop type="localpickupid" name="yourloopname"}
        {$MODULE_ID}
        {/loop}```

### Intégration
Pour intégrer ce module, seule la page "order-invoice" est à modifier.
En effet, deux boucles sont disponibles: localpickupid et address.local
Le but de la modification est de vérifier si le module utilisé pour la livraison est le retrait sur place
en comparant les ids, et si tel est le cas, utiliser la boucle address.local au lieu de address pour afficher l'address du magasin


en_US
-----

### Install notes

#### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is LocalPickup.
* Activate it in your thelia administration panel

#### Composer

Add it in your main thelia composer.json file:

```
composer require thelia/local-pickup-module:~1.0
```

### How to use

To use the module, you first need to activate it in the back-office, tab Modules, and click on "Configure" on the line
of the module. Enter the price you want for local pickup and save.

### Loops
1.  address.local
    - Arguments:
        1. id | mandatory | id of the customer's address
    - Output:
        The same variables as address loop, but the given address is the store's address.
    - Usage:
        ```{loop type="address.local" name="yourloopname" id="1"}
            <!-- your template -->
        {/loop}```
2.  localpickupid
    - Arguments: no args
    -Output:
        1. \$MODULE_ID : id of LocalPickup module
    -Usage:
        ```{loop type="localpickupid" name="yourloopname"}
        {$MODULE_ID}
        {/loop}```


### Integration
This module only requires to edit "order-invoice".
To do the job, you have two loops: localpickupid and address.local
The goal is to check if the order's delivery module is local pickup by comparing the ids,
and if the module is local pickup, use the loop address.local instead of address to show the store's address.
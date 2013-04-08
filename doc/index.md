# Utilisation du client API Photos FranceProNet

L'API fournit 2 classes utilitaires vous permettant de manipuler simplement les presets et les filtres les composant.

## La classe Preset

Simple d'utilisation, il suffit de l'instancier et via quelques méthodes on peut gérer complètement ses presets.

```php
<?php

use Fpn\ApiClient\Pictures\Preset;

// récupère le preset #3
$preset = new Preset();
$preset->fetch(3);
echo $preset->getId(); // "3"
var_dump($preset->getFilters()); // array de tous les filtres du preset (objets Filtre, cf. ci-après)

// récupère la page 2 d'une liste de preset, avec 10 presets par page
$preset = new Preset();
$presets = $preset->fetchAll(2, 10); // les paramètres sont optionnels et ont pour valeur par défaut : page = 1, limit = 20

// créé un nouveau preset
$preset = new Preset();
$preset
    ->setName('Mon preset')
    ->setPosId(10)
    ->save()
    ;
echo $preset->getId(); // après la sauvegarde, l'attribut ID de la classe a été set avec l'ID du preset créé

// modifier un preset
$preset = new Preset();
$preset
    ->fetch(3)
    ->setName('Nouveau nom')
    ->save()
    ;

// supprimer un preset
$preset = new Preset();
$preset
    ->fetch(3)
    ->delete()
    ;

// appliquer un preset à une image
$preset = new Preset();
$preset
    ->fetch(3)
    ->apply('/path/to/source/image.jpg', '/path/to/modified/image.jpg')
    ;
```

## La classe Filtre

Comme pour la classe Preset, on utilise une instance de la classe Filter :

```php
<?php

use Fpn\ApiClient\Pictures\Preset;
use Fpn\ApiClient\Pictures\Filter;

// récupérer les filtres du preset #3
$preset = new Preset();
$preset->fetch(3);
$filters = $presets->getFilters();

// récupérer le filtre #4 appartenant au preset #3
$filter = new Filter();
$filter
    ->setPresertId(3)
    ->fetch(4)
    ;
echo $filter->getId(); // "4"

// ajouter un filtre au preset #3
$filter = new Filter();
$filter
    ->setPresetId(3)
    ->setType('addBorder')
    ->setParams(array(5))
    ->save()
    ;

// modifier le filtre #4 appartenant au preset #3
$filter = new Filter();
$filter
    ->setPresetId(3)
    ->fetch(4)
    ->setType('roundCorners')
    ->save()
    ;

// supprimer le filtre #4 appartenant au preset #3
$filter = new Filter();
$filter
    ->setPresetId(3)
    ->fetch(4)
    ->delete()
    ;
```

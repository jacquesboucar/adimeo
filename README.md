# Test Drupal

## Description de l'existant
Le site est déjà installé (profile standard), la db est à la racine du projet.
Un **type de contenu** `Événement` a été créé et des contenus générés avec Devel. Il y a également une **taxonomie** `Type d'événement` avec des termes.

La version du core est la 9.4.8 et le composer lock a été généré sur PHP 8.1.

Les files sont versionnées sous forme d'une archive compressée. Vous êtes invité à créer un fichier `settings.local.php` pour renseigner vos accès à la DB. Le fichier `settings.php` est lui versionné.

Un environnement local avec docker a été mis en place avec une image php 8.1 et un serveur apache. Pour le lancer voici les étapes à suivre :
* **docker-compose build**
* **docker-compose up -d**
* **docker-compose exec web composer install**
* **docker-compose exec web vendor/bin/drush cim**
* **docker-compose exec web vendor/bin/drush cr**
* Importer la base de donnée initiale
* Vous pouvez maintenant accéder au site depuis l'url adimeo.local penser à l'ajouter dans votre host

## Consignes

### 1. Faire un bloc custom (plugin annoté)
* s'affichant sur la page de détail d'un événement ;
* et affichant 3 autres événements du même type (taxonomie) que l'événement courant, ordonnés par date de début (asc), et dont la date de fin n'est pas dépassée ;
* S'il y a moins de 3 événements du même type, compléter avec un ou plusieurs événements d'autres types, ordonnés par date de début (asc), et dont la date de fin n'est pas dépassée.


Pour cet exercice nous avons choisi d'afficher le bloc "Mes evenements liés" en bas de la page de détail d'un evenement.
Dans le cas ou il y'a moins de 3 événements du même type nous avons aussi choisi d'afficher juste 3 autres événements d'autres types

### 2. Faire une tache cron
qui **dépublie** les événements dont la date de fin est dépassée à l'aide d'un **QueueWorker**.
Il est mieux de lancer ce genre de tâche en background plutot que d'utiliser le cron drupal par exemple qui dépend d'une requête de page
et donc pourrait avoir des lenteurs.
Nous avons choisi de creer une commande drush pour ajouter l'ensemble des événements à dépublier dans le queue. Et l'execution du queue
pourra etre effectués avec drush. Ces commandes drush pourront etre executés dans un crontab, un serveur de cron.
* docker-compose exec web vendor/bin/drush event:unpublish //Ajout des events dans le queue
* docker-compose exec web vendor/bin/drush queue:run unpublish_events_queue_worker // lancer le queue pour supprimer les events


## Rendu attendu
**Vous devez cloner ce repo, MERCI DE NE PAS LE FORKER,** et nous envoyer soit un lien vers votre propre repo, soit un package avec :

* votre configuration exportée ;
* **et/ou** un dump de base de données ;
* **et pourquoi pas** des readme, des commentaires etc. :)

**Le temps que vous avez passé** : par mail ou dans un readme par exemple.

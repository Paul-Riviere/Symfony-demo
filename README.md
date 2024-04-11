# Symfony demo

Demo de Symfony dans le cadre d'une formation.

Ce projet a été développé en utilisant une base MySQL via Wamp.

## Utilisation

Pour démarrer le projet, il faut :

- Renseigner dans le .env la connexion à la BDD, en changeant le user/mdp et le nom/port de la bdd.
- Créer la base de données : `php bin/console doctrine:database:create`
- Appliquer la migration : `php bin/console doctrine:migrations:migrate`
- Charger les fixtures : `php bin/console doctrine:fixtures:load`
- Démarrer le serveur : `symfony server:start`

## Contenu du projet

Le projet comporte une commande console : `app:import-articles` qui possède plusieurs options possibles.

Une authentification par JWT est en place sur les routes /api/xxx, il faut donc récupérer un jeton JWT via /api/login_check en fournissant dans le body un json contenant "username" et "password"

Les tests unitaires ne sont pas fonctionnels en l'état, les utilisateurs étant créés aléatoirement, il faut changer le username dans le fichier tests/articleTest.php.

Enfin, un controller `ExempleController` présente différentes routes d'exemple, comme pour présenter la serialisation.


MoulaCiné est une application de panel d’administration pour la recherche de films. Elle permet d’ajouter, de modifier, de supprimer et de rechercher des films via l’API OMDb.

Prérequis
Symfony 7 doit être installé sur votre machine.
MAMP doit être installé pour gérer la base de données.
Installation


Accédez au répertoire du projet :
cd project-om

Installez les dépendances du projet :
composer install

Configurez votre fichier .env avec vos informations d’API OMDb.
Configuration de la base de données
Lancez MAMP et démarrez les serveurs Apache et MySQL. 
Ouvrez un nouveau terminal et tapez la commande suivante pour démarrer MySQL :
startmysql

Accédez à phpMyAdmin en tapant la commande suivante dans votre terminal :
phpmyadmin

Importez le fichier de votre base de données via le lien fourni.
Lancement du projet
Ouvrez votre terminal.
Lancez le serveur Symfony :
symfony server:start

Ouvrez un autre terminal et lancez la commande suivante pour compiler les bundles et tout ce qui est lié au CSS du site :
npm run watch

Accédez à l’application via votre navigateur à l’adresse suivante :
http://localhost:8000

Fonctionnalités
Ajouter un film : Ajoutez de nouveaux films à la base de données.
Modifier un film : Mettez à jour les informations des films existants.
Supprimer un film : Supprimez des films de la base de données et de la liste de films.
Rechercher un film : Recherchez des films via l’API OMDb et l'ajouté à notre liste de films et à notre base de données.

Technologies utilisées
Symfony 7
API OMDb
MAMP
npm
Tailwind CSS

Le sites assez simple d'utilisation vous pouvez naviguer facilement dessus.

Explications supplémentaires
J’ai réalisé cette application avec Symfony comme il me l’était demandé. J’ai utilisé les commandes make:crud et make:entity pour générer les contrôleurs de mon application. Ces commandes m’ont permis de générer automatiquement le squelette de mon application, facilitant ainsi l’intégration de ma base de données via Doctrine.

Pour le style, j’ai opté pour Tailwind CSS car je peux directement changer le style sur mes templates Twig, ce qui m’a permis d’apporter des changements rapides sur le design de la page. , j’ai essayé d’aller au plus simple.

En cas d’erreur, commencez par analyser le message d’erreur. Symfony renvoie directement les erreurs sur votre écran. Il peut s’agir d’une erreur de configuration ou de dépendance. Assurez-vous que vos fichiers sont corrects, notamment les fichiers comme config/packages/doctrine.yaml et config/packages/twig.yaml. Ces fichiers se trouvent dans l’arborescence du projet. N’hésitez pas à utiliser la documentation Symfony pour vous aider.

ABDOU Nouroudine.

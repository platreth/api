# P7 - Projet OpenClassrooms

Créez un web service exposant une API

## Installation

*   Clonez ou téléchargez le repository GitHub :
```system
git clone https://github.com/platreth/api.git
```
*   Configurez vos variables d'environnement telles que la connexion à la base de données .env

*   Installez les dépendances avec Composer :
```system
composer install
```

*   Créez la structure de la base de données :
```system
php bin/console doctrine:schema:create
```

*   Créez les fixtures vous permettant de tester :
```system
php bin/console doctrine:fixtures:load
```

*   Accédez à l'aide de l'API :
127.0.0.1:8000/doc (en fonction de l'adresse d'hébergement de l'API)

*   Se connecter et obtenir un token :
Requête GET sur http://127.0.0.1:8000/login_check, body {"username": "clientsfr@client.com", "password": "clientsfr"}

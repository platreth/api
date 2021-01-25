# P7 - Projet OpenClassrooms

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/fbd99af55506498c821bc46eb2629ecd)](https://app.codacy.com/manual/platreth/api?utm_source=github.com&utm_medium=referral&utm_content=platreth/api&utm_campaign=Badge_Grade_Dashboard)

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

* update base de données :
```system
php bin/console doctrine:server:update
```

*   Accédez à l'aide de l'API :
127.0.0.1:8000/api/doc (en fonction de l'adresse d'hébergement de l'API)

*   Collection postman :
https://www.getpostman.com/collections/e004297d449a8c5cd41b


# Guide de déploiement Alwaysdata

## Étapes de déploiement

### 1. Configuration Alwaysdata

#### A. Créer la base de données MySQL
1. Connectez-vous à votre compte Alwaysdata
2. Allez dans **Bases de données** > **MySQL**
3. Cliquez sur **Ajouter une base de données**
4. Nom : `votrecompte_mediatekformation`
5. Notez les identifiants de connexion

#### B. Configurer le site web
1. Allez dans **Web** > **Sites**
2. Cliquez sur **Ajouter un site**
3. Adresses : `votrecompte.alwaysdata.net` (ou votre domaine)
4. Type : **PHP**
5. Version PHP : **8.1** ou supérieure
6. Racine : `/www/public` (important pour Symfony)
7. Enregistrer

#### C. Configurer SSH
1. Allez dans **Accès distant** > **SSH**
2. Activez l'accès SSH
3. Notez vos identifiants SSH

### 2. Déploiement via SSH

#### A. Se connecter en SSH
```bash
ssh votrecompte@ssh-votrecompte.alwaysdata.net
```

#### B. Cloner le projet
```bash
cd www
git clone https://github.com/zennouikram/mediatekformation.git .
```

#### C. Installer les dépendances
```bash
composer install --no-dev --optimize-autoloader
```

#### D. Configurer l'environnement
```bash
nano .env.local
```

Ajouter :
```
APP_ENV=prod
APP_SECRET=VOTRE_CLE_SECRETE_ALEATOIRE
DATABASE_URL="mysql://votrecompte:motdepasse@mysql-votrecompte.alwaysdata.net:3306/votrecompte_mediatekformation?serverVersion=8.0&charset=utf8mb4"
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
```

#### E. Créer le cache et les permissions
```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
chmod -R 777 var/
```

#### F. Importer la base de données
```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

Ou importer le fichier SQL :
```bash
mysql -h mysql-votrecompte.alwaysdata.net -u votrecompte -p votrecompte_mediatekformation < mediatekformation.sql
```

#### G. Créer l'utilisateur admin
```bash
php bin/console app:create-admin-user
```

### 3. Mettre à jour la page CGU

Modifier le template CGU avec la bonne URL :
```bash
nano templates/pages/cgu.html.twig
```

Remplacer toutes les occurrences de `localhost` par `votrecompte.alwaysdata.net`

### 4. Vérification

1. Visitez : `https://votrecompte.alwaysdata.net`
2. Testez l'accès admin : `https://votrecompte.alwaysdata.net/admin`
3. Vérifiez que toutes les pages fonctionnent

### 5. Mises à jour futures

```bash
ssh votrecompte@ssh-votrecompte.alwaysdata.net
cd www
git pull
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console doctrine:migrations:migrate --no-interaction
```

## Dépannage

### Erreur 500
```bash
tail -f ~/admin/logs/error.log
```

### Problèmes de permissions
```bash
chmod -R 777 var/
```

### Cache non actualisé
```bash
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod
```

## Documentation technique

La documentation technique doit inclure :
- Architecture de l'application
- Schéma de base de données
- Guide d'installation
- Guide d'utilisation admin
- Ce guide de déploiement

Hébergez la documentation sur le même serveur dans un dossier `/docs` ou utilisez GitHub Pages.

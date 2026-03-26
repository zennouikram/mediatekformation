# Documentation Technique - Mediatekformation

## 1. Architecture de l'application

### Technologies utilisées
- **Framework** : Symfony 6.x
- **Langage** : PHP 8.1+
- **Base de données** : MySQL 8.0
- **Template engine** : Twig
- **ORM** : Doctrine
- **Frontend** : Bootstrap 5, JavaScript vanilla

### Structure du projet
```
mediatekformation/
├── bin/                    # Scripts console
├── config/                 # Configuration Symfony
├── migrations/            # Migrations de base de données
├── public/                # Point d'entrée web
├── src/
│   ├── Command/          # Commandes console
│   ├── Controller/       # Contrôleurs
│   ├── Entity/           # Entités Doctrine
│   ├── Form/             # Formulaires
│   └── Repository/       # Repositories Doctrine
├── templates/            # Templates Twig
│   ├── Backoffice/      # Templates admin
│   └── pages/           # Templates publiques
└── tests/               # Tests unitaires et fonctionnels
    ├── Unit/
    ├── Integration/
    └── Functional/
```

## 2. Schéma de base de données

### Table: formation
- `id` (INT, PK, AUTO_INCREMENT)
- `title` (VARCHAR 100)
- `description` (TEXT)
- `video_id` (VARCHAR 20) - ID YouTube
- `published_at` (DATETIME)
- `playlist_id` (INT, FK)

### Table: playlist
- `id` (INT, PK, AUTO_INCREMENT)
- `name` (VARCHAR 100)
- `description` (TEXT)

### Table: categorie
- `id` (INT, PK, AUTO_INCREMENT)
- `name` (VARCHAR 50)

### Table: formation_categorie (Many-to-Many)
- `formation_id` (INT, FK)
- `categorie_id` (INT, FK)

### Table: user
- `id` (INT, PK, AUTO_INCREMENT)
- `email` (VARCHAR 180, UNIQUE)
- `roles` (JSON)
- `password` (VARCHAR 255)

## 3. Fonctionnalités principales

### Partie publique
1. **Page d'accueil** (`/`)
   - Affiche les 2 dernières formations
   - Contrôleur: `AccueilController::index()`

2. **Liste des formations** (`/formations`)
   - Affichage de toutes les formations
   - Tri et filtrage par titre, playlist, catégorie
   - Contrôleur: `FormationsController::index()`

3. **Détail d'une formation** (`/formations/{id}`)
   - Affichage de la vidéo YouTube
   - Informations détaillées
   - Contrôleur: `FormationsController::showOne()`

4. **Playlists** (`/playlists`)
   - Liste des playlists
   - Tri et filtrage
   - Contrôleur: `PlaylistsController::index()`

5. **CGU** (`/cgu`)
   - Mentions légales
   - Contrôleur: `AccueilController::cgu()`

### Partie administration (sécurisée)
Accès: `/admin` (authentification requise)

1. **Gestion des formations**
   - Liste: `/admin/formations`
   - Ajout: `/admin/formations/ajout`
   - Modification: `/admin/formations/edit/{id}`
   - Suppression: `/admin/formations/suppr/{id}`

2. **Gestion des playlists**
   - Liste: `/admin/playlists`
   - Ajout: `/admin/playlists/ajout`
   - Modification: `/admin/playlists/edit/{id}`
   - Suppression: `/admin/playlists/suppr/{id}`

3. **Gestion des catégories**
   - Liste: `/admin/categories`
   - Ajout: `/admin/categories/ajout`
   - Modification: `/admin/categories/edit/{id}`
   - Suppression: `/admin/categories/suppr/{id}`

## 4. Sécurité

### Authentification
- Système de login Symfony Security
- Route de connexion: `/admin/login`
- Rôle requis: `ROLE_ADMIN`

### Protection CSRF
- Tous les formulaires sont protégés par token CSRF

### Validation des données
- Validation côté serveur avec Symfony Validator
- Validation côté client avec HTML5

## 5. Tests

### Tests unitaires
- **FormationTest**: Tests des méthodes de l'entité Formation
- Localisation: `tests/Unit/`

### Tests d'intégration
- **FormationRepositoryTest**: Tests des requêtes personnalisées
- Localisation: `tests/Integration/`

### Tests fonctionnels
- **AccueilTest**: Tests des pages publiques
- Localisation: `tests/Functional/`

### Exécution des tests
```bash
php bin/phpunit
```

## 6. Installation locale

### Prérequis
- PHP 8.1+
- MySQL 8.0+
- Composer
- Git

### Étapes d'installation
```bash
# Cloner le projet
git clone https://github.com/zennouikram/mediatekformation.git
cd mediatekformation

# Installer les dépendances
composer install

# Configurer la base de données
cp .env .env.local
# Éditer .env.local avec vos paramètres MySQL

# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Importer les données (optionnel)
mysql -u root -p mediatekformation < mediatekformation.sql

# Créer un utilisateur admin
php bin/console app:create-admin-user

# Lancer le serveur de développement
symfony server:start
# ou
php -S localhost:8000 -t public/
```

## 7. Déploiement en production

Voir le fichier `DEPLOYMENT_GUIDE.md` pour les instructions détaillées de déploiement sur Alwaysdata.

### Points importants
1. Configurer `APP_ENV=prod` dans `.env.local`
2. Générer une clé secrète unique pour `APP_SECRET`
3. Configurer la connexion à la base de données de production
4. Vider le cache: `php bin/console cache:clear --env=prod`
5. Définir les permissions: `chmod -R 777 var/`

## 8. Maintenance

### Mise à jour du code
```bash
git pull
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console doctrine:migrations:migrate --no-interaction
```

### Sauvegarde de la base de données
```bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
```

### Logs
- Logs d'erreur: `var/log/prod.log`
- Logs de développement: `var/log/dev.log`

## 9. API et méthodes personnalisées

### FormationRepository

#### `findAllOrderBy($champ, $ordre, $table="")`
Retourne toutes les formations triées sur un champ.
- **Paramètres**:
  - `$champ`: Nom du champ de tri
  - `$ordre`: 'ASC' ou 'DESC'
  - `$table`: Nom de la table jointe (optionnel)

#### `findAllLasted($nb)`
Retourne les N formations les plus récentes.
- **Paramètres**:
  - `$nb`: Nombre de formations à retourner

#### `findByContainValue($champ, $valeur, $table="")`
Recherche les formations contenant une valeur.
- **Paramètres**:
  - `$champ`: Nom du champ de recherche
  - `$valeur`: Valeur recherchée
  - `$table`: Nom de la table jointe (optionnel)

#### `findAllForOnePlaylist($idPlaylist)`
Retourne toutes les formations d'une playlist.
- **Paramètres**:
  - `$idPlaylist`: ID de la playlist

### Formation Entity

#### `getPublishedAtString()`
Retourne la date de publication formatée en 'd/m/Y'.

#### `getMiniature()`
Retourne l'URL de la miniature YouTube (default.jpg).

#### `getPicture()`
Retourne l'URL de l'image YouTube haute qualité (hqdefault.jpg).

## 10. Support et contact

Pour toute question technique:
- Email: contact@mediatekformation.fr
- GitHub Issues: https://github.com/zennouikram/mediatekformation/issues

## 11. Licence

Ce projet est développé dans un cadre éducatif.

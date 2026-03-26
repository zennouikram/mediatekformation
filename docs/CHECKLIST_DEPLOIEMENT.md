# Checklist de déploiement - Mediatekformation

## ✅ Avant le déploiement

### 1. Compte Alwaysdata
- [ ] Créer un compte sur https://www.alwaysdata.com/
- [ ] Noter le nom de compte (ex: `votrecompte`)
- [ ] Activer l'accès SSH

### 2. Configuration locale
- [ ] Tous les tests passent (`php bin/phpunit`)
- [ ] Le code est commité sur GitHub
- [ ] Les fichiers `.env.prod` et `.htaccess` sont créés

## ✅ Configuration Alwaysdata

### 3. Base de données MySQL
- [ ] Se connecter au panel Alwaysdata
- [ ] Aller dans **Bases de données** > **MySQL**
- [ ] Cliquer sur **Ajouter une base de données**
- [ ] Nom: `votrecompte_mediatekformation`
- [ ] Noter:
  - Utilisateur: `_________________`
  - Mot de passe: `_________________`
  - Hôte: `mysql-votrecompte.alwaysdata.net`
  - Port: `3306`

### 4. Configuration du site web
- [ ] Aller dans **Web** > **Sites**
- [ ] Cliquer sur **Ajouter un site**
- [ ] Configuration:
  - Adresses: `votrecompte.alwaysdata.net`
  - Type: **PHP**
  - Version PHP: **8.1** ou **8.2**
  - Racine: `/www/public` ⚠️ IMPORTANT
- [ ] Enregistrer

### 5. Accès SSH
- [ ] Aller dans **Accès distant** > **SSH**
- [ ] Activer l'accès SSH
- [ ] Noter les identifiants SSH

## ✅ Déploiement

### 6. Connexion SSH et clonage
```bash
ssh votrecompte@ssh-votrecompte.alwaysdata.net
cd www
git clone https://github.com/zennouikram/mediatekformation.git .
```
- [ ] Connexion SSH réussie
- [ ] Projet cloné

### 7. Installation des dépendances
```bash
composer install --no-dev --optimize-autoloader
```
- [ ] Dépendances installées sans erreur

### 8. Configuration de l'environnement
```bash
nano .env.local
```

Contenu à ajouter:
```
APP_ENV=prod
APP_SECRET=GENERER_UNE_CLE_ALEATOIRE_ICI
DATABASE_URL="mysql://votrecompte:MOTDEPASSE@mysql-votrecompte.alwaysdata.net:3306/votrecompte_mediatekformation?serverVersion=8.0&charset=utf8mb4"
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
```

- [ ] Fichier `.env.local` créé
- [ ] `APP_SECRET` généré (utilisez: `php bin/console secrets:generate-keys`)
- [ ] `DATABASE_URL` configuré avec les bons identifiants

### 9. Import de la base de données

**Option A: Via migrations**
```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

**Option B: Via fichier SQL**
```bash
mysql -h mysql-votrecompte.alwaysdata.net -u votrecompte -p votrecompte_mediatekformation < mediatekformation.sql
```

- [ ] Base de données importée
- [ ] Tables créées

### 10. Création de l'utilisateur admin
```bash
php bin/console app:create-admin-user
```
- [ ] Utilisateur admin créé
- [ ] Email admin noté: `_________________`
- [ ] Mot de passe admin noté: `_________________`

### 11. Configuration du cache et permissions
```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
chmod -R 777 var/
```
- [ ] Cache vidé et réchauffé
- [ ] Permissions configurées

## ✅ Mise à jour de la page CGU

### 12. Mettre à jour l'URL dans CGU
Sur votre machine locale:
```bash
# Remplacer votrecompte par votre vrai nom de compte
bash update_cgu.sh votrecompte.alwaysdata.net
```

Ou manuellement éditer `templates/pages/cgu.html.twig`:
- [ ] Remplacer `http://www.mediatekformation.fr` par `https://votrecompte.alwaysdata.net`
- [ ] Remplacer `mediatekformation.fr` par `votrecompte.alwaysdata.net`

### 13. Déployer la mise à jour CGU
```bash
git add templates/pages/cgu.html.twig
git commit -m "Update CGU with production URL"
git push
```

Sur le serveur:
```bash
ssh votrecompte@ssh-votrecompte.alwaysdata.net
cd www
git pull
php bin/console cache:clear --env=prod
```
- [ ] CGU mise à jour et déployée

## ✅ Vérification

### 14. Tests de fonctionnement
- [ ] Site accessible: `https://votrecompte.alwaysdata.net`
- [ ] Page d'accueil s'affiche correctement
- [ ] Les formations s'affichent
- [ ] Les playlists s'affichent
- [ ] Page CGU accessible et URL correcte
- [ ] Vidéos YouTube se chargent

### 15. Tests de l'administration
- [ ] Page de login accessible: `https://votrecompte.alwaysdata.net/admin`
- [ ] Connexion admin fonctionne
- [ ] Liste des formations admin accessible
- [ ] Ajout d'une formation fonctionne
- [ ] Modification d'une formation fonctionne
- [ ] Suppression d'une formation fonctionne
- [ ] Gestion des playlists fonctionne
- [ ] Gestion des catégories fonctionne

### 16. Tests de sécurité
- [ ] Accès admin sans login redirige vers login
- [ ] HTTPS activé (cadenas dans le navigateur)
- [ ] Pas d'erreurs dans les logs

## ✅ Documentation

### 17. Hébergement de la documentation
- [ ] Documentation technique uploadée sur le serveur
- [ ] Accessible via `/docs` ou GitHub Pages
- [ ] README.md à jour sur GitHub

### 18. Informations à fournir
- [ ] URL du site: `https://votrecompte.alwaysdata.net`
- [ ] URL de la documentation: `_________________`
- [ ] Identifiants admin de test (si demandé)
- [ ] Lien GitHub du projet

## ✅ Post-déploiement

### 19. Monitoring
- [ ] Vérifier les logs: `tail -f ~/admin/logs/error.log`
- [ ] Tester toutes les fonctionnalités une dernière fois
- [ ] Créer une sauvegarde de la base de données

### 20. Documentation finale
- [ ] Capturer des screenshots du site en production
- [ ] Documenter les identifiants (dans un endroit sécurisé)
- [ ] Partager l'URL avec les parties prenantes

## 🎉 Déploiement terminé !

**Informations importantes à conserver:**
- URL du site: `https://votrecompte.alwaysdata.net`
- URL admin: `https://votrecompte.alwaysdata.net/admin`
- Email admin: `_________________`
- Mot de passe admin: `_________________`
- Accès SSH: `ssh votrecompte@ssh-votrecompte.alwaysdata.net`
- Base de données: `mysql-votrecompte.alwaysdata.net:3306`

## 🔄 Mises à jour futures

Pour mettre à jour le site après modifications:
```bash
# Sur votre machine locale
git add .
git commit -m "Description des modifications"
git push

# Sur le serveur
ssh votrecompte@ssh-votrecompte.alwaysdata.net
cd www
git pull
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console doctrine:migrations:migrate --no-interaction
```

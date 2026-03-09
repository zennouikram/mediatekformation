# Admin Setup Steps

## 1. Database Migration
- Run migration to create user table

## 2. Create Admin User
- Execute command to create admin account

## 3. Files Modified

### Created Files:
- `migrations/Version20260309000000.php`
- `src/Command/CreateAdminUserCommand.php`
- `src/Form/FormationType.php`
- `src/Form/PlaylistType.php`

### Modified Files:
- `.env.local`
- `config/packages/security.yaml`
- `src/Controller/Backoffice/LoginController.php`
- `src/Controller/Backoffice/FormationsBackController.php`
- `src/Controller/Backoffice/CategoriesBackController.php`
- `src/Controller/Backoffice/PlaylistsBackController.php`
- `templates/Backoffice/listeformations.html.twig`
- `templates/Backoffice/addeditformations.html.twig`

## 4. Commands to Run

```bash
# Step 1: Run migration
php bin/console doctrine:migrations:migrate

# Step 2: Create admin user
php bin/console app:create-admin
```

## 5. Access Admin Panel

Login URL: `http://localhost/mediatekformation-main/public/index.php/admin/login`

## Changes Summary

- **Database**: Added user table for authentication
- **Security**: Configured database authentication with form login
- **Forms**: Created FormationType and PlaylistType classes
- **Templates**: Fixed property names (date → publishedAt)
- **Controllers**: Fixed method calls and template references

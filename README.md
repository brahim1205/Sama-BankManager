# BankManager API

Une API REST moderne pour la gestion bancaire développée avec Laravel 10, permettant la gestion des comptes bancaires, des transactions et l'authentification sécurisée.

## 🚀 Fonctionnalités

- **Authentification sécurisée** : Utilise Laravel Passport pour les tokens JWT
- **Gestion des comptes** : Comptes épargne et chèque avec archivage
- **Transactions** : Dépôts et retraits avec historique complet
- **Rôles utilisateurs** : Clients et administrateurs avec permissions différenciées
- **Calcul automatique des soldes** : Basé sur les transactions validées
- **Filtrage avancé** : Recherche, tri et pagination des comptes
- **Cache intégré** : Optimisation des performances avec Redis
- **Soft deletes** : Sécurité des données avec suppression réversible

## 🛠️ Technologies Utilisées

- **Laravel 10** - Framework PHP moderne
- **PHP 8.1+** - Version minimale requise
- **Laravel Passport** - Authentification API
- **MySQL/PostgreSQL** - Base de données
- **Redis** - Cache et sessions
- **Docker** - Conteneurisation (optionnel)

## 📋 Prérequis

- PHP >= 8.1
- Composer
- Node.js & NPM (pour les assets frontend)
- MySQL ou PostgreSQL
- Redis (recommandé)

## 🚀 Installation

1. **Cloner le repository**
   ```bash
   git clone <repository-url>
   cd bankmanager
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Configuration de l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configuration de la base de données**
   - Créer une base de données
   - Modifier les variables dans `.env` :
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=bankmanager
     DB_USERNAME=votre_username
     DB_PASSWORD=votre_password
     ```

5. **Exécuter les migrations et seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Générer les clés Passport**
   ```bash
   php artisan passport:install
   ```

7. **Installer les dépendances frontend (optionnel)**
   ```bash
   npm install
   npm run build
   ```

8. **Démarrer le serveur**
   ```bash
   php artisan serve
   ```

## 📖 Utilisation

### Authentification

#### Connexion
```http
POST /api/v1/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

**Réponse :**
```json
{
  "access_token": "token_here",
  "refresh_token": "refresh_token_here"
}
```

#### Rafraîchir le token
```http
POST /api/v1/refresh
Authorization: Bearer <refresh_token>
Content-Type: application/json

{
  "refresh_token": "refresh_token_here"
}
```

### Gestion des Comptes

Toutes les routes suivantes nécessitent l'authentification (`Authorization: Bearer <access_token>`).

#### Lister les comptes
```http
GET /api/v1/comptes?page=1&limit=10&type=epargne&search=john
```

**Paramètres de filtrage :**
- `type` : epargne, cheque
- `statut` : actif, bloque, ferme
- `search` : recherche dans titulaire/numéro de compte
- `sort` : dateCreation, solde, titulaire
- `order` : asc, desc

#### Détail d'un compte
```http
GET /api/v1/comptes/{id}
```

#### Archiver un compte
```http
PATCH /api/v1/comptes/{id}/archive
```

#### Désarchiver un compte
```http
PATCH /api/v1/comptes/{id}/unarchive
```

#### Supprimer un compte (soft delete)
```http
DELETE /api/v1/comptes/{id}
```

#### Lister les comptes archivés
```http
GET /api/v1/comptes-archives
```

## 🏗️ Architecture

### Modèles

- **User** : Utilisateur de base avec rôles (Client/Admin)
- **Compte** : Compte bancaire avec calcul automatique du solde
- **Transaction** : Opérations de dépôt/retrait
- **Client/Admin** : Extensions du modèle User

### Contrôleurs

- **Authontroller** : Gestion de l'authentification
- **CompteController** : CRUD des comptes avec autorisations

### Middlewares

- `auth:api` : Protection des routes API

## 🔒 Autorisations

- **Clients** : Peuvent voir/modifier uniquement leurs propres comptes
- **Administrateurs** : Accès complet à tous les comptes

## 🧪 Tests

```bash
# Exécuter tous les tests
php artisan test

# Tests avec couverture
php artisan test --coverage
```

## 📦 Déploiement

### Avec Docker

```bash
# Construire et démarrer les conteneurs
docker-compose up -d

# Exécuter les migrations dans le conteneur
docker-compose exec app php artisan migrate
```

### Production

1. Optimiser l'autoloader
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

2. Optimiser la configuration
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. Configurer le serveur web (Nginx/Apache)

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📝 Licence

Ce projet est sous licence MIT - voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 📞 Support

Pour toute question ou problème, veuillez créer une issue sur GitHub.

---

# Movies API - WR506D

[![wakatime](https://wakatime.com/badge/user/655aa6eb-7c71-402f-b161-4ab28498501a/project/4616f0df-5b15-496c-946f-6b93334769a4.svg)](https://wakatime.com/badge/user/655aa6eb-7c71-402f-b161-4ab28498501a/project/4616f0df-5b15-496c-946f-6b93334769a4)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-7.3-000000?logo=symfony&logoColor=white)
![API Platform](https://img.shields.io/badge/API%20Platform-4.1-38A9B4?logo=api-platform&logoColor=white)
![GraphQL](https://img.shields.io/badge/GraphQL-E10098?logo=graphql&logoColor=white)

> API REST & GraphQL de gestion de films, acteurs, réalisateurs et avis utilisateurs.

**Auteur :** Elouan Bruzek

---

## Table des matières

- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Documentation API](#documentation-api)
- [Authentification](#authentification)
- [Tests](#tests)
- [Structure du projet](#structure-du-projet)

---

## Fonctionnalités

- **Gestion complète des films** - CRUD avec filtres avancés (durée, date de sortie, budget)
- **Gestion des acteurs et réalisateurs** - Biographies, filmographies, photos
- **Système de catégories** - Classification des films par genre
- **Avis utilisateurs** - Notes et commentaires sur les films
- **Upload de médias** - Photos de profil, affiches de films
- **Double API** - REST API complète + GraphQL
- **Authentification JWT** - Sécurisation des endpoints
- **Authentification 2FA** - Double authentification TOTP
- **API Keys** - Authentification par clé API avec rate limiting
- **Filtres et pagination** - Recherche avancée sur toutes les entités

---

## Stack technique

| Composant | Version | Description |
|-----------|---------|-------------|
| **PHP** | 8.2+ | Runtime |
| **Symfony** | 7.3 | Framework PHP |
| **API Platform** | 4.1 | Framework API REST & GraphQL |
| **Doctrine ORM** | 3.x | Mapping objet-relationnel |
| **LexikJWTAuthenticationBundle** | 3.x | Authentification JWT |
| **VichUploaderBundle** | 2.x | Gestion des uploads |
| **NelmioCorsBundle** | 2.x | Configuration CORS |

---

## Prérequis

- PHP 8.2 ou supérieur
- Composer
- MySQL / MariaDB / PostgreSQL
- OpenSSL (pour la génération des clés JWT)

---

## Installation

### 1. Cloner le repository

```bash
git clone https://github.com/AloneDay-91/wr506d.git
cd WR506D
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configuration de l'environnement

```bash
cp .env .env.local
```

Modifier le fichier `.env.local` avec vos paramètres de base de données :

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/wr506d?serverVersion=8.0"
```

### 4. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Générer les clés JWT

```bash
php bin/console lexik:jwt:generate-keypair
```

### 6. Charger les fixtures (données de test)

```bash
php bin/console doctrine:fixtures:load
```

### 7. Lancer le serveur de développement

```bash
symfony server:start
```

---

## Configuration

### Variables d'environnement principales

| Variable | Description |
|----------|-------------|
| `DATABASE_URL` | URL de connexion à la base de données |
| `JWT_SECRET_KEY` | Chemin vers la clé privée JWT |
| `JWT_PUBLIC_KEY` | Chemin vers la clé publique JWT |
| `JWT_PASSPHRASE` | Phrase secrète pour les clés JWT |
| `CORS_ALLOW_ORIGIN` | Origines autorisées pour CORS |

---

## Utilisation

### Endpoints principaux

| Endpoint                | Description |
|-------------------------|-------------|
| `/api/docs`             | Documentation interactive (Swagger UI) |
| `/api/graphql`          | Endpoint GraphQL |
| `/api/graphql/graphiql` | Interface GraphiQL |

### Entités disponibles

| Entité | Endpoint REST | Description |
|--------|---------------|-------------|
| Movies | `/api/movies` | Films |
| Actors | `/api/actors` | Acteurs |
| Directors | `/api/directors` | Réalisateurs |
| Categories | `/api/categories` | Genres/Catégories |
| Reviews | `/api/reviews` | Avis utilisateurs |
| Users | `/api/users` | Utilisateurs |
| MediaObjects | `/api/media_objects` | Fichiers médias |

---

## Documentation API

La documentation complète des requêtes REST et GraphQL est disponible dans :

📄 **[docs/GRAPHQL_QUERIES.md](docs/GRAPHQL_QUERIES.md)**

Cette documentation inclut :
- Toutes les opérations CRUD pour chaque entité
- Exemples de requêtes REST avec Postman
- Exemples de mutations et queries GraphQL
- Liste des filtres disponibles par entité
- Niveaux d'authentification requis

---

## Authentification

### Obtenir un token JWT

```bash
curl -X POST http://localhost:8000/api/auth \
  -H "Content-Type: application/json" \
  -d '{"username": "user@example.com", "password": "password"}'
```

### Utiliser le token

```bash
curl http://localhost:8000/api/movies \
  -H "Authorization: Bearer {votre_token}"
```

### Rôles disponibles

| Rôle | Description |
|------|-------------|
| `ROLE_USER` | Utilisateur standard (création d'avis) |
| `ROLE_ADMIN` | Administrateur (accès complet) |

---

## Tests

### Lancer les tests unitaires

```bash
php bin/phpunit
```

### Analyse statique du code

```bash
# PHPStan
vendor/bin/phpstan analyse

# PHP_CodeSniffer
vendor/bin/phpcs

# PHP Mess Detector
vendor/bin/phpmd src text cleancode,codesize,controversial,design,naming,unusedcode
```

---

## Structure du projet

```
WR506D/
├── config/             # Configuration Symfony
├── docs/               # Documentation
│   └── GRAPHQL_QUERIES.md
├── migrations/         # Migrations Doctrine
├── public/             # Point d'entrée web
├── src/
│   ├── Controller/     # Contrôleurs
│   ├── DataFixtures/   # Données de test
│   ├── Entity/         # Entités Doctrine
│   │   ├── Actor.php
│   │   ├── Category.php
│   │   ├── Director.php
│   │   ├── MediaObject.php
│   │   ├── Movie.php
│   │   ├── Review.php
│   │   └── User.php
│   ├── Repository/     # Repositories Doctrine
│   └── ...
├── templates/          # Templates Twig
├── tests/              # Tests
├── composer.json
└── README.md
```

---

<p align="center">
  <strong>Elouan Bruzek</strong> - WR506D - BUT MMI
</p>

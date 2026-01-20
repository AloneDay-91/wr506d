# Documentation des requêtes GraphQL et REST API

Ce document contient toutes les requêtes GraphQL et REST CRUD pour les entités de l'application Movies WR506D par Elouan Bruzek.

## Table des matières

- [Actors](#actors)
- [Movies](#movies)
- [Directors](#directors)
- [Categories](#categories)
- [Reviews](#reviews)
- [Users](#users)
- [MediaObjects](#mediaobjects)

---

## Actors

### REST API

| Méthode | Endpoint | Description | Authentification |
|---------|----------|-------------|------------------|
| GET | `/api/actors` | Liste tous les acteurs | Public |
| GET | `/api/actors/{id}` | Récupère un acteur | Public |
| POST | `/api/actors` | Crée un acteur | ROLE_ADMIN |
| PUT | `/api/actors/{id}` | Remplace un acteur | ROLE_ADMIN ou propriétaire |
| PATCH | `/api/actors/{id}` | Modifie un acteur | ROLE_ADMIN ou propriétaire |
| DELETE | `/api/actors/{id}` | Supprime un acteur | ROLE_ADMIN |

**Filtres disponibles:**
- `lastname` (start) - Recherche par début du nom
- `firstname` (start) - Recherche par début du prénom
- `bio` (partial) - Recherche dans la biographie
- `dob[before]`, `dob[after]` - Filtrer par date de naissance
- `dod[before]`, `dod[after]` - Filtrer par date de décès

**Exemples Postman:**

```
GET {{base_url}}/api/actors
GET {{base_url}}/api/actors/1
GET {{base_url}}/api/actors?lastname=Di
```

```
POST {{base_url}}/api/actors
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json
Body (raw JSON):
{
  "lastname": "DiCaprio",
  "firstname": "Leonardo",
  "dob": "1974-11-11",
  "bio": "American actor and film producer.",
  "photo": "/api/media_objects/1",
  "movies": ["/api/movies/1", "/api/movies/2"]
}
```

```
PUT {{base_url}}/api/actors/1
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json
Body (raw JSON):
{
  "lastname": "DiCaprio",
  "firstname": "Leonardo Wilhelm",
  "dob": "1974-11-11",
  "bio": "American actor, film producer, and environmentalist."
}
```

```
PATCH {{base_url}}/api/actors/1
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/merge-patch+json
Body (raw JSON):
{
  "bio": "Updated biography"
}
```

```
DELETE {{base_url}}/api/actors/1
Headers:
  Authorization: Bearer {{token}}
```

### GraphQL

#### GetAll - Liste de tous les acteurs

```graphql
query GetAllActors {
  actors {
    edges {
      node {
        id
        lastname
        firstname
        dob
        dod
        bio
        photo {
          id
          contentUrl
          type
        }
        movies {
          edges {
            node {
              id
              name
            }
          }
        }
        createdAt
      }
    }
  }
}
```

#### GetById - Récupérer un acteur par ID

```graphql
query GetActorById($id: ID!) {
  actor(id: $id) {
    id
    _id
    lastname
    firstname
    dob
    dod
    bio
    photo {
      id
      _id
      contentUrl
      type
    }
    movies {
      edges {
        node {
          id
          name
          description
          releaseDate
        }
      }
    }
    createdAt
  }
}
```

**Variables :**
```json
{
  "id": "/api/actors/1"
}
```

#### Create - Créer un acteur

```graphql
mutation CreateActor($lastname: String!, $firstname: String, $dob: String, $dod: String, $bio: String, $photo: String, $movies: [String]) {
  createActor(input: {
    lastname: $lastname
    firstname: $firstname
    dob: $dob
    dod: $dod
    bio: $bio
    photo: $photo
    movies: $movies
  }) {
    actor {
      id
      _id
      lastname
      firstname
      dob
      dod
      bio
      photo {
        id
        contentUrl
      }
      createdAt
    }
  }
}
```

**Variables :**
```json
{
  "lastname": "DiCaprio",
  "firstname": "Leonardo",
  "dob": "1974-11-11",
  "dod": null,
  "bio": "American actor and film producer.",
  "photo": "/api/media_objects/1",
  "movies": ["/api/movies/1", "/api/movies/2"]
}
```

#### Update - Mettre à jour un acteur

```graphql
mutation UpdateActor($id: ID!, $lastname: String, $firstname: String, $dob: String, $dod: String, $bio: String, $photo: String, $movies: [String]) {
  updateActor(input: {
    id: $id
    lastname: $lastname
    firstname: $firstname
    dob: $dob
    dod: $dod
    bio: $bio
    photo: $photo
    movies: $movies
  }) {
    actor {
      id
      _id
      lastname
      firstname
      dob
      dod
      bio
      photo {
        id
        contentUrl
      }
      createdAt
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/actors/1",
  "lastname": "DiCaprio",
  "firstname": "Leonardo Wilhelm",
  "bio": "American actor, film producer, and environmentalist."
}
```

#### Delete - Supprimer un acteur

```graphql
mutation DeleteActor($id: ID!) {
  deleteActor(input: { id: $id }) {
    actor {
      id
      _id
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/actors/1"
}
```

---

## Movies

### REST API

| Méthode | Endpoint | Description | Authentification |
|---------|----------|-------------|------------------|
| GET | `/api/movies` | Liste tous les films | Public |
| GET | `/api/movies/{id}` | Récupère un film | Public |
| POST | `/api/movies` | Crée un film | ROLE_ADMIN |
| PUT | `/api/movies/{id}` | Remplace un film | ROLE_ADMIN ou propriétaire |
| PATCH | `/api/movies/{id}` | Modifie un film | ROLE_ADMIN ou propriétaire |
| DELETE | `/api/movies/{id}` | Supprime un film | ROLE_ADMIN |

**Filtres disponibles:**
- `name` (partial) - Recherche par nom
- `description` (partial) - Recherche dans la description
- `releaseDate[before]`, `releaseDate[after]` - Filtrer par date de sortie
- `duration[gte]`, `duration[lte]` - Filtrer par durée

**Exemples Postman:**

```
GET {{base_url}}/api/movies
GET {{base_url}}/api/movies/1
GET {{base_url}}/api/movies?name=Inception
GET {{base_url}}/api/movies?duration[gte]=120&duration[lte]=180
GET {{base_url}}/api/movies?releaseDate[after]=2010-01-01
```

```
POST {{base_url}}/api/movies
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json
Body (raw JSON):
{
  "name": "Inception",
  "description": "A thief who steals corporate secrets through dream-sharing technology.",
  "duration": 148,
  "releaseDate": "2010-07-16",
  "nbEntries": 836836967,
  "url": "https://www.imdb.com/title/tt1375666/",
  "budget": 160000000,
  "director": "/api/directors/1",
  "image": "/api/media_objects/1",
  "actors": ["/api/actors/1", "/api/actors/2"],
  "categories": ["/api/categories/1"]
}
```

```
PUT {{base_url}}/api/movies/1
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json
Body (raw JSON):
{
  "name": "Inception (Updated)",
  "description": "Updated description",
  "duration": 148,
  "director": "/api/directors/1"
}
```

```
PATCH {{base_url}}/api/movies/1
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/merge-patch+json
Body (raw JSON):
{
  "description": "New description"
}
```

```
DELETE {{base_url}}/api/movies/1
Headers:
  Authorization: Bearer {{token}}
```

### GraphQL

#### GetAll - Liste de tous les films

```graphql
query GetAllMovies {
  movies {
    edges {
      node {
        id
        name
        description
        duration
        releaseDate
        nbEntries
        url
        budget
        image {
          id
          contentUrl
          type
        }
        director {
          id
          firstname
          lastname
        }
        actors {
          edges {
            node {
              id
              firstname
              lastname
            }
          }
        }
        categories {
          edges {
            node {
              id
              name
            }
          }
        }
        reviews {
          edges {
            node {
              id
              title
              rating
            }
          }
        }
        createdAt
      }
    }
  }
}
```

#### GetById - Récupérer un film par ID

```graphql
query GetMovieById($id: ID!) {
  movie(id: $id) {
    id
    _id
    name
    description
    duration
    releaseDate
    nbEntries
    url
    budget
    image {
      id
      _id
      contentUrl
      type
    }
    director {
      id
      _id
      firstname
      lastname
      dob
    }
    actors {
      edges {
        node {
          id
          firstname
          lastname
          photo {
            id
            contentUrl
          }
        }
      }
    }
    categories {
      edges {
        node {
          id
          name
        }
      }
    }
    reviews {
      edges {
        node {
          id
          title
          comment
          rating
          user {
            id
            firstname
            lastname
          }
          createdAt
        }
      }
    }
    createdAt
  }
}
```

**Variables :**
```json
{
  "id": "/api/movies/1"
}
```

#### Create - Créer un film

```graphql
mutation CreateMovie($name: String!, $description: String, $duration: Int, $releaseDate: String, $nbEntries: Int, $url: String, $budget: Float, $director: String!, $image: String, $actors: [String], $categories: [String]) {
  createMovie(input: {
    name: $name
    description: $description
    duration: $duration
    releaseDate: $releaseDate
    nbEntries: $nbEntries
    url: $url
    budget: $budget
    director: $director
    image: $image
    actors: $actors
    categories: $categories
  }) {
    movie {
      id
      _id
      name
      description
      duration
      releaseDate
      nbEntries
      url
      budget
      image {
        id
        contentUrl
      }
      director {
        id
        firstname
        lastname
      }
      createdAt
    }
  }
}
```

**Variables :**
```json
{
  "name": "Inception",
  "description": "A thief who steals corporate secrets through dream-sharing technology.",
  "duration": 148,
  "releaseDate": "2010-07-16",
  "nbEntries": 836836967,
  "url": "https://www.imdb.com/title/tt1375666/",
  "budget": 160000000,
  "director": "/api/directors/1",
  "image": "/api/media_objects/1",
  "actors": ["/api/actors/1", "/api/actors/2"],
  "categories": ["/api/categories/1"]
}
```

#### Update - Mettre à jour un film

```graphql
mutation UpdateMovie($id: ID!, $name: String, $description: String, $duration: Int, $releaseDate: String, $nbEntries: Int, $url: String, $budget: Float, $director: String, $image: String, $actors: [String], $categories: [String]) {
  updateMovie(input: {
    id: $id
    name: $name
    description: $description
    duration: $duration
    releaseDate: $releaseDate
    nbEntries: $nbEntries
    url: $url
    budget: $budget
    director: $director
    image: $image
    actors: $actors
    categories: $categories
  }) {
    movie {
      id
      _id
      name
      description
      duration
      releaseDate
      nbEntries
      url
      budget
      image {
        id
        contentUrl
      }
      director {
        id
        firstname
        lastname
      }
      createdAt
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/movies/1",
  "name": "Inception (Updated)",
  "description": "Updated description"
}
```

#### Delete - Supprimer un film

```graphql
mutation DeleteMovie($id: ID!) {
  deleteMovie(input: { id: $id }) {
    movie {
      id
      _id
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/movies/1"
}
```

---

## Directors

### REST API

| Méthode | Endpoint | Description | Authentification |
|---------|----------|-------------|------------------|
| GET | `/api/directors` | Liste tous les réalisateurs | Public |
| GET | `/api/directors/{id}` | Récupère un réalisateur | Public |
| POST | `/api/directors` | Crée un réalisateur | ROLE_ADMIN |
| PUT | `/api/directors/{id}` | Remplace un réalisateur | ROLE_ADMIN ou propriétaire |
| DELETE | `/api/directors/{id}` | Supprime un réalisateur | ROLE_ADMIN |

**Exemples Postman:**

```
GET {{base_url}}/api/directors
GET {{base_url}}/api/directors/1
```

```
POST {{base_url}}/api/directors
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json
Body (raw JSON):
{
  "lastname": "Nolan",
  "firstname": "Christopher",
  "dob": "1970-07-30",
  "photo": "/api/media_objects/1"
}
```

```
PUT {{base_url}}/api/directors/1
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json
Body (raw JSON):
{
  "lastname": "Nolan",
  "firstname": "Christopher Edward",
  "dob": "1970-07-30"
}
```

```
DELETE {{base_url}}/api/directors/1
Headers:
  Authorization: Bearer {{token}}
```

### GraphQL

#### GetAll - Liste de tous les réalisateurs

```graphql
query GetAllDirectors {
  directors {
    edges {
      node {
        id
        lastname
        firstname
        dob
        dod
        photo {
          id
          contentUrl
          type
        }
        movies {
          edges {
            node {
              id
              name
            }
          }
        }
      }
    }
  }
}
```

#### GetById - Récupérer un réalisateur par ID

```graphql
query GetDirectorById($id: ID!) {
  director(id: $id) {
    id
    _id
    lastname
    firstname
    dob
    dod
    photo {
      id
      _id
      contentUrl
      type
    }
    movies {
      edges {
        node {
          id
          name
          description
          releaseDate
          duration
        }
      }
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/directors/1"
}
```

#### Create - Créer un réalisateur

```graphql
mutation CreateDirector($lastname: String!, $firstname: String!, $dob: String!, $dod: String, $photo: String) {
  createDirector(input: {
    lastname: $lastname
    firstname: $firstname
    dob: $dob
    dod: $dod
    photo: $photo
  }) {
    director {
      id
      _id
      lastname
      firstname
      dob
      dod
      photo {
        id
        contentUrl
      }
    }
  }
}
```

**Variables :**
```json
{
  "lastname": "Nolan",
  "firstname": "Christopher",
  "dob": "1970-07-30",
  "dod": null,
  "photo": "/api/media_objects/1"
}
```

#### Update - Mettre à jour un réalisateur

```graphql
mutation UpdateDirector($id: ID!, $lastname: String, $firstname: String, $dob: String, $dod: String, $photo: String) {
  updateDirector(input: {
    id: $id
    lastname: $lastname
    firstname: $firstname
    dob: $dob
    dod: $dod
    photo: $photo
  }) {
    director {
      id
      _id
      lastname
      firstname
      dob
      dod
      photo {
        id
        contentUrl
      }
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/directors/1",
  "lastname": "Nolan",
  "firstname": "Christopher Edward"
}
```

#### Delete - Supprimer un réalisateur

```graphql
mutation DeleteDirector($id: ID!) {
  deleteDirector(input: { id: $id }) {
    director {
      id
      _id
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/directors/1"
}
```

---

## Categories

### REST API

| Méthode | Endpoint | Description | Authentification |
|---------|----------|-------------|------------------|
| GET | `/api/categories` | Liste toutes les catégories | Public |
| GET | `/api/categories/{id}` | Récupère une catégorie | Public |
| POST | `/api/categories` | Crée une catégorie | ROLE_ADMIN |
| PUT | `/api/categories/{id}` | Remplace une catégorie | ROLE_ADMIN ou propriétaire |
| DELETE | `/api/categories/{id}` | Supprime une catégorie | ROLE_ADMIN |

**Filtres disponibles:**
- `name` (partial) - Recherche par nom

**Exemples Postman:**

```
GET {{base_url}}/api/categories
GET {{base_url}}/api/categories/1
GET {{base_url}}/api/categories?name=Action
```

```
POST {{base_url}}/api/categories
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json
Body (raw JSON):
{
  "name": "Science Fiction",
  "movies": ["/api/movies/1", "/api/movies/2"]
}
```

```
PUT {{base_url}}/api/categories/1
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json
Body (raw JSON):
{
  "name": "Sci-Fi"
}
```

```
DELETE {{base_url}}/api/categories/1
Headers:
  Authorization: Bearer {{token}}
```

### GraphQL

#### GetAll - Liste de toutes les catégories

```graphql
query GetAllCategories {
  categories {
    edges {
      node {
        id
        name
        movies {
          edges {
            node {
              id
              name
            }
          }
        }
        createdAt
      }
    }
  }
}
```

#### GetById - Récupérer une catégorie par ID

```graphql
query GetCategoryById($id: ID!) {
  category(id: $id) {
    id
    _id
    name
    movies {
      edges {
        node {
          id
          name
          description
          releaseDate
        }
      }
    }
    createdAt
  }
}
```

**Variables :**
```json
{
  "id": "/api/categories/1"
}
```

#### Create - Créer une catégorie

```graphql
mutation CreateCategory($name: String!, $movies: [String]) {
  createCategory(input: {
    name: $name
    movies: $movies
  }) {
    category {
      id
      _id
      name
      createdAt
    }
  }
}
```

**Variables :**
```json
{
  "name": "Science Fiction",
  "movies": ["/api/movies/1", "/api/movies/2"]
}
```

#### Update - Mettre à jour une catégorie

```graphql
mutation UpdateCategory($id: ID!, $name: String, $movies: [String]) {
  updateCategory(input: {
    id: $id
    name: $name
    movies: $movies
  }) {
    category {
      id
      _id
      name
      createdAt
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/categories/1",
  "name": "Sci-Fi"
}
```

#### Delete - Supprimer une catégorie

```graphql
mutation DeleteCategory($id: ID!) {
  deleteCategory(input: { id: $id }) {
    category {
      id
      _id
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/categories/1"
}
```

---

## Reviews

### REST API

| Méthode | Endpoint | Description | Authentification |
|---------|----------|-------------|------------------|
| GET | `/api/reviews` | Liste tous les avis | Public |
| GET | `/api/reviews/{id}` | Récupère un avis | Public |
| POST | `/api/reviews` | Crée un avis | ROLE_USER |
| PUT | `/api/reviews/{id}` | Remplace un avis | ROLE_ADMIN ou propriétaire |
| PATCH | `/api/reviews/{id}` | Modifie un avis | ROLE_ADMIN ou propriétaire |
| DELETE | `/api/reviews/{id}` | Supprime un avis | ROLE_ADMIN ou propriétaire |

**Filtres disponibles:**
- `title` (partial) - Recherche par titre
- `comment` (partial) - Recherche dans le commentaire
- `rating[gte]`, `rating[lte]` - Filtrer par note (1-5)

**Exemples Postman:**

```
GET {{base_url}}/api/reviews
GET {{base_url}}/api/reviews/1
GET {{base_url}}/api/reviews?rating[gte]=4
GET {{base_url}}/api/reviews?title=Amazing
```

```
POST {{base_url}}/api/reviews
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json
Body (raw JSON):
{
  "title": "Amazing movie!",
  "comment": "This movie blew my mind.",
  "rating": 5,
  "user": "/api/users/1",
  "movie": "/api/movies/1"
}
```

```
PATCH {{base_url}}/api/reviews/1
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/merge-patch+json
Body (raw JSON):
{
  "rating": 4,
  "comment": "Updated comment"
}
```

```
DELETE {{base_url}}/api/reviews/1
Headers:
  Authorization: Bearer {{token}}
```

### GraphQL

#### GetAll - Liste de tous les avis

```graphql
query GetAllReviews {
  reviews {
    edges {
      node {
        id
        title
        comment
        rating
        user {
          id
          firstname
          lastname
        }
        movie {
          id
          name
        }
        createdAt
        updatedAt
      }
    }
  }
}
```

#### GetById - Récupérer un avis par ID

```graphql
query GetReviewById($id: ID!) {
  review(id: $id) {
    id
    _id
    title
    comment
    rating
    user {
      id
      _id
      email
      firstname
      lastname
    }
    movie {
      id
      _id
      name
      description
    }
    createdAt
    updatedAt
  }
}
```

**Variables :**
```json
{
  "id": "/api/reviews/1"
}
```

#### Create - Créer un avis

```graphql
mutation CreateReview($title: String!, $comment: String!, $rating: Int!, $user: String!, $movie: String!) {
  createReview(input: {
    title: $title
    comment: $comment
    rating: $rating
    user: $user
    movie: $movie
  }) {
    review {
      id
      _id
      title
      comment
      rating
      user {
        id
        firstname
        lastname
      }
      movie {
        id
        name
      }
      createdAt
    }
  }
}
```

**Variables :**
```json
{
  "title": "Amazing movie!",
  "comment": "This movie blew my mind. The story, the visuals, everything was perfect.",
  "rating": 5,
  "user": "/api/users/1",
  "movie": "/api/movies/1"
}
```

#### Update - Mettre à jour un avis

```graphql
mutation UpdateReview($id: ID!, $title: String, $comment: String, $rating: Int) {
  updateReview(input: {
    id: $id
    title: $title
    comment: $comment
    rating: $rating
  }) {
    review {
      id
      _id
      title
      comment
      rating
      updatedAt
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/reviews/1",
  "title": "Updated: Amazing movie!",
  "comment": "After watching it again, still amazing!",
  "rating": 5
}
```

#### Delete - Supprimer un avis

```graphql
mutation DeleteReview($id: ID!) {
  deleteReview(input: { id: $id }) {
    review {
      id
      _id
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/reviews/1"
}
```

---

## Users

> **Note :** Les requêtes User nécessitent généralement un rôle `ROLE_ADMIN` sauf pour la création et la modification de son propre compte.

### REST API

| Méthode | Endpoint | Description | Authentification |
|---------|----------|-------------|------------------|
| GET | `/api/users` | Liste tous les utilisateurs | ROLE_ADMIN |
| GET | `/api/users/{id}` | Récupère un utilisateur | ROLE_ADMIN |
| POST | `/api/users` | Crée un utilisateur | Public |
| PUT | `/api/users/{id}` | Remplace un utilisateur | ROLE_USER ou ROLE_ADMIN |
| PATCH | `/api/users/{id}` | Modifie un utilisateur | ROLE_USER ou ROLE_ADMIN |
| DELETE | `/api/users/{id}` | Supprime un utilisateur | ROLE_ADMIN |

**Exemples Postman:**

```
GET {{base_url}}/api/users
Headers:
  Authorization: Bearer {{admin_token}}
```

```
GET {{base_url}}/api/users/1
Headers:
  Authorization: Bearer {{admin_token}}
```

```
POST {{base_url}}/api/users
Headers:
  Content-Type: application/json
Body (raw JSON):
{
  "email": "john.doe@example.com",
  "plainPassword": "SecurePassword123!",
  "firstname": "John",
  "lastname": "Doe"
}
```

```
PATCH {{base_url}}/api/users/1
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/merge-patch+json
Body (raw JSON):
{
  "firstname": "Johnny",
  "lastname": "Doe Updated"
}
```

```
DELETE {{base_url}}/api/users/1
Headers:
  Authorization: Bearer {{admin_token}}
```

### GraphQL

#### GetAll - Liste de tous les utilisateurs (Admin)

```graphql
query GetAllUsers {
  users {
    edges {
      node {
        id
        email
        roles
        firstname
        lastname
        photo {
          id
          contentUrl
        }
        twoFactorEnabled
        apiKeyEnabled
        apiKeyPrefix
        reviews {
          edges {
            node {
              id
              title
              rating
            }
          }
        }
      }
    }
  }
}
```

#### GetById - Récupérer un utilisateur par ID (Admin)

```graphql
query GetUserById($id: ID!) {
  user(id: $id) {
    id
    _id
    email
    roles
    firstname
    lastname
    photo {
      id
      _id
      contentUrl
      type
    }
    twoFactorEnabled
    apiKeyEnabled
    apiKeyPrefix
    apiKeyCreatedAt
    apiKeyLastUsedAt
    rateLimit
    reviews {
      edges {
        node {
          id
          title
          comment
          rating
          movie {
            id
            name
          }
          createdAt
        }
      }
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/users/1"
}
```

#### Create - Créer un utilisateur (Public)

```graphql
mutation CreateUser($email: String!, $plainPassword: String!, $firstname: String!, $lastname: String!, $roles: Iterable, $photo: String) {
  createUser(input: {
    email: $email
    plainPassword: $plainPassword
    firstname: $firstname
    lastname: $lastname
    roles: $roles
    photo: $photo
  }) {
    user {
      id
      _id
      email
      roles
      firstname
      lastname
      photo {
        id
        contentUrl
      }
    }
  }
}
```

**Variables :**
```json
{
  "email": "john.doe@example.com",
  "plainPassword": "SecurePassword123!",
  "firstname": "John",
  "lastname": "Doe",
  "roles": ["ROLE_USER"],
  "photo": "/api/media_objects/1"
}
```

#### Update - Mettre à jour un utilisateur

```graphql
mutation UpdateUser($id: ID!, $email: String, $plainPassword: String, $firstname: String, $lastname: String, $roles: Iterable, $photo: String) {
  updateUser(input: {
    id: $id
    email: $email
    plainPassword: $plainPassword
    firstname: $firstname
    lastname: $lastname
    roles: $roles
    photo: $photo
  }) {
    user {
      id
      _id
      email
      roles
      firstname
      lastname
      photo {
        id
        contentUrl
      }
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/users/1",
  "firstname": "Johnny",
  "lastname": "Doe Updated"
}
```

#### Delete - Supprimer un utilisateur (Admin)

```graphql
mutation DeleteUser($id: ID!) {
  deleteUser(input: { id: $id }) {
    user {
      id
      _id
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/users/1"
}
```

---

## MediaObjects

> **Note :** Les MediaObjects sont uploadés via REST API (multipart/form-data). GraphQL est utilisé principalement pour la lecture et la suppression.

### REST API

| Méthode | Endpoint | Description | Authentification |
|---------|----------|-------------|------------------|
| GET | `/api/media_objects` | Liste tous les médias | Public |
| GET | `/api/media_objects/{id}` | Récupère un média | Public |
| POST | `/api/media_objects` | Upload un média | Public |
| DELETE | `/api/media_objects/{id}` | Supprime un média | Public |

**Filtres disponibles:**
- `type` (exact) - Filtrer par type de média

**Types de médias:**
- `profile` - Photos de profil
- `movie_cover` - Couvertures de films
- `actor` - Photos d'acteurs
- `director` - Photos de réalisateurs
- `other` - Autres types

**Exemples Postman:**

```
GET {{base_url}}/api/media_objects
GET {{base_url}}/api/media_objects/1
GET {{base_url}}/api/media_objects?type=profile
```

```
POST {{base_url}}/api/media_objects
Headers:
  Authorization: Bearer {{token}}
Body (form-data):
  file: [select file]
  type: profile
```

```
DELETE {{base_url}}/api/media_objects/1
Headers:
  Authorization: Bearer {{token}}
```

### GraphQL

#### GetAll - Liste de tous les médias

```graphql
query GetAllMediaObjects {
  mediaObjects {
    edges {
      node {
        id
        contentUrl
        filePath
        type
        createdAt
      }
    }
  }
}
```

#### GetById - Récupérer un média par ID

```graphql
query GetMediaObjectById($id: ID!) {
  mediaObject(id: $id) {
    id
    _id
    contentUrl
    filePath
    type
    createdAt
  }
}
```

**Variables :**
```json
{
  "id": "/api/media_objects/1"
}
```

#### Delete - Supprimer un média

```graphql
mutation DeleteMediaObject($id: ID!) {
  deleteMediaObject(input: { id: $id }) {
    mediaObject {
      id
      _id
    }
  }
}
```

**Variables :**
```json
{
  "id": "/api/media_objects/1"
}
```

---

## Notes importantes

### Authentification

La plupart des requêtes nécessitent un token JWT. Dans Postman, configurez une variable d'environnement `{{token}}` et ajoutez le header :

```
Authorization: Bearer {{token}}
```

### Format des IDs

Les IDs dans GraphQL avec API Platform utilisent le format IRI (Internationalized Resource Identifier) :
- Actors: `/api/actors/{id}`
- Movies: `/api/movies/{id}`
- Directors: `/api/directors/{id}`
- Categories: `/api/categories/{id}`
- Reviews: `/api/reviews/{id}`
- Users: `/api/users/{id}`
- MediaObjects: `/api/media_objects/{id}`

### Configuration Postman

Variables d'environnement recommandées :
- `{{base_url}}` : URL de base de l'API (ex: `http://localhost:8000`)

### Résumé des filtres disponibles

| Entité | Filtres |
|--------|---------|
| **Actors** | `lastname` (start), `firstname` (start), `bio` (partial), `dob`, `dod` |
| **Movies** | `name` (partial), `description` (partial), `releaseDate`, `duration` (range) |
| **Categories** | `name` (partial) |
| **Reviews** | `title` (partial), `comment` (partial), `rating` (range) |
| **MediaObjects** | `type` (exact) |

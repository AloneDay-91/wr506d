# Documentation des requêtes GraphQL

Ce document contient toutes les requêtes GraphQL CRUD pour les entités de l'application WR506D.

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

### GetAll - Liste de tous les acteurs

```graphql
query GetAllActors {
  actors {
    totalCount
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
    pageInfo {
      hasNextPage
      hasPreviousPage
      startCursor
      endCursor
    }
  }
}
```

### GetAll avec pagination

```graphql
query GetAllActorsPaginated($first: Int, $after: String) {
  actors(first: $first, after: $after) {
    totalCount
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
        }
        createdAt
      }
    }
    pageInfo {
      hasNextPage
      endCursor
    }
  }
}
```

**Variables :**
```json
{
  "first": 10,
  "after": "cursor_value"
}
```

### GetById - Récupérer un acteur par ID

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

### Create - Créer un acteur

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

### Update - Mettre à jour un acteur

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

### Delete - Supprimer un acteur

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

### GetAll - Liste de tous les films

```graphql
query GetAllMovies {
  movies {
    totalCount
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
    pageInfo {
      hasNextPage
      hasPreviousPage
      startCursor
      endCursor
    }
  }
}
```

### GetAll avec pagination et filtres

```graphql
query GetAllMoviesPaginated($first: Int, $after: String, $name: String) {
  movies(first: $first, after: $after, name: $name) {
    totalCount
    edges {
      node {
        id

        name
        description
        duration
        releaseDate
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
    pageInfo {
      hasNextPage
      endCursor
    }
  }
}
```

**Variables :**
```json
{
  "first": 10,
  "after": null,
  "name": "Inception"
}
```

### GetById - Récupérer un film par ID

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

### Create - Créer un film

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

### Update - Mettre à jour un film

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

### Delete - Supprimer un film

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

### GetAll - Liste de tous les réalisateurs

```graphql
query GetAllDirectors {
  directors {
    totalCount
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
    pageInfo {
      hasNextPage
      hasPreviousPage
      startCursor
      endCursor
    }
  }
}
```

### GetAll avec pagination

```graphql
query GetAllDirectorsPaginated($first: Int, $after: String) {
  directors(first: $first, after: $after) {
    totalCount
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
        }
      }
    }
    pageInfo {
      hasNextPage
      endCursor
    }
  }
}
```

**Variables :**
```json
{
  "first": 10,
  "after": null
}
```

### GetById - Récupérer un réalisateur par ID

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

### Create - Créer un réalisateur

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

### Update - Mettre à jour un réalisateur

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

### Delete - Supprimer un réalisateur

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

### GetAll - Liste de toutes les catégories

```graphql
query GetAllCategories {
  categories {
    totalCount
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
    pageInfo {
      hasNextPage
      hasPreviousPage
      startCursor
      endCursor
    }
  }
}
```

### GetAll avec pagination et filtre

```graphql
query GetAllCategoriesPaginated($first: Int, $after: String, $name: String) {
  categories(first: $first, after: $after, name: $name) {
    totalCount
    edges {
      node {
        id

        name
        createdAt
      }
    }
    pageInfo {
      hasNextPage
      endCursor
    }
  }
}
```

**Variables :**
```json
{
  "first": 10,
  "after": null,
  "name": "Action"
}
```

### GetById - Récupérer une catégorie par ID

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

### Create - Créer une catégorie

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

### Update - Mettre à jour une catégorie

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

### Delete - Supprimer une catégorie

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

### GetAll - Liste de tous les avis

```graphql
query GetAllReviews {
  reviews {
    totalCount
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
    pageInfo {
      hasNextPage
      hasPreviousPage
      startCursor
      endCursor
    }
  }
}
```

### GetAll avec pagination et filtres

```graphql
query GetAllReviewsPaginated($first: Int, $after: String, $title: String, $rating_gte: Int, $rating_lte: Int) {
  reviews(first: $first, after: $after, title: $title, rating: { gte: $rating_gte, lte: $rating_lte }) {
    totalCount
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
      }
    }
    pageInfo {
      hasNextPage
      endCursor
    }
  }
}
```

**Variables :**
```json
{
  "first": 10,
  "after": null,
  "title": "Great",
  "rating_gte": 4,
  "rating_lte": 5
}
```

### GetById - Récupérer un avis par ID

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

### Create - Créer un avis

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

### Update - Mettre à jour un avis

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

### Delete - Supprimer un avis

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

### GetAll - Liste de tous les utilisateurs (Admin)

```graphql
query GetAllUsers {
  users {
    totalCount
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
    pageInfo {
      hasNextPage
      hasPreviousPage
      startCursor
      endCursor
    }
  }
}
```

### GetAll avec pagination (Admin)

```graphql
query GetAllUsersPaginated($first: Int, $after: String) {
  users(first: $first, after: $after) {
    totalCount
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
      }
    }
    pageInfo {
      hasNextPage
      endCursor
    }
  }
}
```

**Variables :**
```json
{
  "first": 10,
  "after": null
}
```

### GetById - Récupérer un utilisateur par ID (Admin)

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

### Create - Créer un utilisateur (Public)

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

### Update - Mettre à jour un utilisateur

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

### Delete - Supprimer un utilisateur (Admin)

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

### GetAll - Liste de tous les médias

```graphql
query GetAllMediaObjects {
  mediaObjects {
    totalCount
    edges {
      node {
        id

        contentUrl
        filePath
        type
        createdAt
      }
    }
    pageInfo {
      hasNextPage
      hasPreviousPage
      startCursor
      endCursor
    }
  }
}
```

### GetAll avec pagination et filtre par type

```graphql
query GetAllMediaObjectsPaginated($first: Int, $after: String, $type: String) {
  mediaObjects(first: $first, after: $after, type: $type) {
    totalCount
    edges {
      node {
        id

        contentUrl
        filePath
        type
        createdAt
      }
    }
    pageInfo {
      hasNextPage
      endCursor
    }
  }
}
```

**Variables :**
```json
{
  "first": 10,
  "after": null,
  "type": "profile"
}
```

### GetById - Récupérer un média par ID

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

### Delete - Supprimer un média

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

La plupart des requêtes nécessitent un token JWT. Ajoutez le header suivant à vos requêtes :

```
Authorization: Bearer <votre_token_jwt>
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

### Pagination

Par défaut, API Platform limite les résultats à 30 éléments. Utilisez les paramètres de pagination :
- `first`: Nombre d'éléments à récupérer
- `after`: Curseur pour la page suivante
- `last`: Nombre d'éléments depuis la fin
- `before`: Curseur pour la page précédente

### Filtres disponibles

- **Actors**: `lastname`, `firstname`, `bio`, `photo`, `dob`, `dof`
- **Movies**: `name`, `description`, `image`, `releaseDate`, `duration`
- **Categories**: `name`
- **Reviews**: `title`, `comment`, `rating`
- **MediaObjects**: `type`

### Types de médias

- `profile`: Photos de profil (utilisateurs, acteurs, réalisateurs)
- `movie_cover`: Couvertures de films
- `other`: Autres types de médias


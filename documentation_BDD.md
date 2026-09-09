# Documentation de la base de données — Bookly

## 1. Présentation

Bookly utilise une base de données MySQL pour stocker les utilisateurs, les livres et les genres.

La base de données est gérée avec Doctrine dans Symfony.

## 2. Tables

### User

| Champ | Type | Rôle |
|---|---|---|
| id | INT | Identifiant unique |
| email | VARCHAR(180) | Adresse email |
| roles | JSON | Rôles de l'utilisateur |
| password | VARCHAR(255) | Mot de passe hashé |

### Book

| Champ | Type | Rôle |
|---|---|---|
| id | INT | Identifiant unique |
| title | VARCHAR(255) | Titre du livre |
| author | VARCHAR(255) | Auteur |
| status | VARCHAR(255) | Statut de lecture |
| created_at | DATETIME | Date de création |
| owner_id | INT | Utilisateur propriétaire |
| genre_id | INT | Genre du livre |

### Genre

| Champ | Type | Rôle |
|---|---|---|
| id | INT | Identifiant unique |
| name | VARCHAR | Nom du genre |

## 3. Relations

### User → Book

Un utilisateur peut posséder plusieurs livres.

Un livre appartient obligatoirement à un utilisateur.

Relation : **OneToMany / ManyToOne**

### Genre → Book

Un genre peut être associé à plusieurs livres.

Un livre appartient obligatoirement à un genre.

Relation : **OneToMany / ManyToOne**

## 4. Schéma

```text
┌──────────────┐
│     User     │
├──────────────┤
│ id           │
│ email        │
│ roles        │
│ password     │
└──────┬───────┘
       │
       │ 1
       │
       │ N
┌──────▼───────┐
│     Book     │
├──────────────┤
│ id           │
│ title        │
│ author       │
│ status       │
│ created_at   │
│ owner_id     │
│ genre_id     │
└──────┬───────┘
       │ N
       │
       │ 1
┌──────▼───────┐
│    Genre     │
├──────────────┤
│ id           │
│ name         │
└──────────────┘
```

## 5. Règles de nommage

Le projet utilise les conventions suivantes :

- Les tables SQL utilisent des noms au singulier.
- Les entités Symfony utilisent le PascalCase (`User`, `Book`, `Genre`).
- Les propriétés PHP utilisent le camelCase (`createdAt`, `owner`, `genre`).
- Les colonnes SQL utilisent généralement le snake_case (`created_at`, `owner_id`, `genre_id`).

L'objectif est de conserver une convention de nommage cohérente dans l'ensemble du projet.

## 6. Contraintes et intégrité

La base de données applique plusieurs règles d'intégrité :

- Chaque table possède un identifiant unique.
- L'adresse email d'un utilisateur est unique.
- Les champs obligatoires ne peuvent pas être `NULL`.
- Un livre doit obligatoirement avoir un propriétaire.
- Un livre doit obligatoirement avoir un genre.
- Le statut d'un livre est contrôlé par l'Enum `BookStatus`.
- Les relations entre les tables utilisent des clés étrangères.
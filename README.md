# PolyBook - Application de Partage de Livres 📚

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-Academic-green.svg)]()

## 📋 Description

PolyBook est une application web de gestion de bibliothèque numérique développée dans le cadre du projet PROJ631. Inspirée de Goodreads, elle permet aux utilisateurs de partager leurs lectures, publier des avis, créer des listes personnalisées et participer à des cercles de lecture.

**🔗 Repository**: [https://github.com/Monarch7-NS/PolyBook.git](https://github.com/Monarch7-NS/PolyBook.git)

## 🎯 Objectifs du Projet

- Créer une plateforme de partage littéraire pour étudiants et professeurs
- Permettre la découverte de nouveaux livres via des recommandations communautaires
- Faciliter les échanges autour de la lecture
- Offrir des outils d'organisation personnelle (listes, favoris)
- Créer des espaces de discussion thématiques

## 👥 Équipe de Développement

### Partie Client Web
- **Anas Mohamed Draoui** - Responsable Design & CSS
- **Houssam Eddine Syouti** - Développeur PHP & Frontend
- **Ilyass Babile** - Développeur PHP & Base de données

### Partie Administrateur Java
- **Othmane Makboul** - Développeur Java
- **Amine Abidi** - Développeur Java

## 🛠️ Technologies Utilisées

### Frontend
- HTML5
- CSS3
- JavaScript (Vanilla)
- Font Awesome (icônes)

### Backend
- PHP 7.4+
- MySQL 5.7+
- Apache (via XAMPP)

### APIs Externes
- Open Library API (couvertures de livres)
- Gravatar (avatars utilisateurs)

## 📂 Structure du Projet

```
PolyBook/
├── 📁 db/
│   ├── data_base.sql       # Script de création de la BDD
│   └── procedur.sql        # Procédures stockées
│
├── 📁 website/
│   ├── index.php           # Page d'accueil
│   ├── connexion.php       # Configuration BDD
│   ├── header.php          # En-tête commun
│   ├── footer.php          # Pied de page
│   ├── styles.css          # Styles principaux
│   ├── scripts.js          # Scripts JavaScript
│   │
│   ├── 📁 Authentification/
│   │   ├── connexion_form.php
│   │   ├── inscription_form.php
│   │   └── deconnexion.php
│   │
│   ├── 📁 Catalogue/
│   │   ├── catalogue.php
│   │   ├── livre.php
│   │   └── ajouter_commentaire.php
│   │
│   ├── 📁 Profil/
│   │   ├── profil.php
│   │   ├── favoris.php
│   │   └── avis.php
│   │
│   ├── 📁 Social/
│   │   ├── cercles.php
│   │   ├── groupes_lecture.php
│   │   └── recherche_utilisateurs.php
│   │
│   └── 📁 Listes/
│       ├── listes.php
│       └── liste.php
│
└── 📄 README.md
```

## ✨ Fonctionnalités

### 🔐 Authentification
- ✅ Inscription/Connexion sécurisée
- ✅ Gestion des sessions
- ✅ Profils personnalisables
- ✅ Rôles (utilisateur/admin)

### 📚 Gestion des Livres
- ✅ Catalogue avec pagination
- ✅ Recherche multi-critères
- ✅ Filtrage par genre/langue
- ✅ Couvertures automatiques
- ✅ Pages détaillées

### ⭐ Avis et Interactions
- ✅ Publication d'avis (1-5 étoiles)
- ✅ Commentaires sur avis
- ✅ Système de tags
- ✅ Gestion des spoilers
- ✅ Modification/Suppression

### 📝 Organisation Personnelle
- ✅ Listes de lecture (publiques/privées)
- ✅ Livres favoris
- ✅ Historique des avis
- ✅ Statistiques d'activité

### 👥 Fonctionnalités Sociales
- ✅ Système d'amitié
- ✅ Cercles d'amis privés
- ✅ Groupes de lecture publics
- ✅ Flux d'activité
- ✅ Recherche d'utilisateurs

## 🗄️ Base de Données

### Schema Principal

```sql
-- Tables principales
user                 -- Utilisateurs
book                 -- Catalogue de livres  
review              -- Avis sur les livres
comment             -- Commentaires
genre               -- Genres littéraires
reading_list        -- Listes personnelles
circle              -- Cercles d'amis
reading_group       -- Groupes de lecture
friendship          -- Relations d'amitié
favorite_book       -- Favoris
```

### Relations
- Many-to-Many : `book_genre`, `user_genre`
- One-to-Many : `user->review`, `book->review`
- Self-referencing : `friendship`

## 🚀 Installation

### Prérequis
- XAMPP (Apache + MySQL + PHP)
- Navigateur web moderne
- Git

### Étapes d'installation

1. **Cloner le repository**
```bash
git clone https://github.com/Monarch7-NS/PolyBook.git
cd PolyBook
```

2. **Copier dans XAMPP**
```bash
cp -r website/* C:/xampp/htdocs/polybook/
```

3. **Créer la base de données**
- Démarrer XAMPP (Apache + MySQL)
- Ouvrir phpMyAdmin : http://localhost/phpmyadmin
- Importer `db/data_base.sql`

4. **Configurer la connexion**
Modifier `website/connexion.php` :
```php
$serveur = "localhost";
$utilisateur = "root";
$motdepasse = "";  // Votre mot de passe MySQL
$basededonnees = "polybook";
```

5. **Accéder à l'application**
```
http://localhost/polybook/
```

## 🔑 Comptes de Test

| Rôle | Username | Password |
|------|----------|----------|
| User | alice | pwd_1 |
| Admin | bob | pwd_2 |
| User | charlie | pwd_3 |

## 📸 Aperçu

### Page d'Accueil
- Livres à découvrir
- Groupes de lecture actifs
- Cercles d'amis (si connecté)

### Catalogue
- Grille de livres avec couvertures
- Filtres avancés
- Pagination fluide

### Profil Utilisateur
- Informations personnelles
- Statistiques d'activité
- Livres favoris
- Avis récents

## ⚠️ Limitations Connues

### Sécurité
- ⚠️ Mots de passe stockés en clair (préfixe "hashed_")
- ⚠️ Pas de protection CSRF
- ⚠️ Validation des entrées basique

### Base de Données
- ❌ IDs sans AUTO_INCREMENT
- ❌ Manque d'index pour l'optimisation
- ❌ Certaines clés étrangères manquantes

### Fonctionnalités
- ❌ Partie administrative Java non implémentée
- ❌ Système de notifications incomplet
- ❌ Discussions des groupes partiellement fonctionnelles
- ⚠️ Certaines fonctionnalités des cercles perdues (conflits Git)

## 🔧 Développement

### Structure du Code

**PHP** - Pattern MVC simplifié
```php
// Connexion BDD
require_once 'connexion.php';

// Vérification authentification
if (!estConnecte()) {
    redirect('connexion_form.php');
}

// Logique métier
$data = $conn->query("SELECT ...");

// Affichage
include 'header.php';
// HTML + PHP
include 'footer.php';
```

**CSS** - Variables et composants réutilisables
```css
:root {
    --primary-color: #4285f4;
    --secondary-color: #5f6368;
    /* ... */
}
```

### Conventions
- Nommage : snake_case pour PHP, camelCase pour JS
- Commentaires en français
- Indentation : 4 espaces
- UTF-8 pour tous les fichiers

## 🤝 Contribution

Ce projet étant académique, les contributions externes ne sont pas acceptées. Cependant, vous pouvez :
- 🍴 Fork le projet pour vos propres expérimentations
- ⭐ Star le repository si vous l'appréciez
- 📝 Ouvrir des issues pour signaler des bugs

## 📄 License

Projet académique - Polytech Annecy-Chambéry
Année : 2024-2025
Cours : PROJ631

## 🙏 Remerciements

- Équipe pédagogique de Polytech
- Open Library pour l'API des couvertures
- Communauté Stack Overflow pour l'aide technique

---

📧 **Contact**: Pour toute question, contactez les membres de l'équipe via GitHub.

🎥 **Démo**: Voir `website.mp4` pour une démonstration vidéo de l'application.

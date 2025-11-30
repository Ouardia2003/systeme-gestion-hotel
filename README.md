# 🏨 Système de Gestion d'Hôtel

## 📋 Description du Projet

Système complet de gestion hôtelière intégrant une base de données relationnelle PostgreSQL et une architecture réseau client-serveur. Ce projet universitaire (L3 Informatique - CY Cergy Paris Université) vise à automatiser les opérations clés d'un établissement hôtelier : réservations, facturation, gestion des chambres et maintenance.


## 👥 Équipe - Groupe D4

- 👩‍💻 *ACHAB Ouardia*  
  📧 Email : ouardia.achab@etu.cyu.fr
    
- 👩‍💻 *ISSAD Lisa*  
  📧 Email : lisa.issad@etu.cyu.fr
    
- 👩‍💻 *ELYAMANY Nouha*  
  📧 Email : nouha.elyamany@etu.cyu.fr


**Responsable de formation :** Marc Lemaire  
**Période :** Septembre 2025 - Décembre 2025


## 🎯 Objectifs

- ✅ Concevoir une base de données relationnelle optimisée pour la gestion hôtelière
- ✅ Développer une architecture réseau client-serveur robuste
- ✅ Créer une solution scalable garantissant une expérience fluide
- ✅ Implémenter un protocole de communication basé sur TCP/JSON


## ✨ Fonctionnalités Principales

- **Gestion des Réservations** : Création, modification et annulation de réservations avec vérification automatique de disponibilité des chambres
- **Gestion des Chambres** : Consultation et mise à jour en temps réel du statut des chambres via scan de code QR
- **Facturation et Paiements** : Génération automatique des factures avec calcul de TVA et support de multiples modes de paiement
- **Gestion de la Maintenance** : Signalement et attribution automatique des interventions techniques avec gestion des priorités
- **Services Hôteliers** : Enregistrement et facturation automatique des services additionnels consommés par les clients
- **Interface Personnel** : Application tablette permettant la gestion simplifiée des check-in, check-out et consultation instantanée des informations


## 🏗️ Architecture Technique

### 1. **Base de Données PostgreSQL**
- 10 tables principales
- 1 table de liaison (many-to-many)
- Gestion complète des contraintes d'intégrité

### 2. **Interface Web PHP**
- Réservations en ligne pour les clients
- Interface de gestion pour les employés
- Authentification sécurisée pour les deux types d'utilisateurs
- Consultation des disponibilités et gestion des opérations hôtelières

### 3. **Serveur Applicatif Java**
- Communication TCP avec clients distants
- Connexion JDBC vers PostgreSQL
- Gestion des transactions fiables
- Port configurable : 8080 par défaut ou port personnalisé en paramètre

### 4. **Client Réseau Python**
- Application tablette pour le personnel
- Scan de codes QR des chambres
- Mise à jour temps réel des statuts
- Démontre l'interopérabilité du protocole


## 🚀 Installation et Configuration

### 1. **Logiciels requis**
- PostgreSQL 12+
- Java JDK 11+
- Python 3.8+
- PHP 7.4+

### 2. **Bibliothèques Java**
- json-20240303.jar
- postgresql-42.7.1.jar

### 3. **Bibliothèques Python**
- socket (standard)
- json (standard)


### 4. **Connexion au serveur PostgreSQL distant**
```
psql -h postgresql-achabouardia.alwaysdata.net -p 5432 -U achabouardia -d achabouardia_hotel_db
```

### 5. **Lancement du Serveur Java**
```bash
# Compiler le serveur
javac -cp ../:/home/etudiant/Téléchargements/json-20240303.jar:/home/etudiant/Téléchargements/postgresql-42.7.7.jar server/*.java
# Exécuter avec port par défaut (8080)
java -cp .:/home/etudiant/Téléchargements/json-20240303.jar:/home/etudiant/Téléchargements/postgresql-42.7.7.jar server.ServerTCP

# Exécuter avec port personnalisé
java -cp .:/home/etudiant/Téléchargements/json-20240303.jar:/home/etudiant/Téléchargements/postgresql-42.7.7.jar server.ServerTCP 9000
```

**Sortie attendue :**
```
Serveur en attente sur le port 8080...
```


### 6. **Lancement du Client Python**
```bash
# Exécuter le client avec port par défaut (8080)
python3 /home/etudiant/Téléchargements/client/HotelClient.py localhost 
# Exécuter avec port personnalisé
python3 /home/etudiant/Téléchargements/client/HotelClient.py localhost 9000

```

**Sortie attendue :**
```
Client Partner ID 101 prêt à se connecter à 127.0.0.1:8080
Connexion réussie au port 8080.
```


## 🔐 Sécurité

### 1. **Contraintes Base de Données**
- **Intégrité référentielle** : Clés étrangères avec CASCADE
- **Contraintes de domaine** : CHECK, UNIQUE, NOT NULL
- **Validation format** : Email, téléphone, dates
- **Cohérence temporelle** : `date_fin > date_debut`


### 2. **Réseau**
- **TCP** : Garantie livraison et ordre des messages
- **Validation JSON** : Vérification structure avant traitement
- **Gestion erreurs** : Codes HTTP-like (400, 401, 404, 409, 500)
- **Authentification** : Handshake HELLO/HELLO_ACK obligatoire


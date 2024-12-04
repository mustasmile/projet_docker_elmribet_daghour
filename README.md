# PROJET_DOCKER_ELMRIBET_DAGHOUR

Une application pour la gestion des armes médiévales et des clans féodaux. Ce projet est basé sur une architecture multi-services utilisant Node.js pour le backend, PHP pour le frontend, MongoDB et PostgreSQL pour les bases de données, le tout conteneurisé avec Docker.

---

## Table des Matières

1. [Fonctionnalités](#fonctionnalités)
2. [Technologies Utilisées](#technologies-utilisées)
3. [Configuration du Projet](#configuration-du-projet)
4. [Structure du Projet](#structure-du-projet)
5. [Lancement de l'Application](#lancement-de-lapplication)
6. [Variables d'Environnement](#variables-denvironnement)
7. [Contribuer](#contribuer)
8. [Licence](#licence)

---

## Fonctionnalités

- **Node.js Backend** :
  - API RESTful pour la gestion des armes médiévales.
  - Connexion à MongoDB pour stocker les données d'armes.

- **Frontend PHP** :
  - Interface utilisateur pour interagir avec les données stockées dans PostgreSQL.

- **Bases de Données** :
  - MongoDB pour les armes.
  - PostgreSQL pour les données des clans.

- **Dockerisé** :
  - Facilement déployable grâce à Docker et Docker Compose.

---

## Technologies Utilisées

- **Backend** : Node.js, Express.
- **Frontend** : PHP, Apache.
- **Bases de Données** : MongoDB, PostgreSQL.
- **Containerisation** : Docker, Docker Compose.

---

## Configuration du Projet

### Fichier `docker-compose.yml`

Ce fichier définit les services suivants :

- **`node_app`** : Application Node.js connectée à MongoDB.
- **`php_apache`** : Serveur PHP avec Apache.
- **`mongo`** : Base de données MongoDB.
- **`postgres`** : Base de données PostgreSQL.

### Dockerfile Node.js (`Dockerfile.node`)

```dockerfile
FROM node:16

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

EXPOSE 3000

CMD ["node", "server.js"]
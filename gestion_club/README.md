# ⚽️🎾🏊‍♂️ Gestion du club omnisport – Guide général

Ce document aligne l’équipe (15 pers) sur l’architecture et les conventions pour construire une application de gestion de club multi-disciplines.

## 🧭 Objectif général
- Gérer adhérents, coachs, activités/entraînements, cotisations/paiements, compétitions/événements et statistiques.
- Réduire les frictions via des contrats (OpenAPI, migrations SQL, interfaces DAL) et un workflow Git clair.

## 📁 Structure des dossiers (racine `gestion_club/`)
- `api/` – API REST (endpoints, auth, validations) → voir `api/read.me`
- `bdd/` – Base de données (migrations, seeds, ERD) → voir `bdd/read.me`
- `front/` – Interface utilisateur (UI/UX) → voir `front/read.me`
- `data-layer/` – DAL/Services métiers (ORM/DAO/Repositories) → voir `data-layer/read.me`

## 🔐 Authentification & gestion des rôles
- Inscription/connexion sécurisée, profil utilisateur.
- Rôles: Admin (tout), Coach (ses séances/groupes), Adhérent (profil/inscriptions).
- Optionnel: mot de passe oublié / double authentification (2FA).

## 👤 Gestion des adhérents
- CRUD adhérents (nom, prénom, date de naissance, adresse, email, téléphone).
- Statut (actif, suspendu, en attente de paiement), disciplines pratiquées (N:N).
- Certificat médical (upload), export des adhérents (CSV/XLSX).

## 🧑‍🏫 Gestion des coachs
- Fiche coach: coordonnées, spécialité, horaires.
- Affectation à une ou plusieurs disciplines, historique de présence/activités encadrées.

## 🏋️‍♀️ Gestion des activités / entraînements
- Création d’activités (natation, tennis, course…), créneaux horaires, lieu, capacité max.
- Inscription/désinscription des adhérents, feuille de présence numérique.
- Planning hebdomadaire généré/affiché.

## 💰 Gestion des cotisations et paiements
- Tarifs par discipline/formule (annuelle, trimestrielle…).
- Suivi paiements (payé, en attente, retard), reçus/factures, rappels email automatiques.

## 🏆 Compétitions / événements
- Calendrier des compétitions, inscriptions, résultats/classements.
- Statistiques de participation.

## 📊 Tableau de bord & statistiques
- Adhérents par sport, taux de présence, évolution des inscriptions, revenus par mois.

## 📱 Interfaces utilisateurs (UI)
- Dashboard administrateur (cards: adhérents, activités, paiements, etc.).
- Profil utilisateur (photo, infos, planning, cotisation).
- Interface responsive (mobile). Optionnel: mur d’actualité.

## ⚙️ Aspects techniques / architecture
- Base de données (MySQL/phpMyAdmin):
  - Tables: utilisateurs, adherents, coachs, disciplines, activites, creneaux, inscriptions_activites, cotisations, paiements, competitions, inscriptions_competitions, resultats.
- Back-end (Codespaces):
  - PHP (ou Node.js / Python Flask), requêtes SQL pour CRUD via couche DAL.
- Front-end:
  - HTML/CSS/JS (Bootstrap) ou framework léger (React si OK).
- API REST (conseillée):
  - Endpoints /api/adherents, /api/activites, /api/paiements, /api/competitions, etc.

## 🤝 Contrats inter-équipes
- API: `api/openapi.yaml` (source de vérité) + clients Front générés/alignés.
- BDD: migrations versionnées + changelog; annonces avant breaking changes.
- DAL: services typés/DTO; pas d’objets de persistance exposés à l’API.

## 🔀 Workflow Git (proposition)
- Branches: `main` (protégée), `dev`, `feature/<equipe>/<sujet>`, `hotfix/<sujet>`.
- Commits: Conventional Commits. PR: 1 review min, tests verts; merge en squash recommandé.

## ✅ Definition of Done (DoD) commun
- Tests (unit/intégration) couvrant les nouveautés; lint/format OK.
- Docs à jour (OpenAPI, migrations, readme). Pas de secrets en logs.
- Listes paginées/filtrées; perfs OK (index nécessaires en place).

## 🗓️ Démarrage (Semaine 1)
- BDD: ERD v0 + migrations initiales + seeds.
- API: `openapi.yaml` v0 + GET /health + POST /auth/login (mock) + GET /adherents.
- DAL: Repos/Services Adhérents + inscription à créneau (gestion capacité).
- Front: Login, Dashboard, Liste Adhérents, Détail Adhérent.

## 🧩 Bonus (si temps disponible)
- Synchronisation Google Calendar (entraînements).
- Upload photos de compétitions.
- Envoi d’emails automatiques (PHPMailer).
- Gestion de documents (certificats, factures, etc.).

## 📝 ADR (Architecture Decision Records)
- Pour auth (JWT vs sessions), ORM vs DAO, pagination, formats d’export, créer `docs/adr/YYYYMMDD-titre.md` (optionnel si dossier `docs/`).

## Liens utiles
- API → `gestion_club/api/read.me`
- BDD → `gestion_club/bdd/read.me`
- Front → `gestion_club/front/read.me`
- DAL → `gestion_club/data-layer/read.me`


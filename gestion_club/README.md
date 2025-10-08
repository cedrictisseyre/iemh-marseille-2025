# Gestion du club – Guide général (organisation & conventions)

Ce document synthétise l’organisation des 4 équipes, les contrats inter-équipes, les conventions d’ingénierie et le workflow pour livrer rapidement et proprement.

## Objectifs
- Aligner 15 personnes sur une architecture claire (API, BDD, Front, Data Layer/DAL).
- Réduire les frictions via des contrats partagés (OpenAPI, migrations, interfaces).
- Garantir la qualité (tests, revues, conventions) sans ralentir l’équipe.

## Structure des dossiers (racine `gestion_club/`)
- `api/` – Équipe API (REST) → voir `api/read.me`
- `bdd/` – Équipe Base de Données → voir `bdd/read.me`
- `front/` – Équipe Front (UI/UX) → voir `front/read.me`
- `data-layer/` – Équipe Lien avec BDD (DAL/Services) → voir `data-layer/read.me`
- `docs/` – Documentation transversale (ce fichier)

## Répartition des équipes (rappel)
- API: endpoints, auth, validation, erreurs, doc OpenAPI.
- BDD: schéma, migrations, seeds, index, ERD.
- Front: UI/UX, composants, pages, accessibilité, client API.
- Data Layer (DAL): ORM/DAO/Repositories, services métiers, transactions.

## Contrats inter-équipes (source de vérité)
- API: `gestion_club/api/openapi.yaml` (à créer) — versionné; les clients Front sont générés ou alignés dessus.
- BDD: Changelog/migrations sous `gestion_club/bdd/migrations/` + `bdd/changelog.sql` — communiqués en avance.
- DAL: Interfaces de services stables exposées à l’API (documentées dans `data-layer/read.me`).

Compatibilité:
- Préserver la compat ascendante quand possible; sinon déprécier et planifier la transition.

## Workflow Git (proposition)
- Branches:
  - `main` (protégée): prod/stable.
  - `dev`: intégration continue.
  - `feature/<equipe>/<sujet>` (ex: `feature/api/auth-jwt`).
  - `hotfix/<sujet>` pour corrections urgentes.
- Commits: Conventional Commits (ex: `feat(api): add members list`, `fix(front): form validation`).
- PR: taille raisonnable, description claire, 1 review min, tests verts, liens vers tickets.
- Merge: squash & merge recommandé; supprimer la branche après merge.

## Qualité & Sécurité (DoD commun)
- Tests: unit/integration pertinents, > couvrir nouveautés.
- Lint/format: appliqués; pas de warnings bloquants si évitables.
- Docs: OpenAPI, migrations, readme(s) mis à jour.
- Erreurs: messages et codes HTTP cohérents; pas de secrets en logs.
- Performance: requêtes indexées pour les listes; pagination côté API/DAL.

## Rituels légers
- Daily 10 min/équipe (blocage, priorité du jour).
- Weekly inter-équipes 30 min (changements de contrats à venir, risques).
- Démo courte en fin de sprint (ce qui est testable).

## Environnements (baseline dev)
- Local: `.env` (non versionné) — gérer secrets via variables d’env.
- Base locale pour tests d’intégration (DAL/BDD) avec seeds minimales.

## Conventions rapides
- Temps/dates en UTC; format ISO 8601.
- JSON en snake_case; réponses paginées: `{ data, meta: { total, page, limit } }`.
- Tables SQL au pluriel; colonnes timestamps: `created_at`, `updated_at`, `deleted_at` (nullable).

## Démarrage Semaine 1 (checklist)
- BDD: ERD v0 + premières migrations + seeds de base.
- API: `openapi.yaml` v0 + 2 endpoints mockés + tests.
- DAL: `MembersRepository` + `MembersService` (liste/détail) + tests.
- Front: page Login + page Liste (Membres) consommant GET /membres.

## ADR (Architecture Decision Records)
- Pour les décisions clés (auth, ORM, pagination, formats), créer un fichier `docs/adr/YYYYMMDD-titre.md` avec contexte, options, décision.

## Liens utiles
- API → `gestion_club/api/read.me`
- BDD → `gestion_club/bdd/read.me`
- Front → `gestion_club/front/read.me`
- DAL → `gestion_club/data-layer/read.me`

— Fin —

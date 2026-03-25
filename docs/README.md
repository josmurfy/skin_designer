# 📚 DOCUMENTATION DU PROJET - PhoenixLiquidation

---

## 🤖 [INSTRUCTIONS POUR L'IA]

**⚠️ SI tu es une IA et que ce fichier est attaché:**

**Ce fichier README.md explique l'ORGANISATION de la documentation.**

**RÈGLES D'UTILISATION:**

1. **Fichiers OBLIGATOIRES à lire au début de session:**
   - `DEV_GUIDELINES.md` → Règles projet PhoenixLiquidation
   - `OPENCART_GUIDELINES.md` → Standards OpenCart 4
   - `README.md` (ce fichier) → Organisation documentation

2. **Fichiers PERMANENTS (jamais supprimés):**
   - ✅ `/docs/DEV_GUIDELINES.md`
   - ✅ `/docs/DEV_TASKS.md`
   - ✅ `/docs/DEV_PATTERNS.md`
   - ✅ `/docs/OPENCART_GUIDELINES.md`
   - ✅ `/docs/README.md`
   - ✅ `/docs/archives/` (tout le contenu)

3. **Fichiers TEMPORAIRES (supprimés au "bye bye yoda"):**
   - ❌ `WORKLOAD_*.md`
   - ❌ `LOG_*.md`
   - ❌ `REPORT_*.md`
   - ❌ `AUDIT_*.md`
   - ❌ `MIGRATION_*.md`
   - ❌ `REFACTOR_*.md`

4. **Workflow documentation travail majeur:**
   ```
   AVANT travail → Créer WORKLOAD_[NOM].md
   PENDANT → Créer LOG_[NOM].md
   APRÈS → Créer REPORT_[NOM]_SUCCESS.md
   AU BYEBYE → Tous supprimés SAUF si archivage demandé
   ```

5. **Archivage (seulement si user demande):**
   ```bash
   # Consolider fichiers temporaires
   cat docs/WORKLOAD_*.md docs/LOG_*.md docs/REPORT_*.md > docs/archives/ARCHIVE_[NOM]_$(date +%Y-%m-%d).md
   # Puis supprimer temporaires
   find docs/ -name "WORKLOAD_*.md" -o -name "LOG_*.md" -o -name "REPORT_*.md" -delete
   ```

6. **NE JAMAIS:**
   - Supprimer fichiers permanents (DEV_*, OPENCART_*, README.md)
   - Mettre backups dans /docs/ (ils restent dans leurs dossiers respectifs)
   - Archiver automatiquement (seulement si user demande)

---

## 📚 SI JE T'APPEL YODA

Ce dossier contient toute la documentation technique et les journaux de modifications importantes du projet.

---

## 📋 STRUCTURE

### Guidelines Principales (Permanentes)
- **DEV_GUIDELINES.md** - Guidelines de développement ⭐
- **OPENCART_GUIDELINES.md** - Guidelines spécifiques OpenCart ⭐
- **README.md** - Ce fichier
  
  ℹ️ *Ces fichiers sont aussi disponibles à la racine du projet pour faciliter l'attachement dans les chats AI*

### Archives (Historique)
- **archives/** - Dossier contenant les documentations de sessions IMPORTANTES à conserver
  - `ARCHIVE_DECENTRALIZATION_2026-01-06.md` - Archive complète décentralisation tools.js
  - `ARCHIVE_[NOM]_[DATE].md` - Futures archives de sessions majeures

⚠️ **Note:** Les fichiers de travail quotidiens (WORKLOAD, LOG, REPORT) sont créés en temps réel pendant une session et **automatiquement supprimés** lors du `byebye`. Seules les archives consolidées des sessions importantes sont conservées ici.

---

## 🎯 CONVENTION

### Où placer la documentation?

#### 📁 `/docs/` - Documentation de Projet
- Journaux de modifications majeures (CHANGELOG, WORKLOAD, SUCCESS_REPORT)
- Documentation technique spécifique (migration, refactoring, décentralisation)
- Rapports d'audit et d'analyse
- Guides de déploiement et procédures

#### 📄 Racine du projet - Guidelines Générales
- `DEV_GUIDELINES.md` - Guidelines de développement générales
- `OPENCART_GUIDELINES.md` - Guidelines spécifiques au framework
- `README.md` - Documentation principale du projet (si existant)

#### 📁 Dossier `/administrator/` - Documentation Admin
- `INTERNATIONALISATION_AUDIT.md` - Audits spécifiques au backend
- Documentation liée aux fonctionnalités administratives

---

## 📝 NAMING CONVENTIONS

### Pour les fichiers .md dans `/docs/`:

**Format recommandé:** `[TYPE]_[NOM]_[DATE].md`

**Types courants:**
- `WORKLOAD_` - Plan de travail pour une tâche majeure
- `LOG_` - Journal de modifications détaillé
- `REPORT_` - Rapport final/success report
- `AUDIT_` - Audit de code ou de fonctionnalités
- `MIGRATION_` - Documentation de migration
- `REFACTOR_` - Documentation de refactoring

**Exemples:**
- `WORKLOAD_DECENTRALIZATION.md`
- `LOG_API_MIGRATION_2026-01-15.md`
- `REPORT_PERFORMANCE_OPTIMIZATION_2026-02-01.md`
- `AUDIT_SECURITY_2026-03-10.md`

---

## 🔧 WORKFLOW RECOMMANDÉ

### Lors d'un travail majeur:

1. **AVANT** de commencer:
   - Créer `WORKLOAD_[NOM].md` avec le plan détaillé
   - Ce fichier est à la racine de `/docs/` (temporaire)

2. **PENDANT** le travail:
   - Créer `LOG_[NOM].md` pour documenter les changements en temps réel
   - Mettre à jour le workload avec les statuts
   - Ces fichiers servent de "debug en temps réel"

3. **APRÈS completion:**
   - Créer `REPORT_[NOM]_SUCCESS.md` avec le résumé final
   
4. **AU BYEBYE (fin de session):**
   - **PAR DÉFAUT:** Tous supprimés automatiquement ❌
   - **SI SESSION IMPORTANTE:** Demander archivage avant byebye
     - Créer `ARCHIVE_[NOM]_[DATE].md` consolidé
     - Déplacer dans `/docs/archives/` ✅
     - Puis supprimer fichiers temporaires

**⚠️ IMPORTANT:**
Les fichiers WORKLOAD/LOG/REPORT sont des **outils de travail temporaires** créés pendant la session. Ils sont supprimés au `byebye` pour garder `/docs/` propre.

**Seules les archives consolidées** des sessions majeures sont conservées dans `/docs/archives/`.

---

## 📦 BACKUPS

Les fichiers `.backup` doivent rester dans leurs dossiers respectifs, **PAS** dans `/docs/`.

---

**Date de création:** 2026-01-06  
**Maintenu par:** Équipe de développement

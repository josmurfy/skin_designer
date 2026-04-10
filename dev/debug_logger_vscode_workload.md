# Workload : Extension VS Code - Debug Logger Manager

## Vue d'ensemble

Développer une extension VS Code qui permet aux développeurs de gérer les tickets Debug Logger d'OpenCart 4 directement depuis leur éditeur. L'extension doit se connecter à la base de données MySQL, afficher les rapports de bugs, et intégrer le contexte des tickets dans les prompts GitHub Copilot pour accélérer la résolution.

---

## Objectifs principaux

1. **Connexion base de données** : Se connecter à la DB OpenCart via credentials configurables
2. **Vue liste tickets** : Afficher tous les rapports avec filtres et recherche
3. **Détails ticket** : Voir toutes les infos d'un rapport (console log, network log, screenshot)
4. **Injection Copilot** : Bouton pour injecter le contexte du ticket dans le prompt Copilot
5. **Gestion historique** : Logger les changements, résolutions, et commentaires
6. **Workflow développeur** : Assigner, fermer, tagger, et documenter les corrections

---

## Architecture technique

### Stack technologique

- **Extension VS Code** : TypeScript avec VS Code Extension API
- **Base de données** : MySQL via `mysql2` package
- **UI** : Webview API de VS Code + HTML/CSS/JavaScript
- **Intégration Copilot** : VS Code Chat API (`vscode.lm` namespace)
- **State management** : Context global de l'extension

### Structure du projet

```
debug-logger-manager/
├── src/
│   ├── extension.ts                 # Point d'entrée
│   ├── database/
│   │   ├── connection.ts            # Gestion connexion MySQL
│   │   ├── queries.ts               # Toutes les requêtes SQL
│   │   └── models.ts                # Interfaces TypeScript
│   ├── views/
│   │   ├── ticketListView.ts        # Vue principale liste
│   │   ├── ticketDetailView.ts      # Vue détail ticket
│   │   └── settingsView.ts          # Configuration DB
│   ├── providers/
│   │   ├── ticketProvider.ts        # TreeDataProvider pour sidebar
│   │   └── copilotProvider.ts       # Intégration Copilot Chat
│   ├── commands/
│   │   ├── ticketCommands.ts        # Toutes les commandes
│   │   └── copilotCommands.ts       # Commandes Copilot
│   └── utils/
│       ├── formatter.ts             # Format données pour affichage
│       └── logger.ts                # Logs extension
├── webview/
│   ├── ticketList.html
│   ├── ticketDetail.html
│   ├── styles.css
│   └── main.js
├── package.json
└── README.md
```

---

## Fonctionnalités détaillées

### 1. Configuration de la connexion

#### Interface Settings

Créer une commande `Debug Logger: Configure Database` qui ouvre un webview avec formulaire :

```typescript
interface DatabaseConfig {
  host: string;           // ex: localhost
  port: number;           // ex: 3306
  database: string;       // ex: opencart_db
  user: string;           // ex: root
  password: string;       // Stocké dans SecretStorage VS Code
  prefix: string;         // ex: oc_
}
```

**Stockage sécurisé** :
- Host, port, database, user, prefix → `globalState`
- Password → `context.secrets` (API SecretStorage)

**Test de connexion** :
- Bouton "Test Connection" qui exécute `SELECT 1` 
- Affiche statut : ✅ Connecté | ❌ Erreur avec message

#### Fichier `connection.ts`

```typescript
import mysql from 'mysql2/promise';

export class DatabaseConnection {
  private pool: mysql.Pool | null = null;
  
  async connect(config: DatabaseConfig): Promise<void> {
    this.pool = mysql.createPool({
      host: config.host,
      port: config.port,
      database: config.database,
      user: config.user,
      password: config.password,
      waitForConnections: true,
      connectionLimit: 10,
      queueLimit: 0
    });
  }
  
  async testConnection(): Promise<boolean> {
    try {
      const [rows] = await this.pool!.query('SELECT 1');
      return true;
    } catch (error) {
      return false;
    }
  }
  
  getPool(): mysql.Pool {
    if (!this.pool) throw new Error('Database not connected');
    return this.pool;
  }
}
```

---

### 2. Vue liste des tickets

#### Sidebar TreeView

Créer un TreeDataProvider dans la sidebar VS Code :

```
DEBUG LOGGER
├── 🔴 Bugs ouverts (12)
│   ├── #145 - TypeError in product.js
│   ├── #142 - 404 on checkout page
│   └── ...
├── ⚠️ Warnings ouverts (5)
├── ℹ️ Info ouverts (3)
├── ✅ Fermés récents (10)
└── 👤 Assignés à moi (7)
```

**Structure du TreeItem** :

```typescript
interface TicketTreeItem extends vscode.TreeItem {
  ticket: DebugReport;
  contextValue: 'ticket-bug' | 'ticket-warning' | 'ticket-info' | 'ticket-closed';
}
```

**Click sur un ticket** → Ouvre la vue détail dans un webview panel

#### Webview Liste complète

Commande `Debug Logger: Show All Tickets` ouvre un webview avec tableau filtrable :

**Fonctionnalités** :
- Filtres : Status (ouvert/fermé), Severity (bug/warning/info), Source (admin/catalog), Tags
- Recherche full-text : URL, comment, console_log, resolution
- Tri cliquable : ID, Date, Severity, Assigned
- Pagination : 50 tickets par page
- Compteurs en haut : Total | Ouverts | Bugs | Warnings | Infos

**Tableau HTML** :

```html
<table class="ticket-table">
  <thead>
    <tr>
      <th data-sort="id">ID ↕</th>
      <th data-sort="severity">Severity ↕</th>
      <th>URL</th>
      <th>Comment</th>
      <th data-sort="date_added">Date ↕</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <!-- Rangées générées dynamiquement -->
    <tr data-ticket-id="145" class="severity-bug status-open">
      <td>#145</td>
      <td><span class="badge badge-bug">Bug</span></td>
      <td class="url-cell">admin/index.php?route=catalog/product</td>
      <td>TypeError: Cannot read property 'value'...</td>
      <td>2026-04-09 14:32</td>
      <td><span class="status-open">Open</span></td>
      <td>
        <button class="btn-view">👁️ View</button>
        <button class="btn-copilot">🤖 Copilot</button>
      </td>
    </tr>
  </tbody>
</table>
```

**CSS** : Style sombre compatible VS Code theme (utiliser CSS variables `var(--vscode-*)`)

---

### 3. Vue détail d'un ticket

#### Webview Panel Détail

Commande `Debug Logger: Show Ticket Details` ouvre un panel avec toutes les infos :

**Sections** :

1. **Header** : ID, Severity badge, Status, Date, Assigned user
2. **URL** : Lien cliquable (ouvre dans browser par défaut)
3. **Comment utilisateur** : Textarea éditable
4. **Console Log** : Code block avec syntax highlighting
5. **Network Log** : Code block JSON formaté
6. **Screenshot** : Image base64 affichée (si existe)
7. **Resolution Notes** : Textarea éditable pour notes du dev
8. **Tags** : Liste modifiable (ajouter/retirer)
9. **Actions** : Boutons Close, Reopen, Delete, Assign to me
10. **History** : Timeline des changements

**Bouton principal** : **"🤖 Send to Copilot"** (voir section 4)

#### Fichier `ticketDetailView.ts`

```typescript
export class TicketDetailView {
  private panel: vscode.WebviewPanel | null = null;
  
  async show(ticketId: number): Promise<void> {
    const ticket = await queries.getTicketById(ticketId);
    
    this.panel = vscode.window.createWebviewPanel(
      'debugLoggerDetail',
      `Ticket #${ticket.id}`,
      vscode.ViewColumn.One,
      {
        enableScripts: true,
        localResourceRoots: [/* webview resources */]
      }
    );
    
    this.panel.webview.html = this.getHtmlContent(ticket);
    
    // Message handlers
    this.panel.webview.onDidReceiveMessage(async (message) => {
      switch (message.command) {
        case 'updateComment':
          await queries.updateTicket(ticketId, { comment: message.value });
          break;
        case 'updateResolution':
          await queries.updateTicket(ticketId, { resolution: message.value });
          break;
        case 'closeTicket':
          await queries.closeTicket(ticketId);
          this.refresh();
          break;
        case 'sendToCopilot':
          await this.sendToCopilot(ticket);
          break;
      }
    });
  }
}
```

---

### 4. Intégration GitHub Copilot

#### Injection du contexte dans Copilot Chat

Utiliser l'API `vscode.lm` pour créer un participant Copilot custom :

**Fichier `copilotProvider.ts`** :

```typescript
import * as vscode from 'vscode';

export class CopilotProvider {
  
  async injectTicketContext(ticket: DebugReport): Promise<void> {
    
    // Construire le prompt structuré
    const prompt = this.buildPrompt(ticket);
    
    // Ouvrir Copilot Chat et injecter le prompt
    await vscode.commands.executeCommand('workbench.action.chat.open', {
      query: prompt
    });
  }
  
  private buildPrompt(ticket: DebugReport): string {
    return `
# Debug Ticket #${ticket.id} - ${ticket.severity.toUpperCase()}

## Context
- **URL**: ${ticket.url}
- **Source**: ${ticket.source}
- **Date**: ${ticket.date_added}
- **User**: ${ticket.admin_user}

## User Report
${ticket.comment}

## Console Errors
\`\`\`javascript
${ticket.console_log || 'No console errors captured'}
\`\`\`

## Network Errors
\`\`\`json
${ticket.network_log || 'No network errors captured'}
\`\`\`

## Resolution Notes (if any)
${ticket.resolution || 'Not yet resolved'}

---

**Task**: Analyze this bug report and suggest:
1. The likely root cause
2. The file(s) that need to be modified
3. A code fix with explanation
4. Any preventive measures

Focus on OpenCart 4.x PHP/JavaScript/Twig context.
`;
  }
}
```

#### Participant Copilot custom (optionnel, avancé)

Créer un participant `@debuglogger` qui peut être appelé :

```typescript
// Dans extension.ts
const participant = vscode.chat.createChatParticipant('debuglogger', async (request, context, stream, token) => {
  
  // Parse la requête
  if (request.command === 'analyze') {
    const ticketId = parseInt(request.prompt);
    const ticket = await queries.getTicketById(ticketId);
    
    // Envoyer le contexte au modèle
    stream.markdown(`Analyzing ticket #${ticketId}...\n\n`);
    stream.markdown(`**URL**: ${ticket.url}\n`);
    stream.markdown(`**Error**: ${ticket.comment}\n\n`);
    
    // Utiliser le modèle pour suggérer une solution
    const messages = [
      vscode.LanguageModelChatMessage.User(buildPrompt(ticket))
    ];
    
    const model = await vscode.lm.selectChatModels({ family: 'gpt-4' });
    const chatRequest = await model[0].sendRequest(messages, {}, token);
    
    for await (const fragment of chatRequest.text) {
      stream.markdown(fragment);
    }
  }
  
  return {};
});
```

**Utilisation** : Dans Copilot Chat, taper `@debuglogger /analyze 145`

---

### 5. Gestion de l'historique

#### Table d'historique (nouvelle table SQL)

Créer une table `{prefix}debug_logger_history` :

```sql
CREATE TABLE IF NOT EXISTS `oc_debug_logger_history` (
  `history_id` INT AUTO_INCREMENT PRIMARY KEY,
  `report_id` INT NOT NULL,
  `action` VARCHAR(50) NOT NULL,  -- 'created', 'updated', 'closed', 'reopened', 'assigned', 'tag_added', etc.
  `field_changed` VARCHAR(100),    -- ex: 'comment', 'resolution', 'status'
  `old_value` TEXT,
  `new_value` TEXT,
  `changed_by` VARCHAR(255),       -- Username VS Code
  `changed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_report` (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### Logger automatiquement les changements

Dans `queries.ts`, wrapper toutes les UPDATE avec un log :

```typescript
async updateTicket(id: number, updates: Partial<DebugReport>): Promise<void> {
  const old = await this.getTicketById(id);
  
  // Appliquer les updates
  await this.pool.query(
    `UPDATE ${this.prefix}debug_report SET ? WHERE id = ?`,
    [updates, id]
  );
  
  // Logger chaque champ modifié
  for (const [field, newValue] of Object.entries(updates)) {
    if (old[field] !== newValue) {
      await this.addHistory({
        report_id: id,
        action: 'updated',
        field_changed: field,
        old_value: String(old[field]),
        new_value: String(newValue),
        changed_by: this.getCurrentVSCodeUser()
      });
    }
  }
}
```

#### Afficher l'historique dans la vue détail

Section "History" en bas de la vue détail :

```html
<div class="history-section">
  <h3>📜 Change History</h3>
  <div class="timeline">
    <div class="timeline-item">
      <span class="time">2026-04-09 15:42</span>
      <span class="user">@josmurfy</span>
      <span class="action">updated <strong>resolution</strong></span>
      <div class="diff">
        <span class="old">-</span>
        <span class="new">+ Fixed by adding null check in line 234</span>
      </div>
    </div>
    <div class="timeline-item">
      <span class="time">2026-04-09 14:32</span>
      <span class="user">@admin</span>
      <span class="action">created ticket</span>
    </div>
  </div>
</div>
```

---

### 6. Workflow développeur optimisé

#### Actions rapides

**Depuis la vue liste** :
- Click droit sur ticket → Menu contextuel :
  - 🤖 Send to Copilot
  - 👤 Assign to me
  - ✅ Mark as closed
  - 🏷️ Add tag...
  - 🗑️ Delete

**Depuis la vue détail** :
- Bouton "Start Working" → Crée une branche Git `fix/ticket-{id}` et ouvre les fichiers suspects
- Bouton "Mark Resolved" → Change status + demande notes de résolution
- Bouton "Create Issue" → Ouvre formulaire pour créer un GitHub Issue lié

#### Auto-détection des fichiers concernés

Parser le `console_log` et `url` pour suggérer les fichiers à ouvrir :

```typescript
function detectFilesFromTicket(ticket: DebugReport): string[] {
  const files: string[] = [];
  
  // Parser l'URL pour trouver la route OpenCart
  const routeMatch = ticket.url.match(/route=([^&]+)/);
  if (routeMatch) {
    const route = routeMatch[1].replace(/\//g, '/');
    // ex: catalog/product → catalog/controller/product.php
    files.push(`catalog/controller/${route}.php`);
    files.push(`catalog/model/${route}.php`);
  }
  
  // Parser le console_log pour les noms de fichiers
  const fileMatches = ticket.console_log.match(/([a-z_]+\.(?:php|js|twig))/gi);
  if (fileMatches) {
    files.push(...fileMatches);
  }
  
  return [...new Set(files)]; // Dédupliquer
}
```

**Bouton "Open Related Files"** → Ouvre tous les fichiers suspects dans l'éditeur

---

### 7. Commandes VS Code

Enregistrer toutes les commandes dans `package.json` :

```json
{
  "contributes": {
    "commands": [
      {
        "command": "debugLogger.configure",
        "title": "Debug Logger: Configure Database"
      },
      {
        "command": "debugLogger.showTickets",
        "title": "Debug Logger: Show All Tickets"
      },
      {
        "command": "debugLogger.showTicket",
        "title": "Debug Logger: Show Ticket Details"
      },
      {
        "command": "debugLogger.sendToCopilot",
        "title": "Debug Logger: Send Ticket to Copilot"
      },
      {
        "command": "debugLogger.assignToMe",
        "title": "Debug Logger: Assign to Me"
      },
      {
        "command": "debugLogger.closeTicket",
        "title": "Debug Logger: Close Ticket"
      },
      {
        "command": "debugLogger.refresh",
        "title": "Debug Logger: Refresh Tickets"
      },
      {
        "command": "debugLogger.openRelatedFiles",
        "title": "Debug Logger: Open Related Files"
      }
    ],
    "viewsContainers": {
      "activitybar": [
        {
          "id": "debug-logger",
          "title": "Debug Logger",
          "icon": "resources/icon.svg"
        }
      ]
    },
    "views": {
      "debug-logger": [
        {
          "id": "ticketList",
          "name": "Tickets"
        }
      ]
    },
    "menus": {
      "view/item/context": [
        {
          "command": "debugLogger.sendToCopilot",
          "when": "view == ticketList && viewItem == ticket",
          "group": "inline"
        },
        {
          "command": "debugLogger.assignToMe",
          "when": "view == ticketList && viewItem == ticket"
        },
        {
          "command": "debugLogger.closeTicket",
          "when": "view == ticketList && viewItem == ticket"
        }
      ]
    }
  }
}
```

---

### 8. Requêtes SQL (fichier `queries.ts`)

Toutes les requêtes nécessaires :

```typescript
export class DebugLoggerQueries {
  
  constructor(private pool: mysql.Pool, private prefix: string) {}
  
  // Liste tous les tickets avec filtres
  async getTickets(filters: TicketFilters): Promise<DebugReport[]> {
    let query = `
      SELECT r.*, 
             GROUP_CONCAT(t.tag_name) as tags,
             u.username as assigned_username
      FROM ${this.prefix}debug_report r
      LEFT JOIN ${this.prefix}debug_logger_tags t ON r.id = t.report_id
      LEFT JOIN ${this.prefix}user u ON r.assigned_to = u.user_id
      WHERE 1=1
    `;
    
    const params: any[] = [];
    
    if (filters.status !== undefined) {
      query += ` AND r.status = ?`;
      params.push(filters.status);
    }
    
    if (filters.severity) {
      query += ` AND r.severity = ?`;
      params.push(filters.severity);
    }
    
    if (filters.source) {
      query += ` AND r.source = ?`;
      params.push(filters.source);
    }
    
    if (filters.assignedTo) {
      query += ` AND r.assigned_to = ?`;
      params.push(filters.assignedTo);
    }
    
    if (filters.search) {
      query += ` AND (
        r.url LIKE ? OR 
        r.comment LIKE ? OR 
        r.console_log LIKE ? OR 
        r.resolution LIKE ?
      )`;
      const searchTerm = `%${filters.search}%`;
      params.push(searchTerm, searchTerm, searchTerm, searchTerm);
    }
    
    query += ` GROUP BY r.id ORDER BY r.id DESC`;
    
    if (filters.limit) {
      query += ` LIMIT ? OFFSET ?`;
      params.push(filters.limit, filters.offset || 0);
    }
    
    const [rows] = await this.pool.query(query, params);
    return rows as DebugReport[];
  }
  
  // Détail d'un ticket
  async getTicketById(id: number): Promise<DebugReport> {
    const [rows] = await this.pool.query(
      `SELECT r.*, 
              GROUP_CONCAT(t.tag_name) as tags,
              u.username as assigned_username
       FROM ${this.prefix}debug_report r
       LEFT JOIN ${this.prefix}debug_logger_tags t ON r.id = t.report_id
       LEFT JOIN ${this.prefix}user u ON r.assigned_to = u.user_id
       WHERE r.id = ?
       GROUP BY r.id`,
      [id]
    );
    return (rows as any[])[0];
  }
  
  // Mettre à jour un ticket
  async updateTicket(id: number, updates: Partial<DebugReport>): Promise<void> {
    const old = await this.getTicketById(id);
    
    await this.pool.query(
      `UPDATE ${this.prefix}debug_report SET ? WHERE id = ?`,
      [updates, id]
    );
    
    // Logger les changements
    for (const [field, newValue] of Object.entries(updates)) {
      if (old[field] !== newValue) {
        await this.addHistory({
          report_id: id,
          action: 'updated',
          field_changed: field,
          old_value: String(old[field] || ''),
          new_value: String(newValue),
          changed_by: this.getCurrentUser()
        });
      }
    }
  }
  
  // Fermer un ticket
  async closeTicket(id: number): Promise<void> {
    await this.updateTicket(id, { status: 1 });
    await this.addHistory({
      report_id: id,
      action: 'closed',
      changed_by: this.getCurrentUser()
    });
  }
  
  // Rouvrir un ticket
  async reopenTicket(id: number): Promise<void> {
    await this.updateTicket(id, { status: 0 });
    await this.addHistory({
      report_id: id,
      action: 'reopened',
      changed_by: this.getCurrentUser()
    });
  }
  
  // Assigner un ticket
  async assignTicket(id: number, userId: number): Promise<void> {
    await this.updateTicket(id, { assigned_to: userId });
    await this.addHistory({
      report_id: id,
      action: 'assigned',
      new_value: String(userId),
      changed_by: this.getCurrentUser()
    });
  }
  
  // Ajouter un tag
  async addTag(reportId: number, tagName: string): Promise<void> {
    await this.pool.query(
      `INSERT INTO ${this.prefix}debug_logger_tags (report_id, tag_name) VALUES (?, ?)`,
      [reportId, tagName]
    );
    await this.addHistory({
      report_id: reportId,
      action: 'tag_added',
      new_value: tagName,
      changed_by: this.getCurrentUser()
    });
  }
  
  // Retirer un tag
  async removeTag(reportId: number, tagName: string): Promise<void> {
    await this.pool.query(
      `DELETE FROM ${this.prefix}debug_logger_tags WHERE report_id = ? AND tag_name = ?`,
      [reportId, tagName]
    );
    await this.addHistory({
      report_id: reportId,
      action: 'tag_removed',
      old_value: tagName,
      changed_by: this.getCurrentUser()
    });
  }
  
  // Supprimer un ticket
  async deleteTicket(id: number): Promise<void> {
    await this.pool.query(`DELETE FROM ${this.prefix}debug_logger_tags WHERE report_id = ?`, [id]);
    await this.pool.query(`DELETE FROM ${this.prefix}debug_logger_history WHERE report_id = ?`, [id]);
    await this.pool.query(`DELETE FROM ${this.prefix}debug_report WHERE id = ?`, [id]);
  }
  
  // Historique d'un ticket
  async getHistory(reportId: number): Promise<HistoryEntry[]> {
    const [rows] = await this.pool.query(
      `SELECT * FROM ${this.prefix}debug_logger_history 
       WHERE report_id = ? 
       ORDER BY changed_at DESC`,
      [reportId]
    );
    return rows as HistoryEntry[];
  }
  
  // Ajouter une entrée d'historique
  async addHistory(entry: Partial<HistoryEntry>): Promise<void> {
    await this.pool.query(
      `INSERT INTO ${this.prefix}debug_logger_history SET ?`,
      [entry]
    );
  }
  
  // Stats rapides
  async getStats(): Promise<TicketStats> {
    const [rows] = await this.pool.query(`
      SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as open,
        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as closed,
        SUM(CASE WHEN severity = 'bug' THEN 1 ELSE 0 END) as bugs,
        SUM(CASE WHEN severity = 'warning' THEN 1 ELSE 0 END) as warnings,
        SUM(CASE WHEN severity = 'info' THEN 1 ELSE 0 END) as infos
      FROM ${this.prefix}debug_report
    `);
    return (rows as any[])[0];
  }
  
  // Username VS Code actuel
  private getCurrentUser(): string {
    return process.env.USER || process.env.USERNAME || 'vscode-user';
  }
}
```

---

### 9. Interfaces TypeScript (fichier `models.ts`)

```typescript
export interface DatabaseConfig {
  host: string;
  port: number;
  database: string;
  user: string;
  password: string;
  prefix: string;
}

export interface DebugReport {
  id: number;
  url: string;
  console_log: string | null;
  network_log: string | null;
  screenshot: string | null;
  comment: string;
  resolution: string | null;
  admin_user: string;
  assigned_to: number | null;
  assigned_username?: string;
  severity: 'bug' | 'warning' | 'info';
  source: 'admin' | 'catalog';
  status: number; // 0 = open, 1 = closed
  date_added: string;
  tags?: string; // Comma-separated (from GROUP_CONCAT)
}

export interface TicketFilters {
  status?: number;
  severity?: 'bug' | 'warning' | 'info';
  source?: 'admin' | 'catalog';
  assignedTo?: number;
  search?: string;
  limit?: number;
  offset?: number;
}

export interface HistoryEntry {
  history_id?: number;
  report_id: number;
  action: string;
  field_changed?: string;
  old_value?: string;
  new_value?: string;
  changed_by: string;
  changed_at?: string;
}

export interface TicketStats {
  total: number;
  open: number;
  closed: number;
  bugs: number;
  warnings: number;
  infos: number;
}
```

---

## Checklist de développement

### Phase 1 : Infrastructure (Jour 1-2)
- [ ] Initialiser projet VS Code extension avec `yo code`
- [ ] Installer dépendances : `mysql2`, `@types/vscode`
- [ ] Créer structure de dossiers
- [ ] Implémenter `DatabaseConnection` class
- [ ] Implémenter `DebugLoggerQueries` class
- [ ] Créer vue Settings avec formulaire de connexion
- [ ] Tester connexion DB avec stockage sécurisé du password

### Phase 2 : Affichage tickets (Jour 3-4)
- [ ] Créer `TicketProvider` pour TreeView
- [ ] Implémenter requête `getTickets()` avec filtres
- [ ] Créer webview liste avec tableau HTML
- [ ] Ajouter filtres : status, severity, source, search
- [ ] Ajouter pagination (50 par page)
- [ ] Tester affichage avec 100+ tickets

### Phase 3 : Détails et édition (Jour 5-6)
- [ ] Créer webview détail ticket
- [ ] Afficher toutes les sections (URL, comment, logs, screenshot)
- [ ] Implémenter édition inline (comment, resolution)
- [ ] Boutons actions : Close, Reopen, Delete, Assign
- [ ] Gestion des tags (ajouter/retirer)
- [ ] Tester modifications DB

### Phase 4 : Intégration Copilot (Jour 7)
- [ ] Implémenter `CopilotProvider` class
- [ ] Créer fonction `buildPrompt()` structurée
- [ ] Bouton "Send to Copilot" dans vue détail
- [ ] Tester injection dans Copilot Chat
- [ ] (Optionnel) Créer participant `@debuglogger`

### Phase 5 : Historique (Jour 8)
- [ ] Créer table SQL `debug_logger_history`
- [ ] Implémenter fonction `addHistory()`
- [ ] Wrapper toutes les UPDATE avec logging
- [ ] Afficher timeline dans vue détail
- [ ] Tester avec modifications multiples

### Phase 6 : Workflow développeur (Jour 9)
- [ ] Fonction auto-détection fichiers concernés
- [ ] Bouton "Open Related Files"
- [ ] Menu contextuel click-droit
- [ ] Actions rapides depuis TreeView
- [ ] Status bar item avec compteur tickets

### Phase 7 : Polish et tests (Jour 10)
- [ ] CSS responsive et compatible dark theme
- [ ] Gestion erreurs réseau/DB
- [ ] Messages toast pour confirmations
- [ ] Icônes et badges dans TreeView
- [ ] Documentation README.md
- [ ] Package extension (.vsix)

---

## Package.json complet

```json
{
  "name": "debug-logger-manager",
  "displayName": "Debug Logger Manager",
  "description": "Manage OpenCart Debug Logger tickets directly in VS Code",
  "version": "1.0.0",
  "publisher": "phoenixliquidation",
  "engines": {
    "vscode": "^1.85.0"
  },
  "categories": [
    "Other"
  ],
  "activationEvents": [
    "onStartupFinished"
  ],
  "main": "./out/extension.js",
  "contributes": {
    "commands": [
      {
        "command": "debugLogger.configure",
        "title": "Debug Logger: Configure Database",
        "icon": "$(gear)"
      },
      {
        "command": "debugLogger.showTickets",
        "title": "Debug Logger: Show All Tickets",
        "icon": "$(list-unordered)"
      },
      {
        "command": "debugLogger.showTicket",
        "title": "Debug Logger: Show Ticket Details"
      },
      {
        "command": "debugLogger.sendToCopilot",
        "title": "Debug Logger: Send to Copilot",
        "icon": "$(robot)"
      },
      {
        "command": "debugLogger.assignToMe",
        "title": "Debug Logger: Assign to Me",
        "icon": "$(person)"
      },
      {
        "command": "debugLogger.closeTicket",
        "title": "Debug Logger: Close Ticket",
        "icon": "$(check)"
      },
      {
        "command": "debugLogger.reopenTicket",
        "title": "Debug Logger: Reopen Ticket"
      },
      {
        "command": "debugLogger.deleteTicket",
        "title": "Debug Logger: Delete Ticket",
        "icon": "$(trash)"
      },
      {
        "command": "debugLogger.refresh",
        "title": "Debug Logger: Refresh",
        "icon": "$(refresh)"
      },
      {
        "command": "debugLogger.openRelatedFiles",
        "title": "Debug Logger: Open Related Files"
      }
    ],
    "viewsContainers": {
      "activitybar": [
        {
          "id": "debug-logger",
          "title": "Debug Logger",
          "icon": "resources/icon.svg"
        }
      ]
    },
    "views": {
      "debug-logger": [
        {
          "id": "ticketList",
          "name": "Tickets",
          "icon": "resources/icon.svg"
        }
      ]
    },
    "menus": {
      "view/title": [
        {
          "command": "debugLogger.refresh",
          "when": "view == ticketList",
          "group": "navigation"
        },
        {
          "command": "debugLogger.configure",
          "when": "view == ticketList",
          "group": "navigation"
        }
      ],
      "view/item/context": [
        {
          "command": "debugLogger.sendToCopilot",
          "when": "view == ticketList",
          "group": "inline@1"
        },
        {
          "command": "debugLogger.assignToMe",
          "when": "view == ticketList",
          "group": "1_actions@1"
        },
        {
          "command": "debugLogger.closeTicket",
          "when": "view == ticketList && viewItem =~ /open/",
          "group": "1_actions@2"
        },
        {
          "command": "debugLogger.reopenTicket",
          "when": "view == ticketList && viewItem =~ /closed/",
          "group": "1_actions@2"
        },
        {
          "command": "debugLogger.deleteTicket",
          "when": "view == ticketList",
          "group": "2_danger@1"
        }
      ]
    }
  },
  "scripts": {
    "vscode:prepublish": "npm run compile",
    "compile": "tsc -p ./",
    "watch": "tsc -watch -p ./",
    "pretest": "npm run compile && npm run lint",
    "lint": "eslint src --ext ts"
  },
  "devDependencies": {
    "@types/node": "^20.x",
    "@types/vscode": "^1.85.0",
    "@typescript-eslint/eslint-plugin": "^6.x",
    "@typescript-eslint/parser": "^6.x",
    "eslint": "^8.x",
    "typescript": "^5.x"
  },
  "dependencies": {
    "mysql2": "^3.6.0"
  }
}
```

---

## Prochaines étapes après MVP

### Améliorations futures

1. **Export rapports** : Bouton pour exporter en CSV/JSON depuis VS Code
2. **Notifications** : Toast VS Code quand un nouveau ticket est créé (polling DB toutes les 30s)
3. **Graphiques stats** : Webview Analytics avec Chart.js intégré
4. **Snippets de code** : Créer des snippets pour les patterns de fix courants
5. **Multi-workspace** : Supporter plusieurs DB OpenCart (dev, staging, prod)
6. **GitHub Issues** : Bouton pour créer un GitHub Issue lié au ticket
7. **AI Summary** : Utiliser Copilot pour résumer automatiquement le ticket
8. **Tests unitaires** : Mocha + chai pour tester les requêtes SQL

---

## Notes importantes

### Sécurité
- **Passwords** : TOUJOURS utiliser `context.secrets` pour stocker le password DB
- **SQL Injection** : Utiliser les prepared statements de `mysql2` (déjà fait dans les exemples)
- **HTTPS** : Si DB distante, forcer SSL/TLS

### Performance
- **Connection pooling** : Le `mysql2` pool gère automatiquement les connexions
- **Lazy loading** : Charger les screenshots seulement quand la vue détail s'ouvre
- **Cache** : Mettre en cache les stats pour 5 minutes

### UX
- **Loading states** : Afficher spinners pendant les requêtes DB
- **Error handling** : Messages clairs si DB déconnectée ou requête échoue
- **Keyboard shortcuts** : Ajouter raccourcis clavier pour actions courantes

---

**Fin du workload**

Ce document contient toutes les spécifications pour développer l'extension VS Code Debug Logger Manager. Copilot peut maintenant générer le code complet en suivant cette structure.
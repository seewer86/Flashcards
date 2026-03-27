# Flashcards – PHP flashcards with local login & LDAP

English version · Deutsche Version weiter unten

This application is a simple web‑based flashcard app with an admin area, user management, and optional LDAP authentication.  
It is optimized for smartphones and can be installed as a **Progressive Web App (PWA)**, so users can add it to their home screen and use it like a native app.  
It runs as a PHP/Apache container with MySQL/MariaDB and is well suited for small schools, companies, or private learning environments.

---

## Features

- Study with simple question/answer flashcards
- Mobile‑friendly responsive UI
- Installable as a PWA (add to home screen, full‑screen usage)
- Categories for cards
- Progress reset per user
- Admin area for:
  - Creating/editing categories and cards
  - User management (only for **local** admin users)
- Two auth modes:
  - **Local**: Users stored in a MySQL table, password hashed via `password_hash`
  - **LDAP / LDAPS**: Login against a directory service, users are mirrored locally
- Multilingual setup (DE/EN/FR)
- Setup wizard with:
  - DB configuration
  - Auth mode
  - LDAP settings including a “Test LDAP” button

---

## System requirements

- Docker / Docker Compose
- Optional: OpenLDAP / Active Directory for LDAP login

---

## Installation (with Docker)

1. Clone the repository:

   ```bash
   git clone git@github.com:seewer86/Flashcards.git
   cd Flashcards
   ```

2. Adjust DB passwords in the docker compose file:

   ```bash
   nano docker-compose.yml
   ```

3. Start the containers:

   ```bash
   docker compose up -d --build
   ```

4. The setup wizard opens on the first visit.

5. Go through the setup:

   - **Database**
     - Enter host, DB name, user, password
   - **App language**
     - Choose default language (DE/EN/FR)
   - **Authentication**
     - Select `Local` or `LDAP`
   - **Initial admin** (local only)
     - Username + password of the first admin account
   - **LDAP** (LDAP only)
     - Scheme: `ldap://` or `ldaps://`
     - Host: e.g. `192.168.1.100`
     - Base DN: e.g. `ou=users,dc=example,dc=com`
     - Group DN: e.g. `cn=flashcards,ou=groups,dc=example,dc=com`
     - Bind DN: service account (e.g. `cn=admin,dc=example,dc=com`)
     - Bind password: password of the service account
     - Use the **“Test LDAP”** button to verify connection and user count

6. After saving you will be redirected to the login page.

---

## Login & roles

- **Local mode**
  - The admin defined during setup can log in immediately.
  - More users/passwords can be created in the admin area.
  - Only local users with `is_admin = 1` see the **user management**.
- **LDAP mode**
  - Users log in with their LDAP account and password.
  - On successful login a local record without password is created if needed.
  - LDAP users do **not** see a link to user management.

---

## Configuration

After setup, `config/config.php` is generated.  
This file contains among other things:

- App name & default language
- Auth mode (`local` or `ldap`)
- LDAP settings (`host`, `basedn`, `group_dn`, `bind_dn`, `bind_pass`)
- Database connection parameters

---

## Security notes

- Prefer **LDAPS** for LDAP.
- Keep `config/config.php` out of version control.
- Protect the setup URL once installation is finished (e.g. remove the setup directory or block it via web server).
- Use long, random passwords for:
  - DB user
  - LDAP service account
  - Local admin users

---

# Flashcards – PHP Lernkarten mit lokalem Login & LDAP

Diese Anwendung ist eine einfache, webbasierte Lernkarten‑App mit Admin‑Bereich, Benutzerverwaltung und optionaler LDAP‑Authentifizierung.  
Sie ist für Smartphones optimiert und kann als **Progressive Web App (PWA)** installiert werden, sodass Nutzer sie zum Homescreen hinzufügen und wie eine native App im Vollbild-Modus verwenden können.  
Sie läuft als PHP‑/Apache‑Container mit MySQL/MariaDB und eignet sich gut für kleine Schulen, Firmen oder private Lernumgebungen.

---

## Features

- Lernen mit einfachen Frage/Antwort‑Karteikarten
- Mobile‑optimiertes, responsives UI
- Als PWA installierbar (zum Homescreen hinzufügen, Vollbild‑Nutzung)
- Kategorien für Karten
- Fortschritts‑Reset pro Benutzer
- Admin‑Bereich für:
  - Anlegen/Bearbeiten von Kategorien und Karten
  - Benutzerverwaltung (nur für **lokale** Admin‑Benutzer)
- Zwei Auth‑Modi:
  - **Local**: Benutzer in MySQL‑Tabelle, Passwort mit `password_hash`
  - **LDAP / LDAPS**: Login gegen Verzeichnisdienst, Benutzer werden lokal gespiegelt
- Mehrsprachiges Setup (DE/EN/FR)
- Setup‑Wizard mit:
  - DB‑Konfiguration
  - Auth‑Modus
  - LDAP‑Einstellungen inkl. „LDAP testen“‑Button

---

## Systemanforderungen

- Docker / Docker Compose
- Optional: OpenLDAP / Active Directory für LDAP‑Login

---

## Installation (mit Docker)

1. Repository klonen:

   ```bash
   git clone git@github.com:seewer86/Flashcards.git
   cd Flashcards
   ```

2. DB‑Passwörter im Docker Compose anpassen:

   ```bash
   nano docker-compose.yml
   ```

3. Container starten:

   ```bash
   docker compose up -d --build
   ```

4. Setup‑Wizard öffnet sich beim ersten Aufrufen der Seite.

5. Setup durchlaufen:

   - **Database**
     - Host, DB‑Name, User, Passwort angeben
   - **App language**
     - Standardsprache wählen (DE/EN/FR)
   - **Authentication**
     - `Local` oder `LDAP` auswählen
   - **Initial admin** (nur bei Local)
     - Benutzername + Passwort des ersten Admin‑Accounts
   - **LDAP** (nur bei LDAP)
     - Schema: `ldap://` oder `ldaps://`
     - Host: z.B. `192.168.1.100`
     - Base DN: z.B. `ou=users,dc=example,dc=com`
     - Group DN: z.B. `cn=flashcards,ou=groups,dc=example,dc=com`
     - Bind DN: Service‑Account (z.B. `cn=admin,dc=example,dc=com`)
     - Bind Password: Passwort des Service‑Accounts
     - Button **„LDAP testen“** verwenden, um Verbindung + Benutzeranzahl zu prüfen

6. Nach dem Speichern wirst du auf die Login‑Seite weitergeleitet.

---

## Login & Rollen

- **Lokaler Modus**
  - Beim Setup definierter Admin kann sich sofort anmelden.
  - Im Admin‑Bereich können weitere Benutzer/Passwörter angelegt werden.
  - Nur lokale Benutzer mit `is_admin = 1` sehen die **Benutzerverwaltung**.
- **LDAP‑Modus**
  - Benutzer melden sich mit LDAP‑Account und Passwort an.
  - Bei erfolgreichem Login wird (falls nötig) ein lokaler Datensatz ohne Passwort angelegt.
  - LDAP‑Benutzer sehen **keinen** Link zur Benutzerverwaltung.

---

## Konfiguration

Nach dem Setup wird `config/config.php` erzeugt.  
Diese Datei enthält u.a.:

- App‑Name & Default‑Sprache
- Auth‑Modus (`local` oder `ldap`)
- LDAP‑Einstellungen (`host`, `basedn`, `group_dn`, `bind_dn`, `bind_pass`)
- Datenbank‑Verbindungsdaten

---

## Sicherheitshinweise

- Verwende für LDAP nach Möglichkeit **LDAPS**.
- Halte `config/config.php` außerhalb der Versionsverwaltung.
- Schütze die Setup‑URL, sobald die Installation abgeschlossen ist (z.B. Setup‑Verzeichnis entfernen oder per Webserver sperren).
- Setze lange, zufällige Passwörter für:
  - DB‑User
  - LDAP‑Service‑Account
  - lokale Admin‑Benutzer

---

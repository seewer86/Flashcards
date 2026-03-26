# Flashcards – PHP Lernkarten mit lokalem Login & LDAP

Diese Anwendung ist eine einfache, webbasierte Lernkarten‑App mit Admin‑Bereich, Benutzerverwaltung und optionaler LDAP‑Authentifizierung.  
Sie läuft als PHP‑/Apache‑Container mit MySQL/MariaDB und eignet sich gut für kleine Schulen, Firmen oder private Lernumgebungen.

---

## Features

- Lernen mit einfachen Frage/Antwort‑Karteikarten
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
2. DB Passwörter im docker compose anpassen:
    ```bash
   nano docker-compose.yml
   ```
  
3. Container starten:

   ```bash
   docker compose up -d --build
   ```

4. Setup‑Wizard öffnet sich beim ersten aufrufen der Seite.

5. Setup durchlaufen:

   - **Database**
     - Host, DB‑Name, User, Passwort angeben
   - **App language**
     - Standardsprache wählen (DE/EN/FR)
   - **Authentication**
     - `Local` oder `LDAP` auswählen
   - **Initial admin (local)** (nur bei Local)
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

## Lizenz

MIT

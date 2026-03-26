<?php
$appName = 'Karteikarten';

$availableLocales = ['de', 'en', 'fr'];

// Sprache aus ?lang=
$requested = $_GET['lang'] ?? 'de';
if (!in_array($requested, $availableLocales, true)) {
    $requested = 'de';
}
$currentLocale = $requested;

$t = [
    'de' => [
        'setup_title'          => 'Setup',
        'db_section'           => 'Datenbank',
        'db_host'              => 'Host',
        'db_name'              => 'Datenbank',
        'db_user'              => 'User',
        'db_pass'              => 'Passwort',
        'app_language'         => 'App-Sprache',
        'lang_de'              => 'Deutsch',
        'lang_fr'              => 'Französisch',
        'lang_en'              => 'Englisch',
        'auth_section'         => 'Authentifizierung',
        'auth_local'           => 'Lokal',
        'auth_ldap'            => 'LDAP',
        'auth_hint'            => 'Im lokalen Modus wird ein erster Admin-Benutzer angelegt. Im LDAP-Modus erfolgt der Login über den Verzeichnisdienst.',
        'local_admin_section'  => 'Initialer Admin (lokal)',
        'username'             => 'Benutzername',
        'password'             => 'Passwort',
        'local_admin_hint'     => 'Dieser Benutzer wird mit Admin-Rechten in der lokalen Benutzerverwaltung angelegt.',
        'ldap_section'         => 'LDAP',
        'ldap_tested'          => 'Getestet mit OpenLDAP.',
        'ldap_host'            => 'Host',
        'ldap_basedn'          => 'Base DN',
        'ldap_group_dn'        => 'Group DN',
        'ldap_bind_dn'         => 'Bind DN',
        'ldap_bind_pass'       => 'Bind Password',
        'save'                 => 'Speichern',
        'language_label'       => 'Sprache',

        // LDAP-Test
        'ldap_test_button'     => 'LDAP testen',
        'ldap_test_fill'       => 'Bitte Host, Base DN, Bind DN und Passwort ausfüllen.',
        'ldap_test_connect_fail'=> 'Verbindung (ldap_connect) fehlgeschlagen.',
        'ldap_test_bind_fail'  => 'Bind fehlgeschlagen: ',
        'ldap_test_search_fail'=> 'Suche fehlgeschlagen: ',
        'ldap_test_ok'         => 'Verbindung ok, Benutzer gefunden: ',
        'ldap_test_error'      => 'Fehler beim Test.',
        'ldap_test_running'    => 'Teste...',
    ],
    'en' => [
        'setup_title'          => 'Setup',
        'db_section'           => 'Database',
        'db_host'              => 'Host',
        'db_name'              => 'Database',
        'db_user'              => 'User',
        'db_pass'              => 'Password',
        'app_language'         => 'App language',
        'lang_de'              => 'German',
        'lang_fr'              => 'French',
        'lang_en'              => 'English',
        'auth_section'         => 'Authentication',
        'auth_local'           => 'Local',
        'auth_ldap'            => 'LDAP',
        'auth_hint'            => 'In local mode an initial admin user is created. In LDAP mode logins are done via the directory service.',
        'local_admin_section'  => 'Initial admin (local)',
        'username'             => 'Username',
        'password'             => 'Password',
        'local_admin_hint'     => 'This user will be created with admin rights in the local user management.',
        'ldap_section'         => 'LDAP',
        'ldap_tested'          => 'Tested with OpenLDAP.',
        'ldap_host'            => 'Host',
        'ldap_basedn'          => 'Base DN',
        'ldap_group_dn'        => 'Group DN',
        'ldap_bind_dn'         => 'Bind DN',
        'ldap_bind_pass'       => 'Bind password',
        'save'                 => 'Save',
        'language_label'       => 'Language',

        'ldap_test_button'     => 'Test LDAP',
        'ldap_test_fill'       => 'Please fill host, base DN, bind DN and password.',
        'ldap_test_connect_fail'=> 'Connection (ldap_connect) failed.',
        'ldap_test_bind_fail'  => 'Bind failed: ',
        'ldap_test_search_fail'=> 'Search failed: ',
        'ldap_test_ok'         => 'Connection ok, users found: ',
        'ldap_test_error'      => 'Error while testing.',
        'ldap_test_running'    => 'Testing...',
    ],
    'fr' => [
        'setup_title'          => 'Configuration',
        'db_section'           => 'Base de données',
        'db_host'              => 'Hôte',
        'db_name'              => 'Base de données',
        'db_user'              => 'Utilisateur',
        'db_pass'              => 'Mot de passe',
        'app_language'         => 'Langue de l’application',
        'lang_de'              => 'Allemand',
        'lang_fr'              => 'Français',
        'lang_en'              => 'Anglais',
        'auth_section'         => 'Authentification',
        'auth_local'           => 'Local',
        'auth_ldap'            => 'LDAP',
        'auth_hint'            => 'En mode local, un premier utilisateur administrateur est créé. En mode LDAP, la connexion se fait via l’annuaire.',
        'local_admin_section'  => 'Administrateur initial (local)',
        'username'             => 'Nom d’utilisateur',
        'password'             => 'Mot de passe',
        'local_admin_hint'     => 'Cet utilisateur sera créé avec les droits administrateur dans la gestion locale des utilisateurs.',
        'ldap_section'         => 'LDAP',
        'ldap_tested'          => 'Testé avec OpenLDAP.',
        'ldap_host'            => 'Hôte',
        'ldap_basedn'          => 'Base DN',
        'ldap_group_dn'        => 'Group DN',
        'ldap_bind_dn'         => 'Bind DN',
        'ldap_bind_pass'       => 'Mot de passe de liaison',
        'save'                 => 'Enregistrer',
        'language_label'       => 'Langue',

        'ldap_test_button'     => 'Tester LDAP',
        'ldap_test_fill'       => 'Veuillez renseigner l’hôte, le Base DN, le Bind DN et le mot de passe.',
        'ldap_test_connect_fail'=> 'Connexion (ldap_connect) échouée.',
        'ldap_test_bind_fail'  => 'Échec du bind : ',
        'ldap_test_search_fail'=> 'Échec de la recherche : ',
        'ldap_test_ok'         => 'Connexion ok, utilisateurs trouvés : ',
        'ldap_test_error'      => 'Erreur lors du test.',
        'ldap_test_running'    => 'Test en cours...',
    ],
];

$tr = $t[$currentLocale] ?? $t['de'];

// Form-Defaults (kein config-Laden hier)
$appNameValue   = $appName;
$defaultLocale  = $currentLocale;
$authMode       = 'local';

$ldapScheme     = 'ldap';
$ldapHost       = '';
$ldapBasedn     = '';
$ldapGroupDn    = '';
$ldapBindDn     = '';
$ldapBindPass   = '';

$dbHost         = '';
$dbName         = '';
$dbUser         = '';
$dbPass         = '';
?>
<!doctype html>
<html lang="<?= htmlspecialchars($currentLocale) ?>">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($appName) ?> <?= htmlspecialchars($tr['setup_title']) ?></title>
  <style>
      body {
          font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
          background: #f5f5f7;
          margin: 0;
          display: flex;
          align-items: center;
          justify-content: center;
          min-height: 100vh;
      }
      .card {
          background: #fff;
          padding: 32px 40px;
          border-radius: 12px;
          box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
          max-width: 520px;
          width: 100%;
      }
      h1 {
          margin-top: 0;
          margin-bottom: 24px;
          font-size: 26px;
      }
      h2 {
          margin: 24px 0 8px;
          font-size: 16px;
      }
      label {
          display: block;
          font-size: 14px;
          margin-bottom: 6px;
      }
      input[type="text"],
      input[type="password"],
      select {
          width: 100%;
          box-sizing: border-box;
          padding: 8px 10px;
          border-radius: 6px;
          border: 1px solid #d1d5db;
          font-size: 14px;
      }
      .radio-row {
          margin-top: 6px;
          margin-bottom: 6px;
      }
      .radio-row label {
          display: inline-block;
          margin-right: 16px;
      }
      .hint {
          font-size: 12px;
          color: #4b5563;
          margin-top: 4px;
      }
      .section {
          margin-top: 12px;
      }
      .actions {
          margin-top: 24px;
      }
      button {
          padding: 10px 14px;
          border-radius: 8px;
          border: none;
          background: #e5e7eb;
          font-size: 15px;
          cursor: pointer;
      }
      button:hover {
          background: #d1d5db;
      }
      .hidden {
          display: none;
      }
      .lang-switch {
          font-size: 12px;
          text-align: right;
          margin-bottom: 8px;
      }
      .lang-switch a {
          color: #2563eb;
          text-decoration: none;
          margin-left: 6px;
      }
      .lang-switch a.active {
          font-weight: 600;
          text-decoration: underline;
      }
      .logo {
          text-align:center;
          margin-bottom:0.75rem;
      }
      .logo img {
          max-height:40px;
          width:auto;
      }
      #btn-ldap-test {
          margin-top:.4rem;
      }
  </style>
</head>
<body>
<div class="card">
  <div class="logo">
    <img src="../public/logo.png" alt="Karteikarten Logo">
  </div>
  <div class="lang-switch">
      <?= htmlspecialchars($tr['language_label']) ?>:
      <?php foreach ($availableLocales as $loc): ?>
          <a href="?lang=<?= $loc ?>" class="<?= $loc === $currentLocale ? 'active' : '' ?>">
              <?= strtoupper($loc) ?>
          </a>
      <?php endforeach; ?>
  </div>

  <h1><?= htmlspecialchars($appName) ?> <?= htmlspecialchars($tr['setup_title']) ?></h1>

  <form method="post" action="save.php" id="setup-form">
      <!-- Datenbank -->
      <h2><?= htmlspecialchars($tr['db_section']) ?></h2>
      <div class="section">
          <label><?= htmlspecialchars($tr['db_host']) ?>
              <input type="text" name="db_host" required>
          </label>
          <label><?= htmlspecialchars($tr['db_name']) ?>
              <input type="text" name="db_name" required>
          </label>
          <label><?= htmlspecialchars($tr['db_user']) ?>
              <input type="text" name="db_user" required>
          </label>
          <label><?= htmlspecialchars($tr['db_pass']) ?>
              <input type="password" name="db_pass" required>
          </label>
      </div>

      <!-- App -->
      <h2><?= htmlspecialchars($tr['app_language']) ?></h2>
      <div class="section">
          <select name="default_locale">
              <option value="de"<?= $defaultLocale === 'de' ? ' selected' : '' ?>><?= htmlspecialchars($tr['lang_de']) ?></option>
              <option value="fr"<?= $defaultLocale === 'fr' ? ' selected' : '' ?>><?= htmlspecialchars($tr['lang_fr']) ?></option>
              <option value="en"<?= $defaultLocale === 'en' ? ' selected' : '' ?>><?= htmlspecialchars($tr['lang_en']) ?></option>
          </select>
      </div>

      <!-- Auth -->
      <h2><?= htmlspecialchars($tr['auth_section']) ?></h2>
      <div class="section">
          <div class="radio-row">
              <label>
                  <input type="radio" name="auth_mode" value="local" checked>
                  <?= htmlspecialchars($tr['auth_local']) ?>
              </label>
              <label>
                  <input type="radio" name="auth_mode" value="ldap">
                  <?= htmlspecialchars($tr['auth_ldap']) ?>
              </label>
          </div>
          <div class="hint">
              <?= htmlspecialchars($tr['auth_hint']) ?>
          </div>
      </div>

      <!-- Lokaler Initial-Admin -->
      <div id="local-section" class="section">
          <h2><?= htmlspecialchars($tr['local_admin_section']) ?></h2>
          <label><?= htmlspecialchars($tr['username']) ?>
              <input type="text" name="admin_username">
          </label>
          <label><?= htmlspecialchars($tr['password']) ?>
              <input type="password" name="admin_password">
          </label>
          <div class="hint">
              <?= htmlspecialchars($tr['local_admin_hint']) ?>
          </div>
      </div>

      <!-- LDAP -->
      <div id="ldap-section" class="section hidden">
          <h2><?= htmlspecialchars($tr['ldap_section']) ?></h2>
          <div class="hint"><?= htmlspecialchars($tr['ldap_tested']) ?></div>

          <label><?= htmlspecialchars($tr['ldap_host']) ?>
              <div style="display:flex; gap:.5rem;">
                  <select name="ldap_scheme" style="flex:0 0 110px;">
                      <option value="ldap" <?= ($ldapScheme ?? 'ldap') === 'ldap' ? 'selected' : '' ?>>ldap://</option>
                      <option value="ldaps" <?= ($ldapScheme ?? '') === 'ldaps' ? 'selected' : '' ?>>ldaps://</option>
                  </select>
                  <input type="text" name="ldap_host"
                         value="<?= htmlspecialchars($ldapHost ?? '') ?>"
                         style="flex:1; min-width:0;">
              </div>
          </label>

          <label><?= htmlspecialchars($tr['ldap_basedn']) ?>
              <input type="text" name="ldap_basedn"
                     value="<?= htmlspecialchars($ldapBasedn ?? '') ?>">
          </label>
          <label><?= htmlspecialchars($tr['ldap_group_dn']) ?>
              <input type="text" name="ldap_group_dn"
                     value="<?= htmlspecialchars($ldapGroupDn ?? '') ?>">
          </label>
          <label><?= htmlspecialchars($tr['ldap_bind_dn']) ?>
              <input type="text" name="ldap_bind_dn"
                     value="<?= htmlspecialchars($ldapBindDn ?? '') ?>">
          </label>
          <label><?= htmlspecialchars($tr['ldap_bind_pass']) ?>
              <input type="password" name="ldap_bind_pass"
                     value="<?= htmlspecialchars($ldapBindPass ?? '') ?>">
          </label>

          <div style="margin-top:.5rem;">
              <button type="button" id="btn-ldap-test">
                  <?= htmlspecialchars($tr['ldap_test_button']) ?>
              </button>
              <span id="ldap-test-result" style="margin-left:.5rem; font-size:13px;"></span>
          </div>
      </div>

      <div class="actions">
          <button type="submit"><?= htmlspecialchars($tr['save']) ?></button>
      </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const localRadio = document.querySelector('input[name="auth_mode"][value="local"]');
  const ldapRadio  = document.querySelector('input[name="auth_mode"][value="ldap"]');
  const localSec   = document.getElementById('local-section');
  const ldapSec    = document.getElementById('ldap-section');

  function updateVisibility() {
      if (localRadio.checked) {
          localSec.classList.remove('hidden');
          ldapSec.classList.add('hidden');
      } else {
          localSec.classList.add('hidden');
          ldapSec.classList.remove('hidden');
      }
  }

  localRadio.addEventListener('change', updateVisibility);
  ldapRadio.addEventListener('change', updateVisibility);
  updateVisibility();

  // LDAP-Test
  var btnTest = document.getElementById('btn-ldap-test');
  if (btnTest) {
      btnTest.addEventListener('click', function () {
          var form  = document.getElementById('setup-form');
          var data  = new FormData(form);
          var outEl = document.getElementById('ldap-test-result');
          outEl.style.color = '';
          outEl.textContent = <?= json_encode($tr['ldap_test_running']) ?>;

          fetch('test_ldap.php?lang=<?= $currentLocale ?>', {
              method: 'POST',
              body: data
          })
          .then(r => r.json())
          .then(function (res) {
              outEl.style.color = res.ok ? 'green' : 'red';
              outEl.textContent = res.message;
          })
          .catch(function () {
              outEl.style.color = 'red';
              outEl.textContent = <?= json_encode($tr['ldap_test_error']) ?>;
          });
      });
  }
});
</script>
</body>
</html>

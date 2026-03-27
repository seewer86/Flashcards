<?php
// learn.php
require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/i18n.php';
require_login();
$user = current_user();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLocale) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <link rel="apple-touch-icon" href="../public/icons/apple-touch-icon.png">
  <title><?= htmlspecialchars(t('app_title')) ?></title>
  <style>
    body { font-family: system-ui, sans-serif; margin:0; padding:1rem; background:#f5f5f5; }
    #app { max-width:600px; margin:0 auto; }
    .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem; font-size:14px; }
    .topbar a { color:#1976d2; text-decoration:none; margin-left:.5rem; }
    .topbar a:hover { text-decoration:underline; }
    .question { font-size:1.2rem; margin-bottom:0.5rem; }
    #progress { font-size:0.9rem; color:#555; margin-bottom:0.5rem; }
    .options { display:flex; flex-direction:column; gap:.5rem; }
    button.option {
      padding:.75rem 1rem;
      font-size:1rem;
      border-radius:8px;
      border:1px solid #ccc;
      background:#fff;
      cursor:pointer;
    }
    button.option.correct { background:#c8f7c5; }
    button.option.wrong { background:#f7c5c5; }
    #status { margin-top:1rem; }
    #categoryBar { margin-bottom:0.5rem; }
    #categoryBar label { font-size:0.9rem; }
    .logo { text-align: center; margin-bottom: 0.75rem; }
    .logo img { max-height: 60px; width: auto; }
  </style>
</head>
<body>
<body>
<div class="logo">
  <img src="logo.png" alt="Karteikarten Logo">
</div>
<div id="app">
  <div class="topbar">
    <span>
      <?= htmlspecialchars(t('app_title')) ?> Login: <?= htmlspecialchars($user['display_name']) ?>
    </span>
    <span>
      <a href="learn.php?lang=de">DE</a>
      <a href="learn.php?lang=en">EN</a>
      <a href="learn.php?lang=fr">FR</a> |
      <a href="admin.php"><?= htmlspecialchars(t('admin')) ?></a> |
      <a href="logout.php"><?= htmlspecialchars(t('logout')) ?></a>
    </span>
  </div>

  <div id="categoryBar">
    <label>
      <?= htmlspecialchars(t('category')) ?>:
      <select id="categorySelect"></select>
    </label>
  </div>

  <div id="content">
    <p><?= htmlspecialchars(t('loading_question')) ?></p>
  </div>
</div>

<script>
let currentCard = null;
let awaitingAnswer = false;
let currentCategoryId = 0;

async function loadCategories() {
  const res = await fetch('api_categories.php');
  const data = await res.json();
  const select = document.getElementById('categorySelect');
  select.innerHTML = '';

  const optAll = document.createElement('option');
  optAll.value = '0';
  optAll.textContent = '<?= addslashes(t('category_all') ?? 'Alle') ?>';
  select.appendChild(optAll);

  if (data.success && data.categories) {
    data.categories.forEach(cat => {
      const opt = document.createElement('option');
      opt.value = cat.id;
      opt.textContent = cat.name;
      select.appendChild(opt);
    });
  }

  select.value = String(currentCategoryId);
  select.onchange = () => {
    currentCategoryId = parseInt(select.value, 10) || 0;
    loadNextQuestion();
  };
}

async function loadNextQuestion() {
  const content = document.getElementById('content');
  content.innerHTML = '<p><?= addslashes(t('loading_question')) ?></p>';
  awaitingAnswer = false;

  const url = 'api_next_question.php' + (currentCategoryId ? ('?category_id=' + currentCategoryId) : '');
  const res = await fetch(url);
  const data = await res.json();

  if (!data.success) {
    content.innerHTML = '<p><?= addslashes(t('load_error')) ?></p>';
    return;
  }

  if (data.finished) {
    let info = '<?= addslashes(t('finished_all')) ?>';
    if (data.stats) {
      const statsText = '<?= addslashes(t('finished_stats')) ?>'
        .replace('%done%', data.stats.done)
        .replace('%total%', data.stats.total);
      info += '<br>' + statsText;
    }
    content.innerHTML = `<p>${info}</p>`;
    return;
  }

  currentCard = data.card;
  const options = data.options;

  content.innerHTML = `
    <div id="progress"></div>
    <div class="question">${escapeHtml(currentCard.question)}</div>
    <div class="options" id="options"></div>
    <div id="status"></div>
    <div id="nextWrapper"></div>
  `;

  if (data.stats) {
    const p = document.getElementById('progress');
    const total = data.stats.total;
    const done = data.stats.done;
    const remaining = data.stats.remaining;
    const text = '<?= addslashes(t('remaining_stats')) ?>'
      .replace('%remaining%', remaining)
      .replace('%total%', total)
      .replace('%done%', done);
    p.textContent = text;
  }

  const optionsDiv = document.getElementById('options');
  options.forEach(opt => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'option';
    btn.textContent = opt;
    btn.onclick = () => submitAnswer(opt, btn);
    optionsDiv.appendChild(btn);
  });
  awaitingAnswer = true;
}

async function submitAnswer(selectedText, btn) {
  if (!awaitingAnswer || !currentCard) return;
  awaitingAnswer = false;

  const isCorrect = (selectedText === currentCard.correctAnswer);
  const res = await fetch('api_answer.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      card_id: currentCard.id,
      is_correct: isCorrect
    })
  });
  const data = await res.json();

  const status = document.getElementById('status');
  const buttons = document.querySelectorAll('button.option');
  const nextWrapper = document.getElementById('nextWrapper');
  nextWrapper.innerHTML = '';

  buttons.forEach(b => {
    if (b.textContent === currentCard.correctAnswer) {
      b.classList.add('correct');
    }
    b.disabled = true;
  });
  if (!isCorrect) {
    btn.classList.add('wrong');
  }

  if (data.correct) {
    const msg = '<?= addslashes(t('correct_short')) ?>'
      .replace('%count%', data.correctCount);
    status.textContent = msg;
    setTimeout(loadNextQuestion, 1200);
  } else {
    status.textContent = '<?= addslashes(t('wrong_long')) ?>';
    const nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.textContent = '<?= addslashes(t('next')) ?>';
    nextBtn.style.marginTop = '1rem';
    nextBtn.onclick = () => {
      nextBtn.disabled = true;
      loadNextQuestion();
    };
    nextWrapper.appendChild(nextBtn);
  }
}

function escapeHtml(str) {
  return str.replace(/[&<>\"']/g, function(m) {
    return ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    })[m];
  });
}

// Pull-to-refresh
(function () {
  let startY = 0;
  let pulling = false;

  function getScrollTop() {
    return document.scrollingElement ? document.scrollingElement.scrollTop : window.scrollY || 0;
  }

  window.addEventListener('touchstart', function (e) {
    if (getScrollTop() === 0) {
      startY = e.touches[0].clientY;
      pulling = true;
    }
  }, { passive: true });

  window.addEventListener('touchmove', function (e) {
    if (!pulling) return;
    const currentY = e.touches[0].clientY;
    const diff = currentY - startY;

    if (diff > 70 && getScrollTop() === 0) {
      pulling = false;
      location.reload();
    }
  }, { passive: true });

  window.addEventListener('touchend', function () {
    pulling = false;
  });
})();

loadCategories().then(loadNextQuestion);
</script>
</body>
</html>

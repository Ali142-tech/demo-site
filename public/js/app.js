(function () {
  'use strict';

  // ---- Mobile nav / sidebar --------------------------------------------
  var burger = document.getElementById('burger');
  var links = document.getElementById('nav-links');
  if (burger && links) {
    burger.addEventListener('click', function () {
      var open = links.classList.toggle('open');
      burger.setAttribute('aria-expanded', open);
    });
  }
  var side = document.getElementById('sidebar');
  var scrim = document.getElementById('scrim');
  document.querySelectorAll('[data-toggle-sidebar]').forEach(function (b) {
    b.addEventListener('click', function () {
      side.classList.toggle('open');
      scrim.classList.toggle('open');
    });
  });
  if (scrim) scrim.addEventListener('click', function () { side.classList.remove('open'); scrim.classList.remove('open'); });

  // ---- Reveal on scroll --------------------------------------------------
  var reveals = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
    }, { threshold: .12 });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('in'); });
  }

  // ---- Count-up numbers --------------------------------------------------
  document.querySelectorAll('[data-count]').forEach(function (el) {
    var target = parseInt(el.dataset.count, 10), suffix = el.dataset.suffix || '';
    var done = false;
    function run() {
      if (done) return; done = true;
      var start = null;
      (function tick(ts) {
        start = start || ts;
        var p = Math.min((ts - start) / 1400, 1);
        el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString() + suffix;
        if (p < 1) requestAnimationFrame(tick);
      })(performance.now());
    }
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (en, ob) { if (en[0].isIntersecting) { run(); ob.disconnect(); } }).observe(el);
    } else { run(); }
  });

  // ---- Marquee: duplicate content for a seamless loop -------------------
  var track = document.querySelector('.marquee-track');
  if (track) track.innerHTML += track.innerHTML;

  // ---- Quote request builder --------------------------------------------
  var builder = document.getElementById('items');
  if (builder) {
    var products = JSON.parse(document.getElementById('products-data').textContent);
    var tpl = document.getElementById('item-template').innerHTML;
    var preselect = builder.dataset.preselect;
    var oldItems = JSON.parse(builder.dataset.old || '[]');
    var counter = 0;

    function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }

    function addRow(data) {
      data = data || {};
      var i = counter++;
      var wrap = document.createElement('div');
      wrap.innerHTML = tpl.replace(/__I__/g, i).trim();
      var row = wrap.firstChild;
      builder.appendChild(row);

      var sel = row.querySelector('.product-select');
      var other = row.querySelector('.other-name');
      var qty = row.querySelector('.qty-input');
      var specs = row.querySelector('.specs-input');
      var chips = row.querySelector('.opt-chips-box');

      if (data.product_id) sel.value = data.product_id;
      if (data.name) other.value = data.name;
      if (data.quantity) qty.value = data.quantity;
      if (data.specs) specs.value = data.specs;

      function refresh(useMin) {
        var p = products.find(function (x) { return String(x.id) === sel.value; });
        other.closest('.field').style.display = p ? 'none' : '';
        chips.innerHTML = '';
        if (!p) { return; }
        if (useMin && (!qty.value || qty.value === '1')) qty.value = p.min_quantity;
        qty.min = 1;
        row.querySelector('.min-hint').textContent = 'Min. ' + p.min_quantity + ' ' + p.price_unit + 's recommended';
        Object.keys(p.options || {}).forEach(function (label) {
          var group = document.createElement('div'); group.className = 'opt-chips';
          group.innerHTML = '<small>' + esc(label) + ':</small>';
          p.options[label].forEach(function (choice) {
            var b = document.createElement('button'); b.type = 'button'; b.textContent = choice;
            if (specs.value.indexOf(label + ': ' + choice) !== -1) b.classList.add('on');
            b.addEventListener('click', function () {
              var line = label + ': ' + choice;
              var lines = specs.value.split('\n').filter(function (l) { return l && l.indexOf(label + ':') !== 0; });
              if (!b.classList.contains('on')) lines.push(line);
              specs.value = lines.join('\n');
              group.querySelectorAll('button').forEach(function (x) { x.classList.remove('on'); });
              if (specs.value.indexOf(line) !== -1) b.classList.add('on');
            });
            group.appendChild(b);
          });
          chips.appendChild(group);
        });
      }
      sel.addEventListener('change', function () { refresh(true); });
      row.querySelector('.rm').addEventListener('click', function () {
        if (builder.children.length > 1) row.remove();
      });
      refresh(false);
    }

    document.getElementById('add-item').addEventListener('click', function () { addRow(); });

    if (oldItems.length) {
      oldItems.forEach(addRow);
    } else {
      var first = products.find(function (p) { return p.slug === preselect; });
      addRow(first ? { product_id: first.id, quantity: first.min_quantity } : {});
      if (first) builder.querySelector('.product-select').dispatchEvent(new Event('change'));
    }
  }

  // ---- File drop zones: files accumulate across picks/drops, each removable ----
  document.querySelectorAll('.drop').forEach(function (zone) {
    var input = zone.querySelector('input[type=file]');
    var list = zone.parentElement.querySelector('.picked');
    var MAX = 10;
    var dt = new DataTransfer();

    function key(f) { return f.name + '|' + f.size + '|' + f.lastModified; }
    function add(files) {
      var have = {};
      Array.prototype.forEach.call(dt.files, function (f) { have[key(f)] = 1; });
      Array.prototype.forEach.call(files, function (f) {
        if (!have[key(f)] && dt.items.length < MAX) { dt.items.add(f); have[key(f)] = 1; }
      });
      input.files = dt.files;
      show();
    }
    function remove(i) {
      var next = new DataTransfer();
      Array.prototype.forEach.call(dt.files, function (f, idx) { if (idx !== i) next.items.add(f); });
      dt = next; input.files = dt.files; show();
    }
    function show() {
      if (!list) return;
      list.innerHTML = '';
      Array.prototype.forEach.call(dt.files, function (f, i) {
        var li = document.createElement('li');
        if (/^image\//.test(f.type)) {
          var img = document.createElement('img');
          img.src = URL.createObjectURL(f); img.alt = '';
          img.style.cssText = 'width:34px;height:34px;object-fit:cover;border-radius:6px;flex:none';
          li.appendChild(img);
        }
        var name = document.createElement('a'); name.textContent = f.name; li.appendChild(name);
        var s = document.createElement('small'); s.textContent = (f.size / 1048576).toFixed(1) + ' MB'; li.appendChild(s);
        var x = document.createElement('button'); x.type = 'button'; x.textContent = '×'; x.setAttribute('aria-label', 'Remove ' + f.name);
        x.style.cssText = 'border:0;background:none;color:#d92d4b;font-size:1.2rem;cursor:pointer;line-height:1';
        x.addEventListener('click', function () { remove(i); });
        li.appendChild(x); list.appendChild(li);
      });
      if (dt.files.length >= MAX) {
        var note = document.createElement('li'); note.textContent = 'Maximum of ' + MAX + ' files per upload.'; list.appendChild(note);
      }
    }
    input.addEventListener('change', function () { add(input.files); });
    ['dragenter', 'dragover'].forEach(function (ev) { zone.addEventListener(ev, function (e) { e.preventDefault(); zone.classList.add('over'); }); });
    ['dragleave', 'drop'].forEach(function (ev) { zone.addEventListener(ev, function (e) { e.preventDefault(); zone.classList.remove('over'); }); });
    zone.addEventListener('drop', function (e) { add(e.dataTransfer.files); });
  });

  // ---- Show/hide organization field by account type ---------------------
  var orgField = document.getElementById('org-field');
  if (orgField) {
    var radios = document.querySelectorAll('input[name=account_type]');
    var label = orgField.querySelector('label');
    function sync() {
      var v = document.querySelector('input[name=account_type]:checked');
      var type = v ? v.value : 'school';
      orgField.style.display = type === 'individual' ? 'none' : '';
      label.textContent = type === 'school' ? 'School / college name' : 'Business / organization name';
    }
    radios.forEach(function (r) { r.addEventListener('change', sync); });
    sync();
  }

  // ---- Live quotation total (admin) ------------------------------------
  var calc = document.getElementById('quotation-form');
  if (calc) {
    var cur = calc.dataset.currency;
    function fmt(n) { return cur + ' ' + Math.round(n).toLocaleString(); }
    function recalc() {
      var sub = 0;
      calc.querySelectorAll('[data-line]').forEach(function (row) {
        var price = parseFloat(row.querySelector('.price-input').value) || 0;
        var line = price * parseInt(row.dataset.qty, 10);
        row.querySelector('.line-total').textContent = fmt(line);
        sub += line;
      });
      var fee = parseFloat(calc.querySelector('[name=delivery_fee]').value) || 0;
      var disc = parseFloat(calc.querySelector('[name=discount]').value) || 0;
      calc.querySelector('#t-sub').textContent = fmt(sub);
      calc.querySelector('#t-total').textContent = fmt(Math.max(0, sub + fee - disc));
    }
    calc.addEventListener('input', recalc);
    recalc();
  }

  // ---- Auto-scroll chat to the latest message -------------------------
  document.querySelectorAll('.chat').forEach(function (c) { c.scrollTop = c.scrollHeight; });
})();

// ---- Styled confirmation dialog (replaces the browser's confirm box) -----
(function () {
  'use strict';
  var dlg = document.createElement('dialog');
  dlg.className = 'confirm';
  dlg.innerHTML = '<h3></h3><p></p><div class="confirm-actions"><button type="button" class="btn btn-sm" data-no></button><button type="button" class="btn btn-sm btn-danger" data-yes></button></div>';
  document.body.appendChild(dlg);
  var proceed = null;

  function ask(el, go) {
    dlg.querySelector('h3').textContent = el.dataset.confirmTitle || 'Are you sure?';
    dlg.querySelector('p').textContent = el.dataset.confirm || '';
    dlg.querySelector('[data-yes]').textContent = el.dataset.confirmOk || 'Yes, continue';
    dlg.querySelector('[data-no]').textContent = el.dataset.confirmCancel || 'No, go back';
    proceed = go;
    dlg.showModal();
    dlg.querySelector('[data-no]').focus();
  }
  dlg.querySelector('[data-no]').addEventListener('click', function () { proceed = null; dlg.close(); });
  dlg.querySelector('[data-yes]').addEventListener('click', function () { var go = proceed; proceed = null; dlg.close(); if (go) go(); });
  dlg.addEventListener('click', function (e) { if (e.target === dlg) { proceed = null; dlg.close(); } });

  // A button with data-confirm
  document.addEventListener('click', function (e) {
    var b = e.target.closest('button[data-confirm], a[data-confirm]');
    if (!b || b.dataset.confirmed) return;
    e.preventDefault();
    ask(b, function () {
      if (b.form) { b.dataset.confirmed = '1'; b.form.requestSubmit(b); }
      else if (b.href) { window.location = b.href; }
    });
  });
  // A form with data-confirm
  document.addEventListener('submit', function (e) {
    var f = e.target;
    if (!f.dataset || !f.dataset.confirm || f.dataset.confirmed) return;
    e.preventDefault();
    ask(f, function () { f.dataset.confirmed = '1'; f.requestSubmit(); });
  });
})();

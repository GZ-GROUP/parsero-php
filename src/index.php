<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Libros Disponibles</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <!-- ── Header ─────────────────────────────────────────── -->
  <header class="site-header">
    <div class="logo-area">
      <img class="logo-img" src="white.svg" alt="GZ Group Logo" />
    </div>
    <nav>
      <a href="#">Inicio</a>
      <a href="#">Catálogo</a>
      <a href="#">Acerca de</a>
    </nav>
  </header>

  <div class="accent-stripe"></div>

  <!-- ── Main ───────────────────────────────────────────── -->
  <main>
    <ul id="book-list" class="list bg-base-100 rounded-box shadow-md">
      <li class="p-4 pb-2 text-xs opacity-60 tracking-wide">Libros Disponibles</li>

      <!-- Loading skeleton -->
      <li class="skeleton-row" id="loading">
        <div class="skeleton size-10 rounded-box shrink-0"></div>
        <div class="flex flex-col gap-2 flex-1">
          <div class="skeleton h-3 w-32"></div>
          <div class="skeleton h-2 w-20"></div>
        </div>
      </li>
    </ul>
  </main>

  <!-- ── Footer ─────────────────────────────────────────── -->
  <footer class="site-footer">
    <div class="logo-area">
      <div class="brand-block">
        <img class="logo-img" src="white.svg" alt="GZ Group Logo" />
        <span class="brand-name">GZ Group</span>
      </div>
    </div>
    <span class="footer-copy">&copy; 2026 Biblioapp. Todos los derechos reservados.</span>
    <div class="footer-links">
      <a href="#">Privacidad</a>
      <a href="#">Términos</a>
      <a href="#">Contacto</a>
    </div>
  </footer>

  <script>
    async function loadBooks() {
      const list = document.getElementById('book-list');
      const loading = document.getElementById('loading');

      try {
        const response = await fetch('data.xml');
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const text = await response.text();
        const parser = new DOMParser();
        const xml = parser.parseFromString(text, 'application/xml');

        const parseError = xml.querySelector('parsererror');
        if (parseError) throw new Error('XML malformado');

        const libros = xml.querySelectorAll('libro');

        loading.remove();

        if (libros.length === 0) {
          const empty = document.createElement('li');
          empty.className = 'p-6 text-center text-sm opacity-50';
          empty.textContent = 'No hay libros disponibles.';
          list.appendChild(empty);
          return;
        }

        libros.forEach(libro => {
          const titulo    = libro.querySelector('titulo')?.textContent?.trim()    || 'Sin título';
          const año       = libro.querySelector('año')?.textContent?.trim()       || '—';
          const autor     = libro.querySelector('autor')?.textContent?.trim()     || 'Autor desconocido';
          const image_url = libro.querySelector('image_url')?.textContent?.trim() || '';

          const li = document.createElement('li');
          li.className = 'list-row';
          li.innerHTML = `
            <div>
              <img
                class="size-10 rounded-box object-cover"
                src="${escapeHtml(image_url)}"
                alt="Portada de ${escapeHtml(titulo)}"
                onerror="this.src='https://placehold.co/40x40?text=📖'"
              />
            </div>

            <div class="info">
              <div class="font-medium text-sm">${escapeHtml(titulo)}</div>
              <div class="text-xs uppercase font-semibold opacity-60">${escapeHtml(autor)}</div>
            </div>

            <p class="list-col-wrap text-xs opacity-70">${escapeHtml(año)}</p>

            <button class="btn btn-square btn-ghost" title="Leer" aria-label="Leer ${escapeHtml(titulo)}">
              <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor">
                  <path d="M6 3L20 12 6 21 6 3z"></path>
                </g>
              </svg>
            </button>

            <button class="btn btn-square btn-ghost" title="Favorito" aria-label="Agregar ${escapeHtml(titulo)} a favoritos">
              <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor">
                  <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                </g>
              </svg>
            </button>
          `;

          list.appendChild(li);
        });

      } catch (err) {
        loading.remove();
        const errorLi = document.createElement('li');
        errorLi.className = 'p-4 text-sm text-error';
        errorLi.innerHTML = `
          <span class="font-semibold">Error al cargar data.xml:</span> ${escapeHtml(err.message)}.
          Asegúrate de servir el archivo desde un servidor local (no abrirlo con <code>file://</code>).
        `;
        list.appendChild(errorLi);
        console.error(err);
      }
    }

    function escapeHtml(str) {
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    loadBooks();
  </script>
</body>
</html>

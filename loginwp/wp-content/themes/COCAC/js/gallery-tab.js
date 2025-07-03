$(document).ready(function () {
  const $tabsNav = $("#tabs-nav");
  const $tabsContent = $("#tabs-content");
  const IMAGES_PER_PAGE = 12;
  let galeriaData = {};
  let allImages = [];

  // Crear select para móviles
  const $tabsSelect = $('<select id="tabs-select" style="display:none;margin-bottom:20px;"></select>');
  $tabsNav.after($tabsSelect);

  // Tab "Todos"
  $tabsNav.append(`<li><a href="#" class="tab-link active" data-tab="tab-all">Todos</a></li>`);
  $tabsSelect.append(`<option value="tab-all" selected>Todos</option>`);
  $tabsContent.append(`<div class="tab-content active" id="tab-all"><div class="imagenes"></div><div class="paginacion"></div></div>`);

  $.getJSON("https://cocacbc.com/wp-json/wp/v2/galeria?per_page=100", function (data) {
    data.forEach(function (item) {
      const id = item.id;
      const slug = "tab-" + id;
      const titulo = item.title.rendered;
      const galeria = item.acf.fotos_galeria || [];

      galeriaData[slug] = galeria;
      allImages = allImages.concat(galeria);

      $tabsNav.append(`<li><a href="#" class="tab-link" data-tab="${slug}">${titulo}</a></li>`);
      $tabsSelect.append(`<option value="${slug}">${titulo}</option>`);
      $tabsContent.append(`<div class="tab-content" id="${slug}">
          <div class="imagenes"></div>
          <div class="paginacion"></div>
      </div>`);
    });

    allImages = [...new Set(allImages)];
    galeriaData["tab-all"] = allImages;
    renderTabImages("tab-all", 1);
    renderPagination("tab-all", 1);
  });

  // Mostrar/ocultar tabs-nav y select según ancho
  function toggleTabsNav() {
    if (window.innerWidth <= 700) {
      $tabsNav.hide();
      $tabsSelect.show();
    } else {
      $tabsNav.show();
      $tabsSelect.hide();
    }
  }
  toggleTabsNav();
  $(window).on('resize', toggleTabsNav);

  // Evento para select en móvil
  $tabsSelect.on('change', function () {
    const tab = $(this).val();
    $(".tab-link").removeClass("active");
    $(`.tab-link[data-tab="${tab}"]`).addClass("active");
    $(".tab-content").removeClass("active");
    $(`#${tab}`).addClass("active");
    renderTabImages(tab, 1);
    renderPagination(tab, 1);
  });

  // Renderizar imágenes para un tab y página específica
  function renderTabImages(tabId, page) {
    const $container = $(`#${tabId} .imagenes`);
    $container.empty();

    // Mostrar spinner de carga
    $container.append('<div class="gallery-loading" style="text-align:center;padding:40px 0;"><span class="spinner"></span></div>');

    const images = galeriaData[tabId] || [];
    const start = (page - 1) * IMAGES_PER_PAGE;
    const end = start + IMAGES_PER_PAGE;
    const pageImages = images.slice(start, end);

    let imagesLoaded = 0;
    pageImages.forEach(id => {
      $.getJSON(`https://cocacbc.com/wp-json/wp/v2/media/${id}`, function (imgData) {
        // Quitar el spinner al cargar la primera imagen
        if (imagesLoaded === 0) {
          $container.find('.gallery-loading').remove();
        }
        $container.append(`
          <a href="${imgData.source_url}" class="gallery-item popup-link fade-in" title="${imgData.title.rendered}">
            <img src="${imgData.source_url}" data-id="${id}">
            <span class="gallery-overlay"><i class="fa fa-search-plus"></i></span>
          </a>
        `);
        imagesLoaded++;
        if (imagesLoaded === pageImages.length) {
          $container.magnificPopup({
            delegate: 'a.popup-link',
            type: 'image',
            gallery: { enabled: true },
            image: {
              titleSrc: 'title'
            }
          });
          if ($container.data('masonry')) {
            $container.masonry('destroy');
          }
          $container.imagesLoaded(function () {
            $container.masonry({
              itemSelector: '.gallery-item',
              percentPosition: true,
              gutter: 10
            });
          });
        }
      });
    });

    // Si no hay imágenes, quitar el spinner después de un breve tiempo
    if (pageImages.length === 0) {
      setTimeout(function () {
        $container.find('.gallery-loading').remove();
      }, 500);
    }
  }

  // Renderizar paginación para un tab
  function renderPagination(tabId, currentPage) {
    const $pagination = $(`#${tabId} .paginacion`);
    $pagination.empty();
    const images = galeriaData[tabId] || [];
    const pages = Math.ceil(images.length / IMAGES_PER_PAGE);

    if (pages <= 1) {
      $pagination.hide();
      return;
    } else {
      $pagination.show();
    }

    for (let i = 1; i <= pages; i++) {
      const active = i === currentPage ? 'active' : '';
      $pagination.append(`<button class="${active}" data-page="${i}">${i}</button>`);
    }
  }

  // Cambiar de tab
  $(document).on("click", ".tab-link", function (e) {
    e.preventDefault();
    if ($(this).hasClass("active")) return false;

    const tab = $(this).data("tab");

    $(".tab-link").removeClass("active");
    $(this).addClass("active");

    $(".tab-content").removeClass("active");
    $(`#${tab}`).addClass("active");
    // Renderizar solo si no hay imágenes cargadas
    if ($(`#${tab} .imagenes`).is(':empty')) {
      renderTabImages(tab, 1);
      renderPagination(tab, 1);
    }
  });

  // Paginación por tab
  $(document).on("click", ".paginacion button", function () {
    const $tabContent = $(this).closest('.tab-content');
    const tabId = $tabContent.attr('id');
    const page = parseInt($(this).data("page"));
    renderTabImages(tabId, page);
    renderPagination(tabId, page);
  });
});


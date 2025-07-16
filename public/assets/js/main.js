$(function () {
    let formatTimeout;
    let active = $('.page-content').attr("data-active");
    let pjaxContainer = ["[pjax-load-content]","header"];
    var pjax = new Pjax({
      elements: "[data-pjax]",
      selectors: pjaxContainer,
      cacheBust: false,
      scrollTo: false,
      history: true,
    });
    document.addEventListener('pjax:send', pjaxSend);
    document.addEventListener('pjax:complete', pjaxComplete);
    document.addEventListener('pjax:success', whenDOMReady);
    document.addEventListener('pjax:error',pjaxError);
    $(document).ready(function() {
        document.addEventListener("click", function(event) {
              const link = event.target.closest("a[download]");
              if (!link) return;
              event.preventDefault();
              const url = link.href;
              const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
              const isPWA = window.matchMedia("(display-mode: standalone)").matches || window.navigator.standalone;

              if (isIOS && isPWA) {
                window.location.href = url;
              } else {
                const tempLink = document.createElement("a");
                tempLink.href = url;
                tempLink.setAttribute("download", link.getAttribute("download") || "file.png");
                document.body.appendChild(tempLink);
                tempLink.click();
                document.body.removeChild(tempLink);
              }
        });
        $(document).on("click", "[data-pjax]", function (e) {
            var $this = $(this);
            pjaxConfig($this);
            if ($this.is('[data-dismiss="offcanvas"]')) {
                var offcanvas = document.querySelector('.offcanvas.show') || document.querySelector('.offcanvas-lg.show');
                if (offcanvas) {
                    var bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas);
                    if (bsOffcanvas) bsOffcanvas.hide();
                }
            }
            if ($this.is('[data-dismiss="modal"]')) {
                var Modal = document.querySelector('.modal.show');
                if (Modal) {
                    var bsModal = bootstrap.Modal.getInstance(Modal);
                    if (bsModal) bsModal.hide();
                }
            }
        });
        if($('body').find('.modal-notification-register').length){
          $('body').find('.modal-notification-register').modal('show');
        }
        $(document).on("click",'#subscribe-btn',function() {
            $.getJSON('/users/vapid-public-key', function (response) {
                navigator.serviceWorker.ready.then(function (registration) {
                    return registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: urlBase64ToUint8Array(response.key)
                    });
                }).then(function (subscription) {
                    $.ajax({
                        url: '/users/notification-register',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(subscription),
                        success: function (response) {
                            console.log(response);
                            swal_success(response.content, $('#subscribe-btn'));
                        }
                    });
                }).catch(function (error) {
                    swal_error(error);
                });
            });
        });
        $(document).on('click','.btn-print', function () {
          window.print();
        });
        $(document).on('change', '.checkall', function() {
            var checkbox = $(this).attr('data-checkbox');
            $(checkbox).prop('checked', this.checked);
        });
        themeLayout();
        whenDOMReady();
        // qz.security.setCertificatePromise(function(resolve, reject) {
        //   $.get("/qz-tray/cert.pem").done(resolve).fail(reject);
        // });
        // qz.security.setSignaturePromise(toSign => {
        //   return new Promise((resolve, reject) => {
        //     // Gửi về server PHP để ký bằng private.key
        //     $.ajax({
        //       url: "/qz-sign",
        //       type: "POST",
        //       data: { data: btoa(toSign) },
        //       success: function(signature) {
        //         resolve(signature);
        //       },
        //       error: function(err) {
        //         reject(err);
        //       }
        //     });
        //   });
        // });
        // connectQZ();
        initializeRegisteredPlugins();
    });
    function pjaxConfig($element) {
        let selector = $element.data("selector");
        let scrollTo = $element.data("pjax-scrollTo") !== "false" && ($element.data("scrollTo") ?? true);
        let historyState = $element.data("pjax-history") !== "false" && ($element.data("pjax-history") ?? true);
        pjax.options.history = historyState ?? pjax.options.history;
        pjax.options.scrollTo = scrollTo ?? pjax.options.scrollTo;
        pjax.options.selectors = selector ? selector.split(",").map(s => s.trim()) : pjaxContainer;
        const animationData = {};
        if ($element.data("pjax-animate") !== undefined) {
             animationData.animateEnabled = $element.data("pjax-animate") === true;
        }
        if ($element.data("pjax-right") !== undefined) {
            animationData.animateRightClass = $element.data("pjax-right");
        }
        if ($element.data("pjax-left") !== undefined) {
            animationData.animateLeftClass = $element.data("pjax-left");
        }
        if ($element.data("pjax-faster") !== undefined) {
            animationData.useFaster = $element.data("pjax-faster") !== undefined;
        }
        pjax._nextAnimationData = animationData;
    }
    function pjaxSend(){
      topbar.show();
    }
    function pjaxComplete(){
      topbar.hide();
      initializeRegisteredPlugins();
    }
    function pjaxError(){
      topbar.hide();
    }
    function whenDOMReady(){
      datatable();
      dataAction();
      selected();
      uploadImages();
      DomDataAction();
      editor();
      number();
      Countdown();
      initSearchBoxes();
      swiper();
      step();
      chartjs();
      datapicker();
      print();
    }
    function themeLayout() {
      function setTheme(theme) {
        if (theme === 'system') {
          theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        $("html").attr("data-bs-theme", theme);
        $("body").attr("data-bs-theme", theme);
      }
      function toggleSidebar() {
        const currentLayout = $("body").attr("data-sidebar");
        const newLayout = currentLayout === 'full' ? 'small' : 'full';
        $("body").attr("data-sidebar", newLayout);
        localStorage.setItem('layout', newLayout);
      }
      let theme = localStorage.getItem('theme') || 'system';
      localStorage.setItem('theme', theme);
      setTheme(theme);
      if (theme === 'system') {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
          setTheme('system');
        });
      }
      $(document).on("click", '[data-toggle-theme]', function () {
        const newTheme = $(this).attr("data-theme");
        localStorage.setItem('theme', newTheme);
        setTheme(newTheme);
      });
      $(document).on("click", '[data-toggle-sidebar]', toggleSidebar);
      const savedLayout = localStorage.getItem('layout') || 'full';
      $("body").attr("data-sidebar", savedLayout);
        const $hoverEffect = $('.hover-effect');
        const $menuContainer = $('.menu-container');
        const $menuContainerMobile = $('.menu-container-mobile');
        function updateHoverEffect($element, $container) {
          if ($element.length === 0 || $container.length === 0) return;
          const containerOffset = $container.offset().left;
          const elementOffset = $element.offset().left;
          const leftPos = elementOffset - containerOffset;
          const width = $element.outerWidth();
          $hoverEffect.css({
            left: leftPos,
            width: width,
          });
          $hoverEffect.addClass('show');
          $container.find('.header-menu').removeClass('hover');
          $element.addClass('hover');
        }
        function updateHoverForAllActive() {
          const $activeElements = $('.header-menu.active');
          $activeElements.each(function () {
            const $currentElement = $(this);
            const $currentContainer = $currentElement.closest('.menu-container, .menu-container-mobile');
            if ($currentContainer.length) {
              updateHoverEffect($currentElement, $currentContainer);
            }
          });
        }
        updateHoverForAllActive();
        $('.header-menu').hover(
          function () {
            const $currentContainer = $(this).closest('.menu-container, .menu-container-mobile');
            if ($currentContainer.length) {
              updateHoverEffect($(this), $currentContainer);
            }
          },
          function () {
            updateHoverForAllActive();
          }
        );
        $('.header-menu').on('click', function (e) {
          e.preventDefault();
          $('.header-menu').removeClass('active');
          $(this).addClass('active');
          updateHoverForAllActive();
        });
        $(window).on('resize', function () {
          updateHoverForAllActive();
        });
      if (!getCookie('did')) {
        setCookie('did', generateUUID(), 365);
      }
    }
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0,
                v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    function swal_success(text, $this = null) {
      Swal.fire({
        title: 'Success',
        text: text,
        icon: 'success',
        showCancelButton: false,
        buttonsStyling: false,
        confirmButtonText: 'Ok',
        customClass: {
          confirmButton: "btn fw-bold btn-success rounded-pill px-5"
        }
      }).then(function (result) {
        if ($this) {
          let $modal = $this.closest('.modal');
          let $offcanvas = $this.closest('.offcanvas');
          if ($modal.length) {
            $modal.modal('hide');
          }
          if ($offcanvas.length) {
            $offcanvas.offcanvas('hide');
          }
        }
      });
    }
    function swal_error(text) {
      Swal.fire({
        title: 'Error!',
        text: text,
        icon: 'error',
        showCancelButton: false,
        buttonsStyling: false,
        confirmButtonText: 'Ok',
        customClass: {
          confirmButton: "btn fw-bold btn-danger rounded-pill px-5"
        }
      });
    }
    function number(){
        $('[data-number="number"], [data-number="money"]').each(function() {
          setupNumberInput($(this));
      });
    }
    function formatElement($el) {
        let type = $el.data('number');
        if (!['number', 'money'].includes(type)) return;
        let currency = $el.data('currency') || '';
        let decimals = parseInt($el.data('decimals')) || 0;
        let sepThousands = $el.data('thousands') || '.';
        let sepDecimal = $el.data('decimal') || ',';
        let el = $el[0];
        let caretStart = el.selectionStart;
        let caretEnd = el.selectionEnd;
        let text = $el.val();
        let cleanedText = '';
        let hasMinusSign = false;
        if (text.startsWith('-')) {
            hasMinusSign = true;
            cleanedText = '-';
            text = text.substring(1);
        }
        let tempText = text.replace(new RegExp('[^0-9\\' + sepDecimal + ']', 'g'), '');
        let decimalParts = tempText.split(sepDecimal);
        if (decimalParts.length > 2) {
            tempText = decimalParts[0] + sepDecimal + decimalParts.slice(1).join('');
        }
        cleanedText += tempText;
        if (cleanedText === '-' || cleanedText.trim() === '') {
            $el.val(cleanedText);
            return;
        }
        let raw = cleanedText.replace(sepDecimal, '.');
        let number = parseFloat(raw);
        if (isNaN(number)) number = 0;
        let min = parseFloat($el.data('min'));
        let max = parseFloat($el.data('max'));
        if (!isNaN(min) && number < min) number = min;
        if (!isNaN(max) && number > max) number = max;
        let parts = Math.abs(number).toFixed(decimals).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, sepThousands);
        let formatted = parts.join(decimals > 0 ? sepDecimal : '');
        if (number < 0) {
            formatted = '-' + formatted;
        }
        if (currency) formatted += ' ' + currency;
        if ($el.val() !== formatted) {
            let oldVal = $el.val();
            $el.val(formatted);
            let newCaretPos = caretStart;
            if (formatted.length > oldVal.length) {
                newCaretPos += (formatted.length - oldVal.length);
            } else if (formatted.length < oldVal.length) {
                newCaretPos -= (oldVal.length - formatted.length);
            }
            if (sepThousands !== '') {
                let thousandsAdded = (formatted.match(new RegExp(sepThousands, 'g')) || []).length - (oldVal.match(new RegExp(sepThousands, 'g')) || []).length;
                newCaretPos += thousandsAdded;
            }
            if (currency && formatted.indexOf(currency) > -1 && oldVal.indexOf(currency) === -1) {
                newCaretPos -= (currency.length + 1);
            }
            if (newCaretPos < 0) newCaretPos = 0;
            if (newCaretPos > formatted.length) newCaretPos = formatted.length;
            setTimeout(() => el.setSelectionRange(newCaretPos, newCaretPos), 0);
        }
    }
    function setupNumberInput($input) {
        $input.on('input', function() {
            clearTimeout(formatTimeout);
            let text = $(this).val();
            let sepDecimal = $(this).data('decimal') || ',';
            let cleanedText = '';
            if (text.startsWith('-')) {
                cleanedText = '-';
                text = text.substring(1);
            }
            cleanedText += text.replace(new RegExp('[^0-9\\' + sepDecimal + ']', 'g'), '');
            $(this).val(cleanedText);
            formatTimeout = setTimeout(() => formatElement($(this)), 500);
        });
        $input.on('blur', function() {
            clearTimeout(formatTimeout);
            formatElement($(this));
        });
        formatElement($input);
    }
    function step(){
      $('[data-step="true"]').each(function () {
        const $container = $(this);
        const activeClass = $container.attr('data-step-active').replace('.', '');
        const $steps = $container.find('[data-step-index]');
        function updateStepDisplay() {
          $steps.each(function () {
            const $step = $(this);
            if ($step.hasClass(activeClass)) {
              $step.css({ display: 'block' });
              if ($step.is('[data-step-animate]')) {
                const animateClass = $step.attr('data-step-animate');
                $step.addClass(animateClass);
              }
            } else {
              $step.css({ display: 'none' });
              if ($step.is('[data-step-animate]')) {
                const animateClass = $step.attr('data-step-animate');
                $step.removeClass(animateClass);
              }
            }
          });
        }
        function showStep(index) {
          $steps.removeClass(activeClass);
          const $target = $steps.filter('[data-step-index="' + index + '"]');
          $target.addClass(activeClass);
          updateStepDisplay();
        }
        if ($steps.length > 0) {
          if (!$steps.hasClass(activeClass)) {
            $steps.first().addClass(activeClass);
          }
          updateStepDisplay();
        }
        $container.on('click', '[data-step-next]', function () {
          const $current = $steps.filter('.' + activeClass);
          const currentIndex = parseInt($current.attr('data-step-index'));
          const nextIndex = currentIndex + 1;
          if ($steps.filter('[data-step-index="' + nextIndex + '"]').length) {
            showStep(nextIndex);
          }
        });
        $container.on('click', '[data-step-prev]', function () {
          const $current = $steps.filter('.' + activeClass);
          const currentIndex = parseInt($current.attr('data-step-index'));
          const prevIndex = currentIndex - 1;
          if ($steps.filter('[data-step-index="' + prevIndex + '"]').length) {
            showStep(prevIndex);
          }
        });
      });
    }
    function swiper(){
        function parseValue(value) {
          if (value === 'true') return true;
          if (value === 'false') return false;
          if (!isNaN(value) && value.trim() !== '') return Number(value);
          try {
            return JSON.parse(value);
          } catch (e) {
            return value;
          }
        }
        function setDeep(obj, path, value) {
          const keys = path.split('.');
          let current = obj;
          keys.forEach((key, index) => {
            if (index === keys.length - 1) {
              current[key] = value;
            } else {
              current[key] = current[key] || {};
              current = current[key];
            }
          });
        }
        document.querySelectorAll('[data-swiper]').forEach(el => {
          const options = {};
          Object.entries(el.dataset).forEach(([key, value]) => {
            if (key === 'swiper') return;
            let parsedKey = key.replace(/-./g, x => x[1].toUpperCase());
            setDeep(options, parsedKey, parseValue(value));
          });
          const paginationEl = el.querySelector('.swiper-pagination');
          if (paginationEl) {
            options.pagination = options.pagination || {};
            options.pagination.el = options.pagination.el || paginationEl;
            if (typeof options.pagination.clickable === 'undefined') {
              options.pagination.clickable = true;
            }
          }
          const nextEl = el.querySelector('.swiper-button-next');
          const prevEl = el.querySelector('.swiper-button-prev');
          if (nextEl && prevEl) {
            options.navigation = options.navigation || {};
            options.navigation.nextEl = options.navigation.nextEl || nextEl;
            options.navigation.prevEl = options.navigation.prevEl || prevEl;
          }
          if (options.autoplay === true) {
            options.autoplay = { delay: 3000, disableOnInteraction: false };
          } else if (typeof options.autoplay === 'number') {
            options.autoplay = { delay: options.autoplay, disableOnInteraction: false };
          }
          new Swiper(el, options);
        });
    }
    function datatable(context = document) {
      $(context).find('[data-table]').each(function () {
        const $table = $(this);
        if (!$.fn.dataTable.isDataTable($table)) {
          const columns = $table.find('thead th').map(function () {
              const $th = $(this);
              return {
                  data: $th.attr('data-name') || null,
                  orderable: $th.attr('data-orderable') === "true",
                  visible: $th.attr('data-visible') !== "false",
                  className: $th.attr('data-class') || '',
                  render: function (data, type, row) {
                      if ($th.attr('data-name') === 'actions') {
                          return $th.attr('data-render');
                      }
                      return data;
                  }
              };
          }).get();
          const searchableColumns = columns.map((col, index) => (col.visible ? index : null)).filter(index => index !== null);
          const options = {
              ajax: {
                  url: $table.attr('data-url') || null,
                  type: $table.attr('data-type') || 'POST',
                  data: function(d) {
                      let searchParams = {};
                      return $.extend({}, d, searchParams);
                      Countdown();
                  }
              },
              columns: columns,
              processing: $table.attr('data-processing') === "true",
              serverSide:  $table.attr('data-server') === "true",
              pageLength: parseInt($table.attr('data-page-length')) || 10,
              searching: $table.attr('data-searching') === "true",
              order: JSON.parse($table.attr('data-order') || '[]'),
              lengthMenu: JSON.parse($table.attr('data-length-menu') || '[[10, 25, 50, 100, 200 , 500], ["10", "25", "50", "100", "200", "500"]]'),
              paging: $table.attr('data-paging') !== "false",
              info: $table.attr('data-info') !== "false",
              language: JSON.parse($table.attr('data-lang') || '{"search": "","searchPlaceholder": "Nhập để tìm kiếm...","lengthMenu": "_MENU_", "info": "Hiển thị _START_ đến _END_ của tổng _TOTAL_", "infoEmpty":"Hiển thị 0 đến 0 của tổng 0","emptyTable": "Không tìm thấy dữ liệu"}'),
              scrollX: $table.attr('data-scroll-x') || null,
              scrollY: $table.attr('data-scroll-y') || null,
              stateSave: $table.attr('data-state-save') || null,
              dom: "<'row p-2 align-items-center g-2'<'col-md-6 col-lg-5 col-12 text-start order-2 order-md-1'f><'col-md-6 col-lg-7 col-12 order-1 order-md-2 text-end custom-buttons-display'>>" +
                   "<'row mb-4'<'col-md-12't>>" + 
                   "<'row mb-2 px-2 align-items-center justify-content-between'<'col-md-6 justify-content-start'p><'col-md-6 align-items-center justify-content-md-end d-flex'i l>>",
              button: [],
              initComplete: function () {
                  const $buttonSearch = $('.custom-buttons').clone(true);
                  $('.custom-buttons-display').html($buttonSearch.html());
              },
              drawCallback: function () {
                  Countdown();
              },
              footerCallback: function (row, data, start, end, display) {
                  const api = this.api();
                  const response = api.ajax.json();

                  if (response.footerData) {
                      // Duyệt qua từng cột <th> trong tfoot
                      $(api.table().footer()).find('th').each(function (index) {
                          const name = $(this).data('name');
                          if (name && response.footerData[name] !== undefined) {
                              $(this).html(response.footerData[name]);
                          }
                      });
                  }
              }
          };
          var dataTableInstance = $table.DataTable(options);
        }
        // if (!$.fn.dataTable.isDataTable($table)) {
        // } else {
        //     var dataTableInstance = $table.DataTable();
        // }
        $(document).off("click", ".button-filter").on("click", ".button-filter", function() {
            let table = dataTableInstance;
            let filterData = {};
            let params = new URLSearchParams(window.location.search);
            $(".filter-name").each(function() {
                let $el = $(this);
                let name = $el.attr("name");
                let value = "";

                if ($el.is("select") || $el.is("input")) {
                    value = $el.val();
                } else if ($el.is("input[type='checkbox'], input[type='radio']")) {
                    if ($el.is(":checked")) {
                        value = $el.val();
                    }
                }
                if (value !== "") {
                    filterData[name] = value;
                    params.set(name, value);
                } else {
                    params.delete(name);
                }
            });
            table.settings()[0].ajax.data = function(d) {
                return $.extend({}, d, filterData);
            };
            history.pushState({}, "", "?" + params.toString());
            table.ajax.reload();
        });

        // Reset bộ lọc
        $(document).off("click", ".reset-filter").on("click", ".reset-filter", function() {
            let table = dataTableInstance;
            let params = new URLSearchParams(window.location.search);

            $(".filter-name").each(function() {
                $(this).val("").trigger("change");
                params.delete($(this).attr("name"));
            });

            history.replaceState({}, "", window.location.pathname);
            table.settings()[0].ajax.data = function (d) {
                return d; // Không truyền filterData nữa
            };
            table.ajax.reload(null, false);
        });
        $(document).ready(function() {
            let params = new URLSearchParams(window.location.search);
            let filterData = {};
            $(".filter-name").each(function() {
                let $el = $(this);
                let name = $el.attr("name");
                if (name.endsWith("[]")) {
                    let value = params.get(name);
                    if (value.length > 0) {
                        value = value.split(",").map(item => item.trim()).filter(item => item !== "");
                        $el.val(value).trigger("change");
                        filterData[name] = value;
                    }
                } 
                else if (params.has(name)) {
                    let value = params.get(name);
                    $el.val(value).trigger("change");
                    filterData[name] = value;
                }
            });
            if (Object.keys(filterData).length > 0) {
                dataTableInstance.settings()[0].ajax.data = function(d) {
                    return $.extend({}, d, filterData);
                };
                dataTableInstance.ajax.reload();
            }
        });
      });
      $(context).find('[data-table]').on('show.bs.dropdown', '.dropdown', function () {
          let $dropdownMenu = $(this).find('.dropdown-menu');
          if (!$dropdownMenu.data('original-style')) {
              $dropdownMenu.data('original-style', $dropdownMenu.attr('style') || '');
          }
          $('body').append($dropdownMenu.detach());
          let newStyle = `${$dropdownMenu.data('original-style')}; display: block; position: absolute; top: ${$(this).offset().top + $(this).outerHeight()}px; left: ${$(this).offset().left}px;`;            $dropdownMenu.attr('style', newStyle);
          $(this).data('dropdown-menu', $dropdownMenu);
          pjax.refresh();
          pjaxConfig($(this));
      });
      $(context).find('[data-table]').on('hidden.bs.dropdown', '.dropdown', function () {
          let $dropdownMenu = $(this).data('dropdown-menu');
          if ($dropdownMenu) {
              $(this).append($dropdownMenu.detach());
              $dropdownMenu.attr('style', $dropdownMenu.data('original-style'));
              $(this).removeData('dropdown-menu');
              pjax.refresh();
              pjaxConfig($(this));
          }
      });
    }
    function selected(){
      $(function () {
         $('[data-select]').selectpicker();
      });
    }
    function editor() {
      $('[data-editor]').each(function () {
        var el = this;
        if (!$(el).attr('data-editor-initialized')) {
          new RichTextEditor(el, {
            width: $(el).width() + 'px',
            height: $(el).height() + 'px'
          });
          $(el).attr('data-editor-initialized', 'true');
        }
      });
    }
    function Countdown() {
        $('[data-countdown]').each(function () {
            const $el = $(this);
            if ($el.data('countdown-started')) return;
            $el.data('countdown-started', true);
            const startStr = $el.data('countdown-start');
            const endStr = $el.data('countdown-end');
            const color = $el.data('countdown-color');
            const timeDownThreshold = parseInt($el.data('countdown-timedown'), 10);
            const timeDownColor = $el.data('countdown-timedown-color');
            const timeUpThreshold = parseInt($el.data('countdown-timeup'), 10);
            const timeUpColor = $el.data('countdown-timeup-color');
            if (!startStr || !endStr) return;
            const startTime = new Date(startStr.replace(/-/g, '/')).getTime();
            const endTime = new Date(endStr.replace(/-/g, '/')).getTime();
            if (isNaN(startTime) || isNaN(endTime)) {
                $el.text('Invalid time format');
                return;
            }
            $el.css('color', color);
            function applyColorConditionally(distance, isPast) {
                $el.css('color', color);
                if (timeDownColor?.startsWith('.')) $el.removeClass(timeDownColor.slice(1));
                if (timeUpColor?.startsWith('.')) $el.removeClass(timeUpColor.slice(1));

                const seconds = Math.floor(distance / 1000);

                if (!isPast && timeDownThreshold && seconds <= timeDownThreshold) {
                    if (timeDownColor?.startsWith('.')) {
                        $el.addClass(timeDownColor.slice(1));
                    } else {
                        $el.css('color', timeDownColor);
                    }
                } else if (isPast && timeUpThreshold && seconds >= timeUpThreshold) {
                    if (timeUpColor?.startsWith('.')) {
                        $el.addClass(timeUpColor.slice(1));
                    } else {
                        $el.css('color', timeUpColor);
                    }
                }
            }
            function update() {
                const now = new Date().getTime();
                let distance, prefix, isPast = false;

                if (now < endTime) {
                    distance = endTime - now;
                    prefix = '- ';
                } else {
                    distance = now - endTime;
                    prefix = '+ ';
                    isPast = true;
                }
                applyColorConditionally(distance, isPast);
                const months = Math.floor(distance / (1000 * 60 * 60 * 24 * 30));
                const days = Math.floor((distance % (1000 * 60 * 60 * 24 * 30)) / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                let display = prefix;
                if (months > 0) display += `${months} tháng `;
                if (days > 0 || months > 0) display += `${days} ngày `;

                if (hours > 0 || minutes > 0) {
                    display += `${hours.toString().padStart(2, '0')}:` +
                                `${minutes.toString().padStart(2, '0')}:` +
                                `${seconds.toString().padStart(2, '0')}`;
                } else if (seconds > 0 || distance === 0) {
                    display += `00:00:${seconds.toString().padStart(2, '0')}`;
                } else {
                    display = '';
                }
                $el.text(display.trim());
            }
            update();
            setInterval(update, 1000);
        });
    }
    function chartjs(){
      $('[data-chart]').each(function () {
        const $canvas = $(this);
        const ctx = this.getContext('2d');

        const type = $canvas.data('type') || 'bar';
        const chartName = $canvas.data('chart') || '';

        const width = $canvas.data('width');
        const height = $canvas.data('height');
        if (width) $canvas.attr('width', width);
        if (height) $canvas.attr('height', height);

        // Labels
        let labels = [];
        try {
            labels = JSON.parse($canvas.attr('data-labels') || '[]');
        } catch (e) {
            console.error(`Lỗi parse data-labels của ${chartName}:`, e);
        }

        // Data
        let datasets = [];
        const rawDatasets = $canvas.attr('data-datasets');
        const rawDataset = $canvas.attr('data-dataset');
        const datasetName = $canvas.data('name') || '';

        // Lấy màu, có thể là chuỗi (một màu) hoặc mảng màu
        let colors = $canvas.data('colors') || $canvas.data('color');  // ưu tiên colors rồi mới đến color
        if (typeof colors === 'string') {
            // nếu là chuỗi JSON, parse
            try {
                colors = JSON.parse(colors);
            } catch {
                // nếu không parse được thì giữ nguyên chuỗi (ví dụ: "red", "blue")
            }
        }

        if (rawDatasets) {
            try {
                datasets = JSON.parse(rawDatasets);
            } catch (e) {
                console.error(`Lỗi khi parse data-datasets của ${chartName}:`, e);
            }
        } else if (rawDataset) {
            try {
                const data = JSON.parse(rawDataset);

                datasets = [{
                    label: datasetName,
                    data: data,
                    backgroundColor: colors || 'rgba(75, 192, 192, 0.2)',
                    borderColor: type === 'line' ? (colors || 'rgba(75, 192, 192, 1)') : undefined,
                    borderWidth: 1,
                    fill: type === 'line' ? false : true,
                    tension: type === 'line' ? 0.4 : undefined
                }];
            } catch (e) {
                console.error(`Lỗi khi parse data-dataset của ${chartName}:`, e);
            }
        }

        const isCircular = ['doughnut', 'pie'].includes(type);

        // Lưới (grid)
        const gridXDisplay = $canvas.data('grid-x') !== undefined ? !!$canvas.data('grid-x') : true;
        const gridYDisplay = $canvas.data('grid-y') !== undefined ? !!$canvas.data('grid-y') : true;
        const gridColor = $canvas.data('grid-color') || 'rgba(0,0,0,0.1)';
        const gridLineWidth = $canvas.data('grid-linewidth') || 1;

        new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: !width && !height,
                maintainAspectRatio: !width && !height,
                plugins: {
                    legend: {
                        display: true,
                        position: isCircular ? 'bottom' : 'top'
                    },
                    title: {
                        display: !!chartName,
                        text: chartName
                    }
                },
                scales: isCircular ? {} : {
                    x: {
                        grid: {
                            display: gridXDisplay,
                            color: gridColor,
                            lineWidth: gridLineWidth
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: gridYDisplay,
                            color: gridColor,
                            lineWidth: gridLineWidth
                        }
                    }
                }
            }
        });
    });


      // data-chart="..."  Tên biểu đồ (hiển thị title)
      // data-type="..." Loại biểu đồ (line, bar)
      // data-labels Nhãn trục X
      // data-dataset  Dữ liệu đơn, dùng kèm data-name
      // data-datasets Dữ liệu nhiều datasets
    }
    function datapicker(){
      $('[data-datepicker]').each(function () {
            const $el = $(this);
            const dateFormat = $el.data('format') || "DD/MM/YYYY"; // lấy định dạng từ data-format hoặc mặc định

            $el.daterangepicker({
                "showDropdowns": true,
                "showWeekNumbers": true,
                "showISOWeekNumbers": true,
                "autoApply": true,
                "locale": {
                    "format": dateFormat,
                    "applyLabel": "Áp dụng",
                    "cancelLabel": "Hủy",
                    "fromLabel": "Từ",
                    "toLabel": "Đến",
                    "customRangeLabel": "Tùy chọn",
                    "weekLabel": "Tu",
                    "daysOfWeek": ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
                    "monthNames": ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
                    "firstDay": 1
                },
                ranges: {
                    'Hôm nay': [moment(), moment()],
                    'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 Ngày qua': [moment().subtract(6, 'days'), moment()],
                    '30 Ngày qua': [moment().subtract(29, 'days'), moment()],
                    'Trong tháng': [moment().startOf('month'), moment().endOf('month')],
                    'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                "opens": "left",
                "drops": "auto"
            }, function(start, end, label) {
            });
        });
    }
    function initSearchBoxes() {
      $('[data-search="true"]').each(function () {
        const container = $(this);
        if (container.data('init-search')) return;
        const input = container.find('input');
        const resultBox = container.find('.search-results-box');
        const url = container.data('url');
        const template = container.find('.search-item-template')[0];
        const minLength = container.data('minlength') || 2;
        const highlightColor = container.data('search-color') || 'rgb(0 0 0 / 10%)';
        container.css('position', 'relative');
        container.data('init-search', true);
        let debounceTimer;
        let lastQuery = '';
        let lastFetchedQuery = '';
        let lastResults = null;
        function renderResults(data) {
          resultBox.empty();
          if (Array.isArray(data) && data.length > 0) {
            data.forEach(item => {
              const clone = $(template.content.cloneNode(true));
              clone.find('[data-name]').each(function () {
                const field = $(this).data('name');
                if (item[field] !== undefined) {
                  if (this.tagName === 'IMG') {
                    $(this).attr('src', item[field]);
                  } else {
                    $(this).text(item[field]);
                  }
                } else {
                  if (this.tagName === 'IMG') {
                    $(this).attr('src', '');
                  } else {
                    $(this).text('');
                  }
                }
              });
              clone.find('*').each(function () {
                const el = $(this);
                $.each(this.attributes, function () {
                  const attrName = this.name;
                  const attrValue = this.value;
                  if (attrName.startsWith('data-attr-')) {
                    const realAttr = 'data-' + attrName.slice('data-attr-'.length);
                    const field = attrValue;
                    if (item[field] !== undefined) {
                      el.attr(realAttr, item[field]);
                    } else {
                      el.attr(realAttr, '');
                    }
                    el.removeAttr(attrName);
                  }
                });
              });
              clone.addClass('search-item');
              resultBox.append(clone);
            });
            resultBox.show();
          } else {
            resultBox.html('<div class="search-item p-4 text-center"><img src="/assets/img/no-data.svg" class="w-25"> <strong class="d-block">Không có kết quả</strong></div>').show();
          }
        }
        function performSearch(query) {
          if (query === lastFetchedQuery) return;
          lastFetchedQuery = query;
          resultBox.html('<div class="search-item p-4 text-center"><strong>Đang tìm kiếm...</strong></div>').show();
          $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: { search: query },
            success: function (data) {
              lastResults = data;  // lưu kết quả
              renderResults(data);
            },
            error: function () {
              resultBox.html('<div class="search-item p-4 text-center"><img src="/assets/img/no-data.svg" class="w-25"> <strong class="d-block">Lỗi khi tải dữ liệu</strong></div>').show();
              lastResults = null; // reset kết quả
            }
          });
        }
        function highlightItem(index) {
          const items = resultBox.find('.search-item');
          if (!items.length) return;
          items.css('background', ''); 
          if (index >= 0 && index < items.length) {
            const item = items.eq(index);
            item.css('background', highlightColor);
            const containerTop = resultBox.scrollTop();
            const containerBottom = containerTop + resultBox.innerHeight();
            const itemTop = item.position().top + containerTop;
            const itemBottom = itemTop + item.outerHeight();
            if (itemBottom > containerBottom) {
              resultBox.scrollTop(itemBottom - resultBox.innerHeight());
            } else if (itemTop < containerTop) {
              resultBox.scrollTop(itemTop);
            }
          }
          container.data('highlight-index', index);
        }
        function getHighlightedIndex() {
          const idx = container.data('highlight-index');
          return typeof idx === 'number' ? idx : -1;
        }
        input.on('keyup', function () {
          const query = $(this).val().trim();
          lastQuery = query;
          clearTimeout(debounceTimer);

          debounceTimer = setTimeout(() => {
            if (query.length < minLength) {
              resultBox.hide();
              lastResults = null;
              return;
            }
            performSearch(query);
          }, 300);
        });
        input.on('focus', function () {
          const query = $(this).val().trim();
          if (query.length >= minLength) {
            if (lastResults && lastFetchedQuery === query) {
              renderResults(lastResults);
            } else {
              performSearch(query);
            }
          }
        });
        input.on('keydown', function (e) {
          const items = resultBox.find('.search-item');
          if (!items.length) return;
          let currentIndex = getHighlightedIndex();
          if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentIndex++;
            if (currentIndex >= items.length) currentIndex = 0;
            highlightItem(currentIndex);
          } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentIndex--;
            if (currentIndex < 0) currentIndex = items.length - 1;
            highlightItem(currentIndex);
          } else if (e.key === 'Enter') {
            e.preventDefault();
            if (currentIndex === -1 && items.length > 0) {
              const firstLink = items.eq(0).find('a').first();
              if (firstLink.length) {
                firstLink[0].click();
              } else {
                items.eq(0)[0].click();
              }
            } else if (currentIndex >= 0 && currentIndex < items.length) {
              const link = items.eq(currentIndex).find('a').first();
              if (link.length) {
                link[0].click();
              } else {
                items.eq(currentIndex)[0].click();
              }
            }
          }
        });
        $(document).on('click', function (e) {
          if (!container.is(e.target) && container.has(e.target).length === 0) {
            resultBox.hide();
          }
        });
      });
    }
    function connectQZ() {
      // if (qz.websocket.isActive()) return Promise.resolve();
      // return qz.websocket.connect({ usingSecure: false });
    }
    function print() {
      // Gắn sự kiện click khi DOM sẵn sàng
      // $('.btn-print').on('click', function () {
        // window.print();
      //   const id = $(this).data('id');
      //   const url = $(this).data('url');
      //   const printerName = "print-bill"; // ← thay bằng tên máy in thật

      //   // Kết nối QZ Tray trước khi in
      //   connectQZ().then(() => {
      //     // Gọi server để lấy nội dung HTML
      //     return $.ajax({
      //       url: url,
      //       method: 'GET',
      //     });
      //   }).then((html) => {
      //     const config = qz.configs.create(printerName);

      //     const data = [{
      //       type: 'html',
      //       format: 'plain', // có thể thử 'plain' hoặc 'html'
      //       data: html
      //     }];

      //     return qz.print(config, data);
      //   }).then(() => {
      //     console.log("🖨️ Đã gửi lệnh in");
      //   }).catch((err) => {
      //     console.error("❌ Lỗi khi in:", err);
      //   });
      // });
    }
    function upload(){
        let dropArea = $("#drop-area");
        let fileInput = $("#file-input");
        let uploadBtn = $('[data-action="upload"]');
        let url = uploadBtn.attr("data-url");
        let fileListContainer = $("#file-list");
        let selectedFiles = [];
        let uploadedFiles = new Set();
        dropArea.on("dragover", function (e) {
            e.preventDefault();
            $(this).addClass("bg-light");
        });
        dropArea.on("dragleave", function (e) {
            e.preventDefault();
            $(this).removeClass("bg-light");
        });
        dropArea.on("drop", async function (e) {
            e.preventDefault();
            $(this).removeClass("bg-light");
            let items = e.originalEvent.dataTransfer.items;
            let files = e.originalEvent.dataTransfer.files;
            if (items && items.length > 0) {
                let hasFolder = false;
                let promises = [];
                for (let item of items) {
                    let entry = item.webkitGetAsEntry();
                    if (entry) {
                        if (entry.isDirectory) hasFolder = true;
                        promises.push(traverseFileTree(entry, ""));
                    }
                }
                await Promise.all(promises);
                if (!hasFolder && files.length > 0) {
                    handleFiles(files);
                }
            } else if (files.length > 0) {
                handleFiles(files);
            }
        });
        dropArea.on("click", function () {
            fileInput.click();
        });
        fileInput.on("change", function () {
            handleFiles(this.files);
        });
        async function processDataTransferItems(items) {
            for (let item of items) {
                let entry = item.webkitGetAsEntry();
                if (entry) {
                    await traverseFileTree(entry, "");
                }
            }
            if (selectedFiles.length > 0) {
                uploadBtn.show();
            }
        }
        async function traverseFileTree(item, path) {
            return new Promise((resolve) => {
                if (item.isFile) {
                    item.file((file) => {
                        let relativePath = path + file.name;
                        if (!uploadedFiles.has(relativePath)) { // Kiểm tra file đã upload chưa
                            selectedFiles.push({ file, relativePath });
                            displayFile(file, relativePath);
                        }
                        resolve();
                    });
                } else if (item.isDirectory) {
                    let dirReader = item.createReader();
                    let newPath = path + item.name + "/";
                    dirReader.readEntries(async (entries) => {
                        if (entries.length === 0) {
                            selectedFiles.push({ file: null, relativePath: newPath }); // Đánh dấu thư mục rỗng
                            displayFile(null, newPath);
                        }
                        for (let entry of entries) {
                            await traverseFileTree(entry, newPath);
                        }
                        resolve();
                    });
                } else {
                    resolve();
                }
            });
        }
        function handleFiles(files) {
            Array.from(files).forEach(file => {
                if (!uploadedFiles.has(file.name) && !selectedFiles.some(f => f.file?.name === file.name)) {
                    selectedFiles.push({ file, relativePath: file.name });
                    displayFile(file, file.name);
                }
            });

            if (selectedFiles.length > 0) {
                uploadBtn.show();
            }
        }
        function getFileIcon(file) {
            let fileType = file.type.toLowerCase();
            let fileName = file.name.toLowerCase();

            if (fileType.startsWith("image/")) return URL.createObjectURL(file);
            if (fileType === "application/pdf") return "/assets/icons/pdf.png";
            if (fileType.includes("text")) return "/assets/icons/files.png";
            if (fileType.includes("rar")) return "/assets/icons/rar.png";
            if (fileType.includes("zip")) return "/assets/icons/zip.png";
            if (fileType.includes("audio/")) return "/assets/icons/audio.png";

            // Kiểm tra tất cả định dạng PowerPoint
            if (
                fileName.endsWith(".ppt") ||
                fileName.endsWith(".pptx") ||
                fileName.endsWith(".pps") ||
                fileName.endsWith(".ppsx")
            ) {
                return "/assets/icons/ppt.png";
            }

            // Kiểm tra tất cả định dạng Word
            if (
                fileName.endsWith(".doc") ||
                fileName.endsWith(".docx") ||
                fileName.endsWith(".dot") ||
                fileName.endsWith(".dotx") ||
                fileName.endsWith(".rtf")
            ) {
                return "/assets/icons/doc.png";
            }

            // Kiểm tra tất cả định dạng Excel
            if (
                fileName.endsWith(".xls") ||
                fileName.endsWith(".xlsx") ||
                fileName.endsWith(".xlsm") ||
                fileName.endsWith(".csv")
            ) {
                return "/assets/icons/xls.png";
            }

            // Mặc định là files.png nếu không thuộc các loại trên
            return "/assets/icons/files.png";
        }
        function displayFile(file, displayPath) {
            let fileItem = $("<div>").addClass("file-item border position-relative p-2 rounded-4 w-100 mb-2");

            let fileHtml = `<div class="d-flex justify-content-between align-items-center position-relative z-2">
                <div class="d-flex align-items-center w-75 col-12 text-truncate">
                    ${file ? `<img src="${getFileIcon(file)}" class="width me-2" style="--width:30px;">` : '<i class="ti ti-folder"></i>'}
                    <div class="col-12 text-truncate"><span>${displayPath}</span><span class="text-danger small file-error d-block"></span></div>
                </div>
                <div class="file-action">
                    <button class="removeItem btn p-0 border-0"><i class="ti ti-trash fs-4 text-danger"></i></button>
                </div>
            </div>`;

            fileItem.append(fileHtml);
            fileListContainer.append(fileItem);

            fileItem.find(".removeItem").on("click", function (e) {
                e.stopPropagation();
                selectedFiles = selectedFiles.filter(f => f.relativePath !== displayPath);
                fileItem.remove();
                if (selectedFiles.length === 0) {
                    uploadBtn.hide();
                }
            });
        }
        uploadBtn.on("click", function () {
            if (selectedFiles.length === 0) return;
            uploadBtn.prop("disabled", true);
            let newFilesToUpload = selectedFiles.filter(f => !uploadedFiles.has(f.relativePath));

            if (newFilesToUpload.length === 0) {
                uploadBtn.prop("disabled", false);
                return;
            }
            uploadFiles(selectedFiles.indexOf(newFilesToUpload[0]));
        });
        function uploadFiles(index) {
            if (index >= selectedFiles.length) {
                uploadBtn.prop("disabled", false);
                let load = uploadBtn.attr("data-load");
                pjaxConfig(uploadBtn);
                pjax.loadUrl(load === 'this' ? '' : load);
                return;
            }
            let { file, relativePath } = selectedFiles[index];
            if (uploadedFiles.has(relativePath)) {
                uploadFiles(index + 1);
                return;
            }
            let formData = new FormData();
            formData.append("path", relativePath); 
            if (file) {
                formData.append("file", file);
            }
            let progressBar = $("<div>").addClass("progress position-absolute bg-body top-0 start-0 w-100 h-100 rounded-4")
                .append($("<div>").addClass("progress-bar bg-primary bg-opacity-10 progress-bar-striped progress-bar-animated"));
            fileListContainer.children().eq(index).append(progressBar);
            progressBar.show();
            fileListContainer.children().eq(index).find(".removeItem").hide();
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                xhr: function () {
                    let xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (e) {
                        if (e.lengthComputable) {
                            let percent = Math.round((e.loaded / e.total) * 100);
                            progressBar.children(".progress-bar").css("width", percent + "%");
                            let progressText = fileListContainer.children().eq(index).find(".file-action .progress-text");
                            if (progressText.length) {
                                progressText.text(percent + "%");
                            } else {
                                fileListContainer.children().eq(index).find(".file-action").append('<span class="fs-6 fw-bold text-primary progress-text">' + percent + '%</span>');
                            }
                        }
                    }, false);
                    return xhr;
                },

                success: function (response) {
                    if (response.status === 'success') {
                        progressBar.children(".progress-bar").removeClass("bg-primary").addClass("bg-success");
                        uploadedFiles.add(relativePath);
                        fileListContainer.children().eq(index).find(".removeItem").remove();
                        fileListContainer.children().eq(index).find(".file-action .progress-text").remove();
                        fileListContainer.children().eq(index).find(".file-action").append('<i class="ti ti-circle-check fs-2 text-success"></i>');
                    }
                    else {
                        progressBar.children(".progress-bar").removeClass("bg-primary").addClass("bg-danger");
                        fileListContainer.children().eq(index).find(".removeItem").show();
                    fileListContainer.children().eq(index).find(".file-error").text(response.content);
                        fileListContainer.children().eq(index).find(".file-action .progress-text").remove();
                        fileListContainer.children().eq(index).find(".file-action .removeItem").html('<i class="ti ti-xbox-x fs-2 text-danger"></i>');
                    }
                    uploadFiles(index + 1);
                    
                },
                error: function () {
                    progressBar.children(".progress-bar").css("width", "100%");
                    progressBar.children(".progress-bar").removeClass("bg-primary").addClass("bg-danger");
                    fileListContainer.children().eq(index).find(".removeItem").show();
                    fileListContainer.children().eq(index).find(".file-error").text('Error Connection');
                    fileListContainer.children().eq(index).find(".file-action .progress-text").remove();
                    fileListContainer.children().eq(index).find(".file-action .removeItem").html('<i class="ti ti-xbox-x fs-2 text-danger"></i>');
                    uploadFiles(index + 1);
                }
            });
        }
    }
    function uploadImages() {
        $(".upload-file").off("change").on("change", function (event) {
            handleFileUpload(event, $(this));
        });
        $("#remove-upload-file").off("click").on("click", function () {
            resetFileInput($(this));
        });
        function handleFileUpload(event, $input) {
            let files = event.type === "drop" ? event.originalEvent.dataTransfer.files : event.target.files;
            if (!files || files.length === 0) return;

            let file = files[0];
            let reader = new FileReader();
            let $uploadBox = $input.parents(".upload-files");
            let $showFile = $uploadBox.find(".show-file");
            $uploadBox.addClass("bg-light");
            $input.prop("disabled", false);
            $uploadBox.find(".input-file").hide();
            $uploadBox.find(".upload-progress").show();
            $showFile.show().find(".progress-bar").css("width", "0%").show();
            reader.onloadend = function () {
                let base64String = reader.result;
                $showFile.find(".images-container").attr("src", base64String);
                $showFile.find(".audio-container").attr("data-audio", base64String);
                let router = $input.attr("data-router");
                if (router) {
                    let formData = new FormData();
                    formData.append("file", file);
                    $.ajax({
                        type: "POST",
                        url: router,
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        xhr: function () {
                            let xhr = new XMLHttpRequest();
                            xhr.upload.addEventListener("progress", function (evt) {
                                if (evt.lengthComputable) {
                                    let percentComplete = (evt.loaded / evt.total) * 100;
                                    $showFile.find(".progress-bar").css("width", percentComplete + "%");
                                }
                            });
                            return xhr;
                        },
                        success: function (response) {
                            topbar.hide();
                            if (response.status === "error") {
                                swal_error(response.content);
                                $input.prop("disabled", false);
                            } else if (response.status === "success") {
                                let fileUrl = "/" + response.url;
                                $showFile.find(".audio-container").attr("data-audio", fileUrl);
                                $showFile.find(".value-file").val(response.url);
                                $uploadBox.find(".upload-progress").hide();
                                $input.val("");
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            console.error("Upload error:", thrownError);
                        },
                        complete: function () {
                            $uploadBox.removeClass("bg-light");
                        },
                    });
                }
            };
            reader.onerror = function () {
                console.error("FileReader Error");
                topbar.hide();
                swal_error("Failed to read file. Please try again.");
                $uploadBox.removeClass("bg-light");
            };
            reader.readAsDataURL(file);
        }
        function resetFileInput($button) {
            let $uploadBox = $button.parents(".upload-files");
            let $showFile = $uploadBox.find(".show-file");

            $uploadBox.find(".input-file").show();
            $showFile.hide().find(".images-container, .audio-container").attr({ src: "", "data-audio": "" });
            $showFile.find(".value-file").val("");
            $uploadBox.removeClass("bg-light");
        }
        $(".upload-files").on("dragover dragleave drop", function (event) {
            event.preventDefault();
            event.stopPropagation();
            if (event.type === "dragover") {
                $(this).addClass("bg-light");
            } else if (event.type === "dragleave") {
                $(this).removeClass("bg-light");
            } else if (event.type === "drop") {
                let $input = $(this).find(".upload-file");
                handleFileUpload(event, $input);
            }
        });
    }
    function uploadImagesMulti() {
        $(".upload-file-multi").off("change").on("change", function (event) {
            handleFileUploadMulti(event, $(this));
        });
        $(document).off("click", '.remove-upload-file-multi').on("click", '.remove-upload-file-multi', function () {
            let imageContainer = $(this).parents(".images-container");
            imageContainer.hide().find("img").attr("src",'');
            imageContainer.find(".value-file").val("");
        });
        function handleFileUploadMulti(event, $input) {
            let files = event.type === "drop" ? event.originalEvent.dataTransfer.files : event.target.files;
            if (!files || files.length === 0) return;

            // let file = files[0];
            let $uploadBox = $input.parents(".upload-files-multi");
            let $showFile = $uploadBox.find(".show-file");
            $uploadBox.addClass("bg-light");
            $input.prop("disabled", false);
            // $uploadBox.find(".input-file").hide();
            $showFile.show().find(".progress-bar").css("width", "0%").show();
            Array.from(files).forEach((file) => {
              let reader = new FileReader();
              reader.onloadend = function () {
                  let base64String = reader.result;
                  let $imgTemplate = $showFile.find(".images-container").last().clone().show();
                  $imgTemplate.find("img").attr("src", base64String);
                  $showFile.append($imgTemplate);
                  let router = $input.attr("data-router");
                  if (router) {
                      let formData = new FormData();
                      formData.append("file", file);
                      $.ajax({
                          type: "POST",
                          url: router,
                          data: formData,
                          cache: false,
                          contentType: false,
                          processData: false,
                          xhr: function () {
                              let xhr = new XMLHttpRequest();
                              xhr.upload.addEventListener("progress", function (evt) {
                                  if (evt.lengthComputable) {
                                      let percentComplete = (evt.loaded / evt.total) * 100;
                                      $imgTemplate.find(".progress-bar").css("width", percentComplete + "%");
                                  }
                              });
                              return xhr;
                          },
                          success: function (response) {
                              topbar.hide();
                              if (response.status === "error") {
                                  swal_error(response.content);
                                  $input.prop("disabled", false);
                              } else if (response.status === "success") {
                                  let fileUrl = "/" + response.url;
                                  $imgTemplate.find(".value-file").val(response.url);
                                  $imgTemplate.find(".upload-progress").hide();
                                  $input.val("");
                              }
                          },
                          error: function (xhr, ajaxOptions, thrownError) {
                              console.error("Upload error:", thrownError);
                          },
                          complete: function () {
                              $uploadBox.removeClass("bg-light");
                          },
                      });
                  }
              };
              reader.onerror = function () {
                  console.error("FileReader Error");
                  topbar.hide();
                  swal_error("Failed to read file. Please try again.");
                  $uploadBox.removeClass("bg-light");
              };
              reader.readAsDataURL(file);
            });
        }
        $(".upload-files-multi").on("dragover dragleave drop", function (event) {
            event.preventDefault();
            event.stopPropagation();
            if (event.type === "dragover") {
                $(this).addClass("bg-light");
            } else if (event.type === "dragleave") {
                $(this).removeClass("bg-light");
            } else if (event.type === "drop") {
                let $input = $(this).find(".upload-file-multi");
                handleFileUploadMulti(event, $input);
            }
        });
    }
    function DomDataAction($scope = $(document)) {
        $scope.find('[data-action="load"]').each(function () {
            handleLoad($(this));
        });
    }
    function dataAction(){
        $(document).off('change', '[data-action="change-load"], [data-action="click-load"]').on('change click', '[data-action="change-load"], [data-action="click-load"]', function (e) {
            const $trigger = $(this);
            const action = $trigger.data('action');
            const triggerType = action === 'change-load' ? 'change' : 'click';
            console.log(triggerType);
            if (e.type !== triggerType) return; 
            handleLoad($trigger);
        });
        $(document).off('click', '[data-action="clone"]').on('click', '[data-action="clone"]', function () {
          const $trigger = $(this);
          const $target = $($trigger.data('target'));
          const $template = $target.find($trigger.data('clone')).last();
          if ($template.length === 0) {
              console.warn('Không tìm thấy phần tử để clone');
              return;
          }
          $template.find('select[data-select]').selectpicker('destroy');
          const $newRow = $template.clone();
          $template.find('select[data-select]').selectpicker();
          $newRow.find('input, select, textarea').each(function () {
              const $input = $(this);
              if (!$input.attr('data-keep')) {
                  $input.val('');
              }
              const name = $input.attr('name');
              if (name && name.includes('[')) {
                  const newName = name.replace(/\[[^\]]*\]/, '[]');
                  $input.attr('name', newName);
              }
          });
          $target.append($newRow);
          $newRow.find('select[data-select]').selectpicker();
      });

        $(document).off('click', '[data-action="deleted-clone"]').on('click', '[data-action="deleted-clone"]', function () {
            const $btn = $(this);
            const targetSelector = $btn.data('target');
            const removeType = $btn.data('remove') || 'remove';
            const valueTarget = $btn.data('value');
            const closestSelector = $btn.data('closest') || '.row'; // 👈 thêm config closest

            let $target;

            if (targetSelector === 'this') {
                $target = $btn.closest(closestSelector);
            } else {
                $target = $(targetSelector).first();
            }

            if ($target.length === 0) {
                console.warn('Không tìm thấy phần tử để xóa/ẩn');
                return;
            }

            if (removeType === 'remove') {
                $target.remove();
            } else if (removeType === 'hidden') {
                $target.hide();
                if (valueTarget) {
                    const value = $(valueTarget).val() || $(valueTarget).text();
                    $(valueTarget).val(value + ' (đã ẩn)');
                }
            }
        });
        $(document).off('click', '[data-action="copy"]').on('click', '[data-action="copy"]', function () {
            let $target = $(this).data('target');
            let $attr = $(this).data('attr');
            let $copyElement = $($target);
            let textToCopy = '';
            if ($attr) {
                textToCopy = $copyElement.attr($attr) || '';
            } else {
                textToCopy = $copyElement.text().trim() || $copyElement.html().trim();
            }
            if (textToCopy) {
                navigator.clipboard.writeText(textToCopy).then(() => {
                    alert('Copied: ' + textToCopy);
                }).catch(err => {
                    console.error('Failed to copy:', err);
                });
            }
        });
        $(document).off('click submit', '[data-action]').on('click submit', '[data-action]', function (event) {
            let $this = $(this);
            let type = $this.data('action');
            let $url = $this.data('url');
            let form = $this.closest('form');
            let checkbox = $this.data('checkbox');
            if (type === 'blur' || type === 'change') return;
            // $this.prop('disabled', true);
            if (!$this.is(':checkbox, :radio')) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
            
            if (checkbox) {
                let updatedUrl = handleCheckboxSelection(checkbox, $this, $url);
                if (!updatedUrl) return;
                $url = updatedUrl;
            }
            let options = extractOptions($this);
            let formData = new FormData();
            let dsPOST = false;
            if (type === 'submit' || type === 'click' || type === 'social') {
                dsPOST = true;
                $url = $url || (form.length ? form.attr('action') : '');
                formData = handleFormSubmission(type, form, options, $this);
            } else if (type === 'modal' || type === 'offcanvas') {
                handleModalOrOffcanvas(type, $url, options, $this);
                return;
            }
            if (options.socket && dsPOST) sendSocketData(formData, options);
            if ($url && dsPOST) sendAjaxRequest($url, formData, options, $this);
            if (!$url && options.function && dsPOST) {
                handleTargetFunction(options, $this);
                return;
            }
        });
        ['blur', 'change'].forEach(action => {
            $(document).off(action, `[data-action="${action}"]`).on(action, `[data-action="${action}"]`, function () {
                let $this = $(this);
                let $url = $this.data('url');
                if (!$url) return;
                let formData = new FormData();
                let options = extractOptions($this);
                let formAttr = options.form;
                if (formAttr) handleFormData(formData, formAttr, $this);
                sendAjaxRequest($url, formData, options, $this);
            });
        });
    }
    function handleLoad($el) {
        const url = $el.data('url') || $el.data('load-url');
        const targetSelector = $el.data('target') || $el.data('load-target') || $el.data('html');
        let dataSend = {};
        if ($el.is('select') || $el.is('input') || $el.is('textarea')) {
            const name = $el.attr('name') || 'value';
            dataSend[name] = $el.val();
        }
        if (!url) {
            console.warn('Không có URL để load.');
            return;
        }
        const $target = targetSelector ? $(targetSelector) : $el;
        if ($target.length === 0) {
            console.warn('Không tìm thấy selector mục tiêu:', targetSelector);
            return;
        }
        $target.html('<div class="spinner-load">Loading...</div>');
        $.post(url, dataSend, function (response) {
            $target.html(response.content || response);
            const $newContent = $(response.content || response);
            DomDataAction($newContent);
            if (typeof pjax !== 'undefined' && typeof pjax.refresh === 'function') {
                selected();
                upload();
                uploadImages();
            }
        }).fail(function (xhr, status, error) {
            $target.html('<div class="text-danger">Tải dữ liệu thất bại</div>');
            console.error('Lỗi khi load:', error);
        });
    }
    function extractOptions($element) {
        return {
            alert: $element.data("alert"),
            load: $element.data("load"),
            form: $element.data("form"),
            function: $element.data("function"),
            history: $element.data("pjax-history") !== false,
            selector: $element.data("selector"),
            multi: $element.data("multi"),
            socket: $element.data("socket"),
            socketCode: $element.data("socket-code") || 'send',
            stream: $element.data("stream") || 'false',
            remove: $element.data("remove"),
            print: $element.data("print"),
            printTime: $element.data("print-time"),
        };
    }
    function handleFormSubmission(type, form, options, $this) {
        if (type === 'submit' && form.length) {
            return new FormData(form[0]); // Trả về FormData đúng cách
        } 
        let formData = new FormData();
        if (type === 'click' && options.form) {
            handleFormData(formData, options.form, $this);
        } else if (type === 'social') {
            handleSocialData(formData, $this);
        }
        return formData;
    }
    function handleCheckboxSelection(checkbox, $this, $url) {
        let selected = $(`${checkbox}:checked`);
        if (selected.length === 0) {
            swal_error('Vui lòng chọn dữ liệu');
            topbar.hide();
            $this.removeAttr('disabled');
            return false;
        }
        let boxid = selected.map((_, el) => el.value).get().join(',');
        let updatedUrl = `${$url}?box=${boxid}`;
        $this.data('url', updatedUrl);
        return updatedUrl;
    }
    function handleFormData(formData, dataForm, $this) {
      try {
        let dataFormString = typeof dataForm === 'object' ? JSON.stringify(dataForm) : dataForm;
        let parsedData = JSON.parse(dataFormString);
        for (let key in parsedData) {
            let value = parsedData[key];
            let $el;
            const getByMethod = ($el, method) => {
                if (!$el || $el.length === 0) return '';
                return $el[method] ? $el[method]() : '';
            };
            if (value.startsWith("closest:")) {
                let expr = value.replace(/^closest:/, '').trim();
                let attrMatch = expr.match(/^\[data-([^\]]+)\]\.attr$/);
                let methodMatch = expr.match(/^(.+)\.(val|html|text)$/);
                if (attrMatch) {
                    let attr = attrMatch[1];
                    $el = $this.closest(`[data-${attr}]`);
                    formData.append(key, $el.attr(`data-${attr}`));
                } else if (methodMatch) {
                    let selector = methodMatch[1];
                    let method = methodMatch[2];
                    $el = $this.closest(selector);
                    formData.append(key, getByMethod($el, method));
                } else {
                    $el = $this.closest(expr);
                    formData.append(key, $el.text().trim());
                }
                continue;
            }
            if (value.startsWith("parent:")) {
                let expr = value.replace(/^parent:/, '').trim();
                let attrMatch = expr.match(/^\[data-([^\]]+)\]\.attr$/);
                let methodMatch = expr.match(/^(.+)\.(val|html|text)$/);
                if (attrMatch) {
                    let attr = attrMatch[1];
                    $el = $this.parent().find(`[data-${attr}]`);
                    formData.append(key, $el.attr(`data-${attr}`));
                } else if (methodMatch) {
                    let selector = methodMatch[1];
                    let method = methodMatch[2];
                    $el = $this.parent().find(selector);
                    formData.append(key, getByMethod($el, method));
                } else {
                    $el = $this.parent().find(expr);
                    formData.append(key, $el.text().trim());
                }
                continue;
            }
            if (value.startsWith("find:")) {
                let expr = value.replace(/^find:/, '').trim();
                let attrMatch = expr.match(/^\[data-([^\]]+)\]\.attr$/);
                let methodMatch = expr.match(/^(.+)\.(val|html|text)$/);
                if (attrMatch) {
                    let attr = attrMatch[1];
                    $el = $this.find(`[data-${attr}]`);
                    formData.append(key, $el.attr(`data-${attr}`));
                } else if (methodMatch) {
                    let selector = methodMatch[1];
                    let method = methodMatch[2];
                    $el = $this.find(selector);
                    formData.append(key, getByMethod($el, method));
                } else {
                    $el = $this.find(expr);
                    formData.append(key, $el.text().trim());
                }
                continue;
            }
            let methodMatch = value.match(/^this\.(val|html|text)$/);
            if (methodMatch) {
                let method = methodMatch[1];
                formData.append(key, $this[method]());
                continue;
            }
            let attrMatch = value.match(/^(.*)\.attr\(([^)]+)\)$/);
            if (attrMatch) {
                let selector = attrMatch[1] === 'this' ? $this : $(attrMatch[1]);
                let attrName = attrMatch[2].replace(/['"]/g, '');
                formData.append(key, selector.attr(attrName));
                continue;
            }
            let normalMethodMatch = value.match(/^(.*)\.(val|html|text)$/);
            if (normalMethodMatch) {
                let selector = normalMethodMatch[1] === 'this' ? $this : $(normalMethodMatch[1]);
                let method = normalMethodMatch[2];
                formData.append(key, selector[method]());
                continue;
            }
            if (/^\[data-[^\]]+\]$/.test(value)) {
                let attrName = value.match(/^\[data-([^\]]+)\]$/)[1];
                $el = $this.find(`[data-${attrName}]`);
                if ($el.length === 0) $el = $(`[data-${attrName}]`);
                formData.append(key, $el.attr(`data-${attrName}`));
                continue;
            }
            formData.append(key, value);
        }
      } catch (error) {
        console.error("Error parsing data-form:", error, "Data received:", dataForm);
      }
        // "key": "this.text"  Lấy nội dung .text() từ chính $this
        // "key": "this.val" Lấy giá trị .val() từ chính $this
        // "key": "selector.text"  Lấy .text() từ phần tử được chỉ định
        // "key": "selector.val" Lấy .val() từ phần tử được chỉ định
        // "key": "selector.attr('name')"  Lấy thuộc tính từ selector
        // "key": "[data-abc]" Lấy data-abc từ phần tử có attribute data-abc
        // "key": "closest:selector.text"  Tìm phần tử cha gần nhất rồi lấy .text()
        // "key": "closest:[data-abc].attr"  Tìm phần tử cha gần nhất có data-abc, lấy data-abc
        // "key": "parent:selector.val"  Từ phần tử cha trực tiếp, tìm selector con và lấy .val()
        // "key": "parent:[data-abc].attr" Tìm phần tử con trong phần tử cha gần nhất và lấy data-abc
        // "key": "find:selector.html" Tìm selector bên trong $this rồi lấy .html()
    }
    function handleSocialData(formData, $this) {
        let parent = $this.parents(".social-create");
        formData.append('content', parent.find(".social-post").html());
        formData.append('type', parent.find("input[name='type']").val());
        formData.append('access', parent.find("input[name='access']:checked").val());
        let mediaType = formData.get('type');
        if (mediaType === 'audio') {
            formData.append('voice', parent.find("input[name='voice']").val());
        } else if (mediaType === 'video') {
            formData.append('video', parent.find("input[name='video']").val());
        } else if (mediaType === 'images') {
            parent.find("input[name='images[]']").each(function () {
                let imageUrl = $(this).val();
                if (imageUrl) formData.append('images[]', imageUrl);
            });
        }
    }
    function handleModalOrOffcanvas(type, $url, options = {}, $this) {
        let viewClass = `${type}-view${options?.multi ? '-' + options.multi : '-views'}`;
        if (!$(`.${viewClass}`).length) $('<div>').addClass(viewClass).appendTo('body');
        if (!$url) return;
        let maxZIndex = Math.max(
            ...$('.modal:visible, .offcanvas.show').map(function () {
                return parseInt($(this).css('z-index')) || 1040;
            }).get(),
            1040
        );
        let zIndex = maxZIndex + 20;
        let backdropClass = `${type}-backdrop-${Date.now()}`;

        $(`.${viewClass}`).load($url, function (response, status) {
            if (status === "error") {
                $(`.${viewClass}`).remove();
                swal_error('Failed to load content');
            } else {
                let $target = $(`.${viewClass} .${type}-load`);
                pjax.refresh();
                $target.css('z-index', zIndex);
                setTimeout(() => {
                    let $backdrop = $('.modal-backdrop, .offcanvas-backdrop').not(`.${backdropClass}`).last();
                    if ($backdrop.length) {
                        $backdrop.addClass(backdropClass).css('z-index', zIndex - 10);
                    }
                }, 50);
                if ($target.length) {
                    $target.removeAttr('aria-hidden');
                    if (type === 'modal') {
                        let modalInstance = bootstrap.Modal.getOrCreateInstance($target[0]);
                        modalInstance.show();
                        $target.on('shown.bs.modal', () => topbar.hide());
                    } else if (type === 'offcanvas') {
                        let offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance($target[0]);
                        offcanvasInstance.show();
                        $target.on('shown.bs.offcanvas', () => topbar.hide());
                    }
                }
                selected();
                editor();
                upload();
                uploadImages();
                uploadImagesMulti();
                DomDataAction();
                datatable($target);
                number();
                swiper();
                step();
                print();
                pjaxConfig($this);
                $target.on(`hidden.bs.${type}`, function () {
                    $target.find('[data-table]').each(function () {
                        if ($.fn.dataTable.isDataTable(this)) {
                            $(this).DataTable().destroy(true);
                        }
                    });
                    $(`.${viewClass}`).remove();
                    $(`.${backdropClass}`).remove();
                    $(document).find('rte-floatpanel').remove();
                    $this?.removeAttr('disabled');
                    pjax.options.history = true;
                });
            }
        });
    }
    function handleAjaxResponse(response, options, $this) {
        if (response.status === 'error') {
            swal_error(response.content);
            return;
        }
        if (response.status === 'success') {
            pjaxConfig($this);
            if (options.remove) $(options.remove).remove();
            if (options.load) {
                if (response.load === 'true') {
                    window.location.href = options.load;
                } else if (response.load === 'url') {
                    window.location.href = response.url;
                } else if (response.load === 'ajax') {
                    pjax.loadUrl(response.url);
                } else {
                    pjax.loadUrl(options.load === 'this' ? '' : options.load);
                }
            }
            if (options.alert) swal_success(response.content, $this);
            if(options.print=== 'modal'){
                $('<div class="modal-views-print"></div>').appendTo('body');
                $('.modal-views-print').load(response.print, function(response, status, req) {
                    $('.modal-load').modal('show');
                    $('.modal-load').on('shown.bs.modal', function (e) {
                      topbar.hide();
                      window.print();
                    });
                }).on('hidden.bs.modal', function (e) {
                  $('.modal-load').modal('hide');
                  $('.modal-views-print').remove();
                });
            }
        }
    }
    function sendSocketData(formData, options) {
        let jsonObject = Object.fromEntries(formData.entries());
        let socketPayload = {
            status: "success",
            sender: active,
            token: getToken,
            stream: options.stream,
            router: options.socket,
            data: jsonObject,
            code: options.socketCode
        };
        send(socketPayload);
    }
    function sendAjaxRequest(url, formData, options, $this) {
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                handleAjaxResponse(response, options, $this);
            },
            error: function () {
                console.error("AJAX request failed");
            },
            complete: function () {
                topbar.hide();
                $this.removeAttr('disabled');
            }
        });
    }
    function handleTargetFunction(options, $element) {
        try {
            if (options.function.endsWith("()")) {
                let functionName = options.function.replace("()", "").trim();
                let allowedFunctions = {
                    assets_images: assets_images,
                };
                if (allowedFunctions[functionName]) {
                    allowedFunctions[functionName](options, $element);
                } else {
                    console.warn(`Function ${functionName} is not allowed`);
                }
            }
        } catch (error) {
            console.error("Error executing function from data-function:", error);
        }
    }
    function assets_images(options, $element) {
        $('.upload-files').find('.input-file').hide();
        $('.upload-files').find('.show-file').show();
        $('.upload-files').find('.show-file').find('.images-container').attr("src", "");
        $('.upload-files').find('.show-file').find(".value-file").val("");
        let formData = new FormData();
        handleFormData(formData, options.form, $element);
        let base64String = formData.get("data");
        if (base64String) {
            $('.upload-files').find('.show-file').find('.images-container').attr("src", '/'+base64String);
            $('.upload-files').find('.show-file').find(".value-file").val(base64String);
        } else {
            console.warn("No 'data' key found in formData.");
        }
    }
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        navigator.serviceWorker.register('/sw.js')
        .then(function(registration) {
            // console.log('Service Worker đã được đăng ký:', registration);
        })
        .catch(function(error) {
            // console.error('Lỗi đăng ký Service Worker:', error);
        });
    }
});
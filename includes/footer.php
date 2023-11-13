    <!-- Custom JS -->
    <!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script> -->
    <!-- <script src="./assets/js/admin.js"></script> -->

    <!-- <script src="/assets/vendors/bootstrap/bootstrap.min.js"></script> -->
    <script src="/assets/js/vendor.min.js"></script>
    <script src="/assets/js/theme.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
      (function() {
        // STYLE SWITCHER
        // =======================================================
        const $dropdownBtn = document.getElementById('selectThemeDropdown') // Dropdowon trigger
        const $variants = document.querySelectorAll(`[aria-labelledby="selectThemeDropdown"] [data-icon]`) // All items of the dropdown
        
        window.onload = function () {
          // INITIALIZATION OF NAVBAR VERTICAL ASIDE
          // =======================================================
          new HSSideNav('.js-navbar-vertical-aside').init()
          // INITIALIZATION OF FORM SEARCH
          // =======================================================
          // new HSFormSearch('.js-form-search')
          // INITIALIZATION OF BOOTSTRAP DROPDOWN
          // =======================================================
          HSBsDropdown.init()
          // INITIALIZATION OF SELECT
          // =======================================================
          HSCore.components.HSTomSelect.init('.js-select')
          // INITIALIZATION OF NAV SCROLLER
          // =======================================================
          new HsNavScroller('.js-nav-scroller')
        }

        // Function to set active style in the dorpdown menu and set icon for dropdown trigger
        const setActiveStyle = function() {
          $variants.forEach($item => {
            if ($item.getAttribute('data-value') === HSThemeAppearance.getOriginalAppearance()) {
              $dropdownBtn.innerHTML = `<i class="${$item.getAttribute('data-icon')}" />`
              return $item.classList.add('active')
            }

            $item.classList.remove('active')
          })
        }

        // Add a click event to all items of the dropdown to set the style
        $variants.forEach(function($item) {
          $item.addEventListener('click', function() {
            HSThemeAppearance.setAppearance($item.getAttribute('data-value'))
          })
        })

        // Call the setActiveStyle on load page
        setActiveStyle()

        // Add event listener on change style to call the setActiveStyle function
        window.addEventListener('on-hs-appearance-change', function() {
          setActiveStyle()
        });

        HSCore.components.HSDatatables.init('.js-datatable');
      })()
    </script>

    </body>

    </html>
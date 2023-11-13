    <script src="/assets/js/vendor.min.js"></script>
    <script src="/assets/js/theme.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="./assets/js/admin.js"></script>

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
          const searchField = new HSFormSearch('.js-form-search').getItem(0);
          const serarchDropDown = document.querySelector('#searchDropdownMenu .card-body');
          searchField.$el.addEventListener('input', async (e) => {
            console.log(e);
            if(e.srcElement.value.length == 10) {
              serarchDropDown.innerHTML = '<p>Введите ИНН</p>';
              const searchElem = document.createElement('div');
              searchElem.classList.add('dropdown-item', 'bg-transparent', 'text-wrap');
              const res = await GetCompData(e.srcElement.value);
              const debtor = res.data;
              if(debtor) {
                searchElem.innerHTML = `
                      <a href="#" id="${debtor.ИНН}" class="btn btn-soft-dark btn-xs rounded-pill" data-bs-inn="${debtor.ИНН}" data-bs-toggle="modal" data-bs-target="#editUserModal" >
                      ${debtor.НаимСокр} <i class="bi-search ms-1"></i>
                      </a>
                `;
                serarchDropDown.append(searchElem);           
              }
            } else {
              serarchDropDown.innerHTML = '<p>Введите ИНН</p>';
            }

          });

          document.querySelector('#clearSearchResultsIcon').addEventListener('click', async (e) => {
            serarchDropDown.innerHTML = '<p>Введите ИНН</p>';
          });

          // 2723204352


          // INITIALIZATION OF BOOTSTRAP DROPDOWN
          // =======================================================
          HSBsDropdown.init()
          // INITIALIZATION OF SELECT
          // =======================================================
          HSCore.components.HSTomSelect.init('.js-select')
          // INITIALIZATION OF NAV SCROLLER
          // =======================================================
          new HsNavScroller('.js-nav-scroller')

          var tooltipTriggerList = [].slice.call(document.querySelectorAll('title'))
          var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
              return new bootstrap.Tooltip(tooltipTriggerEl)
          });
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
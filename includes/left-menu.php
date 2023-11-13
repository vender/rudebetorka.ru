<!-- Navigation -->
<aside class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-dark bg-dark">
    <div class="navbar-vertical-container">
        <div class="navbar-vertical-footer-offset">
            <!-- Logo -->
            <a class="navbar-brand" href="#" aria-label="Front">
                <span class="navbar-brand-logo" data-hs-theme-appearance="default">Дебиторка</span>
                <span class="navbar-brand-logo" data-hs-theme-appearance="dark">Дебиторка</span>
                <span class="navbar-brand-logo-mini" data-hs-theme-appearance="default">Д</span>
                <span class="navbar-brand-logo-mini" data-hs-theme-appearance="dark">Д</span>
            </a>
            <!-- End Logo -->

            <!-- Navbar Vertical Toggle -->
            <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
            <i class="bi-arrow-bar-left navbar-toggler-short-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Свернуть"></i>
            <i class="bi-arrow-bar-right navbar-toggler-full-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Развернуть"></i>
            </button>
            <!-- End Navbar Vertical Toggle -->

            <div class="navbar-vertical-content">
                <div id="navbarVerticalMenu" class="nav nav-pills nav-vertical card-navbar-nav">

                    <div class="nav-item">
                        <a class="nav-link <?php echo (CURRENT_PAGE == "index.php") ? 'active' : ''; ?>" href="index.php" data-placement="left">
                            <i class="bi me-2 bi-speedometer2 nav-icon"></i>
                            <span class="nav-link-title">Статистика</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a class="nav-link <?php echo (CURRENT_PAGE == "inwork") ? 'active' : ''; ?>" href="inwork" data-placement="left">
                            <i class="bi me-2 bi-briefcase nav-icon"></i>
                            <span class="nav-link-title">В работе</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="torgi" class="nav-link <?php echo (CURRENT_PAGE == "customers" || CURRENT_PAGE == "add_customer.php") ? 'active' : ''; ?>" data-placement="left">
                            <i class="bi me-2 bi-person-circle nav-icon"></i>
                            <span class="nav-link-title">Лоты</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="admin_users.php" class="nav-link <?php echo (CURRENT_PAGE == "admin_users.php" || CURRENT_PAGE == "add_admin.php") ? 'active' : ''; ?>" data-placement="left">
                            <i class="bi me-2 bi-people-fill nav-icon"></i><span class="nav-link-title">Пользователи</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#dashboard-settings" <?php echo (CURRENT_PAGE == "statuses" || CURRENT_PAGE == "templates" || CURRENT_PAGE == "stats") ? 'aria-expanded="true"' : 'aria-expanded="false"'; ?>>
                            <i class="bi bi-sliders nav-icon"></i>
                            <span class="nav-link-title">Настройки</span>
                        </a>

                        <div class="nav-collapse collapse <?php echo (CURRENT_PAGE == "statuses") ? 'show' : ''; ?>" id="dashboard-settings" bis_skin_checked="1">
                            <a href="settings/statuses" class="nav-link <?php echo (CURRENT_PAGE == "statuses") ? 'active' : ''; ?>">Статусы</a>
                        </div>
                        
                    </div>

                    <!-- <div class="nav-item">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#dashboard-sender" <?php echo (CURRENT_PAGE == "sender" || CURRENT_PAGE == "templates" || CURRENT_PAGE == "stats") ? 'aria-expanded="true"' : 'aria-expanded="false"'; ?>>
                            <i class="bi-mailbox nav-icon"></i>
                            <span class="nav-link-title">Рассылки</span>
                        </a>

                        <div class="nav-collapse collapse <?php echo (CURRENT_PAGE == "sender" || CURRENT_PAGE == "templates" || CURRENT_PAGE == "stats") ? 'show' : ''; ?>" id="dashboard-sender" bis_skin_checked="1">
                            <a href="sender" class="nav-link <?php echo (CURRENT_PAGE == "sender") ? 'active' : ''; ?>">Отправка</a>
                            <a href="sender/templates" class="nav-link <?php echo (CURRENT_PAGE == "templates") ? 'active' : ''; ?>">Шаблоны</a>
                            <a href="sender/stats" class="nav-link <?php echo (CURRENT_PAGE == "stats") ? 'active' : ''; ?>">Статистика</a>
                        </div>
                        
                    </div> -->

                </div>

            </div>

        </div>
    </div>
</aside>
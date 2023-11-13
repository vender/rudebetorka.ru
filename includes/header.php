<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Дебиторская задолженность</title>
    <base href="<?php echo BASE_URI ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/assets/css/vendor.min.css" rel="stylesheet">
    <link rel="preload" href="/assets/css/theme.min.css" data-hs-appearance="default" as="style">
    <link rel="preload" href="/assets/css/theme-dark.min.css" data-hs-appearance="dark" as="style">
    <!-- <link href="/assets/css/admin.css" rel="stylesheet"> -->

    <style data-hs-appearance-onload-styles>
        * {
            transition: unset !important;
        }

        body {
            opacity: 0;
        }
    </style>

    <script>
        window.hs_config = {
            "themeAppearance": {
                "layoutSkin": "default",
                "sidebarSkin": "default",
                "styles": {
                    "colors": {
                        "primary": "#377dff",
                        "transparent": "transparent",
                        "white": "#fff",
                        "dark": "132144",
                        "gray": {
                            "100": "#f9fafc",
                            "900": "#1e2022"
                        }
                    },
                    "font": "Inter"
                }
            },
        }
    </script>
    <script src="/assets/js/theme-appearance.js"></script>
    <script>
        window.addEventListener("load", function (event) {        
            const rootBody = document.getElementsByTagName('body')[0],
            isMini =  window.localStorage.getItem('hs-navbar-vertical-aside-mini') === null ? false : true;            
            if (isMini) {
                rootBody.classList.add('navbar-vertical-aside-mini-mode')
            }
        });
    </script>
</head>

<body class="has-navbar-vertical-aside navbar-vertical-aside-show-xl">
    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] == true) : ?>

        <header id="header" class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-container navbar-bordered bg-white">
            <div class="navbar-nav-wrap">
                <!-- Logo -->
                <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Дебиторка</a>
                <!-- End Logo -->

                <div class="navbar-nav-wrap-content-start">
                    <!-- Navbar Vertical Toggle -->
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
                        <i class="bi-arrow-bar-left navbar-toggler-short-align" data-bs-template='<div class="tooltip d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Свернуть"></i>
                        <i class="bi-arrow-bar-right navbar-toggler-full-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Развернуть"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->

                    <!-- Search Form -->
                    <div class="dropdown ms-2">
                        <!-- Input Group -->
                        <div class="d-none d-lg-block">
                            <div class="input-group input-group-merge input-group-borderless input-group-hover-light navbar-input-group">
                                <div class="input-group-prepend input-group-text">
                                    <i class="bi-search"></i>
                                </div>

                                <input type="search" class="js-form-search form-control" placeholder="Поиск..." aria-label="Поиск...">
                                <a class="input-group-append input-group-text" href="javascript:;">
                                    <i id="clearSearchResultsIcon" class="bi-x-lg" style="display: none; opacity: 1.02333;"></i>
                                </a>
                            </div>
                        </div>
                        <!-- End Input Group -->

                    </div>
                    <!-- End Search Form -->
                </div>

                <div class="navbar-nav-wrap-content-end">
                    <!-- Navbar -->
                    <ul class="navbar-nav">

                        <li class="navbar-vertical-footer-list-item">
                            <!-- Style Switcher -->
                            <div class="dropdown">
                                <button type="button" class="btn btn-ghost-secondary btn-icon rounded-circle" id="selectThemeDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-dropdown-animation>

                                </button>

                                <div class="dropdown-menu navbar-dropdown-menu navbar-dropdown-menu-borderless dropdown-menu-end" aria-labelledby="selectThemeDropdown">
                                    <button class="dropdown-item" data-icon="bi-moon-stars" data-value="auto">
                                        <i class="bi-moon-stars me-2"></i>
                                        <span class="text-truncate" title="Auto (system default)">Авто (системная)</span>
                                    </button>
                                    <button class="dropdown-item" data-icon="bi-brightness-high" data-value="default">
                                        <i class="bi-brightness-high me-2"></i>
                                        <span class="text-truncate" title="Default (light mode)">Светлая</span>
                                    </button>
                                    <button class="dropdown-item active" data-icon="bi-moon" data-value="dark">
                                        <i class="bi-moon me-2"></i>
                                        <span class="text-truncate" title="Dark">Темная</span>
                                    </button>
                                </div>
                            </div>

                            <!-- End Style Switcher -->
                        </li>

                        <li class="nav-item">
                            <!-- Account -->
                            <div class="dropdown">
                                <a class="navbar-dropdown-account-wrapper" href="javascript:;" id="accountNavbarDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" data-bs-dropdown-animation="">
                                    <div class="avatar avatar-sm avatar-circle">
                                        <img class="avatar-img" src="/assets/images/vadik.jpg" alt="">
                                        <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                                    </div>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end navbar-dropdown-menu navbar-dropdown-menu-borderless navbar-dropdown-account" aria-labelledby="accountNavbarDropdown" style="width: 16rem; opacity: 1;">

                                    <a class="dropdown-item" href="#">Аккаунт</a>
                                    <a class="dropdown-item" href="#">Настройки</a>

                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item" href="logout.php">Выход</a>
                                </div>
                            </div>
                            <!-- End Account -->
                        </li>
                    </ul>
                    <!-- End Navbar -->
                </div>
            </div>
        </header>

        <?php include_once(BASE_PATH.'/includes/left-menu.php'); ?>

        <?php endif; ?>
        <!-- The End of the Header -->
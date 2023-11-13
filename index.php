<?php
session_start();
require_once './config.php';
require_once 'includes/auth_validate.php';

$db = getDbInstance();

$numDebtors = $db->getValue("debtors", "count(*)");
$numTorgi = $db->getValue("torgi", "count(*)");
// $stats = $db->getOne("reports", "sum(childrens) as childrens, sum(teenager) as teenager, count(*) as reps");

// $reports = $db->get('reports');

include_once('includes/header.php');
?>

<main id="content" class="main">
    <div class="content container-fluid">

        <div class="page-header">
            <h1 class="page-header-title">Общая Статистика</h1>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 py-3">

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="align-self-center">
                                    <i class="bi bi-person-rolodex text-primary fs-2 text flex-shrink-0 me-3"></i>
                                </div>
                                <div class="media-body text-right">
                                    <h3><?php echo $numDebtors ?></h3>
                                    <span>Всего должников</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="align-self-center">
                                    <i class="bi bi-flag text-warning fs-2 text flex-shrink-0 me-3"></i>
                                </div>
                                <div class="media-body text-right">
                                    <h3><?php echo $numTorgi ?></h3>
                                    <span>Всего торгов</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="align-self-center">
                                    <i class="bi bi-building text-success fs-2 text flex-shrink-0 me-3"></i>
                                </div>
                                <div class="media-body text-right">
                                    <h3>0</h3>
                                    <span>Всего компаний</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </div><!-- container-fluid -->
    
</main><!-- /#page-wrapper -->

<?php include_once('includes/footer.php'); ?>
<?php
ob_start();
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Home Header -->
            <div class="card mb-5 shadow-sm" style="border: 2px solid #000 !important;">
                <div class="card-body text-center py-5">
                    <h1 class="display-4 mb-4 text-dark">
                        <i class="fas fa-graduation-cap me-3"></i>
                        Welcome to LMS
                    </h1>
                    <p class="lead text-muted">
                        Your comprehensive Learning Management System for seamless education and efficient learning management.
                    </p>
                </div>
            </div>

            <!-- Features -->
            
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include('template.php');
?>
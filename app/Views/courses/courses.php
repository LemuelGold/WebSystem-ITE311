<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Course Search' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .course-card {
            transition: transform 0.2s;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url() ?>">
                ITE311 FUNDAR LMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('courses') ?>">Courses</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="display-4">Course Search</h1>
                <p class="lead text-muted">Search and explore available courses</p>
            </div>
        </div>

        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <form id="searchForm" class="d-flex">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" 
                               placeholder="Search courses..." name="search_term">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Courses Container -->
        <div id="coursesContainer" class="row">
            <?php foreach ($courses as $course): ?>
                <div class="col-md-4 mb-4">
                    <div class="card course-card">
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($course['title']) ?></h5>
                            <p class="card-text"><?= esc($course['description']) ?></p>
                            <a href="<?= base_url('courses/view/' . $course['id']) ?>" class="btn btn-primary">View Course</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results Message (hidden by default) -->
        <div id="noResults" class="col-12" style="display: none;">
            <div class="alert alert-info text-center">
                No courses found matching your search.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Client-side filtering
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('.course-card').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Server-side search with AJAX
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                var searchTerm = $('#searchInput').val();

                $.get('<?= base_url('/courses/search') ?>', {search_term: searchTerm}, function(data) {
                    $('#coursesContainer').empty();
                    
                    if (data.courses && data.courses.length > 0) {
                        $.each(data.courses, function(index, course) {
                            var courseHtml = `
                                <div class="col-md-4 mb-4">
                                    <div class="card course-card">
                                        <div class="card-body">
                                            <h5 class="card-title">${course.title}</h5>
                                            <p class="card-text">${course.description}</p>
                                            <a href="<?= base_url('courses/view/') ?>${course.id}" class="btn btn-primary">View Course</a>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#coursesContainer').append(courseHtml);
                        });
                        $('#noResults').hide();
                    } else {
                        $('#coursesContainer').html('<div class="col-12"><div class="alert alert-info text-center">No courses found matching your search.</div></div>');
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
session_start();
include './Database/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Culinary & Educational Resources - FoodFusion</title>
    <?php include './includes/head_tags.php'; ?>
    <style>
        body { font-family: 'Segoe UI', sans-serif; }
        .resource-card { border-radius: 12px; padding: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.08); transition: transform 0.2s; display:flex; flex-direction: column; height:100%; }
        .resource-card:hover { transform: translateY(-5px); }
        .download-btn { background-color: #ffc107; color: #000; border-radius: 6px; padding: 8px 16px; text-decoration: none; margin-top:auto; display:inline-block; font-weight: 500; }
        .download-btn:hover { background-color: #e0ac00; color: #000; }
        .nav-tabs .nav-link.active { background-color: #ffc107; color: #000; border-color: #ffc107; }
        .resource-type { font-weight: 500; font-size: 0.9rem; color: #888; }
        .resource-thumbnail { width: 100%; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; }
        .icon-preview { font-size: 4rem; color: #ffc107; text-align: center; padding: 50px 0; }
        #searchInput { max-width: 400px; margin: 0 auto 20px auto; }
    </style>
</head>
<body class="bg-warning-subtle">
<?php include './includes/navbar.php'; ?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="home_page.php" class="btn btn-outline-secondary">← Back to Home</a>
        <h1 class="mb-0 text-center fw-bold">Culinary & Educational Resources</h1>
        <div style="width: 150.38px;"></div> <!-- Spacer to keep title centered -->
    </div>

    <!-- Search Bar -->
    <div class="input-group mb-4" id="searchInput">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control" placeholder="Search resources..." id="resourceSearch">
    </div>

    <!-- Tabs for filtering -->
    <ul class="nav nav-tabs mb-4 justify-content-center" id="resourceTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="culinary-tab" data-bs-toggle="tab" data-bs-target="#culinary" type="button" role="tab">Culinary</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="educational-tab" data-bs-toggle="tab" data-bs-target="#educational" type="button" role="tab">Educational</button>
        </li>
    </ul>

    <div class="tab-content" id="resourceTabsContent">
        <?php
        $types = ['all'=>'','culinary'=>"WHERE resource_type='culinary'", 'educational'=>"WHERE resource_type='educational'"];
        foreach ($types as $key => $where) {
            $active = ($key=='all') ? 'show active' : '';
            echo '<div class="tab-pane fade '.$active.'" id="'.$key.'" role="tabpanel">';
            $sql = "SELECT * FROM resources $where ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo '<div class="row g-4 resource-list">';
                while($row = $result->fetch_assoc()) {
                    $url = htmlspecialchars($row['url']);
                    $ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));
                    
                    // Determine thumbnail
                    if(in_array($ext, ['jpg','jpeg','png','gif'])) {
                        $thumbnail = '<img class="resource-thumbnail" src="'.$url.'" alt="'.htmlspecialchars($row['title']).'">';
                    } elseif($ext == 'pdf') {
                        $thumbnail = '<div class="icon-preview"><i class="bi bi-file-earmark-pdf"></i></div>';
                    } elseif(in_array($ext, ['mp4','webm','ogg'])) {
                        $thumbnail = '<video class="resource-thumbnail" controls><source src="'.$url.'" type="video/'.$ext.'"></video>';
                    } else {
                        $thumbnail = '<div class="icon-preview"><i class="bi bi-file-earmark"></i></div>';
                    }

                    echo '<div class="col-md-6 col-lg-4 resource-item">';
                    echo '<div class="resource-card">';
                    echo $thumbnail;
                    echo '<h5 class="resource-title">'.htmlspecialchars($row['title']).'</h5>';
                    echo '<p class="resource-desc">'.htmlspecialchars($row['description']).'</p>';
                    echo '<p class="resource-type">'.ucfirst($row['resource_type']).' Resource</p>';
                    echo '<a class="download-btn" href="'.$url.'" target="_blank">View / Download</a>';
                    echo '</div></div>';
                }
                echo '</div>';
            } else {
                echo '<p class="text-center">No resources available in this category.</p>';
            }

            echo '</div>';
        }
        ?>
    </div>
</div>

<!-- Login/Registration Modals -->
<?php include 'modals/modals.php'; ?>

<?php include './includes/footer_tags.php'; ?>
<?php include './includes/script_tags.php'; ?>
<script src="./Assets/js/home.js?v=1.3"></script>

<script>
document.getElementById('resourceSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll('.resource-item').forEach(function(card) {
        let title = card.querySelector('.resource-title').textContent.toLowerCase();
        let desc = card.querySelector('.resource-desc').textContent.toLowerCase();
        if(title.includes(filter) || desc.includes(filter)){
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>
</body>
</html>

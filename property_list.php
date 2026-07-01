<?php
session_start();
require "includes/database_connect.php";

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
$city_name = isset($_GET['city']) ? trim($_GET['city']) : '';
$gender_filter = isset($_GET['gender']) ? strtolower(trim($_GET['gender'])) : 'none';
$allowed_genders = ['male', 'female', 'unisex', 'none'];
if (!in_array($gender_filter, $allowed_genders, true)) {
    $gender_filter = 'none';
}
$property_message = '';
$properties = [];
$city = null;
$city_id = 0;

if ($city_name !== '') {
    $city_key = strtolower($city_name);
    $city_aliases = [
        'bangalore' => ['bangalore', 'bengaluru'],
        'bengaluru' => ['bengaluru', 'bangalore'],
        'delhi' => ['delhi'],
        'mumbai' => ['mumbai'],
        'hyderabad' => ['hyderabad']
    ];
    $variants = isset($city_aliases[$city_key]) ? $city_aliases[$city_key] : [$city_key];
    $escaped_variants = array_map(function ($value) use ($conn) {
        return mysqli_real_escape_string($conn, $value);
    }, $variants);

    $sql_city = "SELECT * FROM cities WHERE LOWER(name) IN ('" . implode("','", $escaped_variants) . "') ORDER BY CASE LOWER(name) " . implode(' ', array_map(function ($value) use ($variants) {
        $index = array_search($value, $variants);
        return "WHEN '$value' THEN $index";
    }, $variants)) . " END LIMIT 1";
    $result_city = mysqli_query($conn, $sql_city);
    if ($result_city) {
        $city = mysqli_fetch_assoc($result_city);
        $city_id = $city ? intval($city['id']) : 0;
    }

    if ($city_id > 0) {
        $sql_properties = "SELECT * FROM properties WHERE city_id = $city_id";
        if ($gender_filter !== 'none') {
            $gender_safe = mysqli_real_escape_string($conn, $gender_filter);
            $sql_properties .= " AND LOWER(gender) = '$gender_safe'";
        }
        $result_properties = mysqli_query($conn, $sql_properties);
        if ($result_properties) {
            $properties = mysqli_fetch_all($result_properties, MYSQLI_ASSOC);
        }
    } else {
        $property_message = "Sorry! We do not have any PGs listed for '" . htmlspecialchars($city_name, ENT_QUOTES, 'UTF-8') . "'.";
    }
} else {
    $property_message = 'Please enter a city name to search for PGs.';
}

$sort_order = isset($_GET['sort']) ? trim($_GET['sort']) : 'none';
if ($sort_order === 'asc') {
    usort($properties, function ($a, $b) {
        return $a['rent'] <=> $b['rent'];
    });
} elseif ($sort_order === 'desc') {
    usort($properties, function ($a, $b) {
        return $b['rent'] <=> $a['rent'];
    });
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $city ? 'Best PG\'s in ' . htmlspecialchars($city['name'], ENT_QUOTES, 'UTF-8') : 'Search PGs'; ?> | PG Life</title>

    <?php
    include "includes/head_links.php";
    ?>
    <link href="css/property_list.css" rel="stylesheet" />
</head>

<body>
    <?php
    include "includes/header.php";
    ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item">
                <a href="index.php">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $city_name; ?>
            </li>
        </ol>
    </nav>

    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div class="page-container">
        <div class="filter-bar row justify-content-around mb-3">
            <div class="col-auto" data-toggle="modal" data-target="#filter-modal">
                <img src="img/filter.png" alt="filter" />
                <span>Filter</span>
                <?php if ($gender_filter !== 'none') { ?>
                    <span class="filter-badge"><?= ucfirst($gender_filter) ?></span>
                <?php } ?>
            </div>
            <div class="col-auto<?= $sort_order === 'desc' ? ' sort-active' : '' ?>">
                <a href="property_list.php?city=<?= urlencode($city_name) ?>&sort=desc" style="color: inherit; text-decoration: none;">
                    <img src="img/desc.png" alt="sort-desc" />
                    <span>Highest rent first</span>
                </a>
            </div>
            <div class="col-auto<?= $sort_order === 'asc' ? ' sort-active' : '' ?>">
                <a href="property_list.php?city=<?= urlencode($city_name) ?>&sort=asc" style="color: inherit; text-decoration: none;">
                    <img src="img/asc.png" alt="sort-asc" />
                    <span>Lowest rent first</span>
                </a>
            </div>
        </div>

        <div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Filter PGs</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5>Gender</h5>
                        <hr />
                        <button type="button" class="btn btn-outline-dark gender-button<?= $gender_filter === 'none' ? ' btn-active' : '' ?>" data-gender="none">All</button>
                        <button type="button" class="btn btn-outline-dark gender-button<?= $gender_filter === 'male' ? ' btn-active' : '' ?>" data-gender="male">Male</button>
                        <button type="button" class="btn btn-outline-dark gender-button<?= $gender_filter === 'female' ? ' btn-active' : '' ?>" data-gender="female">Female</button>
                        <button type="button" class="btn btn-outline-dark gender-button<?= $gender_filter === 'unisex' ? ' btn-active' : '' ?>" data-gender="unisex">Unisex</button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="apply-filter">Apply</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if (count($properties) > 0) { ?>
            <?php foreach ($properties as $property) { 
                $property_images = glob("img/properties/" . $property['id'] . "/*");
                $property_image = isset($property_images[0]) ? $property_images[0] : "img/properties/" . $property['id'] . "/default.jpg";
                $total_rating = ($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3;
                $total_rating = round($total_rating, 1);
            ?>
                <div class="property-card property-id-<?= $property['id'] ?> row">
                    <div class="image-container col-md-4">
                        <img src="<?= $property_image ?>" alt="property" />
                    </div>
                    <div class="content-container col-md-8">
                        <div class="row no-gutters justify-content-between">
                            <div class="star-container" title="<?= $total_rating ?>">
                                <?php
                                $rating = $total_rating;
                                for ($i = 0; $i < 5; $i++) {
                                    if ($rating >= $i + 0.8) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif ($rating >= $i + 0.3) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="detail-container">
                            <div class="property-name"><?= $property['name'] ?></div>
                            <div class="property-address"><?= $property['address'] ?></div>
                            <div class="property-gender">
                                <?php if ($property['gender'] == 'male') { ?>
                                    <img src="img/male.png" alt="male">
                                <?php } elseif ($property['gender'] == 'female') { ?>
                                    <img src="img/female.png" alt="female">
                                <?php } else { ?>
                                    <img src="img/unisex.png" alt="unisex">
                                <?php } ?>
                            </div>
                        </div>
                        <div class="row no-gutters">
                            <div class="rent-container col-6">
                                <div class="rent">₹ <?= number_format($property['rent']) ?>/-</div>
                                <div class="rent-unit">per month</div>
                            </div>
                            <div class="button-container col-6">
                                <a href="property_details.php?property_id=<?= $property['id'] ?>" class="btn btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="no-property-container">
                <p><?= htmlspecialchars($property_message, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php } ?>
    </div>

    <?php
    include "includes/signup_modal.php";
    include "includes/login_modal.php";
    include "includes/footer.php";
    ?>

    <script>
        (function () {
            var selectedGender = '<?= $gender_filter ?>';
            function updateButtonState() {
                var buttons = document.querySelectorAll('.gender-button');
                buttons.forEach(function (button) {
                    if (button.getAttribute('data-gender') === selectedGender) {
                        button.classList.add('btn-active');
                    } else {
                        button.classList.remove('btn-active');
                    }
                });
            }

            document.querySelectorAll('.gender-button').forEach(function (button) {
                button.addEventListener('click', function () {
                    selectedGender = button.getAttribute('data-gender');
                    updateButtonState();
                });
            });

            var applyButton = document.getElementById('apply-filter');
            if (applyButton) {
                applyButton.addEventListener('click', function () {
                    var params = new URLSearchParams(window.location.search);
                    if (selectedGender && selectedGender !== 'none') {
                        params.set('gender', selectedGender);
                    } else {
                        params.delete('gender');
                    }
                    var cityParam = params.get('city');
                    if (!cityParam && document.querySelector('input[name="city"]')) {
                        cityParam = document.querySelector('input[name="city"]').value.trim();
                        if (cityParam) {
                            params.set('city', cityParam);
                        }
                    }
                    window.location.search = params.toString();
                });
            }

            updateButtonState();
        })();
    </script>
</body>

</html>

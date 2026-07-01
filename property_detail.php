<?php
header('Location: property_details.php' . (isset($_GET['property_id']) ? '?property_id=' . urlencode($_GET['property_id']) : ''));
exit;

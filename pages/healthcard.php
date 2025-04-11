<?php
$mysqli = new mysqli("localhost", "root", "", "clinolog");
$mysqli->set_charset("utf8");

//include './ini/config.php';

$systems_sql = "SELECT * FROM systems";
$systems_result = $mysqli->query($systems_sql);

$structure = [];

while ($system = $systems_result->fetch_assoc()) {
    $system_id = $system['system_id'];
    $categories_sql = "SELECT * FROM categories WHERE system_id = $system_id";
    $categories_result = $mysqli->query($categories_sql);

    $cats = [];

    while ($category = $categories_result->fetch_assoc()) {
        $category_id = $category['category_id'];
        $diseases_sql = "SELECT * FROM diseases WHERE category_id = $category_id";
        $diseases_result = $mysqli->query($diseases_sql);

        $choroby = [];
        while ($disease = $diseases_result->fetch_assoc()) {
            $choroby[] = $disease['name'];
        }

        $cats[] = [
            'nazov' => $category['name'],
            'choroby' => $choroby
        ];
    }

    $structure[] = [
        'nazov' => $system['name'],
        'kategorie' => $cats
    ];
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Zdravotná karta</title>
    <link rel="stylesheet" href="../styles/main.css">
</head>
<body>
    <div class="sidebar">
        <h2>Zdravotná karta</h2>
        <ul>
            <?php foreach ($structure as $system): ?>
                <li>
                    <details>
                        <summary><?= htmlspecialchars($system['nazov']) ?></summary>
                        <ul>
                            <?php foreach ($system['kategorie'] as $kat): ?>
                                <li>
                                    <details>
                                        <summary><?= htmlspecialchars($kat['nazov']) ?></summary>
                                        <ul>
                                            <?php foreach ($kat['choroby'] as $choroba): ?>
                                                <li><?= htmlspecialchars($choroba) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </details>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </details>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
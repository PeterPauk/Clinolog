<?php
$mysqli = new mysqli("localhost", "root", "", "clinolog");
$mysqli->set_charset("utf8");

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
            $choroby[] = [
                'id' => $disease['disease_id'],
                'name' => $disease['name']
            ];
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

$disease_name = "";
$disease_description = "";

if (isset($_GET['disease_id'])) {
    $disease_id = $_GET['disease_id'];

    $disease_sql = "SELECT * FROM diseases WHERE disease_id = $disease_id";
    $disease_result = $mysqli->query($disease_sql);
    
    if ($disease_result && $disease_result->num_rows > 0) {
        $disease = $disease_result->fetch_assoc();
        $disease_name = $disease['name'];
        $disease_description = $disease['description'];
    } else {
        $disease_name = "Choroba neexistuje";
        $disease_description = "Popis neexistuje.";
    }
}
?>

<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <title>Zdravotná karta</title>
    <link rel="stylesheet" href="../styles/main.css">
</head>

<body class="healthcard-body">
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
                                        <ul class="sub-ul">
                                            <?php foreach ($kat['choroby'] as $choroba): ?>
                                                <li class="subcategory">
                                                    <a href="?disease_id=<?= $choroba['id'] ?>">
                                                        <?= htmlspecialchars($choroba['name']) ?>
                                                    </a>
                                                </li>
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

    <div class="disease-details">
        <?php if ($disease_name != ""): ?>
            <div class="disease-details">
                <h2><?= htmlspecialchars($disease_name) ?></h2>
                <p><?= nl2br(htmlspecialchars($disease_description)) ?></p>
            </div>
        <?php else: ?>
            <p>Vyberte chorobu na zobrazenie detailov.</p>
        <?php endif; ?>
    </div>

</body>
</html>
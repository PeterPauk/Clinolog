<?php
/* $mysqli = new mysqli("localhost", "root", "", "clinolog");
$mysqli->set_charset("utf8"); */
include '../ini/config.php';

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

$patients = [];

if (isset($_GET['disease_id'])) {
    $disease_id = $_GET['disease_id'];

    $patients_sql = "SELECT * FROM patients WHERE disease_id = ?";
    $stmt = $mysqli->prepare($patients_sql);
    $stmt->bind_param("i", $disease_id);
    $stmt->execute();
    $patients_result = $stmt->get_result();

    while ($row = $patients_result->fetch_assoc()) {
        $patients[] = $row;
    }
}

if (isset($_POST['update_patient'])) {
    $id = $_POST['patient_id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $symptoms = $_POST['symptoms'];
    $cures = $_POST['cures'];

    $update_sql = "UPDATE patients SET name = ?, surname = ?, symptoms = ?, cures = ? WHERE id = ?";
    $stmt = $mysqli->prepare($update_sql);
    $stmt->bind_param("ssssi", $name, $surname, $symptoms, $cures, $id);
    $stmt->execute();

    header("Location: healthcard.php?disease_id=" . $_POST['disease_id']);
    exit();
}

if (isset($_POST['delete_patient'])) {
    $id = $_POST['patient_id'];

    $delete_sql = "DELETE FROM patients WHERE id = ?";
    $stmt = $mysqli->prepare($delete_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: healthcard.php?disease_id=" . $_POST['disease_id']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $symptoms = $_POST['symptoms'];
    $cures = $_POST['cures'];
    $disease_id = $_POST['disease_id'];

    $sql = "INSERT INTO patients (name, surname, symptoms, cures, disease_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssi", $name, $surname, $symptoms, $cures, $disease_id);
    $stmt->execute();

    header("Location: healthcard.php?disease_id=" . $disease_id);
    exit();
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
            <div class="patients-grid">
                <form action="healthcard.php" method="POST">
                    <h3>Pridajte pacienta</h3>

                    <div>
                    <span>Meno</span>
                    <input type="name" name="name" id="" required="required">
                    <i></i>
                    </div>

                    <div>
                    <span>Priezvisko</span>
                    <input type="surname" name="surname" id="" required="required">
                    <i></i>
                    </div>

                    <div>
                    <span>Príznaky</span>
                    <textarea name="symptoms" id="" required="required" rows="6" cols="60"></textarea>
                    <i></i>
                    </div>

                    <div>
                    <span>Liečivá</span>
                    <textarea name="cures" id="" required="required" rows="4" cols="60"></textarea>
                    <i></i>
                    </div>

                    <div><input class="patient-btn" type="submit" value="Pridať pacienta">
                    </div>
                    
                    <div>
                    <span class="disease-name">Choroba:<?= htmlspecialchars($disease_name) ?></span>
                    </div>

                    <input type="hidden" name="disease_id" value="<?= htmlspecialchars($_GET['disease_id']) ?>">
                </form>

                <?php if (!empty($patients)): ?>
                    <?php foreach ($patients as $patient): ?>
                        <form class="patient-card" method="POST" action="healthcard.php?disease_id=<?= $disease_id ?>">
                            <h3>Pacient</h3>

                            <input type="hidden" name="patient_id" value="<?= $patient['id'] ?>">
                            <input type="hidden" name="disease_id" value="<?= $disease_id ?>">

                            <div>
                                <span>Meno</span>
                                <input type="text" name="name" value="<?= htmlspecialchars($patient['name']) ?>">
                            </div>

                            <div>
                                <span>Priezvisko</span>
                                <input type="text" name="surname" value="<?= htmlspecialchars($patient['surname']) ?>">
                            </div>

                            <div>
                                <span>Príznaky</span>
                                <textarea name="symptoms" rows="6" cols="60"><?= htmlspecialchars($patient['symptoms']) ?></textarea>
                            </div>

                            <div>
                                <span>Liečivá</span>
                                <textarea name="cures" rows="4" cols="60"><?= htmlspecialchars($patient['cures']) ?></textarea>
                            </div>

                            <div class="buttons">
                                <button type="submit" name="update_patient" class="update">Upraviť</button>
                                <button type="submit" name="delete_patient" class="delete" onclick="return confirm('Naozaj chcete odstrániť pacienta?');">Odstrániť</button>
                            </div>
                        </form>
                    <?php endforeach; ?>
                <?php else: ?>
                <p>Žiadni pacienti zatiaľ neboli pridaní.</p>
                <?php endif; ?>

            </div>
        <?php else: ?>
            <p>Vyberte chorobu na zobrazenie detailov.</p>
        <?php endif; ?>
    </div>

</body>
</html>

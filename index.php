<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Word List Comparator</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; line-height: 1.6; }
        .container { max-width: 900px; margin: auto; padding: 2em; border: 1px solid #ccc; border-radius: 8px; }
        h1, h2 { text-align: center; }
        form { display: grid; grid-template-columns: 1fr 1fr; gap: 2em; }
        .input-group { display: flex; flex-direction: column; }
        label { margin-bottom: 0.5em; font-weight: bold; }
        textarea, input[type="file"] { width: 100%; padding: 0.5em; border: 1px solid #ccc; border-radius: 4px; }
        button { grid-column: 1 / -1; padding: 1em; background-color: #007BFF; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .results { margin-top: 2em; }
        table { width: 100%; border-collapse: collapse; margin-top: 1em; }
        th, td { border: 1px solid #ccc; padding: 0.8em; text-align: left; }
        th { background-color: #f2f2f2; }
        .options { grid-column: 1 / -1; display: flex; align-items: center; }
        .options input { margin-right: 0.5em; }
    </style>
</head>
<body>

<div class="container">
    <h1>Word List Comparator</h1>
    <p>Upload two text files or paste your lists to find common and duplicate words.</p>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="input-group">
            <label for="lista1_text">List 1 (copy/paste):</label>
            <textarea id="lista1_text" name="lista1_text" rows="10"></textarea>
            <label for="lista1_file" style="margin-top: 1em;">Or upload a text file:</label>
            <input type="file" id="lista1_file" name="lista1_file">
        </div>
        <div class="input-group">
            <label for="lista2_text">List 2 (copy/paste):</label>
            <textarea id="lista2_text" name="lista2_text" rows="10"></textarea>
            <label for="lista2_file" style="margin-top: 1em;">Or upload a text file:</label>
            <input type="file" id="lista2_file" name="lista2_file">
        </div>
        <div class="options">
            <input type="checkbox" id="unificar" name="unificar">
            <label for="unificar">Unify lists with unique occurrences</label>
        </div>
        <div class="options">
            <input type="checkbox" id="generate_file" name="generate_file">
            <label for="generate_file">Create a new file with common words</label>
        </div>
        <button type="submit">Compare Lists</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once 'ListComparator.php';
        $comparator = new ListComparator();

        if (!empty($_FILES['lista1_file']['tmp_name'])) {
            $list1 = file_get_contents($_FILES['lista1_file']['tmp_name']);
        } else {
            $list1 = $_POST['lista1_text'] ?? '';
        }

        if (!empty($_FILES['lista2_file']['tmp_name'])) {
            $list2 = file_get_contents($_FILES['lista2_file']['tmp_name']);
        } else {
            $list2 = $_POST['lista2_text'] ?? '';
        }

        $unify = isset($_POST['unificar']);
        $generateFile = isset($_POST['generate_file']);

        if (!empty($list1) && !empty($list2)) {
            $results = $comparator->compareAndShow($list1, $list2);

            if ($generateFile) {
                $commonWords = array_keys(array_filter($results, function($item) {
                    return $item['repetitions1'] > 0 && $item['repetitions2'] > 0;
                }));
                $comparator->createCommonWordsFile($commonWords, 'newlist.txt');
                echo "<p class='results' style='color:green;'>A file named 'newlist.txt' has been created with the common words.</p><a href='newlist.txt'>list.txt</a>";

            }

            echo "<div class='results'>";
            echo "<h2>Comparison Results</h2>";

            $duplicates = array_filter($results, function($item) {
                return $item['repetitions1'] > 1 || $item['repetitions2'] > 1;
            });
            if (!empty($duplicates)) {
                echo "<h3>Internally Duplicated Words</h3>";
                echo "<table>";
                echo "<thead><tr><th>Word</th><th>Repetitions in List 1</th><th>Repetitions in List 2</th></tr></thead>";
                echo "<tbody>";
                foreach ($duplicates as $word => $values) {
                    echo "<tr><td>{$word}</td><td>{$values['repetitions1']}</td><td>{$values['repetitions2']}</td></tr>";
                }
                echo "</tbody></table>";
            }

            $exclusive1 = array_filter($results, function($item) {
                return $item['repetitions1'] > 0 && $item['repetitions2'] === 0;
            });
            $exclusive2 = array_filter($results, function($item) {
                return $item['repetitions2'] > 0 && $item['repetitions1'] === 0;
            });

            if (!empty($exclusive1)) {
                echo "<h3>Exclusive Words from List 1</h3>";
                echo "<ul>";
                foreach ($exclusive1 as $word => $values) {
                    echo "<li>{$word}</li>";
                }
                echo "</ul>";
            }
            if (!empty($exclusive2)) {
                echo "<h3>Exclusive Words from List 2</h3>";
                echo "<ul>";
                foreach ($exclusive2 as $word => $values) {
                    echo "<li>{$word}</li>";
                }
                echo "</ul>";
            }

            $common = array_filter($results, function($item) {
                return $item['repetitions1'] > 0 && $item['repetitions2'] > 0;
            });
            if (!empty($common)) {
                echo "<h3>Common Words in Both Lists</h3>";
                echo "<table>";
                echo "<thead><tr><th>Word</th><th>Position in List 1</th><th>Position in List 2</th><th>Repetitions in List 1</th><th>Repetitions in List 2</th></tr></thead>";
                echo "<tbody>";
                foreach ($common as $word => $values) {
                    echo "<tr><td>{$word}</td><td>{$values['position1']}</td><td>{$values['position2']}</td><td>{$values['repetitions1']}</td><td>{$values['repetitions2']}</td></tr>";
                }
                echo "</tbody></table>";
            }

            if ($unify) {
                $unifiedList = $comparator->unifyLists($list1, $list2);
                echo "<h3>Unified List (unique words)</h3>";
                echo "<ul>";
                foreach ($unifiedList as $word) {
                    echo "<li>{$word}</li>";
                }
                echo "</ul>";
            }

            echo "</div>";
        } else {
            echo "<p class='results' style='color:red;'>Please provide at least one list to compare.</p>";
        }
    }
    ?>
</div>

</body>
</html>
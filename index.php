<?php
session_start();

if (!isset($_SESSION['serialized_catalogs'])) {
    $_SESSION['serialized_catalogs'] = serialize([]);
}

if (!isset($_SESSION['serialized_catalog_items'])) {
    $_SESSION['serialized_catalog_items'] = serialize([]);
}

if (isset($_POST['catalog']) && !empty($_POST['catalog'])) {
    $catalog = $_POST['catalog'];
    $serializedCatalogs = unserialize($_SESSION['serialized_catalogs']);
    $serializedCatalogs[] = $catalog;
    $_SESSION['serialized_catalogs'] = serialize($serializedCatalogs);
}

if (isset($_POST['selected_catalog']) && isset($_POST['item']) && !empty($_POST['item'])) {
    $selectedCatalog = $_POST['selected_catalog'];
    $item = $_POST['item'];

    $serializedCatalogs = unserialize($_SESSION['serialized_catalogs']);
    $serializedCatalogItems = unserialize($_SESSION['serialized_catalog_items']);

    $catalogIndex = array_search($selectedCatalog, $serializedCatalogs);

    if ($catalogIndex !== false) {
        if (!isset($serializedCatalogItems[$catalogIndex])) {
            $serializedCatalogItems[$catalogIndex] = [];
        }
        $serializedCatalogItems[$catalogIndex][] = $item;

        $_SESSION['serialized_catalog_items'] = serialize($serializedCatalogItems);
    }
}

if (isset($_GET['delete_catalog']) && isset($_GET['catalog_index'])) {
    $catalogIndex = $_GET['catalog_index'];

    $serializedCatalogs = unserialize($_SESSION['serialized_catalogs']);
    $serializedCatalogItems = unserialize($_SESSION['serialized_catalog_items']);

    if (isset($serializedCatalogs[$catalogIndex])) {
        unset($serializedCatalogs[$catalogIndex]);
        unset($serializedCatalogItems[$catalogIndex]);

        $_SESSION['serialized_catalogs'] = serialize(array_values($serializedCatalogs));
        $_SESSION['serialized_catalog_items'] = serialize(array_values($serializedCatalogItems));
    }
}

if (isset($_GET['delete_item']) && isset($_GET['catalog_index']) && isset($_GET['item_index'])) {
    $catalogIndex = $_GET['catalog_index'];
    $itemIndex = $_GET['item_index'];

    $serializedCatalogItems = unserialize($_SESSION['serialized_catalog_items']);

    if (isset($serializedCatalogItems[$catalogIndex][$itemIndex])) {
        unset($serializedCatalogItems[$catalogIndex][$itemIndex]);

        $_SESSION['serialized_catalog_items'] = serialize($serializedCatalogItems);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <title>Document</title>
</head>
<body>
<div class="container">
        <div class="row">
            <div class="col-md-6">
    <h2>Product Catalogs</h2>

    <!-- Form for adding product catalogs -->
    <form method="post" action="index.php">
        <input type="text" name="catalog" placeholder="Enter Product Catalog" class="form-control d-inline-block w-50" />
        <input type="submit" value="Add Catalog" class="btn btn-success btn-lg" />
    </form>
    </div>

    <!-- Display existing product catalogs (if any) -->
    <?php
    $serializedCatalogs = unserialize($_SESSION['serialized_catalogs']);
    if (!empty($serializedCatalogs)) {
        echo "<div class=' mt-5 pt-5'>";

        echo "<h2>Existing Catalogs:</h2>";
        echo "<ul class='list-unstyled '>";
        foreach ($serializedCatalogs as $index => $catalog) {
            echo "<li class='text-center mx-auto mb-2 ' ><p class='position-relative m-0 d-flex align-items-center fw-bold fs-2 text-light ' style='background-color:#336; border-radius:10px; padding-left:10px;'>$catalog <a class='btn btn-danger btn-lg x-btn position-absolute' href='index.php?delete_catalog=1&catalog_index=$index'>Delete Catalog</a></p></li>";
        }
        echo "</ul>";
        echo "</div>";

    }
    ?>

<div class="col-md-6">
    <!-- Form for adding items -->
    <h2>Add Items to Catalog</h2>
    <form method="post" action="index.php">
        <select name="selected_catalog" class="form-select d-inline-block w-25 ">
            <?php
            foreach ($serializedCatalogs as $index => $catalog) {
                echo "<option value='$catalog'>$catalog</option>";
            }
            ?>
        </select>
        <input type="text" name="item" class="form-control d-inline-block w-50" placeholder="Enter Item" />
        <input type="submit" class="btn btn-success btn-lg" value="Add Item" />
    </form>
    </div>
    </div>
    <!-- Display items for each catalog (if any) -->
    <?php
    $serializedCatalogItems = unserialize($_SESSION['serialized_catalog_items']);
    if (!empty($serializedCatalogItems)) {
        echo "<h2>Items in Catalogs:</h2>";
        foreach ($serializedCatalogs as $catalogIndex => $catalog) {
            if (isset($serializedCatalogItems[$catalogIndex]) && !empty($serializedCatalogItems[$catalogIndex])) {
                echo "<div class=' mt-5 pt-5'>";
                echo "<h3 class='position-relative d-flex align-items-center fw-bold fs-2 text-light w-50 ' style='background-color:#036; border-radius:10px; padding-left:10px;'>$catalog</h3>";
                echo "<ul class='row list-unstyled ms-5 ps-5'>";
                foreach ($serializedCatalogItems[$catalogIndex] as $itemIndex => $item) {
                    echo "<li class=' m-3 col-md-4'><p class='fs-3 position-relative d-flex align-items-center text-light' style='width: 152px;
                    height: 20px;
                    background-color: #336;
                    border: 1px solid white;
                    color: #fff;
                    border-radius: 10px;
                    padding: 50px 10px;
                    margin: 0px 3px 3px 3px;'>$item <a class='delete-btn position-absolute btn btn-danger' href='index.php?delete_item=1&catalog_index=$catalogIndex&item_index=$itemIndex'>Delete Item</a></li>";
                }
                echo "</ul>";
                echo "</div>";

            }
        }
    }
    ?>
</div>

</body>
</html>
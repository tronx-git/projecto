<?php
// Include the environment-aware config file
include 'config.php';



$sql = "SELECT * FROM inventory WHERE product_type LIKE '%tis%'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Standard Non Armored Pipes</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/normalize.css">
    <link rel="stylesheet" type="text/css" href="icomoon/icomoon.css">
    <link rel="stylesheet" type="text/css" href="css/vendor.css">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <section id="product-detail" class="py-5 my-5">
        <div class="container">
            <div class="row">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="col-md-6 mb-4">
                            <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'images/tis-pipes.png'; ?>" alt="<?php echo !empty($row['name']) ? htmlspecialchars($row['name']) : 'Standard non armored pipes'; ?>" class="img-fluid">
                        </div>
                        <div class="col-md-6 mb-4">
                            <p class="description"><?php echo !empty($row['description']) ? htmlspecialchars($row['description']) : ''; ?></p>
                            <div class="mb-2">
                                <?php if (!empty($row['diameter'])): ?>
                                    <span><strong>Diameter:</strong> <?php echo htmlspecialchars($row['diameter']); ?></span><br>
                                <?php endif; ?>
                                <?php if (!empty($row['length'])): ?>
                                    <span><strong>Length:</strong> <?php echo htmlspecialchars($row['length']); ?></span><br>
                                <?php endif; ?>
                                <?php if (isset($row['items_in_stock'])): ?>
                                    <span><strong>Items in Stock:</strong> <?php echo htmlspecialchars($row['items_in_stock']); ?></span>
                                <?php endif; ?>
                            </div>
                            <ul>
                                <?php if (!empty($row['sizes'])): ?>
                                    <li>Available sizes: <?php echo htmlspecialchars($row['sizes']); ?></li>
                                <?php endif; ?>
                                <?php if (!empty($row['material'])): ?>
                                    <li>Material: <?php echo htmlspecialchars($row['material']); ?></li>
                                <?php endif; ?>
                                <?php if (!empty($row['features'])): ?>
                                    <li><?php echo htmlspecialchars($row['features']); ?></li>
                                <?php endif; ?>
                            </ul>
                            <div class="price mt-2">฿ <?php echo !empty($row['price']) ? htmlspecialchars($row['price']) : ''; ?></div>
                            
                            <a href="index.html#popular-products" class="btn btn-outline-secondary mt-4 ml-2">Back to Products</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p>No standard pipes found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</body>
</html>
<?php
if ($conn && $conn->ping()) {
    $conn->close();
}
?>

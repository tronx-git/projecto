<?php
session_start();

// --- Simple login logic ---
$login_error = '';
if (isset($_POST['login'])) {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if ($user === 'root' && $pass === '1234') {
        $_SESSION['is_admin'] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $login_error = "Invalid username or password.";
    }
}

// --- Logout logic ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- Show login form if not logged in ---
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body { font-family: sans-serif; background: #f8f8f8; }
        .login-box { max-width: 320px; margin: 80px auto; padding: 24px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        .login-box h2 { margin-bottom: 18px; }
        .login-box input { width: 100%; margin-bottom: 12px; padding: 8px; }
        .login-box button { width: 100%; padding: 8px; }
        .error { color: #b00; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if ($login_error): ?><div class="error"><?= htmlspecialchars($login_error) ?></div><?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
<?php
exit;
endif;

// --- Main inventory table display and actions ---
 $mysqli = new mysqli("localhost", "root", "", "inventory"); //inventory is the database name
 

if ($mysqli->connect_errno) {
    echo "<div class='alert alert-danger'>Failed to connect to database: " . htmlspecialchars($mysqli->connect_error) . "</div>";
    exit;
}

// Handle Add New
if (isset($_POST['add_new'])) {
    $stmt = $mysqli->prepare("INSERT INTO inventory (product, product_type, items_in_stock, items_requested, picture, `3d_image`, `tech_drawing`, price, diameter, length, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    // Correct binding: all except items_in_stock/items_requested (i) and price (d) are strings (s)
    // "sssisssdsss"
    $stmt->bind_param(
        "sssisssdsss",
        $_POST['product'],
        $_POST['product_type'],
        $_POST['items_in_stock'],
        $_POST['items_requested'],
        $_POST['picture'],
        $_POST['3d_image'],
        $_POST['tech_drawing'],
        $_POST['price'],
        $_POST['diameter'],
        $_POST['length'],
        $_POST['description']
    );
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle Delete
if (isset($_POST['delete_id'])) {
    $del_id = intval($_POST['delete_id']);
    $mysqli->query("DELETE FROM inventory WHERE id = $del_id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle Edit
if (isset($_POST['edit_id'])) {
    $edit_id = intval($_POST['edit_id']);
    $edit_row = $mysqli->query("SELECT * FROM inventory WHERE id = $edit_id")->fetch_assoc();
}

// Handle Save Edit
if (isset($_POST['save_edit'])) {
    $stmt = $mysqli->prepare("UPDATE inventory SET product=?, product_type=?, items_in_stock=?, items_requested=?, picture=?, `3d_image`=?, `tech_drawing`=?, price=?, diameter=?, length=?, description=? WHERE id=?");
    // "sssisssdsssi"
    $stmt->bind_param(
        "sssisssdsssi",
        $_POST['product'],
        $_POST['product_type'],
        $_POST['items_in_stock'],
        $_POST['items_requested'],
        $_POST['picture'],
        $_POST['3d_image'],
        $_POST['tech_drawing'],
        $_POST['price'],
        $_POST['diameter'],
        $_POST['length'],
        $_POST['description'],
        $_POST['edit_row_id']
    );
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$result = $mysqli->query("SELECT * FROM inventory");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #eee; }
        img { max-width: 80px; max-height: 80px; }
        .logout-link { float: right; }
        .add-form { margin: 24px 0; padding: 16px; background: #f9f9f9; border: 1px solid #ddd; }
        .add-form input, .add-form textarea { margin-bottom: 8px; width: 100%; }
        .add-form label { font-weight: bold; }
        .action-btn { background: #c00; color: #fff; border: none; padding: 4px 10px; border-radius: 3px; cursor: pointer; }
        .action-btn:hover { background: #900; }
        .edit-btn { background: #0074d9; color: #fff; border: none; padding: 4px 10px; border-radius: 3px; cursor: pointer; }
        .edit-btn:hover { background: #005fa3; }
        .edit-form { background: #eef6ff; border: 1px solid #b3d1f7; padding: 12px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <h1>Inventory Table <a href="?logout=1" class="logout-link">Logout</a></h1>

    <!-- Add New Entry Form -->
    <details class="add-form">
        <summary style="cursor:pointer;font-weight:bold;">+ Add New Product</summary>
        <form method="post">
            <label>Product Name: <input type="text" name="product" required></label><br>
            <label>Product Type: <input type="text" name="product_type"></label><br>
            <label>Items in Stock: <input type="number" name="items_in_stock" value="0" min="0" required></label><br>
            <label>Items Requested: <input type="number" name="items_requested" value="0" min="0" required></label><br>
            <label>Picture (file path): <input type="text" name="picture"></label><br>
            <label>3D Image (file path): <input type="text" name="3d_image"></label><br>
            <label>Tech Drawing (file path): <input type="text" name="tech_drawing"></label><br>
            <label>Price: <input type="number" step="0.01" name="price" required></label><br>
            <label>Diameter: <input type="text" name="diameter"></label><br>
            <label>Length: <input type="text" name="length"></label><br>
            <label>Description:<br>
                <textarea name="description" rows="3" required></textarea>
            </label><br>
            <button type="submit" name="add_new">Add Product</button>
        </form>
    </details>

    <?php if (isset($edit_row)): ?>
    <div class="edit-form">
        <form method="post">
            <input type="hidden" name="edit_row_id" value="<?= $edit_row['id'] ?>">
            <label>Product Name: <input type="text" name="product" value="<?= htmlspecialchars($edit_row['product']) ?>" required></label><br>
            <label>Product Type: <input type="text" name="product_type" value="<?= htmlspecialchars($edit_row['product_type']) ?>"></label><br>
            <label>Items in Stock: <input type="number" name="items_in_stock" value="<?= htmlspecialchars($edit_row['items_in_stock']) ?>" min="0" required></label><br>
            <label>Items Requested: <input type="number" name="items_requested" value="<?= htmlspecialchars($edit_row['items_requested']) ?>" min="0" required></label><br>
            <label>Picture (file path): <input type="text" name="picture" value="<?= htmlspecialchars($edit_row['picture']) ?>"></label><br>
            <label>3D Image (file path): <input type="text" name="3d_image" value="<?= htmlspecialchars($edit_row['3d_image']) ?>"></label><br>
            <label>Tech Drawing (file path): <input type="text" name="tech_drawing" value="<?= htmlspecialchars($edit_row['tech_drawing']) ?>"></label><br>
            <label>Price: <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($edit_row['price']) ?>" required></label><br>
            <label>Diameter: <input type="text" name="diameter" value="<?= htmlspecialchars($edit_row['diameter']) ?>"></label><br>
            <label>Length: <input type="text" name="length" value="<?= htmlspecialchars($edit_row['length']) ?>"></label><br>
            <label>Description:<br>
                <textarea name="description" rows="3" required><?= htmlspecialchars($edit_row['description']) ?></textarea>
            </label><br>
            <button type="submit" name="save_edit" class="edit-btn">Save</button>
            <a href="<?= $_SERVER['PHP_SELF'] ?>" style="margin-left:10px;">Cancel</a>
        </form>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Product Type</th>
                <th>In Stock</th>
                <th>Requested</th>
                <th>Picture</th>
                <th>3D Image</th>
                <th>Tech Drawing</th>
                <th>Price</th>
                <th>Diameter</th>
                <th>Length</th>
                <th>Description</th>
                <th>Delete</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['product']) ?></td>
                <td><?= htmlspecialchars($row['product_type']) ?></td>
                <td><?= htmlspecialchars($row['items_in_stock']) ?></td>
                <td><?= htmlspecialchars($row['items_requested']) ?></td>
                <td>
                    <?php if ($row['picture']): ?>
                        <img src="<?= htmlspecialchars($row['picture']) ?>" alt="picture">
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['3d_image']): ?>
                        <a href="<?= htmlspecialchars($row['3d_image']) ?>" target="_blank">3D Model</a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['tech_drawing']): ?>
                        <a href="<?= htmlspecialchars($row['tech_drawing']) ?>" target="_blank">Tech Drawing</a>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['price']) ?></td>
                <td><?= htmlspecialchars($row['diameter']) ?></td>
                <td><?= htmlspecialchars($row['length']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                <td>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Delete this entry?');">
                        <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="action-btn">Delete</button>
                    </form>
                </td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="edit-btn">Edit</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php
$result->free();
$mysqli->close();
?>

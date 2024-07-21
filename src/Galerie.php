<?php
require_once './classes/Image.php';
$imageHandler = new Image();


if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $imageHandler->deleteImage($_GET['id']);
}


if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
    $imageDetails = $imageHandler->getImageDetails($_GET['id']);
}


if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $imageDetails = $imageHandler->getImageDetails($_GET['id']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["image_name"])) {
        $imageName = $_POST["image_name"];
        
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
            $imageData = file_get_contents($_FILES["image"]["tmp_name"]);
            $imageMime = $_FILES["image"]["type"];
        } else {
            $imageData = null;
            $imageMime = null;
        }
        
        // Edit or upload image based on the presence of an image ID
        if (isset($_POST['image_id']) && !empty($_POST['image_id'])) {
            if ($imageData) {
                $imageHandler->editImage($_POST['image_id'], $imageName, $imageData, $imageMime);
            } else {
                $existingImage = $imageHandler->getImageDetails($_POST['image_id']);
                $imageHandler->editImage($_POST['image_id'], $imageName, $existingImage['data'], $existingImage['mime']);
            }
        } else {
            $imageHandler->uploadImage($imageName, $imageData, $imageMime);
        }
    }
}


$images = $imageHandler->getAllImages();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List BLOB Images</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css?version200">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Upload and List BLOB Images</h1>
        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">Back</a>
        </div>
        <form method="post" action="Galerie.php" enctype="multipart/form-data" class="mb-4">
            <input type="hidden" name="image_id" value="<?php echo isset($imageDetails) ? $imageDetails['id'] : ''; ?>">
            <div class="mb-3">
                <label for="image_name" class="form-label">Image Name:</label>
                <input type="text" name="image_name" class="form-control" value="<?php echo isset($imageDetails) ? htmlspecialchars($imageDetails['name'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Select Image:</label>
                <input type="file" name="image" class="form-control" accept="image/*" <?php echo isset($imageDetails) ? '' : 'required'; ?>>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo isset($imageDetails) ? 'Edit Image' : 'Upload Image'; ?></button>
        </form>
        
        <h2 class="mt-4">Uploaded Images</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($images as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><img src="data:image/jpeg;base64,<?= base64_encode($row['data']) ?>" height="200" width="200"/></td>
                    <td>
                        <a href="Galerie.php?action=view&id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a>
                        <a href="Galerie.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="Galerie.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (isset($imageDetails) && $_GET['action'] == 'view'): ?>
        <div class="center">
            <h2>Viewing Image</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($imageDetails["name"], ENT_QUOTES, 'UTF-8'); ?></p>
            <img src="data:image/jpeg;base64,<?= base64_encode($imageDetails['data']) ?>" width=40%"/>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

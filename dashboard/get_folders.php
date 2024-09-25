<?php
/*
include('/home/drm/public_html/conexion/conexion.php');
$sql = "SELECT f.id AS id, f.id_folder, f.name, f.fecha, COALESCE(COUNT(a.id_video), 0) as video_count 
FROM folders f 
LEFT JOIN archived a ON f.id_folder = a.id_folder 
GROUP BY f.id_folder, f.name, f.fecha ORDER BY f.id ASC";
$result = $conexion->query($sql);

$folders = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $folders[] = $row;
    }
}
?>

<div class="row">
    <?php foreach ($folders as $folder): ?>
      <div class="col-md-3 mb-3 folder-box">
        <div class="d-flex align-items-center">
          <i class="fa-solid fa-folder fa-2x" style="color: #f4d471;"></i>
          <div class="flex-grow-1 ms-3">
            <a href="#" class="folder-name" data-folder-id="<?php echo $folder['id_folder']; ?>">
              <?php echo htmlspecialchars($folder['name']); ?>
            </a>
            <div class="video-count small text-muted ms-3">
              <?php echo $folder['video_count']; ?> video<?php echo $folder['video_count'] != 1 ? 's' : ''; ?>
            </div>
          </div>
          <div class="dropdown">
            <button class="btn-link" type="" id="<?php echo $folder['id_folder']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="<?php echo $folder['id']; ?>">
            <li><a class="dropdown-item rename-folder" href="#" data-folder-id="<?php echo $folder['id_folder']; ?>">Rename</a></li>
              <li><a class="dropdown-item delete-folder" href="#" data-folder-id="<?php echo $folder['id_folder']; ?>">Delete</a></li>
              <li><a class="dropdown-item" href="#"></a></li>
            </ul>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
</div>

<?php
$conexion->close();
?>*/



include('/home/drm/public_html/conexion/conexion.php');

$sql = "SELECT f.id AS id, f.id_folder, f.name, f.fecha, COALESCE(COUNT(a.id_video), 0) as video_count 
FROM folders f 
LEFT JOIN archived a ON f.id_folder = a.id_folder 
GROUP BY f.id_folder, f.name, f.fecha ORDER BY f.id ASC";

$result = $conexion->query($sql);

$folders = [];
while($row = $result->fetch_assoc()) {
    $folders[] = $row;
}

// Generar el HTML para el contenedor de folders
ob_start();
 ?>
<div class="row">
    <?php foreach ($folders as $folder): ?>
      <div class="col-md-3 mb-3 folder-box">
        <div class="d-flex align-items-center">
          <i class="fa-solid fa-folder fa-2x" style="color: #f4d471;"></i>
          <div class="flex-grow-1 ms-3">
            <a href="#" class="folder-name" data-folder-id="<?php echo $folder['id_folder']; ?>">
              <?php echo htmlspecialchars($folder['name']); ?>
            </a>
            <div class="video-count small text-muted ms-3">
              <?php echo $folder['video_count']; ?> video<?php echo $folder['video_count'] != 1 ? 's' : ''; ?>
            </div>
          </div>
          <div class="dropdown">
            <button class="btn-link" type="" id="<?php echo $folder['id_folder']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="<?php echo $folder['id']; ?>">
              <li><a class="dropdown-item rename-folder" href="#" data-folder-id="<?php echo $folder['id_folder']; ?>">Rename</a></li>
              <li><a class="dropdown-item delete-folder" href="#" data-folder-id="<?php echo $folder['id_folder']; ?>">Delete</a></li>
              <li><a class="dropdown-item" href="#"></a></li>
            </ul>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
</div>
<?php
$htmlContent = ob_get_clean();

$response = [
    'containerHtml' => $htmlContent,
    'folders' => $folders
];

header('Content-Type: application/json');
echo json_encode($response);

$conexion->close();
?>
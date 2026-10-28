<?php
session_start();
include '../crypt-start.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../logout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Jamaah </title>
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <link rel="shortcut icon" href="../logo.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../assets/style.css" />
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content" id="mainContent">
        <?php include 'topbar.php'; ?>

        <div class="content-wrapper">
            <div class="panel">
                <div class="panel-head">
                    <h3><i class="fa-solid fa-users"></i> Data Calon Jamaah</h3>
                    <button class="btn-custom btn-gold-custom" onclick="Swal.fire('Info', 'Fitur tambah data', 'info')">
                        <i class="fa-solid fa-plus"></i> Tambah
                    </button>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    <th>Layanan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Ahmad Fauzi</td>
                                    <td>1101010101</td>
                                    <td>Haji Reguler</td>
                                    <td><span class="badge-status verified">Terverifikasi</span></td>
                                    <td>
                                        <button class="btn-action edit"><i class="fa-solid fa-pen"></i></button>
                                        <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Siti Aminah</td>
                                    <td>1101010102</td>
                                    <td>Umrah</td>
                                    <td><span class="badge-status pending">Pending</span></td>
                                    <td>
                                        <button class="btn-action edit"><i class="fa-solid fa-pen"></i></button>
                                        <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'footer.php'; ?>
    </main>
</body>

</html>
<?php include '../crypt-start.php'; ?>
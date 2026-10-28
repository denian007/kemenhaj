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
    <title>Surat keluar</title>
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
                    <h3><i class="fa-solid fa-envelope-open-text"></i> Daftar Surat Keluar</h3>
                    <button class="btn-custom btn-primary-custom" onclick="Swal.fire('Info', 'Fitur tambah surat', 'info')">
                        <i class="fa-solid fa-plus"></i> Tambah Surat
                    </button>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. Surat</th>
                                    <th>Pengirim</th>
                                    <th>Perihal</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SM/001/2025</td>
                                    <td>Kemenag Aceh</td>
                                    <td>Undangan Rapat Koordinasi Haji</td>
                                    <td>15 Jan 2025</td>
                                    <td><button class="btn-action edit"><i class="fa-solid fa-eye"></i></button></td>
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
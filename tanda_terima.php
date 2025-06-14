<?php
$conn = new mysqli("localhost", "root", "", "hr_filing_system");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Proses SIMPAN / UPDATE
if (isset($_POST['submit'])) {
  $id         = $_POST['id'];
  $npk        = $_POST['npk'];
  $nama       = $_POST['nama'];
  $dept       = $_POST['dept'];
  $tanggal    = $_POST['tanggal'];
  $keterangan = $_POST['keterangan'];
  $file       = $_FILES['file_upload']['name'];
  $tmp        = $_FILES['file_upload']['tmp_name'];
  
  // Validasi dan upload file
  if ($file) {
    move_uploaded_file($tmp, "uploads/" . $file);
  }

  if ($id == "") {
    $sql = "INSERT INTO tanda_terima (npk, nama, dept, tanggal, keterangan, file_upload)
            VALUES ('$npk', '$nama', '$dept', '$tanggal', '$keterangan', '$file')";
  } else {
    $sql = "UPDATE tanda_terima SET
              npk='$npk', nama='$nama', dept='$dept', tanggal='$tanggal', keterangan='$keterangan'"
            . ($file ? ", file_upload='$file'" : "")
            . " WHERE id=$id";
  }
  $conn->query($sql);
  header("Location: tanda_terima.php"); exit;
}

// Proses HAPUS
if (isset($_GET['hapus'])) {
  $conn->query("DELETE FROM tanda_terima WHERE id=" . intval($_GET['hapus']));
  header("Location: tanda_terima.php"); exit;
}

// Ambil data untuk EDIT
$id_edit    = "";
$npk        = $nama = $dept = $tanggal = $keterangan = $file_upload = "";
if (isset($_GET['edit'])) {
  $id_edit = intval($_GET['edit']);
  $r       = $conn->query("SELECT * FROM tanda_terima WHERE id=$id_edit")->fetch_assoc();
  $npk        = $r['npk'];
  $nama       = $r['nama'];
  $dept       = $r['dept'];
  $tanggal    = $r['tanggal'];
  $keterangan = $r['keterangan'];
  $file_upload= $r['file_upload'];
}

// Proses Pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Proses Sorting
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'id';
$order_dir = isset($_GET['order_dir']) && $_GET['order_dir'] == 'desc' ? 'desc' : 'asc';
$order_link = $order_dir == 'asc' ? 'desc' : 'asc'; // Link untuk mengubah arah pengurutan

// Ambil data dengan filter pencarian dan pengurutan
$query = "SELECT * FROM tanda_terima WHERE npk LIKE '%$search%' OR nama LIKE '%$search%' OR dept LIKE '%$search%' ORDER BY $order_by $order_dir";
$res = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Tanda Terima</title>
  <style>
    :root {
      --primary-color: #1A3A5D;
      --secondary-color: #F2F2F2;
      --accent-color: #4CAF50;
      --button-color: #FF9F00;
      --text-color: #333333;
      --card-bg: #FFFFFF;
      --shadow-light: rgba(0,0,0,0.05);
      --shadow-dark: rgba(0,0,0,0.1);
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'Roboto', sans-serif;
      background: var(--secondary-color);
      color: var(--text-color);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    header {
      background: var(--primary-color);
      color: #fff;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      box-shadow: 0 2px 5px var(--shadow-light);
    }
    header .brand {
      font-size: 1.5rem;
      font-weight: 700;
    }
	header nav {
	  display: flex;
	  margin-left: 120px;
	  gap: 1rem;
	}

	header nav a {
	  text-decoration: none;
	  color: #fff;
	  font-weight: 500;
	  margin: 0 1rem;
	}

	header nav a:hover {
	  text-decoration: underline;
	}
	
    main.container {
      flex: 1;
      max-width: 1000px;
      margin: 2rem auto;
      padding: 0 1rem;
    }
    .back-btn {
      display: inline-block;
      margin-bottom: 1rem;
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
    }
    .back-btn:hover {
      text-decoration: underline;
    }
    .card {
      background: var(--card-bg);
      border-radius: 8px;
      padding: 1.5rem;
      box-shadow: 0 6px 15px var(--shadow-light);
      margin-bottom: 2rem;
    }
    .card h2 {
      color: var(--primary-color);
      margin-bottom: 1rem;
      font-size: 1.3rem;
    }
    input, textarea, button {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
    }
    button {
      background: var(--button-color);
      color: #fff;
      border: none;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover {
      background: #e68a00;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }
    th, td {
      padding: 0.75rem;
      border: 1px solid #ddd;
      text-align: center;
    }
    th {
      background: orange;
      color: #fff;
    }
    a.action {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
    }
    a.action:hover {
      color: var(--accent-color);
    }
    small {
      color: #666;
      font-size: 0.9rem;
    }
    footer {
      background: var(--primary-color);
      color: #fff;
      text-align: center;
      padding: 1rem;
      box-shadow: 0 -2px 5px var(--shadow-light);
    }
  </style>
</head>
<body>
	<header>
  <div class="brand">HR Filing</div>
  <nav>
    <a href="index.php">Beranda</a>
    <a href="internal_memo.php">Internal Memo</a>
    <a href="berita_acara.php">Berita Acara</a>
	<a href="surat_menyurat.php">Surat Menyurat</a>
    <a href="data_pkl.php">PKL/Magang</a>
    <a href="data_pkwt.php">PKWT Karyawan</a>
  </nav>
</header>

  <main class="container">
    <a href="index.php" class="back-btn">← Kembali ke Beranda</a>
    
    <div class="card">
      <h2><?= $id_edit ? "Edit Tanda Terima" : "Input Tanda Terima" ?></h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id_edit ?>">
        <input type="text" name="npk" placeholder="NPK" value="<?= $npk ?>" required>
        <input type="text" name="nama" placeholder="Nama / Bagian" value="<?= $nama ?>" required>
        <input type="text" name="dept" placeholder="Departemen" value="<?= $dept ?>" required>
        <input type="date" name="tanggal" value="<?= $tanggal ?>" required>
        <textarea name="keterangan" placeholder="Keterangan"><?= $keterangan ?></textarea>
        <input type="file" name="file_upload" <?= $id_edit ? "" : "required" ?>>
        <?php if ($file_upload): ?>
          <small>File lama: <a href="uploads/<?= $file_upload ?>" target="_blank"><?= $file_upload ?></a></small>
        <?php endif; ?>
        <button type="submit" name="submit"><?= $id_edit ? "Update" : "Simpan" ?></button>
      </form>
    </div>

    <!-- Form Pencarian -->
    <form method="POST" class="card">
      <input type="text" name="search" placeholder="Cari berdasarkan NPK, Nama, atau Departemen" value="<?= htmlspecialchars($search) ?>" required>
      <button type="submit">Cari</button>
    </form>

    <!-- Daftar Tanda Terima -->
    <div class="card">
      <h2>Daftar Tanda Terima</h2>
      <table>
        <thead>
          <tr>
            <th><a href="?order_by=id&order_dir=<?= $order_link ?>">No</a></th>
            <th><a href="?order_by=npk&order_dir=<?= $order_link ?>">NPK</a></th>
            <th><a href="?order_by=nama&order_dir=<?= $order_link ?>">Nama</a></th>
            <th><a href="?order_by=dept&order_dir=<?= $order_link ?>">Dept</a></th>
            <th><a href="?order_by=tanggal&order_dir=<?= $order_link ?>">Tanggal</a></th>
            <th><a href="?order_by=keterangan&order_dir=<?= $order_link ?>">Keterangan</a></th>
            <th>File</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          while ($r = $res->fetch_assoc()):
          ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($r['npk']) ?></td>
            <td><?= htmlspecialchars($r['nama']) ?></td>
            <td><?= htmlspecialchars($r['dept']) ?></td>
            <td><?= htmlspecialchars($r['tanggal']) ?></td>
            <td><?= htmlspecialchars($r['keterangan']) ?></td>
            <td><a href="uploads/<?= htmlspecialchars($r['file_upload']) ?>" target="_blank">Lihat</a></td>
            <td>
              <a class="action" href="?edit=<?= $r['id'] ?>">Edit</a> |
              <a class="action" href="?hapus=<?= $r['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>

  <footer>
    &copy; <?= date('Y') ?> HR Filing System. All rights reserved.
  </footer>
</body>
</html>

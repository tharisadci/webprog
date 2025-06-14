<?php
$conn = new mysqli("localhost", "root", "", "hr_filing_system");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// SIMPAN & UPDATE
if (isset($_POST['submit'])) {
  $id         = $_POST['id'];
  $no_surat   = $_POST['no_surat'];
  $keterangan = $_POST['keterangan'];
  $tanggal    = $_POST['tanggal'];
  $file       = $_FILES['file_upload']['name'];
  $tmp        = $_FILES['file_upload']['tmp_name'];

  if ($file) {
    move_uploaded_file($tmp, "uploads/" . $file);
  }

  if ($id == "") {
    $sql = "INSERT INTO surat_menyurat (no_surat, keterangan, tanggal, file_upload)
            VALUES ('$no_surat', '$keterangan', '$tanggal', '$file')";
  } else {
    $sql = "UPDATE surat_menyurat SET 
              no_surat='$no_surat',
              keterangan='$keterangan',
              tanggal='$tanggal'" .
              ($file ? ", file_upload='$file'" : "") .
              " WHERE id=$id";
  }
  $conn->query($sql);
  header("Location: surat_menyurat.php");
}

// HAPUS
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $conn->query("DELETE FROM surat_menyurat WHERE id=$id");
  header("Location: surat_menyurat.php");
}

// AMBIL DATA untuk EDIT
$id_edit = "";
$no_surat = $keterangan = $tanggal = $file_upload = "";
if (isset($_GET['edit'])) {
  $id_edit = $_GET['edit'];
  $row = $conn->query("SELECT * FROM surat_menyurat WHERE id=$id_edit")->fetch_assoc();
  $no_surat   = $row['no_surat'];
  $keterangan = $row['keterangan'];
  $tanggal    = $row['tanggal'];
  $file_upload = $row['file_upload'];
}

// CARI & FILTER
$keyword = $_GET['keyword'] ?? '';
$tgl_dari = $_GET['tgl_dari'] ?? '';
$tgl_sampai = $_GET['tgl_sampai'] ?? '';
$where = "WHERE 1";

if ($keyword) {
  $where .= " AND (no_surat LIKE '%$keyword%' OR keterangan LIKE '%$keyword%')";
}

if ($tgl_dari && $tgl_sampai) {
  $where .= " AND tanggal BETWEEN '$tgl_dari' AND '$tgl_sampai'";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Filing Surat Menyurat</title>
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
    input, textarea, button, select {
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
      background: var(--primary-color);
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
    .search-filter form {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1rem;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>
	<header>
  <div class="brand">HR Filing</div>
  <nav>
    <a href="index.php">Beranda</a>
    <a href="tanda_terima.php">Tanda Terima</a>
    <a href="internal_memo.php">Internal Memo</a>
	<a href="berita_acara.php">Berita Acara</a>
    <a href="data_pkl.php">PKL/Magang</a>
    <a href="data_pkwt.php">PKWT Karyawan</a>
  </nav>
</header>

  <main class="container">
    <a href="index.php" class="back-btn">← Kembali ke Beranda</a>

    <div class="card">
      <h2><?= $id_edit ? "Edit Surat Menyurat" : "Input Surat Menyurat" ?></h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id_edit ?>">
        <input type="text" name="no_surat" placeholder="No Surat" value="<?= $no_surat ?>" required>
        <textarea name="keterangan" placeholder="Keterangan Surat"><?= $keterangan ?></textarea>
        <input type="date" name="tanggal" value="<?= $tanggal ?>" required>
        <input type="file" name="file_upload" <?= $id_edit ? "" : "required" ?>>
        <?php if ($file_upload): ?>
          <small>File lama: <a href="uploads/<?= $file_upload ?>" target="_blank"><?= $file_upload ?></a></small>
        <?php endif; ?>
        <button type="submit" name="submit"><?= $id_edit ? "Update" : "Simpan" ?></button>
      </form>
    </div>

    <div class="card">
      <h2>Cari & Filter Data Surat</h2>
      <div class="search-filter">
        <form method="GET">
          <input type="text" name="keyword" placeholder="Cari No Surat / Keterangan" value="<?= $keyword ?>">
          <input type="date" name="tgl_dari" value="<?= $tgl_dari ?>">
          <input type="date" name="tgl_sampai" value="<?= $tgl_sampai ?>">
          <button type="submit">Terapkan</button>
        </form>
      </div>
    </div>

    <div class="card">
      <h2>Data Surat Menyurat</h2>
      <table>
        <thead>
          <tr>
            <th>No</th><th>No Surat</th><th>Keterangan</th><th>Tanggal</th><th>File</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $res = $conn->query("SELECT * FROM surat_menyurat $where ORDER BY id DESC");
          $no = 1;
          while ($r = $res->fetch_assoc()):
          ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($r['no_surat']) ?></td>
            <td><?= htmlspecialchars($r['keterangan']) ?></td>
            <td><?= htmlspecialchars($r['tanggal']) ?></td>
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

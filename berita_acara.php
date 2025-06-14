<?php
$conn = new mysqli("localhost", "root", "", "hr_filing_system");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// SIMPAN & UPDATE
if (isset($_POST['submit'])) {
  $id         = $_POST['id'];
  $no_berita  = $_POST['no_berita'];
  $nama_bagian= $_POST['nama_bagian'];
  $perihal    = $_POST['perihal'];
  $tanggal    = $_POST['tanggal'];
  $file       = $_FILES['file_upload']['name'];
  $tmp        = $_FILES['file_upload']['tmp_name'];

  if ($file) {
    move_uploaded_file($tmp, "uploads/" . $file);
  }

  if ($id == "") {
    $sql = "INSERT INTO berita_acara (no_berita, nama_bagian, perihal, tanggal, file_upload)
            VALUES ('$no_berita', '$nama_bagian', '$perihal', '$tanggal', '$file')";
  } else {
    $sql = "UPDATE berita_acara SET 
              no_berita='$no_berita',
              nama_bagian='$nama_bagian',
              perihal='$perihal',
              tanggal='$tanggal'" .
              ($file ? ", file_upload='$file'" : "") . 
              " WHERE id=$id";
  }
  $conn->query($sql);
  header("Location: berita_acara.php");
}

// HAPUS
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $conn->query("DELETE FROM berita_acara WHERE id=$id");
  header("Location: berita_acara.php");
}

// EDIT
$id_edit = "";
$no_berita = $nama_bagian = $perihal = $tanggal = $file_upload = "";
if (isset($_GET['edit'])) {
  $id_edit = $_GET['edit'];
  $row = $conn->query("SELECT * FROM berita_acara WHERE id=$id_edit")->fetch_assoc();
  $no_berita = $row['no_berita'];
  $nama_bagian = $row['nama_bagian'];
  $perihal = $row['perihal'];
  $tanggal = $row['tanggal'];
  $file_upload = $row['file_upload'];
}

// Filter dan Sort
$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'id';
$order = $_GET['order'] ?? 'DESC';

$allowed_sort = ['no_berita', 'nama_bagian', 'tanggal'];
if (!in_array($sort_by, $allowed_sort)) $sort_by = 'id';
$order = strtoupper($order) == 'ASC' ? 'ASC' : 'DESC';

$where = $search ? "WHERE no_berita LIKE '%$search%' OR nama_bagian LIKE '%$search%' OR perihal LIKE '%$search%'" : '';
$res = $conn->query("SELECT * FROM berita_acara $where ORDER BY $sort_by $order");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Berita Acara - HR Filing</title>
  <style>
    :root {
      --primary-color: #f8c8dc;
      --secondary-color: #fdf1f9;
      --accent-color: #b087c3;
      --button-color: #ff84a2;
      --text-color: #444;
      --white: #ffffff;
      --gray: #ddd;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: var(--secondary-color);
      color: var(--text-color);
    }

    header {
      background: var(--accent-color);
      padding: 1rem 2rem;
      color: var(--white);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header .brand {
      font-size: 1.5rem;
      font-weight: bold;
    }

    header nav a {
      margin-left: 1rem;
      color: var(--white);
      text-decoration: none;
      font-weight: 500;
    }

    header nav a:hover {
      text-decoration: underline;
    }

    main {
      max-width: 960px;
      margin: 2rem auto;
      background: var(--white);
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    h2 {
      color: var(--accent-color);
      margin-bottom: 1rem;
    }

    form input, form textarea, form select {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border: 1px solid var(--gray);
      border-radius: 8px;
    }

    form button {
      background: var(--button-color);
      color: var(--white);
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    }

    form button:hover {
      background: #ff6b8a;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }

    th, td {
      padding: 0.75rem;
      border: 1px solid var(--gray);
      text-align: center;
	  word-wrap: break-word;
	  white-space: wrap;
    }

    th {
      background: var(--primary-color);
    }

    td a {
      color: var(--accent-color);
      font-weight: bold;
      text-decoration: none;
    }

    td a:hover {
      text-decoration: underline;
    }

    .filter-form {
      display: flex;
      gap: 1rem;
      margin: 1rem 0;
      flex-wrap: wrap;
    }

    .filter-form input,
    .filter-form select {
      flex: 1;
      min-width: 150px;
    }

    .back-btn {
      display: inline-block;
      margin-bottom: 1rem;
      color: var(--accent-color);
      text-decoration: none;
      font-weight: bold;
    }

    .back-btn:hover {
      text-decoration: underline;
    }

    footer {
      text-align: center;
      background: var(--accent-color);
      color: white;
      padding: 1rem;
      margin-top: 2rem;
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
      <a href="surat_menyurat.php">Surat</a>
      <a href="data_pkl.php">PKL</a>
      <a href="data_pkwt.php">PKWT</a>
    </nav>
  </header>

  <main>
    <a href="index.php" class="back-btn">← Kembali ke Beranda</a>

    <h2><?= $id_edit ? "Edit Berita Acara" : "Input Berita Acara" ?></h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $id_edit ?>">
      <input type="text" name="no_berita" placeholder="No Berita Acara" value="<?= $no_berita ?>" required>
      <input type="text" name="nama_bagian" placeholder="Nama/Bagian Karyawan" value="<?= $nama_bagian ?>" required>
      <textarea name="perihal" placeholder="Perihal Berita Acara"><?= $perihal ?></textarea>
      <input type="date" name="tanggal" value="<?= $tanggal ?>" required>
      <input type="file" name="file_upload" <?= $id_edit ? "" : "required" ?>>
      <?php if ($file_upload): ?>
        <small>File lama: <a href="uploads/<?= $file_upload ?>" target="_blank"><?= $file_upload ?></a></small>
      <?php endif; ?>
      <button type="submit" name="submit"><?= $id_edit ? "Update" : "Simpan" ?></button>
    </form>

    <h2>Data Berita Acara</h2>

<table border="1" cellpadding="10" cellspacing="0">
  <thead>
    <tr>
      <th>No</th>
      <th>No Berita Acara</th>
      <th>Nama/Bagian Karyawan</th>
      <th>Perihal</th>
      <th>Tanggal</th>
      <th>File Upload</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = 1;
    $query = "SELECT * FROM berita_acara ORDER BY tanggal DESC";
    $res = $conn->query($query);
    while ($r = $res->fetch_assoc()):
    ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= htmlspecialchars($r['no_berita']) ?></td>
      <td><?= htmlspecialchars($r['nama_bagian']) ?></td>
      <td><?= htmlspecialchars($r['perihal']) ?></td>
      <td><?= date('j M Y', strtotime($r['tanggal'])) ?></td>
      <td>
        <?php if (!empty($r['file_upload'])): ?>
          <a href="uploads/<?= htmlspecialchars($r['file_upload']) ?>" target="_blank">View</a>
        <?php else: ?>
          Tidak ada file
        <?php endif; ?>
      </td>
      <td>
        <a href="?edit=<?= $r['id'] ?>">Edit</a> |
        <a href="?hapus=<?= $r['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
  </main>

  <footer>
    &copy; <?= date('Y') ?> HR Filing System. All rights reserved.
  </footer>
</body>
</html>

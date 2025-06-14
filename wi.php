<?php
$conn = new mysqli("localhost", "root", "", "hr_filing_system");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Simpan & Update
if (isset($_POST['submit'])) {
  $id         = $_POST['id'];
  $no_wi 	  = $_POST['no_wi'];
  $nama_wi	  = $_POST['nama_wi'];
  $tanggal    = $_POST['tanggal'];
  $file       = $_FILES['file_upload']['name'];
  $tmp        = $_FILES['file_upload']['tmp_name'];

  if ($file) {
    move_uploaded_file($tmp, "uploads/" . $file);
  }

  if ($id == "") {
    $sql = "INSERT INTO wi (no_wi, nama_wi, tanggal, file_upload)
            VALUES ('$no_wi', '$nama_wi', '$tanggal', '$file')";
  } else {
    $sql = "UPDATE wi SET 
              no_wi='$no_wi',
              nama_wi='$nama_wi',
              tanggal='$tanggal'" . 
              ($file ? ", file_upload='$file'" : "") . 
              " WHERE id=$id";
  }
  $conn->query($sql);
  header("Location: wi.php");
}

// Hapus
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $conn->query("DELETE FROM wi WHERE id=$id");
  header("Location: wi.php");
}

// Edit
$id_edit = "";
$no_wi = $nama_wi = $tanggal = $file_upload = "";
if (isset($_GET['edit'])) {
  $id_edit = $_GET['edit'];
  $row = $conn->query("SELECT * FROM wi WHERE id=$id_edit")->fetch_assoc();
  $no_wi = $row['no_wi'];
  $nama_wi = $row['nama_wi'];
  $tanggal = $row['tanggal'];
  $file_upload = $row['file_upload'];
}

// Filter dan Sort
$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'id';
$order = $_GET['order'] ?? 'DESC';

$allowed_sort = ['no_wi', 'nama_wi', 'tanggal'];
if (!in_array($sort_by, $allowed_sort)) $sort_by = 'id';
$order = strtoupper($order) == 'ASC' ? 'ASC' : 'DESC';

$where = $search ? "WHERE no_wi LIKE '%$search%' OR nama_wi LIKE '%$search%'" : '';
$res = $conn->query("SELECT * FROM wi $where ORDER BY $sort_by $order");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>WI - HR Filing</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap');

  * {
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
  }

  body {
    margin: 0;
    background: #f2f2f2;
    color: #333;
  }

  header {
    background: linear-gradient(90deg, #2f3a8f, #c7bfff);
    color: white;
    padding: 1.2rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  }

  header h1 {
    margin: 0;
    font-size: 1.6rem;
    font-weight: 600;
  }

  nav a {
    color: white;
    text-decoration: none;
    margin-left: 1rem;
    font-weight: 400;
  }

  nav a:hover {
    text-decoration: underline;
  }

  .container {
    max-width: 960px;
    margin: 2rem auto;
    padding: 2rem;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  }

  h2 {
    color: #2f3a8f;
    margin-bottom: 1rem;
    font-weight: 600;
  }

  form input[type="text"],
  form input[type="date"],
  form input[type="file"],
  form select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 10px;
    margin-bottom: 1rem;
    font-size: 1rem;
  }

  form button {
    background: #2f3a8f;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 10px;
    font-weight: 500;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  form button:hover {
    background: #1f2960;
  }

  .filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1rem;
    margin-bottom: 1rem;
  }

  .table-wrapper {
    overflow-x: auto;
    margin-top: 1.5rem;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
  }

  table th, table td {
    border: 1px solid #e5e5e5;
    padding: 0.75rem;
    text-align: center;
    word-break: break-word;
    max-width: 180px;
  }

  table th {
    background: #c7bfff;
    color: #2f3a8f;
    font-weight: 600;
  }

  .action a {
    margin: 0 0.3rem;
    text-decoration: none;
    color: #2f3a8f;
    font-weight: 500;
  }

  .action a:hover {
    color: #f29cc2;
  }

  footer {
    text-align: center;
    padding: 1rem;
    margin-top: 3rem;
    background: #2f3a8f;
    color: white;
    font-size: 0.9rem;
  }

  @media (max-width: 600px) {
    nav {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
    }

    .filter-form {
      flex-direction: column;
    }
  }
</style>


</head>
<body>
  <header>
    <h1>HR Filing - WI</h1>
    <nav>
      <a href="index.php">Beranda</a>
      <a href="tanda_terima.php">Tanda Terima</a>
      <a href="internal_memo.php">Internal Memo</a>
      <a href="surat_menyurat.php">Surat Menyurat</a>
      <a href="data_pkl.php">PKL</a>
      <a href="data_pkwt.php">PKWT</a>
    </nav>
  </header>

  <div class="container">
    <h2><?= $id_edit ? "Edit WI" : "Input WI" ?></h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $id_edit ?>">
      <input type="text" name="no_wi" placeholder="Nomor WI" value="<?= $no_wi ?>" required>
      <input type="text" name="nama_wi" placeholder="Nama WI" value="<?= $nama_wi ?>" required>
      <input type="date" name="tanggal" value="<?= $tanggal ?>" required>
      <input type="file" name="file_upload" <?= $id_edit ? "" : "required" ?>>
      <?php if ($file_upload): ?>
        <small>File saat ini: <a href="uploads/<?= $file_upload ?>" target="_blank"><?= $file_upload ?></a></small>
      <?php endif; ?>
      <button type="submit" name="submit"><?= $id_edit ? "Update" : "Simpan" ?></button>
    </form>

    <h2>Data WI</h2>
    <form method="GET" class="filter-form">
      <input type="text" name="search" placeholder="Cari WI..." value="<?= htmlspecialchars($search) ?>">
      <select name="sort_by">
        <option value="tanggal" <?= $sort_by == 'tanggal' ? 'selected' : '' ?>>Sortir Tanggal</option>
        <option value="no_wi" <?= $sort_by == 'no_wi' ? 'selected' : '' ?>>Sortir No WI</option>
        <option value="nama_wi" <?= $sort_by == 'nama_wi' ? 'selected' : '' ?>>Sortir Nama</option>
      </select>
      <select name="order">
        <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Terbaru</option>
        <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Terlama</option>
      </select>
      <button type="submit">Filter</button>
    </form>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>No WI</th>
            <th>Nama WI</th>
            <th>Tanggal</th>
            <th>File</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($r = $res->fetch_assoc()): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($r['no_wi']) ?></td>
            <td><?= htmlspecialchars($r['nama_wi']) ?></td>
            <td><?= htmlspecialchars($r['tanggal']) ?></td>
            <td><a href="uploads/<?= htmlspecialchars($r['file_upload']) ?>" target="_blank">View</a></td>
            <td class="action">
              <a href="?edit=<?= $r['id'] ?>">Edit</a> |
              <a href="?hapus=<?= $r['id'] ?>" onclick="return confirm('Yakin ingin hapus data ini?')">Hapus</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <footer>
    &copy; <?= date('Y') ?> HR Filing System
  </footer>
</body>
</html>

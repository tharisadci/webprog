<?php
$conn = new mysqli("localhost", "root", "", "hr_filing_system");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// SIMPAN & UPDATE
if (isset($_POST['submit'])) {
  $id = $_POST['id'];
  $no_memo = $_POST['no_memo'];
  $nama_bagian = $_POST['nama_bagian'];
  $perihal = $_POST['perihal'];
  $tanggal = $_POST['tanggal'];
  $file = $_FILES['file_upload']['name'];
  $tmp = $_FILES['file_upload']['tmp_name'];

  if ($file != "") move_uploaded_file($tmp, "uploads/" . $file);

  if ($id == "") {
    $query = "INSERT INTO internal_memo (no_memo, nama_bagian, perihal, tanggal, file_upload) 
              VALUES ('$no_memo', '$nama_bagian', '$perihal', '$tanggal', '$file')";
  } else {
    $query = "UPDATE internal_memo SET 
              no_memo='$no_memo', nama_bagian='$nama_bagian', perihal='$perihal', tanggal='$tanggal'" . 
              ($file != "" ? ", file_upload='$file'" : "") . 
              " WHERE id=$id";
  }

  $conn->query($query);
  header("Location: internal_memo.php");
}

// HAPUS
if (isset($_GET['hapus'])) {
  $conn->query("DELETE FROM internal_memo WHERE id=" . $_GET['hapus']);
  header("Location: internal_memo.php");
}

// EDIT
$id_edit = "";
$no_memo = $nama_bagian = $perihal = $tanggal = $file_upload = "";
if (isset($_GET['edit'])) {
  $id_edit = $_GET['edit'];
  $res = $conn->query("SELECT * FROM internal_memo WHERE id=$id_edit")->fetch_assoc();
  $no_memo = $res['no_memo'];
  $nama_bagian = $res['nama_bagian'];
  $perihal = $res['perihal'];
  $tanggal = $res['tanggal'];
  $file_upload = $res['file_upload'];
}

// Pencarian
$search_query = "";
if (isset($_GET['search'])) {
  $search_term = $_GET['search'];
  $search_query = "WHERE no_memo LIKE '%$search_term%' OR nama_bagian LIKE '%$search_term%' OR perihal LIKE '%$search_term%'";
}

// Pengurutan
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'id';
$order_dir = isset($_GET['order_dir']) ? $_GET['order_dir'] : 'ASC';
$order_query = "ORDER BY $order_by $order_dir";

// Query untuk mengambil data
$res = $conn->query("SELECT * FROM internal_memo $search_query $order_query");

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Internal Memo</title>
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

/* Styling untuk search input */
.search-bar {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
}
.search-bar input[type="text"] {
  width: 80%;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}
.search-bar button {
  background: var(--accent-color);
  color: #fff;
  padding: 0.75rem 1rem;
  border: none;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}
.search-bar button:hover {
  background: #45a049;
}

/* Styling untuk dropdown sort */
.sort-dropdown {
  margin-left: 1rem;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

  </style>
</head>
<body>
	<header>
  <div class="brand">HR Filing</div>
  <nav>
    <a href="index.php">Beranda</a>
    <a href="tanda_terima.php">Tanda Terima</a>
    <a href="berita_acara.php">Berita Acara</a>
	<a href="surat_menyurat.php">Surat Menyurat</a>
    <a href="data_pkl.php">PKL/Magang</a>
    <a href="data_pkwt.php">PKWT Karyawan</a>
  </nav>
</header>

  <main class="container">
    <a href="index.php" class="back-btn">← Kembali ke Beranda</a>

    <div class="card">
      <h2><?= $id_edit ? "Edit Internal Memo" : "Input Internal Memo" ?></h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id_edit ?>">
        <input type="text" name="no_memo" placeholder="No Memo" value="<?= $no_memo ?>" required>
        <input type="text" name="nama_bagian" placeholder="Nama/Bagian Karyawan" value="<?= $nama_bagian ?>" required>
        <textarea name="perihal" placeholder="Perihal Memo"><?= $perihal ?></textarea>
        <input type="date" name="tanggal" value="<?= $tanggal ?>" required>
        <input type="file" name="file_upload" <?= $id_edit ? "" : "required" ?>>
        <?php if ($file_upload): ?>
          <small>File lama: <a href="uploads/<?= $file_upload ?>" target="_blank"><?= $file_upload ?></a></small>
        <?php endif; ?>
        <button type="submit" name="submit"><?= $id_edit ? "Update" : "Simpan" ?></button>
      </form>
    </div>

    <!-- Form Pencarian -->
    <div class="card">
      <h2>Pencarian</h2>
      <form method="GET">
        <input type="text" name="search" placeholder="Cari No Memo, Nama, atau Perihal" value="<?= isset($search_term) ? $search_term : '' ?>">
        <button type="submit">Cari</button>
      </form>
    </div>

    <!-- Data Internal Memo -->
    <div class="card">
      <h2>Data Internal Memo</h2>
      <table>
        <thead>
          <tr>
            <th><a href="?order_by=id&order_dir=<?= $order_dir == 'ASC' ? 'DESC' : 'ASC' ?>">No</a></th>
            <th><a href="?order_by=no_memo&order_dir=<?= $order_dir == 'ASC' ? 'DESC' : 'ASC' ?>">No Memo</a></th>
            <th><a href="?order_by=nama_bagian&order_dir=<?= $order_dir == 'ASC' ? 'DESC' : 'ASC' ?>">Nama/Bagian</a></th>
            <th><a href="?order_by=perihal&order_dir=<?= $order_dir == 'ASC' ? 'DESC' : 'ASC' ?>">Perihal</a></th>
            <th><a href="?order_by=tanggal&order_dir=<?= $order_dir == 'ASC' ? 'DESC' : 'ASC' ?>">Tanggal</a></th>
            <th>File</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          while ($r = $res->fetch_assoc()):
          ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($r['no_memo']) ?></td>
            <td><?= htmlspecialchars($r['nama_bagian']) ?></td>
            <td><?= htmlspecialchars($r['perihal']) ?></td>
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

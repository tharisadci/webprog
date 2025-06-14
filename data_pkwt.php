<?php
$conn = new mysqli("localhost", "root", "", "hr_filing_system");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// SIMPAN & UPDATE
if (isset($_POST['submit'])) {
  $id             = $_POST['id'];
  $nama           = $_POST['nama_karyawan'];
  $kontrak_ke     = (int)$_POST['kontrak_ke'];
  $dept           = $_POST['dept'];
  $tanggal_mulai  = $_POST['tanggal_mulai'];
  $periode        = (int)$_POST['periode'];
  $tanggal_akhir  = date('Y-m-d', strtotime("+{$periode} months", strtotime($tanggal_mulai)));

  $file           = $_FILES['file_upload']['name'];
  $tmp            = $_FILES['file_upload']['tmp_name'];
  if ($file) move_uploaded_file($tmp, "uploads/" . $file);

  if ($id == "") {
    $sql = "INSERT INTO data_pkwt 
              (nama_karyawan, kontrak_ke, dept, tanggal_mulai, periode, tanggal_akhir, file_upload)
            VALUES 
              ('$nama', $kontrak_ke, '$dept', '$tanggal_mulai', $periode, '$tanggal_akhir', '$file')";
  } else {
    $sql = "UPDATE data_pkwt SET
              nama_karyawan = '$nama',
              kontrak_ke    = $kontrak_ke,
              dept          = '$dept',
              tanggal_mulai = '$tanggal_mulai',
              periode       = $periode,
              tanggal_akhir = '$tanggal_akhir'".
              ($file ? ", file_upload='$file'" : "").
              " WHERE id=$id";
  }
  $conn->query($sql);
  header("Location: data_pkwt.php");
}

// HAPUS
if (isset($_GET['hapus'])) {
  $conn->query("DELETE FROM data_pkwt WHERE id=".$_GET['hapus']);
  header("Location: data_pkwt.php");
}

// AMBIL DATA untuk EDIT
$id_edit         = "";
$nama            = $kontrak_ke = $dept = $tanggal_mulai = $periode = $file_upload = "";
if (isset($_GET['edit'])) {
  $id_edit = $_GET['edit'];
  $r = $conn->query("SELECT * FROM data_pkwt WHERE id=$id_edit")->fetch_assoc();
  $nama           = $r['nama_karyawan'];
  $kontrak_ke     = $r['kontrak_ke'];
  $dept           = $r['dept'];
  $tanggal_mulai  = $r['tanggal_mulai'];
  $periode        = $r['periode'];
  $file_upload    = $r['file_upload'];
}

// Fitur Search
$search = "";
if (isset($_POST['search'])) {
  $search = $_POST['search'];
}

// Sorting
$order_by = "id DESC"; // Default sorting
if (isset($_GET['sort_by'])) {
  $sort_by = $_GET['sort_by'];
  $order_by = "$sort_by DESC";  // sorting berdasarkan kolom
}

$sql_query = "SELECT * FROM data_pkwt WHERE nama_karyawan LIKE '%$search%' ORDER BY $order_by";
$res = $conn->query($sql_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Filing Data PKWT Karyawan</title>
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
</style>

<body>
	<header>
  <div class="brand">HR Filing</div>
  <nav>
    <a href="index.php">Beranda</a>
    <a href="tanda_terima.php">Tanda Terima</a>
    <a href="internal_memo.php">Berita Acara</a>
	<a href="berita_acara.php">Berita Acara</a>
    <a href="surat_menyurat.php">Surat Menyurat</a>
    <a href="data_pkl.php">PKL/Magang</a>
  </nav>
</header>

  <main class="container">
    <a href="index.php" class="back-btn">← Kembali ke Beranda</a>

    <div class="card">
      <h2><?= $id_edit ? "Edit Data PKWT Karyawan" : "Input Data PKWT Karyawan" ?></h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id_edit ?>">
        <input type="text" name="nama_karyawan" placeholder="Nama Karyawan" value="<?= $nama ?>" required>
        <input type="number" name="kontrak_ke" placeholder="Kontrak Ke" value="<?= $kontrak_ke ?>" min="1" required>
        <input type="text" name="dept" placeholder="Bagian/Dept" value="<?= $dept ?>" required>
        <label>Tanggal Mulai PKWT:</label>
        <input type="date" name="tanggal_mulai" value="<?= $tanggal_mulai ?>" required>
        <input type="number" name="periode" placeholder="Periode (bulan)" value="<?= $periode ?>" min="1" required>
        <input type="file" name="file_upload" <?= $id_edit ? "" : "required" ?>>
        <?php if ($file_upload): ?>
          <small>File lama: <a href="uploads/<?= $file_upload ?>" target="_blank"><?= $file_upload ?></a></small><br>
        <?php endif; ?>
        <button type="submit" name="submit"><?= $id_edit ? "Update" : "Simpan" ?></button>
      </form>
    </div>

    <div class="card">
      <h2>Data PKWT Karyawan</h2>

      <!-- Fitur Search -->
      <form method="POST">
        <input type="text" name="search" value="<?= $search ?>" placeholder="Cari Nama Karyawan..." />
        <button type="submit">Cari</button>
      </form>

      <table>
        <thead>
          <tr>
            <th><a href="?sort_by=id">No</a></th>
            <th><a href="?sort_by=nama_karyawan">Nama</a></th>
            <th><a href="?sort_by=kontrak_ke">Kontrak Ke</a></th>
            <th><a href="?sort_by=dept">Dept</a></th>
            <th><a href="?sort_by=tanggal_mulai">Mulai</a></th>
            <th><a href="?sort_by=periode">Periode</a></th>
            <th><a href="?sort_by=tanggal_akhir">Akhir</a></th>
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
            <td><?= $r['nama_karyawan'] ?></td>
            <td><?= $r['kontrak_ke'] ?></td>
            <td><?= $r['dept'] ?></td>
            <td><?= $r['tanggal_mulai'] ?></td>
            <td><?= $r['periode'] ?></td>
            <td><?= $r['tanggal_akhir'] ?></td>
            <td><a href="uploads/<?= $r['file_upload'] ?>" target="_blank">Lihat</a></td>
            <td>
              <a href="?edit=<?= $r['id'] ?>" class="action">✏️</a> | 
              <a href="?hapus=<?= $r['id'] ?>" class="action" onclick="return confirm('Yakin hapus?')">🗑️</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>

  <footer>
    &copy; 2025 HR Filing System
  </footer>
</body>
</html>

<?php
$conn = new mysqli("localhost", "root", "", "hr_filing_system");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// SIMPAN & UPDATE
if (isset($_POST['submit'])) {
  $id            = $_POST['id'];
  $nama          = $_POST['nama'];
  $asal_sekolah  = $_POST['asal_sekolah'];
  $bagian        = $_POST['bagian'];
  $tanggal_mulai = $_POST['tanggal_mulai'];
  $periode       = (int)$_POST['periode'];
  $tanggal_akhir = date('Y-m-d', strtotime("+{$periode} months", strtotime($tanggal_mulai)));

  $file     = $_FILES['file_upload']['name'];
  $tmp      = $_FILES['file_upload']['tmp_name'];
  if ($file) move_uploaded_file($tmp, "uploads/" . $file);

  if ($id == "") {
    $sql = "INSERT INTO data_pkl 
              (nama, asal_sekolah, bagian, tanggal_mulai, periode, tanggal_akhir, file_upload)
            VALUES 
              ('$nama', '$asal_sekolah', '$bagian', '$tanggal_mulai', $periode, '$tanggal_akhir', '$file')";
  } else {
    $sql = "UPDATE data_pkl SET
              nama         = '$nama',
              asal_sekolah = '$asal_sekolah',
              bagian       = '$bagian',
              tanggal_mulai= '$tanggal_mulai',
              periode      = $periode,
              tanggal_akhir= '$tanggal_akhir'".
              ($file ? ", file_upload='$file'" : ""). 
              " WHERE id=$id";
  }
  $conn->query($sql);
  header("Location: data_pkl.php");
}

// HAPUS
if (isset($_GET['hapus'])) {
  $conn->query("DELETE FROM data_pkl WHERE id=".$_GET['hapus']);
  header("Location: data_pkl.php");
}

// AMBIL DATA untuk EDIT
$id_edit = "";
$nama = $asal_sekolah = $bagian = $tanggal_mulai = $periode = $file_upload = "";
if (isset($_GET['edit'])) {
  $id_edit = $_GET['edit'];
  $r = $conn->query("SELECT * FROM data_pkl WHERE id=$id_edit")->fetch_assoc();
  $nama           = $r['nama'];
  $asal_sekolah   = $r['asal_sekolah'];
  $bagian         = $r['bagian'];
  $tanggal_mulai  = $r['tanggal_mulai'];
  $periode        = $r['periode'];
  $file_upload    = $r['file_upload'];
}

// HANDLE PENCARIAN
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";
$search_query = $search ? "WHERE nama LIKE '%$search%' OR asal_sekolah LIKE '%$search%' OR bagian LIKE '%$search%'" : "";

// HANDLE SORTING
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'id'; // Default sort by 'id'
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC'; // Default sort order 'ASC'
$order_query = "ORDER BY $sort_column $sort_order"; // Sorting query

$res = $conn->query("SELECT * FROM data_pkl $search_query $order_query");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Filing Data PKL/Magang</title>
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
      cursor: pointer;
    }

    th:hover {
      background: #4CAF50;
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
  <script>
    // Fungsi untuk menangani klik pada header kolom untuk mengurutkan data
    function sortTable(column) {
      const currentSort = new URLSearchParams(window.location.search);
      let sortOrder = currentSort.get('sort_order') === 'ASC' ? 'DESC' : 'ASC'; // Toggle order
      currentSort.set('sort_column', column);
      currentSort.set('sort_order', sortOrder);
      window.location.search = currentSort.toString();
    }
  </script>
</head>
<body>
	<header>
  <div class="brand">HR Filing</div>
  <nav>
    <a href="index.php">Beranda</a>
    <a href="tanda_terima.php">Tanda Terima</a>
    <a href="internal_memo.php">Berita Acara</a>
	<a href="berita_acara.php">Berita Acara</a>
    <a href="surat_menyurat.php">Surat Menyurat</a>
    <a href="data_pkwt.php">PKWT Karyawan</a>
  </nav>
</header>

  <main class="container">
    <a href="index.php" class="back-btn">← Kembali ke Beranda</a>

    <div class="card">
      <h2><?= $id_edit ? "Edit Data PKL/Magang" : "Input Data PKL/Magang" ?></h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id_edit ?>">
        <input type="text" name="nama" placeholder="Nama" value="<?= $nama ?>" required>
        <input type="text" name="asal_sekolah" placeholder="Asal Sekolah/Universitas" value="<?= $asal_sekolah ?>" required>
        <input type="text" name="bagian" placeholder="Bagian" value="<?= $bagian ?>" required>
        <label>Tanggal Mulai PKL:</label>
        <input type="date" name="tanggal_mulai" value="<?= $tanggal_mulai ?>" required>
        <input type="number" name="periode" min="1" placeholder="Periode (bulan)" value="<?= $periode ?>" required>
        <input type="file" name="file_upload" <?= $id_edit ? "" : "required" ?>>
        <?php if ($file_upload): ?>
          <small>File lama: <a href="uploads/<?= $file_upload ?>" target="_blank"><?= $file_upload ?></a></small><br>
        <?php endif; ?>
        <button type="submit" name="submit"><?= $id_edit ? "Update" : "Simpan" ?></button>
      </form>
    </div>

    <div class="card">
      <h2>Data PKL/Magang</h2>
      <form method="GET">
        <input type="text" name="search" placeholder="Cari nama / asal sekolah / bagian..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Cari</button>
        <?php if ($search): ?>
          <a href="data_pkl.php" style="margin-left: 1rem; color: red; text-decoration: none;">Reset</a>
        <?php endif; ?>

      </form>

      <table>
        <thead>
          <tr>
            <th onclick="sortTable('id')">No</th>
            <th onclick="sortTable('nama')">Nama</th>
            <th onclick="sortTable('asal_sekolah')">Asal Sekolah</th>
            <th onclick="sortTable('bagian')">Bagian</th>
            <th onclick="sortTable('tanggal_mulai')">Mulai</th>
            <th onclick="sortTable('periode')">Periode (bln)</th>
            <th onclick="sortTable('tanggal_akhir')">Akhir</th>
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
            <td><?= htmlspecialchars($r['nama']) ?></td>
            <td><?= htmlspecialchars($r['asal_sekolah']) ?></td>
            <td><?= htmlspecialchars($r['bagian']) ?></td>
            <td><?= htmlspecialchars($r['tanggal_mulai']) ?></td>
            <td><?= htmlspecialchars($r['periode']) ?></td>
            <td><?= htmlspecialchars($r['tanggal_akhir']) ?></td>
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
    &copy; 2025 HR Filing System
  </footer>
</body>
</html>

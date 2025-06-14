<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pusat Bantuan</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #1A237E;
      --secondary-color: #F4F4F9;
      --accent-color: #9575CD;
      --button-color: #FFC107;
      --text-color: #333;
    }

    body {
      font-family: 'Roboto', sans-serif;
      background: var(--secondary-color);
      margin: 0;
      padding: 0;
      color: var(--text-color);
    }

    header {
      background-color: var(--primary-color);
      color: white;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    header h1 {
      margin: 0;
      font-size: 2.2rem;
    }

    main {
      padding: 40px 30px;
      max-width: 900px;
      margin: 0 auto;
      background: white;
      border-radius: 10px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }

    h2 {
      color: var(--primary-color);
      margin-top: 0;
    }

    .faq {
      margin-bottom: 30px;
    }

    .faq h3 {
      color: var(--accent-color);
      margin-bottom: 10px;
    }

    .faq p {
      margin: 0 0 15px;
      line-height: 1.6;
    }

    a.back-btn {
      display: inline-block;
      margin-top: 30px;
      background: var(--button-color);
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: 500;
      transition: background-color 0.3s ease;
    }

    a.back-btn:hover {
      background-color: #FFB300;
    }

    footer {
      background-color: var(--primary-color);
      color: white;
      text-align: center;
      padding: 15px;
      margin-top: 50px;
    }
  </style>
</head>
<body>

  <header>
    <h1>💡 Bantuan & Panduan</h1>
  </header>

  <main>
    <h2>FAQ (Pertanyaan Umum)</h2>

    <div class="faq">
      <h3>📌 Bagaimana cara menginput dokumen?</h3>
      <p>Pada halaman dashboard, klik salah satu modul (misalnya "Tanda Terima"). Setelah masuk, isi form yang tersedia sesuai kebutuhan dan tekan tombol <strong>Simpan</strong>.</p>
    </div>

    <div class="faq">
      <h3>📎 Apa yang harus dilakukan jika data salah input?</h3>
      <p>Setelah masuk ke modul yang sesuai, cari data yang ingin diedit lalu klik tombol <strong>Edit</strong>. Perbarui datanya lalu klik <strong>Simpan Perubahan</strong>.</p>
    </div>

    <div class="faq">
      <h3>🗑️ Bagaimana cara menghapus data?</h3>
      <p>Masuk ke modul yang diinginkan, lalu klik tombol <strong>Hapus</strong> di baris data yang ingin dihapus. Konfirmasi penghapusan saat diminta.</p>
    </div>

    <div class="faq">
      <h3>👩‍💼 Siapa yang bisa menggunakan dashboard ini?</h3>
      <p>Dashboard ini dirancang untuk divisi HRD dan pihak terkait pengelolaan dokumen personalia di PT Dharma Controlcable Indonesia.</p>
    </div>

    <div class="faq">
      <h3>📧 Saya butuh bantuan lebih lanjut, ke mana saya bisa menghubungi?</h3>
      <p>Silakan hubungi tim HRD melalui email: <a href="mailto:hrd@dci.co.id">hrd@dci.co.id</a> atau langsung ke Tharisa 😊</p>
    </div>

    <a class="back-btn" href="index.php">← Kembali ke Dashboard</a>
  </main>

  <footer>
    &copy; <?= date("Y"); ?> PT Dharma Controlcable Indonesia | HRD 💜
  </footer>

</body>
</html>

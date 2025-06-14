<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - HR Filing System</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #1A237E;
      --secondary-color: #F4F4F9;
      --button-color: #FFC107;
      --hover-color: #FFB300;
      --text-color: #2C2C2C;
    }

    body {
      font-family: 'Roboto', sans-serif;
      background-color: #1A237E;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background: #fff;
      padding: 90px 60px 90px 40px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    .login-container img.logo {
      width: 100px;
      margin-bottom: 20px;
    }

    .login-container h2 {
      margin: 0;
      font-size: 1.5rem;
      color: var(--primary-color);
    }

    .login-container p {
      font-size: 0.95rem;
      margin-bottom: 25px;
      color: #555;
    }

    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: var(--button-color);
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: var(--hover-color);
    }

    .error {
      color: red;
      margin-top: 15px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <img src="logo.png" alt="Logo" class="logo">
    <h2>HR Filing System</h2>
    <p>PT Dharma Controlcable Indonesia</p>
    
    <form method="POST" action="proses_login.php">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Masuk</button>
    </form>

    <?php if (isset($_GET['error'])): ?>
      <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
  </div>
</body>
</html>

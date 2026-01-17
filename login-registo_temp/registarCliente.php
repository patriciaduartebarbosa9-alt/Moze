<?php
include "config.php"; // liga à base de dados

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $foto = $_POST['foto-perfil'];
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO utilizadores (nome, email, foto, password) VALUES ('$nome','$email','$foto','$pass')";

    if (mysqli_query($conn, $sql)) {
        $mensagem = "Utilizador registado com sucesso!";
    } else {
        $mensagem = "Erro: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta charset="utf-8" />
        <title>RegistarCliente</title>
        <link rel="stylesheet" href="css/RegistarCliente.css" />
    </head>
    <body>
        <div class="registar-cliente-container">
            <!-- Painel Esquerdo -->
            <div class="welcome-panel">
                <h1 class="welcome-title">Bem Vindo de Volta!</h1>
                <p class="welcome-subtitle">Entre para desfrutar melhor do nosso website.</p>
                <a href="Entrar.html" class="btn-entrar">Entrar</a>
            </div>

            <!-- Painel Direito -->
            <div class="form-panel">
                <h2 class="form-title">Criar Conta</h2>
                
                <div class="social-icons">
                    <a href="#" class="social-icon instagram" title="Instagram">
                        <img src="imagens/instagram.svg" alt="Instagram">
                    </a>
                    <a href="#" class="social-icon linkedin" title="LinkedIn">
                        <img src="imagens/linkedin.svg" alt="LinkedIn">
                    </a>
                    <a href="#" class="social-icon facebook" title="Facebook">
                        <img src="imagens/facebook.svg" alt="Facebook">
                    </a>
                    <a href="#" class="social-icon twitter" title="Twitter">
                        <img src="imagens/twitter.svg" alt="Twitter">
                    </a>
                    <a href="#" class="social-icon google" title="Google">
                        <img src="imagens/google.svg" alt="Google">
                    </a>
                </div>

                <form class="registro-form">
                    <div class="form-group">
                        <label for="nome">Nome Completo*</label>
                        <input type="text" id="nome" name="nome" placeholder="Patricia Barbosa" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email*</label>
                        <input type="email" id="email" name="email" placeholder="patriciabarbosa@gmail.com" required>
                    </div>

                    <div class="form-group">
                        <label for="foto-perfil">Foto de Perfil*</label>
                        <input type="file" id="foto-perfil" name="foto-perfil" accept="image/*" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password*</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="patricia123" required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-registar">Registar</button>
                </form>
            </div>
        </div>

        <script>
            function togglePassword() {
                const passwordInput = document.getElementById('password');
                const eyeIcon = document.querySelector('.eye-icon');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.style.opacity = '0.6';
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.style.opacity = '1';
                }
            }
        </script>
    </body>
</html>
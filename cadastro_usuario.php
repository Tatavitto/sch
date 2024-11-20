<?php
session_start();

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'sch';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão com o banco falhou: " . $conn->connect_error);
}

$response = [
    "sucesso" => false,
    "mensagem" => "Erro desconhecido."
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $tecnico = isset($_POST['tecnico']) ? 1 : 0;

    if (!empty($nome) && !empty($email) && !empty($senha)) {

        $nome = $conn->real_escape_string($nome);
        $email = $conn->real_escape_string($email);
        $senha = password_hash($senha, PASSWORD_BCRYPT);

        $sql_check_email = "SELECT id FROM usuarios WHERE email = '$email'";
        $result = $conn->query($sql_check_email);
        
        if ($result->num_rows > 0) {
            $response["mensagem"] = "Erro: O email informado já está registrado.";
        } else {

            $sql = "INSERT INTO usuarios (nome, email, senha, tecnico) VALUES ('$nome', '$email', '$senha', '$tecnico')";

            if ($conn->query($sql) === TRUE) {
                $response["sucesso"] = true;
                $response["mensagem"] = "Usuário cadastrado com sucesso!";
            } else {
                $response["mensagem"] = "Erro ao cadastrar o usuário: " . $conn->error;
            }
        }
    } else {
        $response["mensagem"] = "Todos os campos são obrigatórios.";
    }

    echo json_encode($response);
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <style>
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Cadastro de Usuário</h1>


    <div id="mensagem"></div>


    <form id="formCadastro">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required><br><br>
        
        <label for="tecnico">Técnico:</label>
        <input type="checkbox" id="tecnico" name="tecnico"><br><br>

        <button type="submit">Cadastrar</button>
    </form>

    <script>

        document.getElementById('formCadastro').addEventListener('submit', function(e) {
            e.preventDefault();

            const nome = document.getElementById('nome').value;
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;

            const dados = {
                nome: nome,
                email: email,
                senha: senha
            };

            fetch('cadastro_usuario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(dados)
            })
            .then(response => response.json())
            .then(data => {

                const mensagemDiv = document.getElementById('mensagem');
                if (data.sucesso) {
                    mensagemDiv.innerHTML = data.mensagem;
                    mensagemDiv.style.color = 'green';
                } else {
                    mensagemDiv.innerHTML = data.mensagem;
                    mensagemDiv.style.color = 'red';
                }
            })
            .catch(error => {
                console.error('Erro ao enviar dados:', error);
            });
        });
    </script>
</body>
</html>


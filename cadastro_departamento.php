<?php
session_start();

$response = [
    "sucesso" => false,
    "mensagem" => "Erro desconhecido."
];

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'sch';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão com o banco falhou: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'] ?? '';

    if (!empty($nome)) {
        $nome = $conn->real_escape_string($nome);

        $sql = "INSERT INTO departamentos (nome) VALUES ('$nome')";

        if ($conn->query($sql) === TRUE) {
            $response["sucesso"] = true;
            $response["mensagem"] = "Departamento cadastrado com sucesso!";
        } else {
            $response["mensagem"] = "Erro ao cadastrar o departamento: " . $conn->error;
        }
    } else {
        $response["mensagem"] = "O nome do departamento é obrigatório.";
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
    <title>Cadastro de Departamento</title>
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
    <h1>Cadastro de Departamento</h1>

    <div id="mensagem"></div>

    <form id="formCadastroDepartamento">
        <label for="nome">Selecione o Departamento:</label>
        <select id="nome" name="nome" required>
            <option value="">Selecione...</option>
            <option value="RH">RH</option>
            <option value="Administração">Administração</option>
            <option value="Contabilidade">Contabilidade</option>
            <option value="Vendas">Vendas</option>
        </select><br><br>

        <button type="submit">Cadastrar</button>
    </form>

    <script>
        document.getElementById('formCadastroDepartamento').addEventListener('submit', function(e) {
            e.preventDefault();

            const nome = document.getElementById('nome').value;

            if (!nome) {
                alert("Por favor, selecione um departamento.");
                return;
            }

            const dados = {
                nome: nome
            };
            fetch('cadastro_departamento.php', {
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

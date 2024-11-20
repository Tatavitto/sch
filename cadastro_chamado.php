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
    $departamento = $_POST['departamento'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $prioridade = $_POST['prioridade'] ?? '';
    $responsavel = $_POST['responsavel'] ?? '';
    $data_hora_limite = $_POST['data_hora_limite'] ?? '';

    $criador = $_SESSION['usuario_nome'] ?? 'Usuário Desconhecido'; 

    if (!empty($departamento) && !empty($descricao) && !empty($prioridade) && !empty($responsavel) && !empty($data_hora_limite)) {
        $departamento = $conn->real_escape_string($departamento);
        $descricao = $conn->real_escape_string($descricao);
        $prioridade = $conn->real_escape_string($prioridade);
        $responsavel = $conn->real_escape_string($responsavel);
        $data_hora_limite = $conn->real_escape_string($data_hora_limite);
        $criador = $conn->real_escape_string($criador);

        $sql = "INSERT INTO chamados (criador, departamento, descricao, prioridade, responsavel, data_hora_limite) 
                VALUES ('$criador', '$departamento', '$descricao', '$prioridade', '$responsavel', '$data_hora_limite')";

        if ($conn->query($sql) === TRUE) {
            $response["sucesso"] = true;
            $response["mensagem"] = "Chamado criado com sucesso!";
        } else {
            $response["mensagem"] = "Erro ao criar o chamado: " . $conn->error;
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
    <title>Criar Chamado</title>
    <style>
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Criar Novo Chamado</h1>

    <div id="mensagem"></div>

    <form id="formCriarChamado">
        <label for="departamento">Departamento:</label>
        <select id="departamento" name="departamento" required>
            <option value="">Selecione...</option>
            <option value="RH">RH</option>
            <option value="Administração">Administração</option>
            <option value="Contabilidade">Contabilidade</option>
            <option value="Vendas">Vendas</option>
        </select><br><br>

        <label for="descricao">Descrição do Chamado:</label>
        <textarea id="descricao" name="descricao" required></textarea><br><br>

        <label for="prioridade">Prioridade:</label>
        <select id="prioridade" name="prioridade" required>
            <option value="baixa">Baixa</option>
            <option value="media">Média</option>
            <option value="alta">Alta</option>
        </select><br><br>

        <label for="responsavel">Responsável:</label>
        <input type="text" id="responsavel" name="responsavel" required><br><br>

        <label for="data_hora_limite">Data e Hora Limite:</label>
        <input type="datetime-local" id="data_hora_limite" name="data_hora_limite" required><br><br>

        <button type="submit">Criar Chamado</button>
    </form>

    <script>
        document.getElementById('formCriarChamado').addEventListener('submit', function(e) {
            e.preventDefault();

            const departamento = document.getElementById('departamento').value;
            const descricao = document.getElementById('descricao').value;
            const prioridade = document.getElementById('prioridade').value;
            const responsavel = document.getElementById('responsavel').value;
            const dataHoraLimite = document.getElementById('data_hora_limite').value;

            const dados = {
                departamento: departamento,
                descricao: descricao,
                prioridade: prioridade,
                responsavel: responsavel,
                data_hora_limite: dataHoraLimite
            };

            fetch('cadastro_chamado.php', {
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

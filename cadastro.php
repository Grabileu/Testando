<?php
// cadastro.php
// Recebe POST do formulário, valida, salva em dados.txt (JSON por linha) e mostra confirmação.

function limpar($str) {
    return trim(htmlspecialchars($str, ENT_QUOTES, 'UTF-8'));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Pegar e sanitizar os campos
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $idade = filter_input(INPUT_POST, 'idade', FILTER_SANITIZE_NUMBER_INT);
    $sexo  = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_STRING);

    // Validações simples
    $erros = [];
    if (!$email) {
        $erros[] = "Email inválido.";
    }
    if ($idade !== null && $idade !== false) {
        $idade = (int)$idade;
        if ($idade < 0 || $idade > 120) $erros[] = "Idade fora do intervalo esperado.";
    } else {
        $idade = ""; // campo vazio aceitável
    }
    if (!$sexo) $sexo = "";

    if (empty($erros)) {
        // Monta o registro como array
        $registro = [
            'data'  => date('Y-m-d H:i:s'),
            'email' => $email,
            'idade' => $idade,
            'sexo'  => $sexo
        ];

        // Converte para JSON e adiciona nova linha
        $linha = json_encode($registro, JSON_UNESCAPED_UNICODE) . PHP_EOL;

        // Salva com LOCK_EX para evitar condições de corrida
        $arquivo = __DIR__ . '/dados.txt';
        $salvou = file_put_contents($arquivo, $linha, FILE_APPEND | LOCK_EX);

        if ($salvou === false) {
            $mensagem = "Erro ao salvar os dados. Tente novamente.";
        } else {
            // Sucesso — mostra confirmação
            ?>
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width,initial-scale=1">
                <title>Confirmação</title>
                <style>
                    body { font-family: Arial, sans-serif; background:#f7f7fb; padding:30px; }
                    .box { background:#fff; border-radius:8px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,.06); max-width:700px; margin:auto; }
                    a { color:#2a7aeb; text-decoration:none; }
                </style>
            </head>
            <body>
            <div class="box">
                <h1>Cadastro recebido ✅</h1>
                <p><strong>Email:</strong> <?php echo limpar($email); ?></p>
                <p><strong>Idade:</strong> <?php echo ($idade !== "") ? limpar($idade) . " anos" : "—"; ?></p>
                <p><strong>Sexo:</strong> <?php echo ($sexo !== "") ? limpar($sexo) : "—"; ?></p>

                <p>Dados salvos com sucesso.</p>
                <p><a href="index.html">Voltar ao formulário</a></p>
            </div>
            </body>
            </html>
            <?php
            exit;
        }
    }
    // Se chegou aqui, houve erros
} else {
    $erros[] = "Acesso inválido. Envie dados via formulário.";
}

// Mostrar erros (se houver)
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Erro</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; background:#fff6f6; padding:30px; }
        .erro { background:#fff; border-left:6px solid #e74c3c; padding:12px 18px; border-radius:4px; max-width:700px; margin:auto; }
        a { color:#2a7aeb; text-decoration:none; }
    </style>
</head>
<body>
    <div class="erro">
        <h2>Problema ao enviar</h2>
        <ul>
            <?php
            if (!empty($erros)) {
                foreach ($erros as $e) {
                    echo "<li>" . htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . "</li>";
                }
            } else {
                echo "<li>Dados inválidos.</li>";
            }
            ?>
        </ul>
        <p><a href="index.html">Voltar e corrigir</a></p>
    </div>
</body>
</html>

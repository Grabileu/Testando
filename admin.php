<?php
// admin.php
// Proteção simples via HTTP Basic Auth (bom para uso local)
// Altere usuario e senha conforme quiser:
$USUARIO_ESPERADO = 'admin';
$SENHA_ESPERADA   = 'senha123';

// Verifica autenticação HTTP Basic
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])
    || $_SERVER['PHP_AUTH_USER'] !== $USUARIO_ESPERADO
    || $_SERVER['PHP_AUTH_PW'] !== $SENHA_ESPERADA) {

    header('WWW-Authenticate: Basic realm="Área Restrita"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Autenticação necessária.';
    exit;
}

// Se autenticado, lê o arquivo dados.txt
$arquivo = __DIR__ . '/dados.txt';
$linhas = [];
if (file_exists($arquivo)) {
    $conteudo = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($conteudo as $linha) {
        $obj = json_decode($linha, true);
        if ($obj) $linhas[] = $obj;
    }
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Painel Administrativo</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; padding:20px; background:#f4f6fb; }
        h1 { color:#333; }
        table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,.05); }
        th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
        th { background:#fafafa; }
        .small { color:#666; font-size:13px; }
        .actions { margin-top:12px; }
        .danger { color:#e74c3c; }
        a { color:#2a7aeb; text-decoration:none; }
    </style>
</head>
<body>
    <h1>Painel — Cadastros</h1>
    <p class="small">Somente usuários autenticados podem ver esses dados.</p>

    <?php if (empty($linhas)): ?>
        <p>Nenhum registro encontrado.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Data</th>
                    <th>Email</th>
                    <th>Idade</th>
                    <th>Sexo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($linhas as $i => $reg): ?>
                    <tr>
                        <td><?php echo $i+1; ?></td>
                        <td><?php echo htmlspecialchars($reg['data'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($reg['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($reg['idade'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($reg['sexo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                                    <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="actions">
        <p><a href="index.html">Voltar ao formulário</a></p>
        <p class="danger">Usuário logado: <?php echo htmlspecialchars($_SERVER['PHP_AUTH_USER']); ?></p>
    </div>
</body>
</html>

               

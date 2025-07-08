<?php

// facilitar a manutenção e leitura do código
define('NOTA_APROVACAO', 70);
define('FUSO_HORARIO', 'America/Sao_Paulo');

date_default_timezone_set(FUSO_HORARIO);

//  array vazio como padrão para evitar erros
$notas_input = $_POST['notas'] ?? [];
$alunos_processados = [];
$num_max_avaliacoes = 0;


if (!empty($notas_input) && is_array($notas_input)) {
    foreach ($notas_input as $aluno_input) {
        // Pula para o próximo se o dado não for um array, para maior robustez
        if (!is_array($aluno_input)) continue;

        $nome = $aluno_input['Aluno'] ?? 'Não informado';
        $avaliacoes = $aluno_input['Avaliacao'] ?? [];

        
        if (!is_array($avaliacoes)) $avaliacoes = [];

        $num_max_avaliacoes = max($num_max_avaliacoes, count($avaliacoes));

        
        $soma = array_sum($avaliacoes);
        $qtd = count($avaliacoes);
        $media = $qtd > 0 ? $soma / $qtd : 0;

        
        $situacao = $media >= NOTA_APROVACAO ? 'Aprovado' : 'Reprovado';

        $alunos_processados[] = ['nome' => $nome, 'avaliacoes' => $avaliacoes, 'media' => $media, 'situacao' => $situacao];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Notas</title>
    <link rel="stylesheet" href="APS.css">
</head>
<body>
    <div class="container">
        <h1>Relatório de Notas</h1>
        <p>Data de geração: <strong><?= date('d/m/Y') ?></strong></p>

        <?php if (!empty($alunos_processados)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Aluno</th>
                        <?php for ($i = 1; $i <= $num_max_avaliacoes; $i++): ?>
                            <th>Avaliação <?= $i ?></th>
                        <?php endfor; ?>
                        <th>Média Final</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alunos_processados as $aluno): 
                        $is_aprovado = $aluno['situacao'] === 'Aprovado';
                        $classe_linha = $is_aprovado ? 'aprovado' : 'reprovado';
                        $emoji = $is_aprovado ? '🥳' : '😥';
                    ?>
                        <tr class="<?= $classe_linha ?>">
                            <td><?= htmlspecialchars($aluno['nome']) ?></td>
                            <?php for ($i = 0; $i < $num_max_avaliacoes; $i++): ?>
                                <td>
                                    <?= isset($aluno['avaliacoes'][$i]) ? htmlspecialchars($aluno['avaliacoes'][$i]) : '–' ?>
                                </td>
                            <?php endfor; ?>
                            <td><?= number_format($aluno['media'], 2, ',', '.') ?></td>
                            <td>
                                <span class="carinha"><?= $emoji ?></span>
                                <?= htmlspecialchars($aluno['situacao']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum dado de nota foi enviado. Por favor, volte e preencha o formulário.</p>
        <?php endif; ?>

        <br>
        <a href="index.html">Novo Cadastro</a>
    </div>
</body>
</html>

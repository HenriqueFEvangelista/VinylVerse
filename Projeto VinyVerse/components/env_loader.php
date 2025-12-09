<?php

// Função simples para carregar .env sem Composer
function carregarEnv($path) {
    if (!file_exists($path)) return;

    $linhas = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($linhas as $linha) {
        $linha = trim($linha);

        // pular comentários ou linhas vazias
        if ($linha === '' || str_starts_with($linha, '#')) continue;

        list($chave, $valor) = explode('=', $linha, 2);

        $chave = trim($chave);
        $valor = trim($valor);

        putenv("$chave=$valor");
        $_ENV[$chave] = $valor;
        $_SERVER[$chave] = $valor;
    }
}

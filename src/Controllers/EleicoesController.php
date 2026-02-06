<?php

namespace JairoJeffersont\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use League\Csv\Reader;


class EleicoesController {

    private function abrirCsv($ano): array {
        $arquivo = __DIR__ . '/../../public/csv/votacao_candidato_munzona_' . $ano . '_AP.csv';
        if (!file_exists($arquivo)) {
            throw new \RuntimeException('Arquivo CSV não encontrado.');
        }

        $csv = Reader::createFromPath($arquivo, 'r');
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        $colunas = [
            'ANO_ELEICAO',
            'NM_MUNICIPIO',
            'DS_CARGO',
            'NM_CANDIDATO',
            'NM_URNA_CANDIDATO',
            'DS_SITUACAO_JULGAMENTO',
            'SG_PARTIDO',
            'NR_PARTIDO',
            'NM_COLIGACAO',
            'DS_COMPOSICAO_COLIGACAO',
            'QT_VOTOS_NOMINAIS_VALIDOS',
            'DS_SIT_TOT_TURNO',
            'CD_SIT_TOT_TURNO',
            'SQ_CANDIDATO',
            'SQ_COLIGACAO',
            'CD_MUNICIPIO',
            'SQ_CANDIDATO'
        ];

        $dados = [];

        foreach ($csv->getRecords() as $linha) {

            $linha = array_map(
                fn($v) => mb_convert_encoding($v, 'UTF-8', 'ISO-8859-1'),
                $linha
            );

            $linha = array_intersect_key($linha, array_flip($colunas));

            // 🔹 remover acentos do município
            if (isset($linha['NM_MUNICIPIO'])) {
                if (class_exists('Transliterator')) {
                    $linha['NM_MUNICIPIO'] = transliterator_transliterate(
                        'Any-Latin; Latin-ASCII; [:Nonspacing Mark:] Remove; Lower(); Upper()',
                        $linha['NM_MUNICIPIO']
                    );
                } else {
                    $linha['NM_MUNICIPIO'] = strtoupper(
                        iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $linha['NM_MUNICIPIO'])
                    );
                }
            }

            $dados[] = $linha;
        }

        return $dados;
    }

    public static function pegarDadosEleicaoVereador(?string $ano = null, ?string $partido = null, ?string $situacao = null, ?string $municipio = null): array {

        try {

            $instancia = new self();
            $dados = $instancia->abrirCsv($ano);

            // 1️⃣ Filtra primeiro
            $filtrados = array_filter($dados, function ($item) use ($partido, $situacao, $municipio) {

                if ($item['DS_CARGO'] !== 'Vereador') {
                    return false;
                }

                if ($partido !== null && $item['NR_PARTIDO'] !== $partido) {
                    return false;
                }

                if ($municipio !== null && $item['CD_MUNICIPIO'] !== $municipio) {
                    return false;
                }

                if ($situacao !== null && $item['CD_SIT_TOT_TURNO'] !== $situacao) {
                    return false;
                }

                return true;
            });

            // 2️⃣ Agrupa por SQ_CANDIDATO somando votos
            $agrupados = [];

            foreach ($filtrados as $item) {
                $id = $item['SQ_CANDIDATO'];

                if (!isset($agrupados[$id])) {
                    // primeira vez → salva tudo
                    $agrupados[$id] = $item;
                } else {
                    // já existe → soma os votos
                    $agrupados[$id]['QT_VOTOS_NOMINAIS_VALIDOS'] += (int) $item['QT_VOTOS_NOMINAIS_VALIDOS'];
                }
            }

            return [
                'status'  => 'success',
                'message' => 'Dados eleitorais carregados com sucesso',
                'data'    => array_values($agrupados)
            ];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');

            return [
                'status'   => 'server_error',
                'message'  => 'Erro interno do servidor',
                'error_id' => $errorId
            ];
        }
    }

    public static function pegarDadosEleicaoPrefeito(?string $ano = null, ?string $partido = null, ?string $situacao = null, ?string $municipio = null): array {

        try {

            $instancia = new self();
            $dados = $instancia->abrirCsv($ano);

            // 1️⃣ Filtra primeiro
            $filtrados = array_filter($dados, function ($item) use ($partido, $situacao, $municipio) {

                if ($item['DS_CARGO'] !== 'Prefeito') {
                    return false;
                }

                if ($partido !== null && $item['NR_PARTIDO'] !== $partido) {
                    return false;
                }

                if ($municipio !== null && $item['CD_MUNICIPIO'] !== $municipio) {
                    return false;
                }

                if ($situacao !== null && $item['CD_SIT_TOT_TURNO'] !== $situacao) {
                    return false;
                }

                return true;
            });

            // 2️⃣ Agrupa por SQ_CANDIDATO somando votos
            $agrupados = [];

            foreach ($filtrados as $item) {
                $id = $item['SQ_CANDIDATO'];

                if (!isset($agrupados[$id])) {
                    // primeira vez → salva tudo
                    $agrupados[$id] = $item;
                } else {
                    // já existe → soma os votos
                    $agrupados[$id]['QT_VOTOS_NOMINAIS_VALIDOS'] += (int) $item['QT_VOTOS_NOMINAIS_VALIDOS'];
                }
            }

            return [
                'status'  => 'success',
                'message' => 'Dados eleitorais carregados com sucesso',
                'data'    => array_values($agrupados)
            ];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');

            return [
                'status'   => 'server_error',
                'message'  => 'Erro interno do servidor',
                'error_id' => $errorId
            ];
        }
    }
}

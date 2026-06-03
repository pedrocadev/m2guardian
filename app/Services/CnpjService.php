<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CnpjService
{
    public static function validate(string $cnpj): bool
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return false;
        }

        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $cnpj[$i] * $weights1[$i];
        }
        $dv1 = 11 - ($sum % 11);
        $dv1 = $dv1 >= 10 ? 0 : $dv1;
        if ((int) $cnpj[12] !== $dv1) {
            return false;
        }

        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $cnpj[$i] * $weights2[$i];
        }
        $dv2 = 11 - ($sum % 11);
        $dv2 = $dv2 >= 10 ? 0 : $dv2;

        return (int) $cnpj[13] === $dv2;
    }

    /**
     * Consulta o CNPJ no BrasilAPI. Retorna ['razao_social' => ...] ou null
     * se invalido, nao encontrado ou erro de rede.
     */
    public static function lookup(string $cnpj): ?array
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (!self::validate($cnpj)) {
            return null;
        }

        try {
            $response = Http::timeout(8)
                ->get("https://brasilapi.com.br/api/cnpj/v1/{$cnpj}");

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            return [
                'razao_social' => $data['razao_social'] ?? null,
                'nome_fantasia' => $data['nome_fantasia'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::warning('CnpjService lookup failed', [
                'cnpj' => $cnpj,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}

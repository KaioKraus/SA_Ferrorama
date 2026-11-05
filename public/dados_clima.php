<?php
session_start();
require_once __DIR__ . '/validar_acesso.php';

date_default_timezone_set('America/Sao_Paulo');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

function responderClima(int $statusHttp, array $dados): void
{
    http_response_code($statusHttp);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    responderClima(405, ['success' => false, 'message' => 'Método não permitido.']);
}

$url = 'https://api.open-meteo.com/v1/forecast?' . http_build_query([
    'latitude' => -26.30444,
    'longitude' => -48.84556,
    'current' => 'temperature_2m,precipitation,weather_code,is_day',
    'timezone' => 'America/Sao_Paulo',
], '', '&', PHP_QUERY_RFC3986);

$curl = curl_init($url);
if ($curl === false) {
    error_log('Não foi possível iniciar a consulta meteorológica.');
    responderClima(502, ['success' => false, 'message' => 'Não foi possível consultar o serviço meteorológico.']);
}

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_USERAGENT => 'SA-Ferrorama/1.0',
]);

$resposta = curl_exec($curl);
$erroCurl = curl_error($curl);
$statusHttp = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($resposta === false || $statusHttp !== 200) {
    error_log('Erro ao consultar Open-Meteo. HTTP ' . $statusHttp . '; ' . $erroCurl);
    responderClima(502, ['success' => false, 'message' => 'Não foi possível atualizar o clima. Tente novamente mais tarde.']);
}

$dadosApi = json_decode($resposta, true);
$atual = $dadosApi['current'] ?? null;
if (
    !is_array($atual) ||
    !isset($atual['temperature_2m'], $atual['weather_code'], $atual['is_day'], $atual['time']) ||
    !is_numeric($atual['temperature_2m']) ||
    !is_numeric($atual['weather_code']) ||
    !is_numeric($atual['is_day'])
) {
    error_log('Resposta inválida recebida da API Open-Meteo.');
    responderClima(502, ['success' => false, 'message' => 'O serviço meteorológico retornou dados inválidos.']);
}

$codigoTempo = (int) $atual['weather_code'];
$duranteODia = (int) $atual['is_day'] === 1;
$condicoes = [
    0 => ['Ensolarado', 'bi-sun-fill', 'bi-moon-stars-fill'],
    1 => ['Predominantemente limpo', 'bi-sun-fill', 'bi-moon-stars-fill'],
    2 => ['Parcialmente nublado', 'bi-cloud-sun-fill', 'bi-cloud-moon-fill'],
    3 => ['Nublado', 'bi-cloud-fill', 'bi-cloud-fill'],
    45 => ['Neblina', 'bi-cloud-fog-fill', 'bi-cloud-fog-fill'],
    48 => ['Neblina com geada', 'bi-cloud-fog-fill', 'bi-cloud-fog-fill'],
    51 => ['Garoa leve', 'bi-cloud-drizzle-fill', 'bi-cloud-drizzle-fill'],
    53 => ['Garoa moderada', 'bi-cloud-drizzle-fill', 'bi-cloud-drizzle-fill'],
    55 => ['Garoa intensa', 'bi-cloud-drizzle-fill', 'bi-cloud-drizzle-fill'],
    56 => ['Garoa congelante leve', 'bi-cloud-sleet-fill', 'bi-cloud-sleet-fill'],
    57 => ['Garoa congelante intensa', 'bi-cloud-sleet-fill', 'bi-cloud-sleet-fill'],
    61 => ['Chuva leve', 'bi-cloud-rain-fill', 'bi-cloud-rain-fill'],
    63 => ['Chuva moderada', 'bi-cloud-rain-fill', 'bi-cloud-rain-fill'],
    65 => ['Chuva intensa', 'bi-cloud-rain-heavy-fill', 'bi-cloud-rain-heavy-fill'],
    66 => ['Chuva congelante leve', 'bi-cloud-sleet-fill', 'bi-cloud-sleet-fill'],
    67 => ['Chuva congelante intensa', 'bi-cloud-sleet-fill', 'bi-cloud-sleet-fill'],
    71 => ['Neve leve', 'bi-cloud-snow-fill', 'bi-cloud-snow-fill'],
    73 => ['Neve moderada', 'bi-cloud-snow-fill', 'bi-cloud-snow-fill'],
    75 => ['Neve intensa', 'bi-cloud-snow-fill', 'bi-cloud-snow-fill'],
    77 => ['Grãos de neve', 'bi-cloud-snow-fill', 'bi-cloud-snow-fill'],
    80 => ['Pancadas de chuva leves', 'bi-cloud-rain-fill', 'bi-cloud-rain-fill'],
    81 => ['Pancadas de chuva moderadas', 'bi-cloud-rain-fill', 'bi-cloud-rain-fill'],
    82 => ['Pancadas de chuva intensas', 'bi-cloud-rain-heavy-fill', 'bi-cloud-rain-heavy-fill'],
    85 => ['Pancadas de neve leves', 'bi-cloud-snow-fill', 'bi-cloud-snow-fill'],
    86 => ['Pancadas de neve intensas', 'bi-cloud-snow-fill', 'bi-cloud-snow-fill'],
    95 => ['Trovoada', 'bi-cloud-lightning-rain-fill', 'bi-cloud-lightning-rain-fill'],
    96 => ['Trovoada com granizo leve', 'bi-cloud-lightning-rain-fill', 'bi-cloud-lightning-rain-fill'],
    99 => ['Trovoada com granizo intenso', 'bi-cloud-lightning-rain-fill', 'bi-cloud-lightning-rain-fill'],
];
$condicao = $condicoes[$codigoTempo] ?? ['Condição desconhecida', 'bi-cloud', 'bi-cloud'];
$horarioObservacao = strtotime((string) $atual['time']);
if ($horarioObservacao === false) {
    error_log('Horário inválido recebido da API Open-Meteo.');
    responderClima(502, ['success' => false, 'message' => 'O serviço meteorológico retornou um horário inválido.']);
}

responderClima(200, [
    'success' => true,
    'localizacao' => 'Joinville, Santa Catarina',
    'temperatura' => round((float) $atual['temperature_2m'], 1),
    'condicao' => $condicao[0],
    'icone' => $duranteODia ? $condicao[1] : $condicao[2],
    'precipitacao_mm' => isset($atual['precipitation']) && is_numeric($atual['precipitation'])
        ? round((float) $atual['precipitation'], 1)
        : null,
    'observado_em' => date('d/m/Y H:i', $horarioObservacao),
    'consultado_em' => date('d/m/Y H:i:s'),
    'fuso_horario' => $dadosApi['timezone'] ?? 'America/Sao_Paulo',
]);

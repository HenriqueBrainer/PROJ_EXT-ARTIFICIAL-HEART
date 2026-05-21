<?php
header('Content-Type: application/json');

// Configurações da Porta Serial (UART)
$serial_port = '/dev/ttyUSB0';
$baud_rate = 9600;

// Lógica para lidar com POST (Enviar dados para UART)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bpm = isset($_POST['bpm']) ? (int)$_POST['bpm'] : 70;
    $fluxo = isset($_POST['fluxo']) ? (int)$_POST['fluxo'] : 5000;

    // Comando para enviar via UART (Exemplo: "BPM:70;FLUXO:5000\n")
    $comando = "BPM:$bpm;FLUXO:$fluxo\n";

    if (file_exists($serial_port)) {
        try {
            // No Linux, configurar a porta antes de escrever
            exec("stty -F $serial_port $baud_rate raw -echo");
            $fp = fopen($serial_port, "w");
            if ($fp) {
                fwrite($fp, $comando);
                fclose($fp);
                echo json_encode(['status' => 'Enviado UART', 'data' => $comando]);
            } else {
                echo json_encode(['status' => 'Erro ao abrir porta', 'data' => $comando]);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'Exceção Serial', 'msg' => $e->getMessage()]);
        }
    } else {
        // Simulação caso a porta não exista
        echo json_encode(['status' => 'Simulado (Porta Inexistente)', 'data' => $comando]);
    }
    exit;
}

// Lógica para lidar com GET (Ler dados da UART)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($serial_port)) {
        // Exemplo de leitura real
        // $line = shell_exec("head -n 1 $serial_port");
        // Simulando
        echo json_encode([
            'bpm' => rand(70, 80),
            'fluxo' => rand(4900, 5100),
            'status' => 'Lido UART'
        ]);
    } else {
        // Simulação
        echo json_encode([
            'bpm' => rand(65, 95),
            'fluxo' => rand(4800, 5200),
            'status' => 'Simulado'
        ]);
    }
    exit;
}

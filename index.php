<?php

// Configurações da Porta Serial (UART)
// No Linux, geralmente é /dev/ttyUSB0 ou /dev/ttyAMA0
$serial_port = '/dev/ttyUSB0'; 
$baud_rate = 9600;

function readUART($port) {

    // Simulação de dados caso a porta não esteja disponível no ambiente de desenvolvimento
    if (!file_exists($port)) {
        return [
            'bpm' => rand(60, 100),
            'fluxo' => rand(4500, 5500),
            'status' => 'Simulado'
        ];
    }

    try {
        // Exemplo básico de leitura usando shell (pode variar conforme o SO)
        // exec("stty -F $port $baud_rate raw -echo");
        // $handle = fopen($port, "r");
        // $line = fgets($handle);
        // fclose($handle);
        // parsear $line...
        return ['bpm' => 75, 'fluxo' => 5000, 'status' => 'UART Ativa'];
    } catch (Exception $e) {
        return ['bpm' => 0, 'fluxo' => 0, 'status' => 'Erro UART'];
    }
}

$dados = readUART($serial_port);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coração Artificial - Monitoramento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        @keyframes heartbeat {
            0% { transform: scale(var(--heart-scale)); }
            15% { transform: scale(calc(var(--heart-scale) * 1.15)); }
            30% { transform: scale(var(--heart-scale)); }
            45% { transform: scale(calc(var(--heart-scale) * 1.15)); }
            100% { transform: scale(var(--heart-scale)); }
        }

        .animate-heart {
            animation: heartbeat var(--pulse-duration) ease-in-out infinite;
            transform-origin: center;
        }

        .pulse-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 4px solid #ef4444;
            border-radius: 50%;
            animation: ring-pulse var(--pulse-duration) ease-out infinite;
        }

        @keyframes ring-pulse {
            0% { width: 40%; height: 40%; opacity: 0.6; }
            100% { width: 100%; height: 100%; opacity: 0; }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-red-50 via-pink-50 to-red-100 flex items-center justify-center p-4">

    <div class="w-full max-w-6xl grid md:grid-cols-2 gap-8 items-center">
        
        <!-- Lado Esquerdo: Visualização -->
        <div class="flex flex-col items-center justify-center p-8">
            <div id="heart-container" class="relative" 
                 style="--heart-scale: 1.0; --pulse-duration: 0.8s;">
                
                <!-- SVG do Coração -->
                <svg width="300" height="300" viewBox="0 0 200 200" class="drop-shadow-2xl animate-heart">
                    <defs>
                        <linearGradient id="heartGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stopColor="#ef4444" />
                            <stop offset="50%" stopColor="#dc2626" />
                            <stop offset="100%" stopColor="#991b1b" />
                        </linearGradient>
                        <filter id="glow">
                            <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
                            <feMerge>
                                <feMergeNode in="coloredBlur"/>
                                <feMergeNode in="SourceGraphic"/>
                            </feMerge>
                        </filter>
                    </defs>
                    <path d="M100,170 C100,170 30,120 30,80 C30,60 45,45 65,45 C80,45 92,52 100,65 C108,52 120,45 135,45 C155,45 170,60 170,80 C170,120 100,170 100,170 Z"
                          fill="#ef4444" filter="#7f1d1d" stroke="#7f1d1d" stroke-width="2" />
                </svg>

                <!-- Anel de Pulso -->
                <div class="pulse-ring"></div>
            </div>

            <!-- Métricas -->
            <div class="mt-8 flex gap-6">
                <div class="bg-white/80 backdrop-blur rounded-2xl p-4 shadow-lg min-w-[120px]">
                    <div class="flex items-center gap-2 text-red-600 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        <span class="text-sm font-medium">BPM</span>
                    </div>
                    <div id="display-bpm" class="text-3xl font-bold text-gray-800"><?php echo $dados['bpm']; ?></div>
                </div>

                <div class="bg-white/80 backdrop-blur rounded-2xl p-4 shadow-lg min-w-[120px]">
                    <div class="flex items-center gap-2 text-blue-600 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                        <span class="text-sm font-medium">Fluxo</span>
                    </div>
                    <div id="display-fluxo" class="text-3xl font-bold text-gray-800"><?php echo $dados['fluxo']; ?></div>
                    <div class="text-xs text-gray-500">mL/min</div>
                </div>
            </div>
        </div>

        <!-- Lado Direito: Controles -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12">
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Coração Artificial</h1>
                <p class="text-gray-600">Interface de Controle</p>
            </div>

            <form id="control-form" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fluxo Sanguíneo (mL/min)</label>
                    <input type="number" name="fluxo" id="input-fluxo" value="<?php echo $dados['fluxo']; ?>" 
                           min="1000" max="10000" step="100"
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                    <p class="mt-1 text-xs text-gray-500">Intervalo: 1000 - 10000 mL/min</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batimentos Cardíacos (BPM)</label>
                    <input type="number" name="bpm" id="input-bpm" value="<?php echo $dados['bpm']; ?>" 
                           min="40" max="180" step="1"
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                    <p class="mt-1 text-xs text-gray-500">Intervalo: 40 - 180 BPM</p>
                </div>

                <button type="submit" 
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-4 rounded-xl shadow-lg shadow-red-200 transition-all transform active:scale-[0.98]">
                    Atualizar Parâmetros
                </button>
            </form>

            <div class="mt-8 p-4 bg-red-50 rounded-xl border border-red-200">
                <h3 class="font-semibold text-red-900 mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    Status do Sistema
                </h3>
                <p class="text-sm text-red-800">
                    Status: <span id="status-text" class="font-bold"><?php echo $dados['status']; ?></span>. 
                    Porta: <code class="bg-red-100 px-1 rounded"><?php echo $serial_port; ?></code>
                </p>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('control-form');
        const heartContainer = document.getElementById('heart-container');
        const displayBpm = document.getElementById('display-bpm');
        const displayFluxo = document.getElementById('display-fluxo');

        function updateHeartAnimation(bpm) {
            // Calcula escala (0.8 a 1.4) e duração (60/bpm)
            const scale = 0.8 + (bpm / 180) * 0.6;
            const duration = 60 / bpm;
            
            heartContainer.style.setProperty('--heart-scale', scale);
            heartContainer.style.setProperty('--pulse-duration', `${duration}s`);
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const bpm = document.getElementById('input-bpm').value;
            const fluxo = document.getElementById('input-fluxo').value;

            // Atualiza UI localmente
            displayBpm.innerText = bpm;
            displayFluxo.innerText = fluxo;
            updateHeartAnimation(bpm);

            // Envia para o servidor (UART)
            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `bpm=${bpm}&fluxo=${fluxo}`
            })
            .then(response => response.json())
            .then(data => {
                console.log('Resposta UART:', data);
                document.getElementById('status-text').innerText = data.status;
            })
            .catch(err => console.error('Erro ao enviar dados:', err));
        });

        // Inicializa animação
        updateHeartAnimation(<?php echo $dados['bpm']; ?>);

        // Polling para ler dados da UART em tempo real (opcional)
        setInterval(() => {
            fetch('api.php')
                .then(res => res.json())
                .then(data => {
                    if (data.bpm) {
                        displayBpm.innerText = data.bpm;
                        displayFluxo.innerText = data.fluxo;
                        updateHeartAnimation(data.bpm);
                        document.getElementById('status-text').innerText = data.status;
                    }
                });
        }, 2000);
    </script>
</body>
</html>

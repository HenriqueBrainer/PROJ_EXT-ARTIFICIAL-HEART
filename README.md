# Coração Artificial - Monitoramento via UART (PHP)

Esta é a versão em PHP da interface do Coração Artificial, adaptada para ler e enviar dados via comunicação serial (UART).

## Estrutura do Projeto

- `index.php`: Interface principal (HTML/CSS/JS) com lógica PHP integrada.
- `api.php`: Endpoint para comunicação assíncrona com a porta serial.

## Requisitos

1. **Servidor Web**: Apache ou Nginx com PHP instalado. ( Baixar a imagem no docker )

## Configuração UART

No topo dos arquivos `index.php` e `api.php`, você encontrará as variáveis de configuração:

```php
$serial_port = '/dev/ttyUSB0'; 
$baud_rate = 9600;
```

## Como Funciona

1. **Leitura**: A página inicial lê os dados atuais ao carregar. Um script JavaScript faz "polling" (consultas repetidas) a cada 2 segundos para atualizar os valores de BPM e Fluxo em tempo real.
2. **Escrita**: Ao clicar em "Atualizar Parâmetros", os valores são enviados via POST para o `api.php`, que por sua vez escreve na porta serial configurada.
3. **Visualização**: A animação do coração é controlada via CSS Variables, ajustando a escala e a velocidade do pulso dinamicamente conforme o BPM recebido.

## Notas de Implementação

O código inclui uma lógica de simulação. Se a porta serial configurada não for encontrada no sistema, ele gerará dados aleatórios para que a interface possa ser testada visualmente sem hardware conectado.

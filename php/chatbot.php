<?php
header('Content-Type: application/json');
require_once __DIR__ . '/vendor/autoload.php'; // Autoload do Composer

// Carrega variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configuração da OpenAI
$openai = OpenAI::client($_ENV['OPENAI_API_KEY']);

// Dúvidas frequentes (exemplo)
$faq = [
    'frete' => 'Entregamos para todo o Brasil em até 5 dias úteis.',
    'devolução' => 'Você tem 30 dias para solicitar devolução.',
    'pagamento' => 'Aceitamos cartão, Pix e boleto.'
];

// Recebe a mensagem do usuário
$data = json_decode(file_get_contents('php://input'), true);
$message = strtolower(trim($data['message']));

// Verifica se é uma pergunta conhecida
foreach ($faq as $key => $answer) {
    if (strpos($message, $key) !== false) {
        echo json_encode(['response' => $answer, 'redirect' => false]);
        exit;
    }
}

// Se não for FAQ, usa GPT-4
try {
    $response = $openai->chat()->create([
        'model' => 'gpt-4',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'Você é um assistente de suporte. Responda de forma clara e objetiva em português.'
            ],
            [
                'role' => 'user',
                'content' => $message
            ]
        ],
        'temperature' => 0.7
    ]);

    $answer = $response->choices[0]->message->content;
    echo json_encode(['response' => $answer, 'redirect' => false]);

} catch (Exception $e) {
    // Redireciona para WhatsApp em caso de erro
    echo json_encode([
        'response' => 'Não consegui resolver. Vou te enviar para um atendente!',
        'redirect' => true,
        'whatsapp' => $_ENV['WHATSAPP_NUMBER']
    ]);
}
?>
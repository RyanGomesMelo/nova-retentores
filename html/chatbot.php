<?php
header('Content-Type: application/json');
require('../config/db.php');

$data = json_decode(file_get_contents('php://input'), true);
$question = trim($data['question'] ?? '');

// Função de similaridade personalizada
function calculateSimilarity($str1, $str2) {
    similar_text(strtolower($str1), strtolower($str2), $percent);
    return $percent;
}

try {
    $answer = 'Desculpe, não encontrei informação sobre isso. Entre em contato com nosso suporte via Whatsapp';
    $bestScore = 0;

    // Pré-processamento da pergunta
    $cleanQuestion = preg_replace('/[^\p{L}\p{N}\s]/u', '', $question);
    
    // Busca semântica com FULLTEXT e similaridade
    $stmt = $pdo->prepare("
        SELECT 
            pergunta, 
            resposta,
            MATCH(pergunta) AGAINST(:question IN NATURAL LANGUAGE MODE) AS relevance
        FROM perguntas
        WHERE MATCH(pergunta) AGAINST(:question IN NATURAL LANGUAGE MODE)
        ORDER BY relevance DESC
        LIMIT 10
    ");
    
    $stmt->execute([':question' => $cleanQuestion]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificação de similaridade refinada
    foreach ($results as $row) {
        $similarity = calculateSimilarity($cleanQuestion, $row['pergunta']);
        
        // Combina relevância do FULLTEXT com similaridade textual
        $combinedScore = ($similarity * 0.7) + ($row['relevance'] * 0.3);
        
        if ($combinedScore > $bestScore && $combinedScore > 30) {
            $bestScore = $combinedScore;
            $answer = $row['resposta'];
        }
    }

    echo json_encode(['answer' => $answer]);

} catch(PDOException $e) {
    echo json_encode(['answer' => 'Erro ao consultar o banco de dados']);
}
?>
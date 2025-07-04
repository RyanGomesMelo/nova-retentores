document.addEventListener('DOMContentLoaded', () => {
    const chatButton = document.getElementById('chatButton');
    const chatWindow = document.getElementById('chatWindow');
    const closeChat = document.getElementById('closeChat');
    const userInput = document.getElementById('userInput');
    const sendButton = document.getElementById('sendButton');
    const chatHistory = document.getElementById('chatHistory');

    // Alternar visibilidade do chat
    chatButton.addEventListener('click', () => chatWindow.classList.toggle('hidden'));
    closeChat.addEventListener('click', () => chatWindow.classList.add('hidden'));

    // Função para adicionar mensagens
    function addMessage(message, isUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = isUser ? 
            'bg-blue-500 text-white p-3 rounded-lg ml-8' :
            'bg-gray-100 p-3 rounded-lg mr-8';
        messageDiv.textContent = message;
        chatHistory.appendChild(messageDiv);
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

    // Enviar mensagem
    async function sendMessage() {
        const question = userInput.value.trim();
        if (!question) return;

        addMessage(question, true);
        userInput.value = '';

        try {
            const response = await fetch('chatbot.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ question })
            });

            const data = await response.json();
            addMessage(data.answer || 'Desculpe, não entendi sua pergunta.');
        } catch (error) {
            addMessage('Erro na conexão com o servidor.');
        }
    }

    // Event Listeners
    sendButton.addEventListener('click', sendMessage);
    userInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
});


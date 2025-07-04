const express = require('express');
const cors = require('cors');
require('dotenv').config();

// Inicialize o Express
const app = express();
const port = 3000;

// Middlewares
app.use(cors());
app.use(express.json());

// Rota POST para o chat
app.post('/chat', async (req, res) => {
  try {
    const { message } = req.body;

    // Chamada à API Gemini usando fetch nativo (Node.js 18+)
    const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=${process.env.GEMINI_API_KEY}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        contents: [{
          parts: [{ text: message }]
        }]
      })
    });

    const data = await response.json();

    // Verifique se a resposta da API é válida
    if (!data.candidates || !data.candidates[0].content.parts[0].text) {
      throw new Error('Resposta inesperada da API Gemini');
    }

    const reply = data.candidates[0].content.parts[0].text;
    res.json({ reply });

  } catch (error) {
    console.error('Erro na API Gemini:', error);
    res.status(500).json({ error: "Erro ao processar sua mensagem" });
  }
});

// Inicie o servidor
app.listen(port, () => {
  console.log(`Servidor rodando em http://localhost:${port}`);
});
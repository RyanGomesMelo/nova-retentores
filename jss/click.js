document.querySelectorAll('.produto-link').forEach(item => {
    item.addEventListener('click', function() {
        let productId = this.dataset.id;
        console.log("Produto clicado:", productId); // Adiciona debug no console

        fetch('../php/update_clicks.php', { 
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + productId
        }).then(response => response.text())
          .then(data => console.log("Resposta do servidor:", data))
          .catch(error => console.error("Erro na requisição:", error));
    });
});

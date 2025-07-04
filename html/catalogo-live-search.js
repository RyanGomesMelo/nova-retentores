document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    const searchForm = document.querySelector('.search-form');
    
    // Onde os resultados da busca principal e paginação são exibidos
    const mainProductContainer = document.querySelector('.product-container');
    const paginationContainer = document.querySelector('.pagination');

    let searchTimeout;

    // Criar o contêiner para os resultados da busca em tempo real
    const liveSearchResultsContainer = document.createElement('div');
    liveSearchResultsContainer.id = 'liveSearchResults';
    // Inserir o novo contêiner logo após o formulário de busca
    searchForm.parentNode.insertBefore(liveSearchResultsContainer, searchForm.nextSibling);

    const performLiveSearch = () => {
        const query = searchInput.value.trim();
        const categoryId = categorySelect.value;

        // Se a busca for curta, esconde os resultados e mostra o conteúdo principal
        if (query.length < 2) {
            liveSearchResultsContainer.style.display = 'none';
            mainProductContainer.style.display = 'grid'; // ou 'block', dependendo do seu CSS
            if (paginationContainer) {
                paginationContainer.style.display = 'block';
            }
            return;
        }
        
        // Mostra um feedback de "carregando"
        liveSearchResultsContainer.innerHTML = '<p class="live-search-loading">Buscando...</p>';
        liveSearchResultsContainer.style.display = 'block';

        // Esconde o conteúdo principal
        mainProductContainer.style.display = 'none';
        if (paginationContainer) {
            paginationContainer.style.display = 'none';
        }

        // Faz a chamada AJAX usando a API Fetch
        fetch(`livesearch.php?q=${encodeURIComponent(query)}&category=${encodeURIComponent(categoryId)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro de rede: ' + response.statusText);
                }
                return response.text();
            })
            .then(html => {
                // O livesearch.php já retorna os cards, então podemos criar um grid temporário
                liveSearchResultsContainer.innerHTML = `<div class="product-container">${html}</div>`;
            })
            .catch(error => {
                console.error('Erro na busca:', error);
                liveSearchResultsContainer.innerHTML = '<p class="live-search-no-results">Ocorreu um erro. Tente novamente.</p>';
            });
    };

    // Evento para o campo de busca com "debounce"
    searchInput.addEventListener('keyup', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performLiveSearch, 300); // Delay de 300ms
    });

    // Evento para o seletor de categoria
    categorySelect.addEventListener('change', performLiveSearch);
});
// Sistema de busca avançada para produtos
const initAdvancedSearch = () => {
    const searchForm = document.querySelector('.search-form');
    const categorySelect = document.querySelector('#category');
    const searchInput = document.querySelector('#search');
    const productsGrid = document.querySelector('.products-grid');
    const filterPanel = document.createElement('div');
    filterPanel.className = 'filter-panel';
    filterPanel.innerHTML = `
        <div class="filter-group">
            <h3>Filtros</h3>
            <div class="filter-options">
                <label>
                    <input type="checkbox" data-filter="disponivel" checked>
                    Disponível em Estoque
                </label>
                <label>
                    <input type="checkbox" data-filter="lancamento">
                    Lançamentos
                </label>
            </div>
        </div>
        <div class="filter-group">
            <h3>Ordenar por</h3>
            <select id="sortBy" class="sort-select">
                <option value="relevancia">Relevância</option>
                <option value="nome-asc">Nome (A-Z)</option>
                <option value="nome-desc">Nome (Z-A)</option>
                <option value="recente">Mais Recentes</option>
            </select>
        </div>
    `;

    // Inserir o painel de filtros antes da grade de produtos
    productsGrid.parentNode.insertBefore(filterPanel, productsGrid);

    // Estilização dinâmica
    const style = document.createElement('style');
    style.textContent = `
        .filter-panel {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            animation: fadeIn 0.3s ease-out;
        }

        .filter-group {
            flex: 1;
            min-width: 250px;
        }

        .filter-group h3 {
            color: var(--accent);
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .filter-options label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-options label:hover {
            color: var(--accent);
        }

        .filter-options input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--accent);
        }

        .sort-select {
            width: 100%;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            color: var(--text);
            font-family: inherit;
            cursor: pointer;
            transition: var(--transition);
        }

        .sort-select:hover {
            border-color: var(--accent);
        }

        .sort-select option {
            background: var(--primary);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .filter-panel {
                flex-direction: column;
                gap: 1.5rem;
            }

            .filter-group {
                width: 100%;
            }
        }
    `;
    document.head.appendChild(style);

    // Lógica de filtragem e ordenação
    const applyFilters = () => {
        const products = Array.from(productsGrid.children);
        const sortBy = document.querySelector('#sortBy').value;
        const filters = Array.from(document.querySelectorAll('.filter-options input:checked'))
            .map(input => input.dataset.filter);

        products.forEach(product => {
            let shouldShow = true;

            // Aplicar filtros
            if (filters.includes('disponivel') && !product.dataset.disponivel) {
                shouldShow = false;
            }
            if (filters.includes('lancamento') && !product.dataset.lancamento) {
                shouldShow = false;
            }

            product.style.display = shouldShow ? '' : 'none';
        });

        // Ordenação
        const sortedProducts = products.sort((a, b) => {
            switch (sortBy) {
                case 'nome-asc':
                    return a.querySelector('h3').textContent.localeCompare(b.querySelector('h3').textContent);
                case 'nome-desc':
                    return b.querySelector('h3').textContent.localeCompare(a.querySelector('h3').textContent);
                case 'recente':
                    return (b.dataset.data || '').localeCompare(a.dataset.data || '');
                default:
                    return 0;
            }
        });

        // Reordenar no DOM
        sortedProducts.forEach(product => productsGrid.appendChild(product));
    };

    // Event listeners
    document.querySelectorAll('.filter-options input, #sortBy').forEach(element => {
        element.addEventListener('change', applyFilters);
    });

    // Melhorar a experiência de busca
    let searchTimeout;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = e.target.value.toLowerCase();
            const products = Array.from(productsGrid.children);

            products.forEach(product => {
                const title = product.querySelector('h3').textContent.toLowerCase();
                const description = product.querySelector('.product-description')?.textContent.toLowerCase() || '';
                const matches = title.includes(searchTerm) || description.includes(searchTerm);
                product.style.display = matches ? '' : 'none';
            });
        }, 300);
    });
};

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', initAdvancedSearch);
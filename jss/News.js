
        document.addEventListener("DOMContentLoaded", function () {
            const apiKey = "0a0360ed35cb4e5f92366326710ec738"; 
            const url = `https://newsapi.org/v2/everything?q=tecnologia&language=pt&apiKey=
${apiKey}`;

            // Função para buscar as notícias
            async function buscarNoticias() {
                try {
                    const resposta = await fetch(url);
                    const dados = await resposta.json();

                    // Verifique se o elemento 'noticias' existe
                    const listaNoticias = document.getElementById("noticias");

                    if (!listaNoticias) {
                        console.error("Elemento <ul> não encontrado!");
                        return;
                    }

                    if (dados.articles && dados.articles.length > 0) {
                        // Limita as notícias a 3
                        const noticiasLimitadas = dados.articles.slice(0, 3);

                        noticiasLimitadas.forEach(noticia => {
                            const item = document.createElement("li");
                            const link = document.createElement("a");
                            link.href = noticia.url;
                            link.textContent = noticia.title;
                            link.target = "_blank";
                            item.appendChild(link);
                            listaNoticias.appendChild(item);
                        });
                    } else {
                        listaNoticias.innerHTML = "<p>Nenhuma notícia encontrada.</p>";
                    }
                } catch (erro) {
                    console.error("Erro ao buscar notícias:", erro);
                    const listaNoticias = document.getElementById("noticias");
                    if (listaNoticias) {
                        listaNoticias.innerHTML = "<p>Erro ao carregar as notícias.</p>";
                    }
                }
            }

            // Chama a função ao carregar a página
            buscarNoticias();
        });
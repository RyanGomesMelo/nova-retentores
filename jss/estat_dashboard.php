   // Pegando os dados do PHP e convertendo para JavaScript
   const productNames = <?php echo json_encode($products); ?>;
   const clicksData = <?php echo json_encode($clicks); ?>;

   // Gráfico de Barras - Cliques por Produto
   const ctxClicks = document.getElementById('clicksChart').getContext('2d');
   new Chart(ctxClicks, {
       type: 'doughnut',
       data: {
           labels: productNames, // Nomes dos produtos
           datasets: [{
               label: 'Cliques',
               data: clicksData, // Número de cliques
               backgroundColor: [
                   '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                   '#9966FF', '#FF9F40', '#E7E9ED', '#B4D455',
                   '#2C3E50', '#F39C12', '#D35400', '#C0392B'
               ],
               hoverBackgroundColor: [
                   '#FF4C75', '#2F95D8', '#E5B936', '#3AAFA9', 
                   '#8854D0', '#E58938', '#D6D6D6', '#A2C245',
                   '#1C2A3A', '#D48C10', '#B94700', '#A92A23'
               ],
               borderColor: '#ffffff',
           }]
       },
       options: {
           responsive: true,
           maintainAspectRatio: false,
           plugins: {
               legend: { display: false }
           },
           scales: {
               y: { beginAtZero: true }
           }
       }
   });

   // Gráfico de Barras com D3.js
   const productClicks = productNames.map((prod, index) => {
       return { name: prod, clicks: clicksData[index] };
   });

   const topProducts = productClicks.sort((a, b) => b.clicks - a.clicks).slice(0, 10);

   // Configuração do gráfico de barras com D3.js
   const margin = { top: 20, right: 30, bottom: 40, left: 40 };
   const barWidth = document.getElementById("clicksBarChart").clientWidth - margin.left - margin.right;
   const barHeight = document.getElementById("clicksBarChart").clientHeight - margin.top - margin.bottom;

   const barSvg = d3.select("#clicksBarChart")
       .append("svg")
       .attr("width", barWidth + margin.left + margin.right)
       .attr("height", barHeight + margin.top + margin.bottom)
       .append("g")
       .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

   const x = d3.scaleBand()
       .domain(topProducts.map(d => d.name))
       .range([0, barWidth])
       .padding(0.1);

   const y = d3.scaleLinear()
       .domain([0, d3.max(topProducts, d => d.clicks)])
       .nice()
       .range([barHeight, 0]);

   barSvg.selectAll(".bar")
       .data(topProducts)
       .enter()
       .append("rect")
       .attr("class", "bar")
       .attr("x", d => x(d.name))
       .attr("y", d => y(d.clicks))
       .attr("width", x.bandwidth())
       .attr("height", d => barHeight - y(d.clicks))
       .attr("fill", "#4BC0C0");

   barSvg.append("g")
       .attr("transform", "translate(0," + barHeight + ")")
       .call(d3.axisBottom(x));

   barSvg.append("g")
       .call(d3.axisLeft(y));
</script>
<script>
   // Get data from PHP
   const productsByCategory = <?php echo json_encode($productsByCategory); ?>;

   // Prepare data for bar chart
   const allProducts = [];
   Object.keys(productsByCategory).forEach(category => {
       productsByCategory[category].forEach(product => {
           allProducts.push({
               name: product.product,
               clicks: product.clicks,
               category: category
           });
       });
   });

   // Create color scale for categories
   const categories = Object.keys(productsByCategory);
   const categoryColors = {};
   categories.forEach((category, index) => {
       categoryColors[category] = d3.schemeCategory10[index % 10];
   });

   // Initialize Bar Chart
   if (allProducts.length > 0) {
       const ctx = document.getElementById('clicksChart').getContext('2d');
       new Chart(ctx, {
           type: 'bar',
           data: {
               labels: allProducts.map(p => p.name),
               datasets: [{
                   label: 'Cliques',
                   data: allProducts.map(p => p.clicks),
                   backgroundColor: allProducts.map(p => categoryColors[p.category]),
                   borderWidth: 1
               }]
           },
           options: {
               indexAxis: 'y',
               responsive: true,
               scales: {
                   x: {
                       beginAtZero: true,
                       title: {
                           display: true,
                           text: 'Número de Cliques'
                       }
                   },
                   y: {
                       ticks: {
                           autoSkip: false
                       }
                   }
               },
               plugins: {
                   legend: {
                       display: true,
                       labels: {
                           generateLabels: (chart) => {
                               return categories.map(category => ({
                                   text: category,
                                   fillStyle: categoryColors[category],
                                   strokeStyle: '#000',
                                   lineWidth: 1,
                                   hidden: false
                               }));
                           }
                       }
                   }
               }
           }
       });
   } else {
       document.getElementById('clicksChart').closest('.card').innerHTML = 
           '<div class="card-body">Nenhum dado disponível</div>';
   }

   // Prepare Sunburst Data
   const sunburstData = {
       name: "root",
       children: Object.keys(productsByCategory).map(category => ({
           name: category,
           children: productsByCategory[category].map(product => ({
               name: product.product,
               value: product.clicks
           }))
       }))
   };

   // Sunburst Configuration
   const width = document.getElementById('sunburstChart').clientWidth;
   const height = 500;
   const radius = Math.min(width, height) / 2;

   const svg = d3.select("#sunburstChart")
       .attr("viewBox", `0 0 ${width} ${height}`)
       .append("g")
       .attr("transform", `translate(${width/2},${height/2})`);

   const partition = data => d3.partition()
       .size([2 * Math.PI, radius])
       (d3.hierarchy(data)
           .sum(d => d.value)
           .sort((a, b) => b.value - a.value));

   const root = partition(sunburstData);
   
   const arc = d3.arc()
       .startAngle(d => d.x0)
       .endAngle(d => d.x1)
       .innerRadius(d => d.y0)
       .outerRadius(d => d.y1);

   // Draw Sunburst
   svg.selectAll('path')
       .data(root.descendants().filter(d => d.depth > 0))
       .join('path')
       .attr('d', arc)
       .style('fill', d => {
           if (d.depth === 1) return categoryColors[d.data.name];
           return d3.color(categoryColors[d.parent.data.name]).darker(0.5);
       })
       .style('stroke', '#fff')
       .style('opacity', 0.8)
       .on('click', (event, d) => {
           if (d.depth === 2) return; // Ignore product clicks

           svg.transition()
               .duration(750)
               .tween("scale", () => {
                   const xd = d3.interpolate(root.x0, d.x0);
                   const yd = d3.interpolate(root.y0, d.y0);
                   return t => {
                       root.each(p => p.current = {
                           x0: xd(t) * (2 * Math.PI) / (d.x1 - d.x0),
                           x1: xd(t) * (2 * Math.PI) / (d.x1 - d.x0),
                           y0: yd(t),
                           y1: yd(t) + (p.y1 - p.y0)
                       });
                   };
               })
               .selectAll('path')
               .attrTween('d', d => () => arc(d.current));
       });

   // Add labels (optional)
   svg.selectAll('text')
       .data(root.descendants().filter(d => d.depth > 0))
       .join('text')
       .attr('transform', d => `rotate(${(d.x0 + d.x1) / 2 * 180 / Math.PI - 90}) translate(${d.y0 + 10},0)`)
       .attr('dy', '0.35em')
       .text(d => d.data.name)
       .style('font-size', '10px')
       .style('pointer-events', 'none');
</script>

<script>
   const productsByCategory = <?php echo json_encode($productsByCategory); ?>;

   // Prepare data for bar chart
   const allProducts = [];
   Object.keys(productsByCategory).forEach(category => {
       productsByCategory[category].forEach(product => {
           allProducts.push({
               name: product.product,
               clicks: product.clicks,
               category: category
           });
       });
   });

   // Create color scale for categories
   const categories = Object.keys(productsByCategory);
   const categoryColors = {};
   categories.forEach((category, index) => {
       categoryColors[category] = d3.schemeCategory10[index % 10];
   });

   // Initialize Bar Chart
   if (allProducts.length > 0) {
       const ctx = document.getElementById('clicksChart').getContext('2d');
       new Chart(ctx, {
           type: 'bar',
           data: {
               labels: allProducts.map(p => p.name),
               datasets: [{
                   label: 'Cliques',
                   data: allProducts.map(p => p.clicks),
                   backgroundColor: allProducts.map(p => categoryColors[p.category]),
                   borderWidth: 1
               }]
           },
           options: {
               indexAxis: 'y',
               responsive: true,
               scales: {
                   x: {
                       beginAtZero: true,
                       title: {
                           display: true,
                           text: 'Número de Cliques'
                       }
                   },
                   y: {
                       ticks: {
                           autoSkip: false
                       }
                   }
               },
               plugins: {
                   legend: {
                       display: true,
                       labels: {
                           generateLabels: (chart) => {
                               return categories.map(category => ({
                                   text: category,
                                   fillStyle: categoryColors[category],
                                   strokeStyle: '#000',
                                   lineWidth: 1,
                                   hidden: false
                               }));
                           }
                       }
                   }
               }
           }
       });
   } else {
       document.getElementById('clicksChart').closest('.card').innerHTML = 
           '<div class="card-body">Nenhum dado disponível</div>';
   }

   // Prepare Sunburst Data
   const sunburstData = {
       name: "root",
       children: Object.keys(productsByCategory).map(category => ({
           name: category,
           children: productsByCategory[category].map(product => ({
               name: product.product,
               value: product.clicks
           }))
       }))
   };

   // Sunburst Configuration
   const width = document.getElementById('sunburstChart').clientWidth;
   const height = 500;
   const radius = Math.min(width, height) / 2;

   const svg = d3.select("#sunburstChart")
       .attr("viewBox", `0 0 ${width} ${height}`)
       .append("g")
       .attr("transform", `translate(${width/2},${height/2})`);

   const partition = data => d3.partition()
       .size([2 * Math.PI, radius])
       (d3.hierarchy(data)
           .sum(d => d.value)
           .sort((a, b) => b.value - a.value));

   const root = partition(sunburstData);
   
   const arc = d3.arc()
       .startAngle(d => d.x0)
       .endAngle(d => d.x1)
       .innerRadius(d => d.y0)
       .outerRadius(d => d.y1);

   // Draw Sunburst
   svg.selectAll('path')
       .data(root.descendants().filter(d => d.depth > 0))
       .join('path')
       .attr('d', arc)
       .style('fill', d => {
           if (d.depth === 1) return categoryColors[d.data.name];
           return d3.color(categoryColors[d.parent.data.name]).darker(0.5);
       })
       .style('stroke', '#fff')
       .style('opacity', 0.8)
       .on('click', (event, d) => {
           if (d.depth === 2) return; // Ignore product clicks

           svg.transition()
               .duration(750)
               .tween("scale", () => {
                   const xd = d3.interpolate(root.x0, d.x0);
                   const yd = d3.interpolate(root.y0, d.y0);
                   return t => {
                       root.each(p => p.current = {
                           x0: xd(t) * (2 * Math.PI) / (d.x1 - d.x0),
                           x1: xd(t) * (2 * Math.PI) / (d.x1 - d.x0),
                           y0: yd(t),
                           y1: yd(t) + (p.y1 - p.y0)
                       });
                   };
               })
               .selectAll('path')
               .attrTween('d', d => () => arc(d.current));
       });

   // Add labels (optional)
   svg.selectAll('text')
       .data(root.descendants().filter(d => d.depth > 0))
       .join('text')
       .attr('transform', d => `rotate(${(d.x0 + d.x1) / 2 * 180 / Math.PI - 90}) translate(${d.y0 + 10},0)`)
       .attr('dy', '0.35em')
       .text(d => d.data.name)
       .style('font-size', '10px')
       .style('pointer-events', 'none');
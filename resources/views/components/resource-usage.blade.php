@props(['apiEndpoint' => null, 'apiOffline' => false])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg md:col-span-2">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Resource Usage</h3>
                @if(!$apiOffline && $apiEndpoint)
                    <div class="flex items-center space-x-2 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                        <button type="button" id="view-bars-btn" class="px-2 py-1 text-xs rounded-md bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 shadow-sm transition-all" title="Progress Bars">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </button>
                        <button type="button" id="view-chart-btn" class="px-2 py-1 text-xs rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-all" title="Charts">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                        </button>
                    </div>
                @endif
            </div>
            @if(!$apiOffline && $apiEndpoint)
                <button type="button" id="refresh-resource-btn" class="inline-flex items-center px-2 py-1 text-xs text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 disabled:opacity-50">
                    <svg id="refresh-icon" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span id="refresh-text">Refresh</span>
                </button>
            @endif
        </div>

        @if($apiOffline)
            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <p class="text-sm text-yellow-700 dark:text-yellow-300">Resource usage data is unavailable while API is offline.</p>
            </div>
        @elseif($apiEndpoint)
            <div id="resource-loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @for($i = 0; $i < 4; $i++)<div class="animate-pulse"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-2"></div><div class="h-2.5 bg-gray-200 dark:bg-gray-700 rounded w-full mb-1"></div><div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div></div>@endfor
            </div>
            <div id="resource-error" style="display: none;" class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg"><p class="text-sm text-red-700 dark:text-red-300" id="error-message"></p></div>

            {{-- Progress Bars View --}}
            <div id="resource-bars" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <div class="flex justify-between items-center mb-1"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">CPU Usage</span><span class="text-sm font-semibold text-gray-900 dark:text-gray-100" id="cpu-percent">0%</span></div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5"><div id="cpu-bar" class="h-2.5 rounded-full transition-all duration-500 bg-green-500" style="width: 0%"></div></div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span id="cpu-used">0</span> / <span id="cpu-limit">0</span> MHz</p>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">RAM Usage</span><span class="text-sm font-semibold text-gray-900 dark:text-gray-100" id="ram-percent">0%</span></div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5"><div id="ram-bar" class="h-2.5 rounded-full transition-all duration-500 bg-green-500" style="width: 0%"></div></div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span id="ram-used">0</span> GB / <span id="ram-limit">0</span> GB</p>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">Disk Usage</span><span class="text-sm font-semibold text-gray-900 dark:text-gray-100" id="disk-percent">0%</span></div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5"><div id="disk-bar" class="h-2.5 rounded-full transition-all duration-500 bg-green-500" style="width: 0%"></div></div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span id="disk-used">0</span> GB / <span id="disk-limit">0</span> GB</p>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">Bandwidth</span><span class="text-sm font-semibold text-gray-900 dark:text-gray-100" id="bandwidth-percent">0%</span></div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5"><div id="bandwidth-bar" class="h-2.5 rounded-full transition-all duration-500 bg-green-500" style="width: 0%"></div></div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span id="bandwidth-used">0</span> GB / <span id="bandwidth-limit">0</span> GB</p>
                </div>
            </div>

            {{-- Chart View --}}
            <div id="resource-chart" style="display: none;">
                {{-- Daily Bandwidth with In/Out --}}
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Daily Bandwidth (MB)</h4>
                        <div class="flex items-center space-x-4 text-xs">
                            <span class="flex items-center text-gray-900 dark:text-white"><span class="w-3 h-3 rounded-full mr-1" style="background:#22c55e"></span>In</span>
                            <span class="flex items-center text-gray-900 dark:text-white"><span class="w-3 h-3 rounded-full mr-1" style="background:#f97316"></span>Out</span>
                            <span id="chart-total" class="text-gray-500 dark:text-gray-400">Total: 0 GB</span>
                        </div>
                    </div>
                    <div class="relative bg-gray-50 dark:bg-gray-900 rounded-lg p-4" style="height: 160px;">
                        <canvas id="daily-canvas" class="w-full h-full"></canvas>
                    </div>
                    <div id="daily-x-labels" class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1 px-4"></div>
                </div>
                {{-- Yearly Bandwidth --}}
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Monthly Bandwidth (GB)</h4>
                    </div>
                    <div class="relative bg-gray-50 dark:bg-gray-900 rounded-lg p-4" style="height: 120px;">
                        <canvas id="yearly-canvas" class="w-full h-full"></canvas>
                    </div>
                    <div id="yearly-x-labels" class="flex text-xs text-gray-500 dark:text-gray-400 mt-1"></div>
                </div>
                {{-- Resource Bars --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <div class="flex justify-between items-center mb-1"><span class="text-xs font-medium text-gray-600 dark:text-gray-400">CPU</span><span class="text-xs font-semibold text-gray-900 dark:text-gray-100" id="chart-cpu">0%</span></div>
                        <div class="w-full bg-gray-300 dark:bg-gray-600 rounded-full h-3"><div id="chart-cpu-bar" class="h-3 rounded-full transition-all duration-500" style="width: 0%; background-color: #10b981;"></div></div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="chart-cpu-detail">0 / 0 MHz</p>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1"><span class="text-xs font-medium text-gray-600 dark:text-gray-400">RAM</span><span class="text-xs font-semibold text-gray-900 dark:text-gray-100" id="chart-ram">0%</span></div>
                        <div class="w-full bg-gray-300 dark:bg-gray-600 rounded-full h-3"><div id="chart-ram-bar" class="h-3 rounded-full transition-all duration-500" style="width: 0%; background-color: #3b82f6;"></div></div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="chart-ram-detail">0 / 0 GB</p>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1"><span class="text-xs font-medium text-gray-600 dark:text-gray-400">Disk</span><span class="text-xs font-semibold text-gray-900 dark:text-gray-100" id="chart-disk">0%</span></div>
                        <div class="w-full bg-gray-300 dark:bg-gray-600 rounded-full h-3"><div id="chart-disk-bar" class="h-3 rounded-full transition-all duration-500" style="width: 0%; background-color: #f59e0b;"></div></div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="chart-disk-detail">0 / 0 GB</p>
                    </div>
                </div>
            </div>

            <script>
            (function() {
                const apiEndpoint = '{{ $apiEndpoint }}';
                const loadingEl = document.getElementById('resource-loading');
                const errorEl = document.getElementById('resource-error');
                const barsEl = document.getElementById('resource-bars');
                const chartEl = document.getElementById('resource-chart');
                const refreshBtn = document.getElementById('refresh-resource-btn');
                const refreshIcon = document.getElementById('refresh-icon');
                const refreshText = document.getElementById('refresh-text');
                const viewBarsBtn = document.getElementById('view-bars-btn');
                const viewChartBtn = document.getElementById('view-chart-btn');
                let currentView = 'bars', currentData = null;

                const getColorClass = p => p <= 60 ? 'bg-green-500' : p <= 80 ? 'bg-yellow-500' : 'bg-red-500';
                const formatNumber = n => new Intl.NumberFormat().format(Math.round(n));
                const formatDate = d => d.substring(6,8) + '/' + d.substring(4,6);
                const formatMonth = d => {const m=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; return m[parseInt(d.substring(4,6))-1] || d;};

                function setLoading(isLoading) {
                    if (isLoading) { loadingEl && (loadingEl.style.display = 'grid'); errorEl && (errorEl.style.display = 'none'); barsEl && (barsEl.style.display = 'none'); chartEl && (chartEl.style.display = 'none'); refreshIcon?.classList.add('animate-spin'); refreshText && (refreshText.textContent = 'Loading...'); refreshBtn && (refreshBtn.disabled = true); }
                    else { loadingEl && (loadingEl.style.display = 'none'); refreshIcon?.classList.remove('animate-spin'); refreshText && (refreshText.textContent = 'Refresh'); refreshBtn && (refreshBtn.disabled = false); }
                }
                function showError(msg) { errorEl.style.display = 'block'; barsEl.style.display = 'none'; chartEl.style.display = 'none'; document.getElementById('error-message').textContent = msg; }
                function switchView(view) {
                    currentView = view;
                    if (view === 'bars') { barsEl.style.display = 'grid'; chartEl.style.display = 'none'; viewBarsBtn.classList.add('bg-white','dark:bg-gray-600','text-gray-900','dark:text-gray-100','shadow-sm'); viewBarsBtn.classList.remove('text-gray-500','dark:text-gray-400'); viewChartBtn.classList.remove('bg-white','dark:bg-gray-600','text-gray-900','dark:text-gray-100','shadow-sm'); viewChartBtn.classList.add('text-gray-500','dark:text-gray-400'); }
                    else { barsEl.style.display = 'none'; chartEl.style.display = 'block'; viewChartBtn.classList.add('bg-white','dark:bg-gray-600','text-gray-900','dark:text-gray-100','shadow-sm'); viewChartBtn.classList.remove('text-gray-500','dark:text-gray-400'); viewBarsBtn.classList.remove('bg-white','dark:bg-gray-600','text-gray-900','dark:text-gray-100','shadow-sm'); viewBarsBtn.classList.add('text-gray-500','dark:text-gray-400'); if (currentData) drawCharts(currentData.bandwidth); }
                }

                function drawCharts(bw) {
                    if (!bw) return;
                    const isDark = document.documentElement.classList.contains('dark');
                    const gridColor = isDark ? '#374151' : '#e5e7eb';
                    const textColor = isDark ? '#9ca3af' : '#6b7280';

                    // Daily Chart with In/Out
                    if (bw.daily_in && bw.daily_out) {
                        const canvas = document.getElementById('daily-canvas');
                        const ctx = canvas.getContext('2d');
                        const container = canvas.parentElement;
                        canvas.width = container.clientWidth; canvas.height = container.clientHeight;
                        const dates = Object.keys(bw.daily_in).sort();
                        const inVals = dates.map(d => parseFloat(bw.daily_in[d]) || 0);
                        const outVals = dates.map(d => parseFloat(bw.daily_out[d]) || 0);
                        const maxVal = Math.max(...inVals, ...outVals, 1);
                        const pad = {top:15, right:15, bottom:5, left:45};
                        const w = canvas.width - pad.left - pad.right;
                        const h = canvas.height - pad.top - pad.bottom;
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.strokeStyle = gridColor; ctx.fillStyle = textColor; ctx.font = '10px sans-serif'; ctx.textAlign = 'right';
                        for (let i = 0; i <= 4; i++) { const y = pad.top + (h/4)*i; ctx.beginPath(); ctx.setLineDash([2,2]); ctx.moveTo(pad.left, y); ctx.lineTo(canvas.width-pad.right, y); ctx.stroke(); ctx.setLineDash([]); ctx.fillText(Math.round(maxVal-(maxVal/4)*i), pad.left-5, y+3); }
                        const inPts = dates.map((d,i) => ({x: pad.left + (i/(dates.length-1||1))*w, y: pad.top + h - (inVals[i]/maxVal)*h}));
                        const outPts = dates.map((d,i) => ({x: pad.left + (i/(dates.length-1||1))*w, y: pad.top + h - (outVals[i]/maxVal)*h}));
                        // In line (green)
                        ctx.beginPath(); ctx.strokeStyle = '#22c55e'; ctx.lineWidth = 2; inPts.forEach((p,i) => i===0 ? ctx.moveTo(p.x,p.y) : ctx.lineTo(p.x,p.y)); ctx.stroke();
                        // Out line (orange)
                        ctx.beginPath(); ctx.strokeStyle = '#f97316'; ctx.lineWidth = 2; outPts.forEach((p,i) => i===0 ? ctx.moveTo(p.x,p.y) : ctx.lineTo(p.x,p.y)); ctx.stroke();
                        // X labels
                        const xLabels = document.getElementById('daily-x-labels'); xLabels.innerHTML = '';
                        dates.forEach((d,i) => { if (i % 7 === 0 || i === dates.length-1) { const span = document.createElement('span'); span.textContent = formatDate(d); xLabels.appendChild(span); }});
                    }

                    // Yearly Chart (bar chart)
                    if (bw.yearly_usage) {
                        const canvas = document.getElementById('yearly-canvas');
                        const ctx = canvas.getContext('2d');
                        const container = canvas.parentElement;
                        canvas.width = container.clientWidth; canvas.height = container.clientHeight;
                        // Filter only months with data
                        const allMonths = Object.keys(bw.yearly_usage).sort();
                        const months = allMonths.filter(m => (bw.yearly_usage[m].in + bw.yearly_usage[m].out) > 0);
                        if (months.length === 0) return;
                        const vals = months.map(m => (bw.yearly_usage[m].in + bw.yearly_usage[m].out) / 1024); // Convert to GB
                        const maxVal = Math.max(...vals, 1);
                        const pad = {top:15, right:20, bottom:5, left:40};
                        const w = canvas.width - pad.left - pad.right;
                        const h = canvas.height - pad.top - pad.bottom;
                        // Spread bars evenly across full width
                        const barW = Math.min(35, Math.max(15, w / months.length * 0.6));
                        const spacing = (w - barW) / Math.max(1, months.length - 1);
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.strokeStyle = gridColor; ctx.fillStyle = textColor; ctx.font = '10px sans-serif'; ctx.textAlign = 'right';
                        for (let i = 0; i <= 3; i++) { const y = pad.top + (h/3)*i; ctx.beginPath(); ctx.setLineDash([2,2]); ctx.moveTo(pad.left, y); ctx.lineTo(canvas.width-pad.right, y); ctx.stroke(); ctx.setLineDash([]); ctx.fillText(Math.round(maxVal-(maxVal/3)*i), pad.left-5, y+3); }
                        // Bars with gradient - spread evenly
                        const barPositions = [];
                        months.forEach((m, i) => {
                            const x = months.length === 1 ? pad.left + w/2 - barW/2 : pad.left + i * spacing;
                            barPositions.push(x + barW/2);
                            const barH = Math.max(4, (vals[i] / maxVal) * h);
                            const grad = ctx.createLinearGradient(x, pad.top + h - barH, x, pad.top + h);
                            grad.addColorStop(0, '#818cf8'); grad.addColorStop(1, '#6366f1');
                            ctx.fillStyle = grad;
                            ctx.beginPath();
                            ctx.roundRect(x, pad.top + h - barH, barW, barH, 4);
                            ctx.fill();
                        });
                        // X labels positioned under each bar
                        const xLabels = document.getElementById('yearly-x-labels'); xLabels.innerHTML = '';
                        xLabels.style.display = 'flex'; xLabels.style.justifyContent = 'space-between'; xLabels.style.paddingLeft = pad.left + 'px'; xLabels.style.paddingRight = pad.right + 'px';
                        months.forEach(m => { const span = document.createElement('span'); span.textContent = formatMonth(m); span.style.textAlign = 'center'; span.style.flex = '1'; xLabels.appendChild(span); });
                    }
                    document.getElementById('chart-total').textContent = 'Total: ' + (bw.used_gb || 0).toFixed(2) + ' / ' + formatNumber(bw.limit_gb || 0) + ' GB';
                }

                function updateUI(data) {
                    currentData = data; errorEl.style.display = 'none';
                    if (currentView === 'bars') { barsEl.style.display = 'grid'; chartEl.style.display = 'none'; }
                    else { barsEl.style.display = 'none'; chartEl.style.display = 'block'; }
                    if (data.cpu) {
                        const p = data.cpu.percent || 0, used = formatNumber(data.cpu.used || 0), limit = formatNumber(data.cpu.limit || 0);
                        document.getElementById('cpu-percent').textContent = p.toFixed(1) + '%';
                        document.getElementById('cpu-bar').style.width = Math.min(p, 100) + '%';
                        document.getElementById('cpu-bar').className = 'h-2.5 rounded-full transition-all duration-500 ' + getColorClass(p);
                        document.getElementById('cpu-used').textContent = used; document.getElementById('cpu-limit').textContent = limit;
                        document.getElementById('chart-cpu').textContent = p.toFixed(0) + '%';
                        document.getElementById('chart-cpu-bar').style.width = Math.min(p, 100) + '%';
                        document.getElementById('chart-cpu-detail').textContent = used + ' / ' + limit + ' MHz';
                    }
                    if (data.ram) {
                        const p = data.ram.percent || 0, usedGb = ((data.ram.used || 0) / 1024).toFixed(2), limitGb = ((data.ram.limit || 0) / 1024).toFixed(2);
                        document.getElementById('ram-percent').textContent = p.toFixed(1) + '%';
                        document.getElementById('ram-bar').style.width = Math.min(p, 100) + '%';
                        document.getElementById('ram-bar').className = 'h-2.5 rounded-full transition-all duration-500 ' + getColorClass(p);
                        document.getElementById('ram-used').textContent = usedGb; document.getElementById('ram-limit').textContent = limitGb;
                        document.getElementById('chart-ram').textContent = p.toFixed(0) + '%';
                        document.getElementById('chart-ram-bar').style.width = Math.min(p, 100) + '%';
                        document.getElementById('chart-ram-detail').textContent = usedGb + ' / ' + limitGb + ' GB';
                    }
                    if (data.disk) {
                        const p = data.disk.percent || 0, usedGb = (data.disk.used_gb || 0).toFixed(2), limitGb = (data.disk.limit_gb || 0).toFixed(2);
                        document.getElementById('disk-percent').textContent = p.toFixed(1) + '%';
                        document.getElementById('disk-bar').style.width = Math.min(p, 100) + '%';
                        document.getElementById('disk-bar').className = 'h-2.5 rounded-full transition-all duration-500 ' + getColorClass(p);
                        document.getElementById('disk-used').textContent = usedGb; document.getElementById('disk-limit').textContent = limitGb;
                        document.getElementById('chart-disk').textContent = p.toFixed(0) + '%';
                        document.getElementById('chart-disk-bar').style.width = Math.min(p, 100) + '%';
                        document.getElementById('chart-disk-detail').textContent = usedGb + ' / ' + limitGb + ' GB';
                    }
                    if (data.bandwidth) {
                        const p = data.bandwidth.percent || 0;
                        document.getElementById('bandwidth-percent').textContent = p.toFixed(1) + '%';
                        document.getElementById('bandwidth-bar').style.width = Math.min(p, 100) + '%';
                        document.getElementById('bandwidth-bar').className = 'h-2.5 rounded-full transition-all duration-500 ' + getColorClass(p);
                        document.getElementById('bandwidth-used').textContent = (data.bandwidth.used_gb || 0).toFixed(2);
                        document.getElementById('bandwidth-limit').textContent = formatNumber(data.bandwidth.limit_gb || 0);
                        if (currentView === 'chart') setTimeout(() => drawCharts(data.bandwidth), 50);
                    }
                }
                async function loadResourceUsage() {
                    setLoading(true);
                    try {
                        const res = await fetch(apiEndpoint, {method:'GET', credentials:'same-origin', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')||''}});
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        const result = await res.json();
                        if (result.success && result.data) updateUI(result.data); else showError(result.message || 'Failed to load.');
                    } catch (e) { console.error('Error:', e); showError('Failed to connect.'); }
                    finally { setLoading(false); }
                }
                viewBarsBtn?.addEventListener('click', () => switchView('bars'));
                viewChartBtn?.addEventListener('click', () => switchView('chart'));
                document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', loadResourceUsage) : loadResourceUsage();
                refreshBtn?.addEventListener('click', loadResourceUsage);
                window.addEventListener('resize', () => { if (currentView === 'chart' && currentData) drawCharts(currentData.bandwidth); });
            })();
            </script>
        @else
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"><p class="text-sm text-gray-500 dark:text-gray-400">Resource usage data is not available.</p></div>
        @endif
    </div>
</div>

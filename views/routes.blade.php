<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Routes List @if(config('app.name')) | {{ config('app.name') }} @endif</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .method-badge { @apply px-2.5 py-0.5 rounded-full text-xs font-semibold border; }
        .action-highlight { color: #f97316; font-weight: 500; }
        th.sortable { cursor: pointer; user-select: none; transition: background-color 0.2s; }
        th.sortable:hover { background-color: #f3f4f6; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 p-6 md:p-12">

    <div class="max-w-8xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Routes List</h1>
                <p class="text-gray-500 mt-1">Total routes: <span class="font-semibold text-orange-600">{{ count($routes) }}</span></p>
            </div>
            
            <!-- Controls -->
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="searchInput" placeholder="Search name, action, middleware..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none w-full sm:w-64 transition-shadow">
                </div>
                <select id="methodFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none bg-white cursor-pointer">
                    <option value="ALL">All Methods</option>
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="PATCH">PATCH</option>
                    <option value="DELETE">DELETE</option>
                    <option value="HEAD">HEAD</option>
                    <option value="OPTIONS">OPTIONS</option>
                </select>
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left" id="routesTable">
                    <thead class="bg-gray-50 text-gray-600 font-semibold uppercase text-xs tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 sortable" data-col="0">Methods <span class="sort-icon text-gray-400">↕</span></th>
                            <th class="px-6 py-4 sortable" data-col="2">Name <span class="sort-icon text-gray-400">↕</span></th>
                            <th class="px-6 py-4 sortable" data-col="3">Action <span class="sort-icon text-gray-400">↕</span></th>
                            <th class="px-6 py-4 sortable" data-col="4">Middleware <span class="sort-icon text-gray-400">↕</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $methodClasses = [
                                'GET' => 'bg-green-50 text-green-700 border-green-200',
                                'POST' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'PUT' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'PATCH' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'DELETE' => 'bg-red-50 text-red-700 border-red-200',
                                'HEAD' => 'bg-gray-100 text-gray-600 border-gray-200',
                                'OPTIONS' => 'bg-gray-100 text-gray-600 border-gray-200',
                            ];
                        @endphp

                        @foreach ($routes as $route)
                            <tr class="hover:bg-gray-50 transition-colors duration-150 group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach (array_diff($route->methods(), config('cute-routes.hide_methods', [])) as $method)
                                            <span class="method-badge {{ $methodClasses[$method] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ $method }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $route->getName() ?: 'unnamed' }}
                                </td>
                                <td class="px-6 py-4 font-mono text-gray-700">
                                    {!! preg_replace('#(@.*)$#', '<span class="action-highlight">$1</span>', $route->getActionName()) !!}
                                </td>
                                <td class="px-6 py-4 text-gray-500 max-w-xs truncate" title="{{ is_callable([$route, 'controllerMiddleware']) ? implode(', ', array_map($middlewareClosure, array_merge($route->middleware(), $route->controllerMiddleware()))) : implode(', ', $route->middleware()) }}">
                                    @if (is_callable([$route, 'controllerMiddleware']))
                                        {{ implode(', ', array_map($middlewareClosure, array_merge($route->middleware(), $route->controllerMiddleware()))) }}
                                    @else
                                        {{ implode(', ', $route->middleware()) }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Empty State (Hidden by default) -->
            <div id="emptyState" class="hidden p-12 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-lg font-medium">No routes found matching your criteria.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const methodFilter = document.getElementById('methodFilter');
            const tableBody = document.querySelector('#routesTable tbody');
            const emptyState = document.getElementById('emptyState');
            let rows = Array.from(tableBody.querySelectorAll('tr'));
            
            let sortCol = -1;
            let sortAsc = true;

            // Auto-hide domain column if all are empty
            const domains = tableBody.querySelectorAll('.domain');
            const emptyDomains = tableBody.querySelectorAll('.domain-empty');
            if (domains.length > 0 && domains.length === emptyDomains.length) {
                document.querySelectorAll('.domain-col').forEach(el => el.style.display = 'none');
            }

            // Filter Logic
            function applyFilters() {
                const search = searchInput.value.toLowerCase();
                const method = methodFilter.value;
                let visibleCount = 0;

                rows.forEach(row => {
                    const textContent = row.textContent.toLowerCase();
                    const methodBadges = Array.from(row.querySelectorAll('.method-badge')).map(b => b.textContent.trim());
                    
                    const matchesSearch = textContent.includes(search);
                    const matchesMethod = method === 'ALL' || methodBadges.includes(method);
                    
                    if (matchesSearch && matchesMethod) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                emptyState.classList.toggle('hidden', visibleCount > 0);
                applySort();
            }

            // Sort Logic
            function applySort() {
                if (sortCol === -1) return;

                const visibleRows = rows.filter(r => r.style.display !== 'none');

                visibleRows.sort((a, b) => {
                    const aText = a.cells[sortCol].textContent.trim();
                    const bText = b.cells[sortCol].textContent.trim();
                    const comparison = aText.localeCompare(bText, undefined, { numeric: true });
                    return sortAsc ? comparison : -comparison;
                });

                visibleRows.forEach(row => tableBody.appendChild(row));
            }

            function handleSort(colIndex) {
                if (sortCol === colIndex) {
                    sortAsc = !sortAsc;
                } else {
                    sortCol = colIndex;
                    sortAsc = true;
                }
                updateSortIcons();
                applySort();
            }

            function updateSortIcons() {
                document.querySelectorAll('th.sortable').forEach((th) => {
                    const icon = th.querySelector('.sort-icon');
                    const colIndex = parseInt(th.dataset.col);
                    
                    if (colIndex === sortCol) {
                        icon.textContent = sortAsc ? '↑' : '↓';
                        th.classList.add('text-orange-600');
                    } else {
                        icon.textContent = '↕';
                        th.classList.remove('text-orange-600');
                    }
                });
            }

            // Event Listeners
            searchInput.addEventListener('input', applyFilters);
            methodFilter.addEventListener('change', applyFilters);

            document.querySelectorAll('th.sortable').forEach(th => {
                th.addEventListener('click', () => handleSort(parseInt(th.dataset.col)));
            });

            // Initial setup
            updateSortIcons();
        });
    </script>
</body>
</html>
<?php
$f = 'd:/xampp/htdocs/totaltech/geeksales/admin/index.php';
$content = file_get_contents($f);

$head_injection = <<<EOT
    <style>
        /* Admin View Toggle Styles */
        body.admin-grid-view .admin-table { display: block; width: 100%; border: none !important; }
        body.admin-grid-view .admin-table thead { display: none; }
        body.admin-grid-view .admin-table tbody { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
        body.admin-grid-view .admin-table tr { display: flex; flex-direction: column; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1rem; background: #fff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
        body.admin-grid-view .admin-table td { display: block; border: none !important; padding: 0.25rem 0 !important; }
        body.admin-grid-view .admin-table td:first-child { align-self: flex-end; order: -1; margin-bottom: 0.5rem; }
        
        body.admin-list-view .view-wrapper { display: flex !important; flex-direction: column !important; gap: 1rem; }
        body.admin-list-view .view-wrapper .card-hover, body.admin-list-view .view-wrapper > div { display: flex !important; align-items: center !important; justify-content: space-between !important; padding: 1rem !important; gap: 1rem; flex-wrap: wrap; }
        body.admin-list-view .view-wrapper .card-hover > div { margin-bottom: 0 !important; width: auto !important; }
        body.admin-list-view .view-wrapper .item-checkbox { position: static !important; margin-right: 0.5rem; }
    </style>
    <script>
        function applyAdminView() {
            const v = localStorage.getItem('admin_view') || 'list';
            const isGrid = v === 'grid';
            
            const btnGrid = document.getElementById('view-grid-btn');
            const btnList = document.getElementById('view-list-btn');
            if (btnGrid) btnGrid.className = isGrid ? 'px-2.5 bg-navy-600 text-white transition' : 'px-2.5 bg-white text-slate-400 hover:text-navy-600 transition';
            if (btnList) btnList.className = !isGrid ? 'px-2.5 bg-navy-600 text-white transition' : 'px-2.5 bg-white text-slate-400 hover:text-navy-600 transition';
            
            if (isGrid) {
                document.body.classList.add('admin-grid-view');
                document.body.classList.remove('admin-list-view');
            } else {
                document.body.classList.add('admin-list-view');
                document.body.classList.remove('admin-grid-view');
            }
        }
        
        function setAdminView(v) {
            localStorage.setItem('admin_view', v);
            applyAdminView();
        }
        
        document.addEventListener('DOMContentLoaded', applyAdminView);
    </script>
</head>
EOT;

$content = str_replace('</head>', $head_injection, $content);

$header_injection = <<<EOT
                <div class="flex items-center gap-3">
                    <?php if(in_array(\$page, ['products','categories','brands','orders','leads','customers','refunds'])): ?>
                    <div class="flex border border-slate-200 rounded-lg overflow-hidden h-8 mr-2">
                        <button onclick="setAdminView('grid')" id="view-grid-btn" class="px-2.5 bg-white text-slate-400 hover:text-navy-600 transition" title="Grid View"><i class="ri-grid-fill"></i></button>
                        <button onclick="setAdminView('list')" id="view-list-btn" class="px-2.5 bg-navy-600 text-white transition" title="List View"><i class="ri-list-check-2"></i></button>
                    </div>
                    <?php endif; ?>
                    <a href="?page=leads" class="relative text-slate-400 header-icon transition">
EOT;

$content = str_replace('<div class="flex items-center gap-3">' . "\n" . '                    <a href="?page=leads" class="relative text-slate-400 header-icon transition">', $header_injection, $content);

file_put_contents($f, $content);
echo "Patched index.php\n";

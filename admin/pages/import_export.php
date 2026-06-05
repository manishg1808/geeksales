<div class="animate-slide">

<div class="mb-6">
    <h2 class="text-xl font-black text-slate-800 flex items-center gap-2"><i class="ri-upload-cloud-2-line text-navy-600"></i> Product Import / Export</h2>
    <p class="text-sm text-slate-400">Bulk manage products via CSV files</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    <!-- Import -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
            <div class="bg-emerald-100 rounded-xl w-11 h-11 flex items-center justify-center shrink-0">
                <i class="ri-upload-2-line text-emerald-600 text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">Import Products</h3>
                <p class="text-xs text-slate-400">Upload CSV to add or update products</p>
            </div>
        </div>

        <!-- Upload Zone -->
        <div id="drop-zone" class="border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center hover:border-emerald-400 hover:bg-emerald-50/50 transition cursor-pointer group mb-5"
            ondragover="event.preventDefault();this.classList.add('border-emerald-500','bg-emerald-50')"
            ondragleave="this.classList.remove('border-emerald-500','bg-emerald-50')"
            ondrop="handleDrop(event)"
            onclick="document.getElementById('csv-input').click()">
            <input type="file" id="csv-input" accept=".csv" class="hidden" onchange="handleFile(this)"/>
            <i class="ri-file-excel-2-line text-slate-300 group-hover:text-emerald-500 text-5xl mb-3 transition"></i>
            <p class="font-bold text-slate-600 group-hover:text-emerald-700 transition">Drag & drop your CSV here</p>
            <p class="text-sm text-slate-400 mt-1">or <span class="text-emerald-600 font-semibold underline">browse file</span></p>
            <p class="text-xs text-slate-400 mt-3">Supported: .csv — Max 10MB</p>
        </div>

        <!-- File Selected State (hidden by default) -->
        <div id="file-selected" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3 mb-4">
            <i class="ri-file-excel-2-fill text-emerald-600 text-2xl shrink-0"></i>
            <div class="flex-1 min-w-0">
                <p id="file-name" class="font-bold text-slate-800 text-sm truncate">products.csv</p>
                <p id="file-size" class="text-xs text-slate-400">2.3 MB · 248 rows detected</p>
            </div>
            <button onclick="clearFile()" class="text-slate-400 hover:text-red-500 transition"><i class="ri-close-line text-lg"></i></button>
        </div>

        <!-- Import Options -->
        <div class="space-y-3 mb-5">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-wider">Import Options</p>
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="radio" name="import_mode" value="add" checked class="accent-navy-600"/>
                <div>
                    <span class="text-sm font-semibold text-slate-700">Add New Products Only</span>
                    <p class="text-xs text-slate-400">Skip existing products</p>
                </div>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="radio" name="import_mode" value="update" class="accent-navy-600"/>
                <div>
                    <span class="text-sm font-semibold text-slate-700">Update Existing + Add New</span>
                    <p class="text-xs text-slate-400">Match by product name</p>
                </div>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="radio" name="import_mode" value="replace" class="accent-navy-600"/>
                <div>
                    <span class="text-sm font-semibold text-slate-700">Replace All Products</span>
                    <p class="text-xs text-red-500 font-semibold flex items-center gap-1"><i class="ri-error-warning-line"></i> This will delete all existing products!</p>
                </div>
            </label>
        </div>

        <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 text-sm">
            <i class="ri-upload-2-line"></i> Start Import
        </button>
    </div>

    <!-- Export -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
            <div class="bg-navy-100 rounded-xl w-11 h-11 flex items-center justify-center shrink-0">
                <i class="ri-download-2-line text-navy-600 text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">Export Products</h3>
                <p class="text-xs text-slate-400">Download your product data as CSV</p>
            </div>
        </div>

        <!-- Export Options -->
        <div class="space-y-3 mb-5">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-wider">Export Scope</p>
            <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border border-slate-200 hover:border-navy-400 hover:bg-navy-50 transition">
                <input type="radio" name="export_scope" value="all" checked class="accent-navy-600"/>
                <div class="flex-1">
                    <span class="text-sm font-semibold text-slate-700">All Products</span>
                    <p class="text-xs text-slate-400">248 products</p>
                </div>
                <span class="text-xs bg-navy-100 text-navy-700 font-bold px-2 py-0.5 rounded-lg">248</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border border-slate-200 hover:border-navy-400 hover:bg-navy-50 transition">
                <input type="radio" name="export_scope" value="active" class="accent-navy-600"/>
                <div class="flex-1">
                    <span class="text-sm font-semibold text-slate-700">Active Products Only</span>
                    <p class="text-xs text-slate-400">231 products</p>
                </div>
                <span class="text-xs bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-lg">231</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border border-slate-200 hover:border-navy-400 hover:bg-navy-50 transition">
                <input type="radio" name="export_scope" value="filtered" class="accent-navy-600"/>
                <div class="flex-1">
                    <span class="text-sm font-semibold text-slate-700">Filter by Category / Brand</span>
                    <p class="text-xs text-slate-400">Choose below</p>
                </div>
            </label>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-5">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Category</label>
                <select class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white outline-none focus:border-navy-600 transition">
                    <option>All Categories</option><option>Inkjet</option><option>Laser</option><option>All-in-One</option><option>Business</option><option>Ink & Toner</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Brand</label>
                <select class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white outline-none focus:border-navy-600 transition">
                    <option>All Brands</option><option>HP</option><option>Canon</option><option>Brother</option><option>Epson</option>
                </select>
            </div>
        </div>

        <p class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Include Columns</p>
        <div class="grid grid-cols-2 gap-2 mb-5">
            <?php foreach(['ID','Name','Brand','Category','Price','Old Price','Stock','Status','Description','Badge'] as $col): ?>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" checked class="accent-navy-600 rounded"/>
                <span class="text-sm text-slate-600"><?php echo $col; ?></span>
            </label>
            <?php endforeach; ?>
        </div>

        <button class="w-full bg-navy-600 hover:bg-navy-700 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 text-sm">
            <i class="ri-download-2-line"></i> Export to CSV
        </button>
    </div>
</div>

<!-- CSV Template + Import History -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    <!-- CSV Template -->
    <div class="bg-white border border-slate-200 rounded-2xl p-5">
        <h4 class="font-bold text-slate-800 text-sm mb-1 flex items-center gap-2"><i class="ri-file-list-3-line text-emerald-600"></i> CSV Template</h4>
        <p class="text-xs text-slate-400 mb-4">Download sample CSV to format your data correctly</p>
        <div class="bg-slate-50 border border-slate-200 rounded-xl overflow-hidden mb-4">
            <div class="overflow-x-auto">
                <table class="text-xs w-full">
                    <thead class="bg-slate-100">
                        <tr>
                            <?php foreach(['name','brand','category','price','old_price','stock','status','badge'] as $h): ?>
                            <th class="px-3 py-2 text-left font-bold text-slate-600 whitespace-nowrap"><?php echo $h; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-200">
                            <td class="px-3 py-2 text-slate-500 whitespace-nowrap">HP DeskJet 4155e</td>
                            <td class="px-3 py-2 text-slate-500">HP</td>
                            <td class="px-3 py-2 text-slate-500">inkjet</td>
                            <td class="px-3 py-2 text-slate-500">89.99</td>
                            <td class="px-3 py-2 text-slate-500">119.99</td>
                            <td class="px-3 py-2 text-slate-500">45</td>
                            <td class="px-3 py-2 text-slate-500">active</td>
                            <td class="px-3 py-2 text-slate-500">SALE</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <button class="w-full flex items-center justify-center gap-2 border border-emerald-300 text-emerald-700 hover:bg-emerald-50 font-semibold py-2.5 rounded-xl transition text-sm">
            <i class="ri-download-line"></i> Download Sample CSV
        </button>
    </div>

    <!-- Import History -->
    <div class="bg-white border border-slate-200 rounded-2xl p-5">
        <h4 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2"><i class="ri-history-line text-slate-500"></i> Import History</h4>
        <div class="space-y-3">
            <?php
            $hist=[
                ['products_june.csv','2026-06-01','248 added, 0 errors','success'],
                ['products_may.csv','2026-05-01','230 added, 3 errors','warning'],
                ['products_april.csv','2026-04-01','210 added, 0 errors','success'],
                ['products_march.csv','2026-03-01','Failed — invalid format','error'],
            ];
            $hclr=['success'=>['bg-emerald-100 text-emerald-700','ri-checkbox-circle-fill'],
                   'warning'=>['bg-amber2-100 text-amber2-700','ri-error-warning-fill'],
                   'error'  =>['bg-red-100 text-red-600','ri-close-circle-fill']];
            foreach($hist as $h): $c=$hclr[$h[3]]; ?>
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                <i class="<?php echo $c[1]; ?> text-lg <?php echo explode(' ',$c[0])[1]; ?>"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 truncate"><?php echo $h[0]; ?></p>
                    <p class="text-xs text-slate-400"><?php echo $h[1]; ?> · <?php echo $h[2]; ?></p>
                </div>
                <span class="<?php echo $c[0]; ?> text-[10px] font-bold px-2 py-0.5 rounded-full"><?php echo ucfirst($h[3]); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</div>
<script>
function handleFile(input){
    if(input.files.length){
        document.getElementById('file-selected').classList.remove('hidden');
        document.getElementById('file-name').textContent=input.files[0].name;
        const sz=(input.files[0].size/1024/1024).toFixed(1);
        document.getElementById('file-size').textContent=sz+' MB';
    }
}
function handleDrop(e){
    e.preventDefault();
    const dt=e.dataTransfer;
    if(dt.files.length){
        document.getElementById('csv-input').files=dt.files;
        handleFile(document.getElementById('csv-input'));
    }
}
function clearFile(){
    document.getElementById('file-selected').classList.add('hidden');
    document.getElementById('csv-input').value='';
}
</script>

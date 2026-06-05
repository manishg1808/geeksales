import sys, os

files = [
    'products.php', 'categories.php', 'brands.php', 
    'orders.php', 'leads.php', 'customers.php', 'refunds.php'
]

def patch_file(filename):
    path = f'd:/xampp/htdocs/totaltech/geeksales/admin/pages/{filename}'
    if not os.path.exists(path):
        return
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Inject the toggle buttons next to the title or inside the header.
    # Look for the header flex div.
    # Typically: <div class="flex items-center gap-2"> or <div class="flex items-center justify-between
    
    # We will inject the toggle in admin/index.php instead to be global!
    
    # However, we need to adapt the files to support grid and list classes.
    # For grids (categories, leads, customers, refunds):
    if filename in ['categories.php', 'leads.php', 'customers.php', 'refunds.php']:
        if '<div class="grid ' in content:
            content = content.replace('<div class="grid ', '<div class="view-wrapper grid ')
        elif '<div class="space-y-4">' in content:
            content = content.replace('<div class="space-y-4">', '<div class="view-wrapper space-y-4">')

    # For tables (products, brands, orders):
    if filename in ['products.php', 'brands.php', 'orders.php']:
        if '<table' in content:
            content = content.replace('<table', '<table class="admin-table"')

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)

for f in files:
    patch_file(f)
print("Patched individual pages.")

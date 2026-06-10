<?php
require_once __DIR__ . '/admin/includes/db.php';

$slug = trim($_GET['slug'] ?? '');
$policy = null;

try {
    $stmt = db()->prepare("SELECT * FROM policies WHERE slug = ?");
    $stmt->execute([$slug]);
    $policy = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

if (!$policy) {
    header('HTTP/1.0 404 Not Found');
}

function render_policy_content(string $content): string
{
    $content = trim($content);
    if ($content === '') {
        return '<p>This policy will be updated soon.</p>';
    }

    $allowedTags = '<p><br><h1><h2><h3><h4><strong><b><em><i><ul><ol><li><a>';
    if (preg_match('/<\/?[a-z][\s\S]*>/i', $content)) {
        return strip_tags($content, $allowedTags);
    }

    $text = html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace("/\r\n|\r/", "\n", $text);
    $text = preg_replace('/[ \t]+/', ' ', $text);
    $text = preg_replace('/\n{3,}/', "\n\n", $text);

    $headings = [
        'RETURN POLICY',
        'RETURNS',
        'RETURN PROCESS',
        'REFUNDS',
        'EXCEPTIONS',
        'QUESTIONS',
        'PRIVACY POLICY',
        'TERMS AND CONDITIONS',
        'SHIPPING POLICY',
        'CONTACT',
    ];
    foreach ($headings as $heading) {
        $text = preg_replace('/(?<!^)\b' . preg_quote($heading, '/') . '\b/i', "\n\n" . ucwords(strtolower($heading)) . "\n", $text);
    }

    $blocks = preg_split('/\n\s*\n/', trim($text));
    $html = [];
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }
        $lines = array_values(array_filter(array_map('trim', explode("\n", $block))));
        if (count($lines) === 1 && preg_match('/^(Return Policy|Returns|Return Process|Refunds|Exceptions|Questions|Privacy Policy|Terms And Conditions|Shipping Policy|Contact)$/i', $lines[0])) {
            $html[] = '<h2>' . e($lines[0]) . '</h2>';
            continue;
        }
        if (count($lines) > 1 && preg_match('/^[-*]\s+/', $lines[0])) {
            $html[] = '<ul>' . implode('', array_map(static fn($line) => '<li>' . e(preg_replace('/^[-*]\s+/', '', $line)) . '</li>', $lines)) . '</ul>';
            continue;
        }
        $html[] = '<p>' . nl2br(e(implode("\n", $lines))) . '</p>';
    }

    return implode("\n", $html);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="icon" type="image/svg+xml" href="IMAGE/geeksupport_unique_simple_icon.svg">
  <?php 
  $seo = get_page_seo(); 
  $pageTitle = $policy ? e($policy['title']) . ' - Geek Support LLc' : $seo['title'];
  $pageDesc = $policy ? e($policy['title']) . ' for Geek Support LLc' : $seo['description'];
  ?>
  <title><?php echo $pageTitle; ?></title>
  <meta name="description" content="<?php echo $pageDesc; ?>" />
  <meta name="keywords" content="<?php echo e($seo['keywords']); ?>" />
  <?php
  render_google_site_verification();
  render_google_analytics();
  render_google_tag_manager_head();
  ?>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            navy: { 50:'#F8FAFC',100:'#F1F5F9',200:'#E5E7EB',300:'#CBD5E1',400:'#6B7280',500:'#2563EB',600:'#2563EB',700:'#1D4ED8',800:'#0F172A',900:'#0F172A' },
            amber2:{ 50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',300:'#FDBA74',400:'#FB923C',500:'#F97316',600:'#EA580C',700:'#C2410C',800:'#9A3412',900:'#7C2D12' },
          },
          fontFamily:{ sans:['Raleway','system-ui','sans-serif'] },
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
  <style>
    .btn-gradient{background:#F97316;color:#fff}
    .brand-gradient{background:#2563EB}
    .policy-content{font-size:1rem;line-height:1.85;color:#475569}
    .policy-content h1,.policy-content h2,.policy-content h3{color:#0f172a;font-weight:900;margin-top:2rem;margin-bottom:.75rem;letter-spacing:0}
    .policy-content h1{font-size:1.875rem}
    .policy-content h2{font-size:1.25rem;border-left:4px solid #2563EB;padding-left:.85rem}
    .policy-content h3{font-size:1.05rem}
    .policy-content p{margin-bottom:1.15rem;color:#475569;max-width:72ch}
    .policy-content ul,.policy-content ol{padding-left:1.25em;margin-bottom:1.25rem;color:#475569;max-width:72ch}
    .policy-content ul li{list-style:disc;margin-bottom:.45rem}
    .policy-content ol li{list-style:decimal;margin-bottom:.45rem}
    .policy-content a{color:#2563EB;text-decoration:underline}
    .policy-content strong{color:#1e293b}
  </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased">
<?php render_google_tag_manager_body(); ?>

<!-- TOP BAR -->
<div class="brand-gradient text-white hidden md:block">
  <div class="max-w-7xl mx-auto px-6 py-2 flex items-center justify-between text-xs">
    <div class="flex items-center gap-5 shrink-0">
      <a href="tel:407-246-9887" class="flex items-center gap-1.5 text-slate-300 hover:text-white transition">
        <i class="ri-phone-fill text-amber2-400"></i>
        <span class="font-medium">407-246-9887</span>
      </a>
      <span class="w-px h-3 bg-slate-600"></span>
      <a href="mailto:support@geeksupportllc.com" class="flex items-center gap-1.5 text-slate-300 hover:text-white transition">
        <i class="ri-mail-fill text-amber2-400"></i>
        <span class="font-medium">support@geeksupportllc.com</span>
      </a>
    </div>
  </div>
</div>

<!-- NAVBAR -->
<header class="bg-white border-b border-slate-100 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center gap-3 lg:gap-8">
    <a href="index.php" class="flex items-center gap-2.5 shrink-0">
      <div class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center shrink-0">
        <img src="IMAGE/geeksupport_unique_simple_icon.svg" alt="Geek Support LLc" class="w-6 h-6 sm:w-7 sm:h-7 object-contain">
      </div>
      <span class="flex flex-col justify-center leading-none">
        <span class="text-[13px] sm:text-[15px] font-black text-slate-900 whitespace-nowrap">Geek Support LLc</span>
        <span class="mt-1 text-[7px] sm:text-[9px] font-bold uppercase tracking-wide sm:tracking-widest text-slate-400 whitespace-nowrap">fast secure remote help</span>
      </span>
    </a>
    <nav class="hidden md:flex items-center gap-1 ml-4 flex-1">
      <a href="index.php" class="px-3 py-2 text-sm font-semibold text-slate-600 hover:text-navy-600 transition rounded-lg">Home</a>
      <a href="products.php" class="px-3 py-2 text-sm font-semibold text-slate-600 hover:text-navy-600 transition rounded-lg">Products</a>
      <a href="support.php" class="px-3 py-2 text-sm font-semibold text-slate-600 hover:text-navy-600 transition rounded-lg">Support</a>
      <a href="contact.php" class="px-3 py-2 text-sm font-semibold text-slate-600 hover:text-navy-600 transition rounded-lg">Contact</a>
    </nav>
    <div class="flex items-center gap-1 shrink-0 ml-auto md:hidden">
      <a href="tel:407-246-9887" class="w-9 h-9 flex items-center justify-center text-slate-600 hover:text-navy-600 transition" aria-label="Call support">
        <i class="ri-phone-line text-xl"></i>
      </a>
      <button type="button" onclick="toggleMobileNav(true)" class="w-9 h-9 flex items-center justify-center text-slate-700 hover:text-navy-600 transition" aria-label="Open menu">
        <i class="ri-menu-3-line text-2xl"></i>
      </button>
    </div>
  </div>
</header>
<?php include __DIR__ . '/includes/mobile_nav.php'; ?>

<!-- BREADCRUMB -->
<div class="bg-white border-b border-slate-100">
  <div class="max-w-7xl mx-auto px-6 py-3 flex items-center gap-2 text-xs text-slate-500">
    <a href="index.php" class="hover:text-navy-600 transition">Home</a>
    <i class="ri-arrow-right-s-line"></i>
    <span class="text-slate-700 font-semibold"><?php echo $policy ? e($policy['title']) : 'Not Found'; ?></span>
  </div>
</div>

<!-- MAIN CONTENT -->
<main class="max-w-5xl mx-auto px-6 py-10 md:py-12">
  <?php if ($policy): ?>
    <div class="mb-7">
      <p class="text-xs font-black uppercase tracking-widest text-navy-600 mb-2">Policy</p>
      <h1 class="text-3xl md:text-4xl font-black text-slate-900 mb-2"><?php echo e($policy['title']); ?></h1>
      <p class="text-sm text-slate-400">Last updated: <?php echo date('F d, Y', strtotime($policy['updated_at'])); ?></p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-10">
      <div class="policy-content">
        <?php echo render_policy_content((string)$policy['content']); ?>
      </div>
    </div>
  <?php else: ?>
    <div class="text-center py-20">
      <i class="ri-file-unknow-line text-6xl text-slate-300 mb-4 block"></i>
      <h1 class="text-2xl font-black text-slate-800 mb-2">Policy Not Found</h1>
      <p class="text-slate-500 mb-6">The policy you're looking for doesn't exist or has been removed.</p>
      <a href="index.php" class="btn-gradient text-white font-bold px-6 py-3 rounded-xl inline-flex items-center gap-2">
        <i class="ri-home-line"></i> Back to Home
      </a>
    </div>
  <?php endif; ?>
</main>

<!-- FOOTER -->
<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPVP Banda Aceh - Sistem Informasi Pelatihan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo blk banda.png') }}">
    <style>
        :root {
            --navy:   #0b1f4b;
            --teal:   #0d9488;
            --teal-l: #ccfbf1;
            --teal-d: #0f766e;
            --gold:   #f59e0b;
            --ink:    #0f172a;
            --muted:  #64748b;
            --line:   #e2e8f0;
            --white:  #ffffff;
        }

        * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        .font-display { font-family: 'Syne', sans-serif; }

        /* ── Scroll progress bar ── */
        #scroll-bar {
            position: fixed; top: 0; left: 0; height: 3px;
            background: linear-gradient(90deg, #0d9488, #f59e0b);
            width: 0%; z-index: 9999; transition: width .1s;
        }

        /* ── Navbar ── */
        #navbar {
            position: fixed; width: 100%; z-index: 1000;
            transition: background .3s, box-shadow .3s, padding .3s;
            padding: 1rem 0;
        }
        #navbar.scrolled {
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(12px);
            box-shadow: 0 1px 20px rgba(0,0,0,.08);
            padding: .6rem 0;
        }
        #navbar.scrolled .nav-link { color: #1e293b; }
        #navbar.scrolled .nav-link:hover { color: var(--teal); }
        .nav-link {
            color: rgba(255,255,255,.85);
            font-size: .875rem; font-weight: 600;
            padding: .4rem .75rem;
            border-radius: .375rem;
            transition: color .2s, background .2s;
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute; bottom: -2px; left: .75rem; right: .75rem;
            height: 2px; background: var(--teal);
            transform: scaleX(0); transform-origin: center;
            transition: transform .2s;
            border-radius: 999px;
        }
        .nav-link:hover::after { transform: scaleX(1); }
        .nav-link:hover { color: #fff; }
        #navbar.scrolled .nav-link:hover { color: var(--teal); }

        /* ── Buttons ── */
        .btn-primary {
            display: inline-flex; align-items: center; gap: .5rem;
            background: var(--teal);
            color: #fff;
            padding: .65rem 1.5rem;
            border-radius: .625rem;
            font-weight: 700; font-size: .875rem;
            transition: background .2s, transform .2s, box-shadow .2s;
            box-shadow: 0 4px 14px rgba(13,148,136,.35);
            text-decoration: none;
        }
        .btn-primary:hover { background: var(--teal-d); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(13,148,136,.4); }

        .btn-outline {
            display: inline-flex; align-items: center; gap: .5rem;
            border: 2px solid rgba(255,255,255,.5);
            color: #fff;
            padding: .65rem 1.5rem;
            border-radius: .625rem;
            font-weight: 700; font-size: .875rem;
            transition: background .2s, border-color .2s, transform .2s;
            text-decoration: none;
        }
        .btn-outline:hover { background: rgba(255,255,255,.12); border-color: rgba(255,255,255,.9); transform: translateY(-2px); }

        .btn-white {
            display: inline-flex; align-items: center; gap: .5rem;
            background: #fff;
            color: var(--navy);
            padding: .75rem 1.75rem;
            border-radius: .75rem;
            font-weight: 800; font-size: 1rem;
            transition: transform .2s, box-shadow .2s;
            box-shadow: 0 4px 18px rgba(0,0,0,.15);
            text-decoration: none;
        }
        .btn-white:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.2); }

        /* ── Hero ── */
        .hero-bg {
            background:
                radial-gradient(ellipse at 70% 30%, rgba(13,148,136,.25) 0%, transparent 55%),
                radial-gradient(ellipse at 10% 80%, rgba(245,158,11,.12) 0%, transparent 45%),
                linear-gradient(150deg, #071330 0%, #0b1f4b 45%, #0f3460 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        .hero-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }
        .hero-blob {
            position: absolute; border-radius: 50%;
            filter: blur(60px); pointer-events: none; opacity: .4;
        }

        /* ── Stat Float Card ── */
        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }
        .stat-float { animation: floatY 4s ease-in-out infinite; }
        .stat-item {
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 1rem;
            padding: 1.25rem 1rem;
            text-align: center;
            flex: 1;
            min-width: 110px;
        }
        .stat-num-hero {
            font-family: 'Syne', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }
        .stat-label-hero { font-size: .72rem; color: rgba(255,255,255,.55); margin-top: .3rem; font-weight: 500; }

        /* ── Section ── */
        .section-eyebrow {
            display: inline-flex; align-items: center; gap: .5rem;
            background: var(--teal-l);
            color: var(--teal-d);
            font-size: .72rem; font-weight: 700;
            letter-spacing: .08em; text-transform: uppercase;
            padding: .3rem .875rem;
            border-radius: 999px;
            margin-bottom: .875rem;
        }
        .section-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(1.6rem, 3.5vw, 2.25rem);
            font-weight: 800;
            color: var(--ink);
            letter-spacing: -.03em;
            line-height: 1.15;
        }
        .section-divider {
            width: 48px; height: 4px;
            background: linear-gradient(90deg, var(--teal), #5eead4);
            border-radius: 999px;
            margin: 1rem 0;
        }

        /* ── About Card ── */
        .about-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 1.25rem;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: transform .25s, box-shadow .25s;
            position: relative; overflow: hidden;
        }
        .about-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 4px;
            background: var(--gradient, linear-gradient(90deg, var(--teal), #14b8a6));
            transform: scaleX(0); transform-origin: left;
            transition: transform .25s;
        }
        .about-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,0,0,.1); }
        .about-card:hover::before { transform: scaleX(1); }
        .about-icon {
            width: 72px; height: 72px;
            border-radius: 1.125rem;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 1.75rem;
        }

        /* ── Program Card ── */
        .prog-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 1.125rem;
            padding: 1.625rem;
            transition: transform .2s, box-shadow .2s, border-color .2s;
            display: flex; flex-direction: column; gap: .75rem;
            position: relative; overflow: hidden;
        }
        .prog-card::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 3px;
            background: var(--accent, var(--teal));
            transform: scaleX(0); transform-origin: left;
            transition: transform .25s;
        }
        .prog-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.1); border-color: rgba(13,148,136,.3); }
        .prog-card:hover::after { transform: scaleX(1); }
        .prog-icon-wrap {
            width: 52px; height: 52px;
            border-radius: .875rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; color: #fff;
            flex-shrink: 0;
        }
        .prog-tag {
            display: inline-flex; align-items: center; gap: .3rem;
            background: #f1f5f9; color: #475569;
            font-size: .72rem; font-weight: 600;
            padding: .2rem .6rem; border-radius: 999px;
        }

        /* ── Contact Card ── */
        .contact-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 1.25rem;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: transform .2s, box-shadow .2s;
        }
        .contact-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.08); }

        /* ── CTA Section ── */
        .cta-bg {
            background:
                radial-gradient(ellipse at 20% 50%, rgba(13,148,136,.3) 0%, transparent 50%),
                linear-gradient(135deg, #071330 0%, #0b1f4b 60%, #0d3d3a 100%);
        }

        /* ── Footer ── */
        .footer-bg { background: #f8fafc; border-top: 1px solid var(--line); }

        /* ── WhatsApp Float ── */
        .wa-float {
            position: fixed; width: 58px; height: 58px;
            bottom: 28px; right: 28px;
            background: #25D366;
            color: #fff; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
            box-shadow: 0 4px 18px rgba(37,211,102,.45);
            cursor: pointer; z-index: 99999;
            animation: waPulse 2.5s infinite;
            transition: transform .2s;
        }
        .wa-float:hover { transform: scale(1.08); }
        @keyframes waPulse {
            0%, 100% { box-shadow: 0 4px 18px rgba(37,211,102,.45); }
            50% { box-shadow: 0 4px 30px rgba(37,211,102,.7); }
        }
        .wa-popup {
            position: fixed; bottom: 106px; right: 28px; width: 320px;
            background: #fff; border-radius: 1.25rem;
            box-shadow: 0 12px 40px rgba(0,0,0,.18);
            z-index: 99999; display: none; overflow: hidden;
            animation: slideUp .3s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .wa-header {
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: #fff; padding: 1.125rem 1.25rem;
            position: relative;
        }
        .wa-header strong { font-size: 1rem; font-weight: 700; }
        .wa-header p { font-size: .78rem; opacity: .85; margin-top: .2rem; }
        .wa-close {
            position: absolute; top: .75rem; right: 1rem;
            font-size: 1.25rem; cursor: pointer; opacity: .8;
            transition: opacity .15s;
        }
        .wa-close:hover { opacity: 1; }
        .wa-body { padding: 1rem; }
        .wa-contact {
            display: flex; align-items: center; gap: .875rem;
            background: #f8fafc; border: 1px solid #e2e8f0;
            padding: .875rem 1rem; border-radius: .875rem;
            margin-bottom: .625rem; cursor: pointer;
            transition: background .15s, border-color .15s;
        }
        .wa-contact:last-child { margin-bottom: 0; }
        .wa-contact:hover { background: #f0fdf4; border-color: #bbf7d0; }
        .wa-img {
            width: 44px; height: 44px; border-radius: 50%;
            background: linear-gradient(135deg, #25D366, #128C7E);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.3rem; flex-shrink: 0;
        }
        .wa-name { font-size: .875rem; font-weight: 700; color: #0f172a; }
        .wa-status { font-size: .72rem; color: #64748b; margin-top: .1rem; }
        @media (min-width: 768px) { .wa-mobile-only { display: none !important; } }

        /* ── Animate on scroll ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .reveal { opacity: 0; }
        .reveal.visible { animation: fadeUp .55s ease both; }
        .reveal-1.visible { animation-delay: .05s; }
        .reveal-2.visible { animation-delay: .12s; }
        .reveal-3.visible { animation-delay: .19s; }
        .reveal-4.visible { animation-delay: .26s; }
        .reveal-5.visible { animation-delay: .33s; }
        .reveal-6.visible { animation-delay: .40s; }

        /* ── Mobile menu ── */
        #mobile-menu { display: none; }
        #mobile-menu.open { display: block; }

        /* ── Program show/hide ── */
        .prog-hidden { display: none !important; }
        .prog-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,.08);
            border-color: rgba(13,148,136,.3) !important;
        }
        #btn-show-more:hover {
            background: #0d9488 !important;
            color: #fff !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(13,148,136,.25);
        }
    </style>
</head>
<body class="antialiased">

<!-- Scroll Progress -->
<div id="scroll-bar"></div>

@php
use Illuminate\Support\Facades\DB;

// ── Stats: langsung dari koneksi Laravel (.env) ──
$totalParticipants = DB::table('participants')->where('status', 'active')->count();
$totalOngoing      = DB::table('programs')->where('status', 'ongoing')->count();
$totalCompleted    = DB::table('programs')->where('status', 'completed')->count();

// Sertifikat: coba filter issued, fallback ke semua
try {
    $totalSertifikat = DB::table('certificates')->where('status', 'issued')->count();
    if ($totalSertifikat === 0) {
        $totalSertifikat = DB::table('certificates')->count();
    }
} catch (\Exception $e) { $totalSertifikat = 0; }

// ── Master Programs: semua dari tabel master_programs ──
try {
    $masterPrograms = DB::table('master_programs as mp')
        ->leftJoin(
            DB::raw('(SELECT master_program_id,
                              COUNT(*) AS total_instances,
                              SUM(CASE WHEN status = "ongoing" THEN 1 ELSE 0 END) AS active_instances
                       FROM programs
                       GROUP BY master_program_id) AS pg'),
            'mp.id', '=', 'pg.master_program_id'
        )
        ->select(
            'mp.id',
            'mp.name',
            'mp.description',
            DB::raw('COALESCE(pg.total_instances, 0) AS total_instances'),
            DB::raw('COALESCE(pg.active_instances, 0) AS active_instances')
        )
        ->orderBy('mp.name')
        ->get()
        ->map(fn($r) => (array) $r)
        ->toArray();
} catch (\Exception $e) {
    // Fallback sederhana jika join gagal
    try {
        $masterPrograms = DB::table('master_programs')
            ->orderBy('name')
            ->get()
            ->map(fn($r) => (array) $r + ['total_instances'=>0,'active_instances'=>0])
            ->toArray();
    } catch (\Exception $e2) { $masterPrograms = []; }
}

// ── Palet warna untuk card program (cycling) ──
$palette = [
    ['accent'=>'#0d9488','grad'=>'linear-gradient(135deg,#0d9488,#14b8a6)','icon'=>'fas fa-book-open'],
    ['accent'=>'#2563eb','grad'=>'linear-gradient(135deg,#2563eb,#60a5fa)','icon'=>'fas fa-laptop-code'],
    ['accent'=>'#7c3aed','grad'=>'linear-gradient(135deg,#7c3aed,#a78bfa)','icon'=>'fas fa-cogs'],
    ['accent'=>'#06b6d4','grad'=>'linear-gradient(135deg,#06b6d4,#22d3ee)','icon'=>'fas fa-paint-brush'],
    ['accent'=>'#d97706','grad'=>'linear-gradient(135deg,#d97706,#fbbf24)','icon'=>'fas fa-chart-line'],
    ['accent'=>'#0f766e','grad'=>'linear-gradient(135deg,#0f766e,#0d9488)','icon'=>'fas fa-database'],
    ['accent'=>'#6366f1','grad'=>'linear-gradient(135deg,#6366f1,#818cf8)','icon'=>'fas fa-mobile-alt'],
    ['accent'=>'#dc2626','grad'=>'linear-gradient(135deg,#dc2626,#f87171)','icon'=>'fas fa-tools'],
    ['accent'=>'#059669','grad'=>'linear-gradient(135deg,#059669,#34d399)','icon'=>'fas fa-leaf'],
    ['accent'=>'#9333ea','grad'=>'linear-gradient(135deg,#9333ea,#c084fc)','icon'=>'fas fa-camera'],
];
$revealClasses = ['reveal-1','reveal-2','reveal-3','reveal-4','reveal-5','reveal-6'];
@endphp

<!-- ══════════════════ NAVBAR ══════════════════ -->
<nav id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="#home" class="flex items-center gap-3 flex-shrink-0">
                <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjz9kmrBxqmtTcjR5DfGbcL0blmphH6V9chaKj5rJHWVW59vuEbp8OvusBJxR79eKcNEjUIstpT4gjQbVUSA5LgemfC5oy5hZgzsqxw8O3pg-064l2YToAxL9E2ljEPBHU05J_2Cl8roOI/s705/logo_blk_biru.png.png"
                     alt="Logo BPVP" class="h-11 w-auto">
            </a>

            <!-- Desktop Nav Links -->
            <div class="hidden md:flex items-center gap-1">
                <a href="#home"     class="nav-link">Beranda</a>
                <a href="#about"    class="nav-link">Tentang</a>
                <a href="#programs" class="nav-link">Program</a>
                <a href="#contact"  class="nav-link">Kontak</a>
            </div>

            <!-- CTA Buttons -->
            <div class="hidden md:flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="nav-link" style="font-weight:700;">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary">
                        <i class="fas fa-user-plus" style="font-size:.8rem;"></i> Daftar
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn-primary">
                        <i class="fas fa-th-large" style="font-size:.8rem;"></i> Dashboard
                    </a>
                @endguest
            </div>

            <!-- Mobile Hamburger -->
            <button id="hamburger" class="md:hidden text-white p-2 rounded-lg transition" onclick="toggleMenu()">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden pb-4">
            <div class="flex flex-col gap-1 bg-white/10 backdrop-blur-md rounded-xl p-3 mt-2">
                <a href="#home"     class="nav-link text-white" onclick="toggleMenu()">Beranda</a>
                <a href="#about"    class="nav-link text-white" onclick="toggleMenu()">Tentang</a>
                <a href="#programs" class="nav-link text-white" onclick="toggleMenu()">Program</a>
                <a href="#contact"  class="nav-link text-white" onclick="toggleMenu()">Kontak</a>
                <div class="mt-2 flex gap-2">
                    @guest
                        <a href="{{ route('login') }}"    class="btn-outline flex-1 justify-center">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary flex-1 justify-center">Daftar</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn-primary flex-1 justify-center">Dashboard</a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- ══════════════════ HERO ══════════════════ -->
<section id="home" class="hero-bg flex items-center min-h-screen pt-20">
    <div class="hero-grid"></div>
    <!-- Decorative blobs -->
    <div class="hero-blob" style="width:500px;height:500px;background:#0d9488;top:-100px;right:-100px;"></div>
    <div class="hero-blob" style="width:300px;height:300px;background:#1e40af;bottom:-80px;left:-80px;"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 w-full" style="position:relative;z-index:1;">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <!-- Left: Copy -->
            <div>
                <div class="section-eyebrow" style="background:rgba(255,255,255,.12); color:rgba(255,255,255,.85);">
                    <span style="width:7px;height:7px;border-radius:50%;background:#5eead4;display:inline-block;"></span>
                    Balai Pelatihan Vokasi dan Produktivitas Banda Aceh
                </div>
                <h1 class="font-display" style="font-size:clamp(2rem,5vw,3.2rem);font-weight:800;color:#fff;letter-spacing:-.03em;line-height:1.1;margin-bottom:1.25rem;">
                    Tingkatkan Kompetensi<br>
                    <span style="color:#5eead4;">Raih Karir Impian</span><br>
                    di Aceh
                </h1>
                <p style="color:rgba(255,255,255,.65);font-size:1.05rem;line-height:1.75;max-width:480px;margin-bottom:2rem;">
                    Platform pelatihan vokasi profesional untuk meningkatkan keterampilan kerja sesuai kebutuhan industri modern di Aceh dan sekitarnya.
                </p>
                <div style="display:flex;gap:.875rem;flex-wrap:wrap;">
                    <a href="{{ route('login') }}" class="btn-primary" style="font-size:.95rem;padding:.75rem 1.75rem;">
                        <i class="fas fa-rocket"></i> Mulai Sekarang
                    </a>
                    <a href="#programs" class="btn-outline" style="font-size:.95rem;padding:.75rem 1.75rem;">
                        <i class="fas fa-compass"></i> Lihat Program
                    </a>
                </div>

                <!-- Trust Badges -->
                <div style="display:flex;align-items:center;gap:1.5rem;margin-top:2.5rem;padding-top:2rem;border-top:1px solid rgba(255,255,255,.1);">
                    <div style="text-align:center;">
                        <div style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:#5eead4;">BNSP</div>
                        <div style="font-size:.7rem;color:rgba(255,255,255,.5);margin-top:.15rem;">Bersertifikat</div>
                    </div>
                    <div style="width:1px;height:36px;background:rgba(255,255,255,.15);"></div>
                    <div style="text-align:center;">
                        <div style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:#fbbf24;">Kemnaker</div>
                        <div style="font-size:.7rem;color:rgba(255,255,255,.5);margin-top:.15rem;">Terakreditasi</div>
                    </div>
                    <div style="width:1px;height:36px;background:rgba(255,255,255,.15);"></div>
                    <div style="text-align:center;">
                        <div style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:#93c5fd;">Gratis</div>
                        <div style="font-size:.7rem;color:rgba(255,255,255,.5);margin-top:.15rem;">Pelatihan</div>
                    </div>
                </div>
            </div>

            <!-- Right: Stats Float Card -->
            <div class="hidden lg:block">
                <div class="stat-float">
                    <div style="background:rgba(255,255,255,.07);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.14);border-radius:1.5rem;padding:2rem;">
                        <!-- Top Row -->
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                            <div class="stat-item" style="background:rgba(13,148,136,.18);border-color:rgba(13,148,136,.3);">
                                <div style="color:#5eead4;font-size:1.5rem;margin-bottom:.5rem;"><i class="fas fa-users"></i></div>
                                <div class="stat-num-hero">{{ number_format($totalParticipants) }}</div>
                                <div class="stat-label-hero">Peserta Aktif</div>
                            </div>
                            <div class="stat-item" style="background:rgba(37,99,235,.18);border-color:rgba(37,99,235,.3);">
                                <div style="color:#93c5fd;font-size:1.5rem;margin-bottom:.5rem;"><i class="fas fa-play-circle"></i></div>
                                <div class="stat-num-hero">{{ $totalOngoing }}</div>
                                <div class="stat-label-hero">Sedang Berjalan</div>
                            </div>
                        </div>
                        <!-- Bottom Row -->
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                            <div class="stat-item" style="background:rgba(16,185,129,.18);border-color:rgba(16,185,129,.3);">
                                <div style="color:#6ee7b7;font-size:1.5rem;margin-bottom:.5rem;"><i class="fas fa-check-circle"></i></div>
                                <div class="stat-num-hero">{{ $totalCompleted }}</div>
                                <div class="stat-label-hero">Sudah Selesai</div>
                            </div>
                            <div class="stat-item" style="background:rgba(245,158,11,.18);border-color:rgba(245,158,11,.3);">
                                <div style="color:#fcd34d;font-size:1.5rem;margin-bottom:.5rem;"><i class="fas fa-certificate"></i></div>
                                <div class="stat-num-hero">{{ number_format($totalSertifikat) }}+</div>
                                <div class="stat-label-hero">Sertifikat Terbit</div>
                            </div>
                        </div>
                        <!-- Bottom Note -->
                        <div style="margin-top:1.25rem;padding:.875rem 1rem;background:rgba(255,255,255,.05);border-radius:.875rem;text-align:center;">
                            <p style="font-size:.75rem;color:rgba(255,255,255,.5);">Data real-time dari sistem BPVP Banda Aceh</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom wave -->
    <div style="position:absolute;bottom:0;left:0;right:0;overflow:hidden;line-height:0;">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;height:60px;display:block;">
            <path d="M0 60L60 50C120 40 240 20 360 15C480 10 600 20 720 25C840 30 960 30 1080 25C1200 20 1320 10 1380 5L1440 0V60H0Z" fill="#f8fafc"/>
        </svg>
    </div>
</section>

<!-- ══════════════════ ABOUT ══════════════════ -->
<section id="about" style="background:#f8fafc;padding:6rem 0;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center reveal reveal-1" style="margin-bottom:3.5rem;">
            <div class="section-eyebrow" style="justify-content:center;">
                <i class="fas fa-info-circle" style="font-size:.75rem;"></i> Tentang Kami
            </div>
            <h2 class="section-title">Tentang BPVP Banda Aceh</h2>
            <div class="section-divider" style="margin:1rem auto;"></div>
            <p style="color:var(--muted);max-width:520px;margin:0 auto;font-size:.95rem;line-height:1.8;">
                Kami hadir untuk mencetak tenaga kerja kompeten yang siap bersaing di era industri modern.
            </p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">

            <!-- Visi -->
            <div class="about-card reveal reveal-1 h-full flex flex-col p-8" style="text-align:left;">
                <div class="about-icon mx-auto mb-6" style="background:#ccfbf1; width:80px; height:80px;">
                    <i class="fas fa-bullseye text-5xl" style="color:#0f766e;"></i>
                </div>
                <h3 class="text-2xl font-bold text-center mb-6">Visi Kami</h3>
                <div class="flex-1 bg-[#f1f5f9] rounded-3xl p-8 border-l-8 border-[#14b8a6] flex items-center">
                    <p class="italic text-center text-[1.1rem] leading-relaxed text-gray-700">
                        “Indonesia Maju Yang Berdaulat, Mandiri dan Berkepribadian Berlandaskan Gotong Royong”
                    </p>
                </div>
            </div>

            <!-- Misi -->
            <div class="about-card reveal reveal-2 h-full flex flex-col p-8" style="text-align:left;">
                <div class="about-icon mx-auto mb-6" style="background:#dbeafe; width:80px; height:80px;">
                    <i class="fas fa-flag text-5xl" style="color:#1d4ed8;"></i>
                </div>
                <h3 class="text-2xl font-bold text-center mb-6">Misi Kami</h3>
                <ul class="flex-1 space-y-4 text-[0.98rem] text-gray-700">
                    <li class="flex gap-3 items-start"><span class="text-emerald-600 text-2xl leading-none mt-1">✔</span> Peningkatan Kualitas Manusia Indonesia;</li>
                    <li class="flex gap-3 items-start"><span class="text-emerald-600 text-2xl leading-none mt-1">✔</span> Struktur Ekonomi Yang Produktif, Mandiri dan Berdaya Saing;</li>
                    <li class="flex gap-3 items-start"><span class="text-emerald-600 text-2xl leading-none mt-1">✔</span> Pembangunan Yang Merata dan Berkeadilan;</li>
                    <li class="flex gap-3 items-start"><span class="text-emerald-600 text-2xl leading-none mt-1">✔</span> Perlindungan Bagi Segenap Bangsa dan Memberikan Rasa Aman Pada Seluruh Warga;</li>
                    <li class="flex gap-3 items-start"><span class="text-emerald-600 text-2xl leading-none mt-1">✔</span> Pengelolaan Pemerintahan Yang Bersih, Efektif dan Terpercaya;</li>
                    <li class="flex gap-3 items-start"><span class="text-emerald-600 text-2xl leading-none mt-1">✔</span> Sinergi Pemerintah Daerah Dalam Kerangka negara Kesatuan.</li>
                </ul>
            </div>

            <!-- Keunggulan -->
            <div class="about-card reveal reveal-3 h-full flex flex-col p-8" style="text-align:left;">
                <div class="about-icon mx-auto mb-6" style="background:#ede9fe; width:80px; height:80px;">
                    <i class="fas fa-award text-5xl" style="color:#6d28d9;"></i>
                </div>
                <h3 class="text-2xl font-bold text-center mb-6">Keunggulan Kami</h3>
                <ul class="flex-1 space-y-4 text-[0.98rem] text-gray-700">
                    <li class="flex gap-3 items-start"><span class="text-violet-600 text-2xl leading-none mt-1">★</span> Sertifikat resmi BNSP</li>
                    <li class="flex gap-3 items-start"><span class="text-violet-600 text-2xl leading-none mt-1">★</span> Fasilitas pelatihan modern</li>
                    <li class="flex gap-3 items-start"><span class="text-violet-600 text-2xl leading-none mt-1">★</span> Instruktur bersertifikat dan berpengalaman</li>
                    <li class="flex gap-3 items-start"><span class="text-violet-600 text-2xl leading-none mt-1">★</span> Jaringan industri luas untuk penyaluran kerja</li>
                    <li class="flex gap-3 items-start"><span class="text-violet-600 text-2xl leading-none mt-1">★</span> Program pelatihan sesuai kebutuhan industri</li>
                </ul>
            </div>

        </div>
    </div>
</section>

<!-- ══════════════════ PROGRAMS ══════════════════ -->
<section id="programs" style="background:#f8fafc;padding:6rem 0;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="text-center reveal reveal-1" style="margin-bottom:2.75rem;">
            <div class="section-eyebrow" style="justify-content:center;">
                <i class="fas fa-graduation-cap" style="font-size:.75rem;"></i> Program Unggulan
            </div>
            <h2 class="section-title">Program Pelatihan</h2>
            <div class="section-divider" style="margin:1rem auto;"></div>
            <p style="color:var(--muted);max-width:520px;margin:0 auto;font-size:.95rem;line-height:1.8;">
                Berbagai program pelatihan vokasi yang dirancang sesuai kebutuhan industri modern
            </p>
            @if(!empty($masterPrograms))
            <div style="margin-top:1rem;">
                <span style="display:inline-flex;align-items:center;gap:.4rem;background:#ccfbf1;color:#0f766e;font-size:.75rem;font-weight:700;padding:.3rem .875rem;border-radius:999px;">
                    <i class="fas fa-layer-group" style="font-size:.65rem;"></i>
                    {{ count($masterPrograms) }} Total Program Tersedia
                </span>
            </div>
            @endif
        </div>

        @if(empty($masterPrograms))
        <div style="text-align:center;padding:3rem 1rem;color:var(--muted);">
            <i class="fas fa-folder-open" style="font-size:3rem;color:#cbd5e1;margin-bottom:1rem;display:block;"></i>
            <p style="font-size:.95rem;">Belum ada program pelatihan tersedia.</p>
        </div>
        @else

        <!-- Program Grid — 6 tampil awal, sisanya disembunyikan -->
        <div id="prog-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;">
        @foreach($masterPrograms as $idx => $mp)
            @php
                $p      = $palette[$idx % count($palette)];
                $rev    = $revealClasses[$idx % count($revealClasses)];
                $dur    = !empty($mp['duration']) ? $mp['duration'] : '';
                $hidden = $idx >= 6 ? 'prog-hidden' : '';
            @endphp
            <div class="prog-item {{ $hidden }} reveal {{ $rev }}"
                 style="background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:1.375rem 1.25rem;
                        display:flex;flex-direction:column;gap:.75rem;position:relative;overflow:hidden;
                        transition:transform .2s,box-shadow .2s,border-color .2s;">
                <!-- Accent bar top -->
                <div style="position:absolute;top:0;left:0;right:0;height:3px;background:{{ $p['grad'] }};border-radius:1rem 1rem 0 0;"></div>

                <!-- Header row -->
                <div style="display:flex;align-items:center;gap:.875rem;">
                    <div style="width:44px;height:44px;border-radius:.75rem;background:{{ $p['grad'] }};
                                display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;flex-shrink:0;">
                        <i class="{{ $p['icon'] }}"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <h3 style="font-family:'Syne',sans-serif;font-size:.9rem;font-weight:800;
                                   color:#0f172a;line-height:1.3;
                                   white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                            title="{{ $mp['name'] }}">
                            {{ $mp['name'] }}
                        </h3>
                        <!-- Badges row -->
                        <div style="display:flex;align-items:center;gap:.375rem;flex-wrap:wrap;margin-top:.3rem;">
                            @if($dur)
                            <span style="display:inline-flex;align-items:center;gap:.25rem;background:#f1f5f9;color:#475569;
                                         font-size:.68rem;font-weight:600;padding:.18rem .55rem;border-radius:999px;">
                                <i class="far fa-clock" style="font-size:.6rem;"></i> {{ $dur }}
                            </span>
                            @endif
                            @if(!empty($mp['total_instances']) && $mp['total_instances'] > 0)
                            <span style="display:inline-flex;align-items:center;gap:.25rem;background:#f0fdf4;color:#15803d;
                                         font-size:.68rem;font-weight:600;padding:.18rem .55rem;border-radius:999px;">
                                <i class="fas fa-layer-group" style="font-size:.58rem;"></i> {{ $mp['total_instances'] }} angkatan
                            </span>
                            @endif
                            @if(!empty($mp['active_instances']) && $mp['active_instances'] > 0)
                            <span style="display:inline-flex;align-items:center;gap:.25rem;background:#dcfce7;color:#15803d;
                                         font-size:.68rem;font-weight:700;padding:.18rem .55rem;border-radius:999px;">
                                ● Sedang Berjalan
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if(!empty($mp['description']))
                <p style="font-size:.8rem;color:#64748b;line-height:1.7;
                           display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
                           margin:0;padding:0;">
                    {{ $mp['description'] }}
                </p>
                @endif
            </div>
        @endforeach
        </div>

        <!-- Show More / Show Less button (hanya tampil jika > 6 program) -->
        @if(count($masterPrograms) > 6)
        <!-- GANTI BUTTON INI -->
<div style="text-align:center;margin-top:2.25rem;">
    <a href="{{ route('programs.public') }}" 
   class="inline-flex items-center gap-3 bg-teal-600 hover:bg-teal-700 text-white font-bold px-8 py-4 rounded-2xl transition-all">
    Lihat Semua Program 
    <span class="text-xl">→</span>
</a>
</div>
        @endif

        @endif
    </div>
</section>

<!-- ══════════════════ CTA ══════════════════ -->
<section class="cta-bg" style="padding:6rem 0;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:40px 40px;pointer-events:none;"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal reveal-1" style="position:relative;z-index:1;">
        <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);border-radius:999px;padding:.3rem .875rem;font-size:.72rem;font-weight:700;color:rgba(255,255,255,.8);letter-spacing:.06em;text-transform:uppercase;margin-bottom:1.25rem;">
            <span style="width:7px;height:7px;border-radius:50%;background:#5eead4;display:inline-block;"></span>
            Bergabunglah Sekarang
        </div>
        <h2 class="font-display" style="font-size:clamp(1.75rem,4vw,2.75rem);font-weight:800;color:#fff;letter-spacing:-.03em;line-height:1.15;margin-bottom:1.25rem;">
            Siap Tingkatkan Skill<br>& Raih Peluang Karir?
        </h2>
        <p style="color:rgba(255,255,255,.6);font-size:1rem;line-height:1.8;max-width:500px;margin:0 auto 2.5rem;">
            Bergabunglah dengan ribuan alumni BPVP Banda Aceh dan mulai perjalanan karir Anda bersama kami.
        </p>
        <div style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap;">
            <a href="{{ route('register') }}" class="btn-white">
                <i class="fas fa-user-plus"></i> Daftar Sekarang — Gratis
            </a>
            <a href="#contact" class="btn-outline" style="padding:.75rem 1.75rem;font-size:1rem;">
                <i class="fas fa-headset"></i> Hubungi Kami
            </a>
        </div>
    </div>
</section>

<!-- ══════════════════ CONTACT ══════════════════ -->
<section id="contact" style="background:#f8fafc;padding:6rem 0;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center reveal reveal-1" style="margin-bottom:3.5rem;">
            <div class="section-eyebrow" style="justify-content:center;">
                <i class="fas fa-envelope" style="font-size:.75rem;"></i> Kontak
            </div>
            <h2 class="section-title">Hubungi Kami</h2>
            <div class="section-divider" style="margin:1rem auto;"></div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.5rem;">
            <div class="contact-card reveal reveal-1">
                <div style="width:60px;height:60px;background:linear-gradient(135deg,#0b1f4b,#1e40af);border-radius:1rem;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;font-size:1.4rem;color:#fff;">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3 style="font-family:'Syne',sans-serif;font-weight:800;color:var(--ink);font-size:1.05rem;margin-bottom:.625rem;">Alamat</h3>
                <p style="color:var(--muted);font-size:.875rem;line-height:1.75;">
                    Jl. Kesatria Geuceu Komplek<br>Kec. Banda Raya, Kota Banda Aceh<br>
                    <span style="font-size:.78rem;color:#94a3b8;">Senin – Jum'at · 08.00 – 16.00</span>
                </p>
            </div>

            <div class="contact-card reveal reveal-2">
                <div style="width:60px;height:60px;background:linear-gradient(135deg,#0d9488,#14b8a6);border-radius:1rem;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;font-size:1.4rem;color:#fff;">
                    <i class="fas fa-phone"></i>
                </div>
                <h3 style="font-family:'Syne',sans-serif;font-weight:800;color:var(--ink);font-size:1.05rem;margin-bottom:.625rem;">Telepon</h3>
                <p style="color:var(--muted);font-size:.875rem;line-height:1.75;">
                    (0651) 45298<br>0812-3456-7890
                </p>
            </div>

            <div class="contact-card reveal reveal-3">
                <div style="width:60px;height:60px;background:linear-gradient(135deg,#6366f1,#818cf8);border-radius:1rem;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;font-size:1.4rem;color:#fff;">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3 style="font-family:'Syne',sans-serif;font-weight:800;color:var(--ink);font-size:1.05rem;margin-bottom:.625rem;">Email</h3>
                <p style="color:var(--muted);font-size:.875rem;line-height:1.75;">
                    blkbandaaceh@kemnaker.go.id<br>admin@blkbandaaceh.ac.id
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════ FOOTER ══════════════════ -->
<footer class="footer-bg" style="padding:3.5rem 0 2rem;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:2.5rem;margin-bottom:3rem;">
            <!-- Brand -->
            <div>
                <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjz9kmrBxqmtTcjR5DfGbcL0blmphH6V9chaKj5rJHWVW59vuEbp8OvusBJxR79eKcNEjUIstpT4gjQbVUSA5LgemfC5oy5hZgzsqxw8O3pg-064l2YToAxL9E2ljEPBHU05J_2Cl8roOI/s705/logo_blk_biru.png.png"
                     alt="Logo BPVP" style="height:44px;width:auto;margin-bottom:1rem;">
                <p style="font-size:.83rem;color:var(--muted);line-height:1.75;">
                    Balai Latihan Kerja terpercaya untuk mengembangkan skill dan kompetensi profesional di Aceh.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 style="font-family:'Syne',sans-serif;font-weight:800;font-size:.9rem;color:var(--ink);margin-bottom:1rem;">Navigasi</h4>
                <ul style="display:flex;flex-direction:column;gap:.5rem;list-style:none;">
                    <li><a href="#home"     style="font-size:.83rem;color:var(--muted);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='#0d9488'" onmouseout="this.style.color='#64748b'">Beranda</a></li>
                    <li><a href="#about"    style="font-size:.83rem;color:var(--muted);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='#0d9488'" onmouseout="this.style.color='#64748b'">Tentang</a></li>
                    <li><a href="#programs" style="font-size:.83rem;color:var(--muted);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='#0d9488'" onmouseout="this.style.color='#64748b'">Program</a></li>
                    <li><a href="#contact"  style="font-size:.83rem;color:var(--muted);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='#0d9488'" onmouseout="this.style.color='#64748b'">Kontak</a></li>
                </ul>
            </div>

            <!-- Layanan -->
            <div>
                <h4 style="font-family:'Syne',sans-serif;font-weight:800;font-size:.9rem;color:var(--ink);margin-bottom:1rem;">Layanan Pengaduan</h4>
                <ul style="display:flex;flex-direction:column;gap:.5rem;list-style:none;">
                    <li><a href="https://wbs.kemnaker.go.id" target="_blank" style="font-size:.83rem;color:var(--muted);text-decoration:none;" onmouseover="this.style.color='#0d9488'" onmouseout="this.style.color='#64748b'">Whistleblowing System</a></li>
                    <li><a href="https://www.lapor.go.id"    target="_blank" style="font-size:.83rem;color:var(--muted);text-decoration:none;" onmouseover="this.style.color='#0d9488'" onmouseout="this.style.color='#64748b'">Lapor.go.id</a></li>
                </ul>
            </div>

            <!-- Social -->
            <div>
                <h4 style="font-family:'Syne',sans-serif;font-weight:800;font-size:.9rem;color:var(--ink);margin-bottom:1rem;">Media Sosial</h4>
                <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                    <a href="#"                                        target="_blank" style="width:36px;height:36px;border-radius:.5rem;background:#eff6ff;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:1rem;transition:background .15s,transform .15s;" onmouseover="this.style.background='#dbeafe';this.style.transform='scale(1.1)'" onmouseout="this.style.background='#eff6ff';this.style.transform='scale(1)'"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://x.com/bpvp.bandaaceh"            target="_blank" style="width:36px;height:36px;border-radius:.5rem;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#0f172a;font-size:1rem;transition:background .15s,transform .15s;" onmouseover="this.style.background='#e2e8f0';this.style.transform='scale(1.1)'" onmouseout="this.style.background='#f8fafc';this.style.transform='scale(1)'"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://www.instagram.com/blkbandaceh"   target="_blank" style="width:36px;height:36px;border-radius:.5rem;background:#fdf2f8;display:flex;align-items:center;justify-content:center;color:#ec4899;font-size:1rem;transition:background .15s,transform .15s;" onmouseover="this.style.background='#fce7f3';this.style.transform='scale(1.1)'" onmouseout="this.style.background='#fdf2f8';this.style.transform='scale(1)'"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.youtube.com/bpvpbandaaceh"   target="_blank" style="width:36px;height:36px;border-radius:.5rem;background:#fff1f0;display:flex;align-items:center;justify-content:center;color:#ef4444;font-size:1rem;transition:background .15s,transform .15s;" onmouseover="this.style.background='#fee2e2';this.style.transform='scale(1.1)'" onmouseout="this.style.background='#fff1f0';this.style.transform='scale(1)'"><i class="fab fa-youtube"></i></a>
                    <a href="#"                                        target="_blank" style="width:36px;height:36px;border-radius:.5rem;background:#eff6ff;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:1rem;transition:background .15s,transform .15s;" onmouseover="this.style.background='#dbeafe';this.style.transform='scale(1.1)'" onmouseout="this.style.background='#eff6ff';this.style.transform='scale(1)'"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"                                        target="_blank" style="width:36px;height:36px;border-radius:.5rem;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#0f172a;font-size:1rem;transition:background .15s,transform .15s;" onmouseover="this.style.background='#e2e8f0';this.style.transform='scale(1.1)'" onmouseout="this.style.background='#f8fafc';this.style.transform='scale(1)'"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <div style="border-top:1px solid var(--line);padding-top:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <p style="font-size:.78rem;color:#94a3b8;">© 2025 Balai Latihan Kerja Banda Aceh. All rights reserved.</p>
            <p style="font-size:.78rem;color:#cbd5e1;">Kementerian Ketenagakerjaan Republik Indonesia</p>
        </div>
    </div>
</footer>

<!-- ══════════════════ WHATSAPP FLOAT ══════════════════ -->
<div class="wa-float" id="waBtn">
    <i class="fab fa-whatsapp"></i>
</div>
<div class="wa-popup" id="waPopup">
    <div class="wa-header">
        <strong>💬 Respon Cepat</strong>
        <span class="wa-close" id="waClose">×</span>
        <p>Pilih admin untuk menghubungi via WhatsApp</p>
    </div>
    <div class="wa-body">
        <div class="wa-contact" onclick="openWa('6281269334494','Halo admin, saya ingin bertanya tentang program pelatihan...')">
            <div class="wa-img"><i class="fab fa-whatsapp"></i></div>
            <div>
                <div class="wa-name">Admin BPVP Aceh</div>
                <div class="wa-status">🟢 Online</div>
            </div>
        </div>
        <div class="wa-contact" onclick="openWa('6282222222222','Halo admin pelatihan, saya ingin info lebih lanjut...')">
            <div class="wa-img"><i class="fab fa-whatsapp"></i></div>
            <div>
                <div class="wa-name">Admin Pelatihan</div>
                <div class="wa-status">⏱ Balas dalam 10 menit</div>
            </div>
        </div>
        <div class="wa-contact wa-mobile-only" onclick="openWa('6283333333333','Halo, saya ingin bertanya...')">
            <div class="wa-img"><i class="fab fa-whatsapp"></i></div>
            <div>
                <div class="wa-name">Admin Mobile</div>
                <div class="wa-status">Khusus pengguna HP</div>
            </div>
        </div>
    </div>
</div>

<script>
// ── Scroll Progress Bar ──
window.addEventListener('scroll', () => {
    const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
    document.getElementById('scroll-bar').style.width = scrolled + '%';

    // Navbar style on scroll
    const nav = document.getElementById('navbar');
    if (window.scrollY > 60) {
        nav.classList.add('scrolled');
        document.getElementById('hamburger').style.color = '#0f172a';
    } else {
        nav.classList.remove('scrolled');
        document.getElementById('hamburger').style.color = '#fff';
    }
});

// ── Mobile Menu Toggle ──
function toggleMenu() {
    const m = document.getElementById('mobile-menu');
    m.classList.toggle('open');
}

// ── Smooth Scroll ──
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const t = document.querySelector(a.getAttribute('href'));
        if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});

// ── Reveal on Scroll ──
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); } });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// ── Program Show More / Less ──
let programsExpanded = false;
function togglePrograms() {
    programsExpanded = !programsExpanded;
    const hidden = document.querySelectorAll('.prog-hidden, .prog-item[data-hidden]');
    const items  = document.querySelectorAll('.prog-item');
    const btn    = document.getElementById('btn-show-more');
    const icon   = document.getElementById('btn-icon');
    const text   = document.getElementById('btn-text');
    const total  = items.length;

    items.forEach((el, i) => {
        if (i >= 6) {
            if (programsExpanded) {
                el.style.display = '';
                // Trigger reveal animation
                el.classList.remove('visible');
                setTimeout(() => el.classList.add('visible'), (i - 6) * 60);
            } else {
                el.style.display = 'none';
            }
        }
    });

    if (programsExpanded) {
        icon.style.transform = 'rotate(180deg)';
        text.textContent = 'Tampilkan Lebih Sedikit';
        btn.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        icon.style.transform = 'rotate(0deg)';
        text.textContent = 'Lihat Semua Program (' + (total - 6) + ' lainnya)';
        document.getElementById('programs').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Init: sembunyikan item >= 6 via JS (CSS class sebagai fallback)
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.prog-item').forEach((el, i) => {
        if (i >= 6) el.style.display = 'none';
    });
});
const waBtn = document.getElementById('waBtn');
const waPopup = document.getElementById('waPopup');
const waClose = document.getElementById('waClose');
waBtn.onclick = () => { waPopup.style.display = waPopup.style.display === 'block' ? 'none' : 'block'; };
waClose.onclick = () => { waPopup.style.display = 'none'; };
document.addEventListener('click', e => { if (!waBtn.contains(e.target) && !waPopup.contains(e.target)) waPopup.style.display = 'none'; });
setTimeout(() => { waPopup.style.display = 'block'; }, 4000);

function openWa(phone, text) {
    window.open(`https://wa.me/${phone}?text=${encodeURIComponent(text)}`, '_blank');
}
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Program Pelatihan - BPVP Banda Aceh</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo blk banda.png') }}">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, .code { font-family: 'DM Mono', monospace; }

        body {
            background: #f0f4f8;
            background-image:
                radial-gradient(at 20% 0%, rgba(13,148,136,0.08) 0px, transparent 50%),
                radial-gradient(at 80% 0%, rgba(6,95,70,0.06) 0px, transparent 50%);
        }

        /* ── Hero ── */
        .hero {
            background: linear-gradient(135deg, #0d4f3c 0%, #0f766e 40%, #0e7490 100%);
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 10% 80%, rgba(255,255,255,0.04) 0%, transparent 40%),
                radial-gradient(circle at 90% 20%, rgba(255,255,255,0.06) 0%, transparent 40%);
        }
        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .hero-glow {
            position: absolute;
            width: 600px; height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(20,184,166,0.25) 0%, transparent 70%);
            top: -200px; right: -100px;
            pointer-events: none;
        }

        /* ── Search Bar ── */
        .search-wrap {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 18px;
            transition: all 0.3s ease;
        }
        .search-wrap:focus-within {
            background: rgba(255,255,255,0.18);
            border-color: rgba(255,255,255,0.4);
            box-shadow: 0 0 0 4px rgba(20,184,166,0.25);
        }
        .search-input {
            background: transparent;
            border: none;
            outline: none;
            color: white;
            font-size: 15px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            width: 100%;
        }
        .search-input::placeholder { color: rgba(255,255,255,0.55); }
        .search-btn {
            background: linear-gradient(135deg, #14b8a6, #0891b2);
            border: none;
            border-radius: 12px;
            padding: 10px 22px;
            color: white;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: flex; align-items: center; gap: 8px;
        }
        .search-btn:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 20px rgba(20,184,166,0.5);
        }

        /* ── Stats pill ── */
        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 100px;
            padding: 6px 16px;
            font-size: 13px;
            color: rgba(255,255,255,0.85);
        }
        .stat-pill strong { color: #5eead4; font-weight: 700; font-size: 15px; }

        /* ── Cards ── */
        .prog-card {
            background: white;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            padding: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: fadeUp 0.5s ease both;
        }
        .prog-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #14b8a6, #0891b2);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
            border-radius: 0 0 20px 20px;
        }
        .prog-card:hover {
            transform: translateY(-6px);
            border-color: #99f6e4;
            box-shadow: 0 20px 40px rgba(13,148,136,0.12), 0 4px 12px rgba(0,0,0,0.06);
        }
        .prog-card:hover::after { transform: scaleX(1); }

        /* Staggered animation delays */
        .prog-card:nth-child(1)  { animation-delay: 0.05s; }
        .prog-card:nth-child(2)  { animation-delay: 0.10s; }
        .prog-card:nth-child(3)  { animation-delay: 0.15s; }
        .prog-card:nth-child(4)  { animation-delay: 0.20s; }
        .prog-card:nth-child(5)  { animation-delay: 0.25s; }
        .prog-card:nth-child(6)  { animation-delay: 0.30s; }
        .prog-card:nth-child(7)  { animation-delay: 0.35s; }
        .prog-card:nth-child(8)  { animation-delay: 0.40s; }
        .prog-card:nth-child(9)  { animation-delay: 0.45s; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Card icon ── */
        .card-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
            border: 1.5px solid #99f6e4;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }
        .prog-card:hover .card-icon {
            background: linear-gradient(135deg, #14b8a6, #0891b2);
            border-color: transparent;
            transform: rotate(-5deg) scale(1.08);
        }

        /* ── Code badge ── */
        .code-badge {
            display: inline-flex;
            align-items: center;
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            color: #0f766e;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 3px 10px;
            border-radius: 100px;
            font-family: 'DM Mono', monospace;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ── Pagination ── */
        .pagination-wrap nav { display: flex; justify-content: center; }
        .pagination-wrap .flex { gap: 6px; }
        .pagination-wrap span,
        .pagination-wrap a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px; height: 40px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            padding: 0 12px;
            transition: all 0.2s ease;
        }
        .pagination-wrap a {
            background: white;
            border: 1.5px solid #e2e8f0;
            color: #374151;
            text-decoration: none;
        }
        .pagination-wrap a:hover {
            background: #f0fdfa;
            border-color: #14b8a6;
            color: #0f766e;
        }
        .pagination-wrap span[aria-current="page"] {
            background: linear-gradient(135deg, #14b8a6, #0891b2);
            color: white;
            border: none;
        }
        .pagination-wrap span[aria-disabled="true"] {
            background: #f8fafc;
            color: #cbd5e1;
            border: 1.5px solid #f1f5f9;
            cursor: not-allowed;
        }

        /* ── Back link ── */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.75);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 100px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.08);
            transition: all 0.2s ease;
        }
        .back-link:hover {
            color: white;
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.35);
        }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 80px 24px;
            background: white;
            border-radius: 24px;
            border: 1.5px dashed #e2e8f0;
        }
        .empty-icon {
            width: 72px; height: 72px;
            background: #f0fdfa;
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            margin: 0 auto 20px;
        }

        /* ── Section label ── */
        .section-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
    </style>
</head>
<body>

<!-- ══════════════ HERO SECTION ══════════════ -->
<div class="hero relative">
    <div class="hero-grid"></div>
    <div class="hero-glow"></div>

    <div class="relative max-w-7xl mx-auto px-6 pt-12 pb-14">

        <!-- Top nav -->
        <div class="flex justify-between items-center mb-10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/15 border border-white/25 flex items-center justify-center text-lg">🎓</div>
                <span class="text-white/80 font-semibold text-sm tracking-wide">BPVP Banda Aceh</span>
            </div>
            <a href="/" class="back-link">
                <i class="fas fa-arrow-left text-xs"></i>
                Kembali ke Beranda
            </a>
        </div>

        <!-- Title -->
        <div class="mb-8">
            <div class="stat-pill mb-5">
                <i class="fas fa-layer-group text-xs"></i>
                <span>Katalog Program</span>
                <span>·</span>
                <strong>{{ $masterPrograms->total() }}</strong>
                <span>program tersedia</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight tracking-tight">
                Semua Program<br>
                <span class="text-transparent bg-clip-text" style="background-image: linear-gradient(90deg, #5eead4, #67e8f9);">Pelatihan</span>
            </h1>
            <p class="text-white/60 mt-3 text-base max-w-lg">
                Temukan program vokasi yang sesuai dengan keahlian dan karier yang ingin kamu raih.
            </p>
        </div>

        <!-- Search -->
        <form action="{{ route('programs.public') }}" method="GET">
            <div class="search-wrap flex items-center gap-3 px-5 py-3 max-w-2xl">
                <i class="fas fa-search text-white/50 text-sm flex-shrink-0"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="search-input"
                    placeholder="Cari nama program, kode, atau kompetensi..."
                    autocomplete="off"
                >
                @if(request('search'))
                    <a href="{{ route('programs.public') }}" class="text-white/50 hover:text-white/80 transition-colors flex-shrink-0">
                        <i class="fas fa-times text-sm"></i>
                    </a>
                @endif
                <button type="submit" class="search-btn flex-shrink-0">
                    <i class="fas fa-search text-xs"></i>
                    Cari
                </button>
            </div>
        </form>

        @if(request('search'))
        <p class="mt-4 text-sm text-white/60">
            Hasil pencarian untuk: <span class="text-teal-300 font-semibold">"{{ request('search') }}"</span>
            — {{ $masterPrograms->total() }} program ditemukan
        </p>
        @endif
    </div>
</div>

<!-- ══════════════ PROGRAM GRID ══════════════ -->
<div class="max-w-7xl mx-auto px-6 py-12">

    @if($masterPrograms->count() > 0)

        <div class="section-label">
            Daftar Program
            <span class="text-xs font-normal text-slate-400 normal-case tracking-normal ml-1">
                halaman {{ $masterPrograms->currentPage() }} dari {{ $masterPrograms->lastPage() }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($masterPrograms as $mp)
            <div class="prog-card">
                <div class="flex items-start gap-4">
                    <div class="card-icon">📚</div>
                    <div class="flex-1 min-w-0">

                        @if(!empty($mp->code))
                        <div class="mb-2">
                            <span class="code-badge" title="{{ $mp->code }}">{{ $mp->code }}</span>
                        </div>
                        @endif

                        <h3 class="font-bold text-base leading-snug text-gray-900 mb-2">
                            {{ $mp->name }}
                        </h3>

                        @if(!empty($mp->description))
                        <p class="text-gray-500 text-sm leading-relaxed line-clamp-3">
                            {{ Illuminate\Support\Str::limit(strip_tags($mp->description), 130) }}
                        </p>
                        @endif

                    </div>
                </div>

                <!-- Card footer -->
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                    <span class="text-xs text-gray-400 font-medium">Program Pelatihan</span>
                    <span class="w-7 h-7 rounded-full bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 text-xs transition-all duration-300 group-hover:bg-teal-600 group-hover:text-white">
                        <i class="fas fa-arrow-right" style="font-size:10px"></i>
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12 pagination-wrap">
            {{ $masterPrograms->appends(request()->query())->links() }}
        </div>

    @else
        <!-- Empty state -->
        <div class="empty-state">
            <div class="empty-icon">🔍</div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Program tidak ditemukan</h3>
            <p class="text-gray-500 text-sm mb-6 max-w-sm mx-auto">
                Tidak ada program yang cocok dengan pencarian
                @if(request('search'))
                    "<span class="font-semibold text-teal-600">{{ request('search') }}</span>"
                @endif
            </p>
            @if(request('search'))
            <a href="{{ route('programs.public') }}"
               class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm px-6 py-3 rounded-xl transition-colors">
                <i class="fas fa-times text-xs"></i>
                Hapus filter & lihat semua
            </a>
            @endif
        </div>
    @endif
</div>

<!-- ══════════════ FOOTER ══════════════ -->
<div class="max-w-7xl mx-auto px-6 pb-10">
    <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-3">
        <p class="text-sm text-gray-400">© {{ date('Y') }} BPVP Banda Aceh · Balai Pelatihan Vokasi &amp; Produktivitas</p>
        <a href="/" class="text-sm text-teal-600 hover:text-teal-700 font-medium flex items-center gap-2">
            <i class="fas fa-home text-xs"></i> Kembali ke Beranda
        </a>
    </div>
</div>

</body>
</html>
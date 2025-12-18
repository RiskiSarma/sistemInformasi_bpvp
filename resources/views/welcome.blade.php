<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BLK Banda Aceh - Sistem Informasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo blk banda.png') }}">
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%);
        }
        .wa-float {
        position: fixed;
        width: 65px;
        height: 65px;
        bottom: 25px;
        right: 25px;
        background-color: #25D366;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        box-shadow: 0px 4px 15px rgba(0,0,0,0.3);
        cursor: pointer;
        z-index: 999999;
        animation: waPulse 2s infinite;
    }

    @keyframes waPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.12); }
        100% { transform: scale(1); }
    }

    /* Popup Container */
    .wa-popup {
        position: fixed;
        bottom: 110px;
        right: 25px;
        width: 330px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 999999;
        display: none;
        overflow: hidden;
        animation: slideUp .35s ease-out;
        font-family: Arial, sans-serif;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Header */
    .wa-header {
        background: #25D366;
        color: #fff;
        padding: 20px;
        font-size: 18px;
        font-weight: bold;
        position: relative;
    }

    .wa-header p {
        font-size: 14px;
        margin: 5px 0 0 0;
        opacity: .9;
    }

    /* Close button */
    .wa-close {
        position: absolute;
        top: 10px;
        right: 12px;
        font-size: 20px;
        color: #fff;
        cursor: pointer;
    }

    /* Body */
    .wa-body {
        padding: 15px;
    }

    /* Admin Item */
    .wa-contact {
        display: flex;
        align-items: center;
        background: #f6f6f6;
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: .2s;
        border: 1px solid #e0e0e0;
    }

    .wa-contact:hover {
        background: #eafdf2;
    }

    .wa-img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #25D366;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        margin-right: 12px;
    }

    .wa-name { font-size: 16px; font-weight: bold; }
    .wa-status { font-size: 13px; color: gray; }

    /* Responsive: tampil hanya di mobile */
    @media (min-width: 768px) {
        .wa-mobile-only { display: none !important; }
    }
    </style>
</head>
<body class="antialiased">
    <?php
    // Koneksi database
    $host = 'localhost';
    $dbname = 'bpvp_db';
    $username = 'root';
    $password = '';

    // Inisialisasi variabel dengan nilai default
    $totalParticipants = 0;
    $totalPrograms = 0;
    $totalInstructors = 0;
    $totalSertifikat = 0;
    $errorMessage = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query untuk mendapatkan jumlah Participants aktif
        try {
            $queryParticipants = "SELECT COUNT(*) as total FROM participants WHERE status = 'active'";
            $stmtParticipants = $pdo->query($queryParticipants);
            $result = $stmtParticipants->fetch(PDO::FETCH_ASSOC);
            $totalParticipants = $result['total'] ?? 0;
        } catch(PDOException $e) {
            // Jika query gagal, coba hitung semua data tanpa filter status
            try {
                $queryParticipants = "SELECT COUNT(*) as total FROM participants";
                $stmtParticipants = $pdo->query($queryParticipants);
                $result = $stmtParticipants->fetch(PDO::FETCH_ASSOC);
                $totalParticipants = $result['total'] ?? 0;
            } catch(PDOException $e2) {
                $errorMessage .= "Error Participants: " . $e2->getMessage() . "<br>";
            }
        }
                // Hitung program ongoing
                $totalOngoing = 0;
                try {
                    $queryOngoing = "SELECT COUNT(*) as total FROM programs WHERE status = 'ongoing'";
                    $stmt = $pdo->query($queryOngoing);
                    $totalOngoing = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                } catch(PDOException $e) {
                    $totalOngoing = 0;
                }

                // Hitung program completed
                $totalCompleted = 0;
                try {
                    $queryCompleted = "SELECT COUNT(*) as total FROM programs WHERE status = 'completed'";
                    $stmt = $pdo->query($queryCompleted);
                    $totalCompleted = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                } catch(PDOException $e) {
                    $totalCompleted = 0;
                }
        // // Query untuk mendapatkan jumlah Programs pelatihan
        // try {
        //     $queryPrograms = "SELECT COUNT(*) as total FROM programs WHERE status = 'ongoing'";
        //     $stmtPrograms = $pdo->query($queryPrograms);
        //     $result = $stmtPrograms->fetch(PDO::FETCH_ASSOC);
        //     $totalPrograms = $result['total'] ?? 0;
        // } catch(PDOException $e) {
        //     // Jika query gagal, coba hitung semua data
        //     try {
        //         $queryPrograms = "SELECT COUNT(*) as total FROM programs";
        //         $stmtPrograms = $pdo->query($queryPrograms);
        //         $result = $stmtPrograms->fetch(PDO::FETCH_ASSOC);
        //         $totalPrograms = $result['total'] ?? 0;
        //     } catch(PDOException $e2) {
        //         $errorMessage .= "Error Programs: " . $e2->getMessage() . "<br>";
        //     }
        // }

        // Query untuk mendapatkan jumlah Instructors
        try {
            $queryInstructors = "SELECT COUNT(*) as total FROM instructors WHERE status = 'active'";
            $stmtInstructors = $pdo->query($queryInstructors);
            $result = $stmtInstructors->fetch(PDO::FETCH_ASSOC);
            $totalInstructors = $result['total'] ?? 0;
        } catch(PDOException $e) {
            // Jika query gagal, coba hitung semua data
            try {
                $queryInstructors = "SELECT COUNT(*) as total FROM instructors";
                $stmtInstructors = $pdo->query($queryInstructors);
                $result = $stmtInstructors->fetch(PDO::FETCH_ASSOC);
                $totalInstructors = $result['total'] ?? 0;
            } catch(PDOException $e2) {
                $errorMessage .= "Error Instructors: " . $e2->getMessage() . "<br>";
            }
        }

        // Query untuk mendapatkan jumlah sertifikat yang sudah diterbitkan
        try {
            $querySertifikat = "SELECT COUNT(*) as total FROM certificates WHERE status = 'issued'";
            $stmtSertifikat = $pdo->query($querySertifikat);
            $result = $stmtSertifikat->fetch(PDO::FETCH_ASSOC);
            $totalSertifikat = $result['total'] ?? 0;
        } catch(PDOException $e) {
            // Jika query gagal, coba hitung semua data
            try {
                $querySertifikat = "SELECT COUNT(*) as total FROM certificates";
                $stmtSertifikat = $pdo->query($querySertifikat);
                $result = $stmtSertifikat->fetch(PDO::FETCH_ASSOC);
                $totalSertifikat = $result['total'] ?? 0;
            } catch(PDOException $e2) {
                $errorMessage .= "Error Sertifikat: " . $e2->getMessage() . "<br>";
            }
        }

    } catch(PDOException $e) {
        $errorMessage = "Koneksi Database Gagal: " . $e->getMessage();
        // Gunakan nilai default yang sudah diset di atas
    }
    ?>

    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjz9kmrBxqmtTcjR5DfGbcL0blmphH6V9chaKj5rJHWVW59vuEbp8OvusBJxR79eKcNEjUIstpT4gjQbVUSA5LgemfC5oy5hZgzsqxw8O3pg-064l2YToAxL9E2ljEPBHU05J_2Cl8roOI/s705/logo_blk_biru.png.png" alt="Logo Kemnaker" class="h-12 w-auto">
                        {{-- <span class="ml-3 text-xl font-bold text-gray-800">BLK Banda Aceh</span> --}}
                    </div>
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <a href="#home" class="text-gray-700 hover:text-blue-700 px-3 py-2 text-sm font-medium transition">Beranda</a>
                        <a href="#about" class="text-gray-700 hover:text-blue-700 px-3 py-2 text-sm font-medium transition">Tentang</a>
                        <a href="#programs" class="text-gray-700 hover:text-blue-700 px-3 py-2 text-sm font-medium transition">Program</a>
                        <a href="#contact" class="text-gray-700 hover:text-blue-700 px-3 py-2 text-sm font-medium transition">Kontak</a>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-700 px-4 py-2 text-sm font-medium transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-700 to-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition duration-200">
                            Daftar
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-blue-700 to-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition duration-200">
                            Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="gradient-bg pt-24 pb-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <h1 class="text-5xl font-bold mb-6 leading-tight">
                        Tingkatkan Skill Anda di <span class="text-yellow-300">BLK Banda Aceh</span>
                    </h1>
                    <p class="text-xl mb-8 text-blue-50">
                        Platform pelatihan profesional untuk meningkatkan kompetensi dan keterampilan kerja yang dibutuhkan industri modern di Aceh dan sekitarnya.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('login') }}" class="bg-white text-blue-800 px-8 py-3 rounded-lg font-bold hover:shadow-2xl transform hover:scale-105 transition duration-200">
                            <i class="fas fa-rocket mr-2"></i>Mulai Sekarang
                        </a>
                        <a href="#programs" class="border-2 border-white text-white px-8 py-3 rounded-lg font-bold hover:bg-white hover:text-blue-800 transition duration-200">
                            <i class="fas fa-info-circle mr-2"></i>Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="float-animation">
                        <div class="bg-white rounded-3xl shadow-2xl p-8">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 text-center border border-blue-200">
                                    <i class="fas fa-users text-blue-700 text-4xl mb-2"></i>
                                    <h3 class="text-3xl font-bold text-gray-800"><?php echo number_format($totalParticipants); ?></h3>
                                    <p class="text-gray-600 text-sm">Peserta Aktif</p>
                                </div>
                                <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl p-6 text-center border border-cyan-200">
                                    <i class="fas fa-play-circle text-cyan-700 text-4xl mb-2"></i>
                                    <h3 class="text-3xl font-bold text-gray-800">{{ $totalOngoing }}</h3>
                                    <p class="text-gray-600 text-sm">Sedang Berjalan</p>
                                </div>

                                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 text-center border border-green-200">
                                    <i class="fas fa-check-circle text-green-700 text-4xl mb-2"></i>
                                    <h3 class="text-3xl font-bold text-gray-800">{{ $totalCompleted }}</h3>
                                    <p class="text-gray-600 text-sm">Sudah Diselenggarakan</p>
                                </div>
                                {{-- <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-6 text-center border border-indigo-200">
                                    <i class="fas fa-chalkboard-teacher text-indigo-700 text-4xl mb-2"></i>
                                    <h3 class="text-3xl font-bold text-gray-800"><?php echo $totalInstructors; ?>+</h3>
                                    <p class="text-gray-600 text-sm">Instruktur Ahli</p>
                                </div> --}}
                                <div class="bg-gradient-to-br from-sky-50 to-sky-100 rounded-xl p-6 text-center border border-sky-200">
                                    <i class="fas fa-certificate text-sky-700 text-4xl mb-2"></i>
                                    <h3 class="text-3xl font-bold text-gray-800"><?php echo number_format($totalSertifikat); ?>+</h3>
                                    <p class="text-gray-600 text-sm">Sertifikat Terbit</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Tentang BLK Banda Aceh</h2>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-700 to-blue-900 mx-auto"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transform hover:-translate-y-2 transition duration-300 border border-gray-200">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-bullseye text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Visi Kami</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Menjadi pusat pelatihan kerja terdepan di Aceh yang menghasilkan tenaga kerja kompeten, berakhlak mulia, dan siap bersaing di era digital.
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transform hover:-translate-y-2 transition duration-300 border border-gray-200">
                    <div class="w-20 h-20 bg-gradient-to-br from-cyan-600 to-cyan-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-flag text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Misi Kami</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Memberikan pelatihan berkualitas dengan kurikulum terkini dan instruktur berpengalaman untuk mempersiapkan SDM unggul bagi pembangunan Aceh.
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transform hover:-translate-y-2 transition duration-300 border border-gray-200">
                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-award text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Keunggulan</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Sertifikat resmi BNSP, fasilitas modern, instruktur bersertifikat, dan jaringan industri yang luas untuk penyaluran kerja.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section id="programs" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Program Pelatihan</h2>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-700 to-blue-900 mx-auto mb-4"></div>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Berbagai program pelatihan yang dirancang untuk meningkatkan kompetensi sesuai kebutuhan industri
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Program 1 -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transform hover:-translate-y-2 transition duration-300 border-t-4 border-blue-600">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center mb-4 shadow-md">
                        <i class="fas fa-code text-white text-2xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Web Programmer</h2>
                    {{-- <h3>Program Pelatihan Web Programmer</h3> --}}
                    <p class="text-gray-600 mb-4 leading-relaxed">Program pelatihan Web Programmer merupakan salah satu program pelatihan dari kejuruan Teknologi Informasi dan Komunikasi (TIK). 
                        Dengan jumlah jam pelatihan 340 JP atau 43 hari kerja. Setelah mengikuti pelatihan peserta di harapkan mampu :</p>
                        <li>
                            Merancang dan mengkonstruksi website, menggunakan prinsip pengoperasian aplikasi pemrograman web dinamis (HTML dan PHP), database (MySql).
                        <li>
                            Memindahkan website dari localhost ke hosting online sesuai tutorial
                        </li>
                        </li>
                    <div class="flex items-center text-sm text-gray-500 pt-3 border-t border-gray-200">
                        <i class="far fa-clock mr-2"></i>
                        <span>3 Bulan</span>
                    </div>
                </div>

                <!-- Program 2 -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transform hover:-translate-y-2 transition duration-300 border-t-4 border-cyan-600">
                    <div class="w-16 h-16 bg-gradient-to-br from-cyan-600 to-cyan-800 rounded-lg flex items-center justify-center mb-4 shadow-md">
                        <i class="fas fa-paint-brush text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Graphic Design</h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">Kuasai seni desain grafis dengan Adobe Photoshop, Illustrator, dan tools profesional lainnya.</p>
                    <div class="flex items-center text-sm text-gray-500 pt-3 border-t border-gray-200">
                        <i class="far fa-clock mr-2"></i>
                        <span>2 Bulan</span>
                    </div>
                </div>

                <!-- Program 3 -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transform hover:-translate-y-2 transition duration-300 border-t-4 border-indigo-600">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-lg flex items-center justify-center mb-4 shadow-md">
                        <i class="fas fa-mobile-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Mobile App Development</h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">Develop aplikasi mobile Android dan iOS dengan Flutter atau React Native.</p>
                    <div class="flex items-center text-sm text-gray-500 pt-3 border-t border-gray-200">
                        <i class="far fa-clock mr-2"></i>
                        <span>4 Bulan</span>
                    </div>
                </div>

                <!-- Program 4 -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transform hover:-translate-y-2 transition duration-300 border-t-4 border-sky-600">
                    <div class="w-16 h-16 bg-gradient-to-br from-sky-600 to-sky-800 rounded-lg flex items-center justify-center mb-4 shadow-md">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Digital Marketing</h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">Pelajari strategi pemasaran digital, SEO, social media marketing, dan content marketing.</p>
                    <div class="flex items-center text-sm text-gray-500 pt-3 border-t border-gray-200">
                        <i class="far fa-clock mr-2"></i>
                        <span>2 Bulan</span>
                    </div>
                </div>

                <!-- Program 5 -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transform hover:-translate-y-2 transition duration-300 border-t-4 border-slate-600">
                    <div class="w-16 h-16 bg-gradient-to-br from-slate-600 to-slate-800 rounded-lg flex items-center justify-center mb-4 shadow-md">
                        <i class="fas fa-database text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Data Analysis</h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">Analisis data dengan Python, SQL, dan tools visualisasi untuk menghasilkan insight bisnis.</p>
                    <div class="flex items-center text-sm text-gray-500 pt-3 border-t border-gray-200">
                        <i class="far fa-clock mr-2"></i>
                        <span>3 Bulan</span>
                    </div>
                </div>

                <!-- Program 6 -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transform hover:-translate-y-2 transition duration-300 border-t-4 border-violet-600">
                    <div class="w-16 h-16 bg-gradient-to-br from-violet-600 to-violet-800 rounded-lg flex items-center justify-center mb-4 shadow-md">
                        <i class="fas fa-camera text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Photography & Videography</h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">Teknik fotografi dan videografi profesional untuk content creator dan multimedia.</p>
                    <div class="flex items-center text-sm text-gray-500 pt-3 border-t border-gray-200">
                        <i class="far fa-clock mr-2"></i>
                        <span>2 Bulan</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="gradient-bg py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Siap Untuk Meningkatkan Skill Anda?</h2>
            <p class="text-xl text-blue-50 mb-8 max-w-2xl mx-auto">
                Bergabunglah dengan ribuan peserta lainnya dan raih kesempatan karir yang lebih baik di Aceh
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('register') }}" class="bg-white text-blue-800 px-8 py-4 rounded-lg font-bold text-lg hover:shadow-2xl transform hover:scale-105 transition duration-200">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </a>
                <a href="#contact" class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-blue-800 transition duration-200">
                    <i class="fas fa-phone mr-2"></i>Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Hubungi Kami</h2>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-700 to-blue-900 mx-auto"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transition border border-gray-200">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                        <i class="fas fa-map-marker-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Alamat</h3>
                    <p class="text-gray-600 justify">Jl. Kesatria Geuceu Komplek
                        Kec. Banda Raya-Kota Banda Aceh
                        <br>Hours: Mon-Fri 8:00AM - 4:00PM</p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transition border border-gray-200">
                    <div class="w-16 h-16 bg-gradient-to-br from-cyan-600 to-cyan-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                        <i class="fas fa-phone text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Telepon</h3>
                    <p class="text-gray-600">(0651) 45298<br>0812-3456-7890</p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transition border border-gray-200">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                        <i class="fas fa-envelope text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Email</h3>
                    <p class="text-gray-600">blkbandaaceh@kemnaker.go.id<br>admin@blkbandaaceh.ac.id</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjz9kmrBxqmtTcjR5DfGbcL0blmphH6V9chaKj5rJHWVW59vuEbp8OvusBJxR79eKcNEjUIstpT4gjQbVUSA5LgemfC5oy5hZgzsqxw8O3pg-064l2YToAxL9E2ljEPBHU05J_2Cl8roOI/s705/logo_blk_biru.png.png" alt="Logo Kemnaker" class="h-12 w-auto">
                    </div>
                    <p class="text-gray-700">Balai Latihan Kerja terpercaya untuk mengembangkan skill dan kompetensi profesional di Aceh.</p>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4 text-black">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-700 hover:text-blue-700 transition">Beranda</a></li>
                        <li><a href="#about" class="text-gray-700 hover:text-blue-700 transition">Tentang</a></li>
                        <li><a href="#programs" class="text-gray-700 hover:text-blue-700 transition">Program</a></li>
                        <li><a href="#contact" class="text-gray-700 hover:text-blue-700 transition">Kontak</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4 text-black">Layanan Pengaduan</h4>
                    <ul class="space-y-2">
                        <li><a href="https://wbs.kemnaker.go.id" class="text-gray-700 hover:text-blue-700 transition">Whistleblowing</a></li>
                        <li><a href="https://www.lapor.go.id" class="text-gray-700 hover:text-blue-700 transition">Lapor.go.id</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4 text-black">Follow Us</h4>
                    <div class="flex space-x-4">

                    <!-- Facebook -->
                    <a href="#" class="w-10 h-10 flex items-center justify-center">
                        <i class="fab fa-facebook-f text-blue-500 hover:text-blue-700 text-2xl transition"></i>
                    </a>

                    <!-- X / Twitter -->
                    <a href="https://x.com/bpvp.bandaaceh" class="w-10 h-10 flex items-center justify-center">
                        <i class="bi bi-twitter-x text-blue-500 hover:text-blue-700 text-2xl transition"></i>
                    </a>

                    <!-- Instagram -->
                    <a href="https://www.instagram.com/blkbandaceh" class="w-10 h-10 flex items-center justify-center">
                        <i class="fab fa-instagram text-blue-500 hover:text-blue-700 text-2xl transition"></i>
                    </a>

                    <!-- YouTube -->
                    <a href="https://www.youtube.com/bpvpbandaaceh" class="w-10 h-10 flex items-center justify-center">
                        <i class="fab fa-youtube text-blue-500 hover:text-blue-700 text-2xl transition"></i>
                    </a>

                    <!-- LinkedIn -->
                    <a href="#" class="w-10 h-10 flex items-center justify-center">
                        <i class="fab fa-linkedin-in text-blue-500 hover:text-blue-700 text-2xl transition"></i>
                    </a>

                    <!-- TikTok -->
                    <a href="#" class="w-10 h-10 flex items-center justify-center">
                        <i class="fab fa-tiktok text-blue-500 hover:text-blue-700 text-2xl transition"></i>
                    </a>
                </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center text-gray-700">
                <p>© 2025 Balai Latihan Kerja Banda Aceh. All rights reserved.</p>
            </div>
        </div>
    </footer>
<!-- Floating Button -->
<div class="wa-float" id="waBtn">
    <i class="fab fa-whatsapp"></i>
</div>

<!-- WhatsApp Popup -->
<div class="wa-popup" id="waPopup">

    <div class="wa-header">
        Respon Cepat
        <div class="wa-close" id="waClose">&times;</div>
        <p>Pilih admin untuk menghubungi via WhatsApp</p>
    </div>

    <div class="wa-body">

        <!-- Admin 1 -->
        <div class="wa-contact" onclick="openWa('6281269334494','Halo admin 1, saya ingin bertanya...')">
            <div class="wa-img"><i class="fab fa-whatsapp"></i></div>
            <div>
                <div class="wa-name">Admin 1 - BPVP Aceh</div>
                <div class="wa-status">Online</div>
            </div>
        </div>

        <!-- Admin 2 -->
        <div class="wa-contact" onclick="openWa('6282222222222','Halo admin 2, saya ingin info lebih lanjut')">
            <div class="wa-img"><i class="fab fa-whatsapp"></i></div>
            <div>
                <div class="wa-name">Admin Pelatihan</div>
                <div class="wa-status">Biasanya membalas dalam 10 menit</div>
            </div>
        </div>

        <!-- Admin 3 -->
        <div class="wa-contact wa-mobile-only" onclick="openWa('6283333333333','Halo admin mobile, saya pengguna HP!')">
            <div class="wa-img"><i class="fab fa-whatsapp"></i></div>
            <div>
                <div class="wa-name">Admin Khusus Mobile</div>
                <div class="wa-status">Tampil hanya di HP</div>
            </div>
        </div>

    </div>
</div>

<script>
    const waBtn = document.getElementById('waBtn');
    const waPopup = document.getElementById('waPopup');
    const waClose = document.getElementById('waClose');

    // Toggle popup
    waBtn.onclick = () => { waPopup.style.display = 'block'; };
    waClose.onclick = () => { waPopup.style.display = 'none'; };

    // Multi-admin function
    function openWa(phone, text) {
        window.open(`https://wa.me/${phone}?text=${encodeURIComponent(text)}`, '_blank');
    }

    // Auto show popup (3 seconds)
    setTimeout(() => {
        waPopup.style.display = 'block';
    }, 3000);
</script>
    <!-- Smooth Scroll -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
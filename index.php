<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rumah Laundry — Laundry Cepat, Bersih, dan Terpercaya</title>
    <meta name="description"
        content="Rumah Laundry menyediakan layanan laundry profesional, cepat, bersih, dan terpercaya. Laundry kiloan, satuan, dry cleaning, dan antar jemput." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="style.css?v=20260223" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
      #logout-overlay {
        position: fixed;
        inset: 0;
        background: rgba(45, 26, 36, 0.45);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
        animation: fadeIn 0.25s ease;
      }
      @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
      }
      #logout-overlay .card {
        width: 100%;
        max-width: 480px;
        background: #ffffff;
        border-radius: 20px;
        padding: 36px 40px 36px;
        box-shadow: 0 4px 40px rgba(180, 100, 130, 0.1);
        position: relative;
        animation: rise 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
      }
      @keyframes rise {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
      }
      #logout-overlay .card::before {
        content: '';
        position: absolute;
        top: 0; left: 40px; right: 40px;
        height: 1.5px;
        border-radius: 0 0 2px 2px;
        background: linear-gradient(90deg, transparent, #e8a0b8, transparent);
      }
      #logout-overlay .illus-area {
        width: 100%; height: 140px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 28px; background: #fdf5f8;
        position: relative; overflow: hidden;
      }
      #logout-overlay .illus-area::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(circle, #e8c0cf 1px, transparent 1px);
        background-size: 18px 18px; opacity: 0.35;
      }
      #logout-overlay .illus-svg { position: relative; z-index: 1; }
      #logout-overlay .eyebrow {
        font-size: 10px; letter-spacing: 2.5px; text-transform: uppercase;
        color: #d4889f; font-weight: 500; margin-bottom: 8px;
        font-family: 'Jost', sans-serif;
      }
      #logout-overlay .title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 25px; font-weight: 300; color: #2d1a24;
        line-height: 1.3; margin-bottom: 10px;
      }
      #logout-overlay .desc {
        font-family: 'Jost', sans-serif;
        font-size: 13px; color: #9e7a88; line-height: 1.7;
        font-weight: 300; margin-bottom: 26px;
      }
      #logout-overlay .session {
        display: flex; align-items: center; gap: 10px;
        padding: 11px 14px; background: #fdf8fa;
        border-radius: 10px; margin-bottom: 24px;
        border: 1px solid #f0e4eb;
        font-family: 'Jost', sans-serif;
      }
      #logout-overlay .session-avatar {
        width: 30px; height: 30px; border-radius: 50%;
        background: linear-gradient(135deg, #f2a0b8, #d46880);
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; color: #fff; font-weight: 500; flex-shrink: 0;
      }
      #logout-overlay .session-name { font-size: 13px; color: #5c3a48; font-weight: 400; }
      #logout-overlay .session-role { font-size: 11px; color: #c0909e; margin-top: 1px; }
      #logout-overlay .divider { height: 1px; background: #f2e8ed; margin-bottom: 24px; }
      #logout-overlay .btn-row { display: flex; gap: 10px; }
      #logout-overlay .btn-modal {
        flex: 1; height: 44px; border-radius: 10px;
        font-family: 'Jost', sans-serif; font-size: 13px; font-weight: 400;
        letter-spacing: 0.5px; cursor: pointer; border: none;
        transition: all 0.2s ease; display: flex; align-items: center; justify-content: center;
      }
      #logout-overlay .btn-ghost {
        background: transparent; color: #b08898; border: 1px solid #ead8e2;
      }
      #logout-overlay .btn-ghost:hover { background: #fdf0f5; border-color: #d4a0b8; color: #8d6070; }
      #logout-overlay .btn-confirm { background: #d4607c; color: #fff; letter-spacing: 0.8px; }
      #logout-overlay .btn-confirm:hover { background: #c0546e; }
      #logout-overlay .close {
        position: absolute; top: 18px; right: 20px;
        width: 28px; height: 28px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: #cdb0bb; font-size: 18px;
        border-radius: 50%; transition: all 0.2s;
        border: none; background: none;
      }
      #logout-overlay .close:hover { color: #8d6070; background: #fdf0f5; }

      @keyframes floatUp {
        0%   { transform: translateY(0) rotate(0deg); opacity: 0.9; }
        100% { transform: translateY(-55px) rotate(25deg); opacity: 0; }
      }
      #logout-overlay .petal-anim { animation: floatUp 2.8s ease-in-out infinite; }
      #logout-overlay .petal-anim:nth-child(2) { animation-delay: 0.6s; }
      #logout-overlay .petal-anim:nth-child(3) { animation-delay: 1.3s; }

      @keyframes gentleRock {
        0%, 100% { transform: rotate(-4deg); }
        50%       { transform: rotate(4deg); }
      }
      #logout-overlay .rock { animation: gentleRock 4s ease-in-out infinite; transform-origin: center bottom; }

      @keyframes shimmer {
        0%, 100% { opacity: 0.6; }
        50%       { opacity: 1; }
      }
      #logout-overlay .shimmer { animation: shimmer 2.5s ease-in-out infinite; }
    </style>
</head>

<body>

    <!-- ═══════════════════ NAVBAR ═══════════════════ -->
    <nav id="navbar" class="navbar">
        <div class="container navbar__inner">
            <a href="#home" class="navbar__brand">
                <img src="assets/img/RL.png" alt="Rumah Laundry" style="height:36px;width:auto;object-fit:contain;vertical-align:middle;margin-right:4px;"> Rumah Laundry
            </a>

            <button class="navbar__toggle" id="navToggle" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>

            <ul class="navbar__menu" id="navMenu">
                <li><a href="#home" class="navbar__link active">Home</a></li>
                <li><a href="#service" class="navbar__link">Service</a></li>
                <li><a href="#review" class="navbar__link">Review</a></li>
                <li><a href="#lacak" class="navbar__link">Lacak Cucian</a></li>
                <li><a href="#contact" class="navbar__link">Contact</a></li>
                <li class="navbar__separator" aria-hidden="true"></li>
                <?php if (isset($_SESSION['id'])): ?>
                <li><a href="riwayat_pesanan.php" class="navbar__link"><i class="fas fa-clipboard-list"></i> Pesanan</a></li>
                <li class="navbar__user-area">
                    <a href="profile.php" class="navbar__avatar-link" title="Profil Saya">
                        <?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?>
                    </a>
                    <a href="logout.php" class="navbar__btn-logout logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
                <?php else: ?>
                <li>
                    <a href="login.php" class="navbar__btn-login">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- ═══════════════════ HERO ═══════════════════ -->
    <section id="home" class="hero">
        <div class="hero__bg-shapes">
            <div class="hero__shape hero__shape--1"></div>
            <div class="hero__shape hero__shape--2"></div>
            <div class="hero__shape hero__shape--3"></div>
        </div>

        <div class="container hero__inner">
            <div class="hero__content" data-animate="fade-up">
                
                <h1 class="hero__title">Laundry Cepat, Bersih,<br />dan <span class="text-gradient">Terpercaya</span>
                </h1>
                <p class="hero__subtitle">Nikmati layanan laundry dengan kualitas terbaik. Pakaian Anda akan
                    kembali bersih, wangi, dan rapi — tanpa repot.</p>
                <div class="hero__buttons">
                    <a href="#service" class="btn btn--primary">
                        <i class="fas fa-shopping-bag"></i> Pesan Sekarang
                    </a>
                    <a href="#lacak" class="btn btn--outline">
                        <i class="fas fa-search"></i> Lacak Cucian
                    </a>
                </div>
                <div class="hero__stats">
                    <div class="hero__stat">
                        <strong>99+</strong>
                        <span>Pelanggan Puas</span>
                    </div>
                    <div class="hero__stat">
                        <strong>4.7★</strong>
                        <span>Rating</span>
                    </div>
                    <div class="hero__stat">
                        <strong>12 Jam</strong>
                        <span>Layanan</span>
                    </div>
                </div>
            </div>

            <div class="hero__visual" data-animate="fade-left">
                <div class="hero__illustration">
                    <div class="hero__float-card hero__float-card--1">
                        <i class="fas fa-check-circle"></i>
                        <span>Cucian Selesai!</span>
                    </div>
                    <div class="hero__float-card hero__float-card--2">
                        <i class="fas fa-star"></i>
                        <span>Rating 4.7</span>
                    </div>
                    <div class="hero__main-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div class="hero__orbit hero__orbit--1">
                        <i class="fas fa-soap"></i>
                    </div>
                    <div class="hero__orbit hero__orbit--2">
                        <i class="fas fa-wind"></i>
                    </div>
                    <div class="hero__orbit hero__orbit--3">
                        <i class="fas fa-spray-can-sparkles"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ SERVICES ═══════════════════ -->
    <section id="service" class="services">
        <div class="container">
            <div class="section-header" data-animate="fade-up">
                <span class="section-badge">Layanan Kami</span>
                <h2 class="section-title">Solusi Laundry <span class="text-gradient">Terlengkap</span></h2>
                <p class="section-subtitle">Kami menyediakan berbagai layanan laundry berkualitas tinggi untuk memenuhi
                    kebutuhan Anda.</p>
            </div>

            <div class="services__grid">
                <!-- Card 1 -->
                <div class="service-card" data-animate="fade-up" data-delay="0">
                    <div class="service-card__icon">
                        <i class="fas fa-weight-hanging"></i>
                    </div>
                    <h3 class="service-card__title">Laundry Kiloan</h3>
                    <p class="service-card__desc">Layanan cuci hemat per kilogram. Cocok untuk pakaian sehari-hari
                        dengan hasil bersih dan wangi.</p>
                    <div class="service-card__price">Mulai <strong>Rp 4.000</strong>/kg</div>
                    <a href="#contact" class="service-card__link">Pesan Sekarang <i class="fas fa-arrow-right"></i></a>
                </div>

                <!-- Card 2 -->
                <div class="service-card" data-animate="fade-up" data-delay="100">
                    <div class="service-card__icon">
                        <i class="fas fa-shirt"></i>
                    </div>
                    <h3 class="service-card__title">Laundry Satuan</h3>
                    <p class="service-card__desc">Perawatan khusus untuk setiap potong pakaian. Ideal untuk pakaian
                        formal dan bahan sensitif.</p>
                    <div class="service-card__price">Mulai <strong>Rp 15.000</strong>/pcs</div>
                    <a href="#contact" class="service-card__link">Pesan Sekarang <i class="fas fa-arrow-right"></i></a>
                </div>

                <!-- Card 3 -->
                <div class="service-card" data-animate="fade-up" data-delay="200">
                    <div class="service-card__icon">
                        <i class="fas fa-bed"></i>
                    </div>
                    <h3 class="service-card__title">Bed Cover</h3>
                    <p class="service-card__desc">Pembersihan khusus untuk bedcover menggunakan mesin dan deterjen yang sesuai agar kotoran, debu, dan bau hilang tanpa merusak bahan.</p>
                    <div class="service-card__price">Mulai <strong>Rp 25.000</strong>/pcs</div>
                    <a href="#contact" class="service-card__link">Pesan Sekarang <i class="fas fa-arrow-right"></i></a>
                </div>

                <!-- Card 4 -->
                <div class="service-card" data-animate="fade-up" data-delay="300">
                    <div class="service-card__icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 class="service-card__title">Antar Jemput</h3>
                    <p class="service-card__desc">Layanan antar jemput cucian langsung ke rumah Anda. Hemat waktu, tanpa
                        perlu keluar rumah.</p>
                    <div class="service-card__price"><strong>Gratis</strong> ongkir*</div>
                    <a href="#contact" class="service-card__link">Pesan Sekarang <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ REVIEWS ═══════════════════ -->
    <section id="review" class="reviews-v2">
        <div class="container">
            <div class="reviews-v2__header" data-animate="fade-up">
                <h2 class="reviews-v2__title">Read reviews,<br>ride with confidence.</h2>
                <div class="reviews-v2__trustpilot">
                    <span class="reviews-v2__rating">4.2/5</span>
                    <div class="reviews-v2__tp-brand">
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="reviews-v2__count">Based on 999+ reviews</span>
                </div>
            </div>

            <div class="reviews-v2__content">
                <div class="reviews-v2__sidebar" data-animate="fade-right">
                    <div class="reviews-v2__quote-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <h3 class="reviews-v2__side-title">What our<br>customers are<br>saying</h3>
                    
                    <div class="reviews-v2__nav">
                        <button class="reviews-v2__btn reviews-v2__btn--prev" aria-label="Previous">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="reviews-v2__pagination"></div>
                        <button class="reviews-v2__btn reviews-v2__btn--next" aria-label="Next">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <div class="reviews-v2__slider swiper" data-animate="fade-left">
                    <div class="swiper-wrapper">
                        <!-- Review 1 -->
                        <div class="swiper-slide">
                            <div class="review-card-v2">
                                <p class="review-card-v2__text">"Pelayanan sangat cepat dan hasilnya luar biasa bersih! Pakaian saya kembali seperti baru. Pasti akan jadi langganan."</p>
                                <div class="review-card-v2__rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <div class="review-card-v2__author">
                                    <div class="review-card-v2__avatar">AS</div>
                                    <div class="review-card-v2__meta">
                                        <strong>Anisa Safira</strong>
                                        <span>1 week ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review 2 -->
                        <div class="swiper-slide">
                            <div class="review-card-v2">
                                <p class="review-card-v2__text">"Layanan antar jemput sangat membantu! Tidak perlu repot keluar rumah, cucian dijemput dan diantar tepat waktu."</p>
                                <div class="review-card-v2__rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <div class="review-card-v2__author">
                                    <div class="review-card-v2__avatar">BR</div>
                                    <div class="review-card-v2__meta">
                                        <strong>Budi Raharjo</strong>
                                        <span>2 weeks ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review 3 -->
                        <div class="swiper-slide">
                            <div class="review-card-v2">
                                <p class="review-card-v2__text">"Dry cleaning mereka top banget! Jas saya terlihat seperti baru lagi. Harga juga sangat terjangkau untuk kualitas sebagus ini."</p>
                                <div class="review-card-v2__rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <div class="review-card-v2__author">
                                    <div class="review-card-v2__avatar">DW</div>
                                    <div class="review-card-v2__meta">
                                        <strong>Dewi Wulandari</strong>
                                        <span>10 days ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review 4 -->
                        <div class="swiper-slide">
                            <div class="review-card-v2">
                                <p class="review-card-v2__text">"Sudah berlangganan 2 tahun dan tidak pernah kecewa. Kualitas konsisten, staff ramah, dan wanginya tahan lama!"</p>
                                <div class="review-card-v2__rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <div class="review-card-v2__author">
                                    <div class="review-card-v2__avatar">FR</div>
                                    <div class="review-card-v2__meta">
                                        <strong>Farhan Rizky</strong>
                                        <span>3 weeks ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review 5 -->
                        <div class="swiper-slide">
                            <div class="review-card-v2">
                                <p class="review-card-v2__text">"Aplikasi tracking-nya sangat membantu. Saya bisa memantau status cucian kapan saja. Sangat modern dan praktis!"</p>
                                <div class="review-card-v2__rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <div class="review-card-v2__author">
                                    <div class="review-card-v2__avatar">MH</div>
                                    <div class="review-card-v2__meta">
                                        <strong>Maya Hartono</strong>
                                        <span>1 month ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ TRACKING ═══════════════════ -->
    <section id="lacak" class="tracking">

        <!-- ── Floating decorative illustrations ── -->
        <div class="tracking__deco" aria-hidden="true">

            <!-- Washing machine — kiri atas -->
            <svg class="tracking__deco-item tracking__deco--washer" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="4" y="4" width="72" height="72" rx="12" fill="#f3d0d8" fill-opacity="0.55"/>
                <rect x="10" y="10" width="60" height="60" rx="8" stroke="#d8a7b1" stroke-width="2.5" fill="none"/>
                <circle cx="40" cy="44" r="16" stroke="#d8a7b1" stroke-width="2.5" fill="white" fill-opacity="0.4"/>
                <circle cx="40" cy="44" r="8" stroke="#c67a89" stroke-width="2" fill="none"/>
                <circle cx="18" cy="20" r="3" fill="#c67a89" fill-opacity="0.7"/>
                <circle cx="28" cy="20" r="3" fill="#d8a7b1" fill-opacity="0.7"/>
                <rect x="50" y="16" width="14" height="8" rx="4" fill="#d8a7b1" fill-opacity="0.6"/>
            </svg>

            <!-- Shirt / baju — kanan atas -->
            <svg class="tracking__deco-item tracking__deco--shirt" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25 8 C25 8 20 5 14 10 L4 22 L16 26 L16 62 L54 62 L54 26 L66 22 L56 10 C50 5 45 8 45 8 C43 14 37 17 35 17 C33 17 27 14 25 8Z" fill="#f3d0d8" fill-opacity="0.55" stroke="#d8a7b1" stroke-width="2.5" stroke-linejoin="round"/>
            </svg>

            <!-- Glitter/sparkle besar — kanan tengah -->
            <svg class="tracking__deco-item tracking__deco--sparkle1" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 2 L22.5 17.5 L38 20 L22.5 22.5 L20 38 L17.5 22.5 L2 20 L17.5 17.5 Z" fill="#d8a7b1" fill-opacity="0.55"/>
            </svg>

            <!-- Sparkle kecil — kiri bawah -->
            <svg class="tracking__deco-item tracking__deco--sparkle2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 1 L13.5 10.5 L23 12 L13.5 13.5 L12 23 L10.5 13.5 L1 12 L10.5 10.5 Z" fill="#c67a89" fill-opacity="0.45"/>
            </svg>

            <!-- Hanger / gantungan baju — kiri tengah -->
            <svg class="tracking__deco-item tracking__deco--hanger" viewBox="0 0 80 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M40 6 C40 6 46 6 46 12 C46 16 42 18 40 20 L10 40 C6 42 6 48 10 50 L70 50 C74 48 74 42 70 40 Z" stroke="#d8a7b1" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="40" cy="6" r="4" stroke="#d8a7b1" stroke-width="2.5" fill="white" fill-opacity="0.5"/>
            </svg>

            <!-- Bubble besar — kanan bawah -->
            <svg class="tracking__deco-item tracking__deco--bubble1" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="30" cy="30" r="26" stroke="#d8a7b1" stroke-width="2.5" fill="#f3d0d8" fill-opacity="0.3"/>
                <circle cx="20" cy="20" r="5" fill="white" fill-opacity="0.5"/>
            </svg>

            <!-- Bubble kecil — tengah kiri -->
            <svg class="tracking__deco-item tracking__deco--bubble2" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="18" cy="18" r="15" stroke="#c67a89" stroke-width="2" fill="#f3d0d8" fill-opacity="0.25"/>
            </svg>

            <!-- Bubble mini — atas kanan -->
            <svg class="tracking__deco-item tracking__deco--bubble3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="#d8a7b1" stroke-width="2" fill="#f3d0d8" fill-opacity="0.35"/>
            </svg>
        </div>

        <div class="container">

            <div class="section-header" data-animate="fade-up">
                <span class="section-badge">Lacak Cucian</span>
                <h2 class="section-title">Pantau <span class="text-gradient">Status Cucian</span> Anda</h2>
                <p class="section-subtitle">Masukkan kode order Anda untuk mengetahui status cucian secara real-time.
                </p>
            </div>

            <div class="tracking__box" data-animate="fade-up">
                <div class="tracking__input-group">
                    <div class="tracking__input-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" id="trackInput" class="tracking__input"
                            placeholder="Masukkan kode order (cth: RL-1234)" autocomplete="off" />
                    </div>
                    <button class="btn btn--primary tracking__btn" id="trackBtn">
                        <i class="fas fa-location-arrow"></i> Lacak Sekarang
                    </button>
                </div>

                <!-- Loading -->
                <div class="tracking__loading" id="trackLoading">
                    <div class="tracking__spinner"></div>
                    <p>Sedang mencari data order Anda…</p>
                </div>

                <!-- Result -->
                <div class="tracking__result" id="trackResult">
                    <div class="tracking__result-header">
                        <div>
                            <h3 id="trackOrderId">Order #RL-1234</h3>
                            <p id="trackOrderDate">10 Februari 2026</p>
                        </div>
                        <span class="tracking__status-badge" id="trackStatusBadge">Diproses</span>
                    </div>

                    <div class="tracking__timeline" id="trackTimeline">
                        <!-- Step 1 -->
                        <div class="tracking__step completed">
                            <div class="tracking__step-icon"><i class="fas fa-clipboard-check"></i></div>
                            <div class="tracking__step-info">
                                <strong>Order Diterima</strong>
                                <span>Cucian sudah diterima di outlet</span>
                            </div>
                        </div>
                        
                        <!-- Step 2 -->
                        <div class="tracking__step active">
                            <div class="tracking__step-icon"><i class="fas fa-soap"></i></div>
                            <div class="tracking__step-info">
                                <strong>Diproses</strong>
                                <span>Cucian sedang dalam proses pencucian/setrika</span>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="tracking__step">
                            <div class="tracking__step-icon"><i class="fas fa-check-double"></i></div>
                            <div class="tracking__step-info">
                                <strong>Selesai</strong>
                                <span>Pesanan sudah selesai dan siap dijemput atau diantar</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Not Found -->
                <div class="tracking__not-found" id="trackNotFound">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Order Tidak Ditemukan</h3>
                    <p>Pastikan kode order yang Anda masukkan benar. Coba lagi atau hubungi kami.</p>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ CONTACT ═══════════════════ -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="contact__wrapper" data-animate="fade-up">

                <!-- ═══ LEFT — Info Section ═══ -->
                <div class="contact__info">
                    <span class="contact__badge">WE'RE HERE TO HELP YOU</span>
                    <h2 class="contact__title">Diskusikan Kebutuhan <br><span class="text-gradient">Laundry Anda</span></h2>
                    <p class="contact__desc">Apakah Anda mencari layanan laundry berkualitas tinggi yang disesuaikan dengan kebutuhan Anda? Hubungi kami sekarang.</p>

                    <div class="contact__details">
                        <div class="contact__item">
                            <div class="contact__icon">
                                <i class="fas fa-location-dot"></i>
                            </div>
                            <div class="contact__text">
                                <span>Alamat</span>
                                <p>Jl. P&K Lama, Sindangkerta, Kec. Lohbener, Kabupaten Indramayu, Jawa Barat 45252</p>
                            </div>
                        </div>

                        <div class="contact__item">
                            <div class="contact__icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="contact__text">
                                <span>WhatsApp</span>
                                <p>+62 812-3456-7890</p>
                            </div>
                        </div>

                        <div class="contact__item">
                            <div class="contact__icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact__text">
                                <span>E-mail</span>
                                <p>rumahlaundry@gmail.com</p>
                            </div>
                        </div>

                        <div class="contact__item">
                            <div class="contact__icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact__text">
                                <span>Jam Buka</span>
                                <p>Sen–Sab 07.00–21.00 &nbsp;|&nbsp; Min 08.00–18.00</p>
                            </div>
                        </div>
                    </div>

                    <a href="https://wa.me/6281234567890" class="contact__wa-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        <span>Chat WhatsApp Sekarang</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- ═══ RIGHT — Form Card ═══ -->
                <div class="contact__form-card">
                    <?php if (!isset($_SESSION['id'])): ?>
                    <!-- ── LOGIN PROMPT (belum login) ── -->
                    <div class="cfc__login-prompt">
                        <div class="cfc__lock-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3 class="cfc__prompt-title">Login Diperlukan</h3>
                        <p class="cfc__prompt-desc">Silakan login atau daftar terlebih dahulu untuk mengisi form pemesanan. Ini membantu kami memastikan keaslian pelanggan.</p>
                        <div class="cfc__prompt-buttons">
                            <a href="login.php" class="btn btn--primary cfc__prompt-btn">
                                <i class="fas fa-sign-in-alt"></i> Masuk
                            </a>
                            <a href="login.php#register" class="btn btn--outline cfc__prompt-btn" onclick="sessionStorage.setItem('toRegister','1')">
                                <i class="fas fa-user-plus"></i> Daftar
                            </a>
                        </div>
                        <p class="cfc__prompt-note"><i class="fas fa-shield-alt"></i> Data Anda aman dan terlindungi</p>
                    </div>
                    </div>
                    <?php else: ?>
                    <!-- ── FORM ORDER (sudah login) ── -->
                    <div id="formAlert" class="cfc__alert" style="display:none;"></div>

                    <form action="dashboard/proses_pesan.php" method="POST" class="cfc__form">
                        <div class="cfc__field">
                            <label for="cf_nama">Name</label>
                            <input id="cf_nama" type="text" name="nama" placeholder="Jane Smith" required value="<?= htmlspecialchars($_SESSION['nama'] ?? '') ?>" />
                        </div>

                        <div class="cfc__field">
                            <label for="cf_hp">Phone Number</label>
                            <input id="cf_hp" type="tel" name="no_hp" placeholder="08xxxxxxxxxx" required value="<?= htmlspecialchars($_SESSION['no_hp'] ?? '') ?>" />
                        </div>

                        <div class="cf-row-grid">
                            <div class="cfc__field">
                                <label for="cf_cuci">Jenis Pencucian</label>
                                <div class="cf-select-wrap">
                                    <select id="cf_cuci" name="jenis_pencucian" required>
                                        <option value="" disabled selected>Select...</option>
                                        <option value="Cuci Kering">Cuci Kering</option>
                                        <option value="Cuci Setrika">Cuci Setrika</option>
                                    </select>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="cfc__field">
                                <label for="cf_layanan">Jenis Layanan</label>
                                <div class="cf-select-wrap">
                                    <select id="cf_layanan" name="jenis_layanan" required>
                                        <option value="" disabled selected>Select...</option>
                                        <option value="Reguler">Reguler</option>
                                        <option value="Express">Express</option>
                                    </select>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <div class="cf-row-grid">
                            <div class="cfc__field">
                                <label for="cf_tanggal">Tanggal</label>
                                <input id="cf_tanggal" type="date" name="tanggal_pengiriman" required />
                            </div>
                            <div class="cfc__field">
                                <label for="cf_jam">Jam</label>
                                <input id="cf_jam" type="time" name="jam_pengiriman" required />
                            </div>
                        </div>

                        <!-- ═══ MAP PICKER ═══ -->
                        <div class="cfc__field">
                            <label><i class="fas fa-map-marker-alt" style="color:var(--accent,#c67a89);margin-right:4px;"></i> Pilih Lokasi di Peta</label>
                            <div id="map-picker-wrap" style="position:relative;border-radius:14px;overflow:hidden;border:2px solid #f0e4e7;box-shadow:0 2px 12px rgba(198,122,137,.08);">
                                <div id="orderMap" style="width:100%;height:280px;z-index:1;"></div>
                                <button type="button" id="btnGeolocate" style="position:absolute;top:12px;right:12px;z-index:999;background:#fff;border:2px solid #f0e4e7;border-radius:10px;padding:8px 14px;font-size:.82rem;font-weight:600;font-family:var(--font,'Inter',sans-serif);color:var(--accent,#c67a89);cursor:pointer;display:flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(0,0,0,.08);transition:all .25s ease;" onmouseover="this.style.background='var(--accent,#c67a89)';this.style.color='#fff';this.style.borderColor='var(--accent,#c67a89)';" onmouseout="this.style.background='#fff';this.style.color='var(--accent,#c67a89)';this.style.borderColor='#f0e4e7';">
                                    <i class="fas fa-crosshairs"></i> Lokasi Saya
                                </button>
                            </div>
                            <p style="font-size:.75rem;color:#9ca3af;margin-top:6px;"><i class="fas fa-info-circle"></i> Klik di peta atau geser pin untuk menentukan titik penjemputan.</p>
                            <input type="hidden" name="latitude" id="cf_lat" value="" />
                            <input type="hidden" name="longitude" id="cf_lng" value="" />
                        </div>

                        <div class="cfc__field">
                            <label for="cf_alamat">Alamat</label>
                            <textarea id="cf_alamat" name="alamat" rows="2" placeholder="Masukkan alamat lengkap..." required></textarea>
                        </div>

                        <div class="cfc__field">
                            <label for="cf_pesan">Message</label>
                            <textarea id="cf_pesan" name="pesan" rows="3" placeholder="Type your message"></textarea>
                        </div>

                        <button type="submit" class="contact__submit">
                            <div class="contact__submit-icon">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <span>Kirim</span>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- ═══════════════════ FOOTER ═══════════════════ -->
    <footer class="footer">
        <div class="container footer__inner">
            <div class="footer__brand">
                <a href="#home" class="navbar__brand"><img src="assets/img/RL.png" alt="Rumah Laundry" style="height:30px;width:auto;object-fit:contain;vertical-align:middle;margin-right:4px;"> Rumah Laundry</a>
                <p>Layanan laundry profesional yang mengutamakan kebersihan, kecepatan, dan kepuasan pelanggan.</p>
            </div>

            <div class="footer__links">
                <h4>Menu</h4>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#service">Service</a></li>
                    <li><a href="#review">Review</a></li>
                    <li><a href="#lacak">Lacak Cucian</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>

            <div class="footer__links">
                <h4>Layanan</h4>
                <ul>
                    <li><a href="#service">Laundry Kiloan</a></li>
                    <li><a href="#service">Laundry Satuan</a></li>
                    <li><a href="#service">Dry Cleaning</a></li>
                    <li><a href="#service">Antar Jemput</a></li>
                </ul>
            </div>

            <div class="footer__social">
                <h4>Ikuti Kami</h4>
                <div class="footer__social-icons">
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <div class="footer__bottom">
            <div class="container">
                <p>&copy; 2026 Rumah Laundry. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="script.js"></script>

    <!-- ═══ LEAFLET MAP INIT ═══ -->
    <script>
    (function() {
        var mapEl = document.getElementById('orderMap');
        if (!mapEl) return;

        // Default: Jakarta center
        var defaultLat = -6.2088, defaultLng = 106.8456;
        var map = L.map('orderMap', { scrollWheelZoom: false }).setView([defaultLat, defaultLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 19
        }).addTo(map);

        var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        function updateCoords(lat, lng) {
            document.getElementById('cf_lat').value = lat.toFixed(7);
            document.getElementById('cf_lng').value = lng.toFixed(7);
        }

        function reverseGeocode(lat, lng) {
            updateCoords(lat, lng);
            fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1', {
                headers: { 'Accept-Language': 'id' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data && data.display_name) {
                    document.getElementById('cf_alamat').value = data.display_name;
                }
            })
            .catch(function() {});
        }

        // Click on map
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });

        // Drag marker
        marker.on('dragend', function() {
            var pos = marker.getLatLng();
            reverseGeocode(pos.lat, pos.lng);
        });

        // Geolocation button
        document.getElementById('btnGeolocate').addEventListener('click', function() {
            var btn = this;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mencari...';
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    var lat = pos.coords.latitude, lng = pos.coords.longitude;
                    map.setView([lat, lng], 17);
                    marker.setLatLng([lat, lng]);
                    reverseGeocode(lat, lng);
                    btn.innerHTML = '<i class="fas fa-crosshairs"></i> Lokasi Saya';
                }, function() {
                    alert('Gagal mendapatkan lokasi. Pastikan GPS aktif.');
                    btn.innerHTML = '<i class="fas fa-crosshairs"></i> Lokasi Saya';
                }, { enableHighAccuracy: true, timeout: 10000 });
            } else {
                alert('Browser tidak mendukung geolokasi.');
                btn.innerHTML = '<i class="fas fa-crosshairs"></i> Lokasi Saya';
            }
        });

        // Fix map sizing on scroll into view
        var resizeObserver = new ResizeObserver(function() { map.invalidateSize(); });
        resizeObserver.observe(mapEl);
    })();
    </script>

    <!-- LOGOUT OVERLAY -->
    <div id="logout-overlay">
        <div class="card">
            <button class="close">×</button>

            <div class="illus-area">
                <svg class="illus-svg" width="220" height="110" viewBox="0 0 220 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <line x1="30" y1="90" x2="190" y2="90" stroke="#f0c8d8" stroke-width="1" stroke-linecap="round"/>
                    <rect x="140" y="30" width="44" height="60" rx="3" fill="white" stroke="#e8a8c0" stroke-width="1.5"/>
                    <rect x="146" y="38" width="14" height="18" rx="2" fill="none" stroke="#f0c0d4" stroke-width="1"/>
                    <rect x="166" y="38" width="12" height="18" rx="2" fill="none" stroke="#f0c0d4" stroke-width="1"/>
                    <rect x="146" y="62" width="32" height="20" rx="2" fill="none" stroke="#f0c0d4" stroke-width="1"/>
                    <circle cx="158" cy="63" r="2.5" fill="#e8a0b8"/>
                    <path d="M140 30 L128 36 L128 93 L140 90" fill="#fce8f0" stroke="#e8a8c0" stroke-width="1"/>
                    <ellipse cx="115" cy="91" rx="9" ry="3" fill="#f5d5e5" opacity="0.7"/>
                    <ellipse cx="96" cy="91" rx="7" ry="2.5" fill="#f5d5e5" opacity="0.5"/>
                    <ellipse cx="78" cy="91" rx="6" ry="2.5" fill="#f5d5e5" opacity="0.4"/>
                    <ellipse cx="112" cy="91" rx="10" ry="3" fill="#e8b0c8" opacity="0.25"/>
                    <path d="M106 70 Q110 84 104 89 Q112 87 120 89 Q114 84 118 70 Z" fill="#e8a0b8"/>
                    <rect x="107" y="57" width="10" height="16" rx="5" fill="#f2c0d0"/>
                    <circle cx="112" cy="51" r="7" fill="#f5d0be"/>
                    <path d="M105 49 Q112 40 119 49 Q117 44 112 43 Q107 44 105 49Z" fill="#8b4a6b"/>
                    <path d="M105 49 Q103 55 106 58" stroke="#8b4a6b" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                    <path d="M107 62 Q98 55 94 52" stroke="#f2c0d0" stroke-width="2.5" stroke-linecap="round" fill="none" class="rock"/>
                    <circle cx="94" cy="51" r="3" fill="#f2c0d0"/>
                    <path d="M117 62 Q120 67 119 71" stroke="#f2c0d0" stroke-width="2" stroke-linecap="round" fill="none"/>
                    <path d="M109 88 Q107 91 106 90" stroke="#e890b0" stroke-width="2" stroke-linecap="round" fill="none"/>
                    <path d="M115 88 Q116 91 117 90" stroke="#e890b0" stroke-width="2" stroke-linecap="round" fill="none"/>
                    <g class="petal-anim" style="transform-origin: 75px 72px">
                        <path d="M72 75 Q75 70 78 75 Q75 80 72 75Z" fill="#e8a0b8" opacity="0.8" transform="rotate(-20, 75, 75)"/>
                    </g>
                    <g class="petal-anim" style="transform-origin: 55px 68px">
                        <path d="M52 71 Q55 66 58 71 Q55 76 52 71Z" fill="#f0b8cc" opacity="0.7" transform="rotate(10, 55, 71)"/>
                    </g>
                    <g class="petal-anim" style="transform-origin: 88px 60px">
                        <path d="M85 63 Q88 58 91 63 Q88 68 85 63Z" fill="#d4889f" opacity="0.65" transform="rotate(-35, 88, 63)"/>
                    </g>
                    <circle cx="48" cy="78" r="5" fill="#e8a0b8" opacity="0.6"/>
                    <circle cx="46" cy="76" r="3" fill="#d4789a" opacity="0.7"/>
                    <path d="M48 83 L48 90" stroke="#b0d090" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M48 87 L44 85" stroke="#b0d090" stroke-width="1" stroke-linecap="round"/>
                    <circle cx="62" cy="82" r="4" fill="#f0b0c8" opacity="0.55"/>
                    <circle cx="60" cy="80" r="2.5" fill="#e08090" opacity="0.65"/>
                    <path d="M62 86 L62 90" stroke="#b0d090" stroke-width="1.5" stroke-linecap="round"/>
                    <circle cx="175" cy="62" r="3.5" fill="#e8a0b8" opacity="0.5"/>
                    <path d="M175 65 L175 72" stroke="#b0d090" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M175 69 L178 67" stroke="#b0d090" stroke-width="1" stroke-linecap="round"/>
                    <g class="shimmer">
                        <path d="M68 50 L69 53 L72 54 L69 55 L68 58 L67 55 L64 54 L67 53 Z" fill="#e8a0b8" opacity="0.6" transform="scale(0.7) translate(28, 18)"/>
                    </g>
                    <g class="shimmer" style="animation-delay:1s">
                        <path d="M155 42 L156 44 L158 45 L156 46 L155 48 L154 46 L152 45 L154 44 Z" fill="#d4889f" opacity="0.5" transform="scale(0.6)"/>
                    </g>
                </svg>
            </div>

            <p class="eyebrow">Keluar Akun</p>
            <h2 class="title">Kamu yakin ingin<br><em>meninggalkan kami?</em></h2>
            <p class="desc">Semua aktivitasmu telah tersimpan dengan aman. Kamu bisa kembali kapan saja — kami selalu di sini untukmu.</p>

            <div class="session">
                <div class="session-avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'S', 0, 1)) ?></div>
                <div>
                    <div class="session-name"><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></div>
                    <div class="session-role">Pengguna Aktif</div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="btn-row">
                <button class="btn-modal btn-ghost">Batal</button>
                <button class="btn-modal btn-confirm">Ya, Keluar</button>
            </div>
        </div>
    </div>

    <script>
        // Buka popup ketika tombol logout diklik
        document.querySelectorAll('.logout-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('logout-overlay').style.display = 'flex';
            });
        });

        // Tutup popup: tombol batal & tombol ×
        document.querySelectorAll('#logout-overlay .btn-ghost, #logout-overlay .close').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('logout-overlay').style.display = 'none';
            });
        });

        // Tutup popup: klik di luar card
        document.getElementById('logout-overlay').addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });

        // Konfirmasi logout: tombol Ya, Keluar
        document.querySelector('#logout-overlay .btn-confirm').addEventListener('click', function() {
            window.location.href = 'logout.php';
        });
    </script>
</body>

</html>

<?php include('crypt-start.php'); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="description"
        content="Kementerian Haji dan Umrah Kota Subulussalam" />
    <meta name="keywords"
        content="Kementerian Haji dan Umrah Kota Subulussalam" />
    <meta name="developer" content="Deni Anugrah" />
    <meta property="og:type" name="og:type" content="website">
    <meta property="og:url" name="og:url" content="logo.png" />
    <meta property="og:title" name="og:title"
        content="Kementerian Haji dan Umrah Kota Subulussalam" />
    <meta property="og:description" name="og:description"
        content="Kementerian Haji dan Umrah Kota Subulussalam" />
    <meta property="og:image" name="og:image" content="logo.png" />
    <meta property='og:locale' name="og:locale" content="id" />
    <meta name="google" content="notranslate" />
    <meta name="mobile-web-app-capable" content="yes" />
    <title>Kementerian Haji dan Umrah Kota Subulussalam</title>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Amiri:wght@400;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        :root {
            --green-dark: #072b1f;
            --green: #0b3d2e;
            --green-mid: #157347;
            --green-bright: #1a9e63;
            --gold: #c9a227;
            --gold-light: #e3c565;
            --gold-glow: #f0d580;
            --cream: #faf7ef;
            --white: #ffffff;
            --text-dark: #14231c;
            --text-muted: #5c6b64;
            --shadow-sm: 0 4px 12px rgba(11, 61, 46, 0.08);
            --shadow: 0 20px 50px -12px rgba(11, 61, 46, 0.25);
            --shadow-lg: 0 30px 70px -20px rgba(11, 61, 46, 0.4);
            --radius: 18px;
            --radius-sm: 12px;
            --transition: all 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-dark);
            background: var(--cream);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* ============ NAVBAR ============ */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: var(--transition);
            padding: 18px 0;
        }

        header.scrolled {
            background: rgba(7, 43, 31, 0.98);
            backdrop-filter: blur(16px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
            padding: 10px 0;
            border-bottom: 1px solid rgba(201, 162, 39, 0.2);
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--white);
            text-decoration: none;
            flex-shrink: 0;
        }

        /* PERUBAHAN: Logo menggunakan gambar */
        .brand-logo {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            transition: var(--transition);
            background: var(--white);
            padding: 2px;
            box-shadow: 0 0 0 3px rgba(201, 162, 39, 0.3);
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .brand:hover .brand-logo {
            transform: rotate(-8deg) scale(1.05);
            box-shadow: 0 0 0 5px rgba(201, 162, 39, 0.4);
        }

        .brand-text {
            line-height: 1.15;
        }

        .brand-text strong {
            display: block;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .brand-text span {
            font-size: 11px;
            color: var(--gold-light);
            font-weight: 500;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            position: relative;
            transition: color 0.3s;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gold);
            transition: width 0.3s;
        }

        .nav-links a:hover {
            color: var(--white);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        /* nav action group */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-login {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1.5px solid rgba(201, 162, 39, 0.5);
            color: var(--gold-light);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: var(--transition);
            backdrop-filter: blur(8px);
        }

        .btn-login:hover {
            background: rgba(201, 162, 39, 0.15);
            border-color: var(--gold);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(201, 162, 39, 0.25);
        }

        .btn-login i {
            font-size: 12px;
        }

        .nav-cta {
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: var(--green-dark) !important;
            padding: 11px 24px;
            border-radius: 50px;
            font-weight: 700 !important;
            font-size: 13px !important;
            transition: var(--transition) !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .nav-cta::after {
            display: none !important;
        }

        .nav-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(201, 162, 39, 0.4);
        }

        /* hamburger */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 6px;
            z-index: 1100;
        }

        .hamburger span {
            width: 26px;
            height: 2.5px;
            background: var(--white);
            border-radius: 2px;
            transition: all 0.35s ease;
            display: block;
        }

        .hamburger.active span:nth-child(1) {
            transform: translateY(7.5px) rotate(45deg);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: translateY(-7.5px) rotate(-45deg);
        }

        /* ============ HERO ============ */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background:
                linear-gradient(115deg, rgba(7, 43, 31, 0.96) 0%, rgba(15, 81, 50, 0.85) 55%, rgba(7, 43, 31, 0.78) 100%),
                url('https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?q=80&w=1920&auto=format&fit=crop') center/cover no-repeat;
            overflow: hidden;
            padding: 120px 0 90px;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(201, 162, 39, 0.14) 1px, transparent 1px);
            background-size: 34px 34px;
            opacity: 0.6;
            pointer-events: none;
        }

        .hero-glow {
            position: absolute;
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(201, 162, 39, 0.3), transparent 70%);
            border-radius: 50%;
            top: -120px;
            right: -100px;
            filter: blur(30px);
            animation: floatGlow 9s ease-in-out infinite;
        }

        .hero-glow.second {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(26, 158, 99, 0.5), transparent 70%);
            bottom: -140px;
            left: -120px;
            top: auto;
            right: auto;
            animation-delay: -4s;
        }

        @keyframes floatGlow {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(-30px, 30px) scale(1.08);
            }
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 56px;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(201, 162, 39, 0.45);
            color: var(--gold-light);
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 26px;
            backdrop-filter: blur(6px);
            animation: fadeUp 0.8s ease both;
        }

        .hero-badge i {
            font-size: 13px;
        }

        .hero h1 {
            font-size: clamp(2.2rem, 5vw, 3.9rem);
            line-height: 1.12;
            color: var(--white);
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 22px;
            animation: fadeUp 0.8s 0.1s ease both;
        }

        .hero h1 .accent {
            background: linear-gradient(100deg, var(--gold-light), var(--gold));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline;
        }

        .hero p.lead {
            color: rgba(255, 255, 255, 0.82);
            font-size: clamp(0.95rem, 1.6vw, 1.1rem);
            max-width: 540px;
            margin-bottom: 36px;
            animation: fadeUp 0.8s 0.2s ease both;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            animation: fadeUp 0.8s 0.3s ease both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 32px;
            border-radius: 50px;
            font-size: 14.5px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: var(--green-dark);
            box-shadow: 0 12px 30px -6px rgba(201, 162, 39, 0.55);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 40px -8px rgba(201, 162, 39, 0.7);
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.06);
            color: var(--white);
            border: 1.5px solid rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(8px);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: var(--gold);
            color: var(--gold-light);
            transform: translateY(-3px);
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 24px;
            padding: 34px 30px;
            backdrop-filter: blur(16px);
            box-shadow: 0 30px 60px -20px rgba(0, 0, 0, 0.5);
            animation: fadeUp 0.9s 0.35s ease both;
        }

        .hero-card h3 {
            color: var(--gold-light);
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.09);
        }

        .stat-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .stat-item:first-of-type {
            padding-top: 0;
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: rgba(201, 162, 39, 0.15);
            color: var(--gold-light);
            display: grid;
            place-items: center;
            font-size: 18px;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .stat-item:hover .stat-icon {
            background: rgba(201, 162, 39, 0.3);
            transform: scale(1.08);
        }

        .stat-info strong {
            display: block;
            color: var(--white);
            font-size: 22px;
            font-weight: 800;
            line-height: 1.1;
        }

        .stat-info span {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12.5px;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============ MARQUEE ============ */
        .marquee-bar {
            background: linear-gradient(90deg, var(--green-dark), var(--green-mid));
            padding: 14px 0;
            overflow: hidden;
            position: relative;
            border-top: 2px solid var(--gold);
            border-bottom: 2px solid var(--gold);
        }

        .marquee-track {
            display: flex;
            gap: 48px;
            width: max-content;
            animation: scrollX 32s linear infinite;
        }

        .marquee-track span {
            color: rgba(255, 255, 255, 0.8);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 12px;
            white-space: nowrap;
        }

        .marquee-track span i {
            color: var(--gold-light);
            font-size: 12px;
        }

        @keyframes scrollX {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        /* ============ SECTION UMUM ============ */
        section {
            padding: 100px 0;
            position: relative;
        }

        .section-head {
            text-align: center;
            max-width: 680px;
            margin: 0 auto 64px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--green-mid);
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .eyebrow::before,
        .eyebrow::after {
            content: '';
            width: 26px;
            height: 1.5px;
            background: var(--gold);
        }

        .section-head h2 {
            font-size: clamp(1.7rem, 3.5vw, 2.6rem);
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.2;
            margin-bottom: 16px;
            color: var(--green-dark);
        }

        .section-head p {
            color: var(--text-muted);
            font-size: 15.5px;
        }

        /* ============ LAYANAN ============ */
        .services {
            background: var(--white);
            position: relative;
        }

        .services::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--green-dark), var(--gold), var(--green-mid));
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 26px;
        }

        .service-card {
            background: var(--cream);
            border-radius: var(--radius);
            padding: 38px 30px;
            border: 1px solid rgba(11, 61, 46, 0.08);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .service-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(160deg, var(--green-dark), var(--green-mid));
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 0;
        }

        .service-card>* {
            position: relative;
            z-index: 1;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        .service-card:hover::after {
            opacity: 1;
        }

        .service-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(201, 162, 39, 0.18), rgba(201, 162, 39, 0.08));
            color: var(--green-mid);
            display: grid;
            place-items: center;
            font-size: 24px;
            margin-bottom: 24px;
            transition: var(--transition);
        }

        .service-card:hover .service-icon {
            background: rgba(201, 162, 39, 0.25);
            color: var(--gold-light);
            transform: rotate(-6deg) scale(1.05);
        }

        .service-card h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--green-dark);
            transition: color 0.4s;
        }

        .service-card p {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.7;
            transition: color 0.4s;
        }

        .service-card:hover h3 {
            color: var(--white);
        }

        .service-card:hover p {
            color: rgba(255, 255, 255, 0.78);
        }

        /* ============ ALUR ============ */
        .steps {
            background: var(--cream);
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            position: relative;
        }

        .step-card {
            text-align: center;
            padding: 20px 14px;
            position: relative;
        }

        .step-num {
            width: 64px;
            height: 64px;
            margin: 0 auto 22px;
            border-radius: 50%;
            background: var(--white);
            border: 2px solid rgba(201, 162, 39, 0.4);
            color: var(--green-dark);
            font-size: 22px;
            font-weight: 800;
            display: grid;
            place-items: center;
            position: relative;
            z-index: 2;
            box-shadow: 0 10px 24px -10px rgba(11, 61, 46, 0.35);
            transition: var(--transition);
        }

        .step-card:hover .step-num {
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            border-color: var(--gold);
            transform: scale(1.08);
        }

        .step-card h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--green-dark);
            margin-bottom: 8px;
        }

        .step-card p {
            font-size: 13.5px;
            color: var(--text-muted);
        }

        @media (min-width: 993px) {
            .steps-grid::before {
                content: '';
                position: absolute;
                top: 52px;
                left: 12%;
                width: 76%;
                height: 2px;
                background: repeating-linear-gradient(90deg, rgba(201, 162, 39, 0.55) 0 8px, transparent 8px 18px);
                z-index: 1;
            }
        }

        /* ============ CTA ============ */
        .cta-section {
            padding: 0 0 100px;
        }

        .cta-banner {
            position: relative;
            border-radius: 28px;
            overflow: hidden;
            background:
                linear-gradient(120deg, rgba(7, 43, 31, 0.96), rgba(21, 115, 71, 0.88)),
                url('https://images.unsplash.com/photo-1564769662533-4f00a87b4056?q=80&w=1600&auto=format&fit=crop') center/cover;
            padding: 70px 60px;
            text-align: center;
            box-shadow: var(--shadow-lg);
        }

        .cta-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(201, 162, 39, 0.18) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.5;
        }

        .cta-banner>* {
            position: relative;
            z-index: 1;
        }

        .cta-banner h2 {
            color: var(--white);
            font-size: clamp(1.6rem, 3.5vw, 2.4rem);
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 14px;
            line-height: 1.25;
        }

        .cta-banner h2 span {
            background: linear-gradient(100deg, var(--gold-light), var(--gold));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .cta-banner p {
            color: rgba(255, 255, 255, 0.8);
            max-width: 560px;
            margin: 0 auto 34px;
            font-size: 15px;
        }

        /* ============ BERITA ============ */
        .news {
            background: var(--white);
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .news-card {
            background: var(--cream);
            border-radius: var(--radius);
            overflow: hidden;
            transition: var(--transition);
            border: 1px solid rgba(11, 61, 46, 0.06);
        }

        .news-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
        }

        .news-thumb {
            height: 190px;
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        .news-thumb::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(7, 43, 31, 0.4), transparent);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .news-card:hover .news-thumb::after {
            opacity: 1;
        }

        .news-tag {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(7, 43, 31, 0.92);
            color: var(--gold-light);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 50px;
            backdrop-filter: blur(4px);
            z-index: 1;
        }

        .news-body {
            padding: 24px;
        }

        .news-date {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .news-date i {
            color: var(--gold);
        }

        .news-card h3 {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.45;
            color: var(--green-dark);
            margin-bottom: 12px;
        }

        .news-link {
            font-size: 13px;
            font-weight: 700;
            color: var(--green-mid);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: gap 0.3s, color 0.3s;
        }

        .news-link:hover {
            gap: 11px;
            color: var(--gold);
        }

        /* ============ FOOTER ============ */
        footer {
            background: var(--green-dark);
            color: rgba(255, 255, 255, 0.72);
            padding: 70px 0 0;
            position: relative;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light), var(--gold));
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 1.2fr;
            gap: 40px;
            padding-bottom: 50px;
        }

        .footer-brand .brand {
            margin-bottom: 18px;
        }

        .footer-brand p {
            font-size: 13.5px;
            line-height: 1.75;
            max-width: 300px;
            margin-bottom: 22px;
        }

        .socials {
            display: flex;
            gap: 10px;
        }

        .socials a {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.07);
            color: rgba(255, 255, 255, 0.8);
            display: grid;
            place-items: center;
            font-size: 15px;
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .socials a:hover {
            background: var(--gold);
            color: var(--green-dark);
            transform: translateY(-3px);
            border-color: var(--gold);
        }

        .footer-col h4 {
            color: var(--white);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 12px;
        }

        .footer-col h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 28px;
            height: 2px;
            background: var(--gold);
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 12px;
        }

        .footer-col ul a {
            color: rgba(255, 255, 255, 0.68);
            text-decoration: none;
            font-size: 13.5px;
            transition: color 0.3s, padding-left 0.3s;
        }

        .footer-col ul a:hover {
            color: var(--gold-light);
            padding-left: 5px;
        }

        .contact-item {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 13.5px;
            line-height: 1.6;
        }

        .contact-item i {
            color: var(--gold);
            font-size: 15px;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 22px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 12.5px;
        }

        .footer-bottom a {
            color: var(--gold-light);
            text-decoration: none;
        }

        /* ============ MODAL LOGIN ============ */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(7, 43, 31, 0.7);
            backdrop-filter: blur(8px);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-box {
            background: var(--white);
            border-radius: 24px;
            width: 100%;
            max-width: 440px;
            padding: 44px 38px 38px;
            position: relative;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.6);
            transform: translateY(30px) scale(0.96);
            transition: var(--transition);
            border: 1px solid rgba(201, 162, 39, 0.2);
            max-height: 92vh;
            overflow-y: auto;
        }

        .modal-overlay.active .modal-box {
            transform: translateY(0) scale(1);
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--cream);
            border: 1px solid rgba(11, 61, 46, 0.1);
            color: var(--text-muted);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 16px;
        }

        .modal-close:hover {
            background: var(--green-dark);
            color: var(--white);
            transform: rotate(90deg);
        }

        /* PERUBAHAN: Modal Icon menggunakan gambar logo */
        .modal-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: var(--white);
            padding: 4px;
            display: grid;
            place-items: center;
            box-shadow: 0 10px 30px -8px rgba(201, 162, 39, 0.5);
            border: 2px solid rgba(201, 162, 39, 0.3);
        }

        .modal-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .modal-box h2 {
            text-align: center;
            font-size: 22px;
            font-weight: 800;
            color: var(--green-dark);
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .modal-box .modal-subtitle {
            text-align: center;
            font-size: 13.5px;
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--green-dark);
            margin-bottom: 8px;
            letter-spacing: 0.2px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
            pointer-events: none;
            transition: color 0.3s;
        }

        .input-wrap input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            border: 1.5px solid rgba(11, 61, 46, 0.15);
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-family: inherit;
            color: var(--text-dark);
            background: var(--cream);
            transition: var(--transition);
            outline: none;
        }

        .input-wrap input:focus {
            border-color: var(--gold);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(201, 162, 39, 0.12);
        }

        .input-wrap input:focus~.input-icon,
        .input-wrap input:focus+.input-icon {
            color: var(--gold);
        }

        .input-wrap.password-wrap input {
            padding-right: 48px;
        }

        .toggle-password {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            display: grid;
            place-items: center;
            font-size: 15px;
            transition: var(--transition);
        }

        .toggle-password:hover {
            background: rgba(201, 162, 39, 0.12);
            color: var(--gold);
        }

        /* Captcha */
        .captcha-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--cream);
            border: 1.5px solid rgba(11, 61, 46, 0.15);
            border-radius: var(--radius-sm);
            padding: 14px 16px;
            margin-bottom: 10px;
            user-select: none;
        }

        .captcha-checkbox {
            width: 26px;
            height: 26px;
            border: 2px solid rgba(11, 61, 46, 0.3);
            border-radius: 6px;
            background: var(--white);
            cursor: pointer;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            transition: var(--transition);
            position: relative;
        }

        .captcha-checkbox i {
            font-size: 14px;
            color: var(--white);
            opacity: 0;
            transform: scale(0.3);
            transition: var(--transition);
        }

        .captcha-checkbox.checked {
            background: var(--green-bright);
            border-color: var(--green-bright);
        }

        .captcha-checkbox.checked i {
            opacity: 1;
            transform: scale(1);
        }

        .captcha-box.loading .captcha-checkbox {
            border-color: transparent;
            background: transparent;
        }

        .captcha-spinner {
            position: absolute;
            width: 22px;
            height: 22px;
            border: 2.5px solid rgba(11, 61, 46, 0.15);
            border-top-color: var(--gold);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            opacity: 0;
            transition: opacity 0.2s;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin-top: -2px;
        }

        .captcha-box.loading .captcha-spinner {
            opacity: 1;
        }

        @keyframes spin {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .captcha-text {
            flex: 1;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .captcha-text small {
            display: block;
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 2px;
        }

        .captcha-badge {
            font-size: 10px;
            color: var(--text-muted);
            text-align: right;
            line-height: 1.2;
            font-weight: 600;
        }

        .captcha-badge i {
            color: var(--green-bright);
            font-size: 12px;
            display: block;
            margin-bottom: 2px;
        }

        .captcha-error {
            font-size: 12px;
            color: #d64545;
            margin-top: -4px;
            margin-bottom: 10px;
            display: none;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .captcha-error.show {
            display: flex;
        }

        .modal-submit {
            width: 100%;
            padding: 15px;
            border-radius: var(--radius-sm);
            background: linear-gradient(135deg, var(--green-dark), var(--green-mid));
            color: var(--white);
            border: none;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 12px 30px -10px rgba(7, 43, 31, 0.6);
        }

        .modal-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 40px -12px rgba(7, 43, 31, 0.7);
            background: linear-gradient(135deg, var(--green-mid), var(--green-bright));
        }

        .modal-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .modal-footer {
            text-align: center;
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 20px;
        }

        .modal-footer a {
            color: var(--green-mid);
            font-weight: 700;
            text-decoration: none;
        }

        .modal-footer a:hover {
            color: var(--gold);
        }

        /* ============ CHAT AI ============ */
        .chat-fab {
            position: fixed;
            bottom: 92px;
            right: 26px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green-dark), var(--green-mid));
            color: var(--gold-light);
            border: 2px solid rgba(201, 162, 39, 0.4);
            cursor: pointer;
            display: grid;
            place-items: center;
            font-size: 24px;
            box-shadow: 0 12px 30px -6px rgba(7, 43, 31, 0.6);
            z-index: 1500;
            transition: var(--transition);
            animation: chatPulse 2.5s ease-in-out infinite;
        }

        .chat-fab:hover {
            transform: scale(1.08) rotate(-6deg);
            box-shadow: 0 18px 40px -8px rgba(7, 43, 31, 0.7);
        }

        @keyframes chatPulse {

            0%,
            100% {
                box-shadow: 0 12px 30px -6px rgba(7, 43, 31, 0.6), 0 0 0 0 rgba(201, 162, 39, 0.4);
            }

            50% {
                box-shadow: 0 12px 30px -6px rgba(7, 43, 31, 0.6), 0 0 0 14px rgba(201, 162, 39, 0);
            }
        }

        .chat-fab .chat-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 700;
            display: grid;
            place-items: center;
            border: 2px solid var(--cream);
        }

        .chat-window {
            position: fixed;
            bottom: 92px;
            right: 26px;
            width: 380px;
            max-width: calc(100vw - 40px);
            height: 540px;
            max-height: calc(100vh - 140px);
            background: var(--white);
            border-radius: 22px;
            box-shadow: 0 30px 80px -20px rgba(0, 0, 0, 0.4);
            z-index: 1600;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid rgba(201, 162, 39, 0.2);
            opacity: 0;
            visibility: hidden;
            transform: translateY(30px) scale(0.95);
            transform-origin: bottom right;
            transition: var(--transition);
        }

        .chat-window.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .chat-header {
            background: linear-gradient(135deg, var(--green-dark), var(--green-mid));
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--white);
            flex-shrink: 0;
        }

        .chat-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: var(--green-dark);
            display: grid;
            place-items: center;
            font-size: 18px;
            flex-shrink: 0;
            position: relative;
        }

        .chat-avatar::after {
            content: '';
            position: absolute;
            bottom: 1px;
            right: 1px;
            width: 11px;
            height: 11px;
            background: #22c55e;
            border: 2px solid var(--green-mid);
            border-radius: 50%;
        }

        .chat-header-info {
            flex: 1;
        }

        .chat-header-info strong {
            display: block;
            font-size: 14.5px;
            font-weight: 700;
        }

        .chat-header-info span {
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .chat-header-info span::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
        }

        .chat-close {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            border: none;
            color: var(--white);
            cursor: pointer;
            display: grid;
            place-items: center;
            font-size: 15px;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .chat-close:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: rotate(90deg);
        }

        .chat-body {
            flex: 1;
            overflow-y: auto;
            padding: 18px;
            background: var(--cream);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .chat-body::-webkit-scrollbar {
            width: 5px;
        }

        .chat-body::-webkit-scrollbar-thumb {
            background: rgba(11, 61, 46, 0.2);
            border-radius: 10px;
        }

        .chat-msg {
            display: flex;
            gap: 8px;
            max-width: 88%;
            animation: msgIn 0.35s ease both;
        }

        @keyframes msgIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chat-msg.bot {
            align-self: flex-start;
        }

        .chat-msg.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .chat-msg-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: var(--green-dark);
            display: grid;
            place-items: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .chat-msg.user .chat-msg-avatar {
            background: var(--green-mid);
            color: var(--white);
        }

        .chat-bubble {
            padding: 11px 15px;
            border-radius: 16px;
            font-size: 13.5px;
            line-height: 1.55;
            white-space: pre-line;
            box-shadow: var(--shadow-sm);
        }

        .chat-msg.bot .chat-bubble {
            background: var(--white);
            color: var(--text-dark);
            border-bottom-left-radius: 4px;
        }

        .chat-msg.user .chat-bubble {
            background: linear-gradient(135deg, var(--green-dark), var(--green-mid));
            color: var(--white);
            border-bottom-right-radius: 4px;
        }

        .typing-dots {
            display: inline-flex;
            gap: 4px;
            padding: 4px 0;
        }

        .typing-dots span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--text-muted);
            animation: typing 1.2s infinite;
        }

        .typing-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dots span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {

            0%,
            60%,
            100% {
                transform: translateY(0);
                opacity: 0.4;
            }

            30% {
                transform: translateY(-6px);
                opacity: 1;
            }
        }

        .chat-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            padding: 12px 18px 0;
            background: var(--cream);
        }

        .chat-suggestion {
            background: var(--white);
            border: 1px solid rgba(11, 61, 46, 0.15);
            color: var(--green-mid);
            padding: 7px 13px;
            border-radius: 50px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
        }

        .chat-suggestion:hover {
            background: var(--green-dark);
            color: var(--white);
            border-color: var(--green-dark);
        }

        .chat-footer {
            padding: 12px 14px;
            background: var(--white);
            border-top: 1px solid rgba(11, 61, 46, 0.08);
            display: flex;
            gap: 8px;
            align-items: center;
            flex-shrink: 0;
        }

        .chat-footer input {
            flex: 1;
            padding: 12px 16px;
            border: 1.5px solid rgba(11, 61, 46, 0.15);
            border-radius: 50px;
            font-size: 13.5px;
            font-family: inherit;
            background: var(--cream);
            outline: none;
            transition: var(--transition);
            min-width: 0;
        }

        .chat-footer input:focus {
            border-color: var(--gold);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(201, 162, 39, 0.12);
        }

        .chat-send {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: var(--green-dark);
            border: none;
            cursor: pointer;
            display: grid;
            place-items: center;
            font-size: 15px;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .chat-send:hover {
            transform: scale(1.08) rotate(-10deg);
            box-shadow: 0 6px 16px rgba(201, 162, 39, 0.4);
        }

        .chat-send:disabled {
            opacity: .65;
            cursor: wait;
            transform: none;
        }

        .chat-send.is-loading i {
            animation: chatSendSpin .9s linear infinite;
        }

        @keyframes chatSendSpin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ============ BACK TO TOP ============ */
        .back-to-top {
            position: fixed;
            bottom: 26px;
            right: 26px;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: var(--green-dark);
            border: none;
            cursor: pointer;
            display: grid;
            place-items: center;
            font-size: 18px;
            box-shadow: 0 12px 30px -6px rgba(201, 162, 39, 0.6);
            z-index: 1500;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: var(--transition);
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .back-to-top:hover {
            transform: translateY(-4px) scale(1.06);
            box-shadow: 0 18px 40px -8px rgba(201, 162, 39, 0.8);
        }

        .back-to-top i {
            transition: transform 0.3s;
        }

        .back-to-top:hover i {
            transform: translateY(-2px);
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 1024px) {
            .hero-inner {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .hero-card {
                max-width: 460px;
            }

            .services-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .news-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }

            .nav-links {
                gap: 18px;
            }

            .nav-links a {
                font-size: 13px;
            }
        }

        @media (max-width: 900px) {
            .nav-links {
                position: fixed;
                top: 0;
                right: 0;
                height: 100vh;
                width: 80%;
                max-width: 340px;
                background: var(--green-dark);
                flex-direction: column;
                justify-content: center;
                gap: 30px;
                transform: translateX(100%);
                transition: transform 0.45s cubic-bezier(0.65, 0, 0.35, 1);
                box-shadow: -20px 0 60px rgba(0, 0, 0, 0.4);
                padding: 40px 30px;
            }

            .nav-links.open {
                transform: translateX(0);
            }

            .nav-links a {
                font-size: 17px;
            }

            .hamburger {
                display: flex;
            }

            .btn-login {
                padding: 9px 16px;
                font-size: 12px;
            }

            .nav-cta {
                display: none;
            }

            .steps-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 34px;
            }

            .cta-banner {
                padding: 50px 28px;
            }
        }

        @media (max-width: 640px) {
            section {
                padding: 70px 0;
            }

            .container {
                padding: 0 18px;
            }

            .hero {
                padding: 110px 0 70px;
                min-height: auto;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p.lead {
                font-size: 0.92rem;
            }

            .hero-actions {
                gap: 12px;
            }

            .btn {
                width: 100%;
                padding: 14px 20px;
                font-size: 14px;
            }

            .hero-card {
                padding: 26px 22px;
            }

            .services-grid,
            .news-grid {
                grid-template-columns: 1fr;
            }

            .steps-grid {
                grid-template-columns: 1fr;
                gap: 28px;
            }

            .step-card {
                display: flex;
                text-align: left;
                gap: 18px;
                align-items: flex-start;
                padding: 0;
            }

            .step-num {
                margin: 0;
                flex-shrink: 0;
                width: 54px;
                height: 54px;
                font-size: 18px;
            }

            .step-card h3 {
                margin-top: 4px;
            }

            .cta-banner {
                padding: 42px 22px;
                border-radius: 20px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 34px;
            }

            .footer-bottom {
                justify-content: center;
                text-align: center;
            }

            .section-head {
                margin-bottom: 44px;
            }

            .modal-box {
                padding: 38px 24px 30px;
            }

            .chat-window {
                width: calc(100vw - 24px);
                right: 12px;
                bottom: 84px;
                height: calc(100vh - 110px);
                max-height: 620px;
            }

            .chat-fab {
                right: 18px;
                bottom: 84px;
                width: 54px;
                height: 54px;
                font-size: 21px;
            }

            .back-to-top {
                right: 18px;
                bottom: 18px;
                width: 46px;
                height: 46px;
                font-size: 16px;
            }

            .brand-text strong {
                font-size: 13.5px;
            }

            .brand-text span {
                font-size: 10px;
            }

            .brand-logo {
                width: 44px;
                height: 44px;
            }
        }

        @media (max-width: 380px) {
            .btn-login span {
                display: none;
            }

            .btn-login {
                padding: 10px 12px;
                border-radius: 50%;
            }
        }

        /* scroll reveal */
        .reveal {
            opacity: 0;
            transform: translateY(36px);
            transition: opacity 0.75s ease, transform 0.75s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* prevent body scroll when modal/chat open on mobile */
        body.no-scroll {
            overflow: hidden;
        }

        /* ============ LIVE DATABASE INDICATORS ============ */
        .hero-card h3 {
            flex-wrap: wrap;
        }

        .public-live-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
            padding: 5px 8px;
            border: 1px solid rgba(126, 239, 190, .25);
            border-radius: 30px;
            color: #b8f5d7;
            background: rgba(71, 204, 143, .12);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: none;
        }

        .public-live-pill>i {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #66e2a8;
            box-shadow: 0 0 0 3px rgba(102, 226, 168, .13);
        }

        .public-live-pill.is-error {
            color: #ffd0c7;
            border-color: rgba(255, 180, 160, .3);
            background: rgba(255, 100, 80, .1);
        }

        .public-live-pill.is-error>i {
            background: #ff9b83;
            box-shadow: 0 0 0 3px rgba(255, 155, 131, .13);
        }

        .public-metrics {
            position: relative;
            padding: 64px 0 70px;
            overflow: hidden;
            background: linear-gradient(180deg, #fff 0%, #f8fbf7 100%);
        }

        .public-metrics::before {
            position: absolute;
            top: -130px;
            right: -100px;
            width: 330px;
            height: 330px;
            border: 1px solid rgba(21, 115, 71, .07);
            border-radius: 50%;
            content: "";
            box-shadow: 0 0 0 34px rgba(21, 115, 71, .025), 0 0 0 70px rgba(21, 115, 71, .02);
            pointer-events: none;
        }

        .public-metrics .container {
            position: relative;
            z-index: 1;
        }

        .public-metrics-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .public-metrics-eyebrow {
            color: var(--green-mid);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
        }

        .public-metrics-heading h2 {
            margin: 5px 0 6px;
            color: var(--green-dark);
            font-size: clamp(23px, 3vw, 32px);
            font-weight: 800;
            letter-spacing: -.8px;
        }

        .public-metrics-heading p {
            margin: 0;
            color: var(--text-muted);
            font-size: 13px;
        }

        .public-metrics-updated {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border: 1px solid #e1ebe4;
            border-radius: 30px;
            color: #64746a;
            background: #fff;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
            box-shadow: var(--shadow-sm);
        }

        .public-metrics-updated i {
            color: var(--green-mid);
        }

        .public-metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .public-metric-card {
            display: flex;
            align-items: center;
            gap: 15px;
            min-height: 112px;
            padding: 20px;
            border: 1px solid #e3ebe5;
            border-radius: 16px;
            background: rgba(255, 255, 255, .92);
            box-shadow: 0 8px 24px rgba(11, 61, 46, .055);
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
        }

        .public-metric-card:hover {
            transform: translateY(-4px);
            border-color: #c2dfcc;
            box-shadow: 0 15px 32px rgba(11, 61, 46, .1);
        }

        .public-metric-icon {
            display: grid;
            place-items: center;
            flex: 0 0 46px;
            width: 46px;
            height: 46px;
            border-radius: 14px;
            font-size: 17px;
        }

        .public-metric-icon.pending {
            color: #a96c08;
            background: #fff4dc;
        }

        .public-metric-icon.followup {
            color: #bd7a15;
            background: #fff1df;
        }

        .public-metric-icon.complete {
            color: #087653;
            background: #e4f6eb;
        }

        .public-metric-card>div {
            display: grid;
            gap: 2px;
            min-width: 0;
        }

        .public-metric-card small {
            color: var(--text-muted);
            font-size: 10px;
            font-weight: 700;
        }

        .public-metric-card strong {
            color: var(--green-dark);
            font-size: 25px;
            font-weight: 800;
            line-height: 1.15;
        }

        .public-metric-card>div>span {
            color: #89958e;
            font-size: 10px;
        }

        @media(max-width:760px) {
            .public-metrics {
                padding: 48px 0 54px;
            }

            .public-metrics-grid {
                grid-template-columns: 1fr;
                gap: 11px;
            }

            .public-metric-card {
                min-height: 88px;
                padding: 15px 17px;
            }
        }

        @media(max-width:560px) {
            .public-metrics-heading {
                align-items: flex-start;
                flex-direction: column;
                gap: 13px;
            }

            .public-metrics-updated {
                font-size: 9px;
            }

            .public-live-pill {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <!-- ============ NAVBAR ============ -->
    <header id="header">
        <div class="container">
            <nav class="nav">
                <a href="#" class="brand">
                    <!-- PERUBAHAN: Logo menggunakan gambar yang diunggah -->
                    <div class="brand-logo">
                        <img src="logo.png"
                            alt="Logo Kementerian Haji dan Umrah">
                    </div>
                    <div class="brand-text">
                        <strong>Kemen Haji &amp; Umrah</strong>
                        <span>Kota Subulussalam</span>
                    </div>
                </a>

                <ul class="nav-links" id="navLinks">
                    <li><a href="#beranda">Beranda</a></li>
                    <li><a href="#layanan">Layanan</a></li>
                    <li><a href="#alur">Alur Pendaftaran</a></li>
                    <li><a href="#berita">Berita</a></li>
                </ul>

                <div class="nav-actions">
                    <button class="btn-login" id="btnLogin">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login</span>
                    </button>
                    <button class="hamburger" id="hamburger" aria-label="Menu">
                        <span></span><span></span><span></span>
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <!-- ============ HERO ============ -->
    <section class="hero" id="beranda">
        <div class="hero-glow"></div>
        <div class="hero-glow second"></div>

        <div class="container">
            <div class="hero-inner">
                <div>
                    <div class="hero-badge">
                        <i class="fa-solid fa-star"></i>
                        Pelayanan Haji &amp; Umrah Terpadu
                    </div>

                    <h1>
                        Wujudkan Niat Suci<br />
                        Menuju <span class="accent">Baitullah</span><br />
                        Bersama Kami
                    </h1>

                    <p class="lead">
                        Kementerian Haji dan Umrah Kota Subulussalam hadir memberikan pelayanan
                        pendaftaran, pembinaan, dan bimbingan manasik yang profesional, transparan,
                        dan amanah bagi seluruh calon jamaah.
                    </p>

                    <div class="hero-actions">
                        <a href="#layanan" class="btn btn-primary">
                            <i class="fa-solid fa-clipboard-check"></i>
                            Daftar Sekarang
                        </a>
                        <a href="#alur" class="btn btn-outline">
                            <i class="fa-solid fa-circle-info"></i>
                            Lihat Alur Pendaftaran
                        </a>
                    </div>
                </div>

                <div class="hero-card">
                    <h3><i class="fa-solid fa-chart-simple"></i> Data Pelayanan Terkini <span class="public-live-pill" id="publicStatsStatus"><i></i><span>Memuat</span></span></h3>

                    <div class="stat-item">
                        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                        <div class="stat-info">
                            <strong data-public-stat="jamaah_total">—</strong>
                            <span>Calon Jamaah Terdaftar</span>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="stat-info">
                            <strong data-public-stat="jamaah_terverifikasi">—</strong>
                            <span>Pendaftaran Terverifikasi</span>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon"><i class="fa-solid fa-inbox"></i></div>
                        <div class="stat-info">
                            <strong data-public-stat="surat_masuk">—</strong>
                            <span>Surat Masuk Tercatat</span>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon"><i class="fa-solid fa-paper-plane"></i></div>
                        <div class="stat-info">
                            <strong data-public-stat="surat_keluar">—</strong>
                            <span>Surat Keluar Tercatat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ RINGKASAN LAYANAN PUBLIK ============ -->
    <section class="public-metrics" aria-label="Ringkasan layanan terkini">
        <div class="container">
            <div class="public-metrics-heading reveal">
                <div>
                    <span class="public-metrics-eyebrow">TRANSPARANSI LAYANAN</span>
                    <h2>Informasi layanan terkini</h2>
                    <p>Ringkasan status pelayanan yang dihitung dari data sistem.</p>
                </div>
                <span class="public-metrics-updated" id="publicStatsUpdated"><i class="fa-regular fa-clock"></i><span>Memuat data...</span></span>
            </div>
            <div class="public-metrics-grid">
                <article class="public-metric-card reveal">
                    <span class="public-metric-icon pending"><i class="fa-regular fa-clock"></i></span>
                    <div><small>Menunggu verifikasi</small><strong data-public-stat="jamaah_pending">—</strong><span>Pendaftaran jamaah</span></div>
                </article>
                <article class="public-metric-card reveal">
                    <span class="public-metric-icon followup"><i class="fa-solid fa-list-check"></i></span>
                    <div><small>Perlu tindak lanjut</small><strong data-public-stat="surat_tindak_lanjut">—</strong><span>Arsip surat</span></div>
                </article>
                <article class="public-metric-card reveal">
                    <span class="public-metric-icon complete"><i class="fa-solid fa-circle-check"></i></span>
                    <div><small>Selesai bulan ini</small><strong data-public-stat="surat_selesai_bulan_ini">—</strong><span>Surat ditangani</span></div>
                </article>
            </div>
        </div>
    </section>

    <!-- ============ MARQUEE ============ -->
    <div class="marquee-bar">
        <div class="marquee-track">
            <span><i class="fa-solid fa-kaaba"></i> Pendaftaran Haji Reguler</span>
            <span><i class="fa-solid fa-star"></i> Bimbingan Manasik</span>
            <span><i class="fa-solid fa-passport"></i> Pengurusan Paspor</span>
            <span><i class="fa-solid fa-shield-halved"></i> Perlindungan Jamaah</span>
            <span><i class="fa-solid fa-heart-pulse"></i> Pemeriksaan Kesehatan</span>
            <span><i class="fa-solid fa-plane"></i> Keberangkatan Terjadwal</span>

            <span><i class="fa-solid fa-kaaba"></i> Pendaftaran Haji Reguler</span>
            <span><i class="fa-solid fa-star"></i> Bimbingan Manasik</span>
            <span><i class="fa-solid fa-passport"></i> Pengurusan Paspor</span>
            <span><i class="fa-solid fa-shield-halved"></i> Perlindungan Jamaah</span>
            <span><i class="fa-solid fa-heart-pulse"></i> Pemeriksaan Kesehatan</span>
            <span><i class="fa-solid fa-plane"></i> Keberangkatan Terjadwal</span>
        </div>
    </div>

    <!-- ============ LAYANAN ============ -->
    <section class="services" id="layanan">
        <div class="container">
            <div class="section-head reveal">
                <div class="eyebrow">Layanan Kami</div>
                <h2>Layanan Terpadu untuk Calon Jamaah</h2>
                <p>Berbagai layanan resmi yang kami sediakan untuk memudahkan proses ibadah haji dan umrah Anda.</p>
            </div>

            <div class="services-grid">
                <div class="service-card reveal">
                    <div class="service-icon"><i class="fa-solid fa-file-signature"></i></div>
                    <h3>Pendaftaran Haji</h3>
                    <p>Pendaftaran haji reguler dan khusus dengan proses cepat, transparan, dan sesuai ketentuan resmi.
                    </p>
                </div>

                <div class="service-card reveal">
                    <div class="service-icon"><i class="fa-solid fa-mosque"></i></div>
                    <h3>Pendaftaran Umrah</h3>
                    <p>Fasilitas pendaftaran umrah melalui travel resmi yang telah terverifikasi dan berizin.</p>
                </div>

                <div class="service-card reveal">
                    <div class="service-icon"><i class="fa-solid fa-book-open"></i></div>
                    <h3>Bimbingan Manasik</h3>
                    <p>Pembinaan dan bimbingan manasik haji serta umrah dengan pembimbing bersertifikat.</p>
                </div>

                <div class="service-card reveal">
                    <div class="service-icon"><i class="fa-solid fa-passport"></i></div>
                    <h3>Dokumen &amp; Paspor</h3>
                    <p>Pendampingan pengurusan dokumen perjalanan, paspor, dan kelengkapan administrasi jamaah.</p>
                </div>

                <div class="service-card reveal">
                    <div class="service-icon"><i class="fa-solid fa-heart-pulse"></i></div>
                    <h3>Pemeriksaan Kesehatan</h3>
                    <p>Pemeriksaan kesehatan dan vaksinasi sebagai syarat wajib keberangkatan jamaah.</p>
                </div>

                <div class="service-card reveal">
                    <div class="service-icon"><i class="fa-solid fa-headset"></i></div>
                    <h3>Konsultasi Jamaah</h3>
                    <p>Konsultasi gratis mengenai prosedur, biaya, jadwal, dan informasi seputar haji dan umrah.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ ALUR ============ -->
    <section class="steps" id="alur">
        <div class="container">
            <div class="section-head reveal">
                <div class="eyebrow">Alur Pendaftaran</div>
                <h2>Empat Langkah Menuju Tanah Suci</h2>
                <p>Proses pendaftaran yang mudah dan sistematis untuk kenyamanan calon jamaah.</p>
            </div>

            <div class="steps-grid">
                <div class="step-card reveal">
                    <div class="step-num">01</div>
                    <div>
                        <h3>Registrasi Awal</h3>
                        <p>Daftar online atau datang langsung ke kantor dengan membawa KTP &amp; KK.</p>
                    </div>
                </div>

                <div class="step-card reveal">
                    <div class="step-num">02</div>
                    <div>
                        <h3>Setor BIPH</h3>
                        <p>Lakukan setoran awal Biaya Perjalanan Ibadah Haji melalui bank syariah resmi.</p>
                    </div>
                </div>

                <div class="step-card reveal">
                    <div class="step-num">03</div>
                    <div>
                        <h3>Verifikasi Dokumen</h3>
                        <p>Lengkapi dan verifikasi seluruh dokumen persyaratan pendaftaran.</p>
                    </div>
                </div>

                <div class="step-card reveal">
                    <div class="step-num">04</div>
                    <div>
                        <h3>Manasik &amp; Berangkat</h3>
                        <p>Ikuti bimbingan manasik lalu bersiap untuk keberangkatan ke Tanah Suci.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ CTA ============ -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-banner reveal">
                <h2>Siap Memulai Perjalanan <span>Ibadah Anda?</span></h2>
                <p>Tim kami siap membantu Anda dari proses pendaftaran hingga keberangkatan. Hubungi kami hari ini untuk
                    informasi lengkap.</p>
                <a href="https://wa.me/6285283044848?text=Halo%2C%20saya%20ingin%20berkonsultasi%20terkait%20Haji%20%26%20Umrah"
                    target="_blank" class="btn btn-primary">
                    <i class="fa-brands fa-whatsapp"></i>
                    Konsultasi Gratis
                </a>
            </div>
        </div>
    </section>

    <!-- ============ BERITA ============ -->
    <section class="news" id="berita">
        <div class="container">
            <div class="section-head reveal">
                <div class="eyebrow">Informasi Terbaru</div>
                <h2>Berita &amp; Pengumuman</h2>
                <p>Ikuti perkembangan terbaru seputar layanan haji dan umrah di Kota Subulussalam.</p>
            </div>

            <div class="news-grid">
                <article class="news-card reveal">
                    <div class="news-thumb"
                        style="background-image:url('https://images.unsplash.com/photo-1580418827493-f2b22c0a76cb?q=80&w=800&auto=format&fit=crop')">
                        <span class="news-tag">Pengumuman</span>
                    </div>
                    <div class="news-body">
                        <div class="news-date"><i class="fa-regular fa-calendar"></i> 15 Januari 2025</div>
                        <h3>Pendaftaran Haji Reguler Tahun 1446 H Resmi Dibuka</h3>
                        <a href="#" class="news-link">Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="news-card reveal">
                    <div class="news-thumb"
                        style="background-image:url('https://images.unsplash.com/photo-1542816417-0983c9c9ad53?q=80&w=800&auto=format&fit=crop')">
                        <span class="news-tag">Kegiatan</span>
                    </div>
                    <div class="news-body">
                        <div class="news-date"><i class="fa-regular fa-calendar"></i> 10 Januari 2025</div>
                        <h3>Bimbingan Manasik Haji Batch 1 Sukses Digelar</h3>
                        <a href="#" class="news-link">Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="news-card reveal">
                    <div class="news-thumb"
                        style="background-image:url('https://images.unsplash.com/photo-1564769662533-4f00a87b4056?q=80&w=800&auto=format&fit=crop')">
                        <span class="news-tag">Informasi</span>
                    </div>
                    <div class="news-body">
                        <div class="news-date"><i class="fa-regular fa-calendar"></i> 5 Januari 2025</div>
                        <h3>Syarat Terbaru Dokumen Keberangkatan Jamaah Umrah</h3>
                        <a href="#" class="news-link">Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ============ FOOTER ============ -->
    <footer id="kontak">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="#" class="brand">
                        <!-- PERUBAHAN: Logo Footer -->
                        <div class="brand-logo">
                            <img src="logo.png"
                                alt="Logo Kementerian Haji dan Umrah">
                        </div>
                        <div class="brand-text">
                            <strong>Kemen Haji &amp; Umrah</strong>
                            <span>Kota Subulussalam</span>
                        </div>
                    </a>
                    <p>
                        Lembaga pelayanan haji dan umrah Kota Subulussalam yang berkomitmen
                        memberikan pelayanan terbaik, transparan, dan amanah bagi seluruh
                        calon jamaah.
                    </p>
                    <div class="socials">
                        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                        <a href="#" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Tautan Cepat</h4>
                    <ul>
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#layanan">Layanan</a></li>
                        <li><a href="#alur">Alur Pendaftaran</a></li>
                        <li><a href="#berita">Berita</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Layanan</h4>
                    <ul>
                        <li><a href="#">Pendaftaran Haji</a></li>
                        <li><a href="#">Pendaftaran Umrah</a></li>
                        <li><a href="#">Bimbingan Manasik</a></li>
                        <li><a href="#">Konsultasi Jamaah</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Kontak Kami</h4>
                    <div class="contact-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Jl. Merdeka No. 123, Kota Subulussalam, Aceh 24782</span>
                    </div>
                    <div class="contact-item">
                        <i class="fa-solid fa-phone"></i>
                        <span>(0652) 123-4567</span>
                    </div>
                    <div class="contact-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>info@kemenhaji-subulussalam.go.id</span>
                    </div>
                    <div class="contact-item">
                        <i class="fa-solid fa-clock"></i>
                        <span>Senin – Jumat, 08.00 – 16.00 WIB</span>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; 2025 Kementerian Haji dan Umrah Kota Subulussalam. Seluruh hak cipta dilindungi.</span>
                <span>Dibuat dengan <a href="#">♥</a> untuk jamaah</span>
            </div>
        </div>
    </footer>

    <!-- ============ MODAL LOGIN ============ -->
    <div class="modal-overlay" id="loginModal">
        <div class="modal-box">
            <button class="modal-close" id="modalClose" aria-label="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- PERUBAHAN: Modal Icon menggunakan logo -->
            <div class="modal-icon">
                <img src="logo.png" alt="Logo Kementerian Haji dan Umrah">
            </div>

            <!-- PERUBAHAN: Teks menjadi Login Administrator & Operator -->
            <h2>Kementerian Haji dan Umrah</h2>
            <p class="modal-subtitle">Silakan masuk untuk mengakses sistem pelayanan</p>

            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <input type="text" id="username" placeholder="Masukkan username" autocomplete="username"
                            required />
                        <i class="fa-solid fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap password-wrap">
                        <input type="password" id="password" placeholder="Masukkan password"
                            autocomplete="current-password" required />
                        <i class="fa-solid fa-lock input-icon"></i>
                        <button type="button" class="toggle-password" id="togglePassword" aria-label="Lihat password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Verifikasi Keamanan</label>
                    <div class="captcha-box" id="captchaBox">
                        <div class="captcha-checkbox" id="captchaCheckbox">
                            <i class="fa-solid fa-check"></i>
                            <div class="captcha-spinner"></div>
                        </div>
                        <div class="captcha-text">
                            Saya bukan robot
                            <small>Verifikasi keamanan</small>
                        </div>
                        <div class="captcha-badge">
                            <i class="fa-solid fa-shield-halved"></i>
                            Secure
                        </div>
                    </div>
                    <div class="captcha-error" id="captchaError">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Silakan verifikasi bahwa Anda bukan robot
                    </div>
                </div>

                <button type="submit" class="modal-submit" id="loginSubmit">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Masuk Sistem
                </button>
            </form>

            <div class="modal-footer">
                Lupa password? <a href="#">Hubungi IT Support</a>
            </div>
        </div>
    </div>

    <!-- ============ CHAT AI WIDGET ============ -->
    <button class="chat-fab" id="chatFab" aria-label="Buka Chat AI">
        <i class="fa-solid fa-robot"></i>
        <span class="chat-badge">1</span>
    </button>

    <div class="chat-window" id="chatWindow">
        <div class="chat-header">
            <div class="chat-avatar"><i class="fa-solid fa-robot"></i></div>
            <div class="chat-header-info">
                <strong>Asisten Haji AI</strong>
                <span>Panduan layanan &amp; cek status jamaah</span>
            </div>
            <button class="chat-close" id="chatClose" aria-label="Tutup chat">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="chat-body" id="chatBody">
            <div class="chat-msg bot">
                <div class="chat-msg-avatar"><i class="fa-solid fa-robot"></i></div>
                <div class="chat-bubble">
                    Assalamu'alaikum! 👋 Saya asisten layanan haji dan umrah. Saya bisa menjawab panduan layanan serta mengecek status pendaftaran dari database. Untuk privasi, pengecekan membutuhkan <strong>nama lengkap dan NIK atau nomor porsi</strong>.
                </div>
            </div>
        </div>

        <div class="chat-suggestions" id="chatSuggestions">
            <button class="chat-suggestion">Cek status jamaah</button>
            <button class="chat-suggestion">Syarat daftar haji</button>
            <button class="chat-suggestion">Alur pendaftaran</button>
            <button class="chat-suggestion">Jadwal manasik</button>
        </div>

        <div class="chat-footer">
            <input type="text" id="chatInput" maxlength="500" placeholder="Tulis pertanyaan atau cek status jamaah..." autocomplete="off" aria-label="Pesan untuk asisten" />
            <button class="chat-send" id="chatSend" type="button" aria-label="Kirim pesan">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- ============ BACK TO TOP ============ -->
    <button class="back-to-top" id="backToTop" aria-label="Kembali ke atas">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <script>
        /* ============ NAVBAR ============ */
        const header = document.getElementById('header');
        const backToTop = document.getElementById('backToTop');

        /* ============ LIVE DATABASE SUMMARY ============ */
        async function loadPublicDashboardStats() {
            const statusBadge = document.getElementById('publicStatsStatus');
            const updatedText = document.querySelector('#publicStatsUpdated span');
            const statElements = document.querySelectorAll('[data-public-stat]');
            const numberFormat = new Intl.NumberFormat('id-ID');

            try {
                const response = await fetch('api/public_dashboard.php', {
                    headers: {
                        'Accept': 'application/json'
                    },
                    cache: 'no-store'
                });
                const payload = await response.json();
                if (!response.ok || !payload || payload.status !== true || !payload.data) {
                    throw new Error('Ringkasan layanan tidak tersedia.');
                }

                statElements.forEach((element) => {
                    const value = Number(payload.data[element.dataset.publicStat]);
                    element.textContent = Number.isFinite(value) ? numberFormat.format(value) : '0';
                });

                const updatedAt = payload.updated_at ? new Date(payload.updated_at) : new Date();
                if (updatedText) {
                    updatedText.textContent = 'Diperbarui ' + new Intl.DateTimeFormat('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }).format(updatedAt);
                }
                if (statusBadge) {
                    statusBadge.classList.remove('is-error');
                    statusBadge.querySelector('span').textContent = 'Data langsung';
                }
            } catch (error) {
                statElements.forEach((element) => {
                    element.textContent = '—';
                });
                if (updatedText) updatedText.textContent = 'Data belum tersedia';
                if (statusBadge) {
                    statusBadge.classList.add('is-error');
                    statusBadge.querySelector('span').textContent = 'Tidak terhubung';
                }
            }
        }

        loadPublicDashboardStats();

        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 40);
            backToTop.classList.toggle('show', window.scrollY > 500);
        });

        /* ============ HAMBURGER ============ */
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('navLinks');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('open');
        });

        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navLinks.classList.remove('open');
            });
        });

        /* ============ SCROLL REVEAL ============ */
        const revealEls = document.querySelectorAll('.reveal');
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    setTimeout(() => entry.target.classList.add('visible'), i * 80);
                    io.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12
        });

        revealEls.forEach(el => io.observe(el));

        /* ============ MODAL LOGIN ============ */
        const loginModal = document.getElementById('loginModal');
        const btnLogin = document.getElementById('btnLogin');
        const modalClose = document.getElementById('modalClose');
        const loginForm = document.getElementById('loginForm');
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const captchaBox = document.getElementById('captchaBox');
        const captchaCheckbox = document.getElementById('captchaCheckbox');
        const captchaError = document.getElementById('captchaError');
        const loginSubmit = document.getElementById('loginSubmit');

        let isCaptchaVerified = false;

        function openModal() {
            loginModal.classList.add('active');
            document.body.classList.add('no-scroll');
        }

        function closeModal() {
            loginModal.classList.remove('active');
            document.body.classList.remove('no-scroll');
        }

        btnLogin.addEventListener('click', openModal);
        modalClose.addEventListener('click', closeModal);

        loginModal.addEventListener('click', (e) => {
            if (e.target === loginModal) closeModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && loginModal.classList.contains('active')) closeModal();
        });

        /* toggle lihat password */
        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            togglePassword.innerHTML = isPassword ?
                '<i class="fa-solid fa-eye-slash"></i>' :
                '<i class="fa-solid fa-eye"></i>';
            togglePassword.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Lihat password');
        });

        /* captcha */
        captchaCheckbox.addEventListener('click', () => {
            if (isCaptchaVerified || captchaBox.classList.contains('loading')) return;

            captchaBox.classList.add('loading');
            captchaError.classList.remove('show');

            // simulasi verifikasi
            setTimeout(() => {
                captchaBox.classList.remove('loading');
                captchaCheckbox.classList.add('checked');
                isCaptchaVerified = true;
            }, 800);
        });

        /* ============ SUBMIT FORM AJAX ============ */
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = passwordInput.value;

            // Validasi input dasar
            if (!username || !password) {
                alert('Mohon lengkapi username dan password.');
                return;
            }

            // Validasi Captcha
            if (!isCaptchaVerified) {
                captchaError.classList.add('show');
                captchaBox.style.borderColor = '#d64545';
                setTimeout(() => {
                    captchaBox.style.borderColor = '';
                }, 2000);
                return;
            }

            // Tampilkan loading state pada tombol
            const originalHTML = loginSubmit.innerHTML;
            loginSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
            loginSubmit.disabled = true;

            try {
                // Kirim data via AJAX (Fetch API) ke backend
                const response = await fetch('api/auth_proses.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        username: username,
                        password: password
                    })
                });

                // Cek apakah respons HTTP valid
                if (!response.ok) {
                    throw new Error('Terjadi kesalahan pada server (HTTP ' + response.status + ')');
                }

                const data = await response.json();

                if (data.status === true) {
                    // Jika sukses
                    loginSubmit.innerHTML = '<i class="fa-solid fa-check"></i> Berhasil!';

                    setTimeout(() => {
                        // Redirect ke halaman yang diberikan backend
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = 'page/index.php'; // Fallback
                        }
                    }, 1000);

                } else {
                    // Jika gagal (kredensial salah atau error lainnya)
                    alert(data.message || 'Login gagal. Silakan coba lagi.');

                    // Kembalikan tombol ke keadaan semula
                    loginSubmit.innerHTML = originalHTML;
                    loginSubmit.disabled = false;

                    // Reset Captcha jika login gagal (opsional, untuk keamanan)
                    captchaCheckbox.classList.remove('checked');
                    isCaptchaVerified = false;
                }

            } catch (error) {
                // Jika terjadi error jaringan atau parsing
                console.error('Error:', error);
                alert('Gagal terhubung ke server: ' + error.message);

                loginSubmit.innerHTML = originalHTML;
                loginSubmit.disabled = false;

                // Reset Captcha
                captchaCheckbox.classList.remove('checked');
                isCaptchaVerified = false;
            }
        });

        /* ============ CHAT AI ============ */
        const chatFab = document.getElementById('chatFab');
        const chatWindow = document.getElementById('chatWindow');
        const chatClose = document.getElementById('chatClose');
        const chatBody = document.getElementById('chatBody');
        const chatInput = document.getElementById('chatInput');
        const chatSend = document.getElementById('chatSend');
        const chatSuggestions = document.getElementById('chatSuggestions');

        function addMessage(text, sender) {
            const msg = document.createElement('div');
            msg.className = 'chat-msg ' + sender;
            const avatar = document.createElement('div');
            avatar.className = 'chat-msg-avatar';
            const icon = document.createElement('i');
            icon.className = sender === 'bot' ? 'fa-solid fa-robot' : 'fa-solid fa-user';
            avatar.appendChild(icon);
            const bubble = document.createElement('div');
            bubble.className = 'chat-bubble';
            bubble.textContent = text;
            msg.append(avatar, bubble);
            chatBody.appendChild(msg);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function showTyping() {
            const msg = document.createElement('div');
            msg.className = 'chat-msg bot';
            msg.id = 'typingMsg';
            const avatar = document.createElement('div');
            avatar.className = 'chat-msg-avatar';
            avatar.innerHTML = '<i class="fa-solid fa-robot"></i>';
            const bubble = document.createElement('div');
            bubble.className = 'chat-bubble';
            bubble.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
            msg.append(avatar, bubble);
            chatBody.appendChild(msg);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function hideTyping() {
            const typing = document.getElementById('typingMsg');
            if (typing) typing.remove();
        }

        async function sendMessage() {
            const text = chatInput.value.trim();
            if (!text || chatSend.disabled) return;

            addMessage(text, 'user');
            chatInput.value = '';
            chatSuggestions.style.display = 'none';
            showTyping();
            chatSend.disabled = true;
            chatSend.classList.add('is-loading');
            try {
                const response = await fetch('api/chat_assistant.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: text
                    })
                });
                const payload = await response.json();
                if (!response.ok && !payload.answer) throw new Error('Asisten tidak dapat dihubungi saat ini. Silakan coba lagi.');
                addMessage(payload.answer || 'Maaf, saya belum dapat menjawab pertanyaan tersebut.', 'bot');
            } catch (error) {
                addMessage(error.message || 'Koneksi asisten terganggu. Silakan coba kembali.', 'bot');
            } finally {
                hideTyping();
                chatSend.disabled = false;
                chatSend.classList.remove('is-loading');
                chatInput.focus();
            }
        }

        chatSend.addEventListener('click', sendMessage);
        chatInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') sendMessage();
        });

        chatSuggestions.querySelectorAll('.chat-suggestion').forEach(btn => {
            btn.addEventListener('click', () => {
                chatInput.value = btn.textContent;
                sendMessage();
            });
        });

        function openChat() {
            chatWindow.classList.add('open');
            chatFab.style.display = 'none';
            document.body.classList.add('no-scroll');
            setTimeout(() => chatInput.focus(), 300);
        }

        function closeChat() {
            chatWindow.classList.remove('open');
            chatFab.style.display = 'grid';
            document.body.classList.remove('no-scroll');
        }

        chatFab.addEventListener('click', openChat);
        chatClose.addEventListener('click', closeChat);

        /* ============ BACK TO TOP ============ */
        backToTop.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>

</html>
<?php include('crypt-end.php'); ?>
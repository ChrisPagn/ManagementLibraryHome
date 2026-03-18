<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ma Bibliothèque Personnelle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Lato', sans-serif;
            background: #1a0f0a;
            overflow: hidden;
            height: 100vh;
        }

        /* ── Image de fond ── */
        .library-bg {
            position: fixed;
            inset: 0;
            background:
                linear-gradient(
                    to bottom,
                    rgba(10, 5, 2, 0.55) 0%,
                    rgba(10, 5, 2, 0.30) 40%,
                    rgba(10, 5, 2, 0.70) 100%
                ),
                url('/images/library-bg-index.jpg')
                center center / cover no-repeat;
            filter: sepia(0.3) brightness(0.85);
            z-index: 0;
        }

        /* ── Cadre décoratif ── */
        .decorative-frame {
            position: fixed;
            inset: 16px;
            z-index: 1;
            pointer-events: none;
        }

        .frame-corner {
            position: absolute;
            width: 48px;
            height: 48px;
            border-color: #c9a96e;
            border-style: solid;
            opacity: 0.8;
        }
        .frame-corner-tl { top: 0; left: 0;  border-width: 2px 0 0 2px; }
        .frame-corner-tr { top: 0; right: 0; border-width: 2px 2px 0 0; }
        .frame-corner-bl { bottom: 0; left: 0;  border-width: 0 0 2px 2px; }
        .frame-corner-br { bottom: 0; right: 0; border-width: 0 2px 2px 0; }

        /* ── Lumière de lampe ── */
        .lamp-light {
            position: absolute;
            top: -60px;
            left: 50%;
            transform: translateX(-50%);
            width: 300px;
            height: 300px;
            background: radial-gradient(ellipse at top, rgba(255, 200, 80, 0.18) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── Contenu central ── */
        .loading-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            gap: 24px;
        }

        /* ── Titre ── */
        .loading-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 5vw, 3.5rem);
            color: #f5e6c8;
            text-align: center;
            letter-spacing: 0.05em;
            text-shadow: 0 2px 20px rgba(201, 169, 110, 0.4);
            animation: fadeInDown 1.2s ease forwards;
        }

        .loading-subtitle {
            font-family: 'Lato', sans-serif;
            font-size: 1rem;
            color: #c9a96e;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            animation: fadeInUp 1.4s ease forwards;
        }

        /* ── Points animés ── */
        .loading-dots {
            display: flex;
            gap: 8px;
            margin: 8px 0;
        }

        .loading-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #c9a96e;
            animation: dotPulse 1.4s ease-in-out infinite;
        }
        .loading-dot:nth-child(2) { animation-delay: 0.2s; }
        .loading-dot:nth-child(3) { animation-delay: 0.4s; }

        /* ── Barre de progression ── */
        .progress-bar-container {
            width: min(320px, 70vw);
            animation: fadeInUp 1.6s ease forwards;
        }

        .progress-track {
            height: 2px;
            background: rgba(201, 169, 110, 0.2);
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #c9a96e, #f5e6c8, #c9a96e);
            background-size: 200% 100%;
            border-radius: 2px;
            animation: progressLoad 2.5s ease forwards, shimmer 1.5s linear infinite;
        }

        /* ── Bouton accès admin ── */
        .admin-btn {
            position: fixed;
            bottom: 32px;
            right: 32px;
            z-index: 10;
            padding: 10px 24px;
            background: rgba(201, 169, 110, 0.15);
            border: 1px solid rgba(201, 169, 110, 0.5);
            color: #c9a96e;
            font-family: 'Lato', sans-serif;
            font-size: 0.8rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            text-decoration: none;
            border-radius: 2px;
            backdrop-filter: blur(8px);
            transition: all 0.3s ease;
            opacity: 0;
            animation: fadeIn 1s ease 3s forwards;
        }
        .admin-btn:hover {
            background: rgba(201, 169, 110, 0.3);
            border-color: #c9a96e;
            color: #f5e6c8;
        }

        /* ── Fade out au chargement ── */
        .loading-wrapper {
            transition: opacity 1s ease;
        }
        .loading-wrapper.fade-out {
            opacity: 0;
            pointer-events: none;
        }

        /* ── Animations ── */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes dotPulse {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
            40%            { transform: scale(1);   opacity: 1; }
        }
        @keyframes progressLoad {
            from { width: 0%; }
            to   { width: 100%; }
        }
        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body>

<div class="loading-wrapper" id="loadingWrapper">

    {{-- Fond bibliothèque --}}
    <div class="library-bg"></div>

    {{-- Cadre décoratif --}}
    <div class="decorative-frame">
        <div class="frame-corner frame-corner-tl"></div>
        <div class="frame-corner frame-corner-tr"></div>
        <div class="frame-corner frame-corner-bl"></div>
        <div class="frame-corner frame-corner-br"></div>
    </div>

    {{-- Contenu central --}}
    <div class="loading-content">
        <div class="lamp-light"></div>

        <div style="text-align:center;">
            <h2 class="loading-title">Bibliothèque Personnelle</h2>
            <p class="loading-subtitle">Préparation de votre collection</p>

            <div class="loading-dots" style="justify-content:center; margin-top:16px;">
                <div class="loading-dot"></div>
                <div class="loading-dot"></div>
                <div class="loading-dot"></div>
            </div>
        </div>

        <div class="progress-bar-container">
            <div class="progress-track">
                <div class="progress-fill"></div>
            </div>
        </div>
    </div>

</div>

{{-- Bouton accès admin (apparaît après le chargement) --}}
<a href="/admin" class="admin-btn">Accéder à la bibliothèque →</a>

<script>
    // Après 3s, fade out + redirection vers /admin
    window.addEventListener('load', function () {
        setTimeout(function () {
            const wrapper = document.getElementById('loadingWrapper');
            wrapper.classList.add('fade-out');

            // Redirige vers /admin après le fade
            setTimeout(() => {
                window.location.href = '/admin';
            }, 1000);
        }, 6000);
    });
</script>

</body>
</html>
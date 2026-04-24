<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spreading Smiles – Touching Lives</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --brand-orange: #ffb300;
            --deep-purple: #310D44;
            /* Darker for better contrast */
            --pastel-purple: #b388ff;
            --golden-yellow: #ffe0b2;
            --golden-yellow: #ffffff;
            --light-cream: #fffbe6;
            /* --light-cream: #fef3dc; */
            --eggshell: #fffaf0;
            --bright-accent: #f65c63;

            --brand-orange: #ffb300;
            --deep-purple: #310D44;
            --pastel-purple: #b388ff;
            --golden-yellow: #ffe0b2;
            --light-cream: #fffbe6;
            --eggshell: #fffaf0;
            --bright-accent: #f65c63;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
            color: var(--deep-purple);
        }

        .font-serif-display {
            font-family: 'Playfair Display', serif;
        }

        .main-container {
            /* background: radial-gradient(circle, var(--light-cream)0%, var(--golden-yellow)30%, var(--pastel-purple)60%, #5b187d)100%; */
            overflow: hidden;
            position: relative;
            background: radial-gradient(circle, var(--light-cream) 40%, var(--golden-yellow) 80%, var(--pastel-purple) 95%, #5b187d 100%);
        }

        .screen {
            min-height: 100vh;
            min-height: 100dvh;
            width: 100%;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out forwards;
            position: absolute;
            top: 0;
            left: 0;
            transition: opacity 0.5s ease-in-out, visibility 0.5s ease-in-out;
            opacity: 0;
            visibility: hidden;
        }

        .screen.active {
            display: flex;
            opacity: 1;
            visibility: visible;
            position: relative;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.98);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .content-card {
            background: rgba(255, 250, 240, 0.9);
            /* eggshell at 90% opacity */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--brand-orange);
            box-shadow: 0 8px 32px 0 rgba(91, 24, 125, 0.37);
            color: var(--deep-purple);


        }

        .cta-button {
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            background: var(--brand-orange);
            color: var(--deep-purple);
            font-weight: 700;
        }

        .cta-button:hover:not(:disabled) {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 8px 30px 0 rgba(0, 0, 0, 0.3);
            background: #ffc233;
        }

        .cta-button:active:not(:disabled) {
            transform: translateY(-1px) scale(1);
        }

        .cta-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .cta-button-secondary {
            background: transparent;
            border: 2px solid var(--eggshell);
            color: var(--eggshell);
        }

        .cta-button-secondary:hover {
            background: var(--eggshell);
            color: var(--deep-purple);
            border-color: var(--eggshell);
            box-shadow: 0 8px 30px rgba(255, 255, 255, 0.3);
        }

        .effects-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        #fireworks-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            opacity: 0.7;
        }

        #click-effect {
            position: fixed;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 223, 186, 0.8) 0%, rgba(255, 165, 0, 0) 70%);
            transform: translate(-50%, -50%) scale(0);
            pointer-events: none;
            z-index: 9999;
            opacity: 0;
        }

        #click-effect.animate {
            opacity: 1;
            transform: translate(-50%, -50%) scale(2.5);
            transition: transform 0.4s ease-out, opacity 0.4s ease-out;
        }



        /* --- New Diya Styles --- */
        #diya-interactive-wrapper {
            cursor: pointer;
        }

        #diya-interactive-wrapper .diya-flame {
            position: absolute;
            width: 40px;
            height: 60px;
            background: radial-gradient(circle, #FFFFFF, #FFD700 30%, #FCA510 80%);
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            left: 50%;
            transform: translateX(-50%);
            bottom: 58%;
            opacity: 0;
            transform-origin: bottom center;
            filter: blur(3px) drop-shadow(0 0 20px #FFC40E);
            z-index: 20;
        }

        #diya-interactive-wrapper.lit-effect .diya-flame {
            animation: igniteSimpleFlame 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards,
                flickerSimpleFlame 2.5s infinite linear 0.7s;
        }

        .diya-halo {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle, rgba(255, 204, 102, 0.6) 0%, rgba(255, 165, 0, 0) 70%);
            border-radius: 50%;
            transform: scale(0);
            opacity: 0;
            transition: transform 0.8s cubic-bezier(0.25, 0.8, 0.25, 1), opacity 0.8s ease-out;
            z-index: 10;
            pointer-events: none;
        }

        #diya-interactive-wrapper.lit-effect .diya-halo {
            transform: scale(3);
            opacity: 1;
        }

        @keyframes igniteSimpleFlame {
            0% {
                opacity: 0;
                transform: translateX(-50%) scaleY(0.2);
            }

            50% {
                opacity: 1;
                transform: translateX(-50%) scaleY(1.1);
            }

            100% {
                opacity: 1;
                transform: translateX(-50%) scaleY(1);
            }
        }

        @keyframes flickerSimpleFlame {

            0%,
            100% {
                transform: translateX(-50%) scaleY(1) skewX(0);
            }

            25% {
                transform: translateX(-50%) scaleY(1.05) skewX(2deg);
                opacity: 0.95;
            }

            75% {
                transform: translateX(-50%) scaleY(0.95) skewX(-2deg);
                opacity: 1;
            }
        }

        /* --- Map Styles --- */
        #india-map-container {
            position: relative;
            max-width: 350px;
            width: 90%;
            aspect-ratio: 1/1.1;
            margin: 0 auto;
            border-radius: 1rem;
            overflow: hidden;
        }

        .india-map-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #india-map-lit {
            clip-path: inset(100% 0 0 0);
            transition: clip-path 2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #india-map-container.is-lit #india-map-lit {
            clip-path: inset(0 0 0 0);
        }

        .error-message {
            color: var(--bright-accent);
            font-size: 0.75rem;
            font-weight: 600;
            text-align: left;
            width: 100%;
            padding-left: 0.5rem;
            min-height: 1.25rem;
            padding-top: 0.25rem;
        }

        .text-shadow {
            text-shadow: 0 1px 3px rgba(255, 255, 255, 0.4);
        }

        .form-input {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 250, 240, 0.5);
            color: var(--deep-purple);
        }

        .form-input::placeholder {
            color: rgba(49, 13, 68, 0.6);
        }

        .form-input:focus {
            --tw-ring-color: var(--brand-orange);
            border-color: var(--brand-orange);
        }

        .form-input-group {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 250, 240, 0.5);
        }

        .form-input-group:focus-within {
            --tw-ring-color: var(--brand-orange);
            border-color: var(--brand-orange);
        }

        .form-input-group span {
            color: var(--deep-purple);
        }

        .form-input-group input {
            color: var(--deep-purple);
        }

        .form-input-group input::placeholder {
            color: rgba(49, 13, 68, 0.6);
        }

        .animated-logo {
            animation: fadeInUp 0.8s ease-out 0.2s backwards, logo-glow 3s infinite ease-in-out 1s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes logo-glow {

            0%,
            100% {
                filter: drop-shadow(0 6px 12px rgba(91, 24, 125, 0.4));
            }

            50% {
                filter: drop-shadow(0 8px 25px rgba(255, 179, 0, 0.8));
            }
        }
    </style>
</head>

<body class="text-white">

    <div id="main-container" class="main-container min-h-screen w-full">
        <!-- Background effects -->
        <div id="effects-container" class="effects-container">
            <canvas id="fireworks-canvas"></canvas>
        </div>
        <div id="click-effect"></div>

        <!-- ========= SCREEN 1: EMPLOYEE LOGIN ========= -->
        <div id="screen-1" class="screen active relative z-10 flex-col">
            <div class="w-64 mb-8 animated-logo">
                <img src="{{ asset('assets/images/campaign_logo.png') }}" alt="Spreading Smiles"
                    class="w-full h-auto">
            </div>
            <div class=" rounded-3xl w-full max-w-sm flex flex-col items-center overflow-hidden">
                <div class="p-6 w-full text-center">
                    <p class="mt-4 text-base max-w-lg leading-relaxed mx-auto" style="color: var(--deep-purple);">Please
                        enter your Employee ID to begin.</p>
                    <form id="login-form" class="w-full flex flex-col items-center mt-6">
                        <input id="emp-id" type="text" inputmode="numeric"
                            class="form-input w-full p-3 rounded-xl border focus:ring-2 focus:outline-none text-base"
                            placeholder="Employee ID (Numbers Only)" required style="border: 2px solid  #b388ff;">
                        <p id="emp-id-error" class="error-message"></p>
                        <button type="submit" class="cta-button font-bold mt-3 py-3 px-8 rounded-full text-lg">
                            Proceed
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========= SCREEN 2: DOCTOR DETAILS ========= -->
        <div id="screen-2" class="screen relative z-10 flex-col">

            <div class=" rounded-3xl p-6 max-w-sm w-full flex flex-col items-center">
                <div class="w-64 mb-8 animated-logo">
                    <img src="{{ asset('assets/images/campaign_logo.png') }}" alt="Spreading Smiles"
                        class="w-full h-auto">
                </div>
                <p style="color: var(--deep-purple);" class="mt-4 text-base leading-relaxed text-shadow">Welcome,
                    Doctor! Please enter your details to
                    begin the campaign and help spread the light of learning to underprivileged children.</p>
                <form id="doctor-details-form" class="w-full flex flex-col items-center mt-6">
                    <div class="w-full">
                        <div style="border: 2px solid  #b388ff;"
                            class="form-input-group w-full flex items-center rounded-xl border focus-within:ring-2 transition-all duration-200">
                            <span class="pl-3 pr-2 font-semibold">Dr.</span>
                            <input id="doctor-name" type="text" class="flex-1 p-3 bg-transparent focus:outline-none"
                                placeholder="Name" required>
                        </div>
                        <p id="doctor-name-error" class="error-message"></p>
                    </div>

                    <div class="w-full">
                        <select id="doctor-specialty" style="border: 2px solid  #b388ff;"
                            class="form-input w-full p-3 rounded-xl border focus:ring-2 focus:outline-none text-base"
                            required>
                            <option value="" disabled selected>Select Specialty</option>
                            <option value="Ophthalmology">Ophthalmology</option>
                            <option value="Gynecology">Gynecology</option>
                            <option value="Nephrology">Nephrology</option>
                        </select>
                        <p id="doctor-specialty-error" class="error-message"></p>
                    </div>

                    <div class="w-full mt-2">
                        <label for="doctor-photo" style="border: 2px solid  #b388ff;"
                            class="form-input w-full p-3 rounded-xl border cursor-pointer flex items-center justify-between text-left">
                            <span id="photo-file-name" class="truncate text-gray-500">Upload Profile Photo</span>
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </label>
                        <input id="doctor-photo" type="file" class="hidden" accept="image/*">
                    </div>

                    <button type="submit" class="cta-button mt-4 font-bold py-3 px-8 rounded-full text-lg">
                        Continue
                    </button>
                </form>
            </div>
        </div>

        <!-- ========= SCREEN 3: ABOUT UDAAN ========= -->
        <div id="screen-3" class="screen relative z-10 flex-col">
            <div class="w-64 mb-8 animated-logo">
                <img src="{{ asset('assets/images/campaign_logo.png') }}" alt="Spreading Smiles"
                    class="w-full h-auto">
            </div>
            <div class=" rounded-3xl p-8 w-full max-w-sm flex flex-col items-center overflow-hidden">
                <!-- <h1 class="font-serif-display text-3xl sm:text-4xl font-bold tracking-tight text-shadow">Gifting
                    Education This Diwali</h1> -->
                <p style="color: var(--deep-purple);" class="mt-4 text-base leading-relaxed text-shadow">
                    <strong>Spreading Smiles</strong> is an initiative to provide
                    educational support to underprivileged children.
                </p>
                <p style="color: var(--deep-purple);" class="mt-6 text-base italic leading-relaxed  text-shadow">
                    Lighting one diya is a
                    contribution to one complete Educational Kit.</p>
                <button data-next="4" class="cta-button mt-8 font-bold py-3 px-8 rounded-full text-lg">
                    Continue
                </button>
            </div>
        </div>

        <!-- ========= SCREEN 4: LIGHT DIYA ========= -->
        <div id="screen-4" class="screen relative z-10">
            <div id="diya-lighting-card"
                class=" rounded-3xl p-8 max-w-sm w-full flex flex-col items-center transition-all duration-500 ease-in-out">
                <div class="w-64 mb-8 animated-logo">
                    <img src="{{ asset('assets/images/campaign_logo.png') }}" alt="Spreading Smiles"
                        class="w-full h-auto">
                </div>
                <div id="diya-lighting-state"
                    class="w-full flex flex-col items-center transition-opacity duration-500 ease-in-out">
                    <div id="diya-state-container" class="relative w-full">

                        <!-- <h1 style="color: var(--deep-purple);" class="font-serif-display text-3xl sm:text-4xl font-bold tracking-tight text-shadow">Light a
                            Diya, Share the Joy of Learning.</h1> -->
                        <p style="color: var(--deep-purple);" class="mt-4 text-base max-w-lg leading-relaxed mx-auto">
                            Light a
                            Diya, Share the Joy of Learning.</p>

                        <div id="diya-interactive-wrapper" class="relative mx-auto w-56 h-56 my-2">
                            <img src="https://placehold.co/250x250/FFFFFF/000000?text=Two+unprivileged+kids+studying"
                                alt="Watermark of kids studying"
                                class="absolute inset-0 w-full h-full object-cover rounded-full opacity-10 z-0">
                            <img id="diya-image" src="{{ asset('assets/images/diwali/Diya.png') }}" alt="Diya"
                                class="relative z-10 w-full h-full object-contain"
                                style="filter: drop-shadow(0 4px 10px rgba(0,0,0,0.5));">
                            <div class="diya-flame"></div>
                            <div class="diya-halo"></div>
                        </div>

                        <p style="color: var(--deep-purple);" class="text-sm leading-relaxed italic text-shadow">Let's
                            spread the light, one child at a time.</p>
                        <button id="light-diya-btn" class="cta-button mt-4 font-bold py-3 px-8 rounded-full text-lg">
                            Light a Diya
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========= SCREEN 5: GIFT KIT RECEIVED ========= -->
        <div id="screen-5" class="screen relative z-10 w-full">
            <div class="w-64 mb-8 animated-logo">
                <img src="{{ asset('assets/images/campaign_logo.png') }}" alt="Spreading Smiles"
                    class="w-full h-auto">
            </div>
            <div class=" rounded-3xl p-8 max-w-sm w-full flex flex-col items-center">
                <!-- <h2 style="color: var(--deep-purple);" class="font-serif-display text-3xl sm:text-4xl font-bold text-shadow">A Gift of Learning Delivered!
                </h2> -->
                <p style="color: var(--deep-purple);" class="mt-2 text-base leading-relaxed text-shadow">You're Donating
                    an Educational Kit to Support Their Education.</p>
                <img src="{{ asset('assets/images/diwali/gift.png') }}" alt="Educational Kit" class="my-4 rounded-lg ">
                <button data-next="6" class="cta-button mt-6 font-bold py-3 px-8 rounded-full text-lg">
                    Donate
                </button>
            </div>
        </div>

        <!-- ========= SCREEN 6: IMPACT MAP ========= -->
        <div id="screen-6" class="screen relative z-10 w-full">
            <div class="w-64 mb-8 animated-logo">
                <img src="{{ asset('assets/images/campaign_logo.png') }}" alt="Spreading Smiles"
                    class="w-full h-auto">
            </div>
            <div class=" rounded-3xl p-6 max-w-md w-full flex flex-col items-center">
                <h2 style="color: var(--deep-purple);" class="mt-2 text-base leading-relaxed text-shadow">Your Light is
                    Spreading Across India</h2>
                <div style="color: var(--deep-purple);" class="my-2 text-center">
                    <span class="text-lg font-semibold">Diyas Lit in Real-Time: </span>
                    @php
                        $totalDoctors = \App\Models\Doctors::count();
                        $displayCount = $totalDoctors + 1000;
                    @endphp

                    <span id="map-diya-counter" class="text-3xl font-bold font-serif-display">
                        {{ $displayCount }}
                    </span>
                </div>
                <div id="india-map-container" class="my-4">
                    <img src="{{ asset('assets/images/diwali/india1.png') }}" alt="Map of India" class="india-map-img">
                    <img id="india-map-lit" src="{{ asset('assets/images/diwali/India_map.png') }}"
                        alt="Illuminated Map of India" class="india-map-img">
                </div>
                <p style="color: var(--deep-purple);" class="mt-4 text-base leading-relaxed text-shadow">Your kindness
                    is reaching underprivileged children
                    in every corner of the country.</p>
                <button id="map-cta-button" data-next="7"
                    class="cta-button mt-4 font-bold py-3 px-3 rounded-full text-lg" disabled>
                    Receive Your Certificate
                </button>
            </div>
        </div>

        <div id="screen-7" class="screen relative z-10">
            <div class="rounded-3xl p-6 max-w-sm w-full flex flex-col items-center">
                <h1 class="mt-2 text-base leading-relaxed text-shadow" style="color: var(--deep-purple);">Thank You for
                    Your Support!</h1>
                <div id="certificate"
                    class="bg-amber-50 mt-3 rounded-2xl p-4 w-full text-center relative overflow-hidden shadow-md">
                    <div class="border-2 border-amber-300 rounded-lg p-4 flex flex-col items-center relative">
                        <div class="flex flex-col items-end w-full" id="a1">
                            <img src="{{ asset('assets/images/diwali/ajanta logo.png') }}" alt="Your Logo Here"
                                class="w-24 h-auto">
                        </div>

                        <div class="absolute top-0 left-0 w-full h-full bg-repeat bg-center opacity-[0.03]"
                            style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4MCIgaGVpZHRoPSI4MCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJjdXJyZW50Q2xvciI+PHBhdGggZD0iTTIxIDEyYTkgOSAwIDEwLTE4IDAgOS A5IDAgMDAxOCAwem0tMiAwYTYgNiAwIDExLTEyIDAgNiA2IDAgMDAxMiAweiIvPjwvc3ZnPg==');">
                        </div>

                        <div class="relative z-10 flex flex-col items-center w-full">
                            <div class="w-60 pb-lg-5">
                                <img src="{{ asset('assets/images/diwali/certificate_logo.png') }}" alt="Your Logo Here"
                                    class="w-full h-auto rounded-lg">
                            </div>

                            <p class="font-serif-display text-xl mt-2" style="color: var(--deep-purple);">Certificate of
                                Appreciation</p>
                            <div class="w-20 h-px my-2" style="background-color: var(--brand-orange);"></div>

                            <p class="text-sm" style="color: #6d483c;">Proudly Presented To</p>
                            <p id="certificate-name" class="font-serif-display text-2xl my-1 tracking-tight"
                                style="color: var(--deep-purple);">A Valued Supporter</p>

                            <p class="text-xs mt-2 text-slate-700 max-w-xs px-2 leading-relaxed">
                                For your generous spirit in lighting a diya of hope and giving wings to an unprivileged
                                child's dream through the Spreading Smiles initiative this Diwali.
                            </p>

                            <div class="mt-4 flex justify-between items-end w-full">
                                <div class="text-center">
                                    <p id="certificate-date" class="text-sm font-serif-display"
                                        style="color: var(--deep-purple);"></p>
                                    <p class="text-xs font-bold uppercase tracking-widest border-t-2 pt-1 mt-1"
                                        style="color: var(--deep-purple); border-color: var(--brand-orange);">Date</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-20  mx-auto relative pt-3">
                                        <img src="{{ asset('assets/images/diwali/Ihskas logo.png') }}"
                                            alt="Ihskas Foundation Logo"
                                            class="rounded-full w-full h-full object-cover">
                                    </div>
                                    <p class="text-xs font-bold uppercase tracking-widest border-t-2 pt-1 mt-1"
                                        style="color: var(--deep-purple); border-color: var(--brand-orange);">Ihskas
                                        Foundation</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button id="download-btn" class="cta-button mt-3 font-bold mb-3 py-3 px-8 rounded-full text-lg w-full">
                    Download Certificate
                </button>

                <button data-next="1" class="cta-button font-bold mb-3 py-3 px-8 rounded-full text-lg w-full">
                    Start Over
                </button>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const screens = document.querySelectorAll('.screen');
            const allButtons = document.querySelectorAll('button');
            const clickEffect = document.getElementById('click-effect');

            const loginForm = document.getElementById('login-form');
            const empIdInput = document.getElementById('emp-id');
            const certificateName = document.getElementById('certificate-name');
            const empIdError = document.getElementById('emp-id-error');

            const doctorDetailsForm = document.getElementById('doctor-details-form');
            const doctorNameInput = document.getElementById('doctor-name');
            const doctorSpecialtyInput = document.getElementById('doctor-specialty');
            const doctorNameError = document.getElementById('doctor-name-error');
            const doctorSpecialtyError = document.getElementById('doctor-specialty-error');
            const doctorPhotoInput = document.getElementById('doctor-photo');
            const photoFileName = document.getElementById('photo-file-name');


            const interactiveDiyaWrapper = document.getElementById('diya-interactive-wrapper');
            const lightDiyaBtn = document.getElementById('light-diya-btn');

            const downloadBtn = document.getElementById('download-btn');
            const certificateDate = document.getElementById('certificate-date');

            const indiaMapContainer = document.getElementById('india-map-container');
            const mapCtaButton = document.getElementById('map-cta-button');

            let diyaHasBeenLit = false;

            // --- Core Navigation Logic ---
            const showScreen = (screenNumber) => {
                screens.forEach(screen => screen.classList.remove('active'));
                const nextScreen = document.getElementById(`screen-${screenNumber}`);
                if (nextScreen) {
                    nextScreen.classList.add('active');
                    if (screenNumber === '6' && indiaMapContainer) {
                        mapCtaButton.disabled = true;
                        indiaMapContainer.classList.remove('is-lit');

                        setTimeout(() => {
                            indiaMapContainer.classList.add('is-lit');
                            triggerCelebratoryBurst();
                            const mapCounterElement = document.getElementById('map-diya-counter');
                            if (mapCounterElement) {
                                let currentCount = parseInt(mapCounterElement.textContent.replace(/,/g, ''), 10);
                                currentCount++;
                                mapCounterElement.textContent = currentCount.toLocaleString();
                                mapCounterElement.classList.add('burst');
                                mapCounterElement.addEventListener('animationend', () => {
                                    mapCounterElement.classList.remove('burst');
                                }, { once: true });
                            }
                        }, 500); // Delay before starting animation

                        setTimeout(() => {
                            mapCtaButton.disabled = false;
                        }, 2500); // Enable button after 2.5s (500ms delay + 2s transition)
                    }
                }
            };

            const handleNavigation = (e) => {
                const target = e.currentTarget;
                if (target.dataset.next) {
                    showClickEffect(e);
                    const nextScreenNumber = target.dataset.next;

                    if (nextScreenNumber === '1') { // Reset logic
                        diyaHasBeenLit = false;
                        lightDiyaBtn.disabled = false;

                        document.getElementById('diya-lighting-state').classList.remove('hidden');
                        document.getElementById('diya-lighting-state').style.opacity = '1';
                        document.getElementById('diya-lighting-state').style.pointerEvents = 'auto';
                        interactiveDiyaWrapper.classList.remove('lit-effect');

                        empIdInput.value = '';
                        doctorNameInput.value = '';
                        doctorSpecialtyInput.value = '';
                        if (indiaMapContainer) {
                            indiaMapContainer.classList.remove('is-lit');
                        }
                        certificateName.textContent = 'A Valued Supporter';
                        mapCtaButton.disabled = true;
                    }

                    showScreen(nextScreenNumber);
                }
            };

            document.querySelectorAll('button[data-next]').forEach(button => {
                button.addEventListener('click', handleNavigation);
            });

            const showClickEffect = (e) => {
                if (!clickEffect) return;
                clickEffect.style.left = `${e.clientX}px`;
                clickEffect.style.top = `${e.clientY}px`;
                clickEffect.classList.add('animate');
                setTimeout(() => clickEffect.classList.remove('animate'), 400);
            };
            allButtons.forEach(button => button.addEventListener('click', showClickEffect));

            // --- Validation Functions ---
            const validateInput = (input, regex, errorElement, errorMessage) => {
                const originalValue = input.value;
                const sanitizedValue = originalValue.replace(regex, '');
                if (originalValue !== sanitizedValue) {
                    errorElement.textContent = errorMessage;
                    input.value = sanitizedValue;
                    setTimeout(() => errorElement.textContent = '', 2000);
                } else {
                    errorElement.textContent = '';
                }
            };

            // --- Step 1: Login ---
            if (loginForm) {
                empIdInput.addEventListener('input', () => {
                    validateInput(empIdInput, /[^0-9]/g, empIdError, 'Only numbers are allowed.');
                });

                loginForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    if (empIdInput.value.trim() === '') {
                        empIdError.textContent = 'Employee Id is required.';
                        return;
                    }

                    empIdError.textContent = '';

                    try {
                        const response = await fetch("{{ route('validate.emp') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ emp_no: empIdInput.value.trim() })
                        });

                        const data = await response.json();

                        if (data.exists) {
                            showScreen('2'); // Only show screen 2 if employee exists
                        } else {
                            empIdError.textContent = 'Employee Id is incorrect.';
                            // Optional: shake input
                        }

                    } catch (err) {
                        console.error(err);
                        empIdError.textContent = 'Server error. Please try again.';
                    }
                });
            }


            // --- Step 2: Doctor Details ---
            if (doctorDetailsForm) {
                if (doctorPhotoInput) {
                    doctorPhotoInput.addEventListener('change', () => {
                        if (doctorPhotoInput.files.length > 0) {
                            photoFileName.textContent = doctorPhotoInput.files[0].name;
                            photoFileName.style.color = 'var(--deep-purple)';
                        } else {
                            photoFileName.textContent = 'Upload Profile Photo';
                        }
                    });
                }


                doctorNameInput.addEventListener('input', () => {
                    validateInput(doctorNameInput, /[^a-zA-Z. ]/g, doctorNameError, 'Only letters, spaces, and periods are allowed.');
                });

                doctorDetailsForm.addEventListener('submit', (e) => {
                    e.preventDefault();

                    let isValid = true;

                    // Validate Doctor Name
                    if (doctorNameInput.value.trim() === '') {
                        doctorNameError.textContent = "Doctor's name is required.";
                        isValid = false;
                    } else {
                        doctorNameError.textContent = '';
                    }

                    // Validate Specialty
                    if (doctorSpecialtyInput.value.trim() === '') {
                        doctorSpecialtyError.textContent = 'Specialty is required.';
                        isValid = false;
                    } else {
                        doctorSpecialtyError.textContent = '';
                    }

                    // Validate Profile Photo
                    // if (!doctorPhotoInput.files || doctorPhotoInput.files.length === 0) {
                    //     photoFileName.textContent = 'Profile image is required.';
                    //     photoFileName.style.color = 'red';
                    //     isValid = false;
                    // } else {
                    //     photoFileName.style.color = 'var(--deep-purple)';
                    // }

                    // Stop if any field is invalid
                    if (!isValid) return;

                    // All valid → set certificate name
                    const docName = doctorNameInput.value.trim();
                    if (certificateName && docName) {
                        certificateName.textContent = "Dr. " + docName;
                    } else if (certificateName) {
                        certificateName.textContent = 'A Valued Supporter';
                    }

                    // Move to screen 3
                    showScreen('3');
                });

            }

            // --- Step 4: Light Diya ---
            if (lightDiyaBtn) {
                lightDiyaBtn.addEventListener('click', (e) => {
                    if (diyaHasBeenLit) return;
                    diyaHasBeenLit = true;
                    showClickEffect(e);

                    interactiveDiyaWrapper.classList.add('lit-effect');
                    lightDiyaBtn.disabled = true;

                    setTimeout(() => {
                        showScreen('5');
                    }, 2000);
                });
            }

            // --- Step 6: Certificate ---
            if (certificateDate) {
                const today = new Date();
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                certificateDate.textContent = today.toLocaleDateString('en-US', options);
            }

            if (downloadBtn) {
                downloadBtn.addEventListener('click', () => {
                    const certificateElement = document.getElementById('certificate');
                    if (certificateElement) {
                        html2canvas(certificateElement, { scale: 3, useCORS: true, backgroundColor: '#FEFBF3' })
                            .then(canvas => {
                                const link = document.createElement('a');
                                link.download = `Pledge-Certificate-${certificateName.textContent.replace(/ /g, '_') || 'Supporter'}.png`;
                                link.href = canvas.toDataURL('image/png');
                                link.click();
                            }).catch(err => {
                                console.error('Error downloading certificate:', err);
                                const messageBox = document.createElement('div');
                                messageBox.textContent = 'Could not download the certificate. Please try again.';
                                messageBox.style.cssText = 'position:fixed; bottom:20px; left:50%; transform:translateX(-50%); background-color:var(--brand-maroon); color:white; padding:10px 20px; border-radius:8px; z-index:10000;';
                                document.body.appendChild(messageBox);
                                setTimeout(() => messageBox.remove(), 3000);
                            });
                    }
                });
            }

            // --- Background Fireworks Logic ---
            const canvas = document.getElementById('fireworks-canvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                let fireworks = [], particles = [], launchInterval;

                const setupCanvas = () => {
                    canvas.width = window.innerWidth;
                    canvas.height = window.innerHeight;
                };

                window.addEventListener('resize', setupCanvas);
                function random(min, max) { return Math.random() * (max - min) + min; }

                function Firework(sx, sy, tx, ty) {
                    this.x = sx; this.y = sy; this.sx = sx; this.sy = sy; this.tx = tx; this.ty = ty;
                    this.distanceToTarget = Math.sqrt(Math.pow(tx - sx, 2) + Math.pow(ty - sy, 2));
                    this.distanceTraveled = 0; this.coordinates = Array(3).fill([this.x, this.y]);
                    this.angle = Math.atan2(ty - sy, tx - sx);
                    this.speed = 2; this.acceleration = 1.05; this.brightness = random(50, 70);
                }
                Firework.prototype.update = function (index) {
                    this.coordinates.pop(); this.coordinates.unshift([this.x, this.y]);
                    this.speed *= this.acceleration;
                    let vx = Math.cos(this.angle) * this.speed; let vy = Math.sin(this.angle) * this.speed;
                    this.distanceTraveled = Math.sqrt(Math.pow(this.x - this.sx, 2) + Math.pow(this.y - this.sy, 2));
                    if (this.distanceTraveled >= this.distanceToTarget) {
                        createParticles(this.tx, this.ty, this.celebratory);
                        fireworks.splice(index, 1);
                    } else { this.x += vx; this.y += vy; }
                };
                Firework.prototype.draw = function () {
                    ctx.beginPath();
                    ctx.moveTo(this.coordinates[this.coordinates.length - 1][0], this.coordinates[this.coordinates.length - 1][1]);
                    ctx.lineTo(this.x, this.y);
                    ctx.strokeStyle = `hsl(${random(25, 55)}, 100%, ${this.brightness}%)`; ctx.stroke();
                };

                function Particle(x, y) {
                    this.x = x; this.y = y; this.coordinates = Array(5).fill([this.x, this.y]);
                    this.angle = random(0, Math.PI * 2); this.speed = random(1, 10);
                    this.friction = 0.95; this.gravity = 1; this.hue = random(25, 55);
                    this.brightness = random(50, 80); this.alpha = 1; this.decay = random(0.015, 0.03);
                }
                Particle.prototype.update = function (index) {
                    this.coordinates.pop(); this.coordinates.unshift([this.x, this.y]);
                    this.speed *= this.friction; this.x += Math.cos(this.angle) * this.speed;
                    this.y += Math.sin(this.angle) * this.speed + this.gravity;
                    this.alpha -= this.decay; if (this.alpha <= this.decay) particles.splice(index, 1);
                };
                Particle.prototype.draw = function () {
                    ctx.beginPath();
                    ctx.moveTo(this.coordinates[this.coordinates.length - 1][0], this.coordinates[this.coordinates.length - 1][1]);
                    ctx.lineTo(this.x, this.y);
                    ctx.strokeStyle = `hsla(${this.hue}, 100%, ${this.brightness}%, ${this.alpha})`; ctx.stroke();
                };

                function createParticles(x, y, celebratory = false) {
                    let particleCount = celebratory ? 200 : 80;
                    while (particleCount--) particles.push(new Particle(x, y));
                }

                function launchFireworks() { if (fireworks.length < 5) { let startX = canvas.width * random(0.4, 0.6); let startY = canvas.height; let targetX = random(0, canvas.width); let targetY = random(0, canvas.height / 2); fireworks.push(new Firework(startX, startY, targetX, targetY)); } }

                function triggerCelebratoryBurst() {
                    let burstCount = 15;
                    while (burstCount--) {
                        setTimeout(() => {
                            const startX = canvas.width * random(0.3, 0.7);
                            const startY = canvas.height;
                            const targetX = random(canvas.width * 0.2, canvas.width * 0.8);
                            const targetY = random(canvas.height * 0.1, canvas.height * 0.4);
                            const fw = new Firework(startX, startY, targetX, targetY);
                            fw.celebratory = true;
                            fireworks.push(fw);
                        }, random(0, 800));
                    }
                }
                function triggerSingleFirework() {
                    const startX = canvas.width * random(0.3, 0.7);
                    const startY = canvas.height;
                    const targetX = random(canvas.width * 0.2, canvas.width * 0.8);
                    const targetY = random(canvas.height * 0.1, canvas.height * 0.4);
                    const fw = new Firework(startX, startY, targetX, targetY);
                    fw.celebratory = true;
                    fireworks.push(fw);
                }


                function loop() {
                    requestAnimationFrame(loop);
                    ctx.globalCompositeOperation = 'destination-out'; ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
                    ctx.fillRect(0, 0, canvas.width, canvas.height); ctx.globalCompositeOperation = 'lighter';
                    fireworks.forEach((fw, i) => {
                        fw.draw();
                        fw.update(i);
                    });
                    particles.forEach((p, i) => { p.draw(); p.update(i); });
                }

                setupCanvas(); launchFireworks();
                launchInterval = setInterval(launchFireworks, 1200);
                loop();
            }
        });
    </script>

    <script>
        document.getElementById('doctor-details-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            const soid = document.getElementById('emp-id').value;
            const name = document.getElementById('doctor-name').value;
            const specialty = document.getElementById('doctor-specialty').value;
            const fileInput = document.getElementById('doctor-photo');

            const formData = new FormData();
            formData.append('soid', soid);
            formData.append('name', name);
            formData.append('specialty', specialty);
            if (fileInput.files.length > 0) {
                formData.append('profile_image', fileInput.files[0]);
            }

            try {
                const response = await fetch("{{ route('save.doctor') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        title: "Saved!",
                        text: data.message,
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Reset the form fields
                    document.getElementById('doctor-details-form').reset();
                    document.getElementById('photo-file-name').textContent = "Upload Profile Photo";
                } else {
                    Swal.fire("Error", data.message || "Something went wrong!", "error");
                }
            } catch (err) {
                console.error(err);
                Swal.fire("Error", "Failed to connect to server.", "error");
            }
        });

        // Show uploaded file name
        document.getElementById('doctor-photo').addEventListener('change', function () {
            const fileName = this.files[0] ? this.files[0].name : "Upload Profile Photo";
            document.getElementById('photo-file-name').textContent = fileName;
        });
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>HAIRSTYLE AI - 2026 트렌드 헤어 가상 체험</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        body {
            font-family: 'Pretendard', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .filled-icon {
            font-variation-settings: 'FILL' 1 !important;
        }

        .hidden {
            display: none !important;
        }

        /* Spinner */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 3px solid #fff;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }
    </style>
</head>

<body class="bg-[#F9F9F9] text-[#111] min-h-screen">

    <!-- Step 1: Upload Layout -->
    <div id="uploadView">
        <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100">
            <div class="max-w-screen-xl mx-auto flex items-center justify-between px-4 h-[52px]">
                <a href="/"
                    class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 active:opacity-50 transition-all">
                    <span class="material-symbols-outlined text-[22px]">arrow_back_ios_new</span>
                </a>
                <h1 class="text-[17px] font-bold tracking-tight">AI 헤어스타일 가상 체험</h1>
                <div class="w-10 h-10"></div> <!-- Placeholder for layout balance -->
            </div>
        </nav>
        <main class="max-w-md mx-auto px-4 pt-6 pb-24 md:max-w-2xl lg:max-w-4xl md:pt-16">
            <div class="md:text-center">
                <!-- Header Section -->
                <h2 class="text-3xl font-extrabold leading-tight tracking-tight mb-2 md:text-5xl md:mb-4">당신에게 어울리는<br>2026 트렌드 헤어</h2>
                <p class="text-slate-500 font-medium mb-8 md:text-lg">AI가 내 얼굴형과 이목구비에 맞는 20가지 헤어스타일을 실시간으로 분석해드립니다.</p>
            </div>

            <!-- Action Card -->
            <div
                class="bg-white rounded-3xl p-6 md:p-12 md:py-16 shadow-sm border border-slate-100 flex flex-col items-center justify-center min-h-[300px] text-center mb-6">
                <div id="previewContainer"
                    class="hidden relative w-32 h-32 rounded-full overflow-hidden mb-4 shadow-md border-4 border-slate-50">
                    <img id="previewImage" src="" alt="preview" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/10"></div>
                    <!-- Remove Image Button -->
                    <button onclick="removeImage()"
                        class="absolute top-1 right-1 bg-black/50 text-white w-6 h-6 rounded-full flex items-center justify-center backdrop-blur-md">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                    </button>
                </div>

                <div id="uploadPlaceholder">
                    <div
                        class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 mx-auto text-slate-400">
                        <span class="material-symbols-outlined text-[40px] font-light">face</span>
                    </div>
                    <h3 class="text-lg font-bold mb-1">정면 셀카 업로드</h3>
                    <p class="text-[13px] text-slate-500">이마와 얼굴형이 잘 보이는 사진이 좋습니다.</p>
                </div>

                <!-- Gender Buttons -->
                <div class="flex gap-2 w-full mt-6" id="actionButtons">
                    <div class="flex-1">
                        <input type="file" accept="image/*" id="fileInputFemale" class="hidden"
                            onchange="handleImageUpload(event, 'female')">
                        <button onclick="document.getElementById('fileInputFemale').click()"
                            class="w-full h-[52px] bg-slate-100 text-black rounded-full font-bold text-[15px] flex items-center justify-center gap-2 active:scale-95 transition-all outline-none border border-slate-200">
                            <span class="material-symbols-outlined text-[18px]">woman</span> 여성 스타일
                        </button>
                    </div>
                    <div class="flex-1">
                        <input type="file" accept="image/*" id="fileInputMale" class="hidden"
                            onchange="handleImageUpload(event, 'male')">
                        <button onclick="document.getElementById('fileInputMale').click()"
                            class="w-full h-[52px] bg-slate-100 text-black rounded-full font-bold text-[15px] flex items-center justify-center gap-2 active:scale-95 transition-all outline-none border border-slate-200">
                            <span class="material-symbols-outlined text-[18px]">man</span> 남성 스타일
                        </button>
                    </div>
                </div>
            </div>

            <button id="generateBtn" onclick="startGeneration()"
                class="w-full h-[56px] bg-black text-white rounded-full font-bold text-[15px] shadow-[0_8px_24px_rgba(0,0,0,0.15)] active:scale-95 transition-all outline-none flex items-center justify-center hidden">
                <span class="material-symbols-outlined mr-2">magic_button</span> AI 결과 확인하기
            </button>
        </main>
    </div>

    <!-- Step 2: Loading State -->
    <div id="loadingView"
        class="hidden fixed inset-0 z-50 bg-[#1A1A1A] text-white flex flex-col items-center justify-center">
        <!-- Starfield Animation BG -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-40">
            <div class="absolute w-2 h-2 bg-white rounded-full animate-ping" style="top: 20%; left: 30%;"></div>
            <div class="absolute w-1 h-1 bg-white rounded-full animate-ping"
                style="top: 60%; left: 80%; animation-delay: 0.5s;"></div>
            <div class="absolute w-1.5 h-1.5 bg-white rounded-full animate-ping"
                style="top: 80%; left: 20%; animation-delay: 1.2s;"></div>
            <div class="absolute w-2 h-2 bg-white rounded-full animate-ping"
                style="top: 40%; left: 70%; animation-delay: 0.8s;"></div>
        </div>

        <div class="relative z-10 flex flex-col items-center">
            <!-- Magic Circle Animation -->
            <div class="relative mb-10 w-32 h-32 flex items-center justify-center">
                <div
                    class="absolute inset-0 rounded-full border-t-2 border-r-2 border-l-2 border-[#135bec] opacity-80 animate-[spin_3s_linear_infinite]">
                </div>
                <div
                    class="absolute inset-2 rounded-full border-b-2 border-l-2 border-[#b5ccf8] opacity-60 animate-[spin_2s_linear_infinite_reverse]">
                </div>
                <div
                    class="absolute inset-6 rounded-full border-t-2 border-r-2 border-[#fff] opacity-40 animate-[spin_4s_linear_infinite]">
                </div>

                <div class="absolute w-20 h-20 rounded-full overflow-hidden border-2 border-white/20">
                    <img id="loadingThumb" src="" alt="Thumbnail" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-[#135bec]/20 mix-blend-overlay"></div>
                </div>

                <div
                    class="absolute w-[180%] h-[5px] bg-gradient-to-r from-transparent via-[#135bec] to-transparent animate-[spin_2s_ease-in-out_infinite]">
                </div>
            </div>

            <div
                class="flex items-center gap-3 bg-white/10 backdrop-blur-md px-5 py-2.5 rounded-full mb-6 border border-white/10 shadow-[0_0_30px_rgba(19,91,236,0.2)]">
                <span class="material-symbols-outlined text-[#135bec] animate-pulse">auto_awesome</span>
                <span class="text-[13px] font-bold tracking-wider text-slate-200">AI PROCESSING</span>
            </div>

            <h3 class="text-[26px] font-extrabold mb-3 text-center tracking-tight leading-tight">얼굴형 및 이목구비<br>정밀 분석 중
            </h3>
            <p class="text-[14px] text-slate-400 text-center font-medium max-w-[260px] leading-relaxed">
                AI가 사진을 스캔하여 가장 잘 어울리는<br>20가지 트렌드 헤어를 조합하고 있습니다.<br>
                <span class="text-white bg-white/10 px-2 py-0.5 rounded text-[12px] mt-2 inline-block">약 30~50초
                    소요됩니다</span>
            </p>
        </div>
    </div>

    <!-- Step 3: Result View -->
    <div id="resultView" class="hidden">
        <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100">
            <div class="max-w-screen-xl mx-auto flex items-center justify-between px-4 h-[52px]">
                <button onclick="resetApp()"
                    class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 active:opacity-50 transition-all">
                    <span class="material-symbols-outlined text-[22px]">arrow_back_ios_new</span>
                </button>
                <h1 class="text-[17px] font-bold tracking-tight">AI 분석 결과</h1>
                <div class="w-10 h-10"></div> <!-- Placeholder -->
            </div>
        </nav>
        <main class="max-w-md mx-auto md:max-w-5xl md:grid md:grid-cols-12 md:gap-10 md:pt-10 md:px-8">
            <section class="p-5 flex gap-4 items-center md:col-span-4 md:flex-col md:items-start md:p-0 md:sticky md:top-32 md:h-fit">
                <div class="relative w-[120px] md:w-full md:aspect-auto md:pb-4">
                    <div
                        class="w-full aspect-[3/4] rounded-2xl overflow-hidden shadow-sm border border-slate-200 bg-white">
                        <img id="resultThumb" alt="User original selfie" class="w-full h-full object-cover" src="">
                    </div>
                    <div
                        class="absolute -bottom-2 md:bottom-2 left-1/2 -translate-x-1/2 bg-black text-white text-[9px] md:text-[11px] px-3 py-1 rounded-full font-bold tracking-widest uppercase">
                        Original</div>
                </div>
                <div class="flex-1 md:w-full">
                    <div class="inline-flex items-center gap-1 text-[#135bec] mb-1 md:mb-2">
                        <span class="material-symbols-outlined text-[14px] md:text-[18px] filled-icon">verified</span>
                        <span class="text-[11px] md:text-[13px] font-bold uppercase tracking-wider">Analysis Complete</span>
                    </div>
                    <h2 class="text-[20px] md:text-[32px] font-extrabold leading-tight tracking-tight mb-2 md:mb-4">당신에게 어울리는<br>2026 트렌드 헤어
                    </h2>
                    <p class="text-[13px] md:text-[15px] text-slate-500 font-medium leading-snug mb-6 md:mb-10">분석 결과 이목구비에 가장 잘 어울리는 20가지 스타일링이
                        생성되었습니다.</p>
                        
                    <!-- Desktop buttons shown only on larger screens -->
                    <div class="hidden md:flex flex-col gap-3 w-full">
                        <button onclick="handleDownload()"
                            class="w-full bg-white text-black h-[52px] rounded-full flex items-center justify-center gap-2.5 font-bold text-[15px] border border-slate-200 shadow-sm hover:bg-slate-50 active:scale-95 transition-all">
                            <span class="material-symbols-outlined text-[20px]">download</span>
                            <span>전체 저장</span>
                        </button>
                        <button onclick="handleShare()"
                            class="w-full bg-black text-white h-[52px] border border-white/10 rounded-full flex items-center justify-center gap-2.5 font-bold text-[15px] hover:bg-zinc-800 active:scale-95 transition-all">
                            <span class="material-symbols-outlined text-[20px]">share</span>
                            <span>SNS 공유하기</span>
                        </button>
                    </div>
                </div>
            </section>

            <section class="px-2 pb-16 md:col-span-8 md:p-0">
                <div class="w-full relative shadow-[0_0_20px_rgba(0,0,0,0.05)] border border-slate-100 rounded-xl md:rounded-3xl overflow-hidden bg-white mb-6 md:mb-0">
                    <img id="generatedImg" alt="Generated Hairstyle" class="w-full h-auto object-cover" src="">
                </div>

                <!-- Mobile buttons shown only on smaller screens -->
                <div class="flex md:hidden justify-center gap-3 mb-8 w-full mt-4">
                    <button onclick="handleDownload()"
                        class="flex-1 bg-white text-black h-[52px] rounded-full flex items-center justify-center gap-2.5 font-bold text-[15px] border border-slate-200 active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-[20px]">download</span>
                        <span>전체 저장</span>
                    </button>
                    <button onclick="handleShare()"
                        class="flex-1 bg-black text-white h-[52px] border border-white/10 rounded-full flex items-center justify-center gap-2.5 font-bold text-[15px] active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-[20px]">share</span>
                        <span>SNS 공유하기</span>
                    </button>
                </div>
            </section>
        </main>
    </div>

    <script>
        let selectedBase64 = null;
        let selectedGender = 'male';
        let generatedUrl = null;

        // 이미지 압축 및 변환
        function fileToBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (event) => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        const MAX_WIDTH = 500;
                        const MAX_HEIGHT = 500;
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > MAX_WIDTH) { 
                                height = Math.round(height * MAX_WIDTH / width); 
                                width = MAX_WIDTH; 
                            }
                        } else {
                            if (height > MAX_HEIGHT) { 
                                width = Math.round(width * MAX_HEIGHT / height); 
                                height = MAX_HEIGHT; 
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;

                        const ctx = canvas.getContext('2d');
                        // 모바일 투명도 오류 방지용 흰색 배경 채우기
                        ctx.fillStyle = '#FFFFFF';
                        ctx.fillRect(0, 0, width, height);
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // 구글 AI 처리 속도 향상 및 타임아웃 방지를 위해 압축률 증가 (용량 대폭 축소)
                        resolve(canvas.toDataURL('image/jpeg', 0.5));
                    };
                    img.onerror = reject;
                };
                reader.onerror = reject;
            });
        }

        async function handleImageUpload(e, gender) {
            const file = e.target.files[0];
            if (!file) return;

            selectedGender = gender;
            try {
                selectedBase64 = await fileToBase64(file);
                document.getElementById('previewImage').src = selectedBase64;
                document.getElementById('uploadPlaceholder').classList.add('hidden');
                document.getElementById('actionButtons').classList.add('hidden');
                document.getElementById('previewContainer').classList.remove('hidden');

                const generateBtn = document.getElementById('generateBtn');
                generateBtn.classList.remove('hidden');
                if (gender === 'female') {
                    generateBtn.innerHTML = '<span class="material-symbols-outlined mr-2">woman</span> 여성 스타일 생성하기';
                } else {
                    generateBtn.innerHTML = '<span class="material-symbols-outlined mr-2">man</span> 남성 스타일 생성하기';
                }
            } catch (error) {
                alert("이미지 처리 중 오류가 발생했습니다.");
            }
        }

        function removeImage() {
            selectedBase64 = null;
            document.getElementById('previewImage').src = '';
            document.getElementById('uploadPlaceholder').classList.remove('hidden');
            document.getElementById('actionButtons').classList.remove('hidden');
            document.getElementById('previewContainer').classList.add('hidden');
            document.getElementById('generateBtn').classList.add('hidden');
        }

        async function startGeneration() {
            if (!selectedBase64) return;

            document.getElementById('loadingThumb').src = selectedBase64;
            document.getElementById('uploadView').classList.add('hidden');
            document.getElementById('loadingView').classList.remove('hidden');

            try {
                const formData = new FormData();
                formData.append("image", selectedBase64);
                formData.append("gender", selectedGender);

                const res = await fetch('api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await res.json();

                if (!result.success) {
                    if (result.error.includes("보안 정책") || result.error.includes("거부")) {
                        throw new Error(result.error);
                    } else {
                        throw new Error("서버 에러: " + (result.error || "알 수 없는 오류"));
                    }
                }

                generatedUrl = result.image_url;
                showResult();
            } catch (error) {
                console.error(error);
                alert("이미지 생성 중 오류가 발생했습니다.\n" + error.message);
                resetApp();
            }
        }

        function showResult() {
            document.getElementById('loadingView').classList.add('hidden');
            document.getElementById('resultView').classList.remove('hidden');
            document.getElementById('resultThumb').src = selectedBase64;
            document.getElementById('generatedImg').src = generatedUrl;
        }

        function resetApp() {
            document.getElementById('resultView').classList.add('hidden');
            document.getElementById('loadingView').classList.add('hidden');
            document.getElementById('uploadView').classList.remove('hidden');
            removeImage();
        }

        async function handleDownload() {
            const isMobile = /iPhone|iPad|iPod|Android|webOS|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            try {
                const response = await fetch(generatedUrl);
                const blob = await response.blob();

                if (isMobile && navigator.canShare) {
                    const file = new File([blob], 'hairstyle_ai_result.png', { type: blob.type || 'image/png' });
                    if (navigator.canShare({ files: [file] })) {
                        try {
                            await navigator.share({
                                files: [file],
                                title: 'HAIRSTYLE AI - 나만의 결과 저장하기'
                            });
                            return;
                        } catch (shareErr) { }
                    }
                }

                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `hairstyle_ai_2026_trend_${Date.now()}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);

                if (isMobile) {
                    setTimeout(() => {
                        alert("혹시 자동 다운로드가 안 되셨나요?\n\n모바일 인앱 브라우저나 특정 환경에서는 파일 자동 저장이 차단되어 있을 수 있습니다.\n\n화면에 보이는 사진을 손가락으로 '길게 꾹' 누르시면 [내 사진첩에 저장]을 선택하실 수 있습니다! 📸");
                    }, 500);
                }
            } catch (error) {
                console.error("다운로드 오류:", error);
                alert("이미지를 정상적으로 다운로드하지 못했습니다. 화면의 이미지를 '길게 꾹' 눌러 저장해주세요.");
            }
        }

        async function handleShare() {
            try {
                if (navigator.share) {
                    let shareData = {
                        title: 'HAIRSTYLE AI - 2026 트렌드 헤어',
                        text: '나에게 어울리는 20가지 헤어스타일을 생성해봤어요! 지금 바로 확인해보세요.',
                        url: window.location.href
                    };

                    try {
                        const response = await fetch(generatedUrl);
                        const blob = await response.blob();
                        const file = new File([blob], 'hairstyle_ai_result.png', { type: blob.type || 'image/png' });

                        if (navigator.canShare && navigator.canShare({ files: [file] })) {
                            shareData.files = [file];
                        }
                    } catch (e) {
                        console.warn("파일 첨부 실패:", e);
                    }

                    await navigator.share(shareData);
                } else {
                    await navigator.clipboard.writeText(window.location.href);
                    alert("현재 페이지 링크가 복사되었습니다! 원하는 곳에 붙여넣어 공유해주세요.");
                }
            } catch (error) {
                if (error.name !== 'AbortError') {
                    try {
                        await navigator.clipboard.writeText(window.location.href);
                        alert("링크가 클립보드에 복사되었습니다!");
                    } catch (e) {
                        alert("지원하지 않는 환경입니다.");
                    }
                }
            }
        }
    </script>
</body>

</html>
<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted,onUnmounted } from 'vue'
import { GeminiApiStore } from '@/Stores/GeminiApiStore'

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
    laravelVersion: { type: String, required: true },
    phpVersion: { type: String, required: true },
});

const GeminiApi = GeminiApiStore();

//healthyChk
let timer: ReturnType<typeof setInterval> | null = null
onMounted(() => {
    timer = setInterval(()=>{
        GeminiApi.act_healthychk();
    },5000); 
})
onUnmounted(() => {
    clearInterval(timer ?? undefined)  //很重要!!
})

</script>

<template>
    <Head title="首頁" />

    <div class="page">
        <!-- 背景裝飾 -->
        <div class="bg-orb bg-orb-1"></div>
        <div class="bg-orb bg-orb-2"></div>
        <div class="bg-orb bg-orb-3"></div>
        <div class="bg-grid"></div>

        <!-- ===== 導覽列 ===== -->
        <nav class="navbar">
            <div class="nav-inner">
                <Link href="/" class="nav-brand">
                    <svg viewBox="0 0 62 66" fill="none" class="nav-logo">
                        <path d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8897 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0262V49.0842C49.9104 49.4582 49.7005 49.8018 49.3576 49.9869L20.3793 66.4926C20.3389 66.5145 20.2966 66.5338 20.2529 66.5502C20.2383 66.5557 20.2226 66.5596 20.2068 66.5637C20.1915 66.5672 20.1758 66.5723 20.1596 66.5735C20.1311 66.5763 20.1022 66.5775 20.0733 66.5765C20.0178 66.5765 19.9629 66.5689 19.9098 66.5542C19.8914 66.549 19.8729 66.5434 19.8549 66.5373C19.7994 66.5185 19.7465 66.4926 19.6973 66.4604L0.722897 55.5337C0.570044 55.4465 0.443259 55.3207 0.355384 55.1692C0.267509 55.0177 0.221328 54.8459 0.221069 54.6711V27.3781C0.221069 27.2906 0.233195 27.2055 0.257297 27.1234C0.265379 27.0939 0.275628 27.065 0.287768 27.0371C0.298381 27.0112 0.310992 26.9860 0.325423 26.9619C0.338753 26.9389 0.353758 26.9169 0.370332 26.8960C0.386124 26.8758 0.403204 26.8569 0.421483 26.8392C0.43784 26.8222 0.455783 26.8065 0.474933 26.7920C0.493495 26.778 0.51290 26.7652 0.533043 26.7536L9.90391 21.2847L19.6088 0.462799C19.6708 0.327339 19.7666 0.210785 19.8874 0.124471C20.0082 0.038157 20.1496 -0.00751167 20.2942 0.000772257Z" fill="url(#nav-grad)"/>
                        <defs>
                            <linearGradient id="nav-grad" x1="0" y1="0" x2="62" y2="66" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#818CF8"/>
                                <stop offset="1" stop-color="#6366F1"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <span>TechLearn</span>
                </Link>

                <div class="nav-actions" v-if="canLogin">
                    <Link :href="route('login')" class="nav-link">登入</Link>
                    <Link v-if="canRegister" :href="route('register')" class="nav-cta">
                        免費開始
                    </Link>
                </div>
            </div>
        </nav>

        <!-- ===== Hero 區塊 ===== -->
        <section class="hero">
            <div class="hero-badge">
                <span class="badge-dot"></span>
                Laravel {{ laravelVersion }} · PHP {{ phpVersion }} · PrimeVue 4
            </div>

            <h1 class="hero-title">
                DEMO頁面<br />
                <span class="gradient-text">LARAVEL VUE K3S</span>
            </h1>

            <p class="hero-desc">
                整合 Laravel、Vue 3、Inertia.js 與 PrimeVue 的現代全端開發起手式。<br />
                從零到一，讓你的創意快速成真。
            </p>
        </section>

        <!-- ===== Gemini 問答區塊 ===== -->
        <section class="gemini-section">
            <div class="gemini-inner">
                <div class="section-header">
                    <h2 class="section-title">✨ AI 問答</h2>
                    <p class="section-desc">
                        讓 Gemini 為你解答 K8S 問題
                    </p>
                    <p class="section-desc">
                        Request Timeout = 
                        <input class="section-ipt" type="number" v-model="GeminiApi.custom_timeout" >
                        s
                    </p>
                </div>
                <div class="gemini-card">
                    <button
                        class="gemini-btn"
                        :disabled="GeminiApi.loading"
                        @click="GeminiApi.act_geminiapi()"
                    >
                        <span v-if="GeminiApi.loading" class="gemini-spinner"></span>
                        <span v-else>🤖</span>
                        {{ GeminiApi.loading ? 'Gemini 詢問中...(約3~15秒)' : '問問 Gemini' }}
                    </button>

                    <div v-if="GeminiApi.error" class="gemini-error">
                        ⚠️ {{ GeminiApi.error }}
                    </div>

                    <div v-if="GeminiApi.question" class="gemini-result">
                        <div class="gemini-question">
                            <span class="result-label">問題</span>
                            <p>{{ GeminiApi.question }}</p>
                        </div>
                        <div class="gemini-answer">
                            <span class="result-label">回答</span>
                            <p>{{ GeminiApi.answer }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== CTA 區塊 ===== -->
        <section class="cta-section">
            <div class="cta-card">
                <h2 class="cta-title">準備好了嗎？</h2>
                <p class="cta-desc">立即建立帳戶，開始您的開發之旅</p>
                <div class="cta-actions">
                    <Link v-if="canRegister" :href="route('register')" class="cta-btn">
                        立即免費開始
                    </Link>
                    <Link v-if="canLogin" :href="route('login')" class="cta-btn-outline">
                        已有帳戶，登入
                    </Link>
                </div>
            </div>
        </section>

        <!-- ===== Footer ===== -->
        <footer class="footer">
            <p>© 2025 TechLearn · Laravel {{ laravelVersion }} · PHP {{ phpVersion }}</p>
        </footer>
    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.page {
    min-height: 100vh;
    background: linear-gradient(180deg, #0a0a1a 0%, #0f0c29 40%, #1a1030 100%);
    color: #ffffff;
    font-family: 'Figtree', -apple-system, BlinkMacSystemFont, sans-serif;
    position: relative;
    overflow-x: hidden;
}

/* 背景裝飾 */
.bg-orb {
    position: fixed;
    border-radius: 50%;
    filter: blur(100px);
    opacity: 0.12;
    pointer-events: none;
    z-index: 0;
}
.bg-orb-1 {
    width: 800px; height: 800px;
    background: #6366f1;
    top: -300px; left: -300px;
    animation: orbFloat 15s ease-in-out infinite;
}
.bg-orb-2 {
    width: 600px; height: 600px;
    background: #8b5cf6;
    bottom: -200px; right: -200px;
    animation: orbFloat 18s ease-in-out infinite reverse;
}
.bg-orb-3 {
    width: 400px; height: 400px;
    background: #3b82f6;
    top: 40%; left: 50%;
    transform: translateX(-50%);
    animation: orbFloat 12s ease-in-out infinite;
}

.bg-grid {
    position: fixed;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
    z-index: 0;
}

@keyframes orbFloat {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -30px) scale(1.05); }
    66% { transform: translate(-20px, 20px) scale(0.95); }
}

/* ===== 導覽列 ===== */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
    padding: 1rem 2rem;
    background: rgba(10, 10, 26, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.nav-inner {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    text-decoration: none;
    color: #ffffff;
    font-weight: 700;
    font-size: 1.1rem;
    letter-spacing: -0.01em;
}

.nav-logo {
    width: 28px;
    height: 28px;
}

.nav-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.nav-link {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    padding: 0.4rem 0.75rem;
    border-radius: 0.5rem;
    transition: color 0.2s, background 0.2s;
}
.nav-link:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.06);
}

.nav-cta {
    padding: 0.45rem 1.1rem;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #ffffff;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    border-radius: 0.6rem;
    transition: opacity 0.2s, transform 0.1s;
}
.nav-cta:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

/* ===== Hero ===== */
.hero {
    position: relative;
    z-index: 1;
    padding: 10rem 2rem 2rem;
    text-align: center;
    max-width: 900px;
    margin: 0 auto;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 1rem;
    background: rgba(99, 102, 241, 0.12);
    border: 1px solid rgba(99, 102, 241, 0.3);
    border-radius: 100px;
    font-size: 0.8rem;
    color: #a5b4fc;
    margin-bottom: 2rem;
    letter-spacing: 0.02em;
}

.badge-dot {
    width: 6px;
    height: 6px;
    background: #6366f1;
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.3); }
}

.hero-title {
    font-size: clamp(2.5rem, 6vw, 5rem);
    font-weight: 800;
    line-height: 1.1;
    margin: 0 0 1.5rem;
    letter-spacing: -0.03em;
    color: #ffffff;
}

.gradient-text {
    background: linear-gradient(135deg, #818cf8 0%, #c084fc 50%, #67e8f9 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-desc {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.5);
    line-height: 1.8;
    margin: 0 0 2.5rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

/* ===== 功能卡片 ===== */
.section-header {
    text-align: center;
    margin-bottom: 2rem;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.75rem;
    letter-spacing: -0.02em;
}

.section-desc {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.4);
    margin: 0;
}

.section-ipt {
    border-radius: 10px;
    background-color:  rgba(10, 10, 26, 0.7);
    line-height: 10px;
    width: 80px;
}

/* ===== Gemini 問答 ===== */
.gemini-section {
    position: relative;
    z-index: 1;
    padding: 2rem 2rem 4rem;
}

.gemini-inner {
    max-width: 800px;
    margin: 0 auto;
}

.gemini-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.gemini-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.875rem 2.25rem;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #ffffff;
    font-size: 1rem;
    font-weight: 600;
    border: none;
    border-radius: 0.875rem;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.1s;
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.35);
}
.gemini-btn:hover:not(:disabled) {
    opacity: 0.9;
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(16, 185, 129, 0.45);
}
.gemini-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.gemini-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.35);
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    flex-shrink: 0;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.gemini-error {
    width: 100%;
    padding: 1rem 1.25rem;
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 0.875rem;
    color: #fca5a5;
    font-size: 0.9rem;
    text-align: center;
}

.gemini-result {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.gemini-question,
.gemini-answer {
    padding: 1.25rem 1.5rem;
    border-radius: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.gemini-question {
    background: rgba(99, 102, 241, 0.08);
    border: 1px solid rgba(99, 102, 241, 0.2);
}

.gemini-answer {
    background: rgba(16, 185, 129, 0.07);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.result-label {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.35);
}

.gemini-question p {
    margin: 0;
    color: #a5b4fc;
    font-size: 0.95rem;
    line-height: 1.6;
}

.gemini-answer p {
    margin: 0;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.95rem;
    line-height: 1.8;
    white-space: pre-line;
}

/* ===== CTA ===== */
.cta-section {
    position: relative;
    z-index: 1;
    padding: 2rem 2rem 6rem;
}

.cta-card {
    max-width: 700px;
    margin: 0 auto;
    text-align: center;
    padding: 3.5rem 2rem;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(139, 92, 246, 0.08));
    border: 1px solid rgba(99, 102, 241, 0.2);
    border-radius: 2rem;
}

.cta-title {
    font-size: 2.25rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.75rem;
    letter-spacing: -0.02em;
}

.cta-desc {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.5);
    margin: 0 0 2rem;
}

.cta-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-btn {
    display: inline-flex;
    padding: 0.8rem 2rem;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #ffffff;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 600;
    border-radius: 0.75rem;
    transition: opacity 0.2s, transform 0.1s;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
}
.cta-btn:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

.cta-btn-outline {
    display: inline-flex;
    padding: 0.8rem 2rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 500;
    border-radius: 0.75rem;
    background: rgba(255, 255, 255, 0.04);
    transition: all 0.2s;
}
.cta-btn-outline:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.3);
    color: #ffffff;
}

/* ===== Footer ===== */
.footer {
    position: relative;
    z-index: 1;
    text-align: center;
    padding: 1.5rem 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    color: rgba(255, 255, 255, 0.2);
    font-size: 0.8rem;
}

/* ===== 響應式 ===== */
@media (max-width: 640px) {
    .hero {
        padding: 8rem 1.25rem 4rem;
    }
    .cta-card {
        padding: 2.5rem 1.5rem;
    }
}
</style>

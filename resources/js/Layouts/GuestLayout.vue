<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="auth-bg">
        <!-- 裝飾光圈 -->
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="auth-wrapper">
            <!-- 品牌區塊 -->
            <div class="brand-area">
                <Link href="/" class="brand-logo">
                    <svg viewBox="0 0 62 65" fill="none" xmlns="http://www.w3.org/2000/svg" class="logo-svg">
                        <path d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8897 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0262V49.0842C49.9104 49.4582 49.7005 49.8018 49.3576 49.9869L20.3793 66.4926C20.3389 66.5145 20.2966 66.5338 20.2529 66.5502C20.2383 66.5557 20.2226 66.5596 20.2068 66.5637C20.1915 66.5672 20.1758 66.5723 20.1596 66.5735C20.1311 66.5763 20.1022 66.5775 20.0733 66.5765C20.0178 66.5765 19.9629 66.5689 19.9098 66.5542C19.8914 66.549 19.8729 66.5434 19.8549 66.5373C19.7994 66.5185 19.7465 66.4926 19.6973 66.4604L0.722897 55.5337C0.570044 55.4465 0.443259 55.3207 0.355384 55.1692C0.267509 55.0177 0.221328 54.8459 0.221069 54.6711V27.3781C0.221069 27.2906 0.233195 27.2055 0.257297 27.1234C0.265379 27.0939 0.275628 27.065 0.287768 27.0371C0.298381 27.0112 0.310992 26.9860 0.325423 26.9619C0.338753 26.9389 0.353758 26.9169 0.370332 26.8960C0.386124 26.8758 0.403204 26.8569 0.421483 26.8392C0.43784 26.8222 0.455783 26.8065 0.474933 26.7920C0.493495 26.778 0.51290 26.7652 0.533043 26.7536L9.90391 21.2847L19.6088 0.462799C19.6708 0.327339 19.7666 0.210785 19.8874 0.124471C20.0082 0.038157 20.1496 -0.00751167 20.2942 0.000772257Z" fill="url(#paint0_linear)"/>
                        <defs>
                            <linearGradient id="paint0_linear" x1="0" y1="0" x2="62" y2="66" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#818CF8"/>
                                <stop offset="1" stop-color="#6366F1"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <span class="brand-name">TechLearn</span>
                </Link>
            </div>

            <!-- 表單卡片 -->
            <div class="auth-card">
                <slot />
            </div>

            <!-- 底部說明 -->
            <p class="auth-footer">
                © 2025 TechLearn. 保留所有權利。
            </p>
        </div>
    </div>
</template>

<style scoped>
.auth-bg {
    min-height: 100vh;
    background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    padding: 2rem 1rem;
}

/* 裝飾光圈 */
.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.15;
    pointer-events: none;
}
.orb-1 {
    width: 600px;
    height: 600px;
    background: #6366f1;
    top: -200px;
    left: -200px;
    animation: float 8s ease-in-out infinite;
}
.orb-2 {
    width: 400px;
    height: 400px;
    background: #8b5cf6;
    bottom: -100px;
    right: -100px;
    animation: float 10s ease-in-out infinite reverse;
}
.orb-3 {
    width: 300px;
    height: 300px;
    background: #3b82f6;
    top: 50%;
    right: 20%;
    animation: float 12s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) scale(1); }
    50% { transform: translateY(-30px) scale(1.05); }
}

.auth-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    max-width: 480px;
    position: relative;
    z-index: 1;
}

/* 品牌 */
.brand-area {
    margin-bottom: 2rem;
}

.brand-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
}

.logo-svg {
    width: 40px;
    height: 40px;
    filter: drop-shadow(0 0 12px rgba(99, 102, 241, 0.8));
}

.brand-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.02em;
}

/* 卡片 */
.auth-card {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1.5rem;
    padding: 2.5rem;
    box-shadow:
        0 25px 50px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

/* 底部說明 */
.auth-footer {
    margin-top: 2rem;
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.3);
    text-align: center;
}
</style>

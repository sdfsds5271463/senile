<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const showMobileMenu = ref(false);
const page = usePage();
</script>

<template>
    <div class="app-shell">
        <!-- 背景裝飾 -->
        <div class="bg-orb bg-orb-1"></div>
        <div class="bg-orb bg-orb-2"></div>
        <div class="bg-grid"></div>

        <!-- ===== 導覽列 ===== -->
        <nav class="navbar">
            <div class="nav-inner">
                <!-- 左側 Logo + 連結 -->
                <div class="nav-left">
                    <Link :href="route('dashboard')" class="nav-brand">
                        <svg viewBox="0 0 62 66" fill="none" class="nav-logo">
                            <path d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8897 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0262V49.0842C49.9104 49.4582 49.7005 49.8018 49.3576 49.9869L20.3793 66.4926C20.2966 66.5338 20.2529 66.5502 20.1596 66.5735C20.1022 66.5775 20.0733 66.5765C20.0178 66.5765 19.9629 66.5689 19.9098 66.5542C19.8914 66.549 19.8729 66.5434 19.8549 66.5373C19.7994 66.5185 19.7465 66.4926 19.6973 66.4604L0.722897 55.5337C0.570044 55.4465 0.443259 55.3207 0.355384 55.1692C0.267509 55.0177 0.221328 54.8459 0.221069 54.6711V27.3781C0.233195 27.2055 0.257297 27.1234C0.275628 27.065 0.287768 27.0371C0.325423 26.9619C0.370332 26.8960C0.421483 26.8392C0.474933 26.7920C0.533043 26.7536L9.90391 21.2847L19.6088 0.462799C19.6708 0.327339 19.7666 0.210785 19.8874 0.124471C20.0082 0.038157 20.1496 -0.00751167 20.2942 0.000772257Z" fill="url(#auth-grad)"/>
                            <defs>
                                <linearGradient id="auth-grad" x1="0" y1="0" x2="62" y2="66" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#818CF8"/>
                                    <stop offset="1" stop-color="#6366F1"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <span>TechLearn</span>
                    </Link>

                    <div class="nav-links">
                        <Link
                            :href="route('dashboard')"
                            class="nav-link"
                            :class="{ 'nav-link-active': route().current('dashboard') }"
                        >
                            Dashboard
                        </Link>
                    </div>
                </div>

                <!-- 右側使用者 -->
                <div class="nav-right">
                    <div class="user-menu">
                        <div class="user-avatar">
                            {{ page.props.auth?.user?.name?.charAt(0)?.toUpperCase() ?? '?' }}
                        </div>
                        <div class="user-info">
                            <span class="user-name">{{ page.props.auth.user.name }}</span>
                        </div>
                        <div class="user-actions">
                            <Link :href="route('profile.edit')" class="action-link">個人資料</Link>
                            <Link :href="route('logout')" method="post" as="button" class="action-link action-logout">
                                登出
                            </Link>
                        </div>
                    </div>

                    <!-- 漢堡選單（行動版） -->
                    <button class="hamburger" @click="showMobileMenu = !showMobileMenu">
                        <span :class="showMobileMenu ? 'rotate-45 translate-y-1.5' : ''" class="ham-line"></span>
                        <span :class="showMobileMenu ? 'opacity-0' : ''" class="ham-line"></span>
                        <span :class="showMobileMenu ? '-rotate-45 -translate-y-1.5' : ''" class="ham-line"></span>
                    </button>
                </div>
            </div>

            <!-- 行動版選單 -->
            <div v-show="showMobileMenu" class="mobile-menu">
                <Link :href="route('dashboard')" class="mobile-link">Dashboard</Link>
                <Link :href="route('profile.edit')" class="mobile-link">個人資料</Link>
                <Link :href="route('logout')" method="post" as="button" class="mobile-link mobile-logout">登出</Link>
            </div>
        </nav>

        <!-- ===== 頁面內容 ===== -->
        <main class="page-content">
            <slot />
        </main>
    </div>
</template>

<style scoped>
.app-shell {
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
    opacity: 0.1;
    pointer-events: none;
    z-index: 0;
}
.bg-orb-1 {
    width: 700px; height: 700px;
    background: #6366f1;
    top: -250px; left: -200px;
}
.bg-orb-2 {
    width: 500px; height: 500px;
    background: #8b5cf6;
    bottom: -150px; right: -150px;
}
.bg-grid {
    position: fixed;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
    z-index: 0;
}

/* ===== 導覽列 ===== */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
    background: rgba(10, 10, 26, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
}

.nav-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1.5rem;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.nav-left {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    text-decoration: none;
    color: #ffffff;
    font-weight: 700;
    font-size: 1rem;
    letter-spacing: -0.01em;
}

.nav-logo {
    width: 24px;
    height: 24px;
}

.nav-links {
    display: flex;
    gap: 0.25rem;
}

.nav-link {
    padding: 0.4rem 0.8rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.5);
    text-decoration: none;
    transition: all 0.2s;
}
.nav-link:hover {
    color: rgba(255, 255, 255, 0.9);
    background: rgba(255, 255, 255, 0.06);
}
.nav-link-active {
    color: #a5b4fc !important;
    background: rgba(99, 102, 241, 0.12) !important;
}

/* 使用者區塊 */
.nav-right {
    display: flex;
    align-items: center;
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 700;
    color: #ffffff;
    flex-shrink: 0;
}

.user-info {
    display: none;
}

@media (min-width: 640px) {
    .user-info {
        display: block;
    }
}

.user-name {
    font-size: 0.875rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.8);
}

.user-actions {
    display: none;
    gap: 0.25rem;
}
@media (min-width: 640px) {
    .user-actions {
        display: flex;
    }
}

.action-link {
    padding: 0.35rem 0.7rem;
    border-radius: 0.45rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.45);
    text-decoration: none;
    border: none;
    background: none;
    cursor: pointer;
    transition: all 0.2s;
}
.action-link:hover {
    color: rgba(255, 255, 255, 0.85);
    background: rgba(255, 255, 255, 0.06);
}

.action-logout {
    color: rgba(248, 113, 113, 0.6);
}
.action-logout:hover {
    color: #f87171;
    background: rgba(248, 113, 113, 0.08);
}

/* 漢堡 */
.hamburger {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 0.4rem;
    background: none;
    border: none;
    cursor: pointer;
}
@media (min-width: 640px) {
    .hamburger {
        display: none;
    }
}

.ham-line {
    display: block;
    width: 20px;
    height: 2px;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 2px;
    transition: all 0.25s;
    transform-origin: center;
}

/* 行動選單 */
.mobile-menu {
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    padding: 0.75rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.mobile-link {
    padding: 0.6rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.65);
    text-decoration: none;
    border: none;
    background: none;
    cursor: pointer;
    text-align: left;
    transition: all 0.2s;
    width: 100%;
    display: block;
}
.mobile-link:hover {
    background: rgba(255, 255, 255, 0.06);
    color: rgba(255, 255, 255, 0.9);
}

.mobile-logout {
    color: rgba(248, 113, 113, 0.6);
}

/* ===== 頁面內容 ===== */
.page-content {
    position: relative;
    z-index: 1;
    padding-top: 60px;
}
</style>

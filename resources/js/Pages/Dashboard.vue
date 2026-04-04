<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();

const stats = [
    { icon: '📁', value: '12', label: '專案' },
    { icon: '✅', value: '48', label: '任務' },
    { icon: '🔔', value: '3',  label: '通知' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="dashboard">

            <!-- 歡迎列 -->
            <div class="welcome-row">
                <div class="welcome-badge">You're logged in!</div>
                <div class="welcome-name">{{ page.props.auth.user.name }}</div>
            </div>

            <!-- 統計卡片 -->
            <div class="stats-grid">
                <div v-for="s in stats" :key="s.label" class="stat-card">
                    <span class="stat-icon">{{ s.icon }}</span>
                    <span class="stat-value">{{ s.value }}</span>
                    <span class="stat-label">{{ s.label }}</span>
                </div>
            </div>

            <!-- 活動區塊 -->
            <div class="activity-panel">
                <div v-for="i in 4" :key="i" class="activity-row">
                    <div class="activity-dot"></div>
                    <div class="activity-bar" :style="{ width: (85 - i * 12) + '%' }"></div>
                    <div class="activity-time">{{ i }}h ago</div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.dashboard {
    max-width: 900px;
    margin: 0 auto;
    padding: 3rem 1.5rem 4rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* 歡迎列 */
.welcome-row {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    flex-wrap: wrap;
}

.welcome-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.45rem 1rem;
    background: rgba(99, 102, 241, 0.12);
    border: 1px solid rgba(99, 102, 241, 0.3);
    border-radius: 100px;
    font-size: 0.85rem;
    font-weight: 500;
    color: #a5b4fc;
    letter-spacing: 0.01em;
}

.welcome-name {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.02em;
}

/* 統計卡片 */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.stat-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 2rem 1rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 1.25rem;
    transition: all 0.3s;
    cursor: default;
}
.stat-card:hover {
    background: rgba(99, 102, 241, 0.07);
    border-color: rgba(99, 102, 241, 0.25);
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(99, 102, 241, 0.1);
}

.stat-icon { font-size: 1.75rem; }

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.03em;
    background: linear-gradient(135deg, #a5b4fc, #c084fc);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.35);
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

/* 活動區塊 */
.activity-panel {
    padding: 1.75rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
}

.activity-row {
    display: flex;
    align-items: center;
    gap: 0.875rem;
}

.activity-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    flex-shrink: 0;
}

.activity-bar {
    height: 6px;
    border-radius: 100px;
    background: linear-gradient(90deg, rgba(99,102,241,0.5), rgba(139,92,246,0.2));
    flex-shrink: 0;
    transition: width 0.3s;
}

.activity-time {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.2);
    margin-left: auto;
    flex-shrink: 0;
}

/* 響應式 */
@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    .welcome-name {
        font-size: 1.5rem;
    }
}
</style>

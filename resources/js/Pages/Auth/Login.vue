<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Message from 'primevue/message';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="登入" />

        <!-- 標題 -->
        <div class="form-header">
            <h1 class="form-title">歡迎回來</h1>
            <p class="form-subtitle">請輸入您的帳戶資訊以繼續</p>
        </div>

        <!-- 狀態訊息 -->
        <Message v-if="status" severity="success" class="mb-5">
            {{ status }}
        </Message>

        <form @submit.prevent="submit" class="form-body">
            <!-- Email -->
            <div class="field-group">
                <label for="email" class="field-label">電子郵件</label>
                <InputText
                    id="email"
                    v-model="form.email"
                    type="email"
                    placeholder="your@email.com"
                    :invalid="!!form.errors.email"
                    autocomplete="username"
                    autofocus
                    fluid
                />
                <small v-if="form.errors.email" class="field-error">{{ form.errors.email }}</small>
            </div>

            <!-- 密碼 -->
            <div class="field-group">
                <div class="field-label-row">
                    <label for="password" class="field-label">密碼</label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="forgot-link"
                    >
                        忘記密碼？
                    </Link>
                </div>
                <Password
                    id="password"
                    v-model="form.password"
                    placeholder="輸入密碼"
                    :feedback="false"
                    toggleMask
                    :invalid="!!form.errors.password"
                    autocomplete="current-password"
                    fluid
                />
                <small v-if="form.errors.password" class="field-error">{{ form.errors.password }}</small>
            </div>

            <!-- 記住我 -->
            <div class="remember-row">
                <div class="flex items-center gap-2">
                    <Checkbox v-model="form.remember" inputId="remember" binary />
                    <label for="remember" class="remember-label">記住我</label>
                </div>
            </div>

            <!-- 登入按鈕 -->
            <Button
                type="submit"
                label="登入"
                :loading="form.processing"
                fluid
                class="login-btn"
            />

            <!-- 分隔線 -->
            <div class="divider">
                <span>還沒有帳戶？</span>
            </div>

            <!-- 前往註冊 -->
            <Link :href="route('register')" class="register-link">
                立即免費註冊
            </Link>
        </form>
    </GuestLayout>
</template>

<style scoped>
.form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.form-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.5rem;
    letter-spacing: -0.02em;
}

.form-subtitle {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.5);
    margin: 0;
}

.form-body {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.field-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.field-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.8);
}

.field-label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.field-error {
    color: #f87171;
    font-size: 0.75rem;
}

.forgot-link {
    font-size: 0.8rem;
    color: #a5b4fc;
    text-decoration: none;
    transition: color 0.2s;
}
.forgot-link:hover {
    color: #c7d2fe;
}

.remember-row {
    display: flex;
    align-items: center;
}

.remember-label {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
}

.login-btn {
    margin-top: 0.25rem;
    font-size: 1rem !important;
    font-weight: 600 !important;
    padding: 0.75rem !important;
    background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
    border: none !important;
    border-radius: 0.75rem !important;
    transition: opacity 0.2s, transform 0.1s !important;
}
.login-btn:hover {
    opacity: 0.9 !important;
    transform: translateY(-1px) !important;
}
.login-btn:active {
    transform: translateY(0) !important;
}

.divider {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: rgba(255, 255, 255, 0.3);
    font-size: 0.8rem;
    text-align: center;
}
.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
}

.register-link {
    display: block;
    text-align: center;
    padding: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 0.75rem;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s;
    background: rgba(255, 255, 255, 0.03);
}
.register-link:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

/* PrimeVue 元件覆寫 - 暗色風格 */
:deep(.p-inputtext) {
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    color: #ffffff !important;
    border-radius: 0.75rem !important;
    padding: 0.7rem 1rem !important;
    font-size: 0.9rem !important;
    transition: border-color 0.2s, background 0.2s !important;
}
:deep(.p-inputtext:focus) {
    border-color: #6366f1 !important;
    background: rgba(99, 102, 241, 0.08) !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
}
:deep(.p-inputtext::placeholder) {
    color: rgba(255, 255, 255, 0.25) !important;
}
:deep(.p-inputtext.p-invalid) {
    border-color: #f87171 !important;
}

:deep(.p-password) {
    width: 100%;
}
:deep(.p-password-input) {
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    color: #ffffff !important;
    border-radius: 0.75rem !important;
    padding: 0.7rem 1rem !important;
    font-size: 0.9rem !important;
}
:deep(.p-password-input:focus) {
    border-color: #6366f1 !important;
    background: rgba(99, 102, 241, 0.08) !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
}
:deep(.p-password-input::placeholder) {
    color: rgba(255, 255, 255, 0.25) !important;
}
:deep(.p-password-toggle-icon) {
    color: rgba(255, 255, 255, 0.4) !important;
}

:deep(.p-checkbox .p-checkbox-box) {
    background: rgba(255, 255, 255, 0.06) !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
    border-radius: 0.375rem !important;
}
:deep(.p-checkbox.p-checkbox-checked .p-checkbox-box) {
    background: #6366f1 !important;
    border-color: #6366f1 !important;
}

:deep(.p-message) {
    border-radius: 0.75rem !important;
}
</style>

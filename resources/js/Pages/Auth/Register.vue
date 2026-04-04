<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="建立帳戶" />

        <!-- 標題 -->
        <div class="form-header">
            <h1 class="form-title">建立帳戶</h1>
            <p class="form-subtitle">加入我們，開始您的學習之旅</p>
        </div>

        <form @submit.prevent="submit" class="form-body">
            <!-- 姓名 -->
            <div class="field-group">
                <label for="name" class="field-label">姓名</label>
                <InputText
                    id="name"
                    v-model="form.name"
                    type="text"
                    placeholder="請輸入您的姓名"
                    :invalid="!!form.errors.name"
                    autocomplete="name"
                    autofocus
                    fluid
                />
                <small v-if="form.errors.name" class="field-error">{{ form.errors.name }}</small>
            </div>

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
                    fluid
                />
                <small v-if="form.errors.email" class="field-error">{{ form.errors.email }}</small>
            </div>

            <!-- 密碼 -->
            <div class="field-group">
                <label for="password" class="field-label">密碼</label>
                <Password
                    id="password"
                    v-model="form.password"
                    placeholder="至少 8 個字元"
                    toggleMask
                    :invalid="!!form.errors.password"
                    autocomplete="new-password"
                    fluid
                />
                <small v-if="form.errors.password" class="field-error">{{ form.errors.password }}</small>
            </div>

            <!-- 確認密碼 -->
            <div class="field-group">
                <label for="password_confirmation" class="field-label">確認密碼</label>
                <Password
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    placeholder="再次輸入密碼"
                    :feedback="false"
                    toggleMask
                    :invalid="!!form.errors.password_confirmation"
                    autocomplete="new-password"
                    fluid
                />
                <small v-if="form.errors.password_confirmation" class="field-error">
                    {{ form.errors.password_confirmation }}
                </small>
            </div>

            <!-- 條款說明 -->
            <p class="terms-text">
                點擊「建立帳戶」即表示您同意我們的
                <a href="#" class="terms-link">服務條款</a>
                與
                <a href="#" class="terms-link">隱私政策</a>
            </p>

            <!-- 提交按鈕 -->
            <Button
                type="submit"
                label="建立帳戶"
                :loading="form.processing"
                fluid
                class="register-btn"
            />

            <!-- 分隔線 -->
            <div class="divider">
                <span>已有帳戶？</span>
            </div>

            <!-- 前往登入 -->
            <Link :href="route('login')" class="login-link">
                返回登入
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
    gap: 1.1rem;
}

.field-group {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
}

.field-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.8);
}

.field-error {
    color: #f87171;
    font-size: 0.75rem;
}

.terms-text {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.35);
    text-align: center;
    margin: 0.25rem 0 0;
    line-height: 1.6;
}
.terms-link {
    color: #a5b4fc;
    text-decoration: none;
}
.terms-link:hover {
    color: #c7d2fe;
    text-decoration: underline;
}

.register-btn {
    margin-top: 0.25rem;
    font-size: 1rem !important;
    font-weight: 600 !important;
    padding: 0.75rem !important;
    background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
    border: none !important;
    border-radius: 0.75rem !important;
    transition: opacity 0.2s, transform 0.1s !important;
}
.register-btn:hover {
    opacity: 0.9 !important;
    transform: translateY(-1px) !important;
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

.login-link {
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
.login-link:hover {
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
</style>

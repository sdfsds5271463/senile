<script setup>
import { ref } from 'vue';
import axios from 'axios';

// 假設這是從父元件或初始 Props 傳進來的資料
const props = defineProps({
    initialPhone: String,
    testId: { type: Number, default: 1 }
});

// 使用 ref 來綁定輸入框
const phone = ref(props.initialPhone || '');
const isProcessing = ref(false);
const errorMessage = ref('');

const submit = async () => {
    isProcessing.value = true;
    errorMessage.value = '';
    
    try {
        // 使用 Axios 發送 PUT 請求
        const response = await axios.put(`/api/tests/${props.testId}`, { 
            phone: phone.value 
        });

        // 成功時，後端回傳的是 JSON，所以從 response.data 拿資料
        alert('更新成功！資料庫現在的值：' + response.data.phone);
        
        // 你可以選擇手動更新本地顯示的數值
        phone.value = response.data.phone;
    } catch (error) {
        // 處理驗證錯誤 (Laravel 422 錯誤)
        if (error.response && error.response.status === 422) {
            console.error('Laravel 422 錯誤', error);
        } else {
            console.error('其他錯誤', error);
        }
    } finally {
        isProcessing.value = false;
    }
};
</script>

<template>
    <h1>Axios 非同步更新 id:{{ props.testId }} phone</h1>
    <input v-model="phone"  type="number"  maxlength="10" ><br>
    <button @click="submit" :disabled="isProcessing">
        {{ isProcessing ? '連線中...' : '點我使用 Axios 更新' }}
    </button>
</template>
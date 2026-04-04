<script setup lang="ts">
    import DataTable from 'primevue/datatable';
    import Column from 'primevue/column';

    interface Props {
        t01: any;      // null 在原代碼代表不限型別，TS 對應為 any
        uri?: string;  // 加個問號代表是可選的 (Optional)
    }
    const props = defineProps<Props>();
    const testTs = (str: string):string=>{
        return str + str;
    }
</script>

<template> 
    {{ t01 }} {{ testTs(uri ?? '') }} ---test s8---
    <div class="card">
        <DataTable :value="[]" paginator :rows="10" tableStyle="min-width: 50rem">
            <Column field="symbol" header="測試UI表格1"></Column>
            <Column field="name" header="測試UI表格2"></Column>
            <Column field="price" header="測試UI表格3"></Column>
            <Column field="change" header="測試UI表格4"></Column>
        </DataTable>
    </div>
</template>

<style lang="scss">
$primary-color: #3490dc;
div { color: $primary-color; }
</style>



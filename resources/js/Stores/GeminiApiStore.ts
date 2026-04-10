import { defineStore } from 'pinia';

export const GeminiApiStore = defineStore('GeminiApiStore', {
  state: () => ({
    question: '' as string,
    answer: '' as string,
    loading: false as boolean,
    error: '' as string,

    custom_timeout: 15 as number,
  }),

  actions: {
    //geminiapi
    async act_geminiapi() {
      this.loading = true;
      this.error = '';
      this.question = '';
      this.answer = '';

      //自訂超時處理
      if(typeof this.custom_timeout != "number" || this.custom_timeout <= 0){
        this.custom_timeout = 15; //不正確的數值強制改 15
      }

      try {
        const response = await fetch('/api/geminiapi?timeout=' + this.custom_timeout.toString(), {
          method: 'GET',
        });
        const data = await response.json();

        if (data.status === 'success') {
          this.question = data.data.question;
          this.answer = data.data.answer;
        } else {
          this.error = data.msg || '發生未知錯誤';
        }
      } catch (e) {
        this.error = '網路錯誤，請稍後再試';
        console.error("GeminiApi error", e);
      } finally {
        this.loading = false;
      }
    },

    //healthychk
    async act_healthychk() {
      try {
        const response = await fetch('/api/healthychk', {
          method: 'GET',
        });
        const data = await response.json();

        if (data.flag != true) {
          console.error("Healthychk is bad now");
        }
      } catch (e) {
        console.error("Healthychk error", e);
      }
    },
  },
});
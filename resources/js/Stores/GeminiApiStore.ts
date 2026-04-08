import { defineStore } from 'pinia';

export const GeminiApiStore = defineStore('GeminiApiStore', {
  state: () => ({
    question: '' as string,
    answer: '' as string,
    loading: false as boolean,
    error: '' as string,
  }),

  actions: {
    async act_geminiapi() {
      this.loading = true;
      this.error = '';
      this.question = '';
      this.answer = '';

      console.log("BEFORE GET");

      const response = await fetch('/api/geminiapi', {
        method: 'GET',
      });

      const data = await response.json();

      console.log("AFTER GET", data);

      if (data.status === 'success') {
        this.question = data.data.question;
        this.answer = data.data.answer;
      } else {
        this.error = data.msg || '發生未知錯誤';
      }

      this.loading = false;
    },
  },
});
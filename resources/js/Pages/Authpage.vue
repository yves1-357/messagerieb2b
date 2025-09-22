<template>
  <div class="min-h-screen bg-gradient-to-r from-indigo-900 via-blue-900 to-indigo-800">
    <!-- HEADER avec logo -->
    <header class="flex items-center p-6">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 52 52"
        class="w-10 h-10 mr-3"
        fill="#4927F5"
        stroke="#4927F5"
      >
        <path d="M26.1,3.3C12.5,3.3,1.5,13.4,1.5,25.8c0,3.9,1.1,7.6,3,10.9c0.3,0.5,0.4,1.1,0.2,1.7l-3.2,8.7
          c-0.3,0.8,0.5,1.5,1.3,1.3l8.8-3.4c0.5-0.2,1.1-0.1,1.7,0.2c3.7,2.1,8.1,3.3,12.9,3.3c13.5-0.1,24.5-10.1,24.5-22.6
          C50.7,13.4,39.7,3.3,26.1,3.3z M14.7,25c0-0.5,0.4-1,1-1h15.9c0.5,0,1,0.4,1,1V27c0,0.5-0.4,1-1,1H15.6c-0.5,0-1-0.4-1-1V25z
          M37.6,34.6c0,0.5-0.4,1-1,1h-21c-0.5,0-1-0.4-1-1v-1.9c0-0.5,0.4-1,1-1h21c0.5,0,1,0.4,1,1V34.6z
          M37.6,19.3c0,0.5-0.4,1-1,1h-21
          c-0.5,0-1-0.4-1-1v-1.9c0-0.5,0.4-1,1-1h21c0.5,0,1,0.4,1,1V19.3z"/>
      </svg>
      <h1 class="text-2xl font-bold text-white">QuickChat</h1>
    </header>

    <!-- Zone centrale : Login ou Register -->
    <main class="flex justify-center items-center mt-20">
      <component :is="currentPage" @switchPage="switchPage" />
    </main>
  </div>
</template>

<script setup>
import { ref } from "vue";
import Login from "./Login.vue";
import Register from "./Register.vue";

const currentPage = ref(Login);

function switchPage(page) {
  if (page === "register") {
    currentPage.value = Register;
  } else {
    currentPage.value = Login;
  }
}
</script>

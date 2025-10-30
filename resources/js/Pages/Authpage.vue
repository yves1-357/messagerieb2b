<template>
  <div class="min-h-screen bg-gradient-to-r from-indigo-900 via-blue-900 to-indigo-800 flex flex-col">
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

    <div class="flex flex-col md:flex-row flex-grow px-4 md:px-8 py-6 max-w-7xl mx-auto w-full">
      <!-- Côté gauche: Texte de présentation -->
      <div class="w-full md:w-1/2 text-white pr-0 md:pr-10 mb-8 md:mb-0">
        <h2 class="text-5xl font-bold mb-6 leading-tight text-blue-300">
          Un espace pour des conversations qui comptent
        </h2>

        <p class="text-xl mb-6 text-blue-100">
          QuickChat vous permet de communiquer instantanément avec vos proches,
          collègues et amis, où que vous soyez.
        </p>

        <ul class="space-y-4 text-lg">
          <li class="flex items-center">
            <span class="bg-blue-500 rounded-full p-1 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </span>
            Messages instantanés et sécurisés
          </li>
          <li class="flex items-center">
            <span class="bg-blue-500 rounded-full p-1 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </span>
            Notifications en temps réel 
          </li>
          <li class="flex items-center">
            <span class="bg-blue-500 rounded-full p-1 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </span>
            Groupes et canaux de discussion
          </li>
        </ul>
      </div>

      <!-- Côté droit: Formulaire de login/register -->
      <div class="w-full md:w-1/2 flex justify-center items-center">
        <div class="bg-white/10 backdrop-blur-lg p-8 rounded-xl shadow-2xl w-full max-w-md border border-white/20">
          <component :is="currentPage" @switchPage="switchPage" />
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-6 text-white/70 text-sm">
      <p>© 2025 QuickChat. Tous droits réservés.</p>
    </footer>
  </div>
</template>

<script setup>
import { ref } from "vue";
import Login from "./Login.vue";
import Register from "./Register.vue";
import { router } from '@inertiajs/vue3';
import { onMounted } from "vue";
import Chat from "./Chat.vue";

const currentPage = ref(Login);

// Détermine le composant initial selon l'URL
onMounted(() => {
  // Vérifie l'URL actuelle
  const currentPath = window.location.pathname;

  if (currentPath === "/register") {
    currentPage.value = Register;
  } else {
    currentPage.value = Login;
  }
});

function switchPage(page) {
  if (page === "register") {
    currentPage.value = Register;
    // Change l'URL sans recharger la page
    window.history.pushState({}, "", "/register");
  } else if (page === 'chat'){
    currentPage.value = Chat;
    window.history.pushState({}, "", "/chat");
  }
  else {
    currentPage.value = Login;
    // Change l'URL
    window.history.pushState({}, "", "/login");
  }
}
</script>

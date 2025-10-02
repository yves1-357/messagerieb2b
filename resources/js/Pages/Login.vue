<template>
  <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-96">
    <h2 class="text-white text-xl font-bold mb-6 text-center">Connexion</h2>

    <form>
      <div class="mb-4">
        <label class="block text-gray-400 mb-2">Email</label>
        <input type="email" v-model="email" class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <div class="mb-6">
        <label class="block text-gray-400 mb-2">Mot de passe</label>
        <input type="password" v-model="password" class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <button type="button" @click="handleLogin" :disabled="chargement" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
        Se connecter
      </button>

      <div class="mt-4 text-center">
        <span class="text-gray-400 text-sm">ou</span>
      </div>

      <a href="/auth/google" class="block mt-4 text-center hover:opacity-80 transition">
        <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Sign in with Google" class="mx-auto">
      </a>
    </form>

    <p class="text-gray-400 mt-6 text-center">
      Pas encore de compte ?
      <a href="#" @click.prevent="$emit('switchPage', 'register')" class="text-indigo-400 hover:underline">S’inscrire</a>
    </p>
  </div>
</template>
<script setup>
import axios from 'axios';
import {ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';


// on declare les proprites vue
const email = ref ('');
const password = ref('');
const chargement = ref(false);

async function handleLogin() {
    console.log('handleLogin appelée');
  if (chargement.value) return;
   chargement.value = true;

    if(!email.value) {
        alert('L\'email est requis.');
        chargement.value = false;
        return;
    }

    if(!email.value.includes('@')){
        alert('Veuillez entrez un email valide.');
        chargement.value = false;
        return;
    }
    if (password.value.length < 5) {
        alert('Le mot de passe doit contenir au moins 5 caractères.')
        chargement.value = false;
        return;

    }


    try {
        //envoie donnes backend
        const response = await axios.post('/login', {
            email:email.value,
            password:password.value,
        });
        //si connexion reussie
        console.log('connexion reussie', response.data);
        Swal.fire({
        title: 'Succès !',
        text: 'Connexion réussie !',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(() => {
        router.visit('/chat');
    });

    }
    catch (error) {
    console.error('Erreur de connexion:', error.response?.data);

    let errorMessage = "Une erreur est survenue lors de la connexion.";

    if (error.response?.data?.message) {
        errorMessage = error.response.data.message;
    }

    Swal.fire({
        title: 'Erreur',
        text: errorMessage,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}
    finally {
        chargement.value = false;
    }
    }


</script>

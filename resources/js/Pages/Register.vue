<template>
  <div class="bg-gray-800 p-4 rounded-lg shadow-lg w-96">
    <h2 class="text-white text-xl font-bold mb-2 text-center">Inscription</h2>

    <form @submit.prevent="handleRegister">
           <div class="mb-2">
        <label class="block text-gray-400 mb-2">Nom</label>
        <input type="text"  v-model="nom" required  class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <div class="mb-2">
        <label class="block text-gray-400 mb-2">Email</label>
        <input type="email" v-model="email" required  class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <div class="mb-2">
        <label class="block text-gray-400 mb-2">Mot de passe</label>
        <input type="password" v-model="password" required  class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <button type="submit"  :disabled="chargement" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
        S’inscrire
      </button>
    </form>

    <p class="text-gray-400 mt-6 text-center">
      Déjà un compte ?
      <a href="#" @click.prevent="$emit('switchPage', 'login')" class="text-indigo-400 hover:underline">Se connecter</a>
    </p>
  </div>
</template>
<script setup>
import {ref} from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';



// on declare
const nom = ref('test');
const email = ref('default@exemple.com');
const password = ref('holaZola');
const chargement = ref(false);
const emit = defineEmits(['switchPage']);

async function handleRegister(){

    console.log('Debut handleRegister');
    chargement.value = true;


    if(!nom.value){
        alert('le nom est requis.');
        chargement.value = false;
        return;
    }
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
    const response = await axios.post('/register', {
      name: nom.value,
      email: email.value,
      password: password.value,
      password_confirmation: password.value
    });

    console.log('Réponse serveur:', response.data);

    // Afficher un message de succès
    Swal.fire({
        title:'Succès !',
        text:'Inscription réussie !',
        icon:'success',
        confirmButtonText:'OK'
    }).then(()=>{

    // Rediriger vers chat
    router.visit('/chat');
    });
  } catch (error) {

  console.error('Erreur complète:', error);
  console.error('Response:', error.response);
  console.error('Response data:', error.response?.data);

  // extraction message erreur
      let errorMessage = "Une erreur est survenue lors de l'inscription.";


    // Afficher un pop-up d'erreur propre
    if (error.response?.data?.errors) {
      // Erreurs de validation (email déjà pris, etc.)
      const errors = error.response.data.errors;
        const firstErrorKey = Object.keys(errors)[0];
        errorMessage = errors[firstErrorKey][0] || "Erreur de validation";

    }
    else if (errorMessage.includes("password")) {
            errorMessage = "Le mot de passe ne respecte pas les critères requis.";
        }


      Swal.fire({
  title: 'Erreur',
  text: errorMessage,
  icon: 'error',
  confirmButtonText: 'OK'
});
  } finally {
    chargement.value = false;
  }
}
</script>

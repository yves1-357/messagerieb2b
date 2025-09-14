<template>
  <div class="min-h-screen bg-gray-100 flex flex-col items-center p-8">
    <!-- Header -->
    <header class="w-full flex justify-between items-center mb-8">
      <h1 class="text-4xl font-bold">Messagerie Chat</h1>
      <div>
        <button @click="showRegister = true" class="px-4 py-2 bg-blue-500 text-white rounded mr-2">Inscription</button>
        <button @click="showRegister = false" class="px-4 py-2 bg-green-500 text-white rounded">Connexion</button>
      </div>
    </header>

    <!-- Cadre central -->
    <div class="w-full max-w-md bg-white p-6 rounded shadow">
      <template v-if="showRegister">
        <h2 class="text-2xl font-bold mb-4">Inscription</h2>
        <form>
          <input type="text" placeholder="Pseudo" class="w-full mb-2 p-2 border rounded" />
          <input type="text" placeholder="Nom" class="w-full mb-2 p-2 border rounded" />
          <input type="email" placeholder="Email" class="w-full mb-2 p-2 border rounded" />
          <input type="password" placeholder="Mot de passe" class="w-full mb-2 p-2 border rounded" />
          <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded mt-2">S'inscrire</button>
        </form>
      </template>

      <template v-else>
        <h2 class="text-2xl font-bold mb-4">Connexion</h2>
        <form>
          <input type="email" placeholder="Email" class="w-full mb-2 p-2 border rounded" />
          <input type="password" placeholder="Mot de passe" class="w-full mb-2 p-2 border rounded" />
          <button type="submit" class="w-full bg-green-700 text-white p-2 rounded mt-2">Connexion</button>
        </form>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const showRegister = ref(true) // true = afficher inscription, false = connexion
</script>

<style scoped>
/* Optionnel : styles supplémentaires si besoin */
</style>

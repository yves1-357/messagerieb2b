<template>
  <div class="bg-gray-800 p-4 rounded-lg shadow-lg w-96">
    <h2 class="text-white text-xl font-bold mb-2 text-center">Inscription</h2>

    <form>
      <div class="mb-2">
        <label class="block text-gray-400 mb-2">Pseudo</label>
        <input type="text" class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <div class="mb-2">
        <label class="block text-gray-400 mb-2">Nom</label>
        <input type="text" class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <div class="mb-2">
        <label class="block text-gray-400 mb-2">Email</label>
        <input type="email" class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <div class="mb-2">
        <label class="block text-gray-400 mb-2">Mot de passe</label>
        <input type="password" class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <button type="button" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
        S’inscrire
      </button>
    </form>

    <p class="text-gray-400 mt-6 text-center">
      Déjà un compte ?
      <a href="#" @click.prevent="$emit('switchPage', 'login')" class="text-indigo-400 hover:underline">Se connecter</a>
    </p>
  </div>
</template>

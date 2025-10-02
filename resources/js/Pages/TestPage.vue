<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg">
      <h1 class="text-3xl font-bold text-green-600 mb-4">✅ Test Réussi !</h1>
      <p class="text-lg mb-2">{{ message }}</p>
      <p class="text-sm text-gray-600">Timestamp: {{ timestamp }}</p>
      <div class="mt-4 p-4 bg-blue-50 rounded">
        <h3 class="font-semibold">Informations techniques :</h3>
        <ul class="text-sm mt-2">
          <li>✅ Laravel : Fonctionnel</li>
          <li>✅ Inertia.js : Fonctionnel</li>
          <li>✅ Vue.js 3 : Fonctionnel</li>
          <li>✅ Vite Build : Fonctionnel</li>
          <li>✅ TailwindCSS : Fonctionnel</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
// Props reçues du backend Laravel
defineProps({
  message: String,
  timestamp: String
})
</script>

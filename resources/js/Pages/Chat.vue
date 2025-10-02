<template>
  <div class="flex h-screen bg-gradient-to-br from-blue-50 to-indigo-100">

    <!-- D'abord un sidebar avec liste des conversations -->
    <div class="w-1/3 bg-white shadow-xl border-r border-gray-200 flex flex-col">
      <!-- Header du sidebar -->
      <div class="p-6 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <img :src="currentUser.avatar_url" :alt="currentUser.name"
                 class="w-10 h-10 rounded-full border-2 border-white/30">
            <div>
              <h1 class="text-lg font-semibold">{{ currentUser.name }}</h1>
              <p class="text-blue-100 text-sm">Messages</p>
            </div>
          </div>
          <button @click="showNewChatModal = true"
                  class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
            <PlusIcon class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Ensuite la fameuse liste des conversations -->
      <div class="flex-1 overflow-y-auto">
        <div v-if="conversations.length === 0" class="p-6 text-center text-gray-500">
          <ChatBubbleLeftRightIcon class="w-12 h-12 mx-auto mb-4 text-gray-300" />
          <p class="font-medium">Aucune conversation</p>
          <p class="text-sm">Cliquez sur + pour commencer</p>
        </div>

        <div v-else class="divide-y divide-gray-100">
          <div v-for="conversation in conversations" :key="conversation.id"
               @click="openConversation(conversation.id)"
               class="p-4 hover:bg-blue-50 cursor-pointer transition-all duration-200 transform hover:translate-x-1">
            <div class="flex items-center space-x-3">
              <div class="relative">
                <img :src="conversation.avatar_url" :alt="conversation.title"
                     class="w-12 h-12 rounded-full object-cover">
                <!-- ça c'est un point important - indicateur de présence pour les conversations privées - J'ai utilisé des propriétés d'objet pour afficher l'horodatage par exemple -->
                <div v-if="conversation.type === 'private' && isUserOnline(conversation)"
                     class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <h3 class="font-medium text-gray-900 truncate">{{ conversation.title }}</h3>
                  <span class="text-xs text-gray-500">{{ conversation.last_activity }}</span>
                </div>

                <div class="flex items-center justify-between mt-1">
                  <p class="text-sm text-gray-600 truncate">
                    <span v-if="conversation.last_message_user" class="font-medium">
                      {{ conversation.last_message_user.name }}:
                    </span>
                    {{ conversation.last_message || 'Aucun message' }}
                  </p>

                  <span v-if="conversation.unread_count > 0"
                        class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full min-w-[20px] text-center">
                    {{ conversation.unread_count }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- La zone principale - affichage des conversations -->
    <div class="flex-1 flex flex-col">
      <div v-if="!selectedConversation" class="flex-1 flex items-center justify-center bg-gray-50">
        <div class="text-center">
          <ChatBubbleLeftRightIcon class="w-24 h-24 mx-auto mb-6 text-gray-300" />
          <h2 class="text-2xl font-bold text-gray-600 mb-2">Bienvenue dans App-Messagerie</h2>
          <p class="text-gray-500 max-w-md mx-auto">
            Sélectionnez une conversation dans la liste de gauche ou créez-en une nouvelle
            pour commencer à échanger des messages en temps réel.
          </p>
        </div>
      </div>
    </div>

    <!-- Modal pour une nouvelle conversation -->
    <div v-if="showNewChatModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-96 max-w-md mx-4">
        <h3 class="text-lg font-semibold mb-4">Nouvelle conversation</h3>

        <div class="space-y-2 max-h-64 overflow-y-auto">
          <div v-for="user in availableUsers" :key="user.id"
               @click="startPrivateChat(user.id)"
               class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
            <img :src="user.avatar_url" :alt="user.name"
                 class="w-10 h-10 rounded-full object-cover">
            <div class="flex-1">
              <p class="font-medium text-gray-900">{{ user.name }}</p>
              <p class="text-sm text-gray-500">{{ user.email }}</p>
            </div>
            <div v-if="user.is_online" class="w-3 h-3 bg-green-500 rounded-full"></div>
          </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
          <button @click="showNewChatModal = false"
                  class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
            Annuler
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { PlusIcon, ChatBubbleLeftRightIcon } from '@heroicons/vue/24/outline'

// Props reçues du contrôleur Laravel
const props = defineProps({
  conversations: Array,
  availableUsers: Array,
  currentUser: Object
})

// État réactif du composant
const showNewChatModal = ref(false)
const selectedConversation = ref(null)

/**
 * Ouvrir une conversation spécifique
 */
const openConversation = (conversationId) => {
  router.visit(`/chat/${conversationId}`)
}

/**
 * Possibilité de démarrer une nouvelle conversation privée
 */
const startPrivateChat = (userId) => {
  router.post('/chat/private', { user_id: userId }, {
    onSuccess: () => {
      showNewChatModal.value = false
    }
  })
}

/**
 * On vérifie si un utilisateur est en ligne (pour les conversations privées)
 */
const isUserOnline = (conversation) => {
  if (conversation.type !== 'private') return false

  // Trouver l'autre participant (pas l'utilisateur actuel)
  const otherParticipant = conversation.participants.find(p => p.id !== props.currentUser.id)
  return otherParticipant?.is_online || false
}

// Actions au montage du composant
onMounted(() => {
  // Ici on pourrait ajouter des écouteurs d'événements WebSocket pour les mises à jour en temps réel
  console.log('Chat principal initialisé avec', props.conversations.length, 'conversations')
})
</script>

<style scoped>
/* Animations personnalisées */
.conversation-item {
  transition: all 0.2s ease-in-out;
}

.conversation-item:hover {
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Scrollbar personnalisée */
::-webkit-scrollbar {
  width: 4px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 2px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}
</style>

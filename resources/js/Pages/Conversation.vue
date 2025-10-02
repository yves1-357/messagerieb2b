<template>
  <div class="flex h-screen bg-gradient-to-br from-blue-50 to-indigo-100">

    <!-- Ici, je place un sidebar avec liste des conversations simplifiée -->
    <div class="w-80 bg-white shadow-xl border-r border-gray-200 flex flex-col">
      <div class="p-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <Link href="/chat" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
          <ArrowLeftIcon class="w-5 h-5" />
          <span class="font-medium">Retour aux conversations</span>
        </Link>
      </div>


      <!-- Informations de la conversation -->
      <div class="p-4 border-b border-gray-200">
        <div class="flex items-center space-x-3">
          <div class="relative">
            <div v-if="conversation.type === 'private'" class="w-12 h-12 rounded-full overflow-hidden">
              <img :src="getConversationAvatar()" :alt="conversation.title"
                   class="w-full h-full object-cover">
            </div>
            <div v-else class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold">
              {{ conversation.title.charAt(0).toUpperCase() }}
            </div>
            <div v-if="isOtherUserOnline()"
                 class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
          </div>

          <div class="flex-1">
            <h2 class="font-semibold text-gray-900">{{ conversation.title }}</h2>
            <p class="text-sm text-gray-500">
              {{ conversation.participants.length }} participant{{ conversation.participants.length > 1 ? 's' : '' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Liste des participants -->
      <div class="p-4 space-y-2">
        <h3 class="text-sm font-medium text-gray-700 uppercase tracking-wide">Participants</h3>
        <div v-for="participant in conversation.participants" :key="participant.id"
             class="flex items-center space-x-2">
          <img :src="participant.avatar_url" :alt="participant.name"
               class="w-8 h-8 rounded-full object-cover">
          <div class="flex-1">
            <p class="text-sm font-medium text-gray-900">{{ participant.name }}</p>
            <div class="flex items-center space-x-1">
              <div :class="participant.is_online ? 'bg-green-500' : 'bg-gray-400'"
                   class="w-2 h-2 rounded-full"></div>
              <span class="text-xs text-gray-500">
                {{ participant.is_online ? 'En ligne' : 'Hors ligne' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Zone principale de conversation -->
    <div class="flex-1 flex flex-col">
      <!-- Header de la conversation -->
      <div class="bg-white border-b border-gray-200 p-4 shadow-sm">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <h1 class="text-lg font-semibold text-gray-900">{{ conversation.title }}</h1>
            <div v-if="typingUsers.length > 0" class="flex items-center space-x-1 text-sm text-gray-500">
              <div class="flex space-x-1">
                <div class="w-1 h-1 bg-gray-400 rounded-full animate-bounce"></div>
                <div class="w-1 h-1 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-1 h-1 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
              </div>
              <span>{{ getTypingText() }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Zone des messages -->
      <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
        <div v-for="(message, index) in messages" :key="message.id"
             class="flex" :class="message.user.id === currentUser.id ? 'justify-end' : 'justify-start'">

          <!-- Message d'un autre utilisateur -->
          <div v-if="message.user.id !== currentUser.id" class="flex space-x-3 max-w-xs lg:max-w-md">
            <img :src="message.user.avatar_url" :alt="message.user.name"
                 class="w-8 h-8 rounded-full object-cover flex-shrink-0">

            <div class="flex-1">
              <div class="bg-white rounded-2xl px-4 py-2 shadow-sm">
                <div v-if="message.type === 'image'" class="mb-2">
                  <img :src="message.file_url" :alt="message.file_name"
                       class="max-w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                       @click="openImageModal(message.file_url)">
                </div>

                <div v-else-if="message.type === 'file'" class="mb-2">
                  <a :href="message.file_url" :download="message.file_name"
                     class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <DocumentArrowDownIcon class="w-5 h-5" />
                    <span class="text-sm font-medium">{{ message.file_name }}</span>
                  </a>
                </div>

                <p v-if="message.content" class="text-gray-800">{{ message.content }}</p>
              </div>

              <div class="flex items-center space-x-2 mt-1">
                <span class="text-xs text-gray-500">{{ message.user.name }}</span>
                <span class="text-xs text-gray-400">{{ formatMessageTime(message.created_at) }}</span>
              </div>
            </div>
          </div>

          <!-- Message de l'utilisateur actuel -->
          <div v-else class="flex space-x-3 max-w-xs lg:max-w-md">
            <div class="flex-1">
              <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-2xl px-4 py-2 shadow-sm">
                <div v-if="message.type === 'image'" class="mb-2">
                  <img :src="message.file_url" :alt="message.file_name"
                       class="max-w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                       @click="openImageModal(message.file_url)">
                </div>

                <div v-else-if="message.type === 'file'" class="mb-2">
                  <a :href="message.file_url" :download="message.file_name"
                     class="flex items-center space-x-2 text-white hover:text-blue-100 transition-colors">
                    <DocumentArrowDownIcon class="w-5 h-5" />
                    <span class="text-sm font-medium">{{ message.file_name }}</span>
                  </a>
                </div>

                <p v-if="message.content">{{ message.content }}</p>
              </div>

              <div class="flex justify-end mt-1">
                <span class="text-xs text-gray-400">{{ formatMessageTime(message.created_at) }}</span>
              </div>
            </div>

            <img :src="message.user.avatar_url" :alt="message.user.name"
                 class="w-8 h-8 rounded-full object-cover flex-shrink-0">
          </div>
        </div>
      </div>

      <!-- La zone de saisie du message avec le bouton fichie juste à côté -->
      <div class="bg-white border-t border-gray-200 p-4">
        <form @submit.prevent="sendMessage" class="flex items-end space-x-3">
          <!-- Bouton d'upload de fichier -->
          <div class="relative">
            <input ref="fileInput" type="file" class="hidden"
                   @change="handleFileSelect"
                   accept="image/*,.pdf,.doc,.docx">
            <button type="button" @click="$refs.fileInput.click()"
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors">
              <PaperClipIcon class="w-5 h-5" />
            </button>
          </div>

          <!-- Champ de saisie -->
          <div class="flex-1 relative">
            <textarea v-model="newMessage"
                      @keydown="handleKeyDown"
                      @input="handleTyping"
                      placeholder="Tapez votre message..."
                      rows="1"
                      class="w-full px-4 py-2 border border-gray-300 rounded-full resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                      style="min-height: 40px; max-height: 120px;"></textarea>

            <!-- Aperçu du fichier sélectionné -->
            <div v-if="selectedFile" class="absolute -top-16 left-0 right-0 bg-white border border-gray-200 rounded-lg p-2 shadow-lg">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                  <DocumentIcon class="w-5 h-5 text-gray-500" />
                  <span class="text-sm text-gray-700">{{ selectedFile.name }}</span>
                </div>
                <button type="button" @click="removeSelectedFile"
                        class="text-gray-500 hover:text-red-500 transition-colors">
                  <XMarkIcon class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>

          <!-- Bouton d'envoi -->
          <button type="submit" :disabled="!canSendMessage"
                  class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-3 rounded-full hover:from-blue-600 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-md hover:shadow-lg">
            <PaperAirplaneIcon class="w-5 h-5" />
          </button>
        </form>
      </div>
    </div>

    <!-- Modal pour afficher les images -->
    <div v-if="imageModalSrc" @click="closeImageModal"
         class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
      <div class="max-w-4xl max-h-full p-4">
        <img :src="imageModalSrc" class="max-w-full max-h-full object-contain rounded-lg">
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, computed, onUnmounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  PaperAirplaneIcon,
  PaperClipIcon,
  DocumentArrowDownIcon,
  DocumentIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

// Props du contrôleur Laravel
const props = defineProps({
  conversation: Object,
  messages: Array,
  currentUser: Object
})

// État réactif
const messagesContainer = ref(null)
const fileInput = ref(null)
const newMessage = ref('')
const selectedFile = ref(null)
const imageModalSrc = ref(null)
const typingUsers = ref([])
const typingTimeout = ref(null)

// Configuration Echo pour WebSocket
let echo = null

// Computed properties
const canSendMessage = computed(() => {
  return newMessage.value.trim() || selectedFile.value
})

/**
 * Configuration et initialisation d'Echo (WebSocket)
 */
onMounted(async () => {
  // Initialiser Laravel Echo
  if (window.Echo) {
    echo = window.Echo

    // Écouter les nouveaux messages sur le canal de la conversation
    echo.private(`conversation.${props.conversation.id}`)
      .listen('message.sent', (e) => {
        // Ajouter le nouveau message à la liste
        props.messages.push(e)
        scrollToBottom()
      })
      .listen('user.typing', (e) => {
        handleTypingEvent(e)
      })
  }

  // Faire défiler vers le bas au chargement
  await nextTick()
  scrollToBottom()
})

// Nettoyage lors du démontage du composant
onUnmounted(() => {
  if (echo) {
    echo.leave(`conversation.${props.conversation.id}`)
  }
})

/**
 * Envoyer un message
 */
const sendMessage = async () => {
  if (!canSendMessage.value) return

  const formData = new FormData()
  formData.append('content', newMessage.value)

  if (selectedFile.value) {
    formData.append('file', selectedFile.value)
  }

  try {
    const response = await fetch(`/chat/${props.conversation.id}/message`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest'
      }
    })

    if (response.ok) {
      const data = await response.json()

      // Ajouter le message à la liste locale
      props.messages.push(data.message)

      // Réinitialiser le formulaire
      newMessage.value = ''
      selectedFile.value = null

      // Faire défiler vers le bas
      scrollToBottom()

      // Arrêter l'indicateur de frappe
      sendTypingStatus(false)
    }
  } catch (error) {
    console.error('Erreur lors de l\'envoi du message:', error)
  }
}

/**
 * Gérer la sélection de fichier
 */
const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedFile.value = file
  }
}

/**
 * Supprimer le fichier sélectionné
 */
const removeSelectedFile = () => {
  selectedFile.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

/**
 * Gérer les touches du clavier
 */
const handleKeyDown = (event) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault()
    sendMessage()
  }
}

/**
 * Gérer l'indicateur de frappe
 */
const handleTyping = () => {
  sendTypingStatus(true)

  // Réinitialiser le timeout
  if (typingTimeout.value) {
    clearTimeout(typingTimeout.value)
  }

  // Arrêter l'indicateur après 2 secondes d'inactivité
  typingTimeout.value = setTimeout(() => {
    sendTypingStatus(false)
  }, 2000)
}

/**
 * Envoyer le statut de frappe
 */
const sendTypingStatus = async (isTyping) => {
  try {
    await fetch(`/chat/${props.conversation.id}/typing`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ is_typing: isTyping })
    })
  } catch (error) {
    console.error('Erreur lors de l\'envoi du statut de frappe:', error)
  }
}

/**
 * Gérer les événements de frappe reçus
 */
const handleTypingEvent = (event) => {
  if (event.user.id === props.currentUser.id) return

  if (event.is_typing) {
    if (!typingUsers.value.find(u => u.id === event.user.id)) {
      typingUsers.value.push(event.user)
    }
  } else {
    typingUsers.value = typingUsers.value.filter(u => u.id !== event.user.id)
  }

  // Supprimer automatiquement après 3 secondes
  setTimeout(() => {
    typingUsers.value = typingUsers.value.filter(u => u.id !== event.user.id)
  }, 3000)
}

/**
 * Obtenir le texte d'indicateur de frappe
 */
const getTypingText = () => {
  if (typingUsers.value.length === 1) {
    return `${typingUsers.value[0].name} est en train d'écrire`
  } else if (typingUsers.value.length > 1) {
    return `${typingUsers.value.length} personnes sont en train d'écrire`
  }
  return ''
}

/**
 * Faire défiler vers le bas de la zone de messages
 */
const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

/**
 * Obtenir l'avatar de la conversation
 */
const getConversationAvatar = () => {
  if (props.conversation.type === 'private') {
    const otherParticipant = props.conversation.participants.find(p => p.id !== props.currentUser.id)
    return otherParticipant?.avatar_url || ''
  }
  return ''
}

/**
 * Vérifier si l'autre utilisateur est en ligne (conversations privées)
 */
const isOtherUserOnline = () => {
  if (props.conversation.type !== 'private') return false

  const otherParticipant = props.conversation.participants.find(p => p.id !== props.currentUser.id)
  return otherParticipant?.is_online || false
}

/**
 * On va formater l'heure du message ici
 */
const formatMessageTime = (timestamp) => {
  const date = new Date(timestamp)
  const now = new Date()
  const diffInHours = (now - date) / (1000 * 60 * 60)

  if (diffInHours < 24) {
    return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
  } else {
    return date.toLocaleDateString('fr-FR', {
      day: 'numeric',
      month: 'short',
      hour: '2-digit',
      minute: '2-digit'
    })
  }
}

/**
 * Ouvrir la modal d'image
 */
const openImageModal = (src) => {
  imageModalSrc.value = src
}

/**
 * Fermer la modal d'image
 */
const closeImageModal = () => {
  imageModalSrc.value = null
}
</script>

<style scoped>
/* Point important avec les animations pour l'indicateur de frappe */
@keyframes bounce {
  0%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-4px);
  }
}

.animate-bounce {
  animation: bounce 1s infinite;
}

/* Style personnalisé pour la scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}
</style>

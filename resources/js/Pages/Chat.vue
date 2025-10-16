<template>
  <!-- Layout avec 3 colonnes - DESKTOP FIXED HEIGHT -->
  <div class="h-screen bg-gray-900 text-white flex overflow-hidden max-h-screen">

    <!-- COLONNE GAUCHE - Profil + Sidebar -->
    <div class="w-80 bg-gray-800 flex flex-col border-r border-gray-700 h-full">
      <!-- Composant Profil (header) avec conversations pour badge -->
      <Profil
        :conversations="conversations"
        @search="handleSearch"
        @show-user-info="handleShowUserInfo"
        @user-updated="handleUserUpdated"
        @theme-changed="handleThemeChanged"
        @start-conversation="handleStartConversation"
      />

      <!-- Composant Sidebar (conversations) -->
      <Sidebar
        :conversations="conversations"
        :selected-conversation-id="selectedConversation?.id"
        :search-query="searchQuery"
        :is-loading="isLoading"
        @select-conversation="selectConversation"
        @new-group="handleNewGroup"
        @new-message="handleNewMessage"
      />
    </div>

    <!-- COLONNE CENTRALE - Zone de chat -->
    <div class="flex-1 flex flex-col h-full min-h-0">
      <div v-if="selectedConversation" class="flex flex-col h-full">
        <!-- En-tête de la conversation - HAUTEUR FIXE -->
        <div class="bg-gray-800 p-4 border-b border-gray-700 flex-shrink-0">
          <div class="flex items-center justify-between">
            <div
              @click="showConversationUserInfo"
              class="flex items-center space-x-3 cursor-pointer hover:bg-gray-700 rounded-lg p-2 -m-2 transition-colors"
            >
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm"
                :style="{ backgroundColor: selectedConversation.avatarColor }"
              >
                {{ getInitials(selectedConversation.name) }}
              </div>
              <div>
                <h2 class="font-semibold text-white">{{ selectedConversation.name }}</h2>
                <p class="text-sm text-gray-400">
                  {{ selectedConversation.isOnline ? 'en ligne' : getLastSeenText(selectedConversation.lastSeen) }}
                </p>
              </div>
            </div>

            <div class="flex items-center space-x-2">
              <button class="p-2 hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </button>
              <button class="p-2 hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
              </button>
              <button class="p-2 hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Zone des messages - -->
        <div class="flex-1 overflow-y-auto bg-gray-900 relative min-h-0 scrollbar-hide" ref="messagesContainer">
          <div v-if="currentMessages.length === 0" class="flex items-center justify-center h-full absolute inset-0">
            <div class="text-center text-gray-400">
              <!-- Sticker cute avec pattern background -->
              <div class="mb-6 relative">
                <div class="w-32 h-32 mx-auto mb-4 text-8xl select-none">
                  🐾
                </div>
              </div>
              <h3 class="text-lg font-medium mb-2 text-gray-300">No messages here yet...</h3>
              <p class="text-sm text-gray-500">Send a message below.</p>
            </div>
          </div>

          <!-- Messages - CENTRÉ -->
          <div v-else class="flex flex-col items-center w-full">
            <div class="w-full max-w-4xl px-4 py-4 space-y-4">
              <!-- Indicateur de date -->
              <div class="text-center">
                <span class="bg-gray-700 text-gray-300 text-xs px-3 py-1 rounded-full">
                  Aujourd'hui
                </span>
              </div>

              <div v-for="message in currentMessages" :key="message.id" class="flex" :class="{ 'justify-end': message.isOwn, 'justify-start': !message.isOwn }">

                <!-- Messages des autres -->
                <div v-if="!message.isOwn" class="flex items-end space-x-2 max-w-md">
                  <div
                    class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0"
                    :style="{ backgroundColor: selectedConversation.avatarColor }"
                  >
                    {{ getInitials(selectedConversation.name) }}
                  </div>
                  <div class="bg-gray-700 text-white rounded-2xl rounded-bl-sm px-4 py-2 shadow-lg">
                    <p class="text-sm">{{ message.content }}</p>
                    <span class="text-xs text-gray-400 mt-1 block">{{ formatMessageTime(message.timestamp) }}</span>
                  </div>
                </div>

                <!-- Mes messages -->
                <div v-else class="flex items-end space-x-2 max-w-md">
                  <div class="bg-blue-600 text-white rounded-2xl rounded-br-sm px-4 py-2 shadow-lg">
                    <p class="text-sm">{{ message.content }}</p>
                    <div class="flex items-center justify-end space-x-1 mt-1">
                      <span class="text-xs text-blue-200">{{ formatMessageTime(message.timestamp) }}</span>
                      <svg v-if="message.status === 'sent'" class="w-3 h-3 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <svg v-else-if="message.status === 'read'" class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Zone de saisie - HAUTEUR FIXE EN BAS ET CENTRÉE -->
        <div class=" p-3 flex-shrink-0 flex justify-center">
          <div class="w-full max-w-4xl flex items-center space-x-2">
            <!-- Bouton emoji -->
            <button class="p-2 text-gray-400 hover:text-white transition-colors rounded-full hover:bg-gray-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </button>

            <!-- Zone de texte avec style -->
            <div class="flex-1 relative">
              <textarea
                v-model="newMessage"
                @keydown.enter.exact.prevent="sendMessage"
                @keydown.enter.shift.exact="handleShiftEnter"
                @input="autoResize"
                placeholder="Message"
                rows="1"
                class="w-full bg-gray-700 text-white placeholder-gray-400 rounded-2xl pl-4 pr-12 py-3 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm"
                style="min-height: 48px; max-height: 120px; line-height: 1.4;"
                ref="messageInput"
              ></textarea>

              <!-- Bouton attach dans le textarea -->
              <button class="absolute right-3 top-1/2 transform -translate-y-1/2 p-1 text-gray-400 hover:text-white transition-colors rounded-full hover:bg-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
              </button>
            </div>

            <!-- Bouton d'envoi-->
            <button
              @click="sendMessage"
              :disabled="!newMessage.trim()"
              class="w-12 h-12 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-600 disabled:cursor-not-allowed rounded-full transition-colors flex items-center justify-center"
            >
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- État quand aucune conversation sélectionnée -->
      <div v-else class="flex-1 flex flex-col">
        <!-- Zone vide -->
        <div class="flex-1 flex items-center justify-center bg-gray-900">
          <div class="text-center text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <h3 class="text-lg font-medium mb-2">Sélectionnez une conversation</h3>
            <p class="text-sm">Choisissez une conversation pour commencer à discuter</p>
          </div>
        </div>

        <!-- Barre de saisie CENTRÉE ET TOUJOURS présente en bas -->
        <div class="bg-gray-800 p-3 border-t border-gray-700 flex justify-center">
          <div class="w-full max-w-4xl flex items-center space-x-2">
            <!-- Bouton emoji -->
            <button disabled class="p-2 text-gray-600 rounded-full cursor-not-allowed">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </button>

            <!-- Zone de texte désactivée -->
            <div class="flex-1 relative">
              <textarea
                disabled
                placeholder="Sélectionnez une conversation pour envoyer un message"
                rows="1"
                class="w-full bg-gray-700 text-gray-500 placeholder-gray-500 rounded-2xl pl-4 pr-12 py-3 resize-none cursor-not-allowed text-sm"
                style="min-height: 48px; line-height: 1.4;"
              ></textarea>

              <!-- Bouton attach désactivé -->
              <button disabled class="absolute right-3 top-1/2 transform -translate-y-1/2 p-1 text-gray-600 rounded-full cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
              </button>
            </div>

            <!-- Bouton d'envoi désactivé -->
            <button
              disabled
              class="w-12 h-12 bg-gray-600 cursor-not-allowed rounded-full flex items-center justify-center"
            >
              <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- COLONNE DROITE - UserInfo (conditionnel) -->
    <UserInfo
      v-if="showUserInfo && (selectedConversation || selectedUser)"
      :user="selectedUser || selectedConversationUser"
      @close="showUserInfo = false"
      @start-conversation="handleStartConversation"
      class="h-full"
    />
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import Profil from './Profil.vue';
import Sidebar from './Sidebar.vue';
import UserInfo from './UserInfo.vue';

// État de l'application
const searchQuery = ref('');
const selectedConversation = ref(null);
const selectedUser = ref(null);
const showUserInfo = ref(false);
const newMessage = ref('');
const messagesContainer = ref(null);
const messageInput = ref(null);

// États pour les données réelles
const conversations = ref([]);
const allMessages = ref({});
const isLoading = ref(false);

// Computed
const currentMessages = computed(() => {
  if (!selectedConversation.value) return [];
  return allMessages.value[selectedConversation.value.id] || [];
});

const selectedConversationUser = computed(() => {
  if (!selectedConversation.value) return null;

  return {
    ...selectedConversation.value,
    email: selectedConversation.value.email || 'N/A',
    phone: selectedConversation.value.phone || 'N/A',
    username: selectedConversation.value.username || 'N/A',
    lastSeen: new Date(Date.now() - 6 * 60000) // 6 minutes ago
  };
});

// Méthodes
const getInitials = (name) => {
  if (!name) return 'U';
  return name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .substring(0, 2);
};

const formatMessageTime = (timestamp) => {
  if (!timestamp) return '';
  const date = new Date(timestamp);
  return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
};

const getLastSeenText = (lastSeen) => {
  if (!lastSeen) return 'vu pour la dernière fois il y a longtemps';

  const now = new Date();
  const lastSeenDate = new Date(lastSeen);
  const diff = now - lastSeenDate;

  const minutes = Math.floor(diff / (1000 * 60));
  if (minutes < 1) return 'vu à l\'instant';
  if (minutes < 60) return `vu il y a ${minutes} minute${minutes > 1 ? 's' : ''}`;

  return 'vu pour la dernière fois il y a 6 minutes';
};

const selectConversation = async (conversation) => {
  selectedConversation.value = conversation;
  showUserInfo.value = false; // Fermer le panel utilisateur

  // Charger les messages de cette conversation si pas encore chargés
  if (!allMessages.value[conversation.id]) {
    await loadConversationMessages(conversation.id);
  }

  scrollToBottom();
};

const showConversationUserInfo = () => {
  if (!selectedConversation.value) return;

  // Créer un objet utilisateur basé sur la conversation
  const conversationUser = {
    id: selectedConversation.value.id,
    name: selectedConversation.value.name,
    username: selectedConversation.value.username || null,
    email: selectedConversation.value.email || null,
    avatarColor: selectedConversation.value.avatarColor,
    status: selectedConversation.value.isOnline ? 'online' : 'offline',
    last_seen_at: selectedConversation.value.lastSeen || null,
    created_at: selectedConversation.value.created_at || null
  };

  // Si la conversation a des utilisateurs spécifiques, utiliser le premier qui n'est pas l'utilisateur actuel
  if (selectedConversation.value.users && selectedConversation.value.users.length > 0) {
    const currentUserId = usePage().props.auth?.user?.id;
    const otherUser = selectedConversation.value.users.find(user => user.id !== currentUserId);
    if (otherUser) {
      selectedUser.value = otherUser;
    } else {
      selectedUser.value = conversationUser;
    }
  } else {
    selectedUser.value = conversationUser;
  }

  showUserInfo.value = true;
};

const handleSearch = (query) => {
  searchQuery.value = query;
};

const handleShowUserInfo = (data) => {
  if (data && data.user) {
    // Ouvrir le UserInfo panel avec l'utilisateur spécifié
    selectedUser.value = data.user;
    showUserInfo.value = true;
  }
};

const handleUserUpdated = (updatedUser) => {
  // Mettre à jour l'utilisateur actuel dans le store global
  if (window.Laravel && window.Laravel.user) {
    window.Laravel.user = updatedUser;
  }

  // Mettre à jour les props Inertia pour la réactivité globale
  const { props } = usePage();
  if (props.auth && props.auth.user) {
    props.auth.user = updatedUser;
  }

  // Si le UserInfo panel affiche cet utilisateur, le mettre à jour
  if (selectedUser.value && selectedUser.value.id === updatedUser.id) {
    selectedUser.value = updatedUser;
  }
};

const handleThemeChanged = (theme) => {
  // Appliquer le thème au document
  const html = document.documentElement;
  if (theme === 'dark') {
    html.classList.add('dark');
  } else {
    html.classList.remove('dark');
  }

  // Sauvegarder dans localStorage
  localStorage.setItem('theme', theme);
};

const handleStartConversation = async (user) => {
  try {
    // Vérifier si une conversation avec cet utilisateur existe déjà
    const existingConversation = conversations.value.find(conv =>
      conv.users && conv.users.some(u => u.id === user.id)
    );

    if (existingConversation) {
      // Sélectionner la conversation existante
      await selectConversation(existingConversation);
    } else {
      // Créer une nouvelle conversation
      await createConversation(user);
    }

    // Fermer le UserInfo panel s'il est ouvert
    showUserInfo.value = false;
    selectedUser.value = null;
  } catch (error) {
    console.error('Erreur lors de la création de conversation:', error);
  }
};

const handleNewGroup = () => {
  console.log('Créer un nouveau groupe');
  // A faire apres : Implémenter la création de groupe
};

const handleNewMessage = () => {
  console.log('Nouveau message');
  // A faire apres : Implémenter nouveau message
};

const sendMessage = () => {
  if (!newMessage.value.trim() || !selectedConversation.value) return;

  const message = {
    id: Date.now(),
    content: newMessage.value.trim(),
    timestamp: new Date(),
    isOwn: true,
    status: 'sent'
  };

  if (!allMessages.value[selectedConversation.value.id]) {
    allMessages.value[selectedConversation.value.id] = [];
  }

  allMessages.value[selectedConversation.value.id].push(message);
  selectedConversation.value.lastMessage = message.content;
  selectedConversation.value.lastMessageTime = message.timestamp;
  selectedConversation.value.lastMessageFromMe = true;

  newMessage.value = '';
  resetTextareaHeight();
  scrollToBottom();
};

const handleShiftEnter = (event) => {
  // Permettre le saut de ligne avec Shift+Enter
  const target = event.target;
  nextTick(() => {
    autoResize();
  });
};

const autoResize = () => {
  const textarea = messageInput.value;
  if (textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
  }
};

const resetTextareaHeight = () => {
  nextTick(() => {
    if (messageInput.value) {
      messageInput.value.style.height = '48px';
    }
  });
};

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  });
};

// ========================
// FONCTIONS API
// ========================

// Charger les conversations depuis l'API
const loadConversations = async () => {
  try {
    isLoading.value = true;
    const response = await axios.get('/api/conversations');
    conversations.value = response.data.data || [];
  } catch (error) {
    console.error('Erreur lors du chargement des conversations:', error);
    conversations.value = [];
  } finally {
    isLoading.value = false;
  }
};

// Charger les messages d'une conversation
const loadConversationMessages = async (conversationId) => {
  try {
    const response = await axios.get(`/api/conversations/${conversationId}`);
    allMessages.value[conversationId] = response.data.messages || [];
  } catch (error) {
    console.error('Erreur lors du chargement des messages:', error);
    allMessages.value[conversationId] = [];
  }
};

// Créer une nouvelle conversation
const createConversation = async (user) => {
  try {
    const response = await axios.post('/api/conversations', {
      user_id: user.id
    });

    const newConversation = response.data;

    // Ajouter la nouvelle conversation à la liste
    conversations.value.unshift(newConversation);

    // Sélectionner la nouvelle conversation
    selectConversation(newConversation);

    return newConversation;
  } catch (error) {
    console.error('Erreur lors de la création de la conversation:', error);
    throw error;
  }
};// Lifecycle
onMounted(async () => {
  // Charger les conversations
  await loadConversations();

  // Initialiser le thème depuis localStorage
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme) {
    const html = document.documentElement;
    if (savedTheme === 'dark') {
      html.classList.add('dark');
    } else {
      html.classList.remove('dark');
    }
  }
});
</script>

<style scoped>
/* Masquer la scrollbar pour un look professionnel */
.scrollbar-hide {
  -ms-overflow-style: none;  /* Internet Explorer 10+ */
  scrollbar-width: none;  /* Firefox */
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;  /* Safari and Chrome */
}
</style>

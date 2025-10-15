<template>
  <div class="flex-1 bg-gray-800 flex flex-col">
    <!-- Header avec bouton d'actions -->
    <div class="p-3 border-b border-gray-700 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-white">Conversations</h2>

      <!-- Bouton actions (3 points) -->
      <div class="relative">
        <button
          @click="toggleActionsMenu"
          class="p-2 hover:bg-gray-700 rounded-lg transition-colors"
        >
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
          </svg>
        </button>

        <!-- Menu actions popup -->
        <div
          v-if="showActionsMenu"
          ref="actionsDropdown"
          class="absolute top-full right-0 mt-2 w-48 bg-gray-700 rounded-lg shadow-lg z-50 py-2"
        >
          <button
            @click="handleAction('newGroup')"
            class="w-full text-left px-4 py-2 text-white hover:bg-gray-600 transition-colors flex items-center space-x-3"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>New Group</span>
          </button>

          <button
            @click="handleAction('newMessage')"
            class="w-full text-left px-4 py-2 text-white hover:bg-gray-600 transition-colors flex items-center space-x-3"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>New Message</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Liste des conversations -->
    <div class="flex-1 overflow-y-auto">
      <div v-if="filteredConversations.length === 0" class="p-4 text-center text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <p class="text-sm">Aucune conversation</p>
        <button
          @click="handleAction('newMessage')"
          class="mt-2 text-blue-400 hover:text-blue-300 text-sm font-medium"
        >
          Créer votre première conversation
        </button>
      </div>

      <div v-else>
        <div
          v-for="conversation in filteredConversations"
          :key="conversation.id"
          @click="selectConversation(conversation)"
          class="p-3 hover:bg-gray-700 cursor-pointer transition-colors"
          :class="{ 'bg-gray-700 border-l-4 border-l-blue-500': selectedConversationId === conversation.id }"
        >
          <div class="flex items-center space-x-3">
            <!-- Avatar -->
            <div class="relative flex-shrink-0">
              <div
                class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-sm"
                :style="{ backgroundColor: conversation.avatarColor }"
              >
                {{ getInitials(conversation.name) }}
              </div>

              <!-- Indicateur en ligne -->
              <div
                v-if="conversation.isOnline"
                class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-green-500 border-2 border-gray-800 rounded-full"
              ></div>
            </div>

            <!-- Informations conversation -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between">
                <span class="font-medium text-white truncate">{{ conversation.name }}</span>
                <span class="text-xs text-gray-400 flex-shrink-0">{{ formatTime(conversation.lastMessageTime) }}</span>
              </div>

              <div class="flex items-center justify-between mt-1">
                <div class="flex items-center space-x-1 flex-1 min-w-0">
                  <!-- Icône de statut du message -->
                  <svg v-if="conversation.lastMessageFromMe" class="w-3 h-3 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>

                  <span class="text-sm text-gray-400 truncate">{{ conversation.lastMessage }}</span>
                </div>

                <!-- Badge messages non lus -->
                <div v-if="conversation.unreadCount > 0" class="bg-blue-500 text-white text-xs rounded-full px-2 py-0.5 min-w-[1.25rem] text-center flex-shrink-0 ml-2">
                  {{ conversation.unreadCount > 99 ? '99+' : conversation.unreadCount }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

// Props
const props = defineProps({
  conversations: {
    type: Array,
    default: () => []
  },
  selectedConversationId: {
    type: [Number, String],
    default: null
  },
  searchQuery: {
    type: String,
    default: ''
  }
});

// Emits
const emit = defineEmits(['selectConversation', 'newGroup', 'newMessage']);

// État du composant
const showActionsMenu = ref(false);
const actionsDropdown = ref(null);

// Données fictives pour les conversations
const defaultConversations = ref([
  {
    id: 1,
    name: 'Carmen Goina',
    avatarColor: '#8B5CF6',
    isOnline: true,
    lastMessage: 'hey question par curiosité tes sur vuemo',
    lastMessageTime: new Date(Date.now() - 5 * 60000), // 5 minutes ago
    lastMessageFromMe: false,
    unreadCount: 1
  },
  {
    id: 2,
    name: 'J, Marian, Celia and Fi Fou',
    avatarColor: '#10B981',
    isOnline: false,
    lastMessage: 'You: J created the group «J, Marian, Celia a...',
    lastMessageTime: new Date(Date.now() - 10 * 60000), // 10 minutes ago
    lastMessageFromMe: true,
    unreadCount: 0
  },
  {
    id: 3,
    name: 'Winner 💎💎💎',
    avatarColor: '#F59E0B',
    isOnline: false,
    lastMessage: '📷 Photo',
    lastMessageTime: new Date(Date.now() - 24 * 60 * 60000), // 1 day ago
    lastMessageFromMe: false,
    unreadCount: 0
  },
  {
    id: 4,
    name: 'August MavisVista',
    avatarColor: '#EF4444',
    isOnline: true,
    lastMessage: 'Hallo, goedemorgen. Kampioen.',
    lastMessageTime: new Date(Date.now() - 24 * 60 * 60000), // 1 day ago
    lastMessageFromMe: false,
    unreadCount: 0
  },
  {
    id: 5,
    name: 'Aurore Manager 🌸',
    avatarColor: '#EC4899',
    isOnline: false,
    lastMessage: '😊',
    lastMessageTime: new Date(Date.now() - 2 * 24 * 60 * 60000), // 2 days ago
    lastMessageFromMe: false,
    unreadCount: 0
  },




]);

// Computed
const allConversations = computed(() => {
  return props.conversations.length > 0 ? props.conversations : defaultConversations.value;
});

const filteredConversations = computed(() => {
  if (!props.searchQuery) {
    return allConversations.value;
  }
  return allConversations.value.filter(conversation =>
    conversation.name.toLowerCase().includes(props.searchQuery.toLowerCase()) ||
    conversation.lastMessage.toLowerCase().includes(props.searchQuery.toLowerCase())
  );
});

// Méthodes
const getInitials = (name) => {
  return name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .substring(0, 2);
};

const formatTime = (timestamp) => {
  if (!timestamp) return '';
  const date = new Date(timestamp);
  const now = new Date();
  const diff = now - date;

  if (diff < 24 * 60 * 60 * 1000) {
    // Moins de 24h : afficher l'heure
    return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
  } else if (diff < 7 * 24 * 60 * 60 * 1000) {
    // Moins de 7 jours : afficher le jour
    const days = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
    return days[date.getDay()];
  } else {
    // Plus de 7 jours : afficher la date
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
  }
};

const selectConversation = (conversation) => {
  emit('selectConversation', conversation);
};

const toggleActionsMenu = () => {
  showActionsMenu.value = !showActionsMenu.value;
};

const handleAction = (action) => {
  if (action === 'newGroup') {
    emit('newGroup');
  } else if (action === 'newMessage') {
    emit('newMessage');
  }
  showActionsMenu.value = false;
};

// Fermer le menu si clic à l'extérieur
const handleClickOutside = (event) => {
  if (actionsDropdown.value && !actionsDropdown.value.contains(event.target) && !event.target.closest('button')) {
    showActionsMenu.value = false;
  }
};

// Lifecycle
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

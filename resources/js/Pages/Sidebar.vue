<template>
  <div class="flex-1 bg-gray-800 flex flex-col">
    <!-- Header simple avec titre -->
    <div class="p-3 border-b border-gray-700">
      <div class="flex items-center justify-between">
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
    </div>

    <!-- Liste des conversations -->
    <div class="flex-1 overflow-y-auto">
      <!-- Loading State -->
      <div v-if="props.isLoading" class="p-4 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
        <p class="text-gray-400 text-sm mt-2">Chargement...</p>
      </div>

      <!-- État vide -->
      <div v-else-if="filteredConversations.length === 0" class="p-4 text-center text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <p class="text-sm mb-2">Aucune conversation</p>
        <button
          @click="handleAction('newMessage')"
          class="text-blue-400 hover:text-blue-300 text-sm font-medium"
        >
          Créer votre première conversation
        </button>
      </div>

      <!-- Liste des conversations -->
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
          class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-sm relative"
          :style="{ backgroundColor: conversation.avatar_color || conversation.avatarColor || '#8B5CF6' }"
        >
          <!-- Icône de groupe si c'est un groupe -->
          <svg v-if="conversation.is_group" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
          </svg>
          <!-- Initiales pour les conversations privées -->
          <span v-else>{{ getInitials(conversation.name) }}</span>
        </div>

        <!-- Indicateur en ligne (seulement pour les conversations privées) -->
        <div
          v-if="!conversation.is_group && (conversation.is_online || conversation.isOnline)"
          class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-green-500 border-2 border-gray-800 rounded-full"
        ></div>

        <!-- Badge nombre de participants pour les groupes -->
        <div
          v-if="conversation.is_group && conversation.participants_count"
          class="absolute -bottom-0.5 -right-0.5 w-5 h-5 bg-blue-500 border-2 border-gray-800 rounded-full flex items-center justify-center"
        >
          <span class="text-xs font-bold text-white">{{ conversation.participants_count }}</span>
        </div>
      </div>

      <!-- Informations conversation -->
      <div class="flex-1 min-w-0">
        <!-- Ligne 1: Nom + Heure -->
        <div class="flex items-center justify-between mb-1">
          <span class="font-medium text-white truncate">{{ conversation.name }}</span>
          <span
            class="text-xs flex-shrink-0 ml-2"
            :class="(conversation.unread_count || conversation.unreadCount || 0) > 0 ? 'text-blue-400 font-medium' : 'text-gray-400'"
          >
            {{ formatTime(conversation.last_message_time || conversation.lastMessageTime) }}
          </span>
        </div>

        <!-- Ligne 2: Message + Badge -->
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-1 flex-1 min-w-0">
            <!-- Icône de statut du message -->
            <svg v-if="conversation.last_message_from_me || conversation.lastMessageFromMe" class="w-3 h-3 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>

            <span
              class="text-sm truncate"
              :class="(conversation.unread_count || conversation.unreadCount || 0) > 0 ? 'text-white font-medium' : 'text-gray-400'"
            >
              {{ conversation.last_message || conversation.lastMessage || 'Aucun message' }}
            </span>
          </div>

          <span
  class="text-xs flex-shrink-0 ml-2"
  :class="(conversation.unread_count || conversation.unreadCount || 0) > 0 ? 'text-blue-400 font-medium' : 'text-gray-400'"
>
  {{ formatTime(conversation.formatted_time) || conversation.last_message_time || conversation.update }}
</span>

          <!-- Badge messages non lus - aligné avec message -->
          <div
            v-if="(conversation.unread_count || conversation.unreadCount || 0) > 0"
            class="bg-blue-500 text-white text-xs font-semibold rounded-full min-w-[1.25rem] h-5 px-1.5 flex items-center justify-center flex-shrink-0 ml-2"
          >
            {{ (conversation.unread_count || conversation.unreadCount) > 99 ? '99+' : (conversation.unread_count || conversation.unreadCount) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
    </div>
  </div>
</template><script setup>
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
  },
  isLoading: {
    type: Boolean,
    default: false
  }
});

// Emits
const emit = defineEmits(['selectConversation', 'newGroup', 'newMessage']);

// État du composant
const showActionsMenu = ref(false);
const actionsDropdown = ref(null);

// Computed
const filteredConversations = computed(() => {
  if (!props.searchQuery) {
    return props.conversations;
  }
  return props.conversations.filter(conversation =>
    conversation.name.toLowerCase().includes(props.searchQuery.toLowerCase()) ||
    (conversation.last_message || conversation.lastMessage || '').toLowerCase().includes(props.searchQuery.toLowerCase())
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
  if (isNaN(date.getTime())) return ''; // vérifie si date valide

  const now = new Date();
  const diff = now - date;


  if (diff < 60000) return 'Now'; // moins d'1 minute

  if (diff < 3600000) {
    const minutes = Math.floor(diff / 60000);
    return `${minutes}m`; //moins d'1heure
  }

  // Aujourd'hui (moins de 24h)
  const isToday = date.toDateString() === now.toDateString();
  if (isToday) {
    return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
  }


  // Hier
  const yesterday = new Date(now);
  yesterday.setDate(yesterday.getDate() - 1);
  if (date.toDateString() === yesterday.toDateString()) {
    return 'Yesterday';
  }

  // Cette semaine (moins de 7 jours)
  if (diff < 604800000) {
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    return days[date.getDay()];
  }

  // Plus ancien - afficher la date
  const currentYear = now.getFullYear();
  const messageYear = date.getFullYear();

  if (currentYear === messageYear) {
    // Même année : afficher jour/mois (ex: "15 Oct")
    return date.toLocaleDateString('en-US', { day: 'numeric', month: 'short' });
  }

  return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: '2-digit' });

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

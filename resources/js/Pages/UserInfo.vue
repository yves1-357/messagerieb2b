<template>
  <div v-if="user" class="w-90 bg-gray-800 border-l border-gray-700 flex flex-col">
    <!-- Header avec bouton de fermeture -->
    <div class="p-4 border-b border-gray-700 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-white">
        {{ user.is_group ? 'Group Info' : 'User Info' }}
      </h2>
      <button
        @click="$emit('close')"
        class="p-1 hover:bg-gray-700 rounded-lg transition-colors"
      >
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Profil utilisateur -->
    <div class="p-6 text-center border-b border-gray-700">
      <!-- Avatar -->
      <div
        class="w-24 h-24 rounded-full mx-auto mb-4 flex items-center justify-center text-white font-bold text-2xl shadow-lg"
        :style="{ backgroundColor: user.avatarColor || '#8B5CF6' }"
      >
        <!-- Icône de groupe si c'est un groupe -->
        <svg v-if="user.is_group" class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
          <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
        </svg>
        <!-- Initiales pour les utilisateurs individuels -->
        <span v-else>{{ getInitials(user.name) }}</span>
      </div>

      <!-- Nom et statut -->
      <h3 class="text-xl font-semibold text-white mb-1">{{ user.name }}</h3>
      <p class="text-gray-400 text-sm mb-3">{{ user.username || '@username' }}</p>

      <!-- Statut en ligne (seulement pour les utilisateurs individuels) -->
      <div v-if="!user.is_group" class="flex items-center justify-center space-x-2 mb-4">
        <div
          class="w-2 h-2 rounded-full"
          :class="{
            'bg-green-500': userStatus.color === 'green',
            'bg-yellow-500': userStatus.color === 'yellow',
            'bg-gray-500': userStatus.color === 'gray'
          }"
        ></div>
        <span class="text-sm text-gray-400">{{ userStatus.text }}</span>
      </div>

      <!-- Actions rapides pour groupes -->
      <div v-if="user.is_group" class="bg-gray-700 rounded-lg p-3">
        <p class="text-sm text-gray-300 text-center">Informations du groupe</p>
      </div>

      <!-- Actions rapides pour utilisateurs individuels -->
      <div v-else-if="!isOwnProfile" class="flex space-x-3">
        <button
          @click="startConversation"
          class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
          <span>Message</span>
        </button>

        <button class="bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
          </svg>
        </button>
      </div>

      <!-- Message informatif pour son propre profil -->
      <div v-else class="bg-gray-700 rounded-lg p-3">
        <p class="text-sm text-gray-300 text-center">Profil</p>
      </div>
    </div>

    <!-- Participants du groupe (si c'est un groupe) -->
    <div v-if="user.is_group" class="p-4 border-b border-gray-700">
      <h4 class="text-sm font-medium text-gray-300 mb-3">
        Participants ({{ user.participants?.length || user.participants_count || 0 }})
      </h4>

      <div v-if="user.participants && user.participants.length > 0" class="space-y-3">
        <div
          v-for="participant in user.participants"
          :key="participant.id"
          class="flex items-center space-x-3 p-2 hover:bg-gray-700 rounded-lg transition-colors"
        >
          <div
            class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm"
            :style="{ backgroundColor: participant.avatar_color || '#8B5CF6' }"
          >
            {{ getInitials(participant.name) }}
          </div>

          <div class="flex-1">
            <div class="flex items-center space-x-2">
              <span class="font-medium text-white">{{ participant.name }}</span>
              <div
                v-if="participant.is_online"
                class="w-2 h-2 bg-green-500 rounded-full"
              ></div>
            </div>
            <p class="text-sm text-gray-400">{{ participant.email }}</p>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-4">
        <p class="text-gray-400 text-sm">Aucun participant trouvé</p>
      </div>
    </div>

    <!-- Informations détaillées (pour les utilisateurs individuels) -->
    <div v-else class="p-4 border-b border-gray-700">
      <h4 class="text-sm font-medium text-gray-300 mb-3">Informations</h4>

      <div class="space-y-3">
        <!-- Email -->
        <div class="flex items-center space-x-3">
          <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
          </svg>
          <div class="flex-1">
            <p class="text-xs text-gray-400">Email</p>
            <p class="text-white">{{ user.email || 'email@example.com' }}</p>
          </div>
        </div>

        <!-- Nom d'utilisateur -->
        <div class="flex items-center space-x-3">
          <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          <div class="flex-1">
            <p class="text-xs text-gray-400">Nom d'utilisateur</p>
            <p class="text-white">{{ user.username ? '@' + user.username : '@Loredana667' }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Notifications -->
    <div class="p-4 border-b border-gray-700">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 4h5v5H4V4z"/>
          </svg>
          <span class="text-white">Notifications</span>
        </div>

        <!-- Toggle switch pour notifications -->
        <button
          @click="toggleNotifications"
          class="relative"
        >
          <div class="w-10 h-6 bg-gray-600 rounded-full transition-colors" :class="{ 'bg-blue-600': notificationsEnabled }">
            <div class="w-5 h-5 bg-white rounded-full shadow-md transform transition-transform" :class="{ 'translate-x-4': notificationsEnabled }"></div>
          </div>
        </button>
      </div>
    </div>

    <!-- Onglets médias -->
    <div class="border-b border-gray-700">
      <div class="flex">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          class="flex-1 py-3 px-4 text-sm font-medium transition-colors"
          :class="activeTab === tab.id ? 'text-blue-400 border-b-2 border-blue-400' : 'text-gray-400 hover:text-white'"
        >
          {{ tab.name }}
        </button>
      </div>
    </div>

    <!-- Contenu des onglets -->
    <div class="flex-1 overflow-y-auto scrollbar-hide">
      <div class="p-4">
        <div v-if="activeTab === 'media'">
          <div class="text-center py-8 " >
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-400">Aucun média partagé</p>
          </div>
        </div>

        <div v-else-if="activeTab === 'files'">
          <div class="text-center py-8">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-400">Aucun fichier partagé</p>
          </div>
        </div>

        <div v-else-if="activeTab === 'links'">
          <div class="text-center py-8">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            <p class="text-gray-400">Aucun lien partagé</p>
          </div>
        </div>

        <div v-else-if="activeTab === 'music'">
          <div class="text-center py-8">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
            </svg>
            <p class="text-gray-400">Aucune musique partagée</p>
          </div>
        </div>

        <div v-else-if="activeTab === 'gif'">
          <div class="text-center py-8">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v18a1 1 0 01-1 1H3a1 1 0 01-1-1V1a1 1 0 011-1h2a1 1 0 011 1v3"/>
            </svg>
            <p class="text-gray-400">Aucun GIF partagé</p>
          </div>
        </div>

        <div v-else-if="activeTab === 'voice'">
          <div class="text-center py-8">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
            </svg>
            <p class="text-gray-400">Aucun message vocal</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Props
const props = defineProps({
  user: {
    type: Object,
    default: null
  }
});

// Emits
const emit = defineEmits(['close', 'start-conversation']);

// État du composant
const notificationsEnabled = ref(true);
const activeTab = ref('media');

// Méthodes
const startConversation = () => {
  if (props.user) {
    emit('start-conversation', props.user);
  }
};

// Vérifier si c'est le propre profil de l'utilisateur
const isOwnProfile = computed(() => {
  const { props: pageProps } = usePage();
  const currentUser = pageProps.auth?.user;
  return currentUser && props.user && currentUser.id === props.user.id;
});

// Computed pour le statut utilisateur
const userStatus = computed(() => {
  if (!props.user) return { status: 'offline', text: 'hors ligne', color: 'gray' };

  if (props.user.status === 'online') {
    return { status: 'online', text: 'en ligne', color: 'green' };
  }

  if (!props.user.last_seen_at) {
    return { status: 'offline', text: 'hors ligne', color: 'gray' };
  }

  const lastSeen = new Date(props.user.last_seen_at);
  const now = new Date();
  const diffInMinutes = Math.floor((now - lastSeen) / (1000 * 60));

  if (diffInMinutes < 1) {
    return { status: 'recently', text: 'à l\'instant', color: 'yellow' };
  } else if (diffInMinutes < 60) {
    return { status: 'recently', text: `il y a ${diffInMinutes} min`, color: 'yellow' };
  } else if (diffInMinutes < 1440) { // 24 hours
    const hours = Math.floor(diffInMinutes / 60);
    return { status: 'offline', text: `il y a ${hours}h`, color: 'gray' };
  } else {
    const days = Math.floor(diffInMinutes / 1440);
    return { status: 'offline', text: `il y a ${days}j`, color: 'gray' };
  }
});

// Onglets
const tabs = ref([
  { id: 'media', name: 'Media' },
  { id: 'files', name: 'Files' },
  { id: 'links', name: 'Links' },
  { id: 'music', name: 'Music' },
  { id: 'gif', name: 'GIF' },
  { id: 'voice', name: 'Voice' }
]);

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

const getLastSeenText = (lastSeen) => {
  if (!lastSeen) return 'vu pour la dernière fois il y a longtemps';

  const now = new Date();
  const lastSeenDate = new Date(lastSeen);
  const diff = now - lastSeenDate;

  const minutes = Math.floor(diff / (1000 * 60));
  const hours = Math.floor(diff / (1000 * 60 * 60));
  const days = Math.floor(diff / (1000 * 60 * 60 * 24));

  if (minutes < 1) return 'vu à l\'instant';
  if (minutes < 60) return `vu il y a ${minutes} minute${minutes > 1 ? 's' : ''}`;
  if (hours < 24) return `vu il y a ${hours} heure${hours > 1 ? 's' : ''}`;
  if (days < 7) return `vu il y a ${days} jour${days > 1 ? 's' : ''}`;

  return lastSeenDate.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

const toggleNotifications = () => {
  notificationsEnabled.value = !notificationsEnabled.value;
  console.log('Notifications:', notificationsEnabled.value);
};
</script>
<style scoped>
/* Masquer la scrollbar pour un look professionnel comme Telegram */
.scrollbar-hide {
  -ms-overflow-style: none;  /* Internet Explorer 10+ */
  scrollbar-width: none;  /* Firefox */
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;  /* Safari and Chrome */
}
</style>

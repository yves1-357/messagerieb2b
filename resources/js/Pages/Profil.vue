<template>
  <div class="bg-gray-800 border-b border-gray-700">
    <div class="p-4">
      <!-- Header avec menu hamburger et recherche -->
      <div class="flex items-center justify-between mb-4">
        <!-- Menu hamburger -->
        <div class="relative">
          <button
            @click="toggleMenu"
            class="p-2 hover:bg-gray-700 rounded-lg transition-colors"
          >
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>

          <!-- Menu déroulant -->
          <div
            v-if="showMenu"
            ref="menuDropdown"
            class="absolute top-full left-0 mt-2 w-56 bg-gray-700 rounded-lg shadow-lg z-50 py-2"
          >
            <!-- Nom de la personne connectée -->
            <div class="px-4 py-3 border-b border-gray-600">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                  {{ userInitials }}
                </div>
                <div>
                  <p class="text-white font-medium">{{ currentUser?.name || 'Utilisateur' }}</p>
                  <p class="text-gray-400 text-sm">{{ currentUser?.email || 'email@example.com' }}</p>
                </div>
              </div>
            </div>

            <!-- Options du menu -->
            <div class="py-1">
              <button
                @click="handleMenuAction('addAccount')"
                class="w-full text-left px-4 py-2 text-white hover:bg-gray-600 transition-colors flex items-center space-x-3"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Add Account</span>
              </button>

              <button
                @click="handleMenuAction('myProfile')"
                class="w-full text-left px-4 py-2 text-white hover:bg-gray-600 transition-colors flex items-center space-x-3"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>My Profile</span>
              </button>

              <button
                @click="handleMenuAction('contacts')"
                class="w-full text-left px-4 py-2 text-white hover:bg-gray-600 transition-colors flex items-center space-x-3"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Contacts</span>
              </button>

              <button
                @click="handleMenuAction('settings')"
                class="w-full text-left px-4 py-2 text-white hover:bg-gray-600 transition-colors flex items-center space-x-3"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Settings</span>
              </button>

              <div class="border-t border-gray-600 my-1"></div>

              <button
                @click="toggleNightMode"
                class="w-full text-left px-4 py-2 text-white hover:bg-gray-600 transition-colors flex items-center justify-between"
              >
                <div class="flex items-center space-x-3">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                  </svg>
                  <span>Night Mode</span>
                </div>
                <!-- Toggle switch -->
                <div class="relative">
                  <div class="w-10 h-6 bg-gray-600 rounded-full transition-colors" :class="{ 'bg-blue-600': nightMode }">
                    <div class="w-5 h-5 bg-white rounded-full shadow-md transform transition-transform" :class="{ 'translate-x-4': nightMode }"></div>
                  </div>
                </div>
              </button>
            </div>
          </div>
        </div>

        <!-- Titre -->
        <h1 class="text-xl font-semibold text-white">QuickChat</h1>

        <!-- Icône de notification avec badge dynamique -->
        <button class="p-2 hover:bg-gray-700 rounded-lg transition-colors relative">
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 4h5v5H4V4z"/>
          </svg>
          <!-- Badge de notification dynamique -->
          <span
            v-if="totalUnreadCount > 0"
            class="absolute -top-1 -right-1 min-w-[1rem] h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center px-1"
          >
            {{ totalUnreadCount > 99 ? '99+' : totalUnreadCount }}
          </span>
        </button>
      </div>

      <!-- Barre de recherche -->
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Rechercher..."
          class="w-full bg-gray-700 text-white placeholder-gray-400 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
        >
        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Props
const props = defineProps({
  conversations: {
    type: Array,
    default: () => []
  }
});



const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

// État du composant
const showMenu = ref(false);
const nightMode = ref(true); // Mode nuit activé par défaut
const searchQuery = ref('');
const menuDropdown = ref(null);

// Computed
const userInitials = computed(() => {
  if (!currentUser.value?.name) return 'U';
  return currentUser.value.name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .substring(0, 2);
});

// Computed pour calculer le total des messages non lus
const totalUnreadCount = computed(() => {
  if (!props.conversations || props.conversations.length === 0) {
    // Données par défaut si aucune conversation n'est passée
    return 1; // Carmen Goina a 1 message non lu dans les données par défaut
  }

  return props.conversations.reduce((total, conversation) => {
    return total + (conversation.unreadCount || 0);
  }, 0);
});

// Méthodes
const toggleMenu = () => {
  showMenu.value = !showMenu.value;
};

const handleMenuAction = (action) => {
  console.log(`Action: ${action}`);
  // TODO: Implémenter les actions du menu
  showMenu.value = false;
};

const toggleNightMode = () => {
  nightMode.value = !nightMode.value;
  // TODO: Implémenter le basculement du mode nuit
  console.log('Night mode:', nightMode.value);
};

// Fermer le menu si clic à l'extérieur
const handleClickOutside = (event) => {
  if (menuDropdown.value && !menuDropdown.value.contains(event.target) && !event.target.closest('button')) {
    showMenu.value = false;
  }
};

// Events
defineEmits(['search']);



// Lifecycle
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

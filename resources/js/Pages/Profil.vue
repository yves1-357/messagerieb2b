<template>
  <div class="bg-gray-800 border-b border-gray-700">
    <div class="p-4 search-container">
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
              <button
                @click="handleMenuAction('myProfile')"
                class="flex items-center space-x-3 w-full text-left hover:bg-gray-600 rounded-lg p-2 transition-colors"
              >
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                  {{ userInitials }}
                </div>
                <div>
                  <p class="text-white font-medium">{{ currentUser?.name || 'Utilisateur' }}</p>
                  <p class="text-gray-400 text-sm">{{ currentUser?.email || 'email@example.com' }}</p>
                </div>
              </button>
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
      <div class="relative mb-3">
        <input
          v-model="searchQuery"
          @input="emitSearch"
          @focus="showSearchTabs = true"
          @click="showSearchTabs = true"
          type="text"
          placeholder="Rechercher..."
          class="w-full bg-gray-700 text-white placeholder-gray-400 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
        >
        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>

      <!-- Onglets Chats/Users (affiché dès qu'on interagit avec la recherche) -->
      <div v-if="showSearchTabs" class="mb-3">
        <!-- Header avec bouton fermer -->
        <div class="flex items-center justify-end mb-2">
          <button
            @click="closeSearchTabs"
            class="p-1 text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Onglets -->
        <div class="flex bg-gray-700 rounded-lg p-1">
          <button
            @click="activeSearchTab = 'chats'"
            class="flex-1 py-2 px-3 text-sm font-medium rounded-md transition-colors"
            :class="activeSearchTab === 'chats'
              ? 'bg-blue-600 text-white'
              : 'text-gray-300 hover:text-white hover:bg-gray-600'"
          >
            Chats
            <span v-if="filteredConversations.length > 0" class="ml-1 bg-gray-500 text-xs px-1.5 py-0.5 rounded-full">
              {{ filteredConversations.length }}
            </span>
          </button>
          <button
            @click="activeSearchTab = 'users'; loadAllUsers()"
            class="flex-1 py-2 px-3 text-sm font-medium rounded-md transition-colors"
            :class="activeSearchTab === 'users'
              ? 'bg-blue-600 text-white'
              : 'text-gray-300 hover:text-white hover:bg-gray-600'"
          >
            Users
            <span v-if="availableUsers.length > 0" class="ml-1 bg-gray-500 text-xs px-1.5 py-0.5 rounded-full">
              {{ availableUsers.length }}
            </span>
          </button>
        </div>
      </div>      <!-- Contenu des onglets (affiché dès qu'on interagit avec la recherche) -->
      <div v-if="showSearchTabs" class="space-y-2 max-h-[60vh] overflow-y-auto scrollbar-hide">
        <!-- Onglet Chats -->
        <div v-if="activeSearchTab === 'chats'">
          <div v-if="filteredConversations.length === 0" class="text-center py-4 text-gray-400">
            <p class="text-sm">Aucune conversation trouvée</p>
          </div>
          <div v-else class="space-y-1">
            <div
              v-for="conversation in filteredConversations"
              :key="conversation.id"
              class="flex items-center space-x-3 p-2 hover:bg-gray-700 rounded-lg cursor-pointer transition-colors"
            >
              <div class="relative">
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm"
                  :style="{ backgroundColor: conversation.avatar_color || conversation.avatarColor || '#8B5CF6' }"
                >
                  {{ getInitials(conversation.name) }}
                </div>
                <div
                  v-if="conversation.is_online || conversation.isOnline"
                  class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-gray-800 rounded-full"
                ></div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-white font-medium truncate">{{ conversation.name }}</p>
                <p class="text-gray-400 text-sm truncate">
                  {{ conversation.last_message || conversation.lastMessage || 'Aucun message' }}
                </p>
              </div>
              <div v-if="(conversation.unread_count || conversation.unreadCount || 0) > 0" class="bg-blue-500 text-white text-xs rounded-full px-2 py-0.5">
                {{ (conversation.unread_count || conversation.unreadCount) > 99 ? '99+' : (conversation.unread_count || conversation.unreadCount) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Onglet Users -->
        <div v-else-if="activeSearchTab === 'users'">
          <!-- Loading -->
          <div v-if="isLoadingUsers" class="text-center py-4">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto"></div>
            <p class="text-gray-400 text-sm mt-2">Chargement des utilisateurs...</p>
          </div>

          <!-- Aucun utilisateur disponible -->
          <div v-else-if="availableUsers.length === 0" class="text-center py-4 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            <p class="text-sm">Aucun utilisateur disponible</p>
            <p class="text-xs text-gray-500 mt-1">Tous les utilisateurs sont déjà dans vos conversations</p>
          </div>

          <!-- Liste des utilisateurs -->
          <div v-else class="space-y-1">
            <div
              v-for="user in filteredUsers"
              :key="user.id"
              @click="showUserInfo(user)"
              class="flex items-center space-x-3 p-2 hover:bg-gray-700 rounded-lg cursor-pointer transition-colors"
            >
              <div class="relative">
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm"
                  :style="{ backgroundColor: user.avatar_color || '#8B5CF6' }"
                >
                  {{ getInitials(user.name) }}
                </div>
                <div
                  class="absolute -bottom-0.5 -right-0.5 w-3 h-3 border-2 border-gray-800 rounded-full"
                  :class="user.is_online ? 'bg-green-500' : 'bg-gray-500'"
                ></div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-white font-medium truncate">{{ user.name }}</p>
                <p class="text-gray-400 text-sm truncate">
                  {{ user.username ? '@' + user.username : user.email }}
                </p>
              </div>
            </div>

            <!-- Bouton Load More -->
            <div v-if="hasMoreUsers" class="pt-2">
              <button
                @click="loadMoreUsers"
                :disabled="isLoadingUsers"
                class="w-full bg-gray-700 hover:bg-gray-600 disabled:bg-gray-700 disabled:opacity-50 text-white py-2 px-4 rounded-lg transition-colors text-sm"
              >
                {{ isLoadingUsers ? 'Chargement...' : 'Voir plus d\'utilisateurs' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Contacts -->
    <div v-if="showContactsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="w-96 bg-gray-800 rounded-lg shadow-xl max-h-[90vh] max-w-full mx-4 flex flex-col">
        <!-- Header -->
        <div class="p-4 border-b border-gray-700 flex items-center justify-between">
          <h2 class="text-lg font-semibold text-white">Contacts</h2>
          <button
            @click="closeContactsModal"
            class="p-1 hover:bg-gray-700 rounded-lg transition-colors"
          >
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Barre de recherche -->
        <div class="p-4 border-b border-gray-700">
          <div class="relative">
            <input
              v-model="contactsSearchQuery"
              @input="searchUsers"
              type="text"
              placeholder="Search users by username..."
              class="w-full bg-gray-700 text-white placeholder-gray-400 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
        </div>

        <!-- Contenu scrollable -->
        <div class="flex-1 overflow-y-auto">
          <!-- Résultats de recherche -->
          <div v-if="contactsSearchQuery.trim()" class="p-4">
            <h3 class="text-sm font-medium text-gray-300 mb-3">Search Results</h3>

            <div v-if="isSearchingUsers" class="text-center py-4">
              <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto"></div>
              <p class="text-gray-400 text-sm mt-2">Searching...</p>
            </div>

            <div v-else-if="foundUsers.length === 0" class="text-center py-4">
              <p class="text-gray-400 text-sm">No users found</p>
            </div>

            <div v-else class="space-y-2">
              <div
                v-for="user in foundUsers"
                :key="user.id"
                class="flex items-center space-x-3 p-2 hover:bg-gray-700 rounded-lg cursor-pointer transition-colors"
              >
                <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                  {{ getInitials(user.name) }}
                </div>
                <div class="flex-1">
                  <p class="text-white font-medium">{{ user.name }}</p>
                  <p class="text-gray-400 text-sm">{{ user.username ? '@' + user.username : '@username' }}</p>
                </div>
                <button
                  @click="startConversation(user)"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors"
                >
                  Message
                </button>
              </div>
            </div>
          </div>

          <!-- Contacts existants -->
          <div v-else class="p-4">
            <h3 class="text-sm font-medium text-gray-300 mb-3">Your Contacts</h3>

            <div v-if="existingContacts.length === 0" class="text-center py-8">
              <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              <p class="text-gray-400 text-sm">No contacts yet</p>
              <p class="text-gray-500 text-xs mt-1">Search for users to start conversations</p>
            </div>

            <div v-else class="space-y-2">
              <div
                v-for="contact in existingContacts"
                :key="contact.id"
                class="flex items-center space-x-3 p-2 hover:bg-gray-700 rounded-lg cursor-pointer transition-colors"
              >
                <div class="relative">
                  <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    {{ getInitials(contact.name) }}
                  </div>
                  <div v-if="contact.is_online" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-gray-800 rounded-full"></div>
                </div>
                <div class="flex-1">
                  <p class="text-white font-medium">{{ contact.name }}</p>
                  <p class="text-gray-400 text-sm">{{ contact.username ? '@' + contact.username : '@username' }}</p>
                </div>
                <div class="text-gray-400 text-xs">
                  {{ contact.is_online ? 'online' : 'offline' }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Settings -->
    <div v-if="showSettingsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-gray-800 rounded-lg shadow-xl w-96 max-w-full mx-4">
        <!-- Header du modal -->
        <div class="flex items-center justify-between p-4 border-b border-gray-700">
          <h3 class="text-lg font-semibold text-white">Settings</h3>
          <button
            @click="closeSettingsModal"
            class="text-gray-400 hover:text-white p-1"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Corps du modal -->
        <div class="p-4 space-y-4">
          <!-- Section Username -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Username</label>
            <div class="flex space-x-2">
              <input
                v-model="newUsername"
                type="text"
                placeholder="Votre username"
                class="flex-1 bg-gray-700 text-white placeholder-gray-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border border-red-500': usernameError }"
              >
              <button
                @click="updateUsername"
                :disabled="isUpdatingUsername || !newUsername.trim()"
                class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg transition-colors"
              >
                {{ isUpdatingUsername ? 'Saving...' : 'Save' }}
              </button>
            </div>
            <p v-if="usernameError" class="text-red-400 text-sm mt-1">{{ usernameError }}</p>
            <p v-if="usernameSuccess" class="text-green-400 text-sm mt-1">{{ usernameSuccess }}</p>
          </div>

          <!-- Section Logout -->
          <div class="border-t border-gray-700 pt-4">
            <button
              @click="confirmLogout"
              class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              <span>Logout</span>
            </button>
          </div>

          <!-- Section Delete Account -->
          <div class="border-t border-gray-700 pt-4">
            <button
              @click="confirmDeleteAccount"
              class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
              <span>Delete Account</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Confirmation Logout -->
    <div v-if="showLogoutConfirm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-70">
      <div class="bg-gray-800 rounded-lg shadow-xl w-80 max-w-full mx-4">
        <div class="p-4">
          <h3 class="text-lg font-semibold text-white mb-4">Confirm Logout</h3>
          <p class="text-gray-300 mb-6">Are you sure you want to logout?</p>
          <div class="flex space-x-3">
            <button
              @click="cancelLogout"
              class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg transition-colors"
            >
              Cancel
            </button>
            <button
              @click="performLogout"
              :disabled="isLoggingOut"
              class="flex-1 bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-600 text-white py-2 px-4 rounded-lg transition-colors"
            >
              {{ isLoggingOut ? 'Logging out...' : 'OK' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Confirmation Delete Account -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-70">
      <div class="bg-gray-800 rounded-lg shadow-xl w-80 max-w-full mx-4">
        <div class="p-4">
          <h3 class="text-lg font-semibold text-white mb-4">Delete Account</h3>
          <p class="text-gray-300 mb-4">This action cannot be undone. Please enter your password to confirm:</p>
          <input
            v-model="deletePassword"
            type="password"
            placeholder="Your password"
            class="w-full bg-gray-700 text-white placeholder-gray-400 rounded-lg px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-red-500"
            :class="{ 'border border-red-500': deletePasswordError }"
          >
          <p v-if="deletePasswordError" class="text-red-400 text-sm mb-4">{{ deletePasswordError }}</p>
          <div class="flex space-x-3">
            <button
              @click="cancelDeleteAccount"
              class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg transition-colors"
            >
              Cancel
            </button>
            <button
              @click="performDeleteAccount"
              :disabled="isDeletingAccount || !deletePassword.trim()"
              class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-gray-600 text-white py-2 px-4 rounded-lg transition-colors"
            >
              {{ isDeletingAccount ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast de succès -->
    <div v-if="showSuccessToast" class="fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-80 transform transition-all duration-300">
      <div class="flex items-center space-x-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ successMessage }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

onMounted(() => {
    initializeTheme();
})

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
const nightMode = ref(false);
const searchQuery = ref('');
const activeSearchTab = ref('chats'); // Pour les onglets Chats/Users dans la recherche
const showSearchTabs = ref(false); // Pour afficher les onglets dès qu'on interagit avec la recherche
const menuDropdown = ref(null);

// Variables pour la gestion des utilisateurs disponibles
const availableUsers = ref([]);
const isLoadingUsers = ref(false);
const usersPage = ref(1);
const hasMoreUsers = ref(true);

// État pour les modals Settings
const showSettingsModal = ref(false);
const showLogoutConfirm = ref(false);
const showDeleteConfirm = ref(false);

// État pour le modal Contacts
const showContactsModal = ref(false);
const contactsSearchQuery = ref('');
const foundUsers = ref([]);
const isSearchingUsers = ref(false);

// État pour le username
const newUsername = ref('');
const usernameError = ref('');
const usernameSuccess = ref('');
const isUpdatingUsername = ref(false);

// État pour logout
const isLoggingOut = ref(false);

// État pour delete account
const deletePassword = ref('');
const deletePasswordError = ref('');
const isDeletingAccount = ref(false);

// État pour le toast de succès
const showSuccessToast = ref(false);
const successMessage = ref('');

// Computed
const userInitials = computed(() => {
  if (!currentUser.value?.name) return 'U';
  return getInitials(currentUser.value.name);
});

// Méthode pour obtenir les initiales d'un nom
const getInitials = (name) => {
  if (!name) return 'U';
  return name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .substring(0, 2);
};

// Computed pour calculer le total des messages non lus
const totalUnreadCount = computed(() => {
  if (!props.conversations || props.conversations.length === 0) {
    // Données par défaut si aucune conversation n'est passée
    return 1; // EX: Carmen Goina a 1 message non lu dans les données par défaut
  }

  return props.conversations.reduce((total, conversation) => {
    return total + (conversation.unreadCount || 0);
  }, 0);
});

// Computed pour filtrer les conversations selon la recherche
const filteredConversations = computed(() => {
  if (!searchQuery.value.trim()) {
    return props.conversations;
  }
  return props.conversations.filter(conversation =>
    conversation.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (conversation.last_message || conversation.lastMessage || '').toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

// Computed pour filtrer les utilisateurs selon la recherche et l'onglet actif
const filteredUsers = computed(() => {
  // Si on est dans l'onglet Users, on montre TOUS les utilisateurs chargés
  if (activeSearchTab.value === 'users') {
    return availableUsers.value;
  }

  // Sinon, filtrage normal par recherche
  if (!searchQuery.value.trim()) {
    return availableUsers.value;
  }
  return availableUsers.value.filter(user =>
    user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    user.username?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

// Méthodes
const toggleMenu = () => {
  showMenu.value = !showMenu.value;
};

const handleMenuAction = (action) => {
  if (action === 'settings') {
    openSettingsModal();
  } else if (action === 'myProfile') {
    // Émettre un événement pour afficher le UserInfo dans Chat.vue avec l'utilisateur actuel
    emit('show-user-info', { user: currentUser.value });
  } else if (action === 'contacts') {
    openContactsModal();
  } else {
    console.log(`Action: ${action}`);
    // à faire : Implémenter les autres actions du menu
  }
  showMenu.value = false;
};

// Méthodes pour la gestion des onglets de recherche
const closeSearchTabs = () => {
  showSearchTabs.value = false;
  searchQuery.value = ''; // Réinitialiser la recherche
  activeSearchTab.value = 'chats'; // Revenir à l'onglet Chats par défaut
};

// Fermer les onglets si clic à l'extérieur
const handleClickOutsideSearch = (event) => {
  // Vérifier si le clic est en dehors de la zone de recherche et des onglets
  const searchContainer = event.target.closest('.search-container');
  if (!searchContainer && showSearchTabs.value) {
    closeSearchTabs();
  }
};

// Méthodes pour le modal Settings
const openSettingsModal = () => {
  showSettingsModal.value = true;
  newUsername.value = currentUser.value?.username || '';
  // Reset des erreurs
  usernameError.value = '';
  usernameSuccess.value = '';
};

const closeSettingsModal = () => {
  showSettingsModal.value = false;
  newUsername.value = '';
  usernameError.value = '';
  usernameSuccess.value = '';
};

// Méthodes pour le modal UserInfo
// Méthode pour formatter la date d'inscription
const formatJoinDate = (dateString) => {
  if (!dateString) return 'Date inconnue';
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long'
  });
};

// Méthodes pour le modal Contacts
const openContactsModal = () => {
  showContactsModal.value = true;
  contactsSearchQuery.value = '';
  foundUsers.value = [];
};

const closeContactsModal = () => {
  showContactsModal.value = false;
  contactsSearchQuery.value = '';
  foundUsers.value = [];
};

// Méthode pour rechercher des utilisateurs
const searchUsers = async () => {
  if (!contactsSearchQuery.value.trim()) {
    foundUsers.value = [];
    return;
  }

  isSearchingUsers.value = true;

  try {
    const response = await axios.get('/api/users/search', {
      params: { username: contactsSearchQuery.value.trim() }
    });
    foundUsers.value = response.data;
  } catch (error) {
    console.error('Erreur lors de la recherche:', error);
    foundUsers.value = [];
  } finally {
    isSearchingUsers.value = false;
  }
};

// Fonction pour démarrer une conversation avec un utilisateur
const startConversation = (user) => {
  // Fermer le modal des contacts
  closeContactsModal();

  // Émettre l'événement pour démarrer la conversation
  emit('start-conversation', user);
};

// Obtenir les contacts depuis les conversations
const existingContacts = computed(() => {
  if (!props.conversations || props.conversations.length === 0) return [];

  // Extraire les utilisateurs uniques des conversations
  const contacts = [];
  props.conversations.forEach(conversation => {
    if (conversation.users) {
      conversation.users.forEach(user => {
        if (user.id !== currentUser.value?.id && !contacts.find(c => c.id === user.id)) {
          contacts.push(user);
        }
      });
    }
  });

  return contacts;
});

// Méthodes pour username
const updateUsername = async () => {
  if (!newUsername.value.trim()) return;

  isUpdatingUsername.value = true;
  usernameError.value = '';
  usernameSuccess.value = '';

  try {
    const response = await axios.patch('/api/user/username', {
      username: newUsername.value.trim()
    });

    // Émettre l'événement de mise à jour utilisateur
    emit('user-updated', response.data.user);

    // Fermer le modal Settings
    closeSettingsModal();

    // Afficher le toast de succès
    showSuccessMessage('Username updated successfully!');

  } catch (error) {
    if (error.response?.data?.errors?.username) {
      usernameError.value = error.response.data.errors.username[0];
    } else if (error.response?.data?.message) {
      usernameError.value = error.response.data.message;
    } else {
      usernameError.value = 'Une erreur est survenue.';
    }
  } finally {
    isUpdatingUsername.value = false;
  }
};

// Méthode pour afficher le toast de succès
const showSuccessMessage = (message) => {
  successMessage.value = message;
  showSuccessToast.value = true;

  // Masquer le pop-up après 2 secondes
  setTimeout(() => {
    showSuccessToast.value = false;
  }, 2000);
};

// Méthodes pour logout
const confirmLogout = () => {
  // Fermer le modal Settings et ouvrir le modal de confirmation
  showSettingsModal.value = false;
  showLogoutConfirm.value = true;
};

const cancelLogout = () => {
  // Retourner au modal Settings
  showLogoutConfirm.value = false;
  showSettingsModal.value = true;
};

const performLogout = async () => {
  isLoggingOut.value = true;

  try {
    //deconnecter pusher
    if(window.Echo){
        window.Echo.disconnect();
    }

    //Api logout
    const response = await axios.post('/api/auth/logout', {}, {
        timeout: 5000, // Timeout de 5 secondes
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    console.log('Log out reussie:', response.data);

    // Rediriger vers la page d'accueil
    setTimeout(() => {
        window.location.href = '/';
    }, 100);

  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error);
    // Même en cas d'erreur, rediriger (peut-être session expirée)
    window.location.href = '/';
  } finally {
    isLoggingOut.value = false;
  }
};

// Méthodes pour delete account
const confirmDeleteAccount = () => {
  // Fermer le modal Settings et ouvrir le modal de confirmation
  showSettingsModal.value = false;
  showDeleteConfirm.value = true;
  deletePassword.value = '';
  deletePasswordError.value = '';
};

const cancelDeleteAccount = () => {
  // Retourner au modal Settings
  showDeleteConfirm.value = false;
  showSettingsModal.value = true;
  deletePassword.value = '';
  deletePasswordError.value = '';
};

const performDeleteAccount = async () => {
  if (!deletePassword.value.trim()) return;

  isDeletingAccount.value = true;
  deletePasswordError.value = '';

  try {
    await axios.delete('/api/user/account', {
      data: { password: deletePassword.value }
    });

    // Rediriger vers la page d'accueil après suppression
    window.location.href = '/';
  } catch (error) {
    if (error.response?.data?.message) {
      deletePasswordError.value = error.response.data.message;
    } else {
      deletePasswordError.value = 'Une erreur est survenue.';
    }
  } finally {
    isDeletingAccount.value = false;
  }
};

//initialiser depuis localstorage
const initializeTheme = () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark'){
        nightMode.value = true;
        document.documentElement.classList.add('dark');
    } else {
        nightMode.value = false;
        document.documentElement.classList.remove('dark');
    }
}

const toggleNightMode = () => {
  nightMode.value = !nightMode.value;

  //appliquer theme
  if (nightMode.value) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }

  // Émettre l'événement pour changer le thème global
  emit('theme-changed', nightMode.value ? 'dark' : 'light');

  // Optionnel : Sauvegarder dans localStorage
  localStorage.setItem('theme', nightMode.value ? 'dark' : 'light');

  console.log('theme changé:', nightMode.value);
};

// Fermer le menu si clic à l'extérieur
const handleClickOutside = (event) => {
  if (menuDropdown.value && !menuDropdown.value.contains(event.target) && !event.target.closest('button')) {
    showMenu.value = false;
  }
};

// ========================
// GESTION DES UTILISATEURS DISPONIBLES (pour les onglets)
// ========================

// Charger TOUS les utilisateurs disponibles (sans filtre de recherche)
const loadAllUsers = async (page = 1) => {
  try {
    isLoadingUsers.value = true;
    const response = await axios.get('/api/users/available', {
      params: {
        page
        // Pas de search : on charge TOUS les utilisateurs
      }
    });

    const result = response.data;
    const newUsers = result.users || [];

    if (page === 1) {
      // Première page : remplacer
      availableUsers.value = newUsers;
    } else {
      // Pages suivantes : ajouter
      availableUsers.value = [...availableUsers.value, ...newUsers];
    }

    // Gérer la pagination
    if (result.pagination) {
      hasMoreUsers.value = result.pagination.has_next_page;
      usersPage.value = page;
    }

  } catch (error) {
    console.error('Erreur lors du chargement des utilisateurs:', error);
    if (page === 1) availableUsers.value = [];
  } finally {
    isLoadingUsers.value = false;
  }
};

// Charger les utilisateurs disponibles pour les onglets (avec recherche)
const loadAvailableUsers = async (page = 1) => {
  try {
    isLoadingUsers.value = true;
    const response = await axios.get('/api/users/available', {
      params: {
        page,
        search: searchQuery.value
      }
    });

    const result = response.data;
    const newUsers = result.users || [];

    if (page === 1) {
      // Première page : remplacer
      availableUsers.value = newUsers;
    } else {
      // Pages suivantes : ajouter
      availableUsers.value = [...availableUsers.value, ...newUsers];
    }

    // Gérer la pagination
    if (result.pagination) {
      hasMoreUsers.value = result.pagination.has_next_page;
      usersPage.value = page;
    }

  } catch (error) {
    console.error('Erreur lors du chargement des utilisateurs:', error);
    if (page === 1) availableUsers.value = [];
  } finally {
    isLoadingUsers.value = false;
  }
};

// Charger plus d'utilisateurs (pagination)
const loadMoreUsers = async () => {
  if (hasMoreUsers.value && !isLoadingUsers.value) {
    await loadAllUsers(usersPage.value + 1);
  }
};

// Démarrer une conversation avec un utilisateur depuis les onglets
const startConversationWithUser = async (user) => {
  emit('start-conversation', user);
  // Fermer les onglets et revenir au menu principal après avoir créé la conversation
  closeSearchTabs();
};

// Afficher les informations d'un utilisateur
const showUserInfo = (user) => {
  emit('show-user-info', { user });
};

// Events
const emit = defineEmits(['search', 'user-updated', 'theme-changed', 'show-user-info', 'start-conversation', 'load-more-users']);

// Watch pour émettre les changements de recherche
const emitSearch = () => {
  emit('search', searchQuery.value);

  // Si on recherche et qu'on est dans l'onglet Chats, filtrer les conversations
  // L'onglet Users affiche toujours tous les utilisateurs chargés
};

// Lifecycle
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  document.addEventListener('click', handleClickOutsideSearch);

  // Initialiser le thème depuis localStorage
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme) {
    nightMode.value = savedTheme === 'dark';
  }
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  document.removeEventListener('click', handleClickOutsideSearch);
});
</script>

<style scoped>
/* Masquer la scrollbar pour un look professionnel */
.scrollbar-hide {
  -ms-overflow-style: none;  /* Internet Explorer 10+ */
  scrollbar-width: none;  /* Firefox */
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;  /* Safari et Chrome */
}
</style>

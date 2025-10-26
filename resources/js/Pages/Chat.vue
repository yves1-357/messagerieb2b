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
                class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm relative"
                :style="{ backgroundColor: selectedConversation.avatar_color || selectedConversation.avatarColor || '#8B5CF6' }"
              >
                <!-- Icône de groupe si c'est un groupe -->
                <svg v-if="selectedConversation.is_group" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                </svg>
                <!-- Initiales pour les conversations privées -->
                <span v-else>{{ getInitials(selectedConversation.name) }}</span>

                <!-- Indicateur de statut en ligne (seulement pour les conversations privées) -->
                <div
                  v-if="!selectedConversation.is_group"
                  class="absolute -bottom-0.5 -right-0.5 w-3 h-3 border-2 border-gray-800 rounded-full"
                  :class="{
                    'bg-green-500': conversationUserStatus.color === 'green',
                    'bg-yellow-500': conversationUserStatus.color === 'yellow',
                    'bg-gray-500': conversationUserStatus.color === 'gray'
                  }"
                ></div>

                <!-- Badge nombre de participants pour les groupes -->
                <div
                  v-if="selectedConversation.is_group && selectedConversation.participants_count"
                  class="absolute -bottom-0.5 -right-0.5 w-5 h-5 bg-blue-500 border-2 border-gray-800 rounded-full flex items-center justify-center"
                >
                  <span class="text-xs font-bold text-white">{{ selectedConversation.participants_count }}</span>
                </div>
              </div>
              <div>
                <h2 class="font-semibold text-white">{{ selectedConversation.name }}</h2>
                <p class="text-sm text-gray-400">
                  {{ conversationUserStatus.text }}
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

                <!-- Mes messages (à droite, bleu) -->
                <div v-if="message.isOwn" class="flex items-end justify-end space-x-2 max-w-md ml-auto">
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

                <!-- Messages des autres (à gauche, gris) -->
                <div v-else class="flex items-end space-x-2 max-w-md">
                  <div
                    class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0"
                    :style="{ backgroundColor: selectedConversation.avatarColor || '#8B5CF6' }"
                  >
                    {{ getInitials(selectedConversation.name) }}
                  </div>
                  <div class="bg-gray-700 text-white rounded-2xl rounded-bl-sm px-4 py-2 shadow-lg">
                    <p class="text-sm">{{ message.content }}</p>
                    <span class="text-xs text-gray-400 mt-1 block">{{ formatMessageTime(message.timestamp) }}</span>
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
              @click.prevent="sendMessage"
              :disabled="!newMessage.trim() || isSendingMessage"
              class="w-12 h-12 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-600 disabled:cursor-not-allowed rounded-full transition-colors flex items-center justify-center"
            >
              <div v-if="isSendingMessage" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
              <svg v-else class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <!-- Modal de création de groupe -->
    <div v-if="showCreateGroupModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-11/12 max-w-4xl h-5/6 flex flex-col">
        <!-- Header du modal -->
        <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Créer un nouveau groupe</h3>
          <button
            @click="closeGroupModal"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Contenu du modal -->
        <div class="flex-1 flex overflow-hidden">
          <!-- Côté gauche - Utilisateurs disponibles -->
          <div class="w-1/2 border-r dark:border-gray-700 flex flex-col">
            <div class="p-4 border-b dark:border-gray-700">
              <h4 class="font-medium text-gray-900 dark:text-white mb-2">Utilisateurs disponibles</h4>
              <div class="relative">
                <input
                  type="text"
                  placeholder="Rechercher un utilisateur..."
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4">
              <div v-if="isLoadingUsers" class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Chargement des utilisateurs...</p>
              </div>

              <div v-else-if="availableUsers.length === 0" class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">Aucun utilisateur disponible</p>
              </div>

              <div v-else class="space-y-2">
                <div
                  v-for="user in availableUsers"
                  :key="user.id"
                  @click="toggleUserSelection(user)"
                  class="flex items-center p-3 rounded-lg cursor-pointer transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
                  :class="{ 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700': isUserSelected(user) }"
                >
                  <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-lg">
                      {{ user.name ? user.name.charAt(0).toUpperCase() : '?' }}
                    </div>
                    <div
                      class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white dark:border-gray-800"
                      :class="user.is_online ? 'bg-green-500' : 'bg-gray-400'"
                    ></div>
                  </div>

                  <div class="ml-3 flex-1">
                    <div class="font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                  </div>

                  <div v-if="isUserSelected(user)" class="text-blue-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Côté droit - Configuration du groupe -->
          <div class="w-1/2 flex flex-col">
            <div class="p-4 border-b dark:border-gray-700">
              <h4 class="font-medium text-gray-900 dark:text-white mb-3">Configuration du groupe</h4>

              <!-- Nom du groupe -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Nom du groupe
                </label>
                <input
                  v-model="groupName"
                  type="text"
                  placeholder="Entrez le nom du groupe"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <!-- Utilisateurs sélectionnés -->
            <div class="flex-1 overflow-y-auto p-4">
              <div class="mb-3">
                <h5 class="font-medium text-gray-900 dark:text-white">
                  Participants sélectionnés ({{ selectedGroupUsers.length }})
                </h5>
              </div>

              <div v-if="selectedGroupUsers.length === 0" class="text-center py-8">
                <div class="text-gray-400 mb-2">
                  <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400">Aucun participant sélectionné</p>
                <p class="text-sm text-gray-400 dark:text-gray-500">Sélectionnez des utilisateurs dans la liste de gauche</p>
              </div>

              <div v-else class="space-y-2">
                <div
                  v-for="user in selectedGroupUsers"
                  :key="user.id"
                  class="flex items-center p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50"
                >
                  <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-sm">
                    {{ user.name ? user.name.charAt(0).toUpperCase() : '?' }}
                  </div>
                  <div class="ml-3 flex-1">
                    <div class="font-medium text-gray-900 dark:text-white text-sm">{{ user.name }}</div>
                  </div>
                  <button
                    @click="toggleUserSelection(user)"
                    class="text-gray-400 hover:text-red-500 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer du modal -->
        <div class="flex items-center justify-end gap-3 p-4 border-t dark:border-gray-700">
          <button
            @click="closeGroupModal"
            class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
          >
            Annuler
          </button>
          <button
            @click="createGroup"
            :disabled="!groupName.trim() || selectedGroupUsers.length === 0 || isSavingGroup"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
          >
            <div v-if="isSavingGroup" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
            {{ isSavingGroup ? 'Création...' : 'Créer le groupe' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import { usePusher } from '@/composables/usePusher.js';
import Profil from './Profil.vue';
import Sidebar from './Sidebar.vue';
import UserInfo from './UserInfo.vue';

// Récupérer l'utilisateur actuel depuis Inertia
const { props } = usePage();
const user = computed(() => props.auth?.user || props.user);

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
const isSendingMessage = ref(false);

// Polling pour les messages (alternative à Pusher)
const pollingInterval = ref(null);
const lastMessageCheck = ref(new Date());

// Pusher setup
const { pusher, isConnected, subscribeToConversation, unsubscribeFromConversation } = usePusher();
let currentConversationChannel = null;

// États pour le modal de création de groupe
const showCreateGroupModal = ref(false);
const groupName = ref('');
const availableUsers = ref([]);
const selectedGroupUsers = ref([]);
const isLoadingUsers = ref(false);
const isSavingGroup = ref(false);

// Computed
const currentMessages = computed(() => {
  if (!selectedConversation.value) return [];
  const messages = allMessages.value[selectedConversation.value.id] || [];
  // Tri par date croissante (du plus ancien au plus récent)
  return [...messages].sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));
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

// Computed pour le statut de l'utilisateur de la conversation
const conversationUserStatus = computed(() => {
  if (!selectedConversation.value) return { text: 'hors ligne', color: 'gray' };

  // Utiliser les données du participant si disponibles
  if (selectedConversation.value.participant) {
    const participant = selectedConversation.value.participant;

    if (participant.status === 'online') {
      return { text: 'en ligne', color: 'green' };
    }

    if (!participant.last_seen_at) {
      return { text: 'hors ligne', color: 'gray' };
    }

    const lastSeen = new Date(participant.last_seen_at);
    const now = new Date();
    const diffInMinutes = Math.floor((now - lastSeen) / (1000 * 60));

    if (diffInMinutes < 1) {
      return { text: 'à l\'instant', color: 'yellow' };
    } else if (diffInMinutes < 60) {
      return { text: `il y a ${diffInMinutes} min`, color: 'yellow' };
    } else if (diffInMinutes < 1440) { // 24 hours
      const hours = Math.floor(diffInMinutes / 60);
      return { text: `il y a ${hours}h`, color: 'gray' };
    } else {
      const days = Math.floor(diffInMinutes / 1440);
      return { text: `il y a ${days}j`, color: 'gray' };
    }
  }

  // Fallback avec les données directes de la conversation
  if (selectedConversation.value.is_online) {
    return { text: 'en ligne', color: 'green' };
  }

  return { text: 'hors ligne', color: 'gray' };
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

const selectConversation = async (conversation) => {
  // Unsubscribe from previous conversation
  if (currentConversationChannel && selectedConversation.value) {
    unsubscribeFromConversation(selectedConversation.value.id);
    currentConversationChannel = null;
  }

  selectedConversation.value = conversation;
  showUserInfo.value = false; // Fermer le panel utilisateur

  // Charger les messages de cette conversation si pas encore chargés
  if (!allMessages.value[conversation.id]) {
    await loadConversationMessages(conversation.id);
  }

  // Subscribe to new conversation for real-time updates (Pusher - backup)
  if (pusher && conversation.id) {
    currentConversationChannel = subscribeToConversation(conversation.id, (data) => {
      // Handle incoming message
      if (data.message && data.conversation_id === conversation.id) {
        const newMessage = {
          ...data.message,
          is_own: data.message.user_id === user.value.id,
          isOwn: data.message.user_id === user.value.id
        };

        if (!allMessages.value[conversation.id]) {
          allMessages.value[conversation.id] = [];
        }

        // Avoid duplicate messages
        const existingMessage = allMessages.value[conversation.id].find(msg => msg.id === newMessage.id);
        if (!existingMessage) {
          allMessages.value[conversation.id].push(newMessage);

          // Update conversation in list
          const conversationIndex = conversations.value.findIndex(conv => conv.id === conversation.id);
          if (conversationIndex !== -1) {
            conversations.value[conversationIndex].last_message = newMessage.content;
            conversations.value[conversationIndex].last_message_time = 'À l\'instant';
            conversations.value[conversationIndex].formatted_time = newMessage.formatted_time;

            // Move conversation to top if it's not the current one being viewed
            if (selectedConversation.value?.id !== conversation.id) {
              const updatedConversation = conversations.value.splice(conversationIndex, 1)[0];
              conversations.value.unshift(updatedConversation);
            }
          }

          scrollToBottom();
        }
      }
    });
  }

  scrollToBottom();

  // Démarrer le polling pour cette conversation
  lastMessageCheck.value = new Date();
  startPolling();
};

const showConversationUserInfo = () => {
  if (!selectedConversation.value) return;

  // Si c'est un groupe, afficher les informations du groupe
  if (selectedConversation.value.is_group) {
    selectedUser.value = {
      id: selectedConversation.value.id,
      name: selectedConversation.value.name,
      username: `Groupe • ${selectedConversation.value.participants_count || (selectedConversation.value.users?.length || 0)} participants`,
      email: null,
      is_group: true,
      participants: selectedConversation.value.users || [],
      participants_count: selectedConversation.value.participants_count,
      avatarColor: '#10B981',
      status: 'group',
      created_at: selectedConversation.value.created_at || null
    };
  }
  // Si la conversation a des utilisateurs spécifiques, utiliser l'autre participant
  else if (selectedConversation.value.users && selectedConversation.value.users.length > 0) {
    const currentUserId = usePage().props.auth?.user?.id;
    const otherUser = selectedConversation.value.users.find(user => user.id !== currentUserId);
    if (otherUser) {
      selectedUser.value = {
        ...otherUser,
        avatarColor: otherUser.avatar_color
      };
    }
  }
  // Sinon utiliser les données participant si disponibles
  else if (selectedConversation.value.other_user) {
    selectedUser.value = {
      ...selectedConversation.value.other_user,
      avatarColor: selectedConversation.value.other_user.avatar_color
    };
  }
  // Fallback : construire à partir des données de conversation
  else {
    selectedUser.value = {
      id: selectedConversation.value.id,
      name: selectedConversation.value.name,
      username: selectedConversation.value.username || null,
      email: selectedConversation.value.email || null,
      avatarColor: selectedConversation.value.avatar_color || selectedConversation.value.avatarColor,
      status: selectedConversation.value.is_online ? 'online' : 'offline',
      last_seen_at: selectedConversation.value.lastSeen || null,
      created_at: selectedConversation.value.created_at || null
    };
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

const handleStartConversation = async (userToChat) => {
  try {
    // Vérifier si une conversation avec cet utilisateur existe déjà
    const existingConversation = conversations.value.find(conv =>
      conv.users && conv.users.some(u => u.id === userToChat.id)
    );

    if (existingConversation) {
      await selectConversation(existingConversation);
    } else {
      const newConversation = await createConversation(userToChat);
      await loadConversations();
      const refreshedConversation = conversations.value.find(conv => conv.id === newConversation.id);

      if (refreshedConversation) {
        await selectConversation(refreshedConversation);
      } else {
        await selectConversation(newConversation);
      }
    }

    showUserInfo.value = false;
    selectedUser.value = null;
  } catch (error) {
    // Erreur pour l'instant à revoir
  }
};

const handleNewGroup = async () => {
  // Réinitialiser les états
  groupName.value = '';
  selectedGroupUsers.value = [];

  // Charger les utilisateurs disponibles
  await loadAvailableUsersForGroup();

  // Ouvrir le modal
  showCreateGroupModal.value = true;
};

const handleNewMessage = () => {
  // A faire apres : Implémenter nouveau message
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedConversation.value || isSendingMessage.value) return;

  const messageContent = newMessage.value.trim();
  newMessage.value = '';
  resetTextareaHeight();

  isSendingMessage.value = true;

  try {
    const response = await axios.post(`/api/conversations/${selectedConversation.value.id}/messages`, {
      content: messageContent,
      type: 'text'
    },{
        headers: {
            'X-Inertia': false,
        'X-Requested-With': 'XMLHttpRequest'
        }
    });

    if (response.data && response.data.message) {
      // Ajouter le message à la liste locale
      if (!allMessages.value[selectedConversation.value.id]) {
        allMessages.value[selectedConversation.value.id] = [];
      }

      // Marquer le message comme étant le vôtre
      const messageWithOwnership = {
        ...response.data.message,
        isOwn: true,
        is_own: true
      };

      allMessages.value[selectedConversation.value.id].push(messageWithOwnership);



      // Mettre à jour la conversation dans la liste
      const conversationIndex = conversations.value.findIndex(conv => conv.id === selectedConversation.value.id);
      if (conversationIndex !== -1) {
        conversations.value[conversationIndex].last_message = response.data.message.content;
        conversations.value[conversationIndex].last_message_time = 'À l\'instant';
        conversations.value[conversationIndex].formatted_time = response.data.message.formatted_time;

        // Remonter la conversation en haut de la liste
        const updatedConversation = conversations.value.splice(conversationIndex, 1)[0];
        conversations.value.unshift(updatedConversation);
      }

      scrollToBottom();
    }

  } catch (error) {
    console.error('Erreur lors de l\'envoi du message:', error);
    // Remettre le contenu dans le champ en cas d'erreur
    newMessage.value = messageContent;

    let errorMessage = 'Erreur lors de l\'envoi du message';
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage = error.response.data.message;
    }

    // Optionnel : Afficher une notification d'erreur
    alert(errorMessage);
  } finally {
    isSendingMessage.value = false;
  }
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

//
// FONCTIONS API
//

// Charger les conversations depuis l'API
const loadConversations = async () => {
  try {
    isLoading.value = true;
    const response = await axios.get('/api/conversations');
    conversations.value = response.data.data || response.data || [];
  } catch (error) {
    conversations.value = [];
  } finally {
    isLoading.value = false;
  }
};

// Charger les messages d'une conversation
const loadConversationMessages = async (conversationId) => {
  try {
    const response = await axios.get(`/api/conversations/${conversationId}/messages`);

    if (response.data && response.data.messages) {
      // Ajouter la propriété isOwn à chaque message
      const messagesWithOwnership = response.data.messages.map(message => ({
        ...message,
        isOwn: message.user_id === user.value.id,
        is_own: message.user_id === user.value.id
      }));
      allMessages.value[conversationId] = messagesWithOwnership;
    } else {
      allMessages.value[conversationId] = [];
    }
  } catch (error) {
    console.error('Erreur lors du chargement des messages:', error);
    allMessages.value[conversationId] = [];
  }
};

// Vérifier les nouveaux messages via polling
const checkForNewMessages = async () => {
  if (!selectedConversation.value) return;

  try {
    const response = await axios.get(`/api/conversations/${selectedConversation.value.id}/messages?since=${lastMessageCheck.value.toISOString()}`);

    if (response.data && response.data.messages && response.data.messages.length > 0) {
      const newMessages = response.data.messages.map(message => ({
        ...message,
        isOwn: message.user_id === user.value.id,
        is_own: message.user_id === user.value.id
      }));

      // Ajouter seulement les nouveaux messages
      newMessages.forEach(newMessage => {
        const existingMessage = allMessages.value[selectedConversation.value.id]?.find(msg => msg.id === newMessage.id);
        if (!existingMessage) {
          if (!allMessages.value[selectedConversation.value.id]) {
            allMessages.value[selectedConversation.value.id] = [];
          }
          allMessages.value[selectedConversation.value.id].push(newMessage);

          // Les messages des autres s'affichent automatiquement
        }
      });

      scrollToBottom();
      lastMessageCheck.value = new Date();
    }
  } catch (error) {
    console.error('Erreur lors de la vérification des nouveaux messages:', error);
  }
};

// Démarrer le polling
const startPolling = () => {
  if (pollingInterval.value) {
    clearInterval(pollingInterval.value);
  }
  pollingInterval.value = setInterval(checkForNewMessages, 2000); // Vérifier toutes les 2 secondes
};

// Arrêter le polling
const stopPolling = () => {
  if (pollingInterval.value) {
    clearInterval(pollingInterval.value);
    pollingInterval.value = null;
  }
};

// Créer une nouvelle conversation
const createConversation = async (userToChat) => {
  const response = await axios.post('/api/conversations', {
    user_id: userToChat.id
  });

  const newConversation = response.data;
  conversations.value.unshift(newConversation);
  selectConversation(newConversation);
  return newConversation;
};

// Group Functions
const loadAvailableUsersForGroup = async () => {
  try {
    isLoadingUsers.value = true;
    const response = await axios.get('/api/users/available');
    availableUsers.value = response.data.users || [];
  } catch (error) {
    availableUsers.value = [];
  } finally {
    isLoadingUsers.value = false;
  }
};

const toggleUserSelection = (user) => {
  const index = selectedGroupUsers.value.findIndex(u => u.id === user.id);
  if (index > -1) {
    selectedGroupUsers.value.splice(index, 1);
  } else {
    selectedGroupUsers.value.push(user);
  }
};

const isUserSelected = (user) => {
  return selectedGroupUsers.value.some(u => u.id === user.id);
};

const createGroup = async () => {
  if (!groupName.value.trim()) {
    alert('Veuillez saisir un nom de groupe');
    return;
  }

  if (selectedGroupUsers.value.length === 0) {
    alert('Veuillez sélectionner au moins un utilisateur');
    return;
  }

  // Sauvegarder les données avant de fermer le modal
  const groupNameToCreate = groupName.value;
  const participantsToAdd = selectedGroupUsers.value.map(u => u.id);

  // Fermer le modal immédiatement pour éviter les doublons
  closeGroupModal();

  try {
    isSavingGroup.value = true;

    const response = await axios.post('/api/conversations/group', {
      name: groupNameToCreate,
      participants: participantsToAdd
    });

    if (response.data && response.data.id) {
      // Ajouter la nouvelle conversation à la liste
      conversations.value.unshift(response.data);

      // Sélectionner automatiquement la nouvelle conversation
      selectedConversation.value = response.data;
    }

  } catch (error) {
    alert('Erreur lors de la création du groupe');
  } finally {
    isSavingGroup.value = false;
  }
};

const closeGroupModal = () => {
  showCreateGroupModal.value = false;
  groupName.value = '';
  selectedGroupUsers.value = [];
  availableUsers.value = [];
};

// Lifecycle
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

// Nettoyer les connexions Pusher à la déconnexion
onUnmounted(() => {
  stopPolling(); // Arrêter le polling
  if (currentConversationChannel && selectedConversation.value) {
    unsubscribeFromConversation(selectedConversation.value.id);
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

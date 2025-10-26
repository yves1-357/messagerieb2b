import { ref, onMounted, onUnmounted } from 'vue';
import Pusher from 'pusher-js';

// Configuration Pusher
const pusherConfig = {
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        },
    },
};

export function usePusher() {
    const pusher = ref(null);
    const isConnected = ref(false);
    const error = ref(null);

    const connect = () => {
        try {
            // Vérifier si les clés Pusher sont configurées
            console.log('Pusher config:', {
                key: pusherConfig.key,
                cluster: pusherConfig.cluster,
                hasKey: !!pusherConfig.key
            });

            if (!pusherConfig.key) {
                console.warn('Pusher key not configured. Real-time features disabled.');
                return null;
            }

            pusher.value = new Pusher(pusherConfig.key, pusherConfig);

            pusher.value.connection.bind('connected', () => {
                isConnected.value = true;
                error.value = null;
                console.log('Pusher connected successfully');
            });

            pusher.value.connection.bind('disconnected', () => {
                isConnected.value = false;
                console.log('Pusher disconnected');
            });

            pusher.value.connection.bind('error', (err) => {
                error.value = err;
                isConnected.value = false;
                console.error('Pusher connection error:', err);
            });

            return pusher.value;
        } catch (err) {
            error.value = err;
            console.error('Failed to initialize Pusher:', err);
            return null;
        }
    };

    const disconnect = () => {
        if (pusher.value) {
            pusher.value.disconnect();
            pusher.value = null;
            isConnected.value = false;
        }
    };

    const subscribeToConversation = (conversationId, onMessageReceived) => {
        if (!pusher.value || !conversationId) {
            console.warn('Cannot subscribe: Pusher not initialized or no conversation ID');
            return null;
        }

        try {
            const channel = pusher.value.subscribe(`private-conversation.${conversationId}`);

            channel.bind('pusher:subscription_succeeded', () => {
                console.log(`Successfully subscribed to conversation ${conversationId}`);
            });

            channel.bind('pusher:subscription_error', (error) => {
                console.error(`Subscription error for conversation ${conversationId}:`, error);
            });

            channel.bind('message.sent', (data) => {
                console.log('New message received:', data);
                if (onMessageReceived) {
                    onMessageReceived(data);
                }
            });

            return channel;
        } catch (err) {
            console.error('Failed to subscribe to conversation:', err);
            return null;
        }
    };

    const unsubscribeFromConversation = (conversationId) => {
        if (!pusher.value || !conversationId) return;

        try {
            pusher.value.unsubscribe(`private-conversation.${conversationId}`);
            console.log(`Unsubscribed from conversation ${conversationId}`);
        } catch (err) {
            console.error('Failed to unsubscribe from conversation:', err);
        }
    };

    const subscribeToUserChannel = (userId, onNotificationReceived) => {
        if (!pusher.value || !userId) {
            console.warn('Cannot subscribe to user channel: Pusher not initialized or no user ID');
            return null;
        }

        try {
            const channel = pusher.value.subscribe(`private-user.${userId}`);

            channel.bind('pusher:subscription_succeeded', () => {
                console.log(`Successfully subscribed to user channel ${userId}`);
            });

            channel.bind('notification.sent', (data) => {
                console.log('New notification received:', data);
                if (onNotificationReceived) {
                    onNotificationReceived(data);
                }
            });

            return channel;
        } catch (err) {
            console.error('Failed to subscribe to user channel:', err);
            return null;
        }
    };

    // Gestion automatique de la connexion
    onMounted(() => {
        connect();
    });

    onUnmounted(() => {
        disconnect();
    });

    return {
        pusher: pusher.value,
        isConnected,
        error,
        connect,
        disconnect,
        subscribeToConversation,
        unsubscribeFromConversation,
        subscribeToUserChannel,
    };
}

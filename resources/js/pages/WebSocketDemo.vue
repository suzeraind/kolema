<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { demo } from '@/routes/websocket';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import { ref } from 'vue';

interface Message {
    id: number;
    message: string;
    user: string;
    userId: number;
    timestamp: string;
}

interface Props {
    initialMessages?: Message[];
    currentUser?: {
        id: number;
        name: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'WebSocket Demo', href: demo().url },
];

const messages = ref<Message[]>(props.initialMessages || []);
const isConnected = ref(false);

const form = useForm({
    message: '',
});

if (typeof window !== 'undefined') {
    const { channel } = useEcho<Message>('demo');

    channel().subscribed(() => {
        console.log('Channel subscribed!');
        isConnected.value = true;
    });

    channel().listen('MessageSentEvent', (data: Message) => {
        console.log('Message received:', data);
        messages.value.push(data);
        scrollToBottom();
    });

    channel().listen('MessageDeletedEvent', (data: { messageId: number; }) => {
        console.log('Message deleted:', data);
        const index = messages.value.findIndex(m => m.id === data.messageId);
        if (index !== -1) {
            messages.value.splice(index, 1);
        }
    });
}

const sendMessage = () => {
    if (!form.message.trim()) return;

    form.post('/api/websocket/send', {
        preserveScroll: true,
        onSuccess: () => form.reset('message'),
    });
};

const deleteMessage = (messageId: number) => {
    if (!confirm('Вы уверены, что хотите удалить это сообщение?')) return;

    form.delete(`/api/websocket/message/${messageId}`, {
        preserveScroll: true,
    });
};

const scrollToBottom = () => {
    setTimeout(() => {
        const container = document.getElementById('messages-container');
        if (container) container.scrollTop = container.scrollHeight;
    }, 100);
};

const formatTime = (timestamp: string) =>
    new Date(timestamp).toLocaleTimeString('ru-RU', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
</script>

<template>

    <Head title="WebSocket Demo" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl p-6">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    WebSocket Demo
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Демонстрация работы Laravel Reverb с использованием хука
                    useEcho
                </p>
            </div>

            <!-- Connection Status -->
            <div class="mb-4 flex items-center gap-2">
                <div :class="[
                    'h-3 w-3 rounded-full',
                    isConnected ? 'bg-green-500' : 'bg-red-500',
                ]" />
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{
                        isConnected
                            ? 'Подключено к WebSocket'
                            : 'Отключено от WebSocket'
                    }}
                </span>
            </div>

            <!-- Messages Container -->
            <div
                class="mb-4 rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div
                    id="messages-container"
                    class="h-96 space-y-3 overflow-y-auto p-4"
                >
                    <div
                        v-if="messages.length === 0"
                        class="flex h-full items-center justify-center text-gray-500 dark:text-gray-400"
                    >
                        Нет сообщений. Отправьте первое сообщение!
                    </div>

                    <div
                        v-for="msg in messages"
                        :key="msg.id"
                        class="rounded-lg bg-gray-50 p-3 dark:bg-gray-700"
                    >
                        <div class="mb-1 flex items-center justify-between">
                            <span class="font-semibold text-blue-600 dark:text-blue-400">
                                {{ msg.user }}
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ formatTime(msg.timestamp) }}
                                </span>
                                <button
                                    v-if="currentUser && msg.userId === currentUser.id"
                                    type="button"
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                    @click="deleteMessage(msg.id)"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-gray-800 dark:text-gray-200">
                            {{ msg.message }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Input Form -->
            <div class="space-y-3">

                <div>
                    <label
                        for="message"
                        class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Сообщение
                    </label>
                    <div class="flex gap-2">
                        <input
                            id="message"
                            v-model="form.message"
                            type="text"
                            class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            placeholder="Введите сообщение..."
                            @keyup.enter="sendMessage"
                        />
                        <button
                            type="button"
                            :disabled="!form.message.trim() || form.processing"
                            class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-gray-800"
                            @click="sendMessage"
                        >
                            {{ form.processing ? 'Отправка...' : 'Отправить' }}
                        </button>
                    </div>
                    <div
                        v-if="form.errors.message"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.message }}
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                <h3 class="mb-2 font-semibold text-blue-900 dark:text-blue-300">
                    Как это работает:
                </h3>
                <ul class="list-inside list-disc space-y-1 text-sm text-blue-800 dark:text-blue-400">
                    <li>
                        Используется хук <code>useEcho()</code> из
                        @laravel/echo-vue
                    </li>
                    <li>
                        Подписка на публичный канал <code>'demo'</code>
                    </li>
                    <li>
                        Событие <code>MessageSent</code> транслируется через
                        Laravel Reverb
                    </li>
                    <li>
                        Все подключенные клиенты получают сообщения в реальном
                        времени
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>

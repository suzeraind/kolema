<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { demo } from '@/routes/websocket';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import { onMounted, ref } from 'vue';

interface Message {
    message: string;
    user: string;
    timestamp: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'WebSocket Demo', href: demo().url },
];

const messages = ref<Message[]>([]);
const isConnected = ref(false);

const form = useForm({
    message: '',
    user: 'User_' + Math.floor(Math.random() * 1000),
});

if (window !== undefined) {
    const { channel } = useEcho<Message>(
        'demo',
        "MessageSentEvent",
        (data: Message) => {
            console.log('Message received:', data);
            messages.value.push(data);
            scrollToBottom();
            isConnected.value = true;
        }
    );
}

const sendMessage = () => {
    if (!form.message.trim()) return;

    form.post('/api/websocket/send', {
        preserveScroll: true,
        onSuccess: () => form.reset('message'),
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
                        v-for="(msg, index) in messages"
                        :key="index"
                        class="rounded-lg bg-gray-50 p-3 dark:bg-gray-700"
                    >
                        <div class="mb-1 flex items-center justify-between">
                            <span class="font-semibold text-blue-600 dark:text-blue-400">
                                {{ msg.user }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ formatTime(msg.timestamp) }}
                            </span>
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
                        for="username"
                        class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Ваше имя
                    </label>
                    <input
                        id="username"
                        v-model="form.user"
                        type="text"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        placeholder="Введите ваше имя"
                    />
                    <div
                        v-if="form.errors.user"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.user }}
                    </div>
                </div>

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

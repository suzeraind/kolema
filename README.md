# Laravel Vue Starter Kit

A modern, production-ready Laravel starter template featuring **Server-Side Rendering (SSR)**, **Real-time WebSockets**, and **Laravel Sail**.

## ✨ Key Features

- **Laravel 12**
- **Vue 3** & **Inertia.js v2** (SSR support)
- **TypeScript** & **Tailwind CSS 4**
- **Laravel Reverb** (WebSockets)
- **Laravel Sail** (Docker environment)
- **Pest PHP** (Testing)

## 📋 Prerequisites

- **Docker**
- **PHP 8.4+** & **Composer**
- **Node.js 22+** & **npm**

## 🚀 Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd kolema
   ```

2. **Install dependencies and setup**
   ```bash
   composer setup
   ```

3. **Start the development server**
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Install frontend dependencies & run dev**
   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run dev
   ```

## 🧪 Testing

```bash
./vendor/bin/sail test
```

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

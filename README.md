# Laravel Vue Starter Kit

A modern, production-ready Laravel starter template featuring **Server-Side Rendering (SSR)**, **Supervisor** **Real-time WebSockets**, **Laravel Sail** for containerized development, and a comprehensive suite of cutting-edge technologies for building performant full-stack web applications.

---

## ✨ Features

### 🚀 Core Technologies

- **[Laravel 12](https://laravel.com)** - The latest version of the PHP framework for web artisans
- **[Vue 3](https://vuejs.org)** - Progressive JavaScript framework with Composition API
- **[Inertia.js v2](https://inertiajs.com)** - Modern monolith architecture connecting Laravel and Vue
- **[TypeScript](https://www.typescriptlang.org)** - Type-safe JavaScript for better developer experience
- **[Tailwind CSS 4](https://tailwindcss.com)** - Utility-first CSS framework for rapid UI development

### 🎯 Advanced Features

#### Server-Side Rendering (SSR)
- **Full SSR support** with Inertia.js for improved SEO and initial page load performance
- SSR-safe component development with proper hydration
- Dedicated SSR entry point and build configuration
- Production-ready SSR server with Laravel Inertia

#### Real-time WebSockets
- **[Laravel Reverb](https://reverb.laravel.com)** - First-party Laravel WebSocket server
- **Laravel Echo** integration for seamless real-time event broadcasting
- Pusher protocol compatibility for easy scaling
- Real-time notifications, chat, and live updates out-of-the-box

#### Development Environment
- **[Laravel Sail](https://laravel.com/docs/sail)** - Docker-powered local development environment
- Pre-configured services: MySQL, Redis, Mailpit, and more
- Consistent development experience across different operating systems
- One-command setup and deployment

#### Authentication & Security
- **[Laravel Fortify](https://laravel.com/docs/fortify)** - Headless authentication backend
- Login, registration, password reset, and email verification
- Two-factor authentication ready
- Secure session management

#### Modern Development Tools
- **[Laravel Wayfinder](https://github.com/laravel/wayfinder)** - Type-safe Laravel routing for Vue
- **[Vite](https://vitejs.dev)** - Lightning-fast frontend build tool
- **Hot Module Replacement (HMR)** for instant feedback during development
- Code splitting and optimized production builds

#### Testing & Code Quality
- **[Pest PHP](https://pestphp.com)** - Elegant PHP testing framework
- **[ESLint](https://eslint.org)** - JavaScript/TypeScript linting
- **[Prettier](https://prettier.io)** - Opinionated code formatter
- **[Laravel Pint](https://laravel.com/docs/pint)** - PHP code style fixer
- Pre-configured testing environment with feature and unit test examples

#### UI Components
- **[Reka UI](https://reka-ui.com)** - Unstyled, accessible Vue components
- **[Lucide Icons](https://lucide.dev)** - Beautiful & consistent icon set
- **[Class Variance Authority](https://cva.style)** - Component variant utilities
- **VueUse** - Collection of essential Vue composition utilities

---

## 📋 Prerequisites


- **PHP 8.2+**
- **Composer**
- **Node.js 20+** and **npm**
- **Docker**

---

## 🚀 Getting Started

### Quick Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd kolema
   ```

2. **Install dependencies and setup**
   ```bash
   composer setup
   ```
   
   This command will:
   - Install PHP dependencies
   - Copy `.env.example` to `.env`
   - Generate application key
   - Run database migrations
   - Install Node dependencies
   - Build frontend assets

3. **Start the development server**
   ```bash
    sail up -d --build
   ```
   
   This will start the Docker containers for your application, including:
   - The application server (PHP, Nginx)
   - Database (MySQL/PostgreSQL)
   - Redis (for caching and queues)

### Development with SSR

To run the application with Server-Side Rendering:

```bash
sail npm run dev:ssr
```

This will:
- Build SSR bundle
- Start PHP development server
- Start queue worker
- Start log monitoring
- Start Inertia SSR server

### Using Laravel Sail (Docker)

For a fully containerized development environment:

1. **Start Sail**
   ```bash
   ./vendor/bin/sail up -d --build
   ```

2. **Run migrations**
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

3. **Install frontend dependencies**
   ```bash
   ./vendor/bin/sail npm install
   ```

4. **Start development**
   ```bash
   ./vendor/bin/sail npm run dev
   ```

You can create a shell alias for convenience:
```bash
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```

---

## 🧪 Testing

This starter kit uses **Pest PHP** for elegant, expressive testing:

```bash
# Run all tests
sail test

# Run specific test file
sail artisan test tests/Feature/ExampleTest.php

# Run with coverage
sail artisan test --coverage
```



### Build for Production

```bash
# Build frontend assets
sail npm run build

# Build with SSR
sail npm run build:ssr
```

### Environment Configuration

1. Update `.env` for production:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   ```

2. Optimize Laravel:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. Set up queue workers (use Supervisor or similar)
4. Configure WebSocket server (Reverb) behind a reverse proxy
5. Enable HTTPS and configure proper CORS settings

---

## 📚 Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Inertia.js Documentation](https://inertiajs.com)
- [Vue 3 Documentation](https://vuejs.org)
- [Laravel Reverb Documentation](https://reverb.laravel.com)
- [Laravel Sail Documentation](https://laravel.com/docs/sail)
- [Pest PHP Documentation](https://pestphp.com)
- [Tailwind CSS Documentation](https://tailwindcss.com)

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 💡 Tips & Best Practices

### SSR Considerations
- Avoid using `window`, `document`, or browser-specific APIs in components that render server-side
- Use `onMounted()` lifecycle hook for client-only code
- Leverage `import.meta.env.SSR` to conditionally run code

### WebSocket Best Practices
- Use private/presence channels for authenticated users
- Implement proper error handling and reconnection logic
- Consider rate limiting for broadcast events
- Use Redis for horizontal scaling with multiple WebSocket servers

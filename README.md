<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Cookies Manager CRM

Cookies Manager CRM is a sophisticated campaign management platform designed to help businesses create, deploy, and manage marketing campaigns across multiple websites and markets. Built with modern web technologies, it provides a comprehensive solution for campaign orchestration, automated deployment, and real-time monitoring.

### What This Project Does

This platform enables marketing teams and operators to:

- **Campaign Management**: Create and configure marketing campaigns with customizable triggers, scheduling, and targeting rules
- **Multi-Website Deployment**: Deploy campaigns across multiple websites and domains simultaneously
- **Market Segmentation**: Organize campaigns by different markets and operators for targeted marketing
- **Automated Scheduling**: Set up campaigns with start/end dates, priority levels, and rotation delays
- **Real-time Monitoring**: Track campaign deployments and performance across all connected websites
- **Cloudflare Integration**: Leverage Cloudflare KV storage for global campaign distribution and edge deployment

### Core Features

- **Campaign Lifecycle Management**: Full campaign creation, editing, scheduling, and deployment workflow
- **Website & Market Organization**: Manage multiple websites, markets, and operators from a single dashboard
- **Automated Deployment Pipeline**: Background job processing for reliable campaign deployment
- **Trigger-based Activation**: Configure campaigns to activate based on specific conditions and user interactions
- **Priority & Rotation System**: Intelligent campaign rotation with customizable priority levels and delays
- **Secure Authentication**: Role-based access control with Laravel Breeze integration
- **Real-time UI**: Interactive interface built with Livewire for seamless user experience

### Tech Stack

#### Backend Framework

- **Laravel 12** - Modern PHP framework with elegant syntax and powerful features
- **PHP 8.2+** - Latest PHP version with improved performance and type safety

#### Frontend & UI

- **Livewire 3** - Full-stack framework for building dynamic interfaces without JavaScript complexity
- **Volt** - Functional API for Livewire components
- **Tailwind CSS** - Utility-first CSS framework for rapid UI development
- **Blade Heroicons** - Beautiful SVG icons for modern web applications

#### Database & Queue Management

- **SQLite/PostgreSQL/MySQL** - Reliable database storage with Laravel's Eloquent ORM
- **Laravel Horizon** - Queue monitoring and management dashboard
- **Background Jobs** - Asynchronous campaign deployment processing

#### Development & Deployment

- **Vite** - Fast build tool and development server
- **Cloudflare Workers** - Edge computing for global campaign distribution
- **Laravel Sail** - Docker-based development environment

## Benefits of This Tech Stack

### Performance & Scalability

- **Asynchronous Processing**: Laravel Horizon handles campaign deployments in background queues
- **Edge Distribution**: Cloudflare KV enables lightning-fast global campaign delivery
- **Optimized Frontend**: Vite provides fast builds and hot module replacement during development
- **Database Efficiency**: Eloquent ORM with optimized queries and eager loading

### Developer Experience

- **Rapid Development**: Laravel's expressive syntax and conventions accelerate feature development
- **Type Safety**: PHP 8.2+ features like union types and readonly properties improve code reliability
- **Hot Reloading**: Livewire enables reactive UIs without complex JavaScript frameworks
- **Integrated Testing**: Built-in PHPUnit integration for comprehensive test coverage

### Security & Reliability

- **Built-in Security**: Protection against CSRF, XSS, SQL injection, and other common vulnerabilities
- **Authentication**: Laravel Breeze provides secure, battle-tested authentication flows
- **Queue Reliability**: Failed job handling and retry mechanisms ensure campaign deployment reliability
- **Soft Deletes**: Data integrity with reversible deletion operations

### Maintenance & Monitoring

- **Real-time Debugging**: Laravel Debugbar for development insights
- **Queue Monitoring**: Horizon dashboard for job queue visualization and management
- **Comprehensive Logging**: Built-in logging with multiple channels and log levels
- **Error Tracking**: Exception handling with detailed stack traces and context

### Deployment & Operations

- **Docker Support**: Laravel Sail for consistent development and deployment environments
- **Asset Pipeline**: Vite handles CSS/JS building and optimization
- **Zero-downtime Deployments**: Queue-based architecture enables seamless updates
- **Cloud-ready**: Built for modern cloud platforms with horizontal scaling capabilities

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm/pnpm
- SQLite or MySQL database

### Installation

1. Clone the repository:

   ```bash
   git clone <repository-url>
   cd cookies-manager-crm
   ```

2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Install Node.js dependencies:

   ```bash
   pnpm install
   ```

4. Set up environment configuration:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Run database migrations:

   ```bash
   php artisan migrate
   ```

6. Build frontend assets:

   ```bash
   pnpm run build
   ```

7. Start the queue worker (for campaign deployments):

   ```bash
   php artisan horizon
   ```

8. Start the development server:

   ```bash
   php artisan serve
   ```

### Development with Docker

If you prefer using Docker, you can use Laravel Sail:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run dev
```

## Usage

1. **Create Markets & Operators**: Set up your business structure
2. **Add Websites**: Register the websites where campaigns will be deployed
3. **Create Campaigns**: Design campaigns with triggers, scheduling, and targeting
4. **Deploy**: Use the automated deployment system to push campaigns live
5. **Monitor**: Track performance through the Horizon dashboard

## Architecture Overview

The application follows a service-oriented architecture with clear separation of concerns:

- **Models**: Campaign, Website, Market, Operator entities with relationships
- **Services**: Business logic for campaign deployment and validation
- **Jobs**: Background processing for reliable campaign deployment
- **Livewire Components**: Interactive UI components for real-time updates

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

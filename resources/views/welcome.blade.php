<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>CampaignCraft</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

  <script>
    tailwind.config = {
      theme: {
        extend: {
          animation: {
            'gradient': 'gradient 8s linear infinite',
            'float': 'float 6s ease-in-out infinite',
            'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            'slide-up': 'slideUp 0.8s ease-out',
            'fade-in': 'fadeIn 1s ease-out',
            'bounce-slow': 'bounce 3s infinite',
          },
          keyframes: {
            gradient: {
              '0%, 100%': {
                'background-size': '200% 200%',
                'background-position': 'left center'
              },
              '50%': {
                'background-size': '200% 200%',
                'background-position': 'right center'
              }
            },
            float: {
              '0%, 100%': {
                transform: 'translateY(0px)'
              },
              '50%': {
                transform: 'translateY(-20px)'
              }
            },
            slideUp: {
              '0%': {
                transform: 'translateY(50px)',
                opacity: '0'
              },
              '100%': {
                transform: 'translateY(0)',
                opacity: '1'
              }
            },
            fadeIn: {
              '0%': {
                opacity: '0'
              },
              '100%': {
                opacity: '1'
              }
            }
          }
        }
      }
    }
  </script>
  <style>
    .glass-effect {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    . {
      /* Gradient removed */
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    . {
      /* /* Gradient removed */
      */ background-size: 200% 200%;
      animation: gradient 8s ease infinite;
    }

    .card-hover {
      transition: all 0.3s ease;
    }

    .card-hover:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    . {
      /* /* Gradient removed */
      */ -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
  </style>

  <!-- Styles -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-900 text-white overflow-x-hidden">
  <!-- Navigation -->
  <nav class="fixed top-0 w-full z-50 glass-effect animate-fade-in">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">
      <div class="flex justify-between items-center py-4">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-lg flex items-center justify-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
          </div>
          <span class="text-xl font-bold">CampaignCraft</span>
        </div>
        <div class="hidden md:flex space-x-8">
          <a href="#features" class="hover:text-purple-400 transition-colors duration-300">Features</a>
          <a href="#pricing" class="hover:text-purple-400 transition-colors duration-300">Pricing</a>
          <a href="#contact" class="hover:text-purple-400 transition-colors duration-300">Contact</a>
        </div>
        <div class="flex space-x-4">
          <a href="/login" class="px-4 py-2 text-purple-400 hover:text-white transition-colors duration-300">Sign
            In</a>
          <button
            class="px-6 py-2 bg-purple-700 hover:bg-purple-600 rounded-lg transition-all duration-300 transform hover:scale-105">Get
            Started</button>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="min-h-screen flex items-center justify-center relative overflow-hidden">
    <div class="absolute inset-0 bg-purple-900 opacity-10"></div>

    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div
        class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float">
      </div>
      <div
        class="absolute top-3/4 right-1/4 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float"
        style="animation-delay: 2s;"></div>
      <div
        class="absolute bottom-1/4 left-1/2 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float"
        style="animation-delay: 4s;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 sm:px-8 text-center relative z-10">
      <div class="animate-slide-up">
        <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
          Deploy Campaigns with
          <span class="gradient-text block mt-2">Precision & Power</span>
        </h1>
        <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto leading-relaxed">
          Transform your marketing strategy with our intelligent campaign manager. Launch, track, and optimize campaigns
          across multiple platforms with unprecedented ease.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
          <button
            class="px-8 py-4 bg-purple-700 hover:bg-purple-600 rounded-xl text-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-2xl">
            Start Free Trial
          </button>
          <button
            class="px-8 py-4 glass-effect rounded-xl text-lg font-semibold hover:bg-white hover:bg-opacity-20 transition-all duration-300 flex items-center space-x-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Watch Demo</span>
          </button>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-16 animate-fade-in" style="animation-delay: 0.5s;">
        <div class="text-center">
          <div class="text-3xl md:text-4xl font-bold text-purple-400 mb-2">10K+</div>
          <div class="text-gray-400">Active Campaigns</div>
        </div>
        <div class="text-center">
          <div class="text-3xl md:text-4xl font-bold text-indigo-400 mb-2">500+</div>
          <div class="text-gray-400">Happy Clients</div>
        </div>
        <div class="text-center">
          <div class="text-3xl md:text-4xl font-bold text-pink-400 mb-2">99.9%</div>
          <div class="text-gray-400">Uptime</div>
        </div>
        <div class="text-center">
          <div class="text-3xl md:text-4xl font-bold text-cyan-400 mb-2">24/7</div>
          <div class="text-gray-400">Support</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section id="features" class="py-20 relative">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">
      <div class="text-center mb-16">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
          Powerful Features for
          <span class="gradient-text">Modern Marketers</span>
        </h2>
        <p class="text-xl text-gray-400 max-w-3xl mx-auto">
          Everything you need to create, deploy, and manage successful campaigns in one intelligent platform.
        </p>
      </div>

      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Feature 1 -->
        <div class="glass-effect rounded-2xl p-8 card-hover">
          <div class="w-16 h-16 bg-purple-700 rounded-xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z">
              </path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-4">Lightning Fast Deployment</h3>
          <p class="text-gray-400 leading-relaxed">Deploy campaigns across multiple platforms in seconds with our
            automated deployment system.</p>
        </div>

        <!-- Feature 2 -->
        <div class="glass-effect rounded-2xl p-8 card-hover">
          <div class="w-16 h-16 bg-indigo-700 rounded-xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
              </path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-4">Advanced Analytics</h3>
          <p class="text-gray-400 leading-relaxed">Track performance with real-time analytics and AI-powered insights to
            optimize your campaigns.</p>
        </div>

        <!-- Feature 3 -->
        <div class="glass-effect rounded-2xl p-8 card-hover">
          <div class="w-16 h-16 bg-purple-700 rounded-xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4">
              </path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-4">Smart Automation</h3>
          <p class="text-gray-400 leading-relaxed">Automate your workflow with intelligent triggers and personalized
            customer journeys.</p>
        </div>

        <!-- Feature 4 -->
        <div class="glass-effect rounded-2xl p-8 card-hover">
          <div class="w-16 h-16 bg-indigo-700 rounded-xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
              </path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-4">Team Collaboration</h3>
          <p class="text-gray-400 leading-relaxed">Work seamlessly with your team with role-based access and real-time
            collaboration tools.</p>
        </div>

        <!-- Feature 5 -->
        <div class="glass-effect rounded-2xl p-8 card-hover">
          <div class="w-16 h-16 bg-pink-700 rounded-xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z">
              </path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-4">Enterprise Security</h3>
          <p class="text-gray-400 leading-relaxed">Bank-level security with end-to-end encryption and compliance with
            industry standards.</p>
        </div>

        <!-- Feature 6 -->
        <div class="glass-effect rounded-2xl p-8 card-hover">
          <div class="w-16 h-16 bg-cyan-700 rounded-xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
              </path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold mb-4">Custom Integrations</h3>
          <p class="text-gray-400 leading-relaxed">Connect with over 100+ tools and platforms through our robust API
            and integration marketplace.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-indigo-600 opacity-10"></div>
    <div class="max-w-4xl mx-auto text-center px-6 sm:px-8 relative z-10">
      <h2 class="text-4xl md:text-5xl font-bold mb-6">
        Ready to Transform Your
        <span class="gradient-text">Campaign Strategy?</span>
      </h2>
      <p class="text-xl text-gray-400 mb-8 leading-relaxed">
        Join thousands of marketers who are already using CampaignCraft to deploy, manage, and optimize their campaigns
        with unprecedented efficiency.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
        <button
          class="px-8 py-4 bg-purple-700 hover:bg-purple-600 rounded-xl text-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-2xl">
          Start Your Free Trial
        </button>
        <button
          class="px-8 py-4 glass-effect rounded-xl text-lg font-semibold hover:bg-white hover:bg-opacity-20 transition-all duration-300">
          Schedule a Demo
        </button>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-12 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="flex items-center space-x-3 mb-4 md:mb-0">
          <div class="w-8 h-8 rounded-lg bg-purple-700 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z">
              </path>
            </svg>
          </div>
          <span class="text-lg font-bold">CampaignCraft</span>
        </div>
        <div class="text-gray-400">
          © 2025 CampaignCraft. All rights reserved.
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Add smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // Add scroll effect to navigation
    window.addEventListener('scroll', () => {
      const nav = document.querySelector('nav');
      if (window.scrollY > 100) {
        nav.classList.add('bg-gray-900');
        nav.classList.add('bg-opacity-90');
      } else {
        nav.classList.remove('bg-gray-900');
        nav.classList.remove('bg-opacity-90');
      }
    });

    // Add intersection observer for animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.animationPlayState = 'running';
        }
      });
    }, observerOptions);

    // Observe all animated elements
    document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach(el => {
      el.style.animationPlayState = 'paused';
      observer.observe(el);
    });
  </script>
</body>

</html>

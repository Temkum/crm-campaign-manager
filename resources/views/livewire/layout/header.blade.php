 <?php
 
 use App\Livewire\Actions\Logout;
 use Livewire\Volt\Component;
 
 new class extends Component {
     /**
      * Log the current user out of the application.
      */
     public function logout(Logout $logout): void
     {
         $logout();
         $this->redirect('/', navigate: true);
     }
 }; ?>

 <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
   <div class="flex items-center justify-between px-4 py-3">
     <div class="flex items-center space-x-4">
       <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
         <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
         </svg>
       </button>
       <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ config('app.name', 'Campaign Manager') }}
       </h1>
     </div>

     <div class="flex items-center space-x-4">
       <!-- Dark Mode Toggle Button -->
       <button @click="darkMode = !darkMode" type="button"
         class="relative inline-flex flex-shrink-0 h-6 mr-5 transition-colors duration-200 ease-in-out border-2 border-transparent rounded-full cursor-pointer bg-zinc-200 dark:bg-zinc-700 w-11 focus:outline-none focus:ring-2 focus:ring-neutral-700 focus:ring-offset-2"
         role="switch" :aria-checked="darkMode.toString()">
         <span class="sr-only">Toggle dark mode</span>
         <span
           class="relative inline-block w-5 h-5 transition duration-500 ease-in-out transform translate-x-0 bg-white rounded-full shadow pointer-events-none dark:translate-x-5 ring-0">
           <!-- Sun icon (show in light mode) -->
           <span
             class="absolute inset-0 flex items-center justify-center w-full h-full transition-opacity duration-500 ease-in opacity-100 dark:opacity-0 dark:duration-100 dark:ease-out"
             aria-hidden="true">
             <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor"
               viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                 d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
               </path>
             </svg>
           </span>
           <!-- Moon icon (show in dark mode) -->
           <span
             class="absolute inset-0 flex items-center justify-center w-full h-full transition-opacity duration-100 ease-out opacity-0 dark:opacity-100 dark:duration-200 dark:ease-in"
             aria-hidden="true">
             <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor"
               viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                 d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
             </svg>
           </span>
         </span>
       </button>

       <!-- Profile dropdown -->
       <div class="relative" x-data="{ open: false }">
         <button @click="open = !open"
           class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
           <div
             class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
             <span class="text-white text-sm font-medium">{{ substr(auth()->user()->name ?? 'JD', 0, 2) }}</span>
           </div>
           <!-- Dropdown arrow -->
           <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
           </svg>
         </button>

         <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
           x-transition:enter-start="transform opacity-0 scale-95"
           x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
           x-transition:leave-start="transform opacity-100 scale-100"
           x-transition:leave-end="transform opacity-0 scale-95"
           class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
           <a href="{{ route('profile') }}"
             class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
             <svg class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
               viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                 d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
             </svg>
             {{ __('Profile') }}
           </a>
           <a href="#"
             class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
             <svg class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
               viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                 d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
               </path>
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                 d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
             </svg>
             {{ __('Settings') }}
           </a>
           <div class="border-t border-gray-100 dark:border-gray-600 my-1"></div>
           <form wire:submit="logout" class="block">
             @csrf
             <button type="submit"
               class="w-full flex items-center text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
               <svg class="w-4 h-4 mr-2 text-red-500 dark:text-red-400" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                   d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
               </svg>
               {{ __('Sign out') }}
             </button>
           </form>
         </div>
       </div>
     </div>
   </div>
 </header>

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
         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
         </svg>
       </button>
       <h1 class="text-xl font-semibold">{{ config('app.name', 'Campaign Manager') }}s</h1>
     </div>

     <div class="flex items-center space-x-4">
       <button @click="darkMode = !darkMode"
         class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
         <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
             d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
         </svg>
         <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
             d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
           </path>
         </svg>
       </button>

       <!-- Profile dropdown - Pure Alpine, no components -->
       <div class="relative" x-data="{ open: false }">
         <button @click="open = !open"
           class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
           <div
             class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
             <span class="text-white text-sm font-medium">{{ substr(auth()->user()->name ?? 'JD', 0, 2) }}</span>
           </div>
         </button>

         <div x-show="open" @click.away="open = false" x-transition
           class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1">
           <a href="{{ route('profile') }}"
             class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Profile') }}</a>
           <a href="#"
             class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Settings') }}</a>
           <div class="border-t border-gray-100 dark:border-gray-600 my-1"></div>
           <form wire:submit="logout" class="block">
             @csrf
             <button type="submit"
               class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
               {{ __('Sign out') }}
             </button>
           </form>
         </div>
       </div>
     </div>
   </div>
 </header>

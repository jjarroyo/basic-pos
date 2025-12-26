<div class="relative mb-6 w-full">
    <div class="flex items-center gap-3 md:gap-4 mb-4">
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center size-10 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:scale-105 transition-transform" title="Volver al Dashboard">
            <span class="material-symbols-outlined text-2xl">arrow_back</span>
        </a>
        <div class="flex-1">
            <flux:heading size="xl" level="1">{{ __('Settings') }}</flux:heading>
            <flux:subheading size="lg" class="mt-1">{{ __('Manage your profile and account settings') }}</flux:subheading>
        </div>
    </div>
    <flux:separator variant="subtle" />
</div>

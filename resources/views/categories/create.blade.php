<x-layouts::app :title="__('Create Category')">
    <div class="mb-6">
        <flux:heading size="xl">{{ __('Create Category') }}</flux:heading>
    </div>

    <form method="POST" action="{{ route('categories.store') }}" class="max-w-lg space-y-6">
        @csrf

        <flux:input name="name" :label="__('Name')" :value="old('name')" required />

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">{{ __('Create') }}</flux:button>
            <flux:button :href="route('categories.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
        </div>
    </form>
</x-layouts::app>

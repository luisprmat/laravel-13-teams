<x-layouts::app :title="__('Edit Category')">
    <div class="mb-6">
        <flux:heading size="xl">{{ __('Edit Category') }}</flux:heading>
    </div>

    <form method="POST" action="{{ route('categories.update', $category) }}" class="max-w-lg space-y-6">
        @csrf
        @method('PUT')

        <flux:input name="name" :label="__('Name')" :value="old('name', $category->name)" required />

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">{{ __('Update') }}</flux:button>
            <flux:button :href="route('categories.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
        </div>
    </form>
</x-layouts::app>

<x-layouts::app :title="__('Categories')">
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ __('Categories') }}</flux:heading>
        <flux:button :href="route('categories.create')" variant="primary" wire:navigate>
            {{ __('New Category') }}
        </flux:button>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/50 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Slug') }}</flux:table.column>
            <flux:table.column>{{ __('Posts') }}</flux:table.column>
            <flux:table.column>{{ __('Created') }}</flux:table.column>
            <flux:table.column class="text-right">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($categories as $category)
                <flux:table.row>
                    <flux:table.cell>{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>{{ $category->slug }}</flux:table.cell>
                    <flux:table.cell>{{ $category->posts_count }}</flux:table.cell>
                    <flux:table.cell>{{ $category->created_at->isoFormat('LL') }}</flux:table.cell>
                    <flux:table.cell class="text-right">
                        <div class="flex items-center justify-end gap-2">
                            <flux:button size="xs" :href="route('categories.edit', $category)" wire:navigate>
                                {{ __('Edit') }}
                            </flux:button>
                            @can('delete', $category)
                                <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this category?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button size="xs" type="submit" variant="danger">
                                        {{ __('Delete') }}
                                    </flux:button>
                                </form>
                            @endcan
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center">
                        {{ __('No categories found.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</x-layouts::app>

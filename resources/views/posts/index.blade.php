<x-layouts::app :title="__('Posts')">
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ __('Posts') }}</flux:heading>
        @can('create', App\Models\Post::class)
            <flux:button :href="route('posts.create')" variant="primary" wire:navigate>
                {{ __('New Post') }}
            </flux:button>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/50 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Title') }}</flux:table.column>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column>{{ __('Created') }}</flux:table.column>
            <flux:table.column class="text-right">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($posts as $post)
                <flux:table.row>
                    <flux:table.cell>{{ $post->title }}</flux:table.cell>
                    <flux:table.cell>{{ $post->category->name }}</flux:table.cell>
                    <flux:table.cell>{{ $post->created_at->isoFormat('LL') }}</flux:table.cell>
                    <flux:table.cell class="text-right">
                        <div class="flex items-center justify-end gap-2">
                            @can('update', $post)
                                <flux:button size="xs" :href="route('posts.edit', $post)" wire:navigate>
                                    {{ __('Edit') }}
                                </flux:button>
                            @endcan
                            @can('delete', $post)
                                <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this post?') }}')">
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
                    <flux:table.cell colspan="4" class="text-center">
                        {{ __('No posts found.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{ $posts->links() }}
    </div>
</x-layouts::app>

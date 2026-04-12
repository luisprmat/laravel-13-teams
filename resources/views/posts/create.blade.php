<x-layouts::app :title="__('Create Post')">
    <div class="mb-6">
        <flux:heading size="xl">{{ __('Create Post') }}</flux:heading>
    </div>

    <form method="POST" action="{{ route('posts.store') }}" class="max-w-lg space-y-6">
        @csrf

        <flux:select name="category_id" :label="__('Category')" required>
            <option value="">{{ __('Select a category') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </flux:select>

        <flux:input name="title" :label="__('Title')" :value="old('title')" required />

        <flux:textarea name="post_text" :label="__('Content')" rows="6" required>{{ old('post_text') }}</flux:textarea>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">{{ __('Create') }}</flux:button>
            <flux:button :href="route('posts.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
        </div>
    </form>
</x-layouts::app>

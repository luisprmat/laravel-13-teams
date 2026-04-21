<x-layouts::app :title="__('Team Settings')">
    <div class="mx-auto max-w-2xl space-y-10 py-8">
        <div>
            <h1 class="text-2xl font-semibold">{{ __('Team Settings') }}</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Manage your team configuration.') }}</p>
        </div>

        @if (session('success'))
            <flux:badge color="green">{{ session('success') }}</flux:badge>
        @endif

        <!-- Avatar Section -->
        <div class="space-y-4">
            <flux:heading>{{ __('Team Avatar') }}</flux:heading>

            <div class="flex items-center gap-4">
                @if ($team->avatar_path)
                    <img
                        src="{{ Storage::url($team->avatar_path) }}"
                        alt="{{ $team->name }}"
                        class="size-16 rounded-full object-cover"
                    />
                @else
                    <flux:avatar :name="$team->name" size="lg" />
                @endif

                <form method="POST" action="{{ route('team.settings.avatar') }}" enctype="multipart/form-data" class="flex items-center gap-3">
                    @csrf
                    <input type="file" name="avatar" accept="image/*" class="text-sm" />
                    <flux:button type="submit" variant="outline" size="sm">
                        {{ __('Upload') }}
                    </flux:button>
                </form>
            </div>
            @error('avatar')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Settings Form -->
        <form method="POST" action="{{ route('team.settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <flux:textarea
                name="description"
                :label="__('Description')"
                rows="3"
            >{{ old('description', $team->settings?->get('description', '')) }}</flux:textarea>

            <flux:select name="timezone" :label="__('Timezone')">
                @foreach (['UTC', 'America/New_York', 'America/Bogota', 'America/Chicago', 'America/Denver', 'America/Los_Angeles', 'Europe/London', 'Europe/Berlin', 'Europe/Vilnius', 'Asia/Tokyo', 'Australia/Sydney'] as $tz)
                    <flux:select.option
                        value="{{ $tz }}"
                        :selected="old('timezone', $team->settings?->get('timezone', 'UTC')) === $tz"
                    >
                        {{ $tz }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:button type="submit" variant="primary">
                {{ __('Save Settings') }}
            </flux:button>
        </form>
    </div>
</x-layouts::app>

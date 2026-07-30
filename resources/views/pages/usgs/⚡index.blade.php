<?php

use App\Enums\IncidentStatus;
use App\Models\Kaiju;
use App\Services\Usgs\UsgsEarthquakeClient;
use App\Services\Usgs\UsgsEarthquakeMapper;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('usgs.page_title')] class extends Component {
    /** @var array<int, array<string, mixed>> */
    public array $events = [];

    public ?string $error = null;

    public ?string $selected_event_id = null;

    public string $kaiju_id = '';

    public function mount(UsgsEarthquakeClient $client, UsgsEarthquakeMapper $mapper): void
    {
        try {
            $this->events = $mapper->map($client->fetchRecentEvents());
        } catch (ConnectionException|RequestException|\UnexpectedValueException $exception) {
            report($exception);
            $this->error = __('usgs.errors.unavailable');
        }
    }

    /**
     * Get the known Kaijus available for the imported Incident.
     *
     * @return Collection<int, Kaiju>
     */
    #[Computed]
    public function kaijus(): Collection
    {
        return Kaiju::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * Get validation rules for importing one USGS event.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'selected_event_id' => ['required', 'string'],
            'kaiju_id' => ['bail', 'required', 'integer', Rule::exists(Kaiju::class, 'id')],
        ];
    }

    /**
     * Fetch the current catalogue and create one Incident from the selected event.
     */
    public function importIncident(UsgsEarthquakeClient $client, UsgsEarthquakeMapper $mapper): void
    {
        abort_unless(auth()->check(), 403);

        $validated = $this->validate();

        try {
            $currentEvents = $mapper->map($client->fetchRecentEvents());
        } catch (ConnectionException|RequestException|\UnexpectedValueException $exception) {
            report($exception);
            $this->addError('selected_event_id', __('usgs.validation.catalogue_unavailable'));

            return;
        }

        $event = collect($currentEvents)->first(
            fn (array $event): bool => $event['id'] === $validated['selected_event_id'],
        );

        if ($event === null) {
            $this->addError('selected_event_id', __('usgs.validation.event_unavailable'));

            return;
        }

        $kaiju = Kaiju::query()->findOrFail((int) $validated['kaiju_id']);

        try {
            $incident = DB::transaction(fn () => $kaiju->incidents()->create([
                'title' => $event['title'],
                'description' => 'Imported from USGS: '.$event['title'],
                'location' => $event['location'] ?? 'Unknown location',
                'status' => IncidentStatus::Open,
                'occurred_at' => CarbonImmutable::parse($event['occurred_at_iso'], 'UTC'),
                'source' => 'USGS',
                'external_event_id' => $event['id'],
                'external_url' => $event['url'],
                'magnitude' => $event['magnitude'],
                'latitude' => $event['latitude'],
                'longitude' => $event['longitude'],
                'depth' => $event['depth'],
            ]));
        } catch (QueryException $exception) {
            if ($exception->getCode() !== '23505' || ! str_contains($exception->getMessage(), 'incidents_source_external_event_id_unique')) {
                throw $exception;
            }

            $this->addError('selected_event_id', __('usgs.validation.duplicate_import'));

            return;
        }

        $this->redirectRoute('incidents.show', $incident, navigate: true);
    }
}; ?>

<section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
    <header class="space-y-2">
        <flux:heading size="xl">{{ __('usgs.title') }}</flux:heading>
        <flux:text>{{ __('usgs.description') }}</flux:text>
    </header>

    @if ($error !== null)
        <div class="rounded-xl border border-red-200 bg-red-50 px-6 py-5 dark:border-red-900 dark:bg-red-950/40" role="alert">
            <flux:heading size="lg">{{ __('usgs.errors.heading') }}</flux:heading>
            <flux:text class="mt-2">{{ $error }}</flux:text>
        </div>
    @elseif (count($events) === 0)
        <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700">
            <flux:heading size="lg">{{ __('usgs.empty.heading') }}</flux:heading>
            <flux:text class="mt-2">{{ __('usgs.empty.description') }}</flux:text>
        </div>
    @else
        @auth
        <form wire:submit="importIncident" class="space-y-6" novalidate>
            @php
                $selectedEvent = collect($events)->first(
                    fn (array $event): bool => $event['id'] === $selected_event_id,
                );
            @endphp

            <div class="space-y-4 rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('usgs.import.heading') }}</flux:heading>
                    <flux:text>{{ __('usgs.import.instructions') }}</flux:text>
                </div>

                <div class="grid gap-4 md:grid-cols-2 md:items-end">
                    <flux:select wire:model="kaiju_id" :label="__('usgs.import.kaiju_label')" required>
                        <flux:select.option value="">{{ __('usgs.import.kaiju_placeholder') }}</flux:select.option>

                        @foreach ($this->kaijus as $kaiju)
                            <flux:select.option :value="$kaiju->id">
                                {{ $kaiju->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:button type="submit" variant="primary" :disabled="$selected_event_id === null" data-test="import-incident">
                        {{ __('usgs.import.button') }}
                    </flux:button>
                </div>

                <div class="rounded-lg bg-zinc-50 px-4 py-3 dark:bg-zinc-800" aria-live="polite" data-test="selected-usgs-event">
                    <span class="font-medium">{{ __('usgs.import.selected_label') }}</span>
                    @if ($selectedEvent !== null)
                        <span>{{ $selectedEvent['title'] }}</span>
                    @else
                        <span class="text-zinc-500 dark:text-zinc-400">{{ __('usgs.import.no_event') }}</span>
                    @endif
                </div>

                @error('selected_event_id')
                    <flux:text class="text-red-600" role="alert">{{ $message }}</flux:text>
                @enderror

                @error('kaiju_id')
                    <flux:text class="text-red-600" role="alert">{{ $message }}</flux:text>
                @enderror
            </div>
        </form>
        @endauth

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($events as $event)
                <div wire:key="usgs-event-{{ $event['id'] }}" class="flex flex-col rounded-xl border border-sky-200 border-t-4 border-t-sky-500 bg-white shadow-sm dark:border-sky-900 dark:border-t-sky-500 dark:bg-zinc-900">
                    @auth
                        <input wire:model.live="selected_event_id" id="usgs-event-{{ $event['id'] }}" type="radio" name="selected_event_id" value="{{ $event['id'] }}" class="peer sr-only">
                    @endauth
                    <label @auth for="usgs-event-{{ $event['id'] }}" @endauth @class([
                        'flex flex-1 flex-col gap-4 rounded-xl p-5 outline-none',
                        'cursor-pointer peer-focus-visible:ring-2 peer-focus-visible:ring-sky-500 peer-checked:bg-sky-50 peer-checked:ring-2 dark:peer-checked:bg-sky-950/40' => auth()->check(),
                    ])>
                        <div class="space-y-1">
                            <p class="text-xs font-semibold tracking-wider text-sky-700 uppercase dark:text-sky-300">
                                {{ __('usgs.event_label') }}
                            </p>
                            <flux:heading size="lg">{{ $event['title'] }}</flux:heading>
                        </div>

                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('usgs.magnitude') }}</dt>
                                <dd class="font-medium text-zinc-900 dark:text-white">{{ $event['magnitude'] ?? __('usgs.not_available') }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('usgs.occurred_at') }}</dt>
                                <dd class="text-right text-zinc-900 dark:text-white">{{ CarbonImmutable::parse($event['occurred_at_iso'])->locale(app()->getLocale())->isoFormat('LLL') }} {{ __('common.labels.utc') }}</dd>
                            </div>
                        </dl>

                        @if ($event['location'] !== null)
                            <flux:text>{{ $event['location'] }}</flux:text>
                        @endif
                    </label>

                    @if ($event['url'] !== null)
                        <flux:button :href="$event['url']" target="_blank" variant="ghost" class="mx-5 mb-5 self-start">
                            {{ __('usgs.view_source') }}
                        </flux:button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>

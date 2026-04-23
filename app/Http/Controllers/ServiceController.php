<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $this->ensureAdmin();

        $services = Service::withCount(['bookings', 'specialists'])
            ->orderBy('name')
            ->get();

        return view('services.index', compact('services'));
    }

    public function create(): View
    {
        $this->ensureAdmin();

        return view('services.create');
    }

    public function store(UpsertServiceRequest $request): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validated();

        Service::create([
            'id' => (string) Str::uuid(),
            ...$data,
            'code' => $this->generateUniqueCode($data['name']),
            'currency' => 'CLP',
            'active' => (bool) $request->boolean('active', true),
        ]);

        return redirect()
            ->route('services.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Service $service): View
    {
        $this->ensureAdmin();

        return view('services.edit', compact('service'));
    }

    public function update(UpsertServiceRequest $request, Service $service): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validated();

        $service->update([
            ...$data,
            'code' => $this->generateUniqueCode($data['name'], $service->id),
            'currency' => 'CLP',
            'active' => (bool) $request->boolean('active', false),
        ]);

        return redirect()
            ->route('services.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    private function generateUniqueCode(string $name, ?string $ignoreServiceId = null): string
    {
        $baseCode = Str::of($name)
            ->ascii()
            ->upper()
            ->replaceMatches('/[^A-Z0-9]+/', '-')
            ->trim('-')
            ->value();

        if ($baseCode === '') {
            $baseCode = 'SERVICIO';
        }

        $candidate = $baseCode;
        $suffix = 2;

        while (
            Service::query()
                ->when($ignoreServiceId, fn ($query) => $query->where('id', '!=', $ignoreServiceId))
                ->where('code', $candidate)
                ->exists()
        ) {
            $candidate = $baseCode . '-' . $suffix;
            $suffix++;
        }

        return $candidate;
    }
}

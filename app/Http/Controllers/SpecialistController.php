<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertSpecialistRequest;
use App\Models\Availability;
use App\Models\Service;
use App\Models\Specialist;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SpecialistController extends Controller
{
    private const DAYS = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miercoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sabado',
        0 => 'Domingo',
    ];

    public function index(): View
    {
        $this->ensureAdmin();

        if (!Schema::hasTable('Specialist')) {
            return view('specialists.index', [
                'specialists' => collect(),
            ]);
        }

        $query = Specialist::query()
            ->withCount('bookings')
            ->orderBy('name');

        if (Schema::hasTable('users')) {
            $query->with('user');
        }

        if (Schema::hasTable('_SpecialistServices')) {
            $query->with('services');
        }

        if (Schema::hasTable('Availability')) {
            $query->with('availabilities');
        }

        $specialists = $query->get();

        return view('specialists.index', compact('specialists'));
    }

    public function create(): View|RedirectResponse
    {
        $this->ensureAdmin();

        if (!Schema::hasTable('Specialist')) {
            return redirect()
                ->route('specialists.index')
                ->with('error', 'El modulo de especialistas no esta disponible porque la tabla Specialist no existe en esta base de datos.');
        }

        return view('specialists.create', $this->formData());
    }

    public function store(UpsertSpecialistRequest $request): RedirectResponse
    {
        $this->ensureAdmin();

        if (!Schema::hasTable('Specialist')) {
            return redirect()
                ->route('specialists.index')
                ->with('error', 'No se puede crear especialistas porque la tabla Specialist no existe en esta base de datos.');
        }

        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'SPECIALIST',
            ]);

            $specialist = Specialist::create([
                'id' => (string) Str::uuid(),
                'userId' => $user->id,
                'name' => $data['name'],
                'specialty' => $data['specialty'] ?? null,
                'active' => (bool) ($data['active'] ?? false),
            ]);

            $specialist->services()->sync($data['serviceIds'] ?? []);
            $this->syncAvailabilities($specialist, $data['availabilities'] ?? []);
        });

        return redirect()
            ->route('specialists.index')
            ->with('success', 'Especialista creado correctamente.');
    }

    public function edit(Specialist $specialist): View
    {
        $this->ensureAdmin();

        $relations = [];
        if (Schema::hasTable('users')) {
            $relations[] = 'user';
        }
        if (Schema::hasTable('_SpecialistServices')) {
            $relations[] = 'services';
        }
        if (Schema::hasTable('Availability')) {
            $relations[] = 'availabilities';
        }

        if ($relations) {
            $specialist->load($relations);
        }

        return view('specialists.edit', $this->formData($specialist));
    }

    public function update(UpsertSpecialistRequest $request, Specialist $specialist): RedirectResponse
    {
        $this->ensureAdmin();

        if (!Schema::hasTable('Specialist')) {
            return redirect()
                ->route('specialists.index')
                ->with('error', 'No se puede actualizar especialistas porque la tabla Specialist no existe en esta base de datos.');
        }

        if (Schema::hasTable('users')) {
            $specialist->load('user');
        }
        $data = $request->validated();

        if (!$specialist->user && !empty($data['email']) && empty($data['password'])) {
            return back()
                ->withErrors([
                    'password' => 'Debes ingresar una contraseña si vas a crear acceso para este especialista.',
                ])
                ->withInput();
        }

        DB::transaction(function () use ($specialist, $data) {
            if ($specialist->user) {
                $specialist->user->update([
                    'name' => $data['name'],
                    'email' => $data['email'] ?: $specialist->user->email,
                    'password' => !empty($data['password'])
                        ? Hash::make($data['password'])
                        : $specialist->user->password,
                    'role' => $specialist->user->isAdmin() ? 'ADMIN' : 'SPECIALIST',
                ]);
            } elseif (!empty($data['email'])) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => 'SPECIALIST',
                ]);

                $specialist->userId = $user->id;
                $specialist->save();
            }

            $specialist->update([
                'name' => $data['name'],
                'specialty' => $data['specialty'] ?? null,
                'active' => (bool) ($data['active'] ?? false),
            ]);

            $specialist->services()->sync($data['serviceIds'] ?? []);
            $this->syncAvailabilities($specialist, $data['availabilities'] ?? []);
        });

        return redirect()
            ->route('specialists.index')
            ->with('success', 'Especialista actualizado correctamente.');
    }

    private function formData(?Specialist $specialist = null): array
    {
        $services = Service::where('active', true)
            ->orderBy('name')
            ->get();

        $days = self::DAYS;

        return compact('services', 'days', 'specialist');
    }

    private function syncAvailabilities(Specialist $specialist, array $availabilities): void
    {
        if (!Schema::hasTable('Availability')) {
            return;
        }

        $specialist->availabilities()->delete();

        foreach ($availabilities as $dayOfWeek => $availability) {
            $enabled = filter_var($availability['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $startTime = $availability['startTime'] ?? null;
            $endTime = $availability['endTime'] ?? null;

            if (!$enabled || !$startTime || !$endTime) {
                continue;
            }

            Availability::create([
                'id' => (string) Str::uuid(),
                'specialistId' => $specialist->id,
                'dayOfWeek' => (int) $dayOfWeek,
                'startTime' => $startTime,
                'endTime' => $endTime,
            ]);
        }
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}

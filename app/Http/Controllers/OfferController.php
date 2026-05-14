<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertOfferRequest;
use App\Models\Offer;
use App\Models\Service;
use App\Models\Specialist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        $this->ensureAdmin();

        $offers = Offer::query()
            ->with(['service', 'specialist'])
            ->withCount(['campaigns', 'bookings'])
            ->orderByDesc('createdAt')
            ->get();

        return view('offers.index', compact('offers'));
    }

    public function create(): View
    {
        $this->ensureAdmin();

        return view('offers.create', $this->formData(new Offer()));
    }

    public function store(UpsertOfferRequest $request): RedirectResponse
    {
        $this->ensureAdmin();

        Offer::create([
            'id' => (string) Str::uuid(),
            ...$this->payload($request),
        ]);

        return redirect()->route('offers.index')->with('success', 'Oferta creada correctamente.');
    }

    public function edit(Offer $offer): View
    {
        $this->ensureAdmin();

        return view('offers.edit', $this->formData($offer));
    }

    public function update(UpsertOfferRequest $request, Offer $offer): RedirectResponse
    {
        $this->ensureAdmin();

        $offer->update($this->payload($request));

        return redirect()->route('offers.index')->with('success', 'Oferta actualizada correctamente.');
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        $this->ensureAdmin();

        if ($offer->campaigns()->exists()) {
            return redirect()->route('offers.index')->with('error', 'No puedes eliminar una oferta que ya está vinculada a una campaña.');
        }

        $name = $offer->name;
        $offer->delete();

        return redirect()->route('offers.index')->with('success', "Oferta «{$name}» eliminada correctamente.");
    }

    private function formData(Offer $offer): array
    {
        return [
            'offer' => $offer,
            'services' => Service::query()->where('active', true)->orderBy('name')->get(),
            'specialists' => Specialist::query()->where('active', true)->orderBy('name')->get(),
        ];
    }

    private function payload(UpsertOfferRequest $request): array
    {
        $data = $request->validated();

        return [
            'name' => $data['name'],
            'description' => $data['description'] ?? '',

            'serviceId' => $data['serviceId'] ?: null,
            'specialistId' => $data['specialistId'] ?: null,
            'discountType' => $data['discountType'],
            'discountValue' => $data['discountType'] === 'CUSTOM_TEXT' ? null : ($data['discountValue'] ?? null),
            'customText' => $data['discountType'] === 'CUSTOM_TEXT' ? ($data['customText'] ?? null) : null,
            'startsAt' => $data['startsAt'],
            'endsAt' => $data['endsAt'],
            'maxRedemptions' => $data['maxRedemptions'] ?? null,
            'active' => (bool) $request->boolean('active', true),
        ];
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}

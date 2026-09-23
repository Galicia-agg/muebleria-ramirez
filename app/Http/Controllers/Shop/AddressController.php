<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\StoreAddressRequest;
use App\Models\Address;
use App\Repositories\Contracts\AddressRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AddressController extends Controller
{
    public function __construct(
        private readonly AddressRepositoryInterface $addresses,
    ) {
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Shop/Addresses', [
            'addresses' => $this->addresses->forUser($request->user()->id),
        ]);
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($data['is_default'] ?? false) {
            Address::query()->where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        $this->addresses->create($data);

        return back()->with('success', 'Dirección agregada.');
    }

    public function update(StoreAddressRequest $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $data = $request->validated();

        if ($data['is_default'] ?? false) {
            Address::query()->where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        $this->addresses->update($address, $data);

        return back()->with('success', 'Dirección actualizada.');
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $this->addresses->delete($address);

        return back()->with('success', 'Dirección eliminada.');
    }
}

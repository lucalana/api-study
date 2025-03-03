<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->tokenCant('client:store')) {
            return response()->json([], 403);
        }
        return new ClientCollection(Client::with('user')->get());
    }

    public function store(StoreClientRequest $request)
    {
        if ($request->user()->tokenCant('client:store')) {
            return response()->json([], 403);
        }
        DB::transaction(function () use ($request): void {
            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => $request->get('password'),
            ]);

            $user->client()->create(['name' => $request->get('name')]);
        });

        return response()->json([], 201);
    }

    public function show(Client $client)
    {
        return new ClientResource($client->load('user'));
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        DB::transaction(function () use ($request, $client): void {
            $clientName = $request->get('name', $client->name);
            $clientEmail = $request->get('email', $client->user->email);
            $clientPassword = $request->get('password', $client->user->password);

            $client->update(['name' => $clientName]);
            $client->user->update([
                'email' => $clientEmail,
                'password' => $clientPassword
            ]);
        });

        return response()->json([], 204);
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json([], 202);
    }
}

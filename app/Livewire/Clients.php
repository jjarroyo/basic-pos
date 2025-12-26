<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class Clients extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $isEditing = false;
    public $clientId;

    public $name;
    public $identification;
    public $document_type = 'CC'; 
    public $email;
    public $phone;
    public $address;
    public $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'document_type' => 'required', 
            'identification' => 'required|numeric|unique:clients,identification,' . $this->clientId,
            'email' => 'nullable|email',
            'phone' => 'nullable|numeric',
        ];
    }

    public function render()
    {
        $clients = Client::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('identification', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.clients', [
            'clients' => $clients
        ]);
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['name', 'identification', 'email', 'phone', 'address', 'clientId', 'isEditing']);
        $this->document_type = 'CC';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(Client $client)
    {
        $this->resetValidation();
        $this->isEditing = true;
        $this->clientId = $client->id;
        $this->name = $client->name;
        $this->identification = $client->identification;
        $this->document_type = $client->document_type;
        $this->email = $client->email;
        $this->phone = $client->phone;
        $this->address = $client->address;
        $this->is_active = (bool) $client->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'identification' => $this->identification,
            'document_type' => $this->document_type,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing) {
            Client::find($this->clientId)->update($data);
        } else {
            Client::create($data);
        }

        $this->showModal = false;
        $this->reset(['name', 'identification']);  
    }

    public function delete($id)
    {
        // Opcional: Validar que no tenga ventas antes de borrar
        // if (Sale::where('client_id', $id)->exists()) { ... error ... }
        Client::find($id)->delete();
    }
}
<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserManagement extends Component
{
    public $users;
    public $roles;
    public $showModal = false;
    public $editMode = false;
    public $userId;
    
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role;
    public $is_active = true;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->userId ?? 'NULL'),
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ];

        if (!$this->editMode) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } elseif ($this->password) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        return $rules;
    }

    public function mount()
    {
        $this->loadUsers();
        $this->roles = Role::whereIn('name', ['admin', 'seller'])->get();
    }

    public function loadUsers()
    {
        $this->users = User::with('roles')->get();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function openEditModal($userId)
    {
        $this->resetForm();
        $user = User::with('roles')->findOrFail($userId);
        
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->roles->first()?->name ?? 'seller';
        $this->is_active = $user->is_active ?? true;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $user = User::findOrFail($this->userId);
                $user->update([
                    'name' => $this->name,
                    'email' => $this->email,
                    'is_active' => $this->is_active,
                ]);

                if ($this->password) {
                    $user->update(['password' => Hash::make($this->password)]);
                }
            } else {
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'is_active' => $this->is_active,
                ]);
            }

            // Sync role
            $user->syncRoles([$this->role]);

            session()->flash('message', $this->editMode ? 'Usuario actualizado correctamente.' : 'Usuario creado correctamente.');
            
            $this->closeModal();
            $this->loadUsers();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar el usuario: ' . $e->getMessage());
        }
    }

    public function toggleActive($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);
        $this->loadUsers();
        session()->flash('message', 'Estado del usuario actualizado.');
    }

    public function deleteUser($userId)
    {
        try {
            if ($userId == \Illuminate\Support\Facades\Auth::id()) {
                session()->flash('error', 'No puedes eliminar tu propio usuario.');
                return;
            }

            User::findOrFail($userId)->delete();
            $this->loadUsers();
            session()->flash('message', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'seller';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.user-management');
    }
}

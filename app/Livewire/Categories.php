<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $isEditing = false;
    public $categoryId;
 
    public $name = '';
    public $description = '';
    public $color = '#3b82f6';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|min:3|max:50',
        'color' => 'required',
    ];

    public function render()
    {
        $categories = Category::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.categories', [
            'categories' => $categories
        ]);
    }

    public function create()
    {
        $this->reset(['name', 'description', 'color', 'is_active', 'categoryId', 'isEditing']);
        $this->showModal = true;
    }

    public function edit(Category $category)
    {
        $this->isEditing = true;
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->color = $category->color;
        $this->is_active = (bool) $category->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $category = Category::find($this->categoryId);
            $category->update([
                'name' => $this->name,
                'description' => $this->description,
                'color' => $this->color,
                'is_active' => $this->is_active,
            ]);
        } else {
            Category::create([
                'name' => $this->name,
                'description' => $this->description,
                'color' => $this->color,
                'is_active' => $this->is_active,
            ]);
        }

        $this->showModal = false;
        $this->reset(['name', 'description', 'color']);
    }

    public function delete($id)
    {
        Category::find($id)->delete();
    }
}
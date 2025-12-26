<?php

namespace App\Livewire;

use App\Exports\ProductTemplateExport;
use App\Imports\ProductsImport;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads; 
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class Products extends Component
{
    use WithPagination, WithFileUploads;

    public $importFile;
    public $showImportModal = false;
    public $showResultsModal = false;

    public $search = '';
    public $categoryFilter = ''; 
    public $showModal = false;
    public $isEditing = false;
    public $productId;

    public $category_id;
    public $name;
    public $barcode;
    public $description;
    public $cost_price;
    public $selling_price;
    public $stock;
    public $min_stock = 5;
    public $image; 
    public $existingImage; 
    public $is_active = true;

    public $importStats = [
        'created' => [],
        'updated' => [],
        'failed' => []
    ];

    protected function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|min:3',
            'barcode' => 'nullable|unique:products,barcode,' . $this->productId,
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer',
            'image' => 'nullable|image|max:2048', 
        ];
    }

    public function render()
    {
        $query = Product::with('category')
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('barcode', 'like', '%' . $this->search . '%');
            });

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        return view('livewire.products', [
            'products' => $query->orderBy('id', 'desc')->paginate(10),
            'categories' => Category::where('is_active', true)->get() 
        ]);
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProductTemplateExport, 'plantilla_productos.xlsx');
    }

    public function openImportModal()
    {
        $this->reset(['importFile', 'importStats']);
        $this->showImportModal = true;
    }

    public function importExcel()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls,csv|max:10240', 
        ]);

        $importer = new ProductsImport();
        
        try {
            Excel::import($importer, $this->importFile);
            
            $this->importStats['created'] = $importer->created;
            $this->importStats['updated'] = $importer->updated;
            $this->importStats['failed']  = $importer->failed;

            $this->showImportModal = false;
            $this->showResultsModal = true; 
            $this->resetPage();

        } catch (\Exception $e) {
            $this->addError('importFile', 'Error procesando el archivo: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['name', 'barcode', 'description', 'cost_price', 'selling_price', 'stock', 'min_stock', 'image', 'existingImage', 'category_id', 'productId', 'isEditing']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(Product $product)
    {
        $this->resetValidation();
        $this->isEditing = true;
        $this->productId = $product->id;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->barcode = $product->barcode;
        $this->description = $product->description;
        $this->cost_price = $product->cost_price;
        $this->selling_price = $product->selling_price;
        $this->stock = $product->stock;
        $this->min_stock = $product->min_stock;
        $this->existingImage = $product->image;
        $this->is_active = (bool) $product->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'category_id' => $this->category_id,
            'name' => $this->name,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'stock' => $this->stock,
            'min_stock' => $this->min_stock,
            'is_active' => $this->is_active,
        ];

        if ($this->image) { 
            if ($this->isEditing && $this->existingImage) {
                Storage::disk('native')->delete($this->existingImage);
            }

           $data['image'] = $this->image->store('products', 'public');
        }

        if ($this->isEditing) {
            Product::find($this->productId)->update($data);
        } else {
            Product::create($data);
        }

        $this->showModal = false;
        $this->reset(['image', 'existingImage']);  
    }

    public function delete($id)
    {
        $product = Product::find($id);
        if ($product->image) {
            Storage::disk('native')->delete($product->image);
        }
        $product->delete();
    }
}
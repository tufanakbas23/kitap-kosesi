<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;

class CategoryController extends TaxonomyController
{
    protected function model(): string
    {
        return Category::class;
    }

    protected function routeName(): string
    {
        return 'categories';
    }

    protected function labels(): array
    {
        return ['tekil' => 'Kategori', 'cogul' => 'Kategoriler'];
    }

    protected function fields(): array
    {
        return [
            'name'        => ['label' => 'Kategori Adı', 'type' => 'text', 'rules' => 'required|string|max:100'],
            'icon'        => ['label' => 'İkon (emoji)', 'type' => 'text', 'rules' => 'nullable|string|max:10'],
            'description' => ['label' => 'Açıklama', 'type' => 'textarea', 'rules' => 'nullable|string|max:500'],
            'sort_order'  => ['label' => 'Sıra', 'type' => 'number', 'rules' => 'nullable|integer|min:0', 'default' => 0],
            'is_active'   => ['label' => 'Aktif', 'type' => 'checkbox', 'rules' => 'nullable'],
        ];
    }
}

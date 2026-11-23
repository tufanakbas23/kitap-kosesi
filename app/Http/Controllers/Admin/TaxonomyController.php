<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Kategori, Yazar ve Yayınevi gibi basit kayıtlar için ortak CRUD altyapısı.
 * Alt sınıflar sadece model, başlıklar ve form alanlarını tanımlar;
 * görünümler (admin/taxonomy/*) ortaktır.
 */
abstract class TaxonomyController extends Controller
{
    /** @return class-string<Model> */
    abstract protected function model(): string;

    /** Rota öneki: categories, authors, publishers */
    abstract protected function routeName(): string;

    /** ['tekil' => 'Kategori', 'cogul' => 'Kategoriler'] */
    abstract protected function labels(): array;

    /**
     * Form alanları:
     * 'alan' => ['label' => .., 'type' => text|textarea|number|checkbox, 'rules' => '..']
     */
    abstract protected function fields(): array;

    public function index(Request $request)
    {
        $model = $this->model();

        $items = $model::withCount('books')
            ->when($request->query('q'), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.taxonomy.index', $this->shared() + ['items' => $items]);
    }

    public function create()
    {
        $model = $this->model();

        return view('admin.taxonomy.form', $this->shared() + ['item' => new $model()]);
    }

    public function store(Request $request)
    {
        $model = $this->model();
        $model::create($this->validated($request));

        return redirect()->route("admin.{$this->routeName()}.index")
            ->with('success', $this->labels()['tekil'] . ' eklendi.');
    }

    public function edit(string $id)
    {
        return view('admin.taxonomy.form', $this->shared() + ['item' => $this->find($id)]);
    }

    public function update(Request $request, string $id)
    {
        $this->find($id)->update($this->validated($request));

        return redirect()->route("admin.{$this->routeName()}.index")
            ->with('success', $this->labels()['tekil'] . ' güncellendi.');
    }

    public function destroy(string $id)
    {
        $item = $this->find($id);

        if ($item->books()->exists()) {
            return back()->with('error', 'Bu kayda bağlı kitaplar olduğu için silinemez.');
        }

        $item->delete();

        return back()->with('success', $this->labels()['tekil'] . ' silindi.');
    }

    /* ------------------------------------------------------------------ */

    private function find(string $id): Model
    {
        return $this->model()::findOrFail($id);
    }

    private function validated(Request $request): array
    {
        $fields = $this->fields();
        $rules  = array_map(fn ($f) => $f['rules'], $fields);
        $names  = array_map(fn ($f) => mb_strtolower($f['label']), $fields);

        $data = $request->validate($rules, [], $names);

        // Checkbox alanları işaretlenmediğinde false olmalı
        foreach ($fields as $key => $f) {
            if ($f['type'] === 'checkbox') {
                $data[$key] = $request->boolean($key);
            }
            if (array_key_exists('default', $f)) {
                $data[$key] ??= $f['default'];
            }
        }

        return $data;
    }

    private function shared(): array
    {
        return [
            'labels' => $this->labels(),
            'route'  => $this->routeName(),
            'fields' => $this->fields(),
        ];
    }
}

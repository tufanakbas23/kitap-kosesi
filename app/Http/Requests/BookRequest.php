<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'title'        => 'required|string|max:200',
            'author_id'    => 'required|exists:authors,id',
            'publisher_id' => 'required|exists:publishers,id',
            'category_id'  => 'required|exists:categories,id',
            'isbn'         => 'nullable|string|max:20',
            'price'        => 'required|numeric|min:0.01|max:99999',
            'list_price'   => 'nullable|numeric|gte:price|max:99999',
            'stock'        => 'required|integer|min:0',
            'page_count'   => 'nullable|integer|min:1',
            'publish_year' => 'nullable|integer|min:1500|max:' . (date('Y') + 1),
            'language'     => 'required|string|max:30',
            'cover_type'   => 'required|in:Karton Kapak,Ciltli,Spiralli',
            'paper_type'   => 'nullable|string|max:30',
            'dimensions'   => 'nullable|string|max:30',
            'description'  => 'nullable|string|max:5000',
            'cover_color'  => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'cover_image'  => 'nullable|image|max:2048',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'kitap adı', 'author_id' => 'yazar', 'publisher_id' => 'yayınevi',
            'category_id' => 'kategori', 'price' => 'satış fiyatı', 'list_price' => 'liste fiyatı',
            'stock' => 'stok', 'page_count' => 'sayfa sayısı', 'publish_year' => 'basım yılı',
            'language' => 'dil', 'cover_type' => 'kapak türü', 'cover_color' => 'kapak rengi',
            'cover_image' => 'kapak görseli',
        ];
    }

    public function messages(): array
    {
        return ['list_price.gte' => 'Liste fiyatı, satış fiyatından küçük olamaz.'];
    }
}

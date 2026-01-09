<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\UpdateProductRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
            {
                return [
                    'name'        => 'required|string|max:255',
                    'category_id' => 'required|exists:categories,id',
                    'price'       => 'required|numeric|min:0',
                    'stock'       => 'required|integer|min:0',
                    'weight'      => 'required|integer|min:0',
                    'images.*'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                ];
            }



public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui!');
    }

}

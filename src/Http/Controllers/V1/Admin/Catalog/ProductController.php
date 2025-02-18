<?php

namespace Webkul\RestApi\Http\Controllers\V1\Admin\Catalog;

use Webkul\Core\Rules\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Webkul\Product\Helpers\ProductType;
use Webkul\Admin\Http\Requests\ProductForm;
use Webkul\Admin\Http\Requests\InventoryRequest;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\RestApi\Http\Resources\V1\Admin\Catalog\ProductResource;

class ProductController extends CatalogController
{
    /**
     * Repository class name.
     *
     * @return string
     */
    public function repository()
    {
        return ProductRepository::class;
    }

    /**
     * Resource class name.
     *
     * @return string
     */
    public function resource()
    {
        return ProductResource::class;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (
            ProductType::hasVariants($request->input('type'))
            && (! $request->has('super_attributes')
                || ! count($request->get('super_attributes')))
        ) {
            return response([
                'message' => trans('rest-api::app.admin.catalog.products.error.configurable-error'),
            ], 400);
        }

        $request->validate([
            'type'                => 'required',
            'attribute_family_id' => 'required',
            'sku'                 => ['required', 'unique:products,sku', new Slug],
        ]);

        Event::dispatch('catalog.product.create.before');

        $product = $this->getRepositoryInstance()->create($request->all());
 
        Event::dispatch('catalog.product.create.after', $product);
     
        return response([
            'data'    => new ProductResource($product),
            'message' => trans('rest-api::app.admin.catalog.products.create-success'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ProductForm $request, int $id)
    {
        $data = $request->all();
    
        $multiselectAttributeCodes = [];

        $productAttributes = $this->getRepositoryInstance()->findOrFail($id);
       
        foreach ($productAttributes->attribute_family->attribute_groups as $attributeGroup) {
            $customAttributes = $productAttributes->getEditableAttributes($attributeGroup);

            if (count($customAttributes)) {
                foreach ($customAttributes as $attribute) {
                    if ($attribute->type == 'multiselect' || $attribute->type == 'checkbox') {
                        array_push($multiselectAttributeCodes, $attribute->code);
                    }
                }
            }
        }

        if (count($multiselectAttributeCodes)) {
            foreach ($multiselectAttributeCodes as $multiselectAttributeCode) {
                if (! isset($data[$multiselectAttributeCode])) {
                    $data[$multiselectAttributeCode] = [];
                }
            }
        }
        Event::dispatch('catalog.product.update.before', $id);

        $product = $this->getRepositoryInstance()->update($data, $id);

        Event::dispatch('catalog.product.update.after', $product);
  
        return response([
            'data'    => new ProductResource($product),
            'message' => trans('rest-api::app.admin.catalog.products.update-success'),
        ]);
    }

    /**
     * Update inventories.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateInventories(InventoryRequest $inventoryRequest, ProductInventoryRepository $productInventoryRepository, int $id)
    {
        $product = $this->getRepositoryInstance()->findOrFail($id);

        Event::dispatch('catalog.product.update.before', $id);
    
        $productInventoryRepository->saveInventories(request()->all(), $product);

        Event::dispatch('catalog.product.update.after', $product);
    
        return response()->json([
            'data'    => [
                'total' => $productInventoryRepository->where('product_id', $product->id)->sum('qty'),
            ],
            'message' => trans('rest-api::app.admin.catalog.products.inventories.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
        $this->getRepositoryInstance()->findOrFail($id);

        Event::dispatch('catalog.product.delete.before', $id);

        $this->getRepositoryInstance()->delete($id);

        Event::dispatch('catalog.product.delete.after', $id);

        return response([
            'message' => trans('rest-api::app.admin.catalog.products.delete-success'),
        ]);
    }

    /**
     * Remove the specified resources from database.
     *
     * @return \Illuminate\Http\Response
     */

    public function massDestroy(MassDestroyRequest $massDestroyRequest, int $id)
    { 
        foreach ($massDestroyRequest->indices as $id) {
            $this->getRepositoryInstance()->findOrFail($id);
    
            Event::dispatch('catalog.product.delete.before', $id);
    
            $this->getRepositoryInstance()->delete($id);
    
            Event::dispatch('catalog.product.delete.after', $id);
        }

        return response([
            'message' => trans('rest-api::app.admin.catalog.products.mass-operation.delete-success'),
        ]);
    }

    /**
     * Mass update the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(MassUpdateRequest $massUpdateRequest)
    {
        foreach ($massUpdateRequest->indices as $id) {
            $this->getRepositoryInstance()->findOrFail($id);

            Event::dispatch('catalog.product.update.before', $id);

            $product = $this->getRepositoryInstance()->update([
                'channel' => null,
                'locale'  => null,
                'status'  => $massUpdateRequest->value,
            ], $id);

            Event::dispatch('catalog.product.update.after', $product);
        }
        
        return response([
            'message' => trans('rest-api::app.admin.catalog.products.mass-operation.update-success'),
        ]);
    }
}

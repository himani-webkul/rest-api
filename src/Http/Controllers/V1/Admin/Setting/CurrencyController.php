<?php

namespace Webkul\RestApi\Http\Controllers\V1\Admin\Setting;

use Illuminate\Http\Request;
use Webkul\Core\Repositories\CurrencyRepository;
use Webkul\Core\Rules\Code;
use Webkul\RestApi\Http\Resources\V1\Admin\Setting\CurrencyResource;

class CurrencyController extends SettingController
{
    /**
     * Repository class name.
     *
     * @return string
     */
    public function repository()
    {
        return CurrencyRepository::class;
    }

    /**
     * Resource class name.
     *
     * @return string
     */
    public function resource()
    {
        return CurrencyResource::class;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|min:3|max:3|unique:currencies,code',
            'name' => 'required',
        ]);

        $currency = $this->getRepositoryInstance()->create($request->all());

        return response([
            'data'    => new CurrencyResource($currency),
            'message' => trans('rest-api::app.admin.settings.currencies.create-success'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => ['required', 'unique:currencies,code,' . $id, new Code],
            'name' => 'required',
        ]);

        $currency = $this->getRepositoryInstance()->update($request->all(), $id);

        return response([
            'data'    => new CurrencyResource($currency),
            'message' => trans('rest-api::app.admin.settings.currencies.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->getRepositoryInstance()->findOrFail($id);

        if ($this->getRepositoryInstance()->count() == 1) {
            return response([
                'message' => trans('rest-api::app.admin.settings.currencies.error.last-item-delete'),
            ], 400);
        }

        $this->getRepositoryInstance()->delete($id);

        return response()->json([
            'message' => trans('rest-api::app.admin.settings.currencies.delete-success'),
        ]);
    }
}

<?php

namespace ErpNET\Profiting\Milk\Http\Controllers;

use App\Http\Controllers\Controller;
use ErpNET\Profiting\Milk\Http\Requests\Production as Request;
use App\Models\Banking\Account;
use App\Models\Expense\Payment;
use App\Models\Expense\Vendor;
use App\Models\Setting\Category;
use App\Models\Setting\Currency;
use App\Traits\Uploads;
use App\Utilities\Import;
use App\Utilities\ImportFile;
use App\Utilities\Modules;
use ErpNET\Profiting\Milk\Models\Production as Model;

class Productions extends Controller
{
    use Uploads;
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $productions = Production::with(['vendor', 'category'])->collect(['created_at'=> 'desc']);
        
        $vendors = collect(Vendor::enabled()->orderBy('name')->pluck('name', 'id'));
        
        $categories = collect(Category::enabled()->type('expense')->orderBy('name')->pluck('name', 'id'));
        
        //$accounts = collect(Account::enabled()->orderBy('name')->pluck('name', 'id'));
        
        //$transfer_cat_id = Category::transfer();
        
        return view('erpnet-profiting-milk::production.index',
            compact('productions', 'vendors', 'categories')
        );
    }
    
    /**
     * Show the form for viewing the specified resource.
     *
     * @return Response
     */
    public function show()
    {
        return redirect()->route('production.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //$accounts = Account::enabled()->orderBy('name')->pluck('name', 'id');
        
        //$currencies = Currency::enabled()->orderBy('name')->pluck('name', 'code')->toArray();
        
        //$account_currency_code = Account::where('id', setting('general.default_account'))->pluck('currency_code')->first();
        
        //$currency = Currency::where('code', $account_currency_code)->first();
        
        $vendors = Vendor::enabled()->orderBy('name')->pluck('name', 'id');
        
        $categories = Category::enabled()->type('expense')->orderBy('name')->pluck('name', 'id');
        
        //$payment_methods = Modules::getPaymentMethods();
        
        return view('erpnet-profiting-milk::production.create', 
            compact('vendors', 'categories'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $model = Model::create($request->input());
        
        // Upload attachment
        $media = $this->getMedia($request->file('attachment'), 'productions');
        
        if ($media) {
            $model->attachMedia($media, 'attachment');
        }
        
        // Recurring
        $model->createRecurring();
        
        $message = trans('messages.success.added', [
            'type' => trans_choice('erpnet-profiting-milk::general.title', 1)]);
        
        flash($message)->success();
        
        return redirect()->route('production.index');
    }
    
    /**
     * Duplicate the specified resource.
     *
     * @param  Model  $model
     *
     * @return Response
     */
    public function duplicate(Model $model)
    {
        $clone = $model->duplicate();
        
        $message = trans('messages.success.duplicated', ['type' => trans_choice('erpnet-profiting-milk::general.title', 1)]);
        
        flash($message)->success();
        
        return redirect()->route('production.edit', [$clone]);
    }
    
    /**
     * Import the specified resource.
     *
     * @param  ImportFile  $import
     *
     * @return Response
     */
    public function import(ImportFile $import)
    {
        if (!Import::createFromFile($import, 'Production', 'ErpNET\Profiting\Milk')) {
            return redirect()->route('import.create', ['group'=>'', 'type'=>'productions']);
        }
        
        $message = trans('messages.success.imported', [
            'type' => trans_choice('erpnet-profiting-milk::general.title', 2)]);
        
        flash($message)->success();
        
        return redirect()->route('production.index');
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Model $model
     *
     * @return Response
     */
    public function edit(Model $model)
    {
        //$accounts = Account::enabled()->orderBy('name')->pluck('name', 'id');
        
        //$currencies = Currency::enabled()->orderBy('name')->pluck('name', 'code')->toArray();
        
        //$currency = Currency::where('code', $payment->currency_code)->first();
        
        $vendors = Vendor::enabled()->orderBy('name')->pluck('name', 'id');
        
        $categories = Category::enabled()->type('expense')->orderBy('name')->pluck('name', 'id');
        
        //$payment_methods = Modules::getPaymentMethods();
        
        return view('erpnet-profiting-milk::production.edit', 
            compact('model', 'vendors', 'categories'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Model $model
     * @param  Request  $request
     *
     * @return Response
     */
    public function update(Model $model, Request $request)
    {
        $model->update($request->input());
        
        // Upload attachment
        if ($request->file('attachment')) {
            $media = $this->getMedia($request->file('attachment'), 'productions');
            
            $model->attachMedia($media, 'attachment');
        }
        
        // Recurring
        $model->updateRecurring();
        
        $message = trans('messages.success.updated', [
            'type' => trans_choice('erpnet-profiting-milk::general.title', 1)]);
        
        flash($message)->success();
        
        return redirect()->route('production.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  Model $model
     *
     * @return Response
     */
    public function destroy(Model $model)
    {
        
        $model->recurring()->delete();
        $model->delete();
        
        $message = trans('messages.success.deleted', [
            'type' => trans_choice('erpnet-profiting-milk::general.title', 1)]);
        
        flash($message)->success();
        
        return redirect()->route('production.index');
    }
    
    /**
     * Export the specified resource.
     *
     * @return Response
     */
    public function export()
    {
        \Excel::create('productions', function($excel) {
            $excel->sheet('productions', function($sheet) {
                $sheet->fromModel(Model::filter(request()->input())->get()->makeHidden([
                    'id', 'company_id', 'parent_id', 'created_at', 'updated_at', 'deleted_at'
                ]));
            });
        })->download('xlsx');
    }
}

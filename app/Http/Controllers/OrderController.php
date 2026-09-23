<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\DataTables\OrdersDataTable;
use App\Models\Order;
use App\Models\Vendor;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(OrdersDataTable $dataTable)
    {
        return $dataTable->render('orders.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function create()
     {
         $vendors = Vendor::all(); // populate dropdown
         return view('orders.create', compact('vendors'));
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
            'date' => 'required|date',
            'trn' => 'required|string',
            'vendor_id' => 'required|exists:vendors,id',
            'attn' => 'nullable|string',
            'ship_to' => 'nullable|string',
            'address' => 'nullable|string',
            'contact' => 'nullable|string',
            'ref_no' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string',
            'items.*.unit' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $order = Order::create($request->only([
            'date', 'trn', 'vendor_id', 'attn', 'ship_to', 'address', 'contact', 'ref_no'
        ]));

        $subtotal = 0;

        foreach ($request->items as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $subtotal += $lineTotal;

            $order->items()->create([
                'item_description' => $item['item_description'],
                'unit' => $item['unit'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $lineTotal,
            ]);
        }

        $discount = $request->input('discount', 0);
        $afterDiscount = $subtotal - $discount;
        $vat = round($afterDiscount * 0.05, 2);
        $totalWithVat = $afterDiscount + $vat;

        $order->update([
            'total_amount' => $subtotal,
            'discount' => $discount,
            'total_after_discount' => $afterDiscount,
            'vat' => $vat,
            'total_with_vat' => $totalWithVat,
        ]);

        return redirect()->route('orders.index')->with('success', 'Order created successfully!');
    }
    


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::with('vendor', 'items')->findOrFail($id);
        return view('orders.show', compact('order'));
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = Order::with('items')->findOrFail($id);
        $vendors = Vendor::all();
        return view('orders.edit', compact('order', 'vendors'));
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
            'date' => 'required|date',
            'trn' => 'required|string',
            'vendor_id' => 'required|exists:vendors,id',
            'attn' => 'nullable|string',
            'ship_to' => 'nullable|string',
            'address' => 'nullable|string',
            'contact' => 'nullable|string',
            'ref_no' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string',
            'items.*.unit' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $order = Order::findOrFail($id);
        $order->update($request->only([
            'date', 'trn', 'vendor_id', 'attn', 'ship_to', 'address', 'contact', 'ref_no'
        ]));

        // Delete and recreate order items
        $order->items()->delete();

        $subtotal = 0;

        foreach ($request->items as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $subtotal += $lineTotal;

            $order->items()->create([
                'item_description' => $item['item_description'],
                'unit' => $item['unit'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $lineTotal,
            ]);
        }

        $discount = $request->input('discount', 0);
        $afterDiscount = $subtotal - $discount;
        $vat = round($afterDiscount * 0.05, 2);
        $totalWithVat = $afterDiscount + $vat;

        $order->update([
            'total_amount' => $subtotal,
            'discount' => $discount,
            'total_after_discount' => $afterDiscount,
            'vat' => $vat,
            'total_with_vat' => $totalWithVat,
        ]);

        return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
    }
    
    
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::with('items')->findOrFail($id);
        $order->items()->delete();
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }
    
    public function getItemRow(Request $request)
    {
        $index = $request->input('index', 0);
        return view('orders.partials.item_row', ['index' => $index]);
    }

}

<?php
// app/Http/Controllers/CustomerController.php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|unique:customers,no_telp',
            'alamat' => 'nullable|string'
        ]);

        $customer = Customer::create($request->all());

        if ($request->ajax()) {
            return response()->json($customer);
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil ditambahkan');
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|unique:customers,no_telp,' . $customer->id,
            'alamat' => 'nullable|string'
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil diupdate');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil dihapus');
    }

    public function search(Request $request)
    {
        $term = $request->get('term');
        $customers = Customer::where('nama', 'LIKE', "%{$term}%")
            ->orWhere('no_telp', 'LIKE', "%{$term}%")
            ->limit(10)
            ->get();

        return response()->json($customers);
    }
}
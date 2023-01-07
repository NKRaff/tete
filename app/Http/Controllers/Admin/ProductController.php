<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    //LIST PRODUCT
    public function index()
    {
        $products = Product::all();
        return view('admin.product.index', compact('products'));
    }

    //ADD PRODUCT
    public function add()
    {
        $category = Category::all();
        return view('admin.product.add', compact('category'));
    }

    public function insert(Request $request)
    {
        $products = new Product();
        if($request->hasFile('image'))
        {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file->move('assets/uploads/products/', $filename);
            $products->image = $filename;
        }
        $products->cate_id = $request->input('cate_id');
        $products->name = $request->input('name');
        $products->description = $request->input('description');
        $products->price = $request->input('price');
        $products->qty = $request->input('qty');
        $products->status = $request->input('status') == TRUE ? 'Y':'N';
        $products->trending = $request->input('trending') == TRUE ? 'Y':'N';
        $products->save();
        return redirect('products')->with('status', "Produto Adicionado com Sucesso");
    }

    //Update Product
    public function edit($id)
    {
        $products = Product::find($id);
        return view('admin.product.edit', compact('products'));
    }

    public function update(Request $request, $id)
    {
        $products = Product::find($id);

        if($request->hasFile('image'))
        {
            $path = 'assets/uploads/products/'.$products->image;
            if(File::exists($path))
            {
                File::delete($path);
            }
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file->move('assets/uploads/products/', $filename);
            $products->image = $filename;
        }
        $products->name = $request->input('name');
        $products->description = $request->input('description');
        $products->price = $request->input('price');
        $products->qty = $request->input('qty');
        $products->status = $request->input('status') == TRUE ? 'Y':'N';
        $products->trending = $request->input('trending') == TRUE ? 'Y':'N';
        $products->update();
        return redirect('products')->with('status', "Produto Atualizado com Sucesso");
    }

    //Delete Protucts
    public function destroy($id)
    {
        $products = Product::find($id);
        //$order = OrderItem::all('prod_id');
        //$pedido = OrderItem::find('prod_id');
        
        if(OrderItem::where('prod_id', $id)->first() && Order::where('status', "Completo")->first()){
            return redirect('products')->with('status', "Erro ao Deletar! ");
        }

        if(Cart::where('prod_id', $id)->first()){
            return redirect('products')->with('status', "Erro ao Deletar! Produto adicionado em algum carrinho");
        }
        else{
            $path = 'assets/uploads/products/'.$products->image;
            if(File::exists($path))
            {
                File::delete($path);
            }
            $products->delete();
            return redirect('products')->with('status', "Produto Deletado com Sucesso");
        }
        
    }
}

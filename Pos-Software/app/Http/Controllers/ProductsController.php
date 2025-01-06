<?php

namespace App\Http\Controllers;

use App\Models\PosSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\ProductTags;
use App\Models\PromotionDetails;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use App\Models\Size;
use App\Models\Tags;
use App\Models\Variation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Services\ImageService;
use function App\Helper\generateUniqueSlug;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    public function index()
    {
        return view('pos.products.product.product');
    }
    public function store(Request $request, ImageService $imageService)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'category_id' => 'required|integer',
            'base_sell_price' => 'required|max:7',
            'unit' => 'required',
            'size' => 'required',
        ]);

        if ($validator->passes()) {
            $product = new Product;
            $product->name =  $request->name;
            $product->slug = generateUniqueSlug($request->name, $product);
            $barcodePrefix = strtoupper(substr($request->name, 0, 2)); // Take the first 2 characters and convert to uppercase
            $uniquePart = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT); // Generate a 6-digit number with leading zeros if needed

            $product->barcode = $barcodePrefix . $uniquePart;
            $product->category_id =  $request->category_id;
            $product->subcategory_id  =  $request->subcategory_id ?? null;
            $product->brand_id  =  $request->brand_id;
            $product->unit  =  $request->unit;

            $product->cost_price  =  $request->cost_price;
            $product->base_sell_price  =  $request->base_sell_price;
            $product->description  =  $request->description;
            $product->save();
            // product variations
            $productvariations = new Variation();
            $productvariations->product_id = $product->id;
            $productvariations->color  =  $request->color;
            $productvariations->price  =  $request->base_sell_price;
            $productvariations->size  =  $request->size;
            $productvariations->model_no  =  $request->model_no;
            $productvariations->quality  =  $request->quality;
            $productvariations->status  = 'default';
            if ($request->hasFile('image')) {
                $destinationPath = public_path('uploads/products');
                $imageName = $imageService->resizeAndOptimize($request->file('image'), $destinationPath);
                $productvariations->image = $imageName;
            }
            $productvariations->save();

            if ($request->tag_id && is_array($request->tag_id)) {
                $tags = [];
                foreach ($request->tag_id as $tag) {

                    $tags[] = [
                        'tag_id' => $tag,
                        'product_id' => $product->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                ProductTags::insert($tags); // Batch insert the tags
            }
            return response()->json([
                'status' => 200,
                'message' => 'Product Save Successfully',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    }
    // product manage
    public function view()
    {
        $category = Category::where('slug', 'via-sell')->first();
        $query = Product::orderBy('id', 'asc')->with('variation');

        if ($category) {
            $query->where('category_id', '!=', $category->id);
        }
        // $products = Product::with('product_details')->get();
        $products = $query->get();

        return view('pos.products.product.product-show', compact('products'));
    }

    // via product
    public function viaProductsView()
    {
        $category = Category::where('slug', 'via-sell')->first();
        if (Auth::user()->id == 1) {
            $products = Product::where('category_id', $category->id)->latest()->get();
        } else {
            $products = Product::where('branch_id', Auth::user()->branch_id)
                ->where('category_id', $category->id)
                ->latest()
                ->get();
        }

        return view('pos.products.product.via_products', compact('products'));
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);

        return view('pos.products.product.product-edit', compact('product'));
    }
    public function update(Request $request, $id, ImageService $imageService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'category_id' => 'required|integer',
            'base_sell_price' => 'required|max:7',
            'unit' => 'required',
            'size' => 'required',
        ]);
        if ($validator->passes()) {
            $product = Product::findOrFail($id);
            $product->name =  $request->name;
            $product->slug = generateUniqueSlug($request->name, $product);
            $barcodePrefix = strtoupper(substr($request->name, 0, 2)); // Take the first 2 characters and convert to uppercase
            $uniquePart = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT); // Generate a 6-digit number with leading zeros if needed

            $product->barcode = $barcodePrefix . $uniquePart;
            $product->category_id =  $request->category_id;
            $product->subcategory_id  =  $request->subcategory_id ?? null;
            $product->brand_id  =  $request->brand_id;
            $product->unit  =  $request->unit;
            $product->cost_price  =  $request->cost_price;
            $product->base_sell_price  =  $request->base_sell_price;
            $product->description  =  $request->description;
            $product->save();

            //Dertails
            $productvariations = Variation::where('product_id', $product->id)
                ->where('status', 'default')
                ->first();

            $productvariations->product_id = $product->id;
            $productvariations->color  =  $request->color;
            $productvariations->price  =  $request->base_sell_price;
            $productvariations->size  =  $request->size;
            $productvariations->model_no  =  $request->model_no;
            $productvariations->quality  =  $request->quality;
            $productvariations->status  = 'default';
            if ($request->hasFile('image')) {
                if ($productvariations->image) {
                    // If the old image exists, unlink it from the filesystem
                    $oldImagePath = public_path('uploads/products/' . $productvariations->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $destinationPath = public_path('uploads/products');
                $imageName = $imageService->resizeAndOptimize($request->file('image'), $destinationPath);
                $productvariations->image = $imageName;
            }
            $productvariations->save();

            if ($request->tag_id && is_array($request->tag_id)) {
                ProductTags::where('product_id', $product->id)->delete();
                $tags = [];
                foreach ($request->tag_id as $tag) {
                    $tags[] = [
                        'tag_id' => $tag,
                        'product_id' => $product->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                ProductTags::insert($tags); // Batch insert the tags
            }
            return response()->json([
                'status' => 200,
                'message' => 'Product Update Successfully',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    }
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        $productDetailse = ProductDetails::where('product_id', $id)->first();
        if ($productDetailse->image || '') {
            $previousImagePath = public_path('uploads/products/') . $productDetailse->image;
            if (file_exists($previousImagePath)) {
                unlink($previousImagePath);
            }
        }
        $productDetailse->delete();
        ProductTags::where('product_id', $id)->delete();

        return back()->with('message', "Product deleted successfully");
    }
    public function find($id)
    {
        // $product = Product::with('unit')->findOrFail($id);
        $varients = Variation::where('product_id', $id)->get();
        
            // If no promotion details exist, still return the product with the unit
            return response()->json([
                'status' => '200',
                'varients' => $varients,
            ]);
    }
    //
    public function ProductBarcode($id)
    {
        $product = Product::findOrFail($id);
        return view('pos.products.product.product-barcode', compact('product'));
    }
    public function globalSearch($search_value)
    {
        $product = Product::where('search_value');

        $products = Product::where('name', 'LIKE', '%' . $search_value . '%')
            ->orWhere('details', 'LIKE', '%' . $search_value . '%')
            ->orWhere('price', 'LIKE', '%' . $search_value . '%')
            ->orWhereHas('category', function ($query) use ($search_value) {
                $query->where('name', 'LIKE', '%' . $search_value . '%');
            })
            ->orWhereHas('subcategory', function ($query) use ($search_value) {
                $query->where('name', 'LIKE', '%' . $search_value . '%');
            })
            ->orWhereHas('brand', function ($query) use ($search_value) {
                $query->where('name', 'LIKE', '%' . $search_value . '%');
            })

            ->get();

        return response()->json([
            'products' => $products,
            'status' => 200
        ]);
    }

    // product Ledger
    public function productLedger($id)
    {
        $data = Product::findOrFail($id);
        $sales = SaleItem::where('product_id', $id)->get();
        $purchases = PurchaseItem::where('product_id', $id)->get();

        // Combine sales and purchases into one array
        $transactions = [];

        foreach ($sales as $sale) {
            $transactions[] = [
                'date' => $sale->created_at,
                'type' => 'sale', // Identifies as a sale
                'transaction' => $sale, // Store the sale object
            ];
        }

        foreach ($purchases as $purchase) {
            $transactions[] = [
                'date' => $purchase->created_at,
                'type' => 'purchase', // Identifies as a purchase
                'transaction' => $purchase, // Store the purchase object
            ];
        }

        // Sort by date
        usort($transactions, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        // Initialize report
        $reports = [];
        $balance = 0;

        // Loop through combined transactions
        foreach ($transactions as $item) {
            if ($item['type'] == 'sale') {
                // Sale transaction
                $sale = $item['transaction'];
                $reports[] = [
                    'date' => $sale->created_at,
                    'particulars' => 'Sale',
                    'stockIn' => 0, // No stock coming in during a sale
                    'stockOut' => $sale->qty, // Quantity sold
                    'balance' => $balance - $sale->qty, // Decrease balance
                ];
                $balance -= $sale->qty;
            } elseif ($item['type'] == 'purchase') {
                // Purchase transaction
                $purchase = $item['transaction'];
                $reports[] = [
                    'date' => $purchase->created_at,
                    'particulars' => 'Purchase',
                    'stockIn' => $purchase->quantity, // Quantity purchased
                    'stockOut' => 0, // No stock going out during a purchase
                    'balance' => $balance + $purchase->quantity, // Increase balance
                ];
                $balance += $purchase->quantity;
            }
        }

        return view('pos.products.product-ledger.product-ledger', compact('data', 'reports'));
    }
    public function latestProduct()
    {
        $product = Product::latest()->first(); // Fetch the latest product
        return response()->json([
            'product' => $product, // Return as 'product', not 'products'
            'status' => 200
        ]);
    }
    public function latestProductSize()
    {
        $product = Product::latest()->first();
        $variation  = Variation::where('product_id', $product->id)->where('status', 'default')->first();
        $sizesIdGet = Size::where('id', $variation->size)->first(); // Fetch the size based on the variation
        $categoryId = $sizesIdGet->category_id;
        $sizes = Size::where('category_id', $categoryId)->get();
        // dd( $sizes);
        return response()->json([
            'sizes' => $sizes,
            'status' => 200
        ]);
    }
    public function storeVariation(Request $request, ImageService $imageService)
    {
        // dd($request->all());
        $color = $request->input('color', []);
        $base_price = $request->input('base_price', []);
        $model_no = $request->input('model_no', []);
        $quality = $request->input('quality', []);
        $size = $request->input('variation_size', []);
        $images = $request->file('image', []);

        // Loop through the arrays and insert each service
        foreach ($base_price as $key => $price) {
            $imageName = null;

            // Handle image upload for this variation
            if (isset($images[$key]) && $images[$key]->isValid()) {
                $destinationPath = public_path('uploads/products');
                $imageName = $imageService->resizeAndOptimize($images[$key], $destinationPath);
            }
            Variation::create([
                'product_id' => $request->productId,
                'color' => $color[$key] ?? null,
                'price' => $price ?? 0, // Default to 0 if price is missing
                'size' => $size[$key] ?? null,
                'model_no' => $model_no[$key] ?? null,
                'quality' => $quality[$key] ?? null,
                'image' => $imageName,
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Variation added successfully!',
        ]);
    }
}

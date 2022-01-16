<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    return '<div class="btn-group">
                                <button class="btn btn-sm btn-primary mr-1" data-id="'.$row['id'].'" id="editProductBtn"><i class="fas fa-edit mr-1"></i>Edit</button>
                                <button class="btn btn-sm btn-danger" data-id="'.$row['id'].'" id="deleteProductBtn"><i class="fas fa-trash-alt mr-1"></i>Delete</button>
                            </div>';
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('min')) {
                        if (is_numeric($request->get('min')))
                            $instance->where('price', '>=', $request->get('min'));
                        else
                            $instance->where('id', '-1');
                    }
                    if ($request->get('max')) {
                        if (is_numeric($request->get('max')))
                            $instance->where('price', '<=', $request->get('max'));
                        else
                            $instance->where('id', '-1');
                    }
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('name', 'LIKE', "%$search%");
                        });
                    }
                })
                ->editColumn('created_at', function ($request) {
                    return $request->created_at->format('d-m-Y H:i:s');
                })
                ->editColumn('updated_at', function ($request) {
                    return $request->updated_at->format('d-m-Y H:i:s');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('products');
    }

    public function deleteProduct(Request $request) {
        $product_id = $request->product_id;
        $query = Product::find($product_id)->delete();

        if ($query) {
            return response()->json(['code'=>1, 'msg'=>'The product has been deleted from the database']);
        } else {
            return response()->json(['code'=>0, 'msg'=>'Something went wrong...']);
        }
    }

    public function getProductDetails(Request $request)
    {
        $product_id = $request->product_id;
        $productDetails = Product::find($product_id);
        return response()->json(['details'=>$productDetails]);
    }

    public function saveProduct(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'picture'=>'mimes:jpeg,jpg,png,bmp,tiff |max:1000',
            'price'=>'required|numeric',
            'status'=>'required',
        ],[
            'picture.mimes'=>'Product image must be a jpg, jpeg, png, bmp or tiff file.',
            'picture.max'=>'Product image too big, maximum size allowed: 1 MB',
        ]);

        if (!$validator->passes()) {
            return response()->json(['code'=>0,'error'=>$validator->errors()->toArray()]);
        }
        else {
            if ($request->has('picture')) {
                $path = 'files/';
                $file = $request->file('picture');
                $file_name = time().'_'.$file->getClientOriginalName();
                $upload = $file->storeAs($path, $file_name, 'public');

                if ($upload) {
                    Product::create([
                        'name'=>$request->name,
                        'picture'=>$file_name,
                        'price'=>$request->price,
                        'status'=>$request->status,
                    ]);
                    return response()->json(['code'=>1,'msg'=>'New product has been saved successfully']);
                }
                else {
                    return response()->json(['code'=>0, 'msg'=>'Something went wrong...']);
                }
            }
            else {
                Product::create([
                    'name'=>$request->name,
                    'picture'=>'480px-No_image_available.svg.png',
                    'price'=>$request->price,
                    'status'=>$request->status,
                ]);
                return response()->json(['code'=>1,'msg'=>'New product has been saved successfully']);
            }
        }
    }

    public function updateProduct(Request $request)
    {
        $product_id = $request->pid;
        $product = Product::find($product_id);
        $path = 'files/';

        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'edit_picture'=>'mimes:jpeg,jpg,png,bmp,tiff |max:1000',
            'price'=>'required|numeric',
            'status'=>'required',
        ],[
            'edit_picture.mimes'=>'Product image must be a jpg, jpeg, png, bmp or tiff file.',
            'edit_picture.max'=>'Product image too big, maximum size allowed: 1 MB',
        ]);

        if (!$validator->passes()) {
            return response()->json(['code'=>0,'error'=>$validator->errors()->toArray()]);
        }
        else {
            if ($request->hasFile('edit_picture')) {
                $file_path = $path.$product->picture;

                //Delete old image
                if ($product->picture != null && Storage::disk('public')->exists($file_path)) {
                    Storage::disk('public')->delete($file_path);
                }

                //Upload new image
                $file = $request->file('edit_picture');
                $file_name = time().'_'.$file->getClientOriginalName();
                $upload = $file->storeAs($path, $file_name, 'public');

                if ($upload) {
                    $product->update([
                        'name'=>$request->name,
                        'picture'=>$file_name,
                        'price'=>$request->price,
                        'status'=>$request->status,
                    ]);

                    return response()->json(['code'=>1, 'msg'=>'Product details have been updated']);
                }
            }
            else {
                $product->update([
                    'name'=>$request->name,
                    'price'=>$request->price,
                    'status'=>$request->status,
                ]);
                return response()->json(['code'=>1, 'msg'=>'Product details have been updated']);
            }
        }
        return response()->json(['code'=>0, 'msg'=>'Something went wrong...']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Model\Product;
use Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = DB::table('products')
                    ->join('categories','products.category_id','categories.id')
                    ->select('categories.name AS cname','products.*')
                    ->orderBy('products.id','DESC')
                    ->paginate(10);
                    return response()->json($product);
    }

    public function getProduct(){
        $product = DB::table('products')
        ->join('categories','products.category_id','categories.id')
        ->select('categories.name AS cname','products.*')
        ->orderBy('products.name')
        ->paginate(10);
        return response()->json($product);
    }

    public function findProduct(){
        if($search = \Request::get('q')) {
            $product = Product::where(function($query) use ($search){
                $query->where('products.name','LIKE',"%$search%");
            })
            ->join('categories','products.category_id','categories.id')
            ->select('categories.name AS cname','products.*')
            ->orderBy('products.id','DESC')
            ->paginate(10);
        }
        else{
            $product = DB::table('products')
            ->join('categories','products.category_id','categories.id')
            ->select('categories.name AS cname','products.*')
            ->orderBy('products.id','DESC')
            ->paginate(10);
        }

        return $product;
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
         'category_id' => 'required',
         'name' => 'required|max:191',
         'model_no' => 'required|unique:products|max:20'
        ]);

        if ($request->image) {
                $position = strpos($request->image, ';');
                $sub = substr($request->image, 0, $position);
                $ext = explode('/', $sub)[1];

                $name = time().".".$ext;
                $img = Image::make($request->image)->resize(240,200);
                $upload_path = 'backend/product/';
                $image_url = $upload_path.$name;
                $img->save(public_path($image_url));

                $product = new Product;
                $product->category_id = $request->category_id;
                $product->name = $request->name;
                $product->model_no = strtoupper($request->model_no);
                $product->description = $request->description;
                $product->image = $name;
                $product->save(); 
        }
        else{
                $product = new Product;
                $product->category_id = $request->category_id;
                $product->name = $request->name;
                $product->model_no = strtoupper($request->model_no);
                $product->description = $request->description; 
                $product->save(); 

     } 
 


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = DB::table('products')->where('id',$id)->first();
       return response()->json($product);
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
        $validateData = $request->validate([
            'category_id' => 'required',
            'name' => 'required|max:191',
            'model_no' => 'required|max:20'
           ]);

       $data = array();
        $data['name'] = $request->name;
        $data['model_no'] = strtoupper($request->model_no);
        $data['category_id'] = $request->category_id;
        $image = $request->image;

        if ($image) {
         $position = strpos($image, ';');
         $sub = substr($image, 0, $position);
         $ext = explode('/', $sub)[1];

         $name = time().".".$ext;
         $img = Image::make($image)->resize(240,200);
         $upload_path = 'backend/product/';
         $image_url = $upload_path.$name;
         $success = $img->save(public_path($image_url));
         
         if ($success) {
            $data['image'] = $name;
            $img = DB::table('products')->where('id',$id)->first();
            $image_path = $upload_path.$img->image;
            if(file_exists(public_path($image_path)))
            $done = unlink(public_path($image_path));
            $user  = DB::table('products')->where('id',$id)->update($data);
         }
          
        }else{
            $user = DB::table('products')->where('id',$id)->update($data);
        }
    }

    public function resize(Request $request){
        $pics = $request->file('file');
        if (!is_array($pics)) {
            $pics = [$pics];
        }

        foreach($pics as $key => $pic){
         $name = $pic->getClientOriginalName();
         $img = Image::make($pic)->resize(400,400, function ($const) {
            $const->aspectRatio();
        });
         $upload_path = 'backend/product/';
         $image_url = $upload_path.$name;
         $success = $img->save(public_path($image_url));
        }
        return ['message'=> 'Success'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         $product = DB::table('products')->where('id',$id)->first();
         $upload_path = 'backend/product/';
         $photo = $product->image;
         if ($photo) {
            unlink(public_path($upload_path.$photo));
            DB::table('products')->where('id',$id)->delete();
         }else{
            DB::table('products')->where('id',$id)->delete();
       }
    }



 public function StockUpdate(Request $request,$id){
    $data = array();
    $data['product_quantity'] = $request->product_quantity;
    DB::table('products')->where('id',$id)->update($data);

 }

 public function listItems()
 {
     $products = Product::select('products.*', 'categories.name as category_name')
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->get();
     $result = [];
     foreach($products as $product):
         $result[] =['value' => $product->id, 'text' => $product->name.' - '.$product->model_no.' - IN '.$product->category_name];
     endforeach;
     return $result;
 }




}

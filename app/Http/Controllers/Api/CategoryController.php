<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Category;
use DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::withCount('products')->latest()->paginate(10);
        return response()->json($category);
    }

    public function getCategory(){
        $category = Category::orderBy('name')->get();
        return response()->json($category);
    }

    public function findCategory(){
        if($search = \Request::get('q')) {
            $category = Category::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%");
            })->withCount('products')->latest()->paginate(10);
        }
        else{
            $category = Category::withCount('products')->latest()->paginate(10);
        }

        return $category;
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
         'name' => 'required|unique:categories|max:255',
         'publish' => 'required'
        ],
        [
            'name.required' => 'The category name field is required'   
        ]
    );

         $category = new Category;
         $category->name = $request->name;
         $category->publish = $request->publish;
         
         $category->save();  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       $category = DB::table('categories')->where('id',$id)->first();
       return response()->json($category);
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
        $data = array();
        $data['name'] =  $request->name;
        $data['publish'] = $request->publish;
        DB::table('categories')->where('id',$id)->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       DB::table('categories')->where('id',$id)->delete();
    }

    public function listItems()
    {
        $records = Category::where('publish', 1)->get();
        $result = [];
        foreach($records as $record):
            $result[] =['value' => $record->id, 'text' => $record->name];
        endforeach;
        return $result;
    }
}

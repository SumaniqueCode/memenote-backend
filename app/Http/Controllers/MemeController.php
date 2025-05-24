<?php

namespace App\Http\Controllers;

use App\Models\meme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MemeController extends Controller
{
    public function memeValidator(array $memeData)
    {
        return Validator::make($memeData, [
            'name' => 'required|string|min:5|max:50',
            'category_id' => 'required|integer',
            'description' => 'required|string|min:20|max:50',
            'tags.*' => 'nullable|string|min:3|max:50',
            'status'=>'required'
        ]);
    }

    public function addProduct(Request $request)
    {
        $memeData = $request->all();
        $validator = $this->memeValidator($memeData);
        $imageArray = [];
        $validator = Validator::make($request->all(), [
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        DB::beginTransaction();

        try {
            if (isset($memeData['image']) && $memeData['image']) {
                $image = $memeData['image'];
                $image_new_name = time() . $image->getClientOriginalName();
                $image->move('Images/Memes/', $image_new_name);
                $imagePath = 'Images/Memes/' . $image_new_name;
                $imageArray[] =  $imagePath;
            } else {
                $imageArray[] = 'Images/Memes/default-meme-image.png';
            }

            $meme = Meme::create([
                'title' => $request->title,
                'category_id' => $request->category_id,
                'user_id' => Auth::user()->id,
                'description' => $request->description,
                'tags' => $request->tags,
                'status'=>$request->status,
                'image'=>$imageArray,
            ]);

            DB::commit();
            return response()->json(['message' => 'Meme Added Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error adding meme: ' . $e->getMessage()]);
        }
    }

    public function getAllmemes(){
        $memes = Meme::where('user_id', Auth::user()->id)->get();
        return response()-> json(['memes'=>$memes]);

    }

    public function getMemeDetails($id)
    {
        $meme = Meme::where('id', $id)->first();
        return response()->json(['meme'=>$meme]);
    }

    public function editMeme(Request $request)
    {
        $memeData = $request->all();
        $validator = $this->memeValidator($memeData);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        } else {
            $meme = Meme::where('id', $request->id)->first();
            $meme::update([
                'title' => $request->title,
                'category_id' => $request->category_id,
                'user_id' => Auth::user()->id,
                'description' => $request->description,
                'tags' => $request->tags,
                'status'=>$request->status,
            ]);
            return response()->json(['success'=> 'Meme Updated Successfully']);
        }
    }

    public function deleteMeme($id)
    {
        $meme = Meme::where('id', $id)->first();
        //$meme->delete();
       return response()->json(['success'=> 'Meme Deleted successfully']);
    }
}

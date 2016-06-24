<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\PostsCreateRequest;
use App\Photo;
use App\Post;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;

class AdminPostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::lists('name','id')->all();
        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostsCreateRequest $request)
    {
        $constants = Config::get('constants');
        $h = $constants['HEIGHT'];
        $w = $constants['WIDTH'];
        $input = $request->all();

        $user = Auth::user();

        if($file = $request->file('photo_id')) {

            $name = time() . $file->getClientOriginalName();
            $height = Image::make($file)->height();
            $width = Image::make($file)->width();

            if ($height / $h < $width / $w) {
                $image = Image::make($file->getRealPath())->resize($w, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } else {

                $image = Image::make($file->getRealPath())->resize(null, $h, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            $image->save('images/'. $name);

            $photo = Photo::create(['name'=>$name]);

            $input['photo_id']  = $photo->id;
        }


        $user->posts()->create($input);

        Session::flash('message','The post has been created.');

        return redirect('/admin/posts');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.posts.edit');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

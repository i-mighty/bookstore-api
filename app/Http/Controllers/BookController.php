<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Comment;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JD\Cloudder\Facades\Cloudder;

class BookController extends Controller{
    public function __construct(){
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        //Returns pagination of all Books.
        //Each page would contain 15 Book instances.
        return response(Book::paginate(15)->toJson(), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'file_path' => 'required|mimes:pdf,docx,doc,epub|between:1, 20000',
        ]);
        if ($validator->fails()){
            return response(collect($validator->errors()), 400);
        }
        $book = new Book($request->except('file_path'));
        $book->file_path = $this->saveFile($request);
        $user = auth()->user();
        $user->books()->save($book);
        return response(compact('user', 'book'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book){
        $ratings = $book->ratings;
        $comment = $book->comments;
        return response(compact('book', 'ratings', 'comment'), 200);
    }

    /**
     * Function for rating the specified book.
     *
     * @param Request $request
     * @param  \App\Models\Book $book
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function rate(Request $request, Book $book){
        $user = auth()->user();
        try{
            $pastRatings = $user->ratings()->where('book_id', $book->id)->get();
            if (!$pastRatings->isEmpty){
                $pastRatings->delete();
            }
            $rating  = new Rating(['stars' => $request->stars]);
            $rating->book()->associate($book);
            $user->ratings()->save($rating);
            return response(collect([
                "status" => "success", 'contentMessage' => "Rating saved successfully",
                "book" => $book, "rating" => $rating, "user" => auth()->user()
            ]));
        }catch (\Exception $e){

        }
    }

    /**
     * Function for storing comment for the specified book
     *
     * @param Request $request
     * @param Book $book
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function comment(Request $request, Book $book){
        $comment = new Comment(['content' => $request->text]);
        $comment->book()->associate($book);
        $comment->user()->associate(auth()->user());
        return response(collect([
            "status" => "success", 'contentMessage' => "Comment saved successfully",
            "book" => $book, "comment" => $comment, "user" => auth()->user()
        ]));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book){
        $user = auth()->user();
        if ($user->can('update', $book)){
            $data = $request->all();
            $validator = Validator::make($data, [
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'file_path' => 'nullable|mimes:pdf,docx,doc,epub|between:1, 20000',
            ]);
            if ($validator->fails()){
                return response(collect($validator->errors()), 400);
            }
            $book->update($request->except('file_path'));
            $file = $request->file('file_path');
            if ($file != null){
                if ($this->deleteFile($book)){
                    $book->file_path = $this->saveFile($request);
                }
            }
            return response(collect(["status" => "success", 'contentMessage' => "Book updated successfully", 'book' => $book]));
        }else {
            return response(collect(["status" => "error", 'contentMessage' => "User not privileged to updated book"]), 403);
        }
    }

    public function saveFile(Request $request){
        $file = $request->file('file_path');
        Cloudder::upload($file->getRealPath(),null);
        return Cloudder::getPublicId();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book){
        $user = auth()->user();
        if ($user->can('delete', $book)){
            try {
                $book->delete();
                return response(collect(['status' => 'success', 'contentMessage' => 'Book deleted successfully']), 200);
            } catch (\Exception $e) {
                return response(collect(['status' => 'success', 'contentMessage' => 'Book deleted successfully']), 500);
            }
        }else {
            return response(collect(["status" => "error", 'contentMessage' => "User not privileged to delete book"]), 403);
        }
    }

    public function deleteFile(Book $book){
        $deleted = false;
        $file = $book->file_path;
        Cloudder::delete($file);
        return $deleted = true;
    }
}

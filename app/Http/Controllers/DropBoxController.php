<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use Illuminate\Http\Request;
use Dcblogdev\Dropbox\Facades\Dropbox;
use Dropbox\Dropbox\Files as DropboxDropbox;

class DropBoxController extends Controller
{

    protected $path;
    protected $dropbox;
    protected $accessToken = 'ltvXt26-6fMAAAAAAAAAATPXSJDD7UnuOH-0IilJCg3V_TAIswKpwYEEvh5jbHeH';

  public function __construct()
  {
    $this->dropbox = new DropboxDropbox($this->accessToken);
  }
    public function index()
    {
        return view('progress-bar-file-upload');
    }

    public function store(Request $request)
    {


        // $request->validate([
        //     'file' => 'required',
        // ]);

        // $title = time().'.'.request()->file->getClientOriginalExtension();
        // $request->file->move(public_path('docs'), $title);
        // $filename = public_path('docs/'.$title);
        // echo $filename;
        // die();
        // $row =  Doc::where('code',$request->code);
        // if($row->count() > 0){
        //     $uploadPath = '/'.$row->first()->code;
        // } else {
        //     $uploadPath = '/'.$request->code;
        // }


    //    Dropbox::files()->upload($uploadPath, $filename);
    // $resp = $this->dropbox->upload_session_start($filename);
// dd($resp);
       $storeFile = new Doc();
       $storeFile->document = $request->document;
       $storeFile->name = $request->name;
       $storeFile->code = $request->code;
       $storeFile->save();

        return response()->json(['success'=>'File Uploaded Successfully']);
    }


}

<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Models\User;
use App\Models\UserDocs;


class FileUploadController extends Controller {

    /**
     * @return Application|Factory|View
     */
    public function index() {
        return view('first');
    }

    public function upload(){
        if(!isset($_GET['paper_number'])){
                    \Session::flash('error', 'Please Enter Valid Paper Number');
         return redirect('/');
        }
    if(!isset($_GET['email'])){
                    \Session::flash('error', 'Please Enter Email Address');
         return redirect('/');
        }
        $paperNumber = $_GET['paper_number'];
        $email = $_GET['email'];
        // $paper = UserDocs::where('paper_number', $paperNumber)->first();
        // if($paper == null ){
        //               \Session::flash('error', 'Please Enter Valid Paper Number');
        //  return redirect('/'); 
        // }
        // if($paper->document != null){
        //       \Session::flash('error', 'You have already submitted the Presentation.');
        //  return redirect('/'); 
        // }
        // dd($paper);
        return view('index')->with(['paper'=>$paperNumber,'email' => $email]);
    }

    public function uploadLargeFiles(Request $request) {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            // file not uploaded
        }

        $fileReceived = $receiver->receive(); // receive file
        if ($fileReceived->isFinished()) { // file uploading is complete / all chunks are uploaded
            $file = $fileReceived->getFile(); // get file
            $extension = $file->getClientOriginalExtension();
            $fileName = str_replace('.'.$extension, '', $file->getClientOriginalName()); //file name without extenstion
            $fileName .= '_' . md5(time()) . '.' . $extension; // a unique file name

            $disk = Storage::disk(config('filesystems.default'));
            $path = $disk->putFileAs('videos', $file, $fileName);
            $name = $file->store("videos");
            // delete chunked file
            unlink($file->getPathname());
            return [
                'path' => $name,
                'filename' => $fileName
            ];
        }

        // otherwise return percentage informatoin
        $handler = $fileReceived->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }

    public function store(Request $request)
    {
        if(empty($request)){
             \Session::flash('alert', 'Please Upload Video');
             return back();
        }
        
         $docs = new UserDocs();
         $docs->document = $request->document;
         $docs->email = $request->email;
         $docs->paper = $request->paper;
         $docs->save();
       
        \Session::flash('success', 'Record saved Successfully');
         return redirect(route('thankyou'));
    }
    
    public function thankyou(){
        return view('thankyou');
    }
}

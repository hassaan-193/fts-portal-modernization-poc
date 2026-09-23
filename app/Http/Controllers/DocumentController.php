<?php

namespace App\Http\Controllers;

use Flash;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\DataTables\StaffProfileDocumentsDataTable;
use App\Repositories\DocumentRepository;

class DocumentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /** @var  StaffProfileDocumentsDataTable */
    private $ProfileDocumentDataTable;
    private $ProfileDocumentRepo;

    public function __construct(StaffProfileDocumentsDataTable $ProfileDataTable, DocumentRepository $docrepo)
    {
        $this->middleware('can:document');
        $this->ProfileDocumentDataTable = $ProfileDataTable;
        $this->ProfileDocumentRepo=$docrepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return  $this->ProfileDocumentDataTable->render('documents.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('documents.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        for ($i=1; $i <= $input['total_row'] ; $i++) { 
            if(isset($input['file_'.$i])){
                $data=array(
                    'name'=>$input['name_'.$i],
                    'type'=>$input['type_'.$i],
                    'date'=>$input['date_'.$i],
                    'file'=>$input['file_'.$i]
                );
                $document = $this->ProfileDocumentRepo->create($data);
                $document->addMedia($data['file'])->toMediaCollection();
            }          
        }
        Flash::success(__('messages.saved', ['model' => __('models/document.singular')]));
        return redirect(route('document.index'));
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
        $document=$this->ProfileDocumentRepo->find($id);
        if(empty($document)){
            Flash::error(__('messages.not_found', ['model' => __('models/document.singular')]));
            return redirect(route('document.index'));
        }
        return view('documents.edit')->with('document',$document); 
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
        $document=$this->ProfileDocumentRepo->find($id);
        
        if(empty($document)){
            Flash::error(__('messages.not_found', ['model' => __('models/document.singular')]));
            return redirect(route('document.index'));
        }
        if(isset($request->file)){
            $document->deleteMedia($document->getMedia()[0]->id);
            $document->addMedia($request->file)->toMediaCollection();
        }
        $document = $this->ProfileDocumentRepo->update($request->all(), $id);

        Flash::success(__('messages.updated', ['model' => __('models/document.singular')]));

        return redirect(route('document.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $doc = $this->ProfileDocumentRepo->find($id);

        if (empty($doc)) {
            Flash::error(__('messages.not_found', ['model' => __('models/document.singular')]));

            return redirect(route('document.index'));
        }

        $status = $this->ProfileDocumentRepo->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/document.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));

        return redirect(route('document.index'));
    }
}

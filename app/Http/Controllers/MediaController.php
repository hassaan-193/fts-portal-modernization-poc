<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\AppBaseController;

class MediaController extends AppBaseController
{
    public function __construct()
    {
        $this->middleware('can:media');
    }

    public function delete_file($id)
    {
        $media = Media::find($id);
        $model_type = $media->model_type;
        $model = $model_type::find($media->model_id);
        $model->deleteMedia($media->id);

        Flash::success(__('messages.deleted', ['model' => 'File']));
        return redirect()->back();

    }
}

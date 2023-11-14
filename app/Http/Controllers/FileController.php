<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $query = File::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $files = $query->paginate(50);
        return response()->json($files);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:8192', // Максимальный размер файла 8 МБ
        ]);

        $file = $request->file('file');
        $path = $file->store('public/files');
        $fileModel = new File([
            'name' => $request->input('name') ?? $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
        ]);
        $fileModel->save();

        return response()->json($fileModel, 201);
    }

    public function show(File $file)
    {
        return response()->json($file);
    }

    public function update(Request $request, File $file)
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'file|max:8192',
            ]);
            Storage::delete($file->path);
            $newFile = $request->file('file');
            $path = $newFile->store('public/files');
            $file->path = $path;
            $file->size = $newFile->getSize();
            $file->extension = $newFile->getClientOriginalExtension();
        }

        $file->name = $request->input('name') ?? $file->name;
        $file->save();

        return response()->json($file);
    }

    public function destroy(File $file)
    {
        Storage::delete($file->path);
        $file->delete();
        return response()->json(['message' => 'File deleted successfully']);
    }

}

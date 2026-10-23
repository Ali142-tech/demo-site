<?php

namespace App\Http\Controllers\Concerns;

use App\Models\QuoteFile;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

trait HandlesQuoteFiles
{
    /** Validation rules shared by every form that accepts uploads. */
    protected function fileRules(): array
    {
        return [
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,csv,txt,jpg,jpeg,png,webp,zip,rar,ai,eps,psd,cdr'],
        ];
    }

    protected function storeFiles(Request $request, QuoteRequest $quote): void
    {
        foreach ($request->file('files', []) as $upload) {
            $quote->files()->create([
                'path' => $upload->store("quotes/{$quote->id}", 'local'),
                'original_name' => $upload->getClientOriginalName(),
                'size' => $upload->getSize(),
            ]);
        }
    }

    /** `?view=1` shows images/PDFs in the browser; everything else (and the default) downloads. */
    protected function downloadFile(Request $request, QuoteRequest $quote, QuoteFile $file): Response
    {
        abort_unless($file->quote_request_id === $quote->id, 404);
        abort_unless(Storage::disk('local')->exists($file->path), 404, 'File is no longer available.');

        if ($request->boolean('view') && $file->isViewable()) {
            return Storage::disk('local')->response($file->path, $file->original_name, [
                'Content-Disposition' => 'inline; filename="'.addslashes($file->original_name).'"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return Storage::disk('local')->download($file->path, $file->original_name);
    }
}

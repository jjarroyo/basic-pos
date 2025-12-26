@props(['path', 'alt' => '', 'class' => ''])

@php
    $src = null;
    
    if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        $absolutePath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
        $fileData = file_get_contents($absolutePath);
        $mimeType = mime_content_type($absolutePath);
        $base64 = base64_encode($fileData);
        $src = 'data:' . $mimeType . ';base64,' . $base64;
    }
@endphp

@if($src)
    <img src="{{ $src }}" alt="{{ $alt }}" class="{{ $class }}">
@else
    <div class="{{ $class }} bg-slate-100 dark:bg-slate-800 flex items-center justify-center border border-slate-200 dark:border-slate-700 overflow-hidden">
        <span class="material-symbols-outlined text-slate-400">image</span>
    </div>
@endif
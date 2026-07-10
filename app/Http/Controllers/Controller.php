<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

abstract class Controller
{
    protected function handleError(\Exception $e, string $message = "An unexpected error occurred.", string $logContext = "")
    {
        Log::error(($logContext ? "[{$logContext}] " : "") . $e->getMessage(), [
            "file" => $e->getFile(),
            "line" => $e->getLine(),
            "trace" => $e->getTraceAsString(),
        ]);

        if (request()->expectsJson()) {
            return response()->json(["error" => $message], 500);
        }

        if (request()->isMethod("GET")) {
            return back()->with("error", $message);
        }

        return back()->with("error", $message)->withInput();
    }
}
